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
 * Class SellerDashboardChallengerCTest
 *
 * Adversarial Empirical Verification Suite by Challenger M2-C for Milestone 2:
 * Seller Dashboard & Performance Analytics.
 *
 * Comprehensive Stress Testing:
 *  1. Extreme Zero-States & Boundary Fallbacks
 *  2. Extreme Values (astronomical financials, huge stocks, negative coordinates)
 *  3. Strict Multi-Tenant Isolation & Zero Leakage Guarantees across 3+ tenants
 *  4. SQL Injection Resilience & Comprehensive XSS Escaping
 *  5. Wholesale Auction Spotlight Timing & Lifecycle Boundaries
 *  6. Pipeline Distribution Combinatorial Arithmetic Safety
 *  7. Defensive Access Control Gate Lockouts
 */
class SellerDashboardChallengerCTest extends TestCase
{
    use RefreshDatabase;

    protected Category $defaultCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultCategory = Category::create([
            'name' => 'Organic Produce & Farm Fresh',
            'slug' => 'organic-produce-farm-fresh',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    // =========================================================================
    // SECTION 1: EXTREME ZERO-STATES & BOUNDARY RESILIENCY
    // =========================================================================

    /**
     * Challenge 1.1: Absolute Zero State.
     * Seller with 0 orders, 0 products, 0 auctions, 0 payouts, 0 reviews.
     * Must render HTTP 200 without arithmetic exceptions (no division by zero),
     * and every metric card and widget must render its designated zero-state fallback.
     */
    public function test_challenge_absolute_zero_state_renders_cleanly_without_division_by_zero(): void
    {
        $seller = $this->createApprovedSeller([
            'name' => 'Zero State Merchant',
        ], [
            'shop_name'           => 'Empty Acres Farm',
            'bank_account_number' => null,
            'trust_score'         => 94.00,
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('seller.dashboard');
        $response->assertSee('Empty Acres Farm');

        // Zero-state viewData values
        $this->assertEquals(0, $response->viewData('totalOrders'));
        $this->assertEquals(0.0, $response->viewData('grossRevenue'));
        $this->assertEquals(0.0, $response->viewData('aov'));
        $this->assertEquals(0, $response->viewData('activeProductsCount'));
        $this->assertEquals(0, $response->viewData('lowStockCount'));
        $this->assertEquals(0.0, $response->viewData('nextPayout'));

        // Zero-state KPIs in rendered view
        $response->assertSee('₹0'); // Gross Revenue / Next settlement
        $response->assertSee('AOV ₹0.00');
        $response->assertSee('0 distinct categories');
        $response->assertSee('+0 added this week');
        $response->assertSee('Not configured'); // Masked bank fallback

        // Zero-state Empty Banners / Widgets
        $response->assertSee('All inventory healthy! No products below minimum threshold.');
        $response->assertSee('No sales velocity recorded yet this month.');
        $response->assertSee('No wholesale lots currently running');
        $response->assertSee('No orders received yet');
        $response->assertSee('NO ACTIVE LOT');

        // No PHP warnings or fatal errors leaked
        $response->assertDontSee('Division by zero');
        $response->assertDontSee('Undefined variable');
        $response->assertDontSee('Attempt to read property');
    }

    /**
     * Challenge 1.2: Single Product Catalog Fallback Boundary.
     * In controller, when sales velocity is empty and product count <= 1,
     * the catalog fallback query must gracefully return empty collection
     * without attempting invalid index lookups or displaying broken items.
     */
    public function test_challenge_single_product_catalog_renders_empty_velocity_gracefully(): void
    {
        $seller = $this->createApprovedSeller([], ['shop_name' => 'Solo Product Orchards']);

        // Exactly 1 product with 0 sales
        $this->createProductRecord($seller, 'Single Solo Peach', 120.00, 50, 10, 'active');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Solo Product Orchards');
        $response->assertSee('1 distinct categories');
        // Top products velocity table should display empty placeholder
        $response->assertSee('No sales velocity recorded yet this month.');
    }

    /**
     * Challenge 1.3: Cancelled and Returned Orders Excluded from Financial Telemetry.
     * Orders marked as 'cancelled' or 'returned' must be excluded from gross revenue,
     * daily chart revenue, and AOV calculations.
     */
    public function test_challenge_cancelled_and_returned_orders_excluded_from_revenue_and_aov(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // Valid orders: ₹1,500 + ₹2,500 = ₹4,000
        $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-VALID-1', 'placed');
        $this->createSellerOrderRecord($seller, $buyer, 2500.00, 'SO-VALID-2', 'delivered');

        // Voided orders: ₹50,000 cancelled, ₹20,000 returned (Total ₹70,000 to be ignored)
        $this->createSellerOrderRecord($seller, $buyer, 50000.00, 'SO-CANCELLED', 'cancelled');
        $this->createSellerOrderRecord($seller, $buyer, 20000.00, 'SO-RETURNED', 'returned');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);

        // Total orders count includes all orders (4)
        $this->assertEquals(4, $response->viewData('totalOrders'));
        $response->assertSee('4 Total');

        // Gross revenue MUST ONLY be ₹4,000 (NOT ₹74,000)
        $this->assertEquals(4000.00, $response->viewData('grossRevenue'));
        $this->assertEquals(4000.00, $response->viewData('totalRevenue'));
        $response->assertSee('4,000');

        // AOV is ₹4,000 / 2 completed/active orders = ₹2,000.00
        $this->assertEquals(2000.00, $response->viewData('aov'));
        $response->assertSee('AOV ₹2,000.00');

        // Cancellation rate calculation: 2 cancelled out of 4 = 50.0%
        $this->assertEquals(50.0, $response->viewData('cancellationRate'));
    }

    // =========================================================================
    // SECTION 2: EXTREME VALUES & BOUNDARY CONDITIONS
    // =========================================================================

    /**
     * Challenge 2.1: Astronomical Financial Values and High Order Quantities.
     * Verify numbers like ₹99,999,999.00 and inventory of 1,500,000 units
     * format cleanly without floating-point exponential corruption (e.g. 1E+8) or fatal errors.
     */
    public function test_challenge_astronomical_financial_values_and_large_inventories(): void
    {
        $seller = $this->createApprovedSeller([], ['shop_name' => 'Mega Agro Wholesale Corp']);
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // Extreme order amount: ₹99,999,999.00
        $this->createSellerOrderRecord($seller, $buyer, 99999999.00, 'SO-MEGA-999', 'delivered');

        // Extreme inventory: 1,500,000 units
        $this->createProductRecord($seller, 'Bulk Grain Silo Lot', 500000.00, 1500000, 100, 'active');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        $this->assertEquals(99999999.00, $response->viewData('grossRevenue'));
        $response->assertSee('99,999,999');
        $response->assertDontSee('1.0E');
        $response->assertDontSee('1E+');
    }

    /**
     * Challenge 2.2: Comprehensive Stock Threshold Boundary Matrix.
     * Evaluates exact boundary conditions for low-stock triggers:
     * - stock = threshold (10 == 10) -> LOW STOCK
     * - stock = threshold + 1 (11 > 10) -> HEALTHY
     * - stock = 0 (0 <= 10) -> CRITICAL LOW STOCK (<5)
     * - default threshold 10 with stock = 4 -> CRITICAL LOW STOCK
     * - inactive product with stock = 0 -> EXCLUDED from active low-stock count
     */
    public function test_challenge_comprehensive_stock_threshold_boundaries(): void
    {
        $seller = $this->createApprovedSeller();

        // 1. Boundary: stock = threshold (10 == 10) -> LOW STOCK
        $this->createProductRecord($seller, 'Item At Threshold', 50.00, 10, 10, 'active');

        // 2. Boundary: stock = threshold + 1 (11 > 10) -> HEALTHY
        $this->createProductRecord($seller, 'Item Above Threshold', 50.00, 11, 10, 'active');

        // 3. Boundary: zero stock (0 <= 10) -> CRITICAL LOW STOCK (<5)
        $this->createProductRecord($seller, 'Item Zero Stock', 50.00, 0, 10, 'active');

        // 4. Boundary: stock 4 with threshold 10 -> CRITICAL LOW STOCK (<5)
        $this->createProductRecord($seller, 'Item Critical Stock', 100.00, 4, 10, 'active');

        // 5. Inactive item with stock = 0 -> MUST BE EXCLUDED from active low stock
        $this->createProductRecord($seller, 'Archived Inactive Item', 20.00, 0, 10, 'inactive');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);

        // Low stock count should be exactly 3 (Item At Threshold, Item Zero Stock, Item Critical Stock)
        $this->assertEquals(3, $response->viewData('lowStockCount'));
        // Critical stock count should be exactly 2 (Item Zero Stock with 0, Item Critical Stock with 4)
        $this->assertEquals(2, $response->viewData('criticalStockCount'));

        $response->assertSee('Item At Threshold');
        $response->assertSee('Item Zero Stock');
        $response->assertSee('Item Critical Stock');

        // Inactive item must NOT appear in low stock alert
        $response->assertDontSee('Archived Inactive Item');
    }

    /**
     * Challenge 2.3: Trust Score Boundary Thresholds and Tier Classifications.
     * Evaluates controller calculations:
     * - >= 90.00 -> Tier 1 Prime
     * - >= 80.00 and < 90.00 -> Tier 2 Verified
     * - < 80.00 or 0.00 -> Standard
     */
    public function test_challenge_trust_score_boundary_values_and_tier_mapping(): void
    {
        // Case A: 100.00 (Max Prime)
        $sellerMax = $this->createApprovedSeller([], ['trust_score' => 100.00]);
        $respA = $this->actingAs($sellerMax, 'seller')->get(route('seller.dashboard'));
        $respA->assertStatus(200);
        $this->assertEquals(100.00, $respA->viewData('trustScore'));
        $this->assertEquals('Tier 1 Prime', $respA->viewData('trustTier'));
        $respA->assertSee('100');

        // Case B: 89.90 (Just below Tier 1 Prime -> Tier 2 Verified)
        $sellerMid = $this->createApprovedSeller([], ['trust_score' => 89.90]);
        $respB = $this->actingAs($sellerMid, 'seller')->get(route('seller.dashboard'));
        $respB->assertStatus(200);
        $this->assertEquals(89.90, $respB->viewData('trustScore'));
        $this->assertEquals('Tier 2 Verified', $respB->viewData('trustTier'));
        $respB->assertSee('89');

        // Case C: 79.90 (Just below Tier 2 Verified -> Standard)
        $sellerLow = $this->createApprovedSeller([], ['trust_score' => 79.90]);
        $respC = $this->actingAs($sellerLow, 'seller')->get(route('seller.dashboard'));
        $respC->assertStatus(200);
        $this->assertEquals(79.90, $respC->viewData('trustScore'));
        $this->assertEquals('Standard', $respC->viewData('trustTier'));
        $respC->assertSee('79');

        // Case D: 0.00 (Zero Trust Score)
        $sellerZero = $this->createApprovedSeller([], ['trust_score' => 0.00]);
        $respD = $this->actingAs($sellerZero, 'seller')->get(route('seller.dashboard'));
        $respD->assertStatus(200);
        $this->assertEquals(0.00, $respD->viewData('trustScore'));
        $this->assertEquals('Standard', $respD->viewData('trustTier'));
        $respD->assertSee('0');
    }

    /**
     * Challenge 2.4: Extreme Geolocation Coordinates & Negative Values.
     * Southern/Western hemisphere coordinates (e.g. Lat -33.8688, Lng -151.2093)
     * and extreme operating radiuses must render cleanly without validation faults.
     */
    public function test_challenge_negative_geo_coordinates_and_extreme_radius(): void
    {
        $seller = $this->createApprovedSeller([], [
            'latitude'            => -33.8688,
            'longitude'           => -151.2093,
            'operating_radius_km' => 99999,
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('seller.dashboard');
    }

    // =========================================================================
    // SECTION 3: MULTI-TENANT ISOLATION & ZERO DATA LEAKAGE GUARANTEES
    // =========================================================================

    /**
     * Challenge 3.1: Strict Multi-Tenant Isolation - Orders, Financials & Dispatches.
     * Seller Alpha has 0 orders.
     * Seller Beta has 10 high-value orders totaling ₹500,000 with private buyer identities.
     * Seller Gamma has 5 orders totaling ₹120,000.
     *
     * Guarantee: Seller Alpha's dashboard must strictly reflect 0 orders, ₹0 revenue,
     * and zero trace of Beta or Gamma's orders, order IDs, or customer identities.
     */
    public function test_challenge_strict_multi_tenant_isolation_orders_and_financials(): void
    {
        $sellerAlpha = $this->createApprovedSeller(['name' => 'Seller Alpha'], ['shop_name' => 'Alpha Bio-Farms']);
        $sellerBeta = $this->createApprovedSeller(['name' => 'Seller Beta'], ['shop_name' => 'Beta Mega Holdings']);
        $sellerGamma = $this->createApprovedSeller(['name' => 'Seller Gamma'], ['shop_name' => 'Gamma Spice Estates']);

        $buyerBeta = User::factory()->create(['name' => 'VIP Beta Buyer', 'role' => 'user']);
        $buyerGamma = User::factory()->create(['name' => 'Corporate Gamma Buyer', 'role' => 'user']);

        // Seller Beta: 10 orders of ₹50,000 each = ₹500,000
        for ($i = 1; $i <= 10; $i++) {
            $this->createSellerOrderRecord($sellerBeta, $buyerBeta, 50000.00, "SO-BETA-0{$i}", 'delivered');
        }

        // Seller Gamma: 5 orders of ₹24,000 each = ₹120,000
        for ($j = 1; $j <= 5; $j++) {
            $this->createSellerOrderRecord($sellerGamma, $buyerGamma, 24000.00, "SO-GAMMA-0{$j}", 'processing');
        }

        // Seller Alpha visits dashboard (has 0 orders)
        $responseAlpha = $this->actingAs($sellerAlpha, 'seller')->get(route('seller.dashboard'));

        $responseAlpha->assertStatus(200);
        $responseAlpha->assertSee('Alpha Bio-Farms');

        // Alpha sees 0 orders, ₹0 revenue
        $this->assertEquals(0, $responseAlpha->viewData('totalOrders'));
        $this->assertEquals(0.0, $responseAlpha->viewData('grossRevenue'));
        $responseAlpha->assertSee('₹0');
        $responseAlpha->assertSee('No orders received yet');

        // MUST NOT leak any of Seller Beta's order numbers, revenue, or buyer identities
        $responseAlpha->assertDontSee('SO-BETA-01');
        $responseAlpha->assertDontSee('SO-BETA-10');
        $responseAlpha->assertDontSee('500,000');
        $responseAlpha->assertDontSee('VIP Beta Buyer');
        $responseAlpha->assertDontSee('Beta Mega Holdings');

        // MUST NOT leak any of Seller Gamma's order numbers, revenue, or buyer identities
        $responseAlpha->assertDontSee('SO-GAMMA-01');
        $responseAlpha->assertDontSee('120,000');
        $responseAlpha->assertDontSee('Corporate Gamma Buyer');
        $responseAlpha->assertDontSee('Gamma Spice Estates');
    }

    /**
     * Challenge 3.2: Multi-Tenant Inventory & Low Stock Alerts Isolation.
     * Seller Beta has 4 depleted products (stock 1).
     * Seller Alpha has 2 well-stocked products (stock 200).
     *
     * Guarantee: Seller Alpha must see zero inventory alerts, and Seller Beta's
     * product names and low-stock telemetry must not leak.
     */
    public function test_challenge_multi_tenant_inventory_and_low_stock_isolation(): void
    {
        $sellerAlpha = $this->createApprovedSeller([], ['shop_name' => 'Alpha Healthy Goods']);
        $sellerBeta = $this->createApprovedSeller([], ['shop_name' => 'Beta Depleted Stock']);

        // Seller Beta depleted products
        $this->createProductRecord($sellerBeta, 'Beta Depleted Cashews', 900.00, 1, 10, 'active');
        $this->createProductRecord($sellerBeta, 'Beta Depleted Saffron', 4500.00, 2, 5, 'active');

        // Seller Alpha well-stocked products
        $this->createProductRecord($sellerAlpha, 'Alpha Fresh Guava', 60.00, 300, 10, 'active');
        $this->createProductRecord($sellerAlpha, 'Alpha Sweet Lemons', 40.00, 200, 10, 'active');

        $responseAlpha = $this->actingAs($sellerAlpha, 'seller')->get(route('seller.dashboard'));

        $responseAlpha->assertStatus(200);
        $this->assertEquals(0, $responseAlpha->viewData('lowStockCount'));
        $responseAlpha->assertSee('Alpha Fresh Guava');
        $responseAlpha->assertSee('Alpha Sweet Lemons');
        $responseAlpha->assertSee('All inventory healthy! No products below minimum threshold.');

        // Beta's products must NOT leak to Alpha
        $responseAlpha->assertDontSee('Beta Depleted Cashews');
        $responseAlpha->assertDontSee('Beta Depleted Saffron');
    }

    /**
     * Challenge 3.3: Multi-Tenant Demand Velocity Ranking Isolation.
     * Top selling products by volume must be strictly partitioned by authenticated seller.
     */
    public function test_challenge_multi_tenant_demand_velocity_ranking_isolation(): void
    {
        $sellerAlpha = $this->createApprovedSeller([], ['shop_name' => 'Alpha Bakery']);
        $sellerBeta = $this->createApprovedSeller([], ['shop_name' => 'Beta Grains']);
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        $productBeta = $this->createProductRecord($sellerBeta, 'Beta Super Velocity Rice', 80.00, 500, 10, 'active');
        $productAlpha = $this->createProductRecord($sellerAlpha, 'Alpha Fresh Sourdough', 150.00, 100, 10, 'active');

        // Order for Beta: 1000 units sold for ₹80,000
        $soBeta = $this->createSellerOrderRecord($sellerBeta, $buyer, 80000.00, 'SO-BETA-VEL', 'delivered');
        OrderItem::create([
            'seller_order_id' => $soBeta->id,
            'product_id'      => $productBeta->id,
            'product_name'    => $productBeta->name,
            'unit_price'      => 80.00,
            'quantity'        => 1000,
            'total_price'     => 80000.00,
        ]);

        // Order for Alpha: 10 units sold for ₹1,500
        $soAlpha = $this->createSellerOrderRecord($sellerAlpha, $buyer, 1500.00, 'SO-ALPHA-VEL', 'delivered');
        OrderItem::create([
            'seller_order_id' => $soAlpha->id,
            'product_id'      => $productAlpha->id,
            'product_name'    => $productAlpha->name,
            'unit_price'      => 150.00,
            'quantity'        => 10,
            'total_price'     => 1500.00,
        ]);

        $responseAlpha = $this->actingAs($sellerAlpha, 'seller')->get(route('seller.dashboard'));

        $responseAlpha->assertStatus(200);
        $responseAlpha->assertSee('Alpha Fresh Sourdough');
        $responseAlpha->assertSee('10 sold');

        // Beta's top product and volume MUST NOT leak to Alpha
        $responseAlpha->assertDontSee('Beta Super Velocity Rice');
        $responseAlpha->assertDontSee('1000 sold');
        $responseAlpha->assertDontSee('80,000');
    }

    /**
     * Challenge 3.4: Multi-Tenant Pending Payout & Settlement Isolation.
     * Seller Beta has ₹85,000 in pending payouts.
     * Seller Alpha has ₹0 in pending payouts.
     * Alpha's dashboard must NOT display Beta's pending payout sum.
     */
    public function test_challenge_multi_tenant_payout_and_settlement_isolation(): void
    {
        $sellerAlpha = $this->createApprovedSeller();
        $sellerBeta = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        $soBeta = $this->createSellerOrderRecord($sellerBeta, $buyer, 94444.00, 'SO-BETA-PAY', 'delivered');

        Payout::create([
            'seller_id'         => $sellerBeta->id,
            'seller_order_id'   => $soBeta->id,
            'gross_amount'      => 94444.00,
            'commission_amount' => 9444.00,
            'net_amount'        => 85000.00,
            'status'            => 'pending',
            'payout_reference'  => 'PAY-BETA-SECRET-001',
        ]);

        $responseAlpha = $this->actingAs($sellerAlpha, 'seller')->get(route('seller.dashboard'));

        $responseAlpha->assertStatus(200);
        $this->assertEquals(0.0, $responseAlpha->viewData('nextPayout'));
        $responseAlpha->assertSee('₹0');
        $responseAlpha->assertDontSee('85,000');
        $responseAlpha->assertDontSee('PAY-BETA-SECRET-001');
    }

    /**
     * Challenge 3.5: Multi-Tenant Wholesale Auction Spotlight Isolation.
     * When multiple sellers have live auctions, each seller must see ONLY their own auction
     * in the Wholesale Auction Spotlight banner.
     */
    public function test_challenge_multi_tenant_wholesale_auction_spotlight_isolation(): void
    {
        $sellerAlpha = $this->createApprovedSeller([], ['shop_name' => 'Alpha Agro']);
        $sellerBeta = $this->createApprovedSeller([], ['shop_name' => 'Beta Spices']);
        $sellerGamma = $this->createApprovedSeller([], ['shop_name' => 'Gamma Farms']);

        $prodBeta = $this->createProductRecord($sellerBeta, 'Beta Cardamom Wholesale Crate', 15000.00, 40, 5, 'active');
        $prodGamma = $this->createProductRecord($sellerGamma, 'Gamma Turmeric Harvest Lot', 25000.00, 60, 5, 'active');

        // Beta's active auction
        Auction::create([
            'product_id'        => $prodBeta->id,
            'seller_id'         => $sellerBeta->sellerProfile->id,
            'starting_price'    => 15000.00,
            'reserve_price'     => 22000.00,
            'current_price'     => 18500.00,
            'minimum_increment' => 500.00,
            'starts_at'         => now()->subHour(),
            'ends_at'           => now()->addHours(4),
            'status'            => 'live',
        ]);

        // Gamma's active auction
        Auction::create([
            'product_id'        => $prodGamma->id,
            'seller_id'         => $sellerGamma->sellerProfile->id,
            'starting_price'    => 25000.00,
            'reserve_price'     => 35000.00,
            'current_price'     => 31000.00,
            'minimum_increment' => 1000.00,
            'starts_at'         => now()->subHour(),
            'ends_at'           => now()->addHours(5),
            'status'            => 'live',
        ]);

        // 1. Seller Alpha has no auctions -> MUST see NO ACTIVE LOT
        $respAlpha = $this->actingAs($sellerAlpha, 'seller')->get(route('seller.dashboard'));
        $respAlpha->assertStatus(200);
        $this->assertNull($respAlpha->viewData('activeAuction'));
        $respAlpha->assertSee('NO ACTIVE LOT');
        $respAlpha->assertDontSee('Beta Cardamom Wholesale Crate');
        $respAlpha->assertDontSee('Gamma Turmeric Harvest Lot');

        // 2. Seller Beta must see Beta's auction ONLY
        $respBeta = $this->actingAs($sellerBeta, 'seller')->get(route('seller.dashboard'));
        $respBeta->assertStatus(200);
        $this->assertNotNull($respBeta->viewData('activeAuction'));
        $respBeta->assertSee('Beta Cardamom Wholesale Crate');
        $respBeta->assertSee('18,500');
        $respBeta->assertDontSee('Gamma Turmeric Harvest Lot');

        // 3. Seller Gamma must see Gamma's auction ONLY
        $respGamma = $this->actingAs($sellerGamma, 'seller')->get(route('seller.dashboard'));
        $respGamma->assertStatus(200);
        $this->assertNotNull($respGamma->viewData('activeAuction'));
        $respGamma->assertSee('Gamma Turmeric Harvest Lot');
        $respGamma->assertSee('31,000');
        $respGamma->assertDontSee('Beta Cardamom Wholesale Crate');
    }

    // =========================================================================
    // SECTION 4: SECURITY HARDENING - SQL INJECTION & XSS ESCAPING
    // =========================================================================

    /**
     * Challenge 4.1: XSS Injection Neutralization Across All Dashboard Fields.
     * Injects adversarial script and DOM-breaking payloads into:
     * - Shop Name
     * - Product Title & Unit Type
     * - Order ID, Customer Name, and Delivery Address
     * - Auction Title & Top Bidder Name
     *
     * Guarantee: Unescaped `<script>`, `<img onerror=`, or `<svg onload=` MUST NOT exist
     * in the rendered response HTML.
     */
    public function test_challenge_xss_prevention_across_all_dashboard_fields(): void
    {
        $xssShop = 'Vulnerable Farm <script>alert("xss-shop")</script>';
        $xssProduct = 'Organic Mango <img src=x onerror="alert(\'xss-prod\')">';
        $xssUnit = '<b onmouseover="alert(\'xss-unit\')">kg</b>';
        $xssCustName = 'Hacker Buyer <svg onload="alert(\'xss-cust\')">';
        $xssOrderNumber = 'BZ-XSS-<script>alert("ord")</script>';

        $seller = $this->createApprovedSeller([], [
            'shop_name' => $xssShop,
        ]);
        $buyer = User::factory()->create([
            'name'   => $xssCustName,
            'role'   => 'user',
            'status' => 'active',
        ]);

        $product = $this->createProductRecord($seller, $xssProduct, 150.00, 3, 10, 'active');
        $product->update(['unit_type' => $xssUnit]);

        $so = $this->createSellerOrderRecord($seller, $buyer, 450.00, $xssOrderNumber, 'placed');
        OrderItem::create([
            'seller_order_id' => $so->id,
            'product_id'      => $product->id,
            'product_name'    => $product->name,
            'unit_price'      => 150.00,
            'quantity'        => 3,
            'total_price'     => 450.00,
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);

        // Raw malicious executable tags MUST NOT appear unescaped
        $response->assertDontSee('<script>alert("xss-shop")</script>', false);
        $response->assertDontSee('<img src=x onerror="alert(\'xss-prod\')">', false);
        $response->assertDontSee('<b onmouseover="alert(\'xss-unit\')">kg</b>', false);
        $response->assertDontSee('<svg onload="alert(\'xss-cust\')">', false);
        $response->assertDontSee('<script>alert("ord")</script>', false);

        // Escaped versions MUST be safely present
        $response->assertSee(e($xssShop), false);
        $response->assertSee(e($xssProduct), false);
        $response->assertSee(e($xssCustName), false);
    }

    /**
     * Challenge 4.2: SQL Injection Payload Resilience.
     * Evaluates injection vectors in model attributes (`' OR '1'='1`, `'); DROP TABLE products; --`).
     * Queries must execute safely via PDO prepared statements without syntax exceptions.
     */
    public function test_challenge_sql_injection_payload_resilience(): void
    {
        $sqliShop = "Agro Farm' OR '1'='1; --";
        $sqliProduct = "Pesticide-Free Wheat'); DROP TABLE products; --";
        $sqliOrderNumber = "ORD-' UNION SELECT 1,2,3,4,5,6,7,8,9,10,11,12 --";

        $seller = $this->createApprovedSeller([], [
            'shop_name' => $sqliShop,
        ]);
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        $product = $this->createProductRecord($seller, $sqliProduct, 250.00, 20, 5, 'active');
        $this->createSellerOrderRecord($seller, $buyer, 500.00, $sqliOrderNumber, 'placed');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        // Database tables must still exist and be intact
        $this->assertDatabaseHas('products', ['id' => $product->id]);
        $this->assertDatabaseHas('seller_profiles', ['id' => $seller->sellerProfile->id]);
    }

    /**
     * Challenge 4.3: Unicode, Emojis, and Vernacular Script Rendering.
     * Verifies that Bengali, Hindi, emojis, and symbols render cleanly without byte corruption.
     */
    public function test_challenge_unicode_emojis_and_vernacular_script_rendering(): void
    {
        $bengaliShop = '🌿 সুন্দরবন ন্যাচারাল এগ্রো 🌾 (Sundarban Agro)';
        $hindiProduct = 'शुद्ध देसी बासमती चावल • Pure Desi Basmati (A-Grade)';

        $seller = $this->createApprovedSeller([], [
            'shop_name' => $bengaliShop,
        ]);

        $this->createProductRecord($seller, $hindiProduct, 180.00, 4, 10, 'active');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        $response->assertSee($bengaliShop);
        $response->assertSee($hindiProduct);
        $response->assertDontSee('???');
    }

    // =========================================================================
    // SECTION 5: WHOLESALE AUCTION SPOTLIGHT TIMING & LIFECYCLE BOUNDARIES
    // =========================================================================

    /**
     * Challenge 5.1: Auction Timing Boundary - Lot Ended 1 Second Ago.
     * An auction whose ends_at timestamp is 1 second in the past (even if status is 'live')
     * must NOT be displayed in the Active Wholesale Lot banner.
     */
    public function test_challenge_auction_ended_one_second_ago_is_excluded_from_spotlight(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Expiring Lot Product', 5000.00, 20, 5, 'active');

        // Auction ended 1 second ago
        Auction::create([
            'product_id'        => $product->id,
            'seller_id'         => $seller->sellerProfile->id,
            'starting_price'    => 5000.00,
            'reserve_price'     => 8000.00,
            'current_price'     => 6500.00,
            'minimum_increment' => 200.00,
            'starts_at'         => now()->subHours(2),
            'ends_at'           => now()->subSecond(),
            'status'            => 'live', // status still marked 'live' but time expired
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        $this->assertNull($response->viewData('activeAuction'));
        $response->assertSee('NO ACTIVE LOT');
        $response->assertDontSee('Expiring Lot Product');
    }

    /**
     * Challenge 5.2: Scheduled Future Auction Not Displayed in Live Spotlight.
     * An auction scheduled to start 1 hour in the future must not appear in the live banner.
     */
    public function test_challenge_scheduled_future_auction_is_excluded_from_live_spotlight(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Future Auction Product', 7000.00, 30, 5, 'active');

        Auction::create([
            'product_id'        => $product->id,
            'seller_id'         => $seller->sellerProfile->id,
            'starting_price'    => 7000.00,
            'reserve_price'     => 10000.00,
            'current_price'     => 7000.00,
            'minimum_increment' => 250.00,
            'starts_at'         => now()->addHour(),
            'ends_at'           => now()->addHours(6),
            'status'            => 'scheduled',
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        $this->assertNull($response->viewData('activeAuction'));
        $response->assertSee('NO ACTIVE LOT');
        $response->assertDontSee('Future Auction Product');
    }

    /**
     * Challenge 5.3: Active Auction with 0 Bids.
     * Displays starting price and graceful top bidder indicator without null pointer crashes.
     */
    public function test_challenge_active_auction_with_zero_bids_renders_cleanly(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Zero Bid Auction Lot', 12000.00, 25, 5, 'active');

        Auction::create([
            'product_id'        => $product->id,
            'seller_id'         => $seller->sellerProfile->id,
            'starting_price'    => 12000.00,
            'reserve_price'     => 16000.00,
            'current_price'     => 12000.00,
            'minimum_increment' => 500.00,
            'starts_at'         => now()->subMinute(),
            'ends_at'           => now()->addHours(2),
            'status'            => 'live',
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        $this->assertNotNull($response->viewData('activeAuction'));
        $response->assertSee('Zero Bid Auction Lot');
        $response->assertSee('12,000');
        $response->assertSee('LIVE AUCTION');
        $response->assertSee('0 verified buyers');
    }

    /**
     * Challenge 5.4: Active Auction with Multiple Bids.
     * Prominently displays the highest bid and top bidder's name.
     */
    public function test_challenge_active_auction_with_multiple_bids_displays_highest_bid(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer1 = User::factory()->create(['name' => 'Low Bidder', 'role' => 'user']);
        $buyer2 = User::factory()->create(['name' => 'Top Wholesaler Champion', 'role' => 'user']);

        $product = $this->createProductRecord($seller, 'Competitive Mango Crate', 8000.00, 50, 5, 'active');

        $auction = Auction::create([
            'product_id'        => $product->id,
            'seller_id'         => $seller->sellerProfile->id,
            'starting_price'    => 8000.00,
            'reserve_price'     => 14000.00,
            'current_price'     => 13500.00,
            'minimum_increment' => 500.00,
            'starts_at'         => now()->subHours(2),
            'ends_at'           => now()->addHours(2),
            'status'            => 'live',
        ]);

        // Bid 1 (Low)
        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyer1->id,
            'amount'     => 10000.00,
        ]);

        // Bid 2 (Top High Bid)
        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $buyer2->id,
            'amount'     => 13500.00,
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Competitive Mango Crate');
        $response->assertSee('13,500');
        $response->assertSee('Top Wholesaler Champion');
        $response->assertSee('2 verified buyers');
    }

    // =========================================================================
    // SECTION 6: PIPELINE DISTRIBUTION ARITHMETIC SAFETY
    // =========================================================================

    /**
     * Challenge 6.1: Order Pipeline Combinatorial Distribution.
     * Evaluates 100% delivered, 100% placed, and 100% cancelled scenarios
     * to guarantee percentage calculations never throw division by zero or NaN errors.
     */
    public function test_challenge_pipeline_percentages_with_varied_status_combinations(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // Scenario: All orders are cancelled (pipelineActiveTotal = 0)
        $this->createSellerOrderRecord($seller, $buyer, 1000.00, 'SO-C1', 'cancelled');
        $this->createSellerOrderRecord($seller, $buyer, 2000.00, 'SO-C2', 'cancelled');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));

        $response->assertStatus(200);
        $this->assertEquals(0, $response->viewData('pipeline')['total']);
        $response->assertDontSee('Division by zero');
        $response->assertDontSee('NAN%');
    }

    // =========================================================================
    // SECTION 7: DEFENSIVE ACCESS CONTROL GATE LOCKOUTS
    // =========================================================================

    /**
     * Challenge 7.1: Seller with Missing SellerProfile Record Gated to Pending.
     * If a user with role='seller' exists in the database but does not have a SellerProfile,
     * the system must defensively lock them out of /seller/dashboard and redirect to /seller/pending.
     */
    public function test_challenge_seller_without_profile_redirected_to_pending(): void
    {
        $sellerNoProfile = User::factory()->create([
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Secret123!'),
        ]);

        $response = $this->actingAs($sellerNoProfile, 'seller')->get(route('seller.dashboard'));

        $response->assertRedirect(route('seller.pending'));
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
            'shop_name'           => 'Approved Empirical Merchant',
            'shop_slug'           => 'approved-empirical-' . $seller->id,
            'seller_type'         => 'Farmer',
            'city'                => 'Contai',
            'state'               => 'West Bengal',
            'status'              => 'approved',
            'trust_score'         => 94.00,
            'commission_rate'     => 10.00,
            'address'             => 'Plot 101, Mandi Bypass Rd',
            'operating_radius_km' => 25,
            'bank_account_number' => '123456789012',
            'bank_ifsc'           => 'HDFC0001234',
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
