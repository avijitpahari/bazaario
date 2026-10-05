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
 * Class SellerDashboardEmpiricalChallengeTest
 *
 * Empirical Challenge Suite for Milestone 2: Seller Dashboard & Performance Analytics.
 * Focuses on stress-testing boundary conditions, dynamic mutations, bulk workloads,
 * cancelled order revenue filtration, 7-day revenue distributions, and telemetry invariants.
 */
class SellerDashboardEmpiricalChallengeTest extends TestCase
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

    protected function tearDown(): void
    {
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    // =========================================================================
    // CHALLENGE 1: BULK 50-ORDER PIPELINE INVARIANTS & ACCURATE PERCENTAGES
    // =========================================================================

    /**
     * Challenge 1: When 50 orders are added in various statuses, pipeline counters
     * and percentages match exactly, and cancelled orders are excluded from active percentages.
     */
    public function test_bulk_pipeline_with_50_orders_matches_counters_and_percentages_exactly(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // Distribute exactly 50 orders across various pipeline stages allowed by schema:
        // - placed: 15 orders @ ₹1,000 = ₹15,000
        // - processing: 15 orders @ ₹2,000 = ₹30,000
        // - packed: 7 orders @ ₹1,500 = ₹10,500
        // - shipped: 5 orders @ ₹1,500 = ₹7,500 (readyCount total = 12, revenue = ₹18,000)
        // - delivered: 5 orders @ ₹3,000 = ₹15,000
        // - cancelled: 2 orders @ ₹5,000 = ₹10,000
        // - returned: 1 order @ ₹5,000 = ₹5,000 (cancelledCount total = 3, revenue = ₹15,000 excluded)
        // Total orders: 15 + 15 + 7 + 5 + 5 + 2 + 1 = 50 orders!
        // Active pipeline total: 15 + 15 + 12 + 5 = 47 orders.
        // Gross revenue: ₹15,000 + ₹30,000 + ₹18,000 + ₹15,000 = ₹78,000 (excludes ₹15,000 cancelled/returned).

        $orderSpecs = [
            ['count' => 15, 'status' => 'placed', 'subtotal' => 1000.00],
            ['count' => 15, 'status' => 'processing', 'subtotal' => 2000.00],
            ['count' => 7,  'status' => 'packed', 'subtotal' => 1500.00],
            ['count' => 5,  'status' => 'shipped', 'subtotal' => 1500.00],
            ['count' => 5,  'status' => 'delivered', 'subtotal' => 3000.00],
            ['count' => 2,  'status' => 'cancelled', 'subtotal' => 5000.00],
            ['count' => 1,  'status' => 'returned', 'subtotal' => 5000.00],
        ];

        $orderIndex = 1;
        foreach ($orderSpecs as $spec) {
            for ($i = 0; $i < $spec['count']; $i++) {
                $this->createSellerOrderRecord(
                    $seller,
                    $buyer,
                    $spec['subtotal'],
                    sprintf('SO-BLK-%03d', $orderIndex++),
                    $spec['status']
                );
            }
        }

        $this->assertEquals(50, SellerOrder::where('seller_id', $seller->id)->count());

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $response->assertStatus(200);

        // 1. Verify viewData values directly
        $viewTotalOrders = $response->viewData('totalOrders');
        $this->assertEquals(50, $viewTotalOrders);

        $viewGrossRevenue = $response->viewData('grossRevenue');
        $this->assertEquals(78000.00, $viewGrossRevenue);

        $pipelineCounts = $response->viewData('pipelineCounts');
        $this->assertEquals(15, $pipelineCounts['placed']);
        $this->assertEquals(15, $pipelineCounts['pending']);
        $this->assertEquals(15, $pipelineCounts['processing']);
        $this->assertEquals(12, $pipelineCounts['ready']);
        $this->assertEquals(5,  $pipelineCounts['delivered']);
        $this->assertEquals(3,  $pipelineCounts['cancelled']);

        $pipeline = $response->viewData('pipeline');
        $this->assertEquals(47, $pipeline['total']); // Active pipeline total excludes 3 cancelled/returned orders

        // Expected percentages:
        // placed: round(15 / 47 * 100, 1) = 31.9%
        // processing: round(15 / 47 * 100, 1) = 31.9%
        // ready: round(12 / 47 * 100, 1) = 25.5%
        // delivered: round(5 / 47 * 100, 1) = 10.6%
        $pipelinePercentages = $response->viewData('pipelinePercentages');
        $this->assertEquals(31.9, $pipelinePercentages['placed']);
        $this->assertEquals(31.9, $pipelinePercentages['processing']);
        $this->assertEquals(25.5, $pipelinePercentages['ready']);
        $this->assertEquals(10.6, $pipelinePercentages['delivered']);

        // 2. Verify rendered view content
        $response->assertSee('50 Total');
        $response->assertSee('78,000');
        $response->assertDontSee('93,000'); // Total revenue MUST NOT include the 15k cancelled/returned orders
    }

    // =========================================================================
    // CHALLENGE 2: CANCELLED & RETURNED ORDER REVENUE EXCLUSION
    // =========================================================================

    /**
     * Challenge 2: Cancelled and returned orders are strictly excluded from gross revenue,
     * total revenue, and average order value (AOV). Dynamic order cancellation immediately
     * decrements gross revenue.
     */
    public function test_cancelled_and_returned_orders_are_strictly_excluded_from_gross_revenue_and_aov(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // Step 1: Initial state - 2 active orders of ₹10,000 each = ₹20,000 gross revenue
        $order1 = $this->createSellerOrderRecord($seller, $buyer, 10000.00, 'SO-REV-01', 'delivered');
        $order2 = $this->createSellerOrderRecord($seller, $buyer, 10000.00, 'SO-REV-02', 'processing');

        $resp1 = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $resp1->assertStatus(200);
        $this->assertEquals(20000.00, $resp1->viewData('grossRevenue'));
        $this->assertEquals(10000.00, $resp1->viewData('aov'));
        $this->assertEquals(2, $resp1->viewData('totalOrders'));

        // Step 2: Add high-value cancelled order (₹50,000) and returned order (₹30,000)
        $this->createSellerOrderRecord($seller, $buyer, 50000.00, 'SO-CANC-03', 'cancelled');
        $this->createSellerOrderRecord($seller, $buyer, 30000.00, 'SO-RET-04', 'returned');

        $resp2 = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $resp2->assertStatus(200);

        // Gross revenue MUST remain strictly ₹20,000 (NOT ₹100,000)
        $this->assertEquals(20000.00, $resp2->viewData('grossRevenue'));
        // Completed orders count remains 2 (non-cancelled/returned)
        $this->assertEquals(10000.00, $resp2->viewData('aov'));
        // Total orders increments to 4
        $this->assertEquals(4, $resp2->viewData('totalOrders'));
        // Cancellation rate: 2 cancelled/returned out of 4 = 50.0%
        $this->assertEquals(50.0, $resp2->viewData('cancellationRate'));

        // Step 3: Dynamic state change: Order 2 gets cancelled midway
        $order2->update(['status' => 'cancelled']);

        $resp3 = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $resp3->assertStatus(200);

        // Gross revenue drops dynamically to ₹10,000 (only order 1 remains active)
        $this->assertEquals(10000.00, $resp3->viewData('grossRevenue'));
        // AOV remains ₹10,000 / 1 = ₹10,000
        $this->assertEquals(10000.00, $resp3->viewData('aov'));
        // Cancellation rate: 3 cancelled out of 4 = 75.0%
        $this->assertEquals(75.0, $resp3->viewData('cancellationRate'));
        // View renders 10,000 and does NOT render 20,000
        $resp3->assertSee('10,000');
    }

    // =========================================================================
    // CHALLENGE 3: DYNAMIC ORDER STATE TRANSITIONS ACROSS FULL LIFECYCLE
    // =========================================================================

    /**
     * Challenge 3: Step-by-step lifecycle state machine progression accurately
     * updates pipeline counters, percentages, and fulfillment rate at each step.
     */
    public function test_dynamic_order_state_transitions_across_full_fulfillment_lifecycle(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        $order = $this->createSellerOrderRecord($seller, $buyer, 4500.00, 'SO-TRANS-01', 'placed');

        // Phase 1: placed
        $resp = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $this->assertEquals(1, $resp->viewData('pipelineCounts')['placed']);
        $this->assertEquals(100.0, $resp->viewData('pipelinePercentages')['placed']);
        $this->assertEquals(0, $resp->viewData('pipelineCounts')['processing']);
        $this->assertEquals(1, $resp->viewData('pendingOrdersCount'));

        // Phase 2: transition to processing
        $order->update(['status' => 'processing']);
        $resp = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $this->assertEquals(0, $resp->viewData('pipelineCounts')['placed']);
        $this->assertEquals(1, $resp->viewData('pipelineCounts')['processing']);
        $this->assertEquals(100.0, $resp->viewData('pipelinePercentages')['processing']);
        $this->assertEquals(0, $resp->viewData('pendingOrdersCount'));

        // Phase 3: transition to packed -> shipped (both aggregate to ready)
        foreach (['packed', 'shipped'] as $readyStatus) {
            $order->update(['status' => $readyStatus]);
            $resp = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
            $this->assertEquals(1, $resp->viewData('pipelineCounts')['ready'], "Failed on status {$readyStatus}");
            $this->assertEquals(100.0, $resp->viewData('pipelinePercentages')['ready']);
        }

        // Phase 4: transition to delivered
        $order->update(['status' => 'delivered']);
        $resp = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $this->assertEquals(0, $resp->viewData('pipelineCounts')['ready']);
        $this->assertEquals(1, $resp->viewData('pipelineCounts')['delivered']);
        $this->assertEquals(100.0, $resp->viewData('pipelinePercentages')['delivered']);
        $this->assertEquals(100.0, $resp->viewData('fulfillmentScore'));

        // Phase 5: cancellation after placement
        $order->update(['status' => 'cancelled']);
        $resp = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $this->assertEquals(1, $resp->viewData('pipelineCounts')['cancelled']);
        $this->assertEquals(0, $resp->viewData('pipeline')['total']);
        $this->assertEquals(0.0, $resp->viewData('grossRevenue'));
        $this->assertEquals(0.0, $resp->viewData('aov'));
        $this->assertEquals(100.0, $resp->viewData('cancellationRate'));
    }

    // =========================================================================
    // CHALLENGE 4: LOW STOCK TELEMETRY LIMITS & DEPLETION HIERARCHY
    // =========================================================================

    /**
     * Challenge 4: When multiple products have low stock, the low stock count
     * reports the total accurately, and the widget lists strictly up to 5 items
     * sorted in ascending order of stock (most critical items first). Inactive items
     * and other sellers' items are strictly excluded.
     */
    public function test_low_stock_hierarchy_count_and_widget_top_5_depletion_limit(): void
    {
        $seller = $this->createApprovedSeller();
        $otherSeller = $this->createApprovedSeller(['name' => 'Other Seller']);

        // 3 healthy products (stock > threshold)
        $this->createProductRecord($seller, 'Healthy Cabbage', 40.00, 50, 10, 'active');
        $this->createProductRecord($seller, 'Healthy Spinach', 25.00, 30, 10, 'active');
        $this->createProductRecord($seller, 'Healthy Potatoes', 30.00, 20, 10, 'active');

        // 12 active products below threshold (depleted) with distinct stock levels
        $depletedProducts = [];
        for ($i = 0; $i < 12; $i++) {
            $depletedProducts[] = $this->createProductRecord(
                $seller,
                "Depleted Item {$i}",
                50.00 + $i,
                $i, // Stock from 0 to 11
                10, // Threshold 10. Items 0..10 qualify as low stock (11 items <= 10)
                'active'
            );
        }
        // Add one more with stock 10 to make exactly 12 low stock items (stock 0,1,2,3,4,5,6,7,8,9,10,10)
        $this->createProductRecord($seller, 'Depleted Extra', 55.00, 10, 10, 'active');

        // Inactive product with stock 1 (should be excluded)
        $this->createProductRecord($seller, 'Inactive Low Stock Item', 60.00, 1, 10, 'inactive');

        // Other seller's product with stock 0 (should be excluded)
        $this->createProductRecord($otherSeller, 'Other Seller Depleted Item', 70.00, 0, 10, 'active');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $response->assertStatus(200);

        // KPI 4 verification:
        // lowStockCount: exactly 12 items (11 from loop where stock <= 10, plus 'Depleted Extra')
        $lowStockCount = $response->viewData('lowStockCount');
        $this->assertEquals(12, $lowStockCount);

        // criticalStockCount: items with stock < 5 (items 0, 1, 2, 3, 4 = 5 items)
        $criticalStockCount = $response->viewData('criticalStockCount');
        $this->assertEquals(5, $criticalStockCount);

        // Widget collection verification:
        $lowStockProducts = $response->viewData('lowStockProducts');
        // Must contain strictly up to 5 items due to limit(5)
        $this->assertCount(5, $lowStockProducts);

        // Must be ordered by stock ASC: stock 0, 1, 2, 3, 4
        $stocks = $lowStockProducts->pluck('stock')->toArray();
        $this->assertEquals([0, 1, 2, 3, 4], $stocks);

        // Items with higher stock (5, 6, 7, etc.) MUST NOT be in the widget list
        $names = $lowStockProducts->pluck('name')->toArray();
        $this->assertContains('Depleted Item 0', $names);
        $this->assertContains('Depleted Item 1', $names);
        $this->assertContains('Depleted Item 4', $names);
        $this->assertNotContains('Depleted Item 5', $names);
        $this->assertNotContains('Depleted Item 10', $names);
        $this->assertNotContains('Inactive Low Stock Item', $names);
        $this->assertNotContains('Other Seller Depleted Item', $names);

        // Widget badge in rendered view should show "5 Requires Restock"
        $response->assertSee('5 Requires Restock');
    }

    // =========================================================================
    // CHALLENGE 5: 7-DAY REVENUE TREND ACROSS VARYING DISTRIBUTIONS
    // =========================================================================

    /**
     * Challenge 5: Revenue chart data array across all 7 days with varying order
     * distributions: verifies chronological sequencing, peak day detection,
     * proportional bar scaling, and date boundary isolation.
     */
    public function test_revenue_chart_7_day_distribution_with_varying_patterns_and_boundaries(): void
    {
        Carbon::setTestNow('2026-09-30 15:00:00'); // Fixed test anchor: Wednesday, Sep 30, 2026

        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // 7-day window spans:
        // Day 6: 2026-09-24 (Thu)
        // Day 5: 2026-09-25 (Fri)
        // Day 4: 2026-09-26 (Sat)
        // Day 3: 2026-09-27 (Sun)
        // Day 2: 2026-09-28 (Mon)
        // Day 1: 2026-09-29 (Tue)
        // Day 0: 2026-09-30 (Wed - Today)

        // Populate orders with varying distributions:
        // Day 6 (Sep 24): 1 order @ ₹1,500
        $this->createHistoricalOrder($seller, $buyer, 1500.00, '2026-09-24 10:00:00', 'delivered');

        // Day 5 (Sep 25): 2 orders @ ₹2,000 + ₹3,000 = ₹5,000
        $this->createHistoricalOrder($seller, $buyer, 2000.00, '2026-09-25 09:30:00', 'delivered');
        $this->createHistoricalOrder($seller, $buyer, 3000.00, '2026-09-25 16:45:00', 'delivered');

        // Day 4 (Sep 26): 0 orders (₹0 revenue day)

        // Day 3 (Sep 27): 1 order @ ₹20,000 (PEAK DAY!)
        $this->createHistoricalOrder($seller, $buyer, 20000.00, '2026-09-27 12:00:00', 'delivered');

        // Cancelled order on Day 3 (Sep 27): ₹50,000 (MUST BE EXCLUDED from chart!)
        $this->createHistoricalOrder($seller, $buyer, 50000.00, '2026-09-27 13:00:00', 'cancelled');

        // Day 2 (Sep 28): 1 order @ ₹4,500
        $this->createHistoricalOrder($seller, $buyer, 4500.00, '2026-09-28 11:00:00', 'processing');

        // Day 1 (Sep 29): 2 orders @ ₹3,500 + ₹2,500 = ₹6,000
        $this->createHistoricalOrder($seller, $buyer, 3500.00, '2026-09-29 08:15:00', 'packed');
        $this->createHistoricalOrder($seller, $buyer, 2500.00, '2026-09-29 18:20:00', 'delivered');

        // Day 0 (Sep 30 - Today): 1 order @ ₹8,000
        $this->createHistoricalOrder($seller, $buyer, 8000.00, '2026-09-30 10:30:00', 'placed');

        // Boundary outlier: Order created 8 days ago (2026-09-22) for ₹35,000
        // (Must be EXCLUDED from 7-day chart, but counted in lifetime gross revenue)
        $this->createHistoricalOrder($seller, $buyer, 35000.00, '2026-09-22 14:00:00', 'delivered');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $response->assertStatus(200);

        $chartData = $response->viewData('revenueChartData');
        $this->assertCount(7, $chartData);

        // Verify dates in strictly chronological order:
        $expectedDates = [
            '2026-09-24',
            '2026-09-25',
            '2026-09-26',
            '2026-09-27',
            '2026-09-28',
            '2026-09-29',
            '2026-09-30',
        ];
        $actualDates = array_column($chartData, 'date');
        $this->assertEquals($expectedDates, $actualDates);

        // Verify revenue per day:
        // Day 6 (Sep 24): 1500
        $this->assertEquals(1500.00, $chartData[0]['revenue']);
        $this->assertEquals(1, $chartData[0]['count']);
        $this->assertFalse($chartData[0]['is_peak']);
        $this->assertFalse($chartData[0]['is_today']);

        // Day 5 (Sep 25): 5000
        $this->assertEquals(5000.00, $chartData[1]['revenue']);
        $this->assertEquals(2, $chartData[1]['count']);
        $this->assertFalse($chartData[1]['is_peak']);

        // Day 4 (Sep 26): 0 (zero state bar)
        $this->assertEquals(0.00, $chartData[2]['revenue']);
        $this->assertEquals(0, $chartData[2]['count']);
        $this->assertFalse($chartData[2]['is_peak']);
        $this->assertEquals(10, $chartData[2]['bar_height']); // Minimum fallback height

        // Day 3 (Sep 27): 20000 (PEAK DAY - cancelled ₹50k excluded!)
        $this->assertEquals(20000.00, $chartData[3]['revenue']);
        $this->assertEquals(1, $chartData[3]['count']);
        $this->assertTrue($chartData[3]['is_peak']);
        $this->assertEquals(160, $chartData[3]['bar_height']); // Maximum peak bar height

        // Day 2 (Sep 28): 4500
        $this->assertEquals(4500.00, $chartData[4]['revenue']);
        $this->assertEquals(1, $chartData[4]['count']);
        $this->assertFalse($chartData[4]['is_peak']);

        // Day 1 (Sep 29): 6000
        $this->assertEquals(6000.00, $chartData[5]['revenue']);
        $this->assertEquals(2, $chartData[5]['count']);
        $this->assertFalse($chartData[5]['is_peak']);

        // Day 0 (Sep 30): 8000 (Today)
        $this->assertEquals(8000.00, $chartData[6]['revenue']);
        $this->assertEquals(1, $chartData[6]['count']);
        $this->assertTrue($chartData[6]['is_today']);

        // Total 7-day revenue: 1500 + 5000 + 0 + 20000 + 4500 + 6000 + 8000 = ₹45,000
        $chartTotalRevenue = $response->viewData('chartTotalRevenue');
        $this->assertEquals(45000.00, $chartTotalRevenue);

        // Chart total count: 1 + 2 + 0 + 1 + 1 + 2 + 1 = 8 orders
        $chartTotalCount = $response->viewData('chartTotalCount');
        $this->assertEquals(8, $chartTotalCount);

        // Peak day details:
        $peakDayAmount = $response->viewData('peakDayAmount');
        $this->assertEquals(20000.00, $peakDayAmount);
        $peakDayName = $response->viewData('peakDayName');
        $this->assertEquals('Sun', $peakDayName);

        // Lifetime gross revenue includes the ₹35,000 order from 8 days ago:
        // ₹45,000 + ₹35,000 = ₹80,000
        $grossRevenue = $response->viewData('grossRevenue');
        $this->assertEquals(80000.00, $grossRevenue);
    }

    // =========================================================================
    // CHALLENGE 6: 7-DAY REVENUE CHART BOUNDARY EDGE CASES (ZERO REVENUE & TIED PEAKS)
    // =========================================================================

    /**
     * Challenge 6: Stress test 7-day chart with all-zero revenue (no division by zero)
     * and multiple tied peak days.
     */
    public function test_7_day_chart_all_zero_and_tied_peaks_boundary_resilience(): void
    {
        Carbon::setTestNow('2026-09-30 12:00:00');

        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // Case A: 0 orders anywhere in past 7 days
        $respZero = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $respZero->assertStatus(200);

        $chartZero = $respZero->viewData('revenueChartData');
        $this->assertCount(7, $chartZero);
        foreach ($chartZero as $day) {
            $this->assertEquals(0.00, $day['revenue']);
            $this->assertEquals(0, $day['count']);
            $this->assertFalse($day['is_peak']);
            $this->assertEquals(10, $day['bar_height']); // Safe fallback
        }
        $this->assertEquals('None', $respZero->viewData('peakDayName'));
        $this->assertEquals(0.00, $respZero->viewData('peakDayAmount'));

        // Case B: Tied peaks (Day 2 ago and Day 5 ago both have ₹15,000)
        // Day 5 ago = 2026-09-25
        // Day 2 ago = 2026-09-28
        $this->createHistoricalOrder($seller, $buyer, 15000.00, '2026-09-25 10:00:00', 'delivered');
        $this->createHistoricalOrder($seller, $buyer, 15000.00, '2026-09-28 10:00:00', 'delivered');

        $respTied = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $respTied->assertStatus(200);

        $chartTied = $respTied->viewData('revenueChartData');
        // Day 5 (index 1: Sep 25) and Day 2 (index 4: Sep 28) should both be peak
        $this->assertTrue($chartTied[1]['is_peak'], 'Day index 1 should be peak');
        $this->assertEquals(160, $chartTied[1]['bar_height']);
        $this->assertTrue($chartTied[4]['is_peak'], 'Day index 4 should be peak');
        $this->assertEquals(160, $chartTied[4]['bar_height']);

        $this->assertEquals(15000.00, $respTied->viewData('peakDayAmount'));
        $this->assertNotEquals('None', $respTied->viewData('peakDayName'));
    }

    // =========================================================================
    // CHALLENGE 7: TOP PRODUCTS DEMAND VELOCITY ORACLE & CANCELLATION ISOLATION
    // =========================================================================

    /**
     * Challenge 7: Top products demand velocity ranks products strictly by revenue descending,
     * sums units sold across multiple orders, excludes items from cancelled orders,
     * and maintains cross-tenant data isolation.
     */
    public function test_top_products_demand_velocity_oracle_with_multi_items_and_cancellation_filtration(): void
    {
        $seller = $this->createApprovedSeller();
        $otherSeller = $this->createApprovedSeller(['name' => 'Competitor Merchant']);
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        $prodAlpha = $this->createProductRecord($seller, 'Alpha Organic Honey', 500.00, 50, 10, 'active');
        $prodBeta  = $this->createProductRecord($seller, 'Beta Desi Ghee', 800.00, 40, 10, 'active');
        $prodGamma = $this->createProductRecord($seller, 'Gamma Mustard Oil', 200.00, 100, 10, 'active');
        $prodCancelled = $this->createProductRecord($seller, 'Cancelled Big Batch', 1000.00, 10, 10, 'active');

        // Order 1 (active): Alpha x 10 (₹5,000), Beta x 5 (₹4,000)
        $so1 = $this->createSellerOrderRecord($seller, $buyer, 9000.00, 'SO-VEL-01', 'delivered');
        OrderItem::create([
            'seller_order_id' => $so1->id,
            'product_id'      => $prodAlpha->id,
            'product_name'    => $prodAlpha->name,
            'unit_price'      => 500.00,
            'quantity'        => 10,
            'total_price'     => 5000.00,
        ]);
        OrderItem::create([
            'seller_order_id' => $so1->id,
            'product_id'      => $prodBeta->id,
            'product_name'    => $prodBeta->name,
            'unit_price'      => 800.00,
            'quantity'        => 5,
            'total_price'     => 4000.00,
        ]);

        // Order 2 (active): Alpha x 20 (₹10,000) -> Alpha total = 30 units, ₹15,000 (RANK 1)
        $so2 = $this->createSellerOrderRecord($seller, $buyer, 10000.00, 'SO-VEL-02', 'delivered');
        OrderItem::create([
            'seller_order_id' => $so2->id,
            'product_id'      => $prodAlpha->id,
            'product_name'    => $prodAlpha->name,
            'unit_price'      => 500.00,
            'quantity'        => 20,
            'total_price'     => 10000.00,
        ]);

        // Order 3 (active): Gamma x 30 (₹6,000) -> Gamma total = 30 units, ₹6,000 (RANK 2)
        // Beta total = 5 units, ₹4,000 (RANK 3)
        $so3 = $this->createSellerOrderRecord($seller, $buyer, 6000.00, 'SO-VEL-03', 'delivered');
        OrderItem::create([
            'seller_order_id' => $so3->id,
            'product_id'      => $prodGamma->id,
            'product_name'    => $prodGamma->name,
            'unit_price'      => 200.00,
            'quantity'        => 30,
            'total_price'     => 6000.00,
        ]);

        // Order 4 (CANCELLED): Cancelled product x 100 (₹100,000) -> MUST BE COMPLETELY EXCLUDED
        $so4 = $this->createSellerOrderRecord($seller, $buyer, 100000.00, 'SO-VEL-04', 'cancelled');
        OrderItem::create([
            'seller_order_id' => $so4->id,
            'product_id'      => $prodCancelled->id,
            'product_name'    => $prodCancelled->name,
            'unit_price'      => 1000.00,
            'quantity'        => 100,
            'total_price'     => 100000.00,
        ]);

        // Order 5 (OTHER SELLER): Competitor sells Alpha product x 500 (₹250,000) -> MUST BE ISOLATED
        $soOther = $this->createSellerOrderRecord($otherSeller, $buyer, 250000.00, 'SO-OTHER-01', 'delivered');
        OrderItem::create([
            'seller_order_id' => $soOther->id,
            'product_id'      => $prodAlpha->id,
            'product_name'    => $prodAlpha->name,
            'unit_price'      => 500.00,
            'quantity'        => 500,
            'total_price'     => 250000.00,
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $response->assertStatus(200);

        $topProducts = $response->viewData('topProducts');
        $this->assertCount(3, $topProducts);

        // Strict Ranking Invariant:
        // Rank 1: Alpha (₹15,000 revenue, 30 units)
        $this->assertEquals($prodAlpha->id, $topProducts[0]->product_id);
        $this->assertEquals(15000.00, $topProducts[0]->total_revenue);
        $this->assertEquals(30, $topProducts[0]->units_sold);

        // Rank 2: Gamma (₹6,000 revenue, 30 units)
        $this->assertEquals($prodGamma->id, $topProducts[1]->product_id);
        $this->assertEquals(6000.00, $topProducts[1]->total_revenue);
        $this->assertEquals(30, $topProducts[1]->units_sold);

        // Rank 3: Beta (₹4,000 revenue, 5 units)
        $this->assertEquals($prodBeta->id, $topProducts[2]->product_id);
        $this->assertEquals(4000.00, $topProducts[2]->total_revenue);
        $this->assertEquals(5, $topProducts[2]->units_sold);

        // Cancelled product must not appear
        $productIds = $topProducts->pluck('product_id')->toArray();
        $this->assertNotContains($prodCancelled->id, $productIds);
    }

    // =========================================================================
    // CHALLENGE 8: NEXT SETTLEMENT CASCADE LOGIC
    // =========================================================================

    /**
     * Challenge 8: Next settlement calculation cascades correctly:
     * - Uses pending Payout records when available
     * - Falls back to active SellerOrder payout_amount when no Payout record exists
     * - Renders 0 when all orders are completed or cancelled
     */
    public function test_next_settlement_cascade_logic(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // Case 1: No orders, no payouts -> next payout is 0.0
        $resp1 = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $this->assertEquals(0.00, $resp1->viewData('nextPayout'));

        // Case 2: Order in 'processing' with payout_amount ₹7,200 (no Payout table record yet)
        $order = $this->createSellerOrderRecord($seller, $buyer, 8000.00, 'SO-PAY-01', 'processing');
        $order->update(['payout_amount' => 7200.00]);

        $resp2 = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $this->assertEquals(7200.00, $resp2->viewData('nextPayout'));

        // Case 3: Explicit Payout record created with pending status for ₹15,500
        Payout::create([
            'seller_id'         => $seller->id,
            'seller_order_id'   => $order->id,
            'gross_amount'      => 17000.00,
            'commission_amount' => 1500.00,
            'net_amount'        => 15500.00,
            'status'            => 'pending',
            'payout_reference'  => 'PAY-EXP-001',
        ]);

        $resp3 = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        // Payout table sum takes precedence over raw orders
        $this->assertEquals(15500.00, $resp3->viewData('nextPayout'));
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
            'shop_name'           => 'Empirical Testing Shop ' . $seller->id,
            'shop_slug'           => 'empirical-shop-' . $seller->id,
            'seller_type'         => 'Farmer',
            'city'                => 'Contai',
            'state'               => 'West Bengal',
            'status'              => 'approved',
            'trust_score'         => 94.00,
            'commission_rate'     => 10.00,
            'address'             => 'Plot 100, Agro Zone',
            'operating_radius_km' => 30,
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
            'delivery_address_line_1' => 'Agro Market Street',
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

    protected function createHistoricalOrder(User $seller, User $buyer, float $subtotal, string $datetime, string $status): SellerOrder
    {
        $carbonDate = Carbon::parse($datetime);

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
            'delivery_address_line_1' => 'Agro Market Street',
            'delivery_city'           => 'Contai',
            'delivery_state'          => 'West Bengal',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '721401',
            'created_at'              => $carbonDate,
            'updated_at'              => $carbonDate,
        ]);

        $sellerOrder = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => 'SO-' . uniqid(),
            'subtotal'            => $subtotal,
            'shipping_amount'     => 0.00,
            'commission_rate'     => 10.00,
            'commission_amount'   => round($subtotal * 0.10, 2),
            'payout_amount'       => round($subtotal * 0.90, 2),
            'status'              => $status,
            'delivery_slot'       => 'Morning 8AM - 11AM',
        ]);

        // Explicitly set timestamps via DB to override Eloquent auto-timestamping
        \Illuminate\Support\Facades\DB::table('seller_orders')
            ->where('id', $sellerOrder->id)
            ->update([
                'created_at' => $carbonDate->toDateTimeString(),
                'updated_at' => $carbonDate->toDateTimeString(),
            ]);

        return $sellerOrder->fresh();
    }
}
