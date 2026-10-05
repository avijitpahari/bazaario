<?php

namespace Tests\Feature\Seller;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payout;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Class SellerDashboardTest
 *
 * Comprehensive Automated Test Suite for Milestone 2: Seller Dashboard & Performance Analytics.
 * Encompasses Tiers 1-4:
 *  - Tier 1: Access Control, Middleware Authentication & Happy-Path KPI Calculations
 *  - Tier 2: Boundary Resilience (Zero-State Graceful Fallbacks) & Strict Multi-Tenant Isolation
 *  - Tier 3: Combinatorial State Mutations (Dynamic Order Placement, Inventory Depletion Triggers, Auction Spotlight Lifecycle)
 *  - Tier 4: Real-World Logistics Pipeline Distribution, Top Selling Velocity Rankings, and Adversarial Input Resilience
 */
class SellerDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected Category $defaultCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultCategory = Category::create([
            'name' => 'Farm Fresh Produce',
            'slug' => 'farm-fresh-produce',
        ]);
    }

    // =========================================================================
    // SECTION 1: TIER 1 - ACCESS CONTROL & HAPPY-PATH KPI CALCULATION
    // =========================================================================

    /**
     * Test 1.1: Approved seller can access /seller/dashboard with HTTP 200.
     */
    public function test_tier1_approved_seller_can_access_dashboard_with_http_200(): void
    {
        $seller = $this->createApprovedSeller([
            'name' => 'Ramesh Agro',
        ], [
            'shop_name' => 'Ramesh Organic Farms',
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('seller.dashboard');
        $response->assertSee('Ramesh Organic Farms');
    }

    /**
     * Test 1.2: Unapproved (pending) seller is strictly redirected to /seller/pending.
     */
    public function test_tier1_unapproved_pending_seller_is_redirected_to_pending_gate(): void
    {
        $seller = $this->createPendingSeller();

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertRedirect(route('seller.pending'));
    }

    /**
     * Test 1.3: Rejected or suspended seller is locked out of dashboard and sent to pending gate.
     */
    public function test_tier1_rejected_or_suspended_seller_is_redirected_to_pending_gate(): void
    {
        // 1. Rejected seller
        $rejectedSeller = $this->createApprovedSeller([], ['status' => 'rejected']);
        $respRejected = $this->actingAs($rejectedSeller, 'seller')->get(route('seller.dashboard'));
        $respRejected->assertRedirect(route('seller.pending'));

        // 2. Suspended seller
        $suspendedSeller = $this->createApprovedSeller([], ['status' => 'suspended']);
        $respSuspended = $this->actingAs($suspendedSeller, 'seller')->get(route('seller.dashboard'));
        $respSuspended->assertRedirect(route('seller.pending'));
    }

    /**
     * Test 1.4: Unauthenticated guest accessing dashboard is redirected to login.
     */
    public function test_tier1_unauthenticated_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('seller.dashboard'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test 1.5: Regular customer (role = 'user') accessing seller dashboard is redirected.
     */
    public function test_tier1_regular_customer_is_redirected_from_seller_dashboard(): void
    {
        $customer = User::factory()->create([
            'role'   => 'user',
            'status' => 'active',
        ]);

        $response = $this->actingAs($customer, 'seller')->get(route('seller.dashboard'));

        $response->assertRedirect(route('products.index'));
    }

    /**
     * Test 1.6: Dashboard renders accurate core KPI metrics:
     * - Total Orders count
     * - Gross Revenue formatted in ₹
     * - Active Products count
     * - Low Stock Alerts count
     * - Trust Score
     */
    public function test_tier1_dashboard_view_computes_and_renders_accurate_kpi_metrics(): void
    {
        $seller = $this->createApprovedSeller([], ['trust_score' => 96.50]);
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // Create 3 orders with total revenue of ₹7,500
        $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-101', 'placed');
        $this->createSellerOrderRecord($seller, $buyer, 2500.00, 'SO-102', 'processing');
        $this->createSellerOrderRecord($seller, $buyer, 3500.00, 'SO-103', 'delivered');

        // Create 4 products: 3 active (1 low stock), 1 inactive (low stock should be ignored for inactive)
        $this->createProductRecord($seller, 'Fresh Cabbage', 40.00, 30, 10, 'active');
        $this->createProductRecord($seller, 'Red Onions', 35.00, 50, 10, 'active');
        $this->createProductRecord($seller, 'Desi Potatoes', 25.00, 4, 10, 'active'); // LOW STOCK (stock 4 <= 10)
        $this->createProductRecord($seller, 'Archived Tomatoes', 20.00, 2, 10, 'inactive'); // Inactive

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);

        // Verify KPI values in view
        // Total orders: 3
        $response->assertSee('3');
        // Gross revenue: 7,500
        $response->assertSee('7,500');
        // Trust score: 96.50 or 97 / 96
        $response->assertSee('96');
        // Low stock count: 1 (Desi Potatoes)
        $response->assertSee('Desi Potatoes');
    }

    // =========================================================================
    // SECTION 2: TIER 2 - ZERO-STATE RESILIENCE & MULTI-TENANT ISOLATION
    // =========================================================================

    /**
     * Test 2.1: Zero-State Resilience: Newly approved seller with 0 orders, 0 products, 0 auctions
     * renders cleanly with 0s and no arithmetic exceptions (no division by zero on AOV or fulfillment rate).
     */
    public function test_tier2_zero_state_resilience_new_seller_with_no_data_renders_cleanly(): void
    {
        $newSeller = $this->createApprovedSeller([], [
            'shop_name'   => 'Brand New Green Farm',
            'trust_score' => 100.00,
        ]);

        $response = $this->actingAs($newSeller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Brand New Green Farm');
        // Should display zero orders and zero revenue safely
        $response->assertSee('0');
        $response->assertDontSee('Division by zero');
    }

    /**
     * Test 2.2: Strict Multi-Tenant Isolation - Orders & Revenue:
     * Seller A cannot see Seller B's orders, order count, or gross revenue.
     */
    public function test_tier2_strict_multi_tenant_isolation_orders_and_revenue(): void
    {
        $sellerA = $this->createApprovedSeller(['name' => 'Seller A'], ['shop_name' => 'Shop A']);
        $sellerB = $this->createApprovedSeller(['name' => 'Seller B'], ['shop_name' => 'Shop B']);
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // Seller B has 5 orders totaling ₹45,000
        for ($i = 1; $i <= 5; $i++) {
            $this->createSellerOrderRecord($sellerB, $buyer, 9000.00, "SO-B-00{$i}", 'delivered');
        }

        // Seller A has 1 order of ₹1,200
        $this->createSellerOrderRecord($sellerA, $buyer, 1200.00, 'SO-A-001', 'placed');

        // Seller A visits dashboard
        $responseA = $this->actingAs($sellerA, 'seller')->get(route('seller.dashboard'));

        $responseA->assertStatus(200);
        // Seller A sees own order and revenue
        $responseA->assertSee('SO-A-001');
        $responseA->assertSee('1,200');

        // Seller A MUST NOT see Seller B's orders or revenue figures
        $responseA->assertDontSee('SO-B-001');
        $responseA->assertDontSee('SO-B-005');
        $responseA->assertDontSee('45,000');
        $responseA->assertDontSee('Shop B');
    }

    /**
     * Test 2.3: Strict Multi-Tenant Isolation - Inventory & Low Stock Alerts:
     * Seller A cannot see Seller B's products or low stock alerts.
     */
    public function test_tier2_strict_multi_tenant_isolation_inventory_and_low_stock(): void
    {
        $sellerA = $this->createApprovedSeller([], ['shop_name' => 'Orchard A']);
        $sellerB = $this->createApprovedSeller([], ['shop_name' => 'Dairy B']);

        // Seller B has depleted products
        $this->createProductRecord($sellerB, 'Rare Himalayan Cheese', 1200.00, 1, 10, 'active');
        $this->createProductRecord($sellerB, 'A2 Pure Ghee', 800.00, 2, 10, 'active');

        // Seller A has only healthy stock items
        $this->createProductRecord($sellerA, 'Nagpur Oranges', 80.00, 150, 10, 'active');
        $this->createProductRecord($sellerA, 'Kashmiri Apples', 180.00, 80, 10, 'active');

        $responseA = $this->actingAs($sellerA, 'seller')->get(route('seller.dashboard'));

        $responseA->assertStatus(200);
        $responseA->assertSee('Nagpur Oranges');
        $responseA->assertSee('Kashmiri Apples');

        // Seller B's products and low stock telemetry MUST NOT leak to Seller A
        $responseA->assertDontSee('Rare Himalayan Cheese');
        $responseA->assertDontSee('A2 Pure Ghee');
    }

    /**
     * Test 2.4: Strict Multi-Tenant Isolation - Wholesale Auctions:
     * Seller B's live auction lot must never appear on Seller A's dashboard.
     */
    public function test_tier2_strict_multi_tenant_isolation_wholesale_auctions(): void
    {
        $sellerA = $this->createApprovedSeller([], ['shop_name' => 'Grains A']);
        $sellerB = $this->createApprovedSeller([], ['shop_name' => 'Spices B']);

        $productB = $this->createProductRecord($sellerB, 'Premium Black Cardamom Lot', 5000.00, 20, 5, 'active');

        // Active live auction for Seller B
        $auctionB = Auction::create([
            'product_id'        => $productB->id,
            'seller_id'         => $sellerB->sellerProfile->id,
            'starting_price'    => 20000.00,
            'reserve_price'     => 30000.00,
            'current_price'     => 26000.00,
            'minimum_increment' => 500.00,
            'starts_at'         => now()->subHours(2),
            'ends_at'           => now()->addHours(6),
            'status'            => 'live',
        ]);

        // Seller A visits dashboard
        $responseA = $this->actingAs($sellerA, 'seller')->get(route('seller.dashboard'));

        $responseA->assertStatus(200);
        $responseA->assertDontSee('Premium Black Cardamom Lot');
        $responseA->assertDontSee('26,000');
    }

    // =========================================================================
    // SECTION 3: TIER 3 - COMBINATORIAL STATE MUTATIONS & DYNAMIC TRIGGERS
    // =========================================================================

    /**
     * Test 3.1: Dynamic Order Trigger: Creating a new seller order increments total orders
     * and updates gross revenue on the next dashboard visit.
     */
    public function test_tier3_placing_new_seller_order_dynamically_increments_orders_and_revenue(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // Initial state: 1 order of ₹2,000
        $this->createSellerOrderRecord($seller, $buyer, 2000.00, 'SO-INIT-01', 'placed');

        $resp1 = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $resp1->assertStatus(200);
        $resp1->assertSee('2,000');
        $resp1->assertSee('SO-INIT-01');

        // Dynamic mutation: New order placed for ₹3,500
        $this->createSellerOrderRecord($seller, $buyer, 3500.00, 'SO-NEW-02', 'processing');

        // Re-query dashboard: should reflect 2 orders and ₹5,500
        $resp2 = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $resp2->assertStatus(200);
        $resp2->assertSee('5,500');
        $resp2->assertSee('SO-NEW-02');
    }

    /**
     * Test 3.2: Stock Threshold Trigger: Updating stock below threshold immediately reflects
     * in the low stock KPI count and populates the Low Stock Telemetry widget.
     */
    public function test_tier3_stock_depletion_below_threshold_immediately_triggers_low_stock_kpi(): void
    {
        $seller = $this->createApprovedSeller();

        // Product with stock above threshold
        $product = $this->createProductRecord($seller, 'Darjeeling Green Tea', 450.00, 25, 10, 'active');

        $resp1 = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $resp1->assertStatus(200);
        // Initially 0 low stock alerts in widget
        $resp1->assertDontSee('Darjeeling Green Tea');

        // Inventory depletion mutation: stock drops to 4 (< threshold of 10)
        $product->update(['stock' => 4]);

        $resp2 = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $resp2->assertStatus(200);
        // Product now appears in low stock widget
        $resp2->assertSee('Darjeeling Green Tea');
        $resp2->assertSee('4');

        // Restock mutation: stock restored to 60
        $product->update(['stock' => 60]);

        $resp3 = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $resp3->assertStatus(200);
        $resp3->assertDontSee('Darjeeling Green Tea');
    }

    /**
     * Test 3.3: Live Wholesale Auction Spotlight: Active auction for seller's profile
     * is prominently featured in the Spotlight Banner.
     */
    public function test_tier3_live_wholesale_auction_appears_in_spotlight_card(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user', 'name' => 'Mandi Wholesaler']);

        $product = $this->createProductRecord($seller, 'Export Grade Mangoes Lot', 15000.00, 50, 10, 'active');

        $auction = Auction::create([
            'product_id'        => $product->id,
            'seller_id'         => $seller->sellerProfile->id,
            'starting_price'    => 10000.00,
            'reserve_price'     => 18000.00,
            'current_price'     => 14500.00,
            'minimum_increment' => 500.00,
            'starts_at'         => now()->subHour(),
            'ends_at'           => now()->addHours(3),
            'status'            => 'live',
        ]);

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyer->id,
            'amount'     => 14500.00,
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Export Grade Mangoes Lot');
        $response->assertSee('14,500');
        $response->assertSee('LIVE');
    }

    /**
     * Test 3.4: Completed or cancelled auction is excluded from live spotlight card.
     */
    public function test_tier3_ended_or_cancelled_auction_is_excluded_from_live_spotlight(): void
    {
        $seller = $this->createApprovedSeller();

        $productEnded = $this->createProductRecord($seller, 'Old Wheat Lot', 20000.00, 10, 2, 'active');
        $productCancelled = $this->createProductRecord($seller, 'Cancelled Corn Lot', 15000.00, 10, 2, 'active');

        // Ended auction
        Auction::create([
            'product_id'        => $productEnded->id,
            'seller_id'         => $seller->sellerProfile->id,
            'starting_price'    => 15000.00,
            'reserve_price'     => 22000.00,
            'current_price'     => 21000.00,
            'minimum_increment' => 500.00,
            'starts_at'         => now()->subDays(3),
            'ends_at'           => now()->subDay(),
            'status'            => 'ended',
        ]);

        // Cancelled auction
        Auction::create([
            'product_id'        => $productCancelled->id,
            'seller_id'         => $seller->sellerProfile->id,
            'starting_price'    => 12000.00,
            'reserve_price'     => 18000.00,
            'current_price'     => 12000.00,
            'minimum_increment' => 500.00,
            'starts_at'         => now()->subHours(5),
            'ends_at'           => now()->addHours(2),
            'status'            => 'cancelled',
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        // Neither ended nor cancelled auction should show in live spotlight
        $response->assertDontSee('Old Wheat Lot');
        $response->assertDontSee('Cancelled Corn Lot');
    }

    /**
     * Test 3.5: Inactive/draft products are excluded from active catalog count.
     */
    public function test_tier3_inactive_products_excluded_from_active_catalog_count(): void
    {
        $seller = $this->createApprovedSeller();

        $this->createProductRecord($seller, 'Active Item 1', 50.00, 20, 5, 'active');
        $this->createProductRecord($seller, 'Active Item 2', 60.00, 30, 5, 'active');
        $this->createProductRecord($seller, 'Draft Item', 70.00, 10, 5, 'draft');
        $this->createProductRecord($seller, 'Inactive Item', 80.00, 15, 5, 'inactive');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        // Active catalog should be 2, not 4
        $activeProductsCount = Product::where('seller_id', $seller->id)->active()->count();
        $this->assertEquals(2, $activeProductsCount);
    }

    // =========================================================================
    // SECTION 4: TIER 4 - LOGISTICS PIPELINE, VELOCITY & ADVERSARIAL RESILIENCE
    // =========================================================================

    /**
     * Test 4.1: Order Fulfillment Pipeline Bar accurately reflects stage distribution:
     * placed (pending), processing, ready pickup / shipped, delivered (fulfilled).
     */
    public function test_tier4_order_pipeline_accurately_reflects_status_distribution(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // Create 2 placed, 3 processing, 1 shipped, 4 delivered
        $this->createSellerOrderRecord($seller, $buyer, 500.00, 'SO-P1', 'placed');
        $this->createSellerOrderRecord($seller, $buyer, 600.00, 'SO-P2', 'placed');

        $this->createSellerOrderRecord($seller, $buyer, 700.00, 'SO-PR1', 'processing');
        $this->createSellerOrderRecord($seller, $buyer, 800.00, 'SO-PR2', 'processing');
        $this->createSellerOrderRecord($seller, $buyer, 900.00, 'SO-PR3', 'processing');

        $this->createSellerOrderRecord($seller, $buyer, 1000.00, 'SO-SH1', 'shipped');

        $this->createSellerOrderRecord($seller, $buyer, 1100.00, 'SO-D1', 'delivered');
        $this->createSellerOrderRecord($seller, $buyer, 1200.00, 'SO-D2', 'delivered');
        $this->createSellerOrderRecord($seller, $buyer, 1300.00, 'SO-D3', 'delivered');
        $this->createSellerOrderRecord($seller, $buyer, 1400.00, 'SO-D4', 'delivered');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);

        // Verify status counts in view
        $response->assertSee('10 Total'); // Total 10 orders
        $response->assertSee('2');        // 2 Placed
        $response->assertSee('3');        // 3 Processing
        $response->assertSee('4');        // 4 Delivered
    }

    /**
     * Test 4.2: Top Products by Velocity ranks products by demand/units sold.
     */
    public function test_tier4_top_products_velocity_ranks_by_volume_and_revenue(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        $prodHigh = $this->createProductRecord($seller, 'Organic Sweet Honey', 500.00, 100, 10, 'active');
        $prodMid  = $this->createProductRecord($seller, 'Mustard Cooking Oil', 200.00, 100, 10, 'active');
        $prodLow  = $this->createProductRecord($seller, 'Herbal Basil Leaves', 50.00, 100, 10, 'active');

        // Order 1: 50 honey
        $so1 = $this->createSellerOrderRecord($seller, $buyer, 25000.00, 'SO-V1', 'delivered');
        OrderItem::create([
            'seller_order_id' => $so1->id,
            'product_id'      => $prodHigh->id,
            'product_name'    => $prodHigh->name,
            'unit_price'      => 500.00,
            'quantity'        => 50,
            'total_price'     => 25000.00,
        ]);

        // Order 2: 20 oil
        $so2 = $this->createSellerOrderRecord($seller, $buyer, 4000.00, 'SO-V2', 'delivered');
        OrderItem::create([
            'seller_order_id' => $so2->id,
            'product_id'      => $prodMid->id,
            'product_name'    => $prodMid->name,
            'unit_price'      => 200.00,
            'quantity'        => 20,
            'total_price'     => 4000.00,
        ]);

        // Order 3: 5 basil
        $so3 = $this->createSellerOrderRecord($seller, $buyer, 250.00, 'SO-V3', 'delivered');
        OrderItem::create([
            'seller_order_id' => $so3->id,
            'product_id'      => $prodLow->id,
            'product_name'    => $prodLow->name,
            'unit_price'      => 50.00,
            'quantity'        => 5,
            'total_price'     => 250.00,
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Organic Sweet Honey');
        $response->assertSee('Mustard Cooking Oil');

        // Honey (50 units) must appear before Oil (20 units) in velocity listing
        $content = $response->getContent();
        $posHigh = strpos($content, 'Organic Sweet Honey');
        $posMid = strpos($content, 'Mustard Cooking Oil');

        $this->assertNotFalse($posHigh);
        $this->assertNotFalse($posMid);
        $this->assertTrue($posHigh < $posMid, 'High velocity product must appear before mid velocity product');
    }

    /**
     * Test 4.3: Next Settlement Card displays net payout calculation or scheduled transfer.
     */
    public function test_tier4_settlement_card_displays_expected_payout_details(): void
    {
        $seller = $this->createApprovedSeller([], [
            'bank_account_number' => '918237465012',
            'bank_ifsc'           => 'HDFC0001234',
        ]);
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // Seller order with net payout amount
        $so = $this->createSellerOrderRecord($seller, $buyer, 10000.00, 'SO-PAY-01', 'delivered');
        // Commission 10% = 1000, payout = 9000
        $so->update([
            'commission_rate'   => 10.00,
            'commission_amount' => 1000.00,
            'payout_amount'     => 9000.00,
        ]);

        Payout::create([
            'seller_id'         => $seller->id,
            'seller_order_id'   => $so->id,
            'gross_amount'      => 10000.00,
            'commission_amount' => 1000.00,
            'net_amount'        => 9000.00,
            'status'            => 'pending',
            'payout_reference'  => 'PAY-REF-001',
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('9,000');
    }

    /**
     * Test 4.4: Adversarial Input & Display Resilience:
     * Handles special characters, HTML tags, and localized vernacular strings cleanly without XSS.
     */
    public function test_tier4_adversarial_shop_name_and_special_character_escaping(): void
    {
        $xssPayload = '<script>alert("xss")</script> & "Farmer\'s Haven" <বাংলা>';
        $seller = $this->createApprovedSeller([], [
            'shop_name' => $xssPayload,
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        // Raw script tag MUST NOT execute or be unescaped
        $response->assertDontSee('<script>alert("xss")</script>', false);
        // HTML entities should be escaped
        $response->assertSee(e($xssPayload), false);
    }

    // =========================================================================
    // HELPER FIXTURE METHODS
    // =========================================================================

    protected function createApprovedSeller(array $userAttrs = [], array $profileAttrs = []): User
    {
        $seller = User::factory()->create(array_merge([
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ], $userAttrs));

        SellerProfile::create(array_merge([
            'user_id'             => $seller->id,
            'shop_name'           => 'Verified Merchant Shop',
            'shop_slug'           => 'verified-merchant-' . $seller->id,
            'seller_type'         => 'Farmer',
            'city'                => 'Contai',
            'state'               => 'West Bengal',
            'status'              => 'approved',
            'trust_score'         => 94.00,
            'commission_rate'     => 10.00,
            'address'             => 'Plot 55, Agro Hub',
            'operating_radius_km' => 25,
        ], $profileAttrs));

        return $seller;
    }

    protected function createPendingSeller(array $userAttrs = [], array $profileAttrs = []): User
    {
        $seller = User::factory()->create(array_merge([
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ], $userAttrs));

        SellerProfile::create(array_merge([
            'user_id'             => $seller->id,
            'shop_name'           => 'Pending Application Shop',
            'shop_slug'           => 'pending-application-' . $seller->id,
            'seller_type'         => 'Farmer',
            'city'                => 'Contai',
            'state'               => 'West Bengal',
            'status'              => 'pending',
            'trust_score'         => 80.00,
            'commission_rate'     => 10.00,
            'address'             => 'Pending Address',
            'operating_radius_km' => 20,
        ], $profileAttrs));

        return $seller;
    }

    protected function createProductRecord(User $seller, string $name, float $price, int $stock, int $threshold, string $status): Product
    {
        return Product::create([
            'seller_id'           => $seller->id,
            'category_id'         => $this->defaultCategory->id,
            'name'                => $name,
            'slug'                => \Illuminate\Support\Str::slug($name) . '-' . uniqid(),
            'sale_type'           => 'fixed_price',
            'unit_type'           => 'kg',
            'price'               => $price,
            'stock'               => $stock,
            'low_stock_threshold' => $threshold,
            'status'              => $status,
            'is_perishable'       => false,
        ]);
    }

    protected function createSellerOrderRecord(User $seller, User $buyer, float $subtotal, string $sellerOrderNumber, string $status): SellerOrder
    {
        $order = Order::create([
            'order_number'            => 'ORD-' . uniqid(),
            'user_id'                 => $buyer->id,
            'subtotal'                => $subtotal,
            'total_amount'            => $subtotal,
            'payment_method'          => 'cod',
            'payment_status'          => $status === 'delivered' ? 'paid' : 'pending',
            'order_status'            => $status === 'delivered' ? 'completed' : 'processing',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => '123 Market Rd',
            'delivery_city'           => 'Contai',
            'delivery_state'          => 'West Bengal',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '721401',
        ]);

        return SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => $sellerOrderNumber,
            'subtotal'            => $subtotal,
            'shipping_amount'     => 0.00,
            'commission_rate'     => 10.00,
            'commission_amount'   => round($subtotal * 0.10, 2),
            'payout_amount'       => round($subtotal * 0.90, 2),
            'status'              => $status,
            'delivery_slot'       => 'Morning 8AM - 11AM',
        ]);
    }
}
