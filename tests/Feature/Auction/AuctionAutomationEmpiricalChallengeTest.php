<?php

namespace Tests\Feature\Auction;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuctionAutomationEmpiricalChallengeTest extends TestCase
{
    use RefreshDatabase;

    protected User $sellerUser;
    protected SellerProfile $sellerProfile;
    protected Product $product;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sellerUser = User::factory()->create([
            'name'     => 'Merchant Seller',
            'email'    => 'merchant_' . Str::random(6) . '@bazaario.test',
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ]);

        $this->sellerProfile = SellerProfile::create([
            'user_id'         => $this->sellerUser->id,
            'shop_name'       => 'Artisan Craft Stall',
            'shop_slug'       => 'artisan-craft-stall-' . Str::random(6),
            'seller_type'     => 'Farmer',
            'city'            => 'Kolkata',
            'state'           => 'West Bengal',
            'country'         => 'India',
            'status'          => 'approved',
            'commission_rate' => 10.00,
        ]);

        $this->category = Category::firstOrCreate(
            ['slug' => 'handicrafts'],
            ['name' => 'Handicrafts', 'status' => 'active']
        );

        $this->product = Product::create([
            'seller_id'           => $this->sellerUser->id,
            'category_id'         => $this->category->id,
            'name'                => 'Handcrafted Terracotta Urn',
            'slug'                => 'handcrafted-terracotta-urn-' . Str::random(6),
            'sku'                 => 'BZ-URN-' . strtoupper(Str::random(4)),
            'sale_type'           => 'auction',
            'unit_type'           => 'piece',
            'price'               => 500.00,
            'stock'               => 1,
            'low_stock_threshold' => 1,
            'status'              => 'active',
            'is_perishable'       => false,
        ]);
    }

    protected function createAuction(array $overrides = []): Auction
    {
        return Auction::create(array_merge([
            'product_id'        => $this->product->id,
            'seller_id'         => $this->sellerProfile->id,
            'starting_price'    => 500.00,
            'reserve_price'     => 1000.00,
            'current_price'     => 500.00,
            'minimum_increment' => 50.00,
            'starts_at'         => now()->subHours(2),
            'ends_at'           => now()->subMinutes(5),
            'status'            => 'live',
            'winner_id'         => null,
        ], $overrides));
    }

    protected function createBuyer(string $name = 'Buyer User'): User
    {
        return User::factory()->create([
            'name'     => $name,
            'email'    => strtolower(Str::slug($name)) . '_' . Str::random(5) . '@bazaario.test',
            'role'     => 'user',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 1. CONCURRENCY & DOUBLE-CLOSING IDEMPOTENCY
    // ─────────────────────────────────────────────────────────────────────────────

    /**
     * Challenge 1A: Rapid repeated execution of command must never create duplicate orders,
     * invoices, or notifications.
     */
    public function test_challenge_concurrency_rapid_repeated_execution_prevents_duplicate_orders_or_invoices(): void
    {
        $auction = $this->createAuction([
            'starting_price' => 500.00,
            'reserve_price'  => 1000.00,
            'ends_at'        => now()->subMinutes(10),
            'status'         => 'live',
        ]);

        $buyer = $this->createBuyer('Aarav Winner');

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyer->id,
            'amount'     => 1200.00,
        ]);

        // First run: closes auction and creates single order
        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 1 expired auction(s). 1 winning order(s) generated.')
            ->assertExitCode(0);

        $this->assertEquals(1, Order::count());
        $this->assertEquals(1, Invoice::count());
        $this->assertEquals(1, SellerOrder::count());
        $this->assertEquals(1, OrderItem::count());
        $this->assertEquals(1, Notification::where('type', 'auction_won')->count());

        // Repeated rapid executions 2 through 6
        for ($i = 2; $i <= 6; $i++) {
            $this->artisan('auctions:close-expired')
                ->expectsOutputToContain('Closed 0 expired auction(s). 0 winning order(s) generated.')
                ->assertExitCode(0);
        }

        // Strict invariant: exactly 1 order and 1 invoice exist
        $this->assertEquals(1, Order::count(), 'Order duplicated on repeated command execution!');
        $this->assertEquals(1, Invoice::count(), 'Invoice duplicated on repeated command execution!');
        $this->assertEquals(1, SellerOrder::count(), 'SellerOrder duplicated on repeated command execution!');
        $this->assertEquals(1, OrderItem::count(), 'OrderItem duplicated on repeated command execution!');
        $this->assertEquals(1, Notification::where('type', 'auction_won')->count(), 'Winner notification duplicated!');

        $fresh = $auction->fresh();
        $this->assertEquals('ended', $fresh->status);
        $this->assertEquals($buyer->id, $fresh->winner_id);
    }

    /**
     * Challenge 1B: Simulates race condition where a stale memory record tries to close
     * an already-settled auction inside a lock transaction.
     */
    public function test_challenge_concurrency_race_condition_simulation_with_stale_reference(): void
    {
        $auction = $this->createAuction([
            'starting_price' => 500.00,
            'reserve_price'  => 1000.00,
            'ends_at'        => now()->subMinutes(5),
            'status'         => 'live',
        ]);

        $buyer = $this->createBuyer('Concurrent Bidder');
        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyer->id,
            'amount'     => 1500.00,
        ]);

        // Process 1 finishes first
        $this->artisan('auctions:close-expired')->assertExitCode(0);
        $this->assertEquals(1, Order::count());

        // Simulate Process 2 attempting to execute the inner transaction on the same pre-fetched auction model
        $simulatedDoubleExecutionProcessed = false;
        DB::transaction(function () use ($auction, &$simulatedDoubleExecutionProcessed) {
            $lockedAuction = Auction::where('id', $auction->id)->lockForUpdate()->first();
            if (!$lockedAuction || $lockedAuction->status !== 'live') {
                return; // Expected early return
            }
            $simulatedDoubleExecutionProcessed = true;
        });

        $this->assertFalse($simulatedDoubleExecutionProcessed, 'Process 2 should have aborted due to status !== live check');
        $this->assertEquals(1, Order::count());
        $this->assertEquals(1, Invoice::count());
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 2. TIE & EDGE BIDS: IDENTICAL AND FRACTIONAL INCREMENTS
    // ─────────────────────────────────────────────────────────────────────────────

    /**
     * Challenge 2A: Multiple bids with identical highest amounts (tie).
     * The system must deterministically pick exactly one highest bidder,
     * set winner_id, and generate exactly one order/invoice without crashing.
     */
    public function test_challenge_tie_identical_bids_resolution_and_single_order_creation(): void
    {
        $auction = $this->createAuction([
            'starting_price' => 500.00,
            'reserve_price'  => 1000.00,
            'ends_at'        => now()->subMinutes(5),
            'status'         => 'live',
        ]);

        $buyerA = $this->createBuyer('Bidder Alpha');
        $buyerB = $this->createBuyer('Bidder Beta');

        // Two bids with exact identical amount (simulating legacy or concurrent insertion)
        $bidA = AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyerA->id,
            'amount'     => 1200.00,
            'created_at' => now()->subMinutes(10),
        ]);

        $bidB = AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyerB->id,
            'amount'     => 1200.00,
            'created_at' => now()->subMinutes(5),
        ]);

        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 1 expired auction(s). 1 winning order(s) generated.')
            ->assertExitCode(0);

        $fresh = $auction->fresh();
        $this->assertEquals('ended', $fresh->status);
        $this->assertContains($fresh->winner_id, [$buyerA->id, $buyerB->id]);
        $this->assertEquals(1, Order::count());
        $this->assertEquals(1, Invoice::count());
        $this->assertEquals(1200.00, (float) Order::first()->total_amount);
        $this->assertEquals(1200.00, (float) Invoice::first()->total);
    }

    /**
     * Challenge 2B: In live bidding, placeBid strictly enforces that a subsequent bid
     * must equal or exceed (current_price + minimum_increment), rejecting ties and fractional underbids.
     */
    public function test_challenge_tie_bids_rejected_by_controller_bid_increment_rule(): void
    {
        $auction = $this->createAuction([
            'starting_price'    => 500.00,
            'current_price'     => 500.00,
            'minimum_increment' => 50.00,
            'starts_at'         => now()->subHour(),
            'ends_at'           => now()->addHours(2),
        ]);

        $buyerA = $this->createBuyer('User One');
        $buyerB = $this->createBuyer('User Two');

        // Buyer A places 550.00
        $this->actingAs($buyerA)->post(route('auctions.placeBid', $auction), [
            'amount' => 550.00,
        ])->assertSessionHasNoErrors();

        $this->assertEquals(550.00, (float) $auction->fresh()->current_price);

        // Buyer B attempts exact tie bid of 550.00 -> Rejected!
        $this->actingAs($buyerB)->post(route('auctions.placeBid', $auction), [
            'amount' => 550.00,
        ])->assertSessionHasErrors(['amount']);

        // Buyer B attempts sub-increment bid of 599.99 (need 600.00) -> Rejected!
        $this->actingAs($buyerB)->post(route('auctions.placeBid', $auction), [
            'amount' => 599.99,
        ])->assertSessionHasErrors(['amount']);

        // Buyer B places valid next bid of 600.00 -> Accepted!
        $this->actingAs($buyerB)->post(route('auctions.placeBid', $auction), [
            'amount' => 600.00,
        ])->assertSessionHasNoErrors();

        $this->assertEquals(600.00, (float) $auction->fresh()->current_price);
    }

    /**
     * Challenge 2C: Fractional increments and precision preservation across bidding and closing.
     */
    public function test_challenge_fractional_increments_and_decimal_precision(): void
    {
        $auction = $this->createAuction([
            'starting_price'    => 100.25,
            'current_price'     => 100.25,
            'minimum_increment' => 10.25,
            'starts_at'         => now()->subHour(),
            'ends_at'           => now()->addHours(2),
        ]);

        $buyer = $this->createBuyer('Fractional Bidder');

        // Valid fractional increment bid: 100.25 + 10.25 = 110.50
        $this->actingAs($buyer)->post(route('auctions.placeBid', $auction), [
            'amount' => 110.50,
        ])->assertSessionHasNoErrors();

        $this->assertEquals(110.50, (float) $auction->fresh()->current_price);

        // Valid fractional bid with uneven cents: 125.75
        $this->actingAs($buyer)->post(route('auctions.placeBid', $auction), [
            'amount' => 125.75,
        ])->assertSessionHasNoErrors();

        $this->assertEquals(125.75, (float) $auction->fresh()->current_price);

        // Expire auction and verify exact decimal preservation in order and invoice
        $auction->update(['ends_at' => now()->subMinute(), 'reserve_price' => 100.00]);

        $this->artisan('auctions:close-expired')->assertExitCode(0);

        $order = Order::first();
        $invoice = Invoice::first();
        $this->assertEquals(125.75, (float) $order->total_amount);
        $this->assertEquals(125.75, (float) $invoice->total);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 3. RESERVE PRICE EXACT BOUNDARY (== RESERVE vs RESERVE - 0.01)
    // ─────────────────────────────────────────────────────────────────────────────

    /**
     * Challenge 3A: Bid amount == reserve_price exactly (must win and generate order).
     */
    public function test_challenge_reserve_price_exact_boundary_bid_equals_reserve_wins(): void
    {
        $auction = $this->createAuction([
            'starting_price' => 500.00,
            'reserve_price'  => 1000.00,
            'ends_at'        => now()->subMinutes(5),
            'status'         => 'live',
        ]);

        $buyer = $this->createBuyer('Exact Match Winner');

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyer->id,
            'amount'     => 1000.00, // Exact boundary match
        ]);

        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 1 expired auction(s). 1 winning order(s) generated.')
            ->assertExitCode(0);

        $fresh = $auction->fresh();
        $this->assertEquals('ended', $fresh->status);
        $this->assertEquals($buyer->id, $fresh->winner_id, 'Bid exactly equal to reserve price must win!');
        $this->assertEquals(1, Order::count());
        $this->assertEquals(1, Invoice::count());
        $this->assertEquals(1000.00, (float) Order::first()->total_amount);
    }

    /**
     * Challenge 3B: Bid amount == reserve_price - 0.01 (must NOT win, zero orders).
     */
    public function test_challenge_reserve_price_exact_boundary_one_cent_below_reserve_fails(): void
    {
        $auction = $this->createAuction([
            'starting_price' => 500.00,
            'reserve_price'  => 1000.00,
            'ends_at'        => now()->subMinutes(5),
            'status'         => 'live',
        ]);

        $buyer = $this->createBuyer('One Cent Short Bidder');

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyer->id,
            'amount'     => 999.99, // Exactly 1 cent below reserve price
        ]);

        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 1 expired auction(s). 0 winning order(s) generated.')
            ->assertExitCode(0);

        $fresh = $auction->fresh();
        $this->assertEquals('ended', $fresh->status);
        $this->assertNull($fresh->winner_id, 'Bid 1 cent below reserve price must not win!');
        $this->assertEquals(0, Order::count());
        $this->assertEquals(0, Invoice::count());
    }

    /**
     * Challenge 3C: Auction with null reserve price wins with any valid highest bid.
     */
    public function test_challenge_reserve_price_null_wins_with_any_valid_bid(): void
    {
        $auction = $this->createAuction([
            'starting_price' => 500.00,
            'reserve_price'  => null,
            'ends_at'        => now()->subMinutes(5),
            'status'         => 'live',
        ]);

        $buyer = $this->createBuyer('Null Reserve Winner');

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyer->id,
            'amount'     => 550.00,
        ]);

        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 1 expired auction(s). 1 winning order(s) generated.')
            ->assertExitCode(0);

        $fresh = $auction->fresh();
        $this->assertEquals('ended', $fresh->status);
        $this->assertEquals($buyer->id, $fresh->winner_id);
        $this->assertEquals(1, Order::count());
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 4. TIMESTAMP BOUNDARIES (1s IN PAST vs 1s IN FUTURE vs EXACT NOW)
    // ─────────────────────────────────────────────────────────────────────────────

    /**
     * Challenge 4A: Auction ending 1 second in the past (ends_at = now - 1s) MUST close.
     */
    public function test_challenge_timestamp_boundary_ending_one_second_in_past_closes(): void
    {
        $frozenNow = Carbon::create(2026, 10, 12, 12, 0, 0);
        Carbon::setTestNow($frozenNow);

        $auction = $this->createAuction([
            'ends_at' => $frozenNow->copy()->subSecond(),
            'status'  => 'live',
        ]);

        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 1 expired auction(s)')
            ->assertExitCode(0);

        $this->assertEquals('ended', $auction->fresh()->status);

        Carbon::setTestNow();
    }

    /**
     * Challenge 4B: Auction ending 1 second in the future (ends_at = now + 1s) MUST REMAIN LIVE.
     */
    public function test_challenge_timestamp_boundary_ending_one_second_in_future_remains_live(): void
    {
        $frozenNow = Carbon::create(2026, 10, 12, 12, 0, 0);
        Carbon::setTestNow($frozenNow);

        $auction = $this->createAuction([
            'ends_at' => $frozenNow->copy()->addSecond(),
            'status'  => 'live',
        ]);

        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 0 expired auction(s)')
            ->assertExitCode(0);

        $this->assertEquals('live', $auction->fresh()->status, 'Auction 1s in the future must remain live!');

        Carbon::setTestNow();
    }

    /**
     * Challenge 4C: Auction ending exactly at current timestamp (ends_at == now) MUST close.
     */
    public function test_challenge_timestamp_boundary_ending_at_exact_now_closes(): void
    {
        $frozenNow = Carbon::create(2026, 10, 12, 12, 0, 0);
        Carbon::setTestNow($frozenNow);

        $auction = $this->createAuction([
            'ends_at' => $frozenNow->copy(),
            'status'  => 'live',
        ]);

        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 1 expired auction(s)')
            ->assertExitCode(0);

        $this->assertEquals('ended', $auction->fresh()->status);

        Carbon::setTestNow();
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 5. OUTBID CHAINING (User A -> User B -> User C)
    // ─────────────────────────────────────────────────────────────────────────────

    /**
     * Challenge 5: Chaining outbids (A -> B -> C):
     * Verify notifications table has exactly 1 notification for A, 1 for B, and 0 for C.
     * Verify self-increment by C generates 0 outbid notifications for C.
     */
    public function test_challenge_outbid_chaining_three_users_exact_notification_counts(): void
    {
        $auction = $this->createAuction([
            'starts_at'         => now()->subHour(),
            'ends_at'           => now()->addHours(2),
            'starting_price'    => 500.00,
            'current_price'     => 500.00,
            'minimum_increment' => 50.00,
        ]);

        $userA = $this->createBuyer('User Alice');
        $userB = $this->createBuyer('User Bob');
        $userC = $this->createBuyer('User Charlie');

        // Step 1: Alice places first bid (600.00)
        $this->actingAs($userA)->post(route('auctions.placeBid', $auction), [
            'amount' => 600.00,
        ])->assertSessionHasNoErrors();

        $this->assertEquals(0, Notification::where('type', 'outbid')->count());

        // Step 2: Bob outbids Alice (700.00)
        $this->actingAs($userB)->post(route('auctions.placeBid', $auction), [
            'amount' => 700.00,
        ])->assertSessionHasNoErrors();

        $this->assertEquals(1, Notification::where('user_id', $userA->id)->where('type', 'outbid')->count());
        $this->assertEquals(0, Notification::where('user_id', $userB->id)->where('type', 'outbid')->count());
        $this->assertEquals(0, Notification::where('user_id', $userC->id)->where('type', 'outbid')->count());

        $aliceNotif = Notification::where('user_id', $userA->id)->where('type', 'outbid')->first();
        $this->assertStringContainsString('700.00', $aliceNotif->message);

        // Step 3: Charlie outbids Bob (800.00)
        $this->actingAs($userC)->post(route('auctions.placeBid', $auction), [
            'amount' => 800.00,
        ])->assertSessionHasNoErrors();

        // Alice must NOT receive a duplicate notification; Bob must receive exactly 1; Charlie 0.
        $this->assertEquals(1, Notification::where('user_id', $userA->id)->where('type', 'outbid')->count());
        $this->assertEquals(1, Notification::where('user_id', $userB->id)->where('type', 'outbid')->count());
        $this->assertEquals(0, Notification::where('user_id', $userC->id)->where('type', 'outbid')->count());

        $bobNotif = Notification::where('user_id', $userB->id)->where('type', 'outbid')->first();
        $this->assertStringContainsString('800.00', $bobNotif->message);

        // Step 4: Charlie self-increments bid to 900.00
        $this->actingAs($userC)->post(route('auctions.placeBid', $auction), [
            'amount' => 900.00,
        ])->assertSessionHasNoErrors();

        // Strict invariant check: Alice 1, Bob 1, Charlie 0
        $this->assertEquals(1, Notification::where('user_id', $userA->id)->where('type', 'outbid')->count(), 'Alice was incorrectly notified again!');
        $this->assertEquals(1, Notification::where('user_id', $userB->id)->where('type', 'outbid')->count(), 'Bob was incorrectly notified again!');
        $this->assertEquals(0, Notification::where('user_id', $userC->id)->where('type', 'outbid')->count(), 'Charlie should not receive outbid notification for own bid!');
        $this->assertEquals(2, Notification::where('type', 'outbid')->count(), 'Total outbid notifications in system must be exactly 2!');
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 6. SELLER ORDER INVOICE BALANCE & FINANCIAL RECONCILIATION
    // ─────────────────────────────────────────────────────────────────────────────

    /**
     * Challenge 6: Verify Invoice total, Order total, SellerOrder subtotal match highest bid,
     * and commission math perfectly balances without penny drift.
     */
    public function test_challenge_seller_order_invoice_balance_and_commission_math(): void
    {
        $this->sellerProfile->update(['commission_rate' => 12.50]); // 12.5% commission

        $auction = $this->createAuction([
            'starting_price' => 500.00,
            'reserve_price'  => 1000.00,
            'ends_at'        => now()->subMinutes(5),
            'status'         => 'live',
        ]);

        $buyer = $this->createBuyer('Balanced Order Winner');

        $winningAmount = 1875.50;

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyer->id,
            'amount'     => $winningAmount,
        ]);

        $this->artisan('auctions:close-expired')->assertExitCode(0);

        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals($winningAmount, (float) $order->subtotal);
        $this->assertEquals($winningAmount, (float) $order->total_amount);
        $this->assertEquals('auction', $order->order_type);
        $this->assertEquals('processing', $order->order_status);
        $this->assertEquals('pending', $order->payment_status);

        $invoice = Invoice::where('order_id', $order->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals($winningAmount, (float) $invoice->subtotal);
        $this->assertEquals($winningAmount, (float) $invoice->total);
        $this->assertEquals(0.00, (float) $invoice->discount);
        $this->assertEquals(0.00, (float) $invoice->shipping_fee);

        $sellerOrder = SellerOrder::where('order_id', $order->id)->first();
        $this->assertNotNull($sellerOrder);
        $this->assertEquals($winningAmount, (float) $sellerOrder->subtotal);
        $this->assertEquals(12.50, (float) $sellerOrder->commission_rate);

        // Commission math: 1875.50 * 0.125 = 234.4375 -> round(234.44)
        $expectedCommission = round($winningAmount * (12.50 / 100), 2);
        $expectedPayout = round($winningAmount - $expectedCommission, 2);

        $this->assertEquals($expectedCommission, (float) $sellerOrder->commission_amount);
        $this->assertEquals($expectedPayout, (float) $sellerOrder->payout_amount);

        // Balance conservation check: commission + payout == subtotal
        $this->assertEquals(
            (float) $sellerOrder->subtotal,
            round((float) $sellerOrder->commission_amount + (float) $sellerOrder->payout_amount, 2),
            'Commission + Payout does not equal subtotal!'
        );

        $orderItem = OrderItem::where('seller_order_id', $sellerOrder->id)->first();
        $this->assertNotNull($orderItem);
        $this->assertEquals(1, $orderItem->quantity);
        $this->assertEquals($winningAmount, (float) $orderItem->unit_price);
        $this->assertEquals($winningAmount, (float) $orderItem->total_price);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 7. BATCH EXECUTION & ISOLATION ACROSS MULTIPLE EXPIRING LOTS
    // ─────────────────────────────────────────────────────────────────────────────

    /**
     * Challenge 7: Batch of mixed lots (Lot 1 meets reserve, Lot 2 fails reserve, Lot 3 has zero bids).
     * The command must process all 3 lots atomically, closing all 3 but generating exactly 1 winning order.
     */
    public function test_challenge_multiple_auctions_batch_closing_isolation(): void
    {
        // Lot 1: Meets reserve
        $lot1 = $this->createAuction(['reserve_price' => 1000.00, 'ends_at' => now()->subMinutes(5)]);
        $buyer1 = $this->createBuyer('Lot1 Winner');
        AuctionBid::create(['auction_id' => $lot1->id, 'user_id' => $buyer1->id, 'amount' => 1200.00]);

        // Lot 2: Fails reserve
        $lot2 = $this->createAuction(['reserve_price' => 2000.00, 'ends_at' => now()->subMinutes(5)]);
        $buyer2 = $this->createBuyer('Lot2 Bidder');
        AuctionBid::create(['auction_id' => $lot2->id, 'user_id' => $buyer2->id, 'amount' => 1500.00]);

        // Lot 3: Zero bids
        $lot3 = $this->createAuction(['reserve_price' => 1000.00, 'ends_at' => now()->subMinutes(5)]);

        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 3 expired auction(s). 1 winning order(s) generated.')
            ->assertExitCode(0);

        $this->assertEquals('ended', $lot1->fresh()->status);
        $this->assertEquals($buyer1->id, $lot1->fresh()->winner_id);

        $this->assertEquals('ended', $lot2->fresh()->status);
        $this->assertNull($lot2->fresh()->winner_id);

        $this->assertEquals('ended', $lot3->fresh()->status);
        $this->assertNull($lot3->fresh()->winner_id);

        $this->assertEquals(1, Order::count());
        $this->assertEquals(1, Invoice::count());
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 8. ANTI-SNIPING EXTENSION VERIFICATION
    // ─────────────────────────────────────────────────────────────────────────────

    /**
     * Challenge 8: Bidding when remaining time is <= 120 seconds extends ends_at by 2 minutes.
     * Bidding when remaining time is > 120 seconds does NOT extend ends_at.
     */
    public function test_challenge_anti_sniping_extension_behavior(): void
    {
        $frozenNow = Carbon::create(2026, 10, 12, 12, 0, 0);
        Carbon::setTestNow($frozenNow);

        $auction = $this->createAuction([
            'starts_at'         => $frozenNow->copy()->subHour(),
            'ends_at'           => $frozenNow->copy()->addSeconds(60), // 60s left (<= 120s)
            'starting_price'    => 500.00,
            'current_price'     => 500.00,
            'minimum_increment' => 50.00,
        ]);

        $buyer = $this->createBuyer('Sniper Bidder');

        $this->actingAs($buyer)->post(route('auctions.placeBid', $auction), [
            'amount' => 600.00,
        ])->assertSessionHasNoErrors();

        // Should be extended by 2 minutes from old ends_at: 60s + 120s = 180s from $frozenNow
        $expectedEndsAt = $frozenNow->copy()->addSeconds(60)->addMinutes(2);
        $this->assertEquals(
            $expectedEndsAt->toDateTimeString(),
            $auction->fresh()->ends_at->toDateTimeString(),
            'Anti-sniping must extend ends_at by 2 minutes when <= 120s left'
        );

        // Now remaining time is 180s (> 120s). Bidding again should NOT extend ends_at.
        $this->actingAs($buyer)->post(route('auctions.placeBid', $auction), [
            'amount' => 700.00,
        ])->assertSessionHasNoErrors();

        $this->assertEquals(
            $expectedEndsAt->toDateTimeString(),
            $auction->fresh()->ends_at->toDateTimeString(),
            'Anti-sniping must not extend when > 120s left'
        );

        Carbon::setTestNow();
    }
}
