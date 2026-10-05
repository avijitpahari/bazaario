<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\SellerOrder;
use App\Models\OrderItem;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Payout;
use App\Models\OrderReturn;
use App\Models\Coupon;
use App\Models\CouponUsage;

class ChallengerStressTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $buyerUser;
    protected User $sellerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Admin Gatekeeper',
            'email' => 'admin.stress@bazaario.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->buyerUser = User::create([
            'name' => 'Buyer Challenger',
            'email' => 'buyer.stress@bazaario.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->sellerUser = User::create([
            'name' => 'Seller Challenger',
            'email' => 'seller.stress@bazaario.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'seller',
            'status' => 'active',
        ]);
    }

    protected function createBankedSeller(array $userAttributes = [], array $profileAttributes = []): array
    {
        $user = User::create(array_merge([
            'name' => 'Merchant ' . uniqid(),
            'email' => 'merchant_' . uniqid() . '@bazaario.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'seller',
            'status' => 'active',
        ], $userAttributes));

        $profile = SellerProfile::create(array_merge([
            'user_id' => $user->id,
            'shop_name' => 'Shop ' . uniqid(),
            'shop_slug' => 'shop-' . uniqid(),
            'status' => 'approved',
            'bank_account_number' => '123456789012',
            'bank_ifsc' => 'HDFC0001234',
            'bank_name' => 'HDFC Bank',
            'commission_rate' => 10.00,
            'trust_score' => 90.00,
        ], $profileAttributes));

        return [$user, $profile];
    }

    protected function createOrderWithSeller(User $sellerUser, array $orderAttrs = [], array $sellerOrderAttrs = []): array
    {
        $order = Order::create(array_merge([
            'user_id' => $this->buyerUser->id,
            'order_number' => 'BZ-STRESS-' . uniqid(),
            'subtotal' => 5000.00,
            'total_amount' => 5000.00,
            'order_status' => 'processing',
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'delivery_full_name' => 'Buyer Challenger',
            'delivery_phone' => '9999988888',
            'delivery_address_line_1' => 'MG Road',
            'delivery_city' => 'Bengaluru',
            'delivery_state' => 'Karnataka',
            'delivery_country' => 'India',
            'delivery_postal_code' => '560001',
        ], $orderAttrs));

        $sellerOrder = SellerOrder::create(array_merge([
            'order_id' => $order->id,
            'seller_id' => $sellerUser->id,
            'seller_order_number' => 'BZ-SO-STRESS-' . uniqid(),
            'subtotal' => 5000.00,
            'status' => 'placed',
        ], $sellerOrderAttrs));

        $category = Category::firstOrCreate(
            ['slug' => 'handicrafts'],
            ['name' => 'Handicrafts', 'status' => 'active']
        );

        $product = Product::create([
            'seller_id' => $sellerUser->id,
            'category_id' => $category->id,
            'name' => 'Stress Test Artisan Item ' . uniqid(),
            'slug' => 'stress-artisan-' . uniqid(),
            'price' => 5000.00,
            'stock' => 10,
            'status' => 'active',
        ]);

        $orderItem = OrderItem::create([
            'seller_order_id' => $sellerOrder->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 5000.00,
            'total_price' => 5000.00,
        ]);

        return [$order, $sellerOrder, $orderItem, $product];
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 1. TRANSACTION ROLLBACK TESTS (MIDWAY EXCEPTION RECOVERY)
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Stress-test: If an exception occurs midway through batch payout release,
     * ALL payouts must rollback cleanly to 'pending'. No partial state allowed.
     */
    public function test_batch_payout_rollback_on_midway_exception_leaves_zero_orphaned_state(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        [$user1, $profile1] = $this->createBankedSeller();
        [$user2, $profile2] = $this->createBankedSeller();
        [$user3, $profile3] = $this->createBankedSeller();

        $p1 = Payout::create([
            'seller_id' => $user1->id,
            'gross_amount' => 1000.00,
            'commission_amount' => 100.00,
            'net_amount' => 900.00,
            'status' => 'pending',
        ]);

        $p2 = Payout::create([
            'seller_id' => $user2->id,
            'gross_amount' => 2000.00,
            'commission_amount' => 200.00,
            'net_amount' => 1800.00,
            'status' => 'pending',
        ]);

        $p3 = Payout::create([
            'seller_id' => $user3->id,
            'gross_amount' => 3000.00,
            'commission_amount' => 300.00,
            'net_amount' => 2700.00,
            'status' => 'pending',
        ]);

        // Inject catastrophic midway exception on the 2nd payout update
        $failingPayoutId = $p2->id;
        $listener = function ($model) use ($failingPayoutId) {
            if ($model->id === $failingPayoutId && $model->status === 'paid') {
                throw new \RuntimeException('MIDWAY_CRASH: Bank gateway network severed on lot #2.');
            }
        };

        Payout::getEventDispatcher()->listen('eloquent.updating: ' . Payout::class, $listener);

        try {
            $response = $this->post(route('admin.payouts.batch-release'));
            $response->assertSessionHas('error');
            $this->assertStringContainsString('MIDWAY_CRASH', session('error'));
        } finally {
            Payout::flushEventListeners();
            Payout::bootIfNotBooted();
        }

        // Forensically verify every single record rolled back completely
        $p1Fresh = $p1->fresh();
        $p2Fresh = $p2->fresh();
        $p3Fresh = $p3->fresh();

        $this->assertEquals('pending', $p1Fresh->status, 'Payout 1 must rollback to pending');
        $this->assertNull($p1Fresh->paid_at, 'Payout 1 paid_at must be null');
        $this->assertNull($p1Fresh->payout_reference, 'Payout 1 reference must be null');

        $this->assertEquals('pending', $p2Fresh->status, 'Payout 2 must remain pending');
        $this->assertNull($p2Fresh->paid_at, 'Payout 2 paid_at must be null');

        $this->assertEquals('pending', $p3Fresh->status, 'Payout 3 must remain pending');
        $this->assertNull($p3Fresh->paid_at, 'Payout 3 paid_at must be null');
    }

    /**
     * Stress-test: If an exception occurs midway through dispute arbitration refund,
     * dispute, order, seller_order, and payout all rollback cleanly.
     */
    public function test_dispute_refund_rollback_on_midway_exception_leaves_zero_orphaned_state(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        [$sellerUser, $profile] = $this->createBankedSeller();
        [$order, $sellerOrder, $orderItem] = $this->createOrderWithSeller($sellerUser);

        $payout = Payout::create([
            'seller_id' => $sellerUser->id,
            'seller_order_id' => $sellerOrder->id,
            'gross_amount' => 5000.00,
            'commission_amount' => 500.00,
            'net_amount' => 4500.00,
            'status' => 'pending',
        ]);

        $dispute = OrderReturn::create([
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'user_id' => $this->buyerUser->id,
            'reason' => 'Defective craftsmanship',
            'status' => 'requested',
            'refund_amount' => 5000.00,
        ]);

        // Inject simulated crash when Order payment_status is being changed to refunded
        $listener = function ($model) {
            if ($model->payment_status === 'refunded') {
                throw new \RuntimeException('MIDWAY_CRASH: Bank refund ledger deadlock.');
            }
        };

        Order::getEventDispatcher()->listen('eloquent.updating: ' . Order::class, $listener);

        try {
            $response = $this->post(route('admin.disputes.arbitrate', $dispute->id), [
                'decision' => 'approve',
            ]);
            $response->assertSessionHas('error');
            $this->assertStringContainsString('MIDWAY_CRASH', session('error'));
        } finally {
            Order::flushEventListeners();
            Order::bootIfNotBooted();
        }

        // Forensically verify atomic rollback
        $this->assertEquals('requested', $dispute->fresh()->status, 'Dispute must stay requested');
        $this->assertNull($dispute->fresh()->approved_at, 'Dispute approved_at must stay null');
        $this->assertEquals('paid', $order->fresh()->payment_status, 'Order must remain paid');
        $this->assertEquals('placed', $sellerOrder->fresh()->status, 'Seller order must remain placed');
        $this->assertEquals('pending', $payout->fresh()->status, 'Payout must remain pending');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 2. FINANCIAL GUARDRAILS: BANK CREDENTIALS & ACTIVE DISPUTES
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Payout release strictly blocked when bank credentials are missing (null or empty)
     */
    public function test_payout_release_blocked_when_bank_credentials_missing(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        // Case A: Missing account number
        [$sellerUser1, $profile1] = $this->createBankedSeller([], [
            'bank_account_number' => null,
            'bank_ifsc' => 'HDFC0001234',
        ]);
        $p1 = Payout::create([
            'seller_id' => $sellerUser1->id,
            'gross_amount' => 1000.00,
            'commission_amount' => 100.00,
            'net_amount' => 900.00,
            'status' => 'pending',
        ]);

        $res1 = $this->post(route('admin.payouts.release', $p1->id));
        $res1->assertSessionHas('error');
        $this->assertStringContainsString('bank', strtolower(session('error')));
        $this->assertEquals('pending', $p1->fresh()->status);

        // Case B: Missing IFSC code
        [$sellerUser2, $profile2] = $this->createBankedSeller([], [
            'bank_account_number' => '123456789012',
            'bank_ifsc' => '',
        ]);
        $p2 = Payout::create([
            'seller_id' => $sellerUser2->id,
            'gross_amount' => 1000.00,
            'commission_amount' => 100.00,
            'net_amount' => 900.00,
            'status' => 'pending',
        ]);

        $res2 = $this->post(route('admin.payouts.release', $p2->id));
        $res2->assertSessionHas('error');
        $this->assertStringContainsString('bank', strtolower(session('error')));
        $this->assertEquals('pending', $p2->fresh()->status);
    }

    /**
     * Payout release strictly blocked when active disputes are pending across all active dispute states
     */
    public function test_payout_release_blocked_across_all_active_dispute_states(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $activeDisputeStatuses = ['requested', 'pickup_scheduled', 'received', 'refund_processing'];

        foreach ($activeDisputeStatuses as $disputeStatus) {
            [$sellerUser, $profile] = $this->createBankedSeller();
            [$order, $sellerOrder, $orderItem] = $this->createOrderWithSeller($sellerUser);

            $payout = Payout::create([
                'seller_id' => $sellerUser->id,
                'seller_order_id' => $sellerOrder->id,
                'gross_amount' => 2000.00,
                'commission_amount' => 200.00,
                'net_amount' => 1800.00,
                'status' => 'pending',
            ]);

            $dispute = OrderReturn::create([
                'order_id' => $order->id,
                'order_item_id' => $orderItem->id,
                'user_id' => $this->buyerUser->id,
                'reason' => 'Dispute status: ' . $disputeStatus,
                'status' => $disputeStatus,
                'refund_amount' => 2000.00,
            ]);

            $response = $this->post(route('admin.payouts.release', $payout->id));
            $response->assertSessionHas('error');
            $this->assertStringContainsString('dispute', strtolower(session('error')));
            $this->assertEquals('pending', $payout->fresh()->status, "Payout must remain pending when dispute is in status {$disputeStatus}");
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 3. LIVE AUCTION BIDDING & INCREMENTAL PRICE UPDATES
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Live auction bidding: unauthenticated users rejected
     */
    public function test_auction_bidding_requires_authentication(): void
    {
        [$sellerUser, $profile] = $this->createBankedSeller();
        $category = Category::create(['name' => 'Auction Art', 'slug' => 'auction-art', 'status' => 'active']);
        $product = Product::create([
            'seller_id' => $sellerUser->id,
            'category_id' => $category->id,
            'name' => 'Sculpture ' . uniqid(),
            'slug' => 'sculpture-' . uniqid(),
            'price' => 1000.00,
            'stock' => 1,
            'status' => 'active',
        ]);

        $auction = Auction::create([
            'product_id' => $product->id,
            'seller_id' => $profile->id,
            'starting_price' => 1000.00,
            'current_price' => 1000.00,
            'minimum_increment' => 100.00,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHours(2),
            'status' => 'live',
        ]);

        $response = $this->post(route('auctions.placeBid', $auction->id), [
            'amount' => 1200.00,
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(0, AuctionBid::where('auction_id', $auction->id)->count());
    }

    /**
     * Live auction bidding: requires valid incremental bids and updates current price correctly
     */
    public function test_auction_bidding_requires_valid_incremental_bids_and_updates_price(): void
    {
        $this->actingAs($this->buyerUser, 'user');

        [$sellerUser, $profile] = $this->createBankedSeller();
        $category = Category::create(['name' => 'Antiques', 'slug' => 'antiques', 'status' => 'active']);
        $product = Product::create([
            'seller_id' => $sellerUser->id,
            'category_id' => $category->id,
            'name' => 'Ancient Vase ' . uniqid(),
            'slug' => 'ancient-vase-' . uniqid(),
            'price' => 2000.00,
            'stock' => 1,
            'status' => 'active',
        ]);

        $auction = Auction::create([
            'product_id' => $product->id,
            'seller_id' => $profile->id,
            'starting_price' => 2000.00,
            'current_price' => 2000.00,
            'minimum_increment' => 150.00,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addMinutes(10),
            'status' => 'live',
        ]);

        // Attempt 1: Bid below minimum increment (current 2000 + increment 150 = 2150 required)
        $subMinResponse = $this->post(route('auctions.placeBid', $auction->id), [
            'amount' => 2100.00,
        ]);
        $subMinResponse->assertSessionHasErrors('amount');
        $this->assertEquals(2000.00, (float)$auction->fresh()->current_price);
        $this->assertEquals(0, AuctionBid::where('auction_id', $auction->id)->count());

        // Attempt 2: Exactly minimum valid incremental bid (2150.00)
        $validBidResponse = $this->post(route('auctions.placeBid', $auction->id), [
            'amount' => 2150.00,
        ]);
        $validBidResponse->assertSessionHas('success');

        $auction->refresh();
        $this->assertEquals(2150.00, (float)$auction->current_price);
        $this->assertEquals(1, AuctionBid::where('auction_id', $auction->id)->count());
        $bid1 = AuctionBid::where('auction_id', $auction->id)->first();
        $this->assertEquals(2150.00, (float)$bid1->amount);
        $this->assertEquals($this->buyerUser->id, $bid1->user_id);

        // Attempt 3: Second incremental bid from another bidder
        $anotherBuyer = User::create([
            'name' => 'Second Bidder',
            'email' => 'bidder2_' . uniqid() . '@bazaario.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'status' => 'active',
        ]);
        $this->actingAs($anotherBuyer, 'user');

        // Must be >= 2150 + 150 = 2300.00
        $secondBidResponse = $this->post(route('auctions.placeBid', $auction->id), [
            'amount' => 2400.00,
        ]);
        $secondBidResponse->assertSessionHas('success');

        $auction->refresh();
        $this->assertEquals(2400.00, (float)$auction->current_price);
        $this->assertEquals(2, AuctionBid::where('auction_id', $auction->id)->count());
    }

    /**
     * Anti-sniping extension stress test
     */
    public function test_auction_anti_sniping_extends_expiration_when_bid_within_two_minutes(): void
    {
        $this->actingAs($this->buyerUser, 'user');

        [$sellerUser, $profile] = $this->createBankedSeller();
        $category = Category::create(['name' => 'Jewelry', 'slug' => 'jewelry', 'status' => 'active']);
        $product = Product::create([
            'seller_id' => $sellerUser->id,
            'category_id' => $category->id,
            'name' => 'Diamond Ring ' . uniqid(),
            'slug' => 'diamond-ring-' . uniqid(),
            'price' => 10000.00,
            'stock' => 1,
            'status' => 'active',
        ]);

        $endsAt = now()->addSeconds(60); // 1 minute left (within 2-minute window)
        $auction = Auction::create([
            'product_id' => $product->id,
            'seller_id' => $profile->id,
            'starting_price' => 10000.00,
            'current_price' => 10000.00,
            'minimum_increment' => 500.00,
            'starts_at' => now()->subHour(),
            'ends_at' => $endsAt,
            'status' => 'live',
        ]);

        $response = $this->post(route('auctions.placeBid', $auction->id), [
            'amount' => 10500.00,
        ]);
        $response->assertSessionHas('success');

        $auction->refresh();
        // Expiration should have been extended by 2 minutes
        $this->assertTrue($auction->ends_at->greaterThan($endsAt), 'Auction ends_at should be extended due to anti-sniping');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 4. TAXONOMY SAFEGUARDS: CATEGORY DELETION
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Category deletion safely blocked if active products exist
     */
    public function test_category_deletion_blocked_if_active_products_exist(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        [$sellerUser, $profile] = $this->createBankedSeller();

        $category = Category::create([
            'name' => 'Pottery & Ceramics',
            'slug' => 'pottery-ceramics',
            'status' => 'active',
        ]);

        $product = Product::create([
            'seller_id' => $sellerUser->id,
            'category_id' => $category->id,
            'name' => 'Terracotta Planter',
            'slug' => 'terracotta-planter',
            'price' => 450.00,
            'stock' => 15,
            'status' => 'active',
        ]);

        $response = $this->delete(route('admin.categories.destroy', $category->id));

        $response->assertSessionHas('error');
        $this->assertStringContainsString('active products', session('error'));
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    /**
     * Clean category with zero products and zero children deletes successfully
     */
    public function test_empty_category_deletes_successfully(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $category = Category::create([
            'name' => 'Temporary Category',
            'slug' => 'temporary-category',
            'status' => 'active',
        ]);

        $response = $this->delete(route('admin.categories.destroy', $category->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 5. COUPON SAFEGUARDS: USAGE HISTORY
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Coupon deletion safely blocked when CouponUsage history exists
     */
    public function test_coupon_deletion_blocked_if_coupon_usages_exist(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $coupon = Coupon::create([
            'code' => 'TESTFEST20',
            'discount_type' => 'percentage',
            'discount_value' => 20.00,
            'status' => 'active',
        ]);

        [$sellerUser, $profile] = $this->createBankedSeller();
        [$order] = $this->createOrderWithSeller($sellerUser);

        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $this->buyerUser->id,
            'order_id' => $order->id,
            'discount_amount' => 400.00,
        ]);

        $response = $this->delete(route('admin.coupons.destroy', $coupon->id));

        $response->assertSessionHas('error');
        $this->assertStringContainsString('redeemed', session('error'));
        $this->assertDatabaseHas('coupons', ['id' => $coupon->id]);
    }

    /**
     * Coupon deletion safely blocked when used_count > 0
     */
    public function test_coupon_deletion_blocked_if_used_count_greater_than_zero(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $coupon = Coupon::create([
            'code' => 'USEDCOUNT10',
            'discount_type' => 'fixed',
            'discount_value' => 100.00,
            'used_count' => 5,
            'status' => 'active',
        ]);

        $response = $this->delete(route('admin.coupons.destroy', $coupon->id));

        $response->assertSessionHas('error');
        $this->assertStringContainsString('redeemed', session('error'));
        $this->assertDatabaseHas('coupons', ['id' => $coupon->id]);
    }

    /**
     * Coupon deletion safely blocked when referenced by an Order
     */
    public function test_coupon_deletion_blocked_if_order_references_coupon(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $coupon = Coupon::create([
            'code' => 'ORDERREF50',
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'status' => 'active',
        ]);

        [$sellerUser, $profile] = $this->createBankedSeller();
        [$order] = $this->createOrderWithSeller($sellerUser, [
            'coupon_id' => $coupon->id,
        ]);

        $response = $this->delete(route('admin.coupons.destroy', $coupon->id));

        $response->assertSessionHas('error');
        $this->assertStringContainsString('redeemed', session('error'));
        $this->assertDatabaseHas('coupons', ['id' => $coupon->id]);
    }

    /**
     * Clean coupon with zero usage history deletes successfully
     */
    public function test_empty_coupon_deletes_successfully(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $coupon = Coupon::create([
            'code' => 'CLEANVOUCHER',
            'discount_type' => 'percentage',
            'discount_value' => 15.00,
            'used_count' => 0,
            'status' => 'active',
        ]);

        $response = $this->delete(route('admin.coupons.destroy', $coupon->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 6. ARCHITECTURAL / FORENSIC VERIFICATION OF CONTROLLER WIRING
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Forensic check: The active route 'auctions.placeBid' must point to a controller
     * that executes pessimistic concurrency locking (lockForUpdate) inside a transaction.
     */
    public function test_active_auction_route_uses_concurrency_lock(): void
    {
        $actionName = Route::getRoutes()->getByName('auctions.placeBid')->getActionName();

        // Check which controller is handling the active route
        $this->assertNotEmpty($actionName);

        // Parse class and method
        [$controllerClass, $method] = explode('@', $actionName);
        $reflector = new \ReflectionMethod($controllerClass, $method);
        $fileName = $reflector->getFileName();
        $source = file_get_contents($fileName);

        // Check if the source code of the active controller contains lockForUpdate
        $hasLock = str_contains($source, 'lockForUpdate');
        $this->assertTrue(
            $hasLock,
            "CRITICAL CONCURRENCY FLAW: Active route handler [{$actionName}] in [{$fileName}] does NOT call lockForUpdate()! Concurrent bids can race and overwrite current_price."
        );
    }

    /**
     * Stress-test: Concurrent bid collision demonstration.
     * When two requests race, the active controller allows a stale lower bid to overwrite a higher bid.
     */
    public function test_concurrent_bid_collision_overwrites_higher_bid_due_to_missing_lock(): void
    {
        [$sellerUser, $profile] = $this->createBankedSeller();
        $category = Category::create(['name' => 'Jewelry2', 'slug' => 'jewelry2', 'status' => 'active']);
        $product = Product::create([
            'seller_id' => $sellerUser->id,
            'category_id' => $category->id,
            'name' => 'Gold Coin ' . uniqid(),
            'slug' => 'gold-coin-' . uniqid(),
            'price' => 2000.00,
            'stock' => 1,
            'status' => 'active',
        ]);

        $auction = Auction::create([
            'product_id' => $product->id,
            'seller_id' => $profile->id,
            'starting_price' => 2000.00,
            'current_price' => 2000.00,
            'minimum_increment' => 100.00,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHour(),
            'status' => 'live',
        ]);

        // Bidder 1 bids 2500
        $this->actingAs($this->buyerUser, 'user');
        $this->post(route('auctions.placeBid', $auction->id), ['amount' => 2500.00]);
        $this->assertEquals(2500.00, (float)$auction->fresh()->current_price);

        // Now simulate Bidder 2 who read the auction before Bidder 1's bid (at current_price 2000)
        // Bidder 2 submits 2200. With proper concurrency locking & re-check inside transaction, 
        // 2200 must be REJECTED because current_price is now 2500 (requires >= 2600).
        $bidder2 = User::create([
            'name' => 'Bidder Two',
            'email' => 'bidder2_' . uniqid() . '@bazaario.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->actingAs($bidder2, 'user');
        $controller = app(\App\Http\Controllers\User\AuctionController::class);

        // Stale auction instance representing concurrent request state
        $staleAuction = Auction::find($auction->id);
        $staleAuction->current_price = 2000.00; // simulates request loaded prior to Bidder 1's commit

        $request = \Illuminate\Http\Request::create(route('auctions.placeBid', $auction->id), 'POST', [
            'amount' => 2200.00,
        ]);

        $controller->placeBid($request, $staleAuction);

        // Under safe concurrency locking, current_price must NEVER regress to 2200!
        $this->assertGreaterThanOrEqual(
            2500.00,
            (float)$auction->fresh()->current_price,
            "CRITICAL RACE CONDITION: Stale concurrent bid of 2200 overwrote valid higher bid of 2500! Price regressed from 2500 to " . $auction->fresh()->current_price
        );
    }
}
