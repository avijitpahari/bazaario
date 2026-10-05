<?php

namespace Tests\Feature\Seller;

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
use Tests\TestCase;

/**
 * Class Milestone4EmpiricalChallengeTest
 *
 * Empirical Challenge Suite for Milestone 4: Order Fulfillment & Payout Management (Features 26–33).
 *
 * Challenge Domains:
 * 1. Multi-Tenant Security Boundary Isolation (Assert 403 on Cross-Tenant Operations)
 * 2. Commission Calculation Precision & Deductions Ledger (subtotal - 10% - 1.5% = net_payout)
 * 3. Order Status Progression Constraints & State Machine Integrity (Linear transitions & terminal immutability)
 */
class Milestone4EmpiricalChallengeTest extends TestCase
{
    use RefreshDatabase, SellerTestHelperTrait;

    protected Category $defaultCategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->defaultCategory = $this->createCategory('Agricultural Staples');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    // =========================================================================
    // DOMAIN 1: MULTI-TENANT SECURITY BOUNDARY ISOLATION (ASSERT 403)
    // =========================================================================

    /**
     * Challenge 1.1: IDOR GET order details - Seller A cannot view Seller B's order.
     * Expectation: HTTP 403 Forbidden.
     */
    public function test_challenge_idor_seller_a_cannot_view_seller_b_order_returns_403(): void
    {
        $sellerA = $this->createApprovedSeller(['name' => 'Seller Alpha', 'email' => 'alpha@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['name' => 'Seller Beta', 'email' => 'beta@bazaario.com']);
        $buyer = $this->createBuyer(['name' => 'Kiran Deshmukh']);

        $orderB = $this->createSellerOrderRecord($sellerB, $buyer, 2500.00, 'SO-BETA-001', 'processing');

        $response = $this->actingAs($sellerA, 'seller')->get(route('seller.orders.show', $orderB));

        $response->assertStatus(403);
    }

    /**
     * Challenge 1.2: IDOR PATCH order status - Seller A cannot update Seller B's order status.
     * Expectation: HTTP 403 Forbidden, status on orderB remains unchanged.
     */
    public function test_challenge_idor_seller_a_cannot_update_seller_b_order_status_returns_403(): void
    {
        $sellerA = $this->createApprovedSeller(['name' => 'Seller Alpha', 'email' => 'alpha@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['name' => 'Seller Beta', 'email' => 'beta@bazaario.com']);
        $buyer = $this->createBuyer();

        $orderB = $this->createSellerOrderRecord($sellerB, $buyer, 3000.00, 'SO-BETA-002', 'processing');

        $response = $this->actingAs($sellerA, 'seller')->patch(route('seller.orders.update-status', $orderB), [
            'status' => 'ready_for_pickup',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('processing', $orderB->fresh()->status);
    }

    /**
     * Challenge 1.3: IDOR POST fulfill order - Seller A cannot fulfill Seller B's order.
     * Expectation: HTTP 403 Forbidden, status on orderB remains unchanged.
     */
    public function test_challenge_idor_seller_a_cannot_fulfill_seller_b_order_returns_403(): void
    {
        $sellerA = $this->createApprovedSeller(['name' => 'Seller Alpha', 'email' => 'alpha@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['name' => 'Seller Beta', 'email' => 'beta@bazaario.com']);
        $buyer = $this->createBuyer();

        $orderB = $this->createSellerOrderRecord($sellerB, $buyer, 4500.00, 'SO-BETA-003', 'ready_for_pickup');

        $response = $this->actingAs($sellerA, 'seller')->post(route('seller.orders.fulfill', $orderB), [
            'courier_name' => 'Bazaario Hyperlocal Fleet #BLR-44',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('ready_for_pickup', $orderB->fresh()->status);
        $this->assertNull($orderB->fresh()->delivered_at);
        $this->assertNull(Payout::where('seller_order_id', $orderB->id)->first());
    }

    /**
     * Challenge 1.4: IDOR POST handover order - Seller A cannot handover Seller B's order.
     * Expectation: HTTP 403 Forbidden, status on orderB remains unchanged.
     */
    public function test_challenge_idor_seller_a_cannot_handover_seller_b_order_returns_403(): void
    {
        $sellerA = $this->createApprovedSeller(['name' => 'Seller Alpha', 'email' => 'alpha@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['name' => 'Seller Beta', 'email' => 'beta@bazaario.com']);
        $buyer = $this->createBuyer();

        $orderB = $this->createSellerOrderRecord($sellerB, $buyer, 1800.00, 'SO-BETA-004', 'ready_for_pickup');

        $response = $this->actingAs($sellerA, 'seller')->post(route('seller.orders.handover', $orderB), [
            'courier_bay' => 'Dock 7',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('ready_for_pickup', $orderB->fresh()->status);
        $this->assertNull(Payout::where('seller_order_id', $orderB->id)->first());
    }

    /**
     * Challenge 1.5: IDOR GET single payout details - Seller A cannot view Seller B's payout record.
     * Expectation: HTTP 403 Forbidden.
     */
    public function test_challenge_idor_seller_a_cannot_view_seller_b_payout_returns_403(): void
    {
        $sellerA = $this->createApprovedSeller(['name' => 'Seller Alpha', 'email' => 'alpha@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['name' => 'Seller Beta', 'email' => 'beta@bazaario.com']);

        $payoutB = $this->createPayoutRecord($sellerB, null, 15000.00, 1500.00, 13500.00, 'paid', [
            'payout_reference' => 'PO-SECRET-BETA-99',
        ]);

        $response = $this->actingAs($sellerA, 'seller')->get(route('seller.payouts.show', $payoutB));

        $response->assertStatus(403);
    }

    /**
     * Challenge 1.6: Orders queue listing isolation - Seller A's index workspace never lists Seller B's orders or status counts.
     */
    public function test_challenge_orders_queue_listing_never_leaks_seller_b_orders_or_counts(): void
    {
        $sellerA = $this->createApprovedSeller(['name' => 'Seller Alpha', 'email' => 'alpha@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['name' => 'Seller Beta', 'email' => 'beta@bazaario.com']);
        $buyerA = $this->createBuyer(['name' => 'Buyer of Alpha']);
        $buyerB = $this->createBuyer(['name' => 'Buyer of Beta']);

        // Create 3 orders for Seller A
        $this->createSellerOrderRecord($sellerA, $buyerA, 1000.00, 'SO-ALPHA-01', 'placed');
        $this->createSellerOrderRecord($sellerA, $buyerA, 2000.00, 'SO-ALPHA-02', 'processing');
        $this->createSellerOrderRecord($sellerA, $buyerA, 3000.00, 'SO-ALPHA-03', 'fulfilled');

        // Create 4 orders for Seller B
        $this->createSellerOrderRecord($sellerB, $buyerB, 5000.00, 'SO-BETA-01', 'placed');
        $this->createSellerOrderRecord($sellerB, $buyerB, 6000.00, 'SO-BETA-02', 'processing');
        $this->createSellerOrderRecord($sellerB, $buyerB, 7000.00, 'SO-BETA-03', 'processing');
        $this->createSellerOrderRecord($sellerB, $buyerB, 8000.00, 'SO-BETA-04', 'cancelled');

        $response = $this->actingAs($sellerA, 'seller')->get(route('seller.orders.index'));

        $response->assertStatus(200);
        $response->assertSee('SO-ALPHA-01');
        $response->assertSee('SO-ALPHA-02');
        $response->assertSee('SO-ALPHA-03');
        $response->assertSee('Buyer of Alpha');

        // Verify Seller B's data is completely absent
        $response->assertDontSee('SO-BETA-01');
        $response->assertDontSee('SO-BETA-02');
        $response->assertDontSee('SO-BETA-03');
        $response->assertDontSee('SO-BETA-04');
        $response->assertDontSee('Buyer of Beta');

        // Verify status counts in view reflect only Seller A's counts
        $counts = $response->viewData('statusCounts');
        $this->assertEquals(3, $counts['all']);
        $this->assertEquals(1, $counts['pending']);
        $this->assertEquals(1, $counts['processing']);
        $this->assertEquals(1, $counts['fulfilled']);
        $this->assertEquals(0, $counts['cancelled']);
    }

    /**
     * Challenge 1.7: Inspector focus injection - Seller A cannot view Seller B's order via ?order_id parameter.
     */
    public function test_challenge_orders_index_selected_order_injection_defense(): void
    {
        $sellerA = $this->createApprovedSeller(['name' => 'Seller Alpha', 'email' => 'alpha@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['name' => 'Seller Beta', 'email' => 'beta@bazaario.com']);
        $buyerA = $this->createBuyer(['name' => 'Customer Alpha']);
        $buyerB = $this->createBuyer(['name' => 'Confidential Customer Beta']);

        $orderA = $this->createSellerOrderRecord($sellerA, $buyerA, 1200.00, 'SO-ALPHA-SAFE', 'placed');
        $orderB = $this->createSellerOrderRecord($sellerB, $buyerB, 9900.00, 'SO-BETA-PRIVATE', 'placed');

        // Seller A requests index attempting to focus Seller B's order
        $response = $this->actingAs($sellerA, 'seller')->get(route('seller.orders.index', ['order_id' => $orderB->id]));

        $response->assertStatus(200);
        // Inspector must not display Seller B's confidential details
        $response->assertDontSee('SO-BETA-PRIVATE');
        $response->assertDontSee('Confidential Customer Beta');

        // Focused order in view must fall back to Seller A's order
        $focused = $response->viewData('focusedOrder');
        $this->assertNotNull($focused);
        $this->assertEquals($orderA->id, $focused->id);
    }

    /**
     * Challenge 1.8: Payouts workspace isolation - KPIs and settlement ledger strictly isolated.
     */
    public function test_challenge_payouts_workspace_isolation_and_kpi_integrity(): void
    {
        $sellerA = $this->createApprovedSeller(['name' => 'Seller Alpha', 'email' => 'alpha@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['name' => 'Seller Beta', 'email' => 'beta@bazaario.com']);

        $soA = $this->createSellerOrderRecord($sellerA, $this->createBuyer(), 2000.00, 'SO-A-PAY', 'delivered');
        $this->createPayoutRecord($sellerA, $soA, 2000.00, 200.00, 1800.00, 'paid', [
            'payout_reference' => 'PO-ALPHA-SETTLED',
        ]);

        $soB = $this->createSellerOrderRecord($sellerB, $this->createBuyer(), 100000.00, 'SO-B-PAY', 'delivered');
        $this->createPayoutRecord($sellerB, $soB, 100000.00, 10000.00, 90000.00, 'paid', [
            'payout_reference' => 'PO-BETA-MASSIVE',
        ]);

        $response = $this->actingAs($sellerA, 'seller')->get(route('seller.payouts.index'));

        $response->assertStatus(200);
        $response->assertSee('PO-ALPHA-SETTLED');
        $response->assertDontSee('PO-BETA-MASSIVE');

        $kpis = $response->viewData('kpis');
        $this->assertEquals(2000.00, $kpis['lifetime_revenue']);
        $this->assertEquals(200.00, $kpis['platform_commission']);
        $this->assertEquals(1800.00, $kpis['total_settled']);
    }

    /**
     * Challenge 1.9: Multi-Seller Parent Order Disaggregation:
     * When a customer places an order with products from multiple merchants, each seller
     * can only view and fulfill their own sub-consignment.
     */
    public function test_challenge_multi_seller_parent_order_suborder_partitioning_and_parent_completion(): void
    {
        $sellerA = $this->createApprovedSeller(['name' => 'Farm Alpha']);
        $sellerB = $this->createApprovedSeller(['name' => 'Orchard Beta']);
        $buyer = $this->createBuyer(['name' => 'Pooja Hegde']);

        $productA = $this->createProductRecord($sellerA, 'Organic Basmati Rice', 150.00, 100, 'kg');
        $productB = $this->createProductRecord($sellerB, 'Kashmiri Apples', 250.00, 80, 'kg');

        $multiOrder = $this->createMultiSellerOrder($buyer, [
            [
                'seller' => $sellerA,
                'items'  => [['product' => $productA, 'qty' => 4]], // ₹600.00
            ],
            [
                'seller' => $sellerB,
                'items'  => [['product' => $productB, 'qty' => 2]], // ₹500.00
            ],
        ]);

        $parentOrder = $multiOrder['parent_order'];
        $sellerOrderA = $multiOrder['seller_orders']->firstWhere('seller_id', $sellerA->id);
        $sellerOrderB = $multiOrder['seller_orders']->firstWhere('seller_id', $sellerB->id);

        $this->assertEquals(2, SellerOrder::where('order_id', $parentOrder->id)->count());

        // Seller A views their order
        $responseA = $this->actingAs($sellerA, 'seller')->get(route('seller.orders.show', $sellerOrderA));
        $responseA->assertStatus(200);
        $responseA->assertSee('Organic Basmati Rice');
        $responseA->assertDontSee('Kashmiri Apples');

        // Seller A tries to view Seller B's sub-order -> 403
        $responseAttack = $this->actingAs($sellerA, 'seller')->get(route('seller.orders.show', $sellerOrderB));
        $responseAttack->assertStatus(403);

        // Progress Seller A's sub-order to ready_for_pickup then fulfill
        $sellerOrderA->update(['status' => 'ready_for_pickup']);
        $this->actingAs($sellerA, 'seller')->post(route('seller.orders.fulfill', $sellerOrderA));
        $this->assertEquals('fulfilled', $sellerOrderA->fresh()->status);

        // Parent order must NOT be completed yet because Seller B has not fulfilled
        $this->assertEquals('processing', $parentOrder->fresh()->order_status);
        $this->assertEquals('placed', $sellerOrderB->fresh()->status);

        // Now Seller B fulfills their sub-order
        $sellerOrderB->update(['status' => 'ready_for_pickup']);
        $this->actingAs($sellerB, 'seller')->post(route('seller.orders.fulfill', $sellerOrderB));
        $this->assertEquals('fulfilled', $sellerOrderB->fresh()->status);

        // With all sub-orders fulfilled, parent order must now be completed
        $this->assertEquals('completed', $parentOrder->fresh()->order_status);
    }

    /**
     * Challenge 1.10: Unauthorized Perimeter Guard:
     * Unauthenticated guests and pending unapproved sellers are blocked from order and payout workspaces.
     */
    public function test_challenge_unauthorized_perimeter_guest_and_pending_seller(): void
    {
        $pendingSeller = $this->createPendingSeller();

        // Guest access
        $this->get(route('seller.orders.index'))->assertRedirect(route('login'));
        $this->get(route('seller.payouts.index'))->assertRedirect(route('login'));

        // Pending seller access redirected to pending gate
        $this->actingAs($pendingSeller, 'seller')->get(route('seller.orders.index'))
            ->assertRedirect(route('seller.pending'));
        $this->actingAs($pendingSeller, 'seller')->get(route('seller.payouts.index'))
            ->assertRedirect(route('seller.pending'));
    }

    // =========================================================================
    // DOMAIN 2: COMMISSION CALCULATION PRECISION & DEDUCTIONS LEDGER
    // Formula: subtotal - (subtotal * 0.10) - (subtotal * 0.015) = net_payout
    // =========================================================================

    /**
     * Challenge 2.1: Mathematical precision of commission and APMC cess across multiple order values.
     * Evaluates: Gross - 10% Platform Fee - 1.5% APMC Mandi Cess = Net Seller Payout.
     */
    public function test_challenge_commission_precision_across_diverse_order_values(): void
    {
        $seller = $this->createApprovedSeller([], ['commission_rate' => 10.00]);
        $buyer = $this->createBuyer();

        $testSubtotals = [
            1000.00,    // Standard clean round benchmark
            1950.00,    // Real-world non-round basket
            333.33,     // Fractional odd cents recurring
            99.99,      // High precision price point
            1.50,       // Micro order boundary
            12547.85,   // Large volume order
            25000.00,   // Bulk wholesale
            749.50,     // Common mid-range
            12.34,      // Small odd cents
            0.00,       // Zero dollar order
        ];

        foreach ($testSubtotals as $subtotal) {
            $expectedCommission = round($subtotal * 0.10, 2);
            $expectedApmcCess = round($subtotal * 0.015, 2);
            $expectedNet = max(0, round($subtotal - $expectedCommission - $expectedApmcCess, 2));

            $so = $this->createSellerOrderRecord($seller, $buyer, $subtotal, 'SO-MATH-' . uniqid(), 'placed', [
                'commission_rate'   => 10.00,
                'commission_amount' => $expectedCommission,
                'payout_amount'     => $expectedNet,
            ]);

            // Test Model Accessors
            $this->assertEquals($expectedCommission, (float) $so->commission_amount, "Commission mismatch for subtotal: {$subtotal}");
            $this->assertEquals($expectedApmcCess, (float) $so->apmc_cess, "APMC cess mismatch for subtotal: {$subtotal}");
            $this->assertEquals($expectedNet, (float) $so->net_payout_calculated, "Net calculated mismatch for subtotal: {$subtotal}");

            // Verify mathematical identity holds exactly
            $reconstituted = round((float) $so->subtotal - (float) $so->commission_amount - (float) $so->apmc_cess, 2);
            $this->assertEquals($expectedNet, max(0, $reconstituted), "Mathematical deduction identity failed for subtotal: {$subtotal}");
        }
    }

    /**
     * Challenge 2.2: Payout Model Accessor precision for APMC cess and net amount.
     */
    public function test_challenge_payout_model_accessors_and_cess_deduction(): void
    {
        $seller = $this->createApprovedSeller();

        $payout = $this->createPayoutRecord($seller, null, 1950.00, 195.00, 1725.75, 'pending', [
            'apmc_cess' => 29.25,
        ]);

        $this->assertEquals(1950.00, $payout->amount);
        $this->assertEquals(195.00, $payout->commission_fee);
        $this->assertEquals(29.25, $payout->apmc_cess);
        $this->assertEquals(1725.75, (float) $payout->net_amount);

        // Mathematical identity
        $this->assertEquals(1725.75, round($payout->gross_amount - $payout->commission_amount - $payout->apmc_cess, 2));
    }

    /**
     * Challenge 2.3: Order fulfillment generates Payout record with exact mathematical deductions.
     * When order is fulfilled, gross, 10% fee, 1.5% APMC cess, and net amount are recorded.
     */
    public function test_challenge_order_fulfillment_generates_accurate_payout_deductions(): void
    {
        $seller = $this->createApprovedSeller([], ['commission_rate' => 10.00]);
        $buyer = $this->createBuyer();

        $gross = 1950.00;
        $expectedComm = 195.00;
        $expectedCess = 29.25;
        $expectedNet = 1725.75;

        // Create order in ready_for_pickup with explicit net payout
        $order = $this->createSellerOrderRecord($seller, $buyer, $gross, 'SO-FULFILL-MATH', 'ready_for_pickup', [
            'commission_rate'   => 10.00,
            'commission_amount' => $expectedComm,
            'payout_amount'     => $expectedNet,
        ]);

        $response = $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $order), [
            'courier_name' => 'Bazaario Hyperlocal Fleet #BLR-44',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('fulfilled', $order->fresh()->status);

        $payout = Payout::where('seller_order_id', $order->id)->first();
        $this->assertNotNull($payout, 'Payout record must be created upon fulfillment.');
        $this->assertEquals($gross, (float) $payout->gross_amount);
        $this->assertEquals($expectedComm, (float) $payout->commission_amount);
        $this->assertEquals($expectedCess, (float) $payout->apmc_cess);
        $this->assertEquals($expectedNet, (float) $payout->net_amount);
        $this->assertEquals('pending', $payout->status);
        $this->assertStringStartsWith('PO-', $payout->payout_reference);
    }

    /**
     * Challenge 2.4: Transparent Commission ledger rendered accurately in Blade views.
     */
    public function test_challenge_transparent_commission_ledger_rendered_in_blade(): void
    {
        $seller = $this->createApprovedSeller([], ['commission_rate' => 10.00]);
        $buyer = $this->createBuyer();

        $gross = 1950.00;
        $comm = 195.00;
        $cess = 29.25;
        $net = 1725.75;

        $so = $this->createSellerOrderRecord($seller, $buyer, $gross, 'SO-BLADE-MATH', 'processing', [
            'commission_rate'   => 10.00,
            'commission_amount' => $comm,
            'payout_amount'     => $net,
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.orders.index', ['order_id' => $so->id]));

        $response->assertStatus(200);
        $response->assertSee('1,950.00');
        $response->assertSee('195.00');
        $response->assertSee('29.25');
        $response->assertSee('1,725.75');
        $response->assertSee('Bazaario Commission (10%)');
        $response->assertSee('APMC Mandi Cess / Tech Fee (1.5%)');
        $response->assertSee('Net Seller Payout');
    }

    // =========================================================================
    // DOMAIN 3: ORDER STATUS PROGRESSION CONSTRAINTS & STATE MACHINE INTEGRITY
    // =========================================================================

    /**
     * Challenge 3.1: Reject jumping directly from placed/pending to fulfilled (skipping processing).
     */
    public function test_challenge_cannot_jump_from_placed_to_fulfilled_directly(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-JUMP-01', 'placed');

        $response = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status' => 'fulfilled',
        ]);

        // State machine must reject invalid skip transition
        $this->assertEquals('placed', $order->fresh()->status);
        $this->assertNull(Payout::where('seller_order_id', $order->id)->first());
    }

    /**
     * Challenge 3.2: Reject jumping directly from placed to ready_for_pickup (skipping processing).
     */
    public function test_challenge_cannot_jump_from_placed_to_ready_for_pickup_directly(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-JUMP-02', 'placed');

        $response = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status' => 'ready_for_pickup',
        ]);

        $this->assertEquals('placed', $order->fresh()->status);
    }

    /**
     * Challenge 3.3: Reject jumping directly from placed to shipped (skipping processing).
     */
    public function test_challenge_cannot_jump_from_placed_to_shipped_directly(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-JUMP-03', 'placed');

        $response = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status' => 'shipped',
        ]);

        $this->assertEquals('placed', $order->fresh()->status);
    }

    /**
     * Challenge 3.4: Fulfill endpoint rejects placed order without prior processing.
     */
    public function test_challenge_fulfill_endpoint_rejects_placed_order(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-JUMP-04', 'placed');

        $response = $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $order));

        $this->assertEquals('placed', $order->fresh()->status);
        $this->assertNull(Payout::where('seller_order_id', $order->id)->first());
    }

    /**
     * Challenge 3.5: Terminal State Guard: Fulfilled order cannot be reverted to processing or placed.
     */
    public function test_challenge_terminal_status_fulfilled_cannot_be_reverted(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 2000.00, 'SO-TERM-01', 'fulfilled');

        // Attempt reverting to processing
        $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status' => 'processing',
        ]);
        $this->assertEquals('fulfilled', $order->fresh()->status);

        // Attempt reverting to placed
        $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status' => 'placed',
        ]);
        $this->assertEquals('fulfilled', $order->fresh()->status);

        // Attempt re-fulfilling
        $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $order));
        $this->assertEquals('fulfilled', $order->fresh()->status);
    }

    /**
     * Challenge 3.6: Terminal State Guard: Delivered order cannot be modified.
     */
    public function test_challenge_terminal_status_delivered_cannot_be_modified(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 2000.00, 'SO-TERM-02', 'delivered');

        $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status' => 'processing',
        ]);
        $this->assertEquals('delivered', $order->fresh()->status);
    }

    /**
     * Challenge 3.7: Cancelled order cannot undergo status transitions or fulfillment.
     */
    public function test_challenge_cancelled_order_cannot_undergo_mutations_or_fulfillment(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 2000.00, 'SO-CANC-01', 'cancelled');

        // Attempt transition to processing
        $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status' => 'processing',
        ]);
        $this->assertEquals('cancelled', $order->fresh()->status);

        // Attempt transition to fulfilled via updateStatus
        $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status' => 'fulfilled',
        ]);
        $this->assertEquals('cancelled', $order->fresh()->status);

        // Attempt direct fulfill endpoint
        $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $order));
        $this->assertEquals('cancelled', $order->fresh()->status);
        $this->assertNull(Payout::where('seller_order_id', $order->id)->first());
    }

    /**
     * Challenge 3.8: Complete valid linear status progression lifecycle:
     * placed -> processing -> ready_for_pickup -> fulfilled (with timestamps and payout creation).
     */
    public function test_challenge_valid_linear_status_progression_lifecycle(): void
    {
        $seller = $this->createApprovedSeller([], ['commission_rate' => 10.00]);
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 3000.00, 'SO-LIFECYCLE-01', 'placed', [
            'payout_amount' => 2655.00,
        ]);

        // Step 1: placed -> processing
        $res1 = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status' => 'processing',
        ]);
        $res1->assertSessionHas('success');
        $this->assertEquals('processing', $order->fresh()->status);

        // Step 2: processing -> ready_for_pickup with courier name
        $res2 = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status'       => 'ready_for_pickup',
            'courier_name' => 'Bazaario Hyperlocal Fleet #BLR-44',
        ]);
        $res2->assertSessionHas('success');
        $this->assertEquals('ready_for_pickup', $order->fresh()->status);
        $this->assertEquals('Bazaario Hyperlocal Fleet #BLR-44', $order->fresh()->courier_name);

        // Step 3: ready_for_pickup -> fulfilled via handover/fulfill endpoint
        $res3 = $this->actingAs($seller, 'seller')->post(route('seller.orders.handover', $order), [
            'courier_name'     => 'Bazaario Hyperlocal Fleet #BLR-44',
            'courier_bay'      => 'Bay 12',
            'courier_verified' => 1,
        ]);
        $res3->assertSessionHas('success');

        $fresh = $order->fresh();
        $this->assertEquals('fulfilled', $fresh->status);
        $this->assertNotNull($fresh->delivered_at);
        $this->assertNotNull($fresh->handover_confirmed_at);

        // Payout created
        $payout = Payout::where('seller_order_id', $order->id)->first();
        $this->assertNotNull($payout);
        $this->assertEquals(3000.00, (float) $payout->gross_amount);
        $this->assertEquals(300.00, (float) $payout->commission_amount);
        $this->assertEquals(45.00, (float) $payout->apmc_cess);
        $this->assertEquals(2655.00, (float) $payout->net_amount);
    }

    /**
     * Challenge 3.9: Validation rejects arbitrary, malicious, or non-existent status tokens.
     */
    public function test_challenge_validation_rejects_arbitrary_status_values(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 1000.00, 'SO-MAL-01', 'processing');

        $maliciousStatuses = [
            'arbitrary_status',
            'refunded_without_escrow',
            '<script>alert("xss")</script>',
            "'; DROP TABLE seller_orders; --",
            '',
        ];

        foreach ($maliciousStatuses as $badStatus) {
            $response = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
                'status' => $badStatus,
            ]);

            // Validation must fail (session has errors or redirect)
            $response->assertSessionHasErrors('status');
            $this->assertEquals('processing', $order->fresh()->status);
        }
    }

    /**
     * Challenge 3.10: Cancellation rules:
     * Orders in placed or processing can be cancelled before dispatch,
     * but fulfilled orders can NEVER be cancelled.
     */
    public function test_challenge_cancellation_allowed_pre_fulfillment_but_strictly_blocked_post_fulfillment(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        // 1. Placed order can be cancelled
        $placedOrder = $this->createSellerOrderRecord($seller, $buyer, 1000.00, 'SO-CAN-01', 'placed');
        $resPlaced = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $placedOrder), [
            'status' => 'cancelled',
        ]);
        $this->assertEquals('cancelled', $placedOrder->fresh()->status);

        // 2. Processing order can be cancelled
        $processingOrder = $this->createSellerOrderRecord($seller, $buyer, 1200.00, 'SO-CAN-02', 'processing');
        $resProc = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $processingOrder), [
            'status' => 'cancelled',
        ]);
        $this->assertEquals('cancelled', $processingOrder->fresh()->status);

        // 3. Fulfilled order CANNOT be cancelled
        $fulfilledOrder = $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-CAN-03', 'fulfilled');
        $resFulfilled = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $fulfilledOrder), [
            'status' => 'cancelled',
        ]);
        $this->assertEquals('fulfilled', $fulfilledOrder->fresh()->status);
    }

    /**
     * Challenge 3.11: JSON API IDOR returns HTTP 403 Forbidden for API/AJAX requests.
     */
    public function test_challenge_json_idor_returns_403_forbidden(): void
    {
        $sellerA = $this->createApprovedSeller();
        $sellerB = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        $orderB = $this->createSellerOrderRecord($sellerB, $buyer, 2500.00, 'SO-JSON-BETA', 'processing');

        // JSON GET
        $getRes = $this->actingAs($sellerA, 'seller')->getJson(route('seller.orders.show', $orderB));
        $getRes->assertStatus(403);

        // JSON PATCH
        $patchRes = $this->actingAs($sellerA, 'seller')->patchJson(route('seller.orders.update-status', $orderB), [
            'status' => 'ready_for_pickup',
        ]);
        $patchRes->assertStatus(403);

        // JSON POST fulfill
        $postRes = $this->actingAs($sellerA, 'seller')->postJson(route('seller.orders.fulfill', $orderB), [
            'courier_name' => 'Bazaario Fleet',
        ]);
        $postRes->assertStatus(403);
    }

    /**
     * Challenge 2.5: Order fulfillment without pre-computed payout_amount calculates APMC cess and net accurately.
     */
    public function test_challenge_order_fulfillment_without_precomputed_payout_amount_calculates_cess(): void
    {
        $seller = $this->createApprovedSeller([], ['commission_rate' => 10.00]);
        $buyer = $this->createBuyer();

        // Create order with payout_amount = 0 (uncalculated)
        $order = $this->createSellerOrderRecord($seller, $buyer, 1000.00, 'SO-ZERO-PRECOMP', 'ready_for_pickup', [
            'commission_rate'   => 10.00,
            'commission_amount' => 0.00,
            'payout_amount'     => 0.00,
        ]);

        $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $order));

        $this->assertEquals('fulfilled', $order->fresh()->status);
        $payout = Payout::where('seller_order_id', $order->id)->first();
        $this->assertNotNull($payout);

        // Exact formula: 1000 - 100 - 15 = 885
        $this->assertEquals(1000.00, (float) $payout->gross_amount);
        $this->assertEquals(100.00, (float) $payout->commission_amount);
        $this->assertEquals(15.00, (float) $payout->apmc_cess);
        $this->assertEquals(885.00, (float) $payout->net_amount);
    }

    /**
     * Challenge 2.6: Order fulfillment with fractional odd values (333.33) maintains 2-decimal rounding.
     */
    public function test_challenge_order_fulfillment_fractional_precision_with_odd_values(): void
    {
        $seller = $this->createApprovedSeller([], ['commission_rate' => 10.00]);
        $buyer = $this->createBuyer();

        $subtotal = 333.33;
        $expectedComm = round(333.33 * 0.10, 2); // 33.33
        $expectedCess = round(333.33 * 0.015, 2); // 5.00
        $expectedNet = round(333.33 - 33.33 - 5.00, 2); // 295.00

        $order = $this->createSellerOrderRecord($seller, $buyer, $subtotal, 'SO-FRAC-ODD', 'ready_for_pickup', [
            'commission_rate'   => 10.00,
            'commission_amount' => 0.00,
            'payout_amount'     => 0.00,
        ]);

        $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $order));

        $payout = Payout::where('seller_order_id', $order->id)->first();
        $this->assertNotNull($payout);
        $this->assertEquals(333.33, (float) $payout->gross_amount);
        $this->assertEquals($expectedComm, (float) $payout->commission_amount);
        $this->assertEquals($expectedCess, (float) $payout->apmc_cess);
        $this->assertEquals($expectedNet, (float) $payout->net_amount);
    }

    /**
     * Challenge 2.7: Custom commission rate overrides (e.g. 5% preferred farmer rate).
     */
    public function test_challenge_custom_commission_rate_override_calculation(): void
    {
        $seller = $this->createApprovedSeller([], ['commission_rate' => 5.00]);
        $buyer = $this->createBuyer();

        $subtotal = 1000.00;
        $expectedComm = 50.00; // 5%
        $expectedCess = 15.00; // 1.5%
        $expectedNet = 935.00; // 1000 - 50 - 15

        $order = $this->createSellerOrderRecord($seller, $buyer, $subtotal, 'SO-PREF-FARMER', 'ready_for_pickup', [
            'commission_rate'   => 5.00,
            'commission_amount' => 0.00,
            'payout_amount'     => 0.00,
        ]);

        $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $order));

        $payout = Payout::where('seller_order_id', $order->id)->first();
        $this->assertNotNull($payout);
        $this->assertEquals(1000.00, (float) $payout->gross_amount);
        $this->assertEquals($expectedComm, (float) $payout->commission_amount);
        $this->assertEquals($expectedCess, (float) $payout->apmc_cess);
        $this->assertEquals($expectedNet, (float) $payout->net_amount);
    }
}
