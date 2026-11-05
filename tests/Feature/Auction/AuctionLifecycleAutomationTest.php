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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuctionLifecycleAutomationTest extends TestCase
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
            'email'    => 'seller@bazaario.test',
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ]);

        $this->sellerProfile = SellerProfile::create([
            'user_id'         => $this->sellerUser->id,
            'shop_name'       => 'Artisan Craft Stall',
            'shop_slug'       => 'artisan-craft-stall-' . Str::random(4),
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
            'slug'                => 'handcrafted-terracotta-urn-' . Str::random(4),
            'sku'                 => 'BZ-URN-001',
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
            'role'     => 'user',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ]);
    }

    /**
     * Test 1: Command transitions live auction past ends_at to ended.
     */
    public function test_command_transitions_live_auction_past_ends_at_to_ended(): void
    {
        $auction = $this->createAuction([
            'ends_at' => now()->subMinutes(10),
            'status'  => 'live',
        ]);

        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 1 expired auction(s)')
            ->assertExitCode(0);

        $this->assertEquals('ended', $auction->fresh()->status);
    }

    /**
     * Test 2: Active auction with ends_at > now() is NOT closed by command.
     */
    public function test_active_auction_with_future_ends_at_is_not_closed(): void
    {
        $auction = $this->createAuction([
            'ends_at' => now()->addHours(3),
            'status'  => 'live',
        ]);

        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 0 expired auction(s)')
            ->assertExitCode(0);

        $this->assertEquals('live', $auction->fresh()->status);
    }

    /**
     * Test 3: Highest bidder meeting reserve price is assigned as winner_id.
     */
    public function test_highest_bidder_meeting_reserve_price_is_assigned_as_winner_id(): void
    {
        $auction = $this->createAuction([
            'starting_price' => 500.00,
            'reserve_price'  => 1000.00,
            'ends_at'        => now()->subMinutes(5),
        ]);

        $buyerA = $this->createBuyer('Ananya');
        $buyerB = $this->createBuyer('Debanjan');

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyerA->id,
            'amount'     => 800.00,
        ]);

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyerB->id,
            'amount'     => 1200.00,
        ]);

        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 1 expired auction(s). 1 winning order(s) generated.')
            ->assertExitCode(0);

        $fresh = $auction->fresh();
        $this->assertEquals('ended', $fresh->status);
        $this->assertEquals($buyerB->id, $fresh->winner_id);
        $this->assertEquals(1200.00, (float) $fresh->current_price);
    }

    /**
     * Test 4: Winning auction creates Order with order_type = 'auction', SellerOrder, OrderItem, and Invoice.
     */
    public function test_winning_auction_creates_order_seller_order_order_item_and_invoice(): void
    {
        $auction = $this->createAuction([
            'starting_price' => 500.00,
            'reserve_price'  => 1000.00,
            'ends_at'        => now()->subMinutes(5),
        ]);

        $winner = $this->createBuyer('Sunita Sen');

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $winner->id,
            'amount'     => 1500.00,
        ]);

        $this->artisan('auctions:close-expired')->assertExitCode(0);

        // Verify Order
        $order = Order::where('user_id', $winner->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('auction', $order->order_type);
        $this->assertEquals(1500.00, (float) $order->total_amount);
        $this->assertEquals(1500.00, (float) $order->subtotal);
        $this->assertEquals('processing', $order->order_status);
        $this->assertEquals('pending', $order->payment_status);

        // Verify SellerOrder
        $sellerOrder = SellerOrder::where('order_id', $order->id)->first();
        $this->assertNotNull($sellerOrder);
        $this->assertEquals($this->sellerUser->id, $sellerOrder->seller_id);
        $this->assertEquals(1500.00, (float) $sellerOrder->subtotal);
        $this->assertEquals('processing', $sellerOrder->status);

        // Verify OrderItem
        $orderItem = OrderItem::where('seller_order_id', $sellerOrder->id)->first();
        $this->assertNotNull($orderItem);
        $this->assertEquals($auction->product_id, $orderItem->product_id);
        $this->assertEquals(1500.00, (float) $orderItem->unit_price);
        $this->assertEquals(1, $orderItem->quantity);
        $this->assertEquals(1500.00, (float) $orderItem->total_price);

        // Verify Invoice
        $invoice = Invoice::where('order_id', $order->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(1500.00, (float) $invoice->total);

        // Verify In-App Notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $winner->id,
            'type'    => 'auction_won',
        ]);
    }

    /**
     * Test 5: Expired auction failing reserve price sets winner_id = null and creates no order.
     */
    public function test_expired_auction_failing_reserve_price_sets_winner_null_and_creates_no_order(): void
    {
        $auction = $this->createAuction([
            'starting_price' => 500.00,
            'reserve_price'  => 1000.00,
            'ends_at'        => now()->subMinutes(5),
        ]);

        $buyer = $this->createBuyer('Rohan');

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyer->id,
            'amount'     => 750.00, // Below reserve of 1000.00
        ]);

        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 1 expired auction(s). 0 winning order(s) generated.')
            ->assertExitCode(0);

        $fresh = $auction->fresh();
        $this->assertEquals('ended', $fresh->status);
        $this->assertNull($fresh->winner_id);
        $this->assertEquals(0, Order::count());
        $this->assertEquals(0, Invoice::count());
    }

    /**
     * Test 6: Expired auction with 0 bids transitions to ended with winner_id = null.
     */
    public function test_expired_auction_with_zero_bids_transitions_to_ended_with_winner_null(): void
    {
        $auction = $this->createAuction([
            'ends_at' => now()->subMinutes(5),
        ]);

        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 1 expired auction(s). 0 winning order(s) generated.')
            ->assertExitCode(0);

        $fresh = $auction->fresh();
        $this->assertEquals('ended', $fresh->status);
        $this->assertNull($fresh->winner_id);
        $this->assertEquals(0, Order::count());
    }

    /**
     * Test 7: Outbid notification is created in notifications table when a user is outbid.
     */
    public function test_outbid_notification_created_in_notifications_table_when_user_is_outbid(): void
    {
        $auction = $this->createAuction([
            'starts_at'         => now()->subHour(),
            'ends_at'           => now()->addHours(2),
            'starting_price'    => 500.00,
            'minimum_increment' => 50.00,
            'current_price'     => 500.00,
        ]);

        $buyerA = $this->createBuyer('Buyer A');
        $buyerB = $this->createBuyer('Buyer B');

        // Buyer A places leading bid
        $this->actingAs($buyerA)->post(route('auctions.placeBid', $auction), [
            'amount' => 600.00,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('notifications', [
            'user_id' => $buyerA->id,
            'type'    => 'outbid',
        ]);

        // Buyer B places higher bid supplanted Buyer A
        $this->actingAs($buyerB)->post(route('auctions.placeBid', $auction), [
            'amount' => 700.00,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $buyerA->id,
            'type'    => 'outbid',
            'title'   => 'You have been outbid!',
        ]);

        $notification = Notification::where('user_id', $buyerA->id)
            ->where('type', 'outbid')
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString('700.00', $notification->message);
    }

    /**
     * Test 8: No outbid notification when user increases their own leading bid.
     */
    public function test_no_outbid_notification_when_user_increases_own_leading_bid(): void
    {
        $auction = $this->createAuction([
            'starts_at'         => now()->subHour(),
            'ends_at'           => now()->addHours(2),
            'starting_price'    => 500.00,
            'minimum_increment' => 50.00,
            'current_price'     => 500.00,
        ]);

        $buyer = $this->createBuyer('Self Bidding Buyer');

        // First bid
        $this->actingAs($buyer)->post(route('auctions.placeBid', $auction), [
            'amount' => 600.00,
        ])->assertSessionHasNoErrors();

        // Increase own bid
        $this->actingAs($buyer)->post(route('auctions.placeBid', $auction), [
            'amount' => 700.00,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('notifications', [
            'user_id' => $buyer->id,
            'type'    => 'outbid',
        ]);
    }

    /**
     * Test 9: Auction show view renders countdown timer container with valid end timestamp.
     */
    public function test_auction_show_view_renders_countdown_timer_container_with_valid_timestamp(): void
    {
        $endsAt = now()->addHours(2);
        $auction = $this->createAuction([
            'ends_at' => $endsAt,
            'status'  => 'live',
        ]);

        $response = $this->get(route('auctions.show', $auction));

        $response->assertStatus(200);
        $response->assertSee('data-countdown="' . $auction->ends_at->toIso8601String() . '"', false);
        $response->assertSee('timer-display');
    }

    /**
     * Test 10: Idempotency: Running command multiple times does not duplicate orders or invoices.
     */
    public function test_idempotency_running_command_multiple_times_does_not_duplicate_records(): void
    {
        $auction = $this->createAuction([
            'starting_price' => 500.00,
            'reserve_price'  => 800.00,
            'ends_at'        => now()->subMinutes(5),
            'status'         => 'live',
        ]);

        $buyer = $this->createBuyer('Idempotent Buyer');

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyer->id,
            'amount'     => 900.00,
        ]);

        // First execution: closes auction and creates 1 order & 1 invoice
        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 1 expired auction(s). 1 winning order(s) generated.')
            ->assertExitCode(0);

        $this->assertEquals(1, Order::count());
        $this->assertEquals(1, SellerOrder::count());
        $this->assertEquals(1, OrderItem::count());
        $this->assertEquals(1, Invoice::count());
        $this->assertEquals('ended', $auction->fresh()->status);

        // Second execution: must not process ended auction again
        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 0 expired auction(s). 0 winning order(s) generated.')
            ->assertExitCode(0);

        // Third execution
        $this->artisan('auctions:close-expired')
            ->expectsOutputToContain('Closed 0 expired auction(s). 0 winning order(s) generated.')
            ->assertExitCode(0);

        $this->assertEquals(1, Order::count());
        $this->assertEquals(1, SellerOrder::count());
        $this->assertEquals(1, OrderItem::count());
        $this->assertEquals(1, Invoice::count());
    }
}
