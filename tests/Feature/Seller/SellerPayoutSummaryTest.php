<?php

namespace Tests\Feature\Seller;

use App\Models\Payout;
use App\Models\SellerOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class SellerPayoutSummaryTest
 *
 * Dedicated Test Suite for Item 78: "View Payout Summary" in the Seller Payout Module.
 * Covers:
 *  - Rendering of "View Payout Summary" triggers in index, table, and inspector
 *  - Dedicated Payout Summary statement workspace (/seller/payouts/summary)
 *  - Accurate dynamic KPI aggregations (lifetime revenue, platform commission, net settled, in-flight escrow, average payout)
 *  - Strict multi-tenant isolation (Seller A never leaks Seller B summary metrics)
 *  - Links from single payout receipt page back to summary
 *  - Guest & pending seller perimeter security gating
 *  - JSON API representation of financial summary
 */
class SellerPayoutSummaryTest extends TestCase
{
    use RefreshDatabase, SellerTestHelperTrait;

    /**
     * Test 1: Payouts index renders "View Payout Summary" button and modal elements.
     */
    public function test_payouts_index_renders_view_payout_summary_triggers_and_modal(): void
    {
        $seller = $this->createApprovedSeller([], ['shop_name' => 'Kolkata Mango Orchard']);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.payouts.index'));

        $response->assertStatus(200);
        $response->assertSee('View Payout Summary');
        $response->assertSee('Merchant Payout Summary');
        $response->assertSee('Item 78');
        $response->assertSee('Gross Sales Realized');
        $response->assertSee('Fixed 10.0% transparent');
    }

    /**
     * Test 2: Approved seller can access dedicated payout summary route (/seller/payouts/summary).
     */
    public function test_approved_seller_can_access_dedicated_payout_summary_route(): void
    {
        $seller = $this->createApprovedSeller([], ['shop_name' => 'Sundarbans Natural Honey']);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.payouts.summary'));

        $response->assertStatus(200);
        $response->assertViewIs('seller.payouts.summary');
        $response->assertSee('Merchant Payout Summary');
        $response->assertSee('Sundarbans Natural Honey');
        $response->assertSee('Gross Sales Realized');
        $response->assertSee('Platform Commission');
        $response->assertSee('Total Disbursed (Paid)');
        $response->assertSee('In-Flight Escrow Reserve');
        $response->assertSee('Zero-Hidden-Fee Guarantee');
        $response->assertSee('Automated Direct NEFT / RTGS');
    }

    /**
     * Test 3: Payout summary computes dynamic financial metrics and average payout accurately.
     */
    public function test_payout_summary_computes_dynamic_metrics_accurately(): void
    {
        $seller = $this->createApprovedSeller();

        // 2 paid settlements: ₹2,500 gross (₹250 comm, ₹2,000 net, ₹250 cess/diff) and ₹6,000 gross (₹600 comm, ₹5,000 net)
        $this->createPayoutRecord($seller, null, 2500.00, 250.00, 2000.00, 'paid');
        $this->createPayoutRecord($seller, null, 6000.00, 600.00, 5000.00, 'paid');

        // 1 processing settlement: ₹3,500 gross (₹350 comm, ₹3,000 net)
        $this->createPayoutRecord($seller, null, 3500.00, 350.00, 3000.00, 'processing');

        // Expected KPIs:
        // Lifetime Gross: 2500 + 6000 + 3500 = 12000.00
        // Platform Commission: 250 + 600 + 350 = 1200.00
        // Total Settled (Paid): 2000 + 5000 = 7000.00
        // In-flight Escrow: 3000.00
        // Average Payout (for paid): 7000 / 2 = 3500.00

        $response = $this->actingAs($seller, 'seller')->get(route('seller.payouts.summary'));

        $response->assertStatus(200);
        $summary = $response->viewData('summary');

        $this->assertEquals(12000.00, (float) $summary['lifetime_revenue']);
        $this->assertEquals(1200.00, (float) $summary['platform_commission']);
        $this->assertEquals(7000.00, (float) $summary['total_settled']);
        $this->assertEquals(3000.00, (float) $summary['pending_processing']);
        $this->assertEquals(3500.00, (float) $summary['avg_payout']);
        $this->assertEquals(2, $summary['paid_count']);
        $this->assertEquals(1, $summary['processing_count']);

        $response->assertSee('12,000.00');
        $response->assertSee('1,200.00');
        $response->assertSee('7,000.00');
        $response->assertSee('3,000.00');
        $response->assertSee('3,500.00');
    }

