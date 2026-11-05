<?php

namespace Tests\Feature\Auction;

use App\Console\Commands\CloseExpiredAuctions;
use App\Models\Address;
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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuditorForensicAuctionStressTest extends TestCase
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
            'name'     => 'Forensic Seller',
            'email'    => 'forensic_seller@bazaario.test',
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ]);

        $this->sellerProfile = SellerProfile::create([
            'user_id'         => $this->sellerUser->id,
            'shop_name'       => 'Forensic Test Stall',
            'shop_slug'       => 'forensic-stall-' . Str::random(4),
            'seller_type'     => 'Kirana Store',
            'city'            => 'Kolkata',
            'state'           => 'West Bengal',
            'country'         => 'India',
            'status'          => 'approved',
            'commission_rate' => 12.50, // Non-standard commission rate to test math
        ]);

        $this->category = Category::firstOrCreate(
            ['slug' => 'artisan'],
            ['name' => 'Artisan Items', 'status' => 'active']
        );

        $this->product = Product::create([
            'seller_id'           => $this->sellerUser->id,
            'category_id'         => $this->category->id,
            'name'                => 'Vintage Brass Bell',
            'slug'                => 'vintage-brass-bell-' . Str::random(4),
            'sku'                 => 'BZ-BELL-999',
            'sale_type'           => 'auction',
            'unit_type'           => 'piece',
            'price'               => 1000.00,
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
            'starting_price'    => 1000.00,
            'reserve_price'     => 2000.00,
            'current_price'     => 1000.00,
            'minimum_increment' => 100.00,
            'starts_at'         => now()->subHours(2),
            'ends_at'           => now()->subMinute(),
            'status'            => 'live',
            'winner_id'         => null,
        ], $overrides));
    }

    protected function createBuyer(string $name, ?string $addressLine = null): User
    {
        $buyer = User::factory()->create([
            'name'     => $name,
            'role'     => 'user',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ]);

        if ($addressLine) {
            Address::create([
                'user_id'        => $buyer->id,
                'full_name'      => $name . ' Real Name',
                'phone'          => '9876543210',
                'address_line_1' => $addressLine,
                'city'           => 'Durgapur',
                'state'          => 'West Bengal',
                'postal_code'    => '713216',
                'country'        => 'India',
                'is_default'     => 1,
            ]);
        }

        return $buyer;
    }

    /**
     * Vector 1: Exact Reserve Boundary Test
     * Bid equals reserve price exactly (₹2000.00) -> MUST WIN.
     */
    public function test_exact_reserve_price_boundary_win(): void
    {
        $auction = $this->createAuction(['reserve_price' => 2000.00]);
        $buyer = $this->createBuyer('Exact Winner', 'Plot 4, Sector 5');

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyer->id,
            'amount'     => 2000.00,
        ]);

        $this->artisan('auctions:close-expired')->assertExitCode(0);

        $fresh = $auction->fresh();
        $this->assertEquals('ended', $fresh->status);
        $this->assertEquals($buyer->id, $fresh->winner_id);
        $this->assertEquals(2000.00, (float) $fresh->current_price);

        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(2000.00, (float) $order->total_amount);
        $this->assertEquals('Plot 4, Sector 5', $order->delivery_address_line_1);
    }

    /**
     * Vector 2: Reserve Failure Boundary Test
     * Bid is 1 paisa below reserve (₹1999.99 vs ₹2000.00) -> MUST FAIL to win.
     */
    public function test_one_paisa_below_reserve_does_not_win(): void
    {
        $auction = $this->createAuction(['reserve_price' => 2000.00]);
        $buyer = $this->createBuyer('Unlucky Bidder');

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyer->id,
            'amount'     => 1999.99,
        ]);

        $this->artisan('auctions:close-expired')->assertExitCode(0);

        $fresh = $auction->fresh();
        $this->assertEquals('ended', $fresh->status);
        $this->assertNull($fresh->winner_id);
        $this->assertEquals(0, Order::count());
        $this->assertEquals(0, Invoice::count());
    }

    /**
     * Vector 3: Financial Calculations & SellerOrder/Invoice Symmetry Test
     * Commission rate: 12.50% on ₹3000.00 bid:
     * Commission amount = 3000 * 0.125 = ₹375.00
     * Payout amount = 3000 - 375 = ₹2625.00
     * Order total = ₹3000.00, Invoice total = ₹3000.00, OrderItem unit = ₹3000.00.
     */
    public function test_financial_ledger_integrity_and_commission_symmetry(): void
    {
        $auction = $this->createAuction(['reserve_price' => 2000.00]);
        $buyer = $this->createBuyer('Financial Checker');

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyer->id,
            'amount'     => 3000.00,
        ]);

        $this->artisan('auctions:close-expired')->assertExitCode(0);

        $order = Order::where('user_id', $buyer->id)->firstOrFail();
        $sellerOrder = SellerOrder::where('order_id', $order->id)->firstOrFail();
        $orderItem = OrderItem::where('seller_order_id', $sellerOrder->id)->firstOrFail();
        $invoice = Invoice::where('order_id', $order->id)->firstOrFail();

        $this->assertEquals(3000.00, (float) $order->total_amount);
        $this->assertEquals(3000.00, (float) $order->subtotal);
        $this->assertEquals(3000.00, (float) $sellerOrder->subtotal);
        $this->assertEquals(12.50, (float) $sellerOrder->commission_rate);
        $this->assertEquals(375.00, (float) $sellerOrder->commission_amount);
        $this->assertEquals(2625.00, (float) $sellerOrder->payout_amount);
        $this->assertEquals(3000.00, (float) $orderItem->unit_price);
        $this->assertEquals(3000.00, (float) $orderItem->total_price);
        $this->assertEquals(3000.00, (float) $invoice->total);
        $this->assertEquals(3000.00, (float) $invoice->subtotal);
    }

    /**
     * Vector 4: Multi-Bidder Outbid Notification Cascade
     * 3 buyers: A bids 1100, B bids 1200, C bids 1300.
     * Buyer A notified once (supplanted by B).
     * Buyer B notified once (supplanted by C).
     * Buyer C notified zero times (currently winning).
     */
    public function test_multi_bidder_notification_cascade(): void
    {
        $auction = $this->createAuction([
            'starts_at'         => now()->subHour(),
            'ends_at'           => now()->addHour(),
            'starting_price'    => 1000.00,
            'current_price'     => 1000.00,
            'minimum_increment' => 100.00,
        ]);

        $buyerA = $this->createBuyer('Buyer A');
        $buyerB = $this->createBuyer('Buyer B');
        $buyerC = $this->createBuyer('Buyer C');

        // Step 1: Buyer A bids 1100
        $this->actingAs($buyerA)->post(route('auctions.placeBid', $auction), [
            'amount' => 1100.00,
        ])->assertSessionHasNoErrors();

        // Step 2: Buyer B bids 1200
        $this->actingAs($buyerB)->post(route('auctions.placeBid', $auction), [
            'amount' => 1200.00,
        ])->assertSessionHasNoErrors();

        // Step 3: Buyer C bids 1300
        $this->actingAs($buyerC)->post(route('auctions.placeBid', $auction), [
            'amount' => 1300.00,
        ])->assertSessionHasNoErrors();

        // Check notifications table
        $notifsA = Notification::where('user_id', $buyerA->id)->where('type', 'outbid')->count();
        $notifsB = Notification::where('user_id', $buyerB->id)->where('type', 'outbid')->count();
        $notifsC = Notification::where('user_id', $buyerC->id)->where('type', 'outbid')->count();

        $this->assertEquals(1, $notifsA, 'Buyer A must receive exactly 1 outbid notification');
        $this->assertEquals(1, $notifsB, 'Buyer B must receive exactly 1 outbid notification');
        $this->assertEquals(0, $notifsC, 'Buyer C must receive 0 outbid notifications (is current leader)');
    }

    /**
     * Vector 5: Winner with Missing Address Record
     * Winner has no address row in addresses table.
     * Command must safely fall back to user's name/phone/defaults without throwing SQL NOT NULL errors.
     */
    public function test_winner_without_saved_address_falls_back_safely(): void
    {
        $auction = $this->createAuction(['reserve_price' => 1500.00]);
        $buyer = $this->createBuyer('Homeless Buyer'); // No address created

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyer->id,
            'amount'     => 1600.00,
        ]);

        $this->artisan('auctions:close-expired')->assertExitCode(0);

        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('Homeless Buyer', $order->delivery_full_name);
        $this->assertEquals('Auction Lot Delivery Address', $order->delivery_address_line_1);
        $this->assertEquals('Kolkata', $order->delivery_city);
        $this->assertEquals('West Bengal', $order->delivery_state);
        $this->assertEquals('700001', $order->delivery_postal_code);
    }

    /**
     * Vector 6: Time Boundary Sensitivity
     * Auction A ends 1 second ago -> MUST close.
     * Auction B ends 2 seconds in future -> MUST stay live.
     */
    public function test_exact_time_boundary_sensitivity(): void
    {
        $closedAuction = $this->createAuction([
            'ends_at' => now()->subSecond(),
            'status'  => 'live',
        ]);

        $openAuction = $this->createAuction([
            'ends_at' => now()->addSeconds(3),
            'status'  => 'live',
        ]);

        $this->artisan('auctions:close-expired')->assertExitCode(0);

        $this->assertEquals('ended', $closedAuction->fresh()->status);
        $this->assertEquals('live', $openAuction->fresh()->status);
    }
}
