<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
use App\Models\SiteSetting;

class AdminHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected User $sellerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Admin Boss',
            'email' => 'admin@bazaario.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->regularUser = User::create([
            'name' => 'Regular Buyer',
            'email' => 'buyer@bazaario.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->sellerUser = User::create([
            'name' => 'Merchant Seller',
            'email' => 'seller@bazaario.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'seller',
            'status' => 'active',
        ]);
    }

    protected function createBankedSeller(array $userAttributes = [], array $profileAttributes = []): array
    {
        $user = User::create(array_merge([
            'name' => 'Banked Merchant ' . uniqid(),
            'email' => 'banked_' . uniqid() . '@bazaario.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'seller',
            'status' => 'active',
        ], $userAttributes));

        $profile = SellerProfile::create(array_merge([
            'user_id' => $user->id,
            'shop_name' => 'Merchant Shop ' . uniqid(),
            'shop_slug' => 'merchant-shop-' . uniqid(),
            'status' => 'approved',
            'bank_account_number' => '987654321012',
            'bank_ifsc' => 'SBIN0001234',
            'bank_name' => 'State Bank of India',
            'commission_rate' => 8.50,
            'trust_score' => 95.00,
        ], $profileAttributes));

        return [$user, $profile];
    }

    protected function createOrder(array $attributes = []): Order
    {
        return Order::create(array_merge([
            'user_id' => $this->regularUser->id,
            'order_number' => 'BZ-' . uniqid(),
            'subtotal' => 2000.00,
            'total_amount' => 2000.00,
            'order_status' => 'processing',
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'delivery_full_name' => 'Regular Buyer',
            'delivery_phone' => '9810011223',
            'delivery_address_line_1' => 'Park Street',
            'delivery_city' => 'Kolkata',
            'delivery_state' => 'West Bengal',
            'delivery_country' => 'India',
            'delivery_postal_code' => '700016',
        ], $attributes));
    }

    protected function createDispute(array $attributes = []): OrderReturn
    {
        $order = $attributes['order'] ?? $this->createOrder();
        unset($attributes['order']);

        $sellerOrder = SellerOrder::create([
            'order_id' => $order->id,
            'seller_id' => $this->sellerUser->id,
            'seller_order_number' => 'BZ-SO-' . uniqid(),
            'subtotal' => $order->total_amount,
            'status' => 'placed',
        ]);

        $orderItem = OrderItem::create([
            'seller_order_id' => $sellerOrder->id,
            'product_name' => 'Dispute Sample Item',
            'quantity' => 1,
            'unit_price' => $order->total_amount,
            'total_price' => $order->total_amount,
        ]);

        return OrderReturn::create(array_merge([
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'user_id' => $this->regularUser->id,
            'reason' => 'Defective',
            'description' => 'Dispute item problem',
            'status' => 'requested',
            'refund_amount' => $order->total_amount,
        ], $attributes));
    }

    // ─────────────────────────────────────────────────────────────
    // 1. SECURITY & AUTHORIZATION TESTS
    // ─────────────────────────────────────────────────────────────

    public function test_unauthenticated_users_are_redirected_to_admin_login(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));

        $response = $this->get(route('admin.sellers.index'));
        $response->assertRedirect(route('admin.login'));

        $response = $this->get(route('admin.orders.index'));
        $response->assertRedirect(route('admin.login'));

        $response = $this->post(route('admin.payouts.batch-release'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_users_are_rejected_from_admin_panel(): void
    {
        // Buyer session
        $this->actingAs($this->regularUser, 'admin');
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
        $this->assertFalse(Auth::guard('admin')->check());

        // Seller session
        $this->actingAs($this->sellerUser, 'admin');
        $response = $this->get(route('admin.sellers.index'));
        $response->assertRedirect(route('admin.login'));
        $this->assertFalse(Auth::guard('admin')->check());
    }

    public function test_admin_login_rejects_non_admin_credentials(): void
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'buyer@bazaario.com',
            'password' => 'Secret123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::guard('admin')->check());
    }

    public function test_admin_login_succeeds_for_authenticated_admin(): void
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'admin@bazaario.com',
            'password' => 'Secret123!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertTrue(Auth::guard('admin')->check());
        $this->assertEquals('admin', Auth::guard('admin')->user()->role);
    }

    public function test_admin_logout_terminates_session(): void
    {
        $this->actingAs($this->adminUser, 'admin');
        $this->assertTrue(Auth::guard('admin')->check());

        $response = $this->post(route('admin.logout'));
        $response->assertRedirect(route('admin.login'));
        $this->assertFalse(Auth::guard('admin')->check());
    }

    // ─────────────────────────────────────────────────────────────
    // 2. RENDERING ALL 16 ADMIN VIEWS (EMPTY & POPULATED DB)
    // ─────────────────────────────────────────────────────────────

    public function test_all_16_admin_views_render_cleanly_on_empty_database(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $screens = [
            'admin.dashboard' => [],
            'admin.sellers.index' => [],
            'admin.sellers.approvals' => [],
            'admin.sellers.show' => ['id' => 9999],
            'admin.products.index' => [],
            'admin.categories.index' => [],
            'admin.orders.index' => [],
            'admin.orders.show' => ['id' => 'NON-EXISTENT'],
            'admin.auctions.index' => [],
            'admin.auctions.show' => ['id' => 9999],
            'admin.payouts.index' => [],
            'admin.disputes.index' => [],
            'admin.customers.index' => [],
            'admin.customers.show' => ['id' => 9999],
            'admin.coupons.index' => [],
            'admin.settings.ai' => [],
        ];

        foreach ($screens as $route => $params) {
            $response = $this->get(route($route, $params));
            // Should either return 200 or redirect to directory with graceful error flash (never 500!)
            $status = $response->getStatusCode();
            $this->assertTrue(in_array($status, [200, 302]), "Route {$route} failed with status {$status}");
            $this->assertNotEquals(500, $status, "Route {$route} returned 500 error!");
        }
    }

    public function test_all_16_admin_views_render_200_with_populated_records(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        // Create sample domain records
        $category = Category::create([
            'name' => 'Textiles & Handloom',
            'slug' => 'textiles-handloom',
            'status' => 'active',
        ]);

        $sellerProfile = SellerProfile::create([
            'user_id' => $this->sellerUser->id,
            'shop_name' => 'Royal Bengal Weavers',
            'shop_slug' => 'royal-bengal-weavers',
            'status' => 'approved',
            'commission_rate' => 8.50,
            'trust_score' => 95.00,
            'city' => 'Shantipur',
            'state' => 'West Bengal',
            'country' => 'India',
        ]);

        $product = Product::create([
            'seller_id' => $this->sellerUser->id,
            'category_id' => $category->id,
            'name' => 'Jamdani Silk Saree',
            'slug' => 'jamdani-silk-saree',
            'price' => 4500.00,
            'stock' => 12,
            'sale_type' => 'auction',
            'status' => 'active',
        ]);

        $order = $this->createOrder([
            'order_number' => 'BZ-1001',
            'subtotal' => 4500.00,
            'total_amount' => 4500.00,
            'order_status' => 'processing',
        ]);

        $sellerOrder = SellerOrder::create([
            'order_id' => $order->id,
            'seller_id' => $this->sellerUser->id,
            'seller_order_number' => 'BZ-1001-S1',
            'subtotal' => 4500.00,
            'commission_rate' => 8.50,
            'commission_amount' => 382.50,
            'payout_amount' => 4117.50,
            'status' => 'placed',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'seller_order_id' => $sellerOrder->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 4500.00,
            'total_price' => 4500.00,
        ]);

        $auction = Auction::create([
            'product_id' => $product->id,
            'seller_id' => $sellerProfile->id,
            'starting_price' => 1000.00,
            'reserve_price' => 3000.00,
            'current_price' => 2500.00,
            'minimum_increment' => 100.00,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
            'status' => 'live',
        ]);

        $bid = AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id' => $this->regularUser->id,
            'amount' => 2500.00,
        ]);

        $payout = Payout::create([
            'seller_id' => $this->sellerUser->id,
            'seller_order_id' => $sellerOrder->id,
            'gross_amount' => 4500.00,
            'commission_amount' => 382.50,
            'net_amount' => 4117.50,
            'status' => 'pending',
        ]);

        $dispute = OrderReturn::create([
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'user_id' => $this->regularUser->id,
            'reason' => 'Defective',
            'description' => 'Dispute description test',
            'status' => 'requested',
            'refund_amount' => 4500.00,
        ]);

        $coupon = Coupon::create([
            'code' => 'DIWALI2026',
            'discount_type' => 'percentage',
            'discount_value' => 15.00,
            'starts_at' => now(),
            'status' => 'active',
        ]);

        SiteSetting::set('gemini_model', 'gemini-1.5-pro');

        $screens = [
            'admin.dashboard' => [],
            'admin.sellers.index' => [],
            'admin.sellers.approvals' => [],
            'admin.sellers.show' => ['id' => $sellerProfile->id],
            'admin.products.index' => [],
            'admin.categories.index' => [],
            'admin.orders.index' => [],
            'admin.orders.show' => ['id' => $order->id],
            'admin.auctions.index' => [],
            'admin.auctions.show' => ['id' => $auction->id],
            'admin.payouts.index' => [],
            'admin.disputes.index' => [],
            'admin.customers.index' => [],
            'admin.customers.show' => ['id' => $this->regularUser->id],
            'admin.coupons.index' => [],
            'admin.settings.ai' => [],
        ];

        foreach ($screens as $route => $params) {
            $response = $this->get(route($route, $params));
            $response->assertStatus(200);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // 3. DATABASE TRANSACTION & ATOMIC ROLLBACK TESTS
    // ─────────────────────────────────────────────────────────────

    public function test_approve_seller_executes_inside_transaction(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $seller = SellerProfile::create([
            'user_id' => $this->sellerUser->id,
            'shop_name' => 'Pending Merchant Corp',
            'shop_slug' => 'pending-merchant-corp',
            'status' => 'pending',
            'rejection_reason' => 'Old reason',
        ]);

        $response = $this->post(route('admin.sellers.approve', $seller->id));
        $response->assertSessionHas('success');

        $seller->refresh();
        $this->assertEquals('approved', $seller->status);
        $this->assertNotNull($seller->verified_at);
        $this->assertNull($seller->rejection_reason);
        $this->assertEquals('active', $this->sellerUser->fresh()->status);
    }

    public function test_reject_seller_executes_inside_transaction_with_sanitized_reason(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $seller = SellerProfile::create([
            'user_id' => $this->sellerUser->id,
            'shop_name' => 'Rejected Merchant Corp',
            'shop_slug' => 'rejected-merchant-corp',
            'status' => 'pending',
        ]);

        $response = $this->post(route('admin.sellers.reject', $seller->id), [
            'reason' => '<b>Invalid trade license and tax documentation.</b><script>alert(1)</script>',
        ]);

        $response->assertSessionHas('success');
        $seller->refresh();
        $this->assertEquals('rejected', $seller->status);
        $this->assertEquals('Invalid trade license and tax documentation.', $seller->rejection_reason);
    }

    public function test_end_auction_assigns_winner_atomically(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $sellerProfile = SellerProfile::create([
            'user_id' => $this->sellerUser->id,
            'shop_name' => 'Auction Seller Hub',
            'shop_slug' => 'auction-seller-hub',
            'status' => 'approved',
        ]);

        $category = Category::create(['name' => 'Art', 'slug' => 'art', 'status' => 'active']);
        $product = Product::create([
            'seller_id' => $this->sellerUser->id,
            'category_id' => $category->id,
            'name' => 'Rare Painting',
            'slug' => 'rare-painting',
            'price' => 5000.00,
            'stock' => 1,
            'sale_type' => 'auction',
            'status' => 'active',
        ]);

        $auction = Auction::create([
            'product_id' => $product->id,
            'seller_id' => $sellerProfile->id,
            'starting_price' => 1000.00,
            'reserve_price' => 2000.00,
            'current_price' => 1000.00,
            'minimum_increment' => 100.00,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
            'status' => 'live',
        ]);

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id' => $this->regularUser->id,
            'amount' => 3500.00,
        ]);

        $response = $this->post(route('admin.auctions.end', $auction->id));
        $response->assertSessionHas('success');

        $auction->refresh();
        $this->assertEquals('ended', $auction->status);
        $this->assertEquals($this->regularUser->id, $auction->winner_id);
        $this->assertEquals(3500.00, (float)$auction->current_price);
    }

    public function test_release_payout_updates_escrow_settlement(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        [$sellerUser, $sellerProfile] = $this->createBankedSeller();

        $payout = Payout::create([
            'seller_id' => $sellerUser->id,
            'gross_amount' => 1000.00,
            'commission_amount' => 85.00,
            'net_amount' => 915.00,
            'status' => 'pending',
        ]);

        $response = $this->post(route('admin.payouts.release', $payout->id));
        $response->assertSessionHas('success');

        $payout->refresh();
        $this->assertEquals('paid', $payout->status);
        $this->assertNotNull($payout->paid_at);
        $this->assertStringStartsWith('NEFT-BZ-', $payout->payout_reference);
    }

    public function test_release_payout_blocked_when_seller_lacks_bank_account(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        [$sellerUser, $sellerProfile] = $this->createBankedSeller([], [
            'bank_account_number' => null,
            'bank_ifsc' => null,
        ]);

        $payout = Payout::create([
            'seller_id' => $sellerUser->id,
            'gross_amount' => 1000.00,
            'commission_amount' => 85.00,
            'net_amount' => 915.00,
            'status' => 'pending',
        ]);

        $response = $this->post(route('admin.payouts.release', $payout->id));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('bank account', session('error'));
        $this->assertEquals('pending', $payout->fresh()->status);
    }

    public function test_release_payout_blocked_when_order_has_open_dispute(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        [$sellerUser, $sellerProfile] = $this->createBankedSeller();

        $order = $this->createOrder();
        $sellerOrder = SellerOrder::create([
            'order_id' => $order->id,
            'seller_id' => $sellerUser->id,
            'seller_order_number' => 'BZ-SO-DISP-' . uniqid(),
            'subtotal' => 2000.00,
            'status' => 'placed',
        ]);

        $orderItem = OrderItem::create([
            'seller_order_id' => $sellerOrder->id,
            'product_name' => 'Disputed Item',
            'quantity' => 1,
            'unit_price' => 2000.00,
            'total_price' => 2000.00,
        ]);

        OrderReturn::create([
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'user_id' => $this->regularUser->id,
            'reason' => 'Defective',
            'status' => 'requested',
            'refund_amount' => 2000.00,
        ]);

        $payout = Payout::create([
            'seller_id' => $sellerUser->id,
            'seller_order_id' => $sellerOrder->id,
            'gross_amount' => 2000.00,
            'commission_amount' => 170.00,
            'net_amount' => 1830.00,
            'status' => 'pending',
        ]);

        $response = $this->post(route('admin.payouts.release', $payout->id));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('dispute', session('error'));
        $this->assertEquals('pending', $payout->fresh()->status);
    }

    public function test_batch_release_payouts_rolls_back_cleanly_on_exception(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        [$sellerUser1, $profile1] = $this->createBankedSeller();
        [$sellerUser2, $profile2] = $this->createBankedSeller();

        $p1 = Payout::create([
            'seller_id' => $sellerUser1->id,
            'gross_amount' => 1000.00,
            'commission_amount' => 85.00,
            'net_amount' => 915.00,
            'status' => 'pending',
        ]);

        $p2 = Payout::create([
            'seller_id' => $sellerUser2->id,
            'gross_amount' => 2000.00,
            'commission_amount' => 170.00,
            'net_amount' => 1830.00,
            'status' => 'pending',
        ]);

        $failingId = $p2->id;
        $listener = function ($model) use ($failingId) {
            if ($model->id === $failingId && $model->status === 'paid') {
                throw new \RuntimeException('Simulated payment gateway timeout during batch processing.');
            }
        };

        Payout::getEventDispatcher()->listen('eloquent.updating: ' . Payout::class, $listener);

        try {
            $response = $this->post(route('admin.payouts.batch-release'));
            $response->assertSessionHas('error');
            $this->assertStringContainsString('Simulated payment gateway timeout', session('error'));
        } finally {
            Payout::flushEventListeners();
            Payout::bootIfNotBooted();
        }

        // Verify that atomic rollback prevented partial or corrupted state
        $this->assertEquals('pending', $p1->fresh()->status);
        $this->assertEquals('pending', $p2->fresh()->status);
    }

    public function test_batch_release_payouts_skips_unbanked_or_disputed_sellers(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        // Payout 1: Valid banked seller
        [$bankedUser, $bankedProfile] = $this->createBankedSeller();
        $p1 = Payout::create([
            'seller_id' => $bankedUser->id,
            'gross_amount' => 1000.00,
            'commission_amount' => 85.00,
            'net_amount' => 915.00,
            'status' => 'pending',
        ]);

        // Payout 2: Unbanked seller
        [$unbankedUser, $unbankedProfile] = $this->createBankedSeller([], [
            'bank_account_number' => null,
            'bank_ifsc' => null,
        ]);
        $p2 = Payout::create([
            'seller_id' => $unbankedUser->id,
            'gross_amount' => 2000.00,
            'commission_amount' => 170.00,
            'net_amount' => 1830.00,
            'status' => 'pending',
        ]);

        $response = $this->post(route('admin.payouts.batch-release'));
        $response->assertSessionHas('success');

        $this->assertEquals('paid', $p1->fresh()->status);
        $this->assertEquals('pending', $p2->fresh()->status);
    }

    public function test_arbitrate_dispute_approval_and_refund_triggers(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $order = $this->createOrder([
            'order_number' => 'BZ-DISP-101',
            'subtotal' => 2000.00,
            'total_amount' => 2000.00,
        ]);

        $dispute = $this->createDispute([
            'order' => $order,
            'reason' => 'Broken item',
            'status' => 'requested',
            'refund_amount' => 2000.00,
        ]);

        $response = $this->post(route('admin.disputes.arbitrate', $dispute->id), [
            'decision' => 'approve',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('approved', $dispute->fresh()->status);
        $this->assertEquals('refunded', $order->fresh()->payment_status);
        $this->assertEquals('returned', $dispute->orderItem->sellerOrder->fresh()->status);
    }

    public function test_arbitrate_dispute_approval_voids_pending_seller_payout(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $order = $this->createOrder([
            'order_number' => 'BZ-DISP-VOID-PO',
            'subtotal' => 3000.00,
            'total_amount' => 3000.00,
        ]);

        $dispute = $this->createDispute([
            'order' => $order,
            'reason' => 'Damaged in transit',
            'status' => 'requested',
            'refund_amount' => 3000.00,
        ]);

        $sellerOrder = $dispute->orderItem->sellerOrder;
        $payout = Payout::create([
            'seller_id' => $sellerOrder->seller_id,
            'seller_order_id' => $sellerOrder->id,
            'gross_amount' => 3000.00,
            'commission_amount' => 255.00,
            'net_amount' => 2745.00,
            'status' => 'pending',
        ]);

        $response = $this->post(route('admin.disputes.arbitrate', $dispute->id), [
            'decision' => 'approve',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('approved', $dispute->fresh()->status);
        $this->assertEquals('failed', $payout->fresh()->status);
        $this->assertStringContainsString('DISPUTE-REFUNDED-', (string)$payout->fresh()->payout_reference);
    }

    public function test_arbitrate_dispute_cannot_re_arbitrate_already_resolved_dispute(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $dispute = $this->createDispute([
            'status' => 'approved',
            'refund_amount' => 1000.00,
        ]);

        $response = $this->post(route('admin.disputes.arbitrate', $dispute->id), [
            'decision' => 'reject',
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('already been arbitrated', session('error'));
        $this->assertEquals('approved', $dispute->fresh()->status);
    }

    public function test_arbitrate_dispute_rolls_back_cleanly_on_exception(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $order = $this->createOrder([
            'order_number' => 'BZ-DISP-ROLLBACK',
            'subtotal' => 1500.00,
            'total_amount' => 1500.00,
        ]);

        $dispute = $this->createDispute([
            'order' => $order,
            'reason' => 'Wrong size',
            'status' => 'requested',
            'refund_amount' => 1500.00,
        ]);

        $listener = function ($model) {
            if ($model->payment_status === 'refunded') {
                throw new \RuntimeException('Simulated payment processor exception on dispute refund.');
            }
        };

        Order::getEventDispatcher()->listen('eloquent.updating: ' . Order::class, $listener);

        try {
            $response = $this->post(route('admin.disputes.arbitrate', $dispute->id), [
                'decision' => 'approve',
            ]);
            $response->assertSessionHas('error');
            $this->assertStringContainsString('Simulated payment processor exception', session('error'));
        } finally {
            Order::flushEventListeners();
            Order::bootIfNotBooted();
        }

        // Verify rollback left dispute intact
        $this->assertEquals('requested', $dispute->fresh()->status);
        $this->assertEquals('paid', $order->fresh()->payment_status);
    }

    // ─────────────────────────────────────────────────────────────
    // 4. INPUT VALIDATION & SANITIZATION HARDENING
    // ─────────────────────────────────────────────────────────────

    public function test_invalid_mutation_payloads_are_rejected(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $category = Category::create(['name' => 'Ceramics', 'slug' => 'ceramics', 'status' => 'active']);
        $product = Product::create([
            'seller_id' => $this->sellerUser->id,
            'category_id' => $category->id,
            'name' => 'Clay Pot',
            'slug' => 'clay-pot',
            'price' => 300.00,
            'stock' => 5,
            'status' => 'active',
        ]);

        // Negative stock on product update
        $res = $this->post(route('admin.products.update-stock', $product->id), [
            'stock' => -10,
            'price' => 300,
        ]);
        $res->assertSessionHasErrors('stock');

        // Negative price on product update
        $res = $this->post(route('admin.products.update-stock', $product->id), [
            'stock' => 10,
            'price' => -50,
        ]);
        $res->assertSessionHasErrors('price');

        // Invalid commission rate > 100
        $seller = SellerProfile::create([
            'user_id' => $this->sellerUser->id,
            'shop_name' => 'Commission Test Shop',
            'shop_slug' => 'commission-test-shop',
        ]);
        $res = $this->post(route('admin.sellers.commission', $seller->id), [
            'commission_rate' => 150,
        ]);
        $res->assertSessionHasErrors('commission_rate');

        // Invalid coupon: negative discount value
        $res = $this->post(route('admin.coupons.store'), [
            'code' => 'INVALID_CODE',
            'discount_type' => 'percentage',
            'discount_value' => -5,
        ]);
        $res->assertSessionHasErrors('discount_value');

        // Invalid dispute decision
        $order = $this->createOrder([
            'order_number' => 'BZ-TEST-DISP',
            'subtotal' => 500.00,
            'total_amount' => 500.00,
        ]);
        $dispute = $this->createDispute([
            'order' => $order,
            'status' => 'requested',
            'refund_amount' => 500.00,
        ]);
        $res = $this->post(route('admin.disputes.arbitrate', $dispute->id), [
            'decision' => 'malicious_input',
        ]);
        $res->assertSessionHasErrors('decision');
    }

    public function test_coupon_store_sanitizes_and_uppercases_code(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->post(route('admin.coupons.store'), [
            'code' => '<b>bengal2026</b>',
            'discount_type' => 'percentage',
            'discount_value' => 20.00,
            'minimum_order_amount' => 500,
            'usage_limit' => 100,
        ]);

        $response->assertSessionHas('success');
        $coupon = Coupon::where('discount_value', 20.00)->first();
        $this->assertNotNull($coupon);
        $this->assertEquals('BENGAL2026', $coupon->code);
    }

    public function test_category_creation_sanitizes_html_tags(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->post(route('admin.categories.store'), [
            'name' => '<script>alert(1)</script>Wooden Crafts',
            'description' => '<b>Traditional handmade woodwork.</b>',
        ]);

        $response->assertSessionHas('success');
        $category = Category::where('slug', 'wooden-crafts')->orWhere('name', 'Wooden Crafts')->first();
        $this->assertNotNull($category);
        $this->assertEquals('Wooden Crafts', $category->name);
        $this->assertEquals('Traditional handmade woodwork.', $category->description);
    }

    public function test_reject_seller_suspends_associated_user_account(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $sellerUser = User::create([
            'name' => 'Fraud Seller',
            'email' => 'fraud@bazaario.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'seller',
            'status' => 'active',
        ]);

        $sellerProfile = SellerProfile::create([
            'user_id' => $sellerUser->id,
            'shop_name' => 'Fraud Shop',
            'shop_slug' => 'fraud-shop',
            'status' => 'pending',
        ]);

        $response = $this->post(route('admin.sellers.reject', $sellerProfile->id), [
            'reason' => 'Forged documents submitted',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('rejected', $sellerProfile->fresh()->status);
        $this->assertEquals('suspended', $sellerUser->fresh()->status);
    }

    public function test_auction_safeguards_for_ended_and_cancelled_states(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        [$sellerUser, $sellerProfile] = $this->createBankedSeller();
        $category = Category::create(['name' => 'Gems', 'slug' => 'gems', 'status' => 'active']);
        $product = Product::create([
            'seller_id' => $sellerUser->id,
            'category_id' => $category->id,
            'name' => 'Ruby Gem',
            'slug' => 'ruby-gem',
            'price' => 10000.00,
            'stock' => 1,
            'sale_type' => 'auction',
            'status' => 'active',
        ]);

        // Cancelled auction cannot be ended
        $cancelledAuction = Auction::create([
            'product_id' => $product->id,
            'seller_id' => $sellerProfile->id,
            'starting_price' => 5000.00,
            'current_price' => 5000.00,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
            'status' => 'cancelled',
        ]);
        $res1 = $this->post(route('admin.auctions.end', $cancelledAuction->id));
        $res1->assertSessionHas('error');

        // Ended auction cannot be cancelled
        $endedAuction = Auction::create([
            'product_id' => $product->id,
            'seller_id' => $sellerProfile->id,
            'starting_price' => 5000.00,
            'current_price' => 5000.00,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
            'status' => 'ended',
        ]);
        $res2 = $this->post(route('admin.auctions.cancel', $endedAuction->id));
        $res2->assertSessionHas('error');
    }

    public function test_coupon_validation_rejects_percentage_discount_greater_than_100(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->post(route('admin.coupons.store'), [
            'code' => 'SUPERDISCOUNT',
            'discount_type' => 'percentage',
            'discount_value' => 120.00,
            'minimum_order_amount' => 100,
        ]);

        $response->assertSessionHasErrors('discount_value');
        $this->assertDatabaseMissing('coupons', ['code' => 'SUPERDISCOUNT']);
    }

    public function test_suspended_admin_is_denied_access_and_logged_out(): void
    {
        $suspendedAdmin = User::create([
            'name' => 'Suspended Admin',
            'email' => 'badadmin@bazaario.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'admin',
            'status' => 'suspended',
        ]);

        // Login attempt fails
        $loginRes = $this->post(route('admin.login.submit'), [
            'email' => 'badadmin@bazaario.com',
            'password' => 'Secret123!',
        ]);
        $loginRes->assertSessionHasErrors('email');
        $this->assertFalse(Auth::guard('admin')->check());

        // Acting as suspended admin is intercepted by middleware
        $this->actingAs($suspendedAdmin, 'admin');
        $dashRes = $this->get(route('admin.dashboard'));
        $dashRes->assertRedirect(route('admin.login'));
        $this->assertFalse(Auth::guard('admin')->check());
    }

    public function test_category_duplicate_slug_resolves_without_crashing(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        Category::create([
            'name' => 'Existing Category',
            'slug' => 'handicrafts',
            'status' => 'active',
        ]);

        // Creating category with different name whose slug collides with existing slug
        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Handicrafts',
            'description' => 'Duplicate slug crafts',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Category::where('slug', 'handicrafts-1')->exists());
    }

    public function test_release_payout_blocked_for_failed_or_voided_payouts(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        [$sellerUser, $sellerProfile] = $this->createBankedSeller();

        $payout = Payout::create([
            'seller_id' => $sellerUser->id,
            'gross_amount' => 1000.00,
            'commission_amount' => 85.00,
            'net_amount' => 915.00,
            'status' => 'failed',
            'payout_reference' => 'DISPUTE-REFUNDED-20260928',
        ]);

        $response = $this->post(route('admin.payouts.release', $payout->id));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('failed', session('error'));
        $this->assertEquals('failed', $payout->fresh()->status);
    }

    public function test_release_payout_blocked_when_associated_order_is_cancelled_or_refunded(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        [$sellerUser, $sellerProfile] = $this->createBankedSeller();

        $order = $this->createOrder([
            'order_status' => 'cancelled',
            'payment_status' => 'paid',
        ]);

        $sellerOrder = SellerOrder::create([
            'order_id' => $order->id,
            'seller_id' => $sellerUser->id,
            'seller_order_number' => 'BZ-SO-CAN-' . uniqid(),
            'subtotal' => 2000.00,
            'status' => 'cancelled',
        ]);

        $payout = Payout::create([
            'seller_id' => $sellerUser->id,
            'seller_order_id' => $sellerOrder->id,
            'gross_amount' => 2000.00,
            'commission_amount' => 170.00,
            'net_amount' => 1830.00,
            'status' => 'pending',
        ]);

        $response = $this->post(route('admin.payouts.release', $payout->id));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('cancelled', session('error'));
        $this->assertEquals('pending', $payout->fresh()->status);
    }

    public function test_batch_release_payouts_skips_cancelled_or_refunded_orders(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        [$sellerUser1, $profile1] = $this->createBankedSeller();
        [$sellerUser2, $profile2] = $this->createBankedSeller();

        // Valid order
        $order1 = $this->createOrder(['order_status' => 'processing']);
        $so1 = SellerOrder::create([
            'order_id' => $order1->id,
            'seller_id' => $sellerUser1->id,
            'seller_order_number' => 'BZ-SO-OK-' . uniqid(),
            'subtotal' => 1000.00,
            'status' => 'placed',
        ]);
        $p1 = Payout::create([
            'seller_id' => $sellerUser1->id,
            'seller_order_id' => $so1->id,
            'gross_amount' => 1000.00,
            'commission_amount' => 85.00,
            'net_amount' => 915.00,
            'status' => 'pending',
        ]);

        // Cancelled order
        $order2 = $this->createOrder(['order_status' => 'cancelled']);
        $so2 = SellerOrder::create([
            'order_id' => $order2->id,
            'seller_id' => $sellerUser2->id,
            'seller_order_number' => 'BZ-SO-BAD-' . uniqid(),
            'subtotal' => 2000.00,
            'status' => 'cancelled',
        ]);
        $p2 = Payout::create([
            'seller_id' => $sellerUser2->id,
            'seller_order_id' => $so2->id,
            'gross_amount' => 2000.00,
            'commission_amount' => 170.00,
            'net_amount' => 1830.00,
            'status' => 'pending',
        ]);

        $response = $this->post(route('admin.payouts.batch-release'));
        $response->assertSessionHas('success');

        $this->assertEquals('paid', $p1->fresh()->status);
        $this->assertEquals('pending', $p2->fresh()->status);
    }

    public function test_update_order_status_to_cancelled_or_refunded_voids_pending_seller_payouts(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        [$sellerUser, $sellerProfile] = $this->createBankedSeller();

        $order = $this->createOrder([
            'order_status' => 'processing',
            'payment_status' => 'paid',
        ]);

        $sellerOrder = SellerOrder::create([
            'order_id' => $order->id,
            'seller_id' => $sellerUser->id,
            'seller_order_number' => 'BZ-SO-VOID-' . uniqid(),
            'subtotal' => 2500.00,
            'status' => 'placed',
        ]);

        $payout = Payout::create([
            'seller_id' => $sellerUser->id,
            'seller_order_id' => $sellerOrder->id,
            'gross_amount' => 2500.00,
            'commission_amount' => 212.50,
            'net_amount' => 2287.50,
            'status' => 'pending',
        ]);

        $response = $this->post(route('admin.orders.update-status', $order->id), [
            'order_status' => 'cancelled',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('cancelled', $order->fresh()->order_status);
        $this->assertEquals('cancelled', $sellerOrder->fresh()->status);
        $this->assertEquals('failed', $payout->fresh()->status);
        $this->assertStringContainsString('ORDER-CANCELLED-', (string)$payout->fresh()->payout_reference);
    }

    public function test_toggle_seller_status_blocks_unverified_merchants_pending_kyc(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        [$sellerUser, $sellerProfile] = $this->createBankedSeller([], [
            'status' => 'pending',
        ]);

        $response = $this->post(route('admin.sellers.toggle-status', $sellerProfile->id));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('pending', session('error'));
        $this->assertEquals('pending', $sellerProfile->fresh()->status);
    }

    public function test_delete_category_blocked_when_subcategories_exist(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $parent = Category::create([
            'name' => 'Parent Handicrafts',
            'slug' => 'parent-handicrafts',
            'status' => 'active',
        ]);

        $child = Category::create([
            'parent_id' => $parent->id,
            'name' => 'Clay Pottery Subcategory',
            'slug' => 'clay-pottery-subcategory',
            'status' => 'active',
        ]);

        $response = $this->delete(route('admin.categories.destroy', $parent->id));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('sub-categories', session('error'));
        $this->assertDatabaseHas('categories', ['id' => $parent->id]);
    }

    public function test_delete_product_blocked_when_product_has_scheduled_auction(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        [$sellerUser, $sellerProfile] = $this->createBankedSeller();
        $category = Category::create(['name' => 'Art', 'slug' => 'art', 'status' => 'active']);
        $product = Product::create([
            'seller_id' => $sellerUser->id,
            'category_id' => $category->id,
            'name' => 'Scheduled Painting',
            'slug' => 'scheduled-painting',
            'price' => 5000.00,
            'stock' => 1,
            'sale_type' => 'auction',
            'status' => 'active',
        ]);

        $auction = Auction::create([
            'product_id' => $product->id,
            'seller_id' => $sellerProfile->id,
            'starting_price' => 2000.00,
            'current_price' => 2000.00,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDays(3),
            'status' => 'scheduled',
        ]);

        $response = $this->delete(route('admin.products.destroy', $product->id));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('featured in an active or scheduled auction', session('error'));
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_csrf_token_validation_enforced_on_admin_mutation_endpoints(): void
    {
        // 1. Verify no admin routes are excluded from CSRF validation
        $testMiddleware = new class($this->app, $this->app['encrypter']) extends \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken {
            protected function runningUnitTests() {
                return false;
            }
            public function testInExceptArray($request): bool {
                return $this->inExceptArray($request);
            }
        };

        $dummyAdminPostRequest = \Illuminate\Http\Request::create('/admin/coupons', 'POST');
        $this->assertFalse(
            $testMiddleware->testInExceptArray($dummyAdminPostRequest),
            'Admin mutation routes must never be exempt from CSRF token verification.'
        );

        // 2. Test that token mismatch throws TokenMismatchException (419) when runningUnitTests bypass is disengaged
        $session = app('session.store');
        $session->start();
        $dummyAdminPostRequest->setLaravelSession($session);

        $executed = false;
        try {
            $testMiddleware->handle($dummyAdminPostRequest, function ($req) use (&$executed) {
                $executed = true;
            });
            $this->fail('Expected TokenMismatchException was not thrown on missing CSRF token.');
        } catch (\Illuminate\Session\TokenMismatchException $e) {
            $this->assertStringContainsString('CSRF', $e->getMessage());
        }
        $this->assertFalse($executed);

        // 3. Test that valid token matches and allows request to proceed
        $validTokenRequest = \Illuminate\Http\Request::create('/admin/coupons', 'POST', [
            '_token' => $session->token(),
        ]);
        $validTokenRequest->setLaravelSession($session);

        $passed = false;
        $testMiddleware->handle($validTokenRequest, function ($req) use (&$passed) {
            $passed = true;
            return response('OK');
        });
        $this->assertTrue($passed, 'Request with valid matching CSRF token must pass verification.');

        // 4. Verify all admin views containing mutation forms render the @csrf token hidden field
        $this->actingAs($this->adminUser, 'admin');

        $screensWithForms = [
            route('admin.coupons.index'),
            route('admin.categories.index'),
            route('admin.settings.ai'),
            route('admin.payouts.index'),
        ];

        foreach ($screensWithForms as $screenUrl) {
            $response = $this->get($screenUrl);
            $response->assertStatus(200);
            $response->assertSee('name="_token"', false);
        }
    }
}