    /**
     * Test 4: Strict multi-tenant isolation on payout summary workspace.
     */
    public function test_strict_multi_tenant_isolation_on_payout_summary(): void
    {
        $sellerA = $this->createApprovedSeller([], ['shop_name' => 'Seller A Organic Farm']);
        $sellerB = $this->createApprovedSeller([], ['shop_name' => 'Seller B Secret Harvest']);

        // Seller A has 1,500 gross / 150 comm / 1,350 net
        $this->createPayoutRecord($sellerA, null, 1500.00, 150.00, 1350.00, 'paid', [
            'payout_reference' => 'PO-A-SUMMARY-01'
        ]);

        // Seller B has 95,000 gross / 9,500 comm / 85,500 net
        $this->createPayoutRecord($sellerB, null, 95000.00, 9500.00, 85500.00, 'paid', [
            'payout_reference' => 'PO-B-SECRET-99'
        ]);

        $response = $this->actingAs($sellerA, 'seller')->get(route('seller.payouts.summary'));

        $response->assertStatus(200);
        $response->assertSee('1,500.00');
        $response->assertSee('1,350.00');
        $response->assertSee('PO-A-SUMMARY-01');

        // Must NEVER leak Seller B's confidential financials
        $response->assertDontSee('95,000.00');
        $response->assertDontSee('85,500.00');
        $response->assertDontSee('PO-B-SECRET-99');
        $response->assertDontSee('Seller B Secret Harvest');
    }

    /**
     * Test 5: Single payout details page includes link to View Payout Summary.
     */
    public function test_payout_show_contains_link_to_view_payout_summary(): void
    {
        $seller = $this->createApprovedSeller();
        $payout = $this->createPayoutRecord($seller, null, 4000.00, 400.00, 3600.00, 'paid', [
            'payout_reference' => 'PO-SHOW-LINK-01'
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.payouts.show', $payout));

        $response->assertStatus(200);
        $response->assertSee('View Payout Summary');
        $response->assertSee(route('seller.payouts.summary'));
    }

    /**
     * Test 6: Perimeter security gates unauthenticated guest and pending unapproved seller.
     */
    public function test_perimeter_security_gates_guest_and_pending_seller_from_summary(): void
    {
        // 1. Guest redirected to login
        $this->get(route('seller.payouts.summary'))->assertRedirect(route('login'));

        // 2. Pending unapproved seller redirected to pending terminal
        $pendingSeller = $this->createPendingSeller();
        $this->actingAs($pendingSeller, 'seller')
            ->get(route('seller.payouts.summary'))
            ->assertRedirect(route('seller.pending'));
    }

    /**
     * Test 7: Payout summary supports JSON API representation.
     */
    public function test_payout_summary_json_endpoint_returns_structured_metrics(): void
    {
        $seller = $this->createApprovedSeller();
        $this->createPayoutRecord($seller, null, 1000.00, 100.00, 900.00, 'paid');

        $response = $this->actingAs($seller, 'seller')
            ->getJson(route('seller.payouts.summary'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'kpis' => [
                'lifetime_revenue',
                'platform_commission',
                'total_settled',
                'pending_processing',
            ],
            'statusCounts',
            'summary' => [
                'lifetime_revenue',
                'platform_commission',
                'total_settled',
                'pending_processing',
                'total_batches',
                'paid_count',
                'avg_payout',
                'commission_rate',
            ],
            'upcomingSettlement',
        ]);
    }
}
