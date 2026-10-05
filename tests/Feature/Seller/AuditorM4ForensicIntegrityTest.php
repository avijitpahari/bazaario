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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class AuditorM4ForensicIntegrityTest
 *
 * Independent Forensic Audit Test Suite for Milestone 4 (Order Fulfillment & Payout Management).
 * Probes:
 *  1. Anti-Cheat & Calculation Integrity:
 *     - Dynamic gross, commission (10%), APMC mandi cess (1.5%), and net payout with arbitrary non-trivial numbers.
 *     - Verifies database row persistence for SellerOrder and Payout models.
 *  2. State Machine & Handover Protocol Integrity:
 *     - Handover / fulfillment genuinely creates Payout records in database with matching calculated amounts.
 *     - Linear progression enforcement (placed/pending cannot jump to fulfilled).
 *     - Terminal states immutability (fulfilled and cancelled cannot be mutated).
 *     - Multi-seller parent order completion synchronization when all sub-orders are fulfilled.
 *  3. Tenancy Isolation & Security Enforcement:
 *     - Cross-tenant order viewing, updating, and fulfillment attempts rejected with HTTP 403.
 *     - Cross-tenant payout viewing rejected with HTTP 403.
 *     - Status tab counts and queue listing strictly scoped to authenticated seller.
 *  4. Blade View Dynamic Rendering Integrity:
 *     - Ensures views render authentic model attributes, formatted currency values, and dynamic status badges without static facades.
 */
class AuditorM4ForensicIntegrityTest extends TestCase
{
    use RefreshDatabase, SellerTestHelperTrait;

    /**
     * Check 1: Dynamic Fee and APMC Cess Calculation Integrity when payout_amount is null/uncomputed.
     * Formula:
     * - Subtotal: arbitrary amount (e.g. ₹3,417.80)
     * - Commission: 10% = ₹341.78
     * - APMC Mandi Cess: 1.5% = ₹51.27
     * - Net Payout: ₹3,417.80 - ₹341.78 - ₹51.27 = ₹3,024.75
     */
    public function test_forensic_dynamic_fee_and_cess_calculation_integrity_with_uncomputed_payout(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        $subtotal = 3417.80;
        $expectedComm = round($subtotal * 0.10, 2); // 341.78
        $expectedCess = round($subtotal * 0.015, 2); // 51.27
        $expectedNet = round($subtotal - $expectedComm - $expectedCess, 2); // 3024.75

        // Create order with payout_amount = 0 to trigger dynamic calculation
        $order = $this->createSellerOrderRecord($seller, $buyer, $subtotal, 'SO-DYN-3417', 'processing', [
            'payout_amount' => 0.00,
        ]);

        // Test model accessors
        $this->assertEquals($expectedCess, $order->apmc_cess);
        $this->assertEquals($expectedNet, $order->net_payout_calculated);

        // Fulfill the order via HTTP endpoint
        $response = $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $order), [
            'courier_name' => 'Hyperlocal Fleet #EXP-99',
        ]);

        $response->assertSessionHas('success');

        // Verify database state directly
        $freshOrder = $order->fresh();
        $this->assertEquals('fulfilled', $freshOrder->status);
        $this->assertNotNull($freshOrder->handover_confirmed_at);
        $this->assertEquals('Hyperlocal Fleet #EXP-99', $freshOrder->courier_name);
        $this->assertEquals($expectedComm, (float) $freshOrder->commission_amount);
        $this->assertEquals($expectedNet, (float) $freshOrder->payout_amount);

        // Verify Payout record was genuinely created in database
        $payout = Payout::where('seller_order_id', $order->id)->first();
        $this->assertNotNull($payout, 'Payout database row must be created upon fulfillment');
        $this->assertEquals($seller->id, $payout->seller_id);
        $this->assertEquals($subtotal, (float) $payout->gross_amount);
        $this->assertEquals($expectedComm, (float) $payout->commission_amount);
        $this->assertEquals($expectedCess, (float) $payout->apmc_cess);
        $this->assertEquals($expectedNet, (float) $payout->net_amount);
        $this->assertEquals('pending', $payout->status);
        $this->assertStringStartsWith('PO-', $payout->payout_reference);
    }

    /**
     * Check 2: Dynamic Fee and APMC Cess Calculation when order already has cess itemized.
     * Subtotal: ₹7,894.40
     * Commission: 10% = ₹789.44
     * Cess: 1.5% = ₹118.42
     * Net: ₹6,986.54
     */
    public function test_forensic_dynamic_calculation_with_precalculated_cess(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        $subtotal = 7894.40;
        $expectedComm = 789.44;
        $expectedCess = 118.42;
        $expectedNet = 6986.54;

        $order = $this->createSellerOrderRecord($seller, $buyer, $subtotal, 'SO-ARBITRARY-7894', 'processing', [
            'commission_amount' => $expectedComm,
            'payout_amount'     => $expectedNet,
        ]);

        $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $order));

        $payout = Payout::where('seller_order_id', $order->id)->firstOrFail();
        $this->assertEquals($expectedComm, (float) $payout->commission_amount);
        $this->assertEquals($expectedCess, (float) $payout->apmc_cess);
        $this->assertEquals($expectedNet, (float) $payout->net_amount);

        // Verify view renders formatted net amount
        $viewResp = $this->actingAs($seller, 'seller')->get(route('seller.payouts.show', $payout));
        $viewResp->assertStatus(200);
        $viewResp->assertSee('7,894.40');
        $viewResp->assertSee('789.44');
        $viewResp->assertSee('118.42');
        $viewResp->assertSee('6,986.54');
    }

    /**
     * Check 3: Handover endpoint (courier physical custody transfer) is an authentic mutation alias.
     */
    public function test_forensic_handover_endpoint_authentically_mutates_database(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-HANDOVER-MUT', 'ready_for_pickup');

        $response = $this->actingAs($seller, 'seller')->post(route('seller.orders.handover', $order), [
            'courier_name' => 'BlueDart Hyperlocal #BD-701',
        ]);

        $response->assertSessionHas('success');
        $fresh = $order->fresh();
        $this->assertEquals('fulfilled', $fresh->status);
        $this->assertEquals('BlueDart Hyperlocal #BD-701', $fresh->courier_name);
        $this->assertNotNull($fresh->handover_confirmed_at);
        $this->assertDatabaseHas('payouts', [
            'seller_order_id' => $order->id,
            'seller_id'       => $seller->id,
            'status'          => 'pending',
        ]);
    }

    /**
     * Check 4: Parent Order Status Synchronization Integrity.
     * When a parent order has multiple sub-orders, parent order transitions to 'completed'
     * only when ALL sub-orders reach fulfilled/delivered status.
     */
    public function test_forensic_parent_order_synchronization_integrity(): void
    {
        $sellerA = $this->createApprovedSeller(['email' => 'sellerA_sync@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['email' => 'sellerB_sync@bazaario.com']);
        $buyer = $this->createBuyer();

        $pA = $this->createProductRecord($sellerA, 'Seller A Sync Item', 600.00);
        $pB = $this->createProductRecord($sellerB, 'Seller B Sync Item', 400.00);

        $multi = $this->createMultiSellerOrder($buyer, [
            ['seller' => $sellerA, 'items' => [['product' => $pA, 'qty' => 1]]],
            ['seller' => $sellerB, 'items' => [['product' => $pB, 'qty' => 1]]],
        ]);

        $parent = $multi['parent_order'];
        $soA = $multi['seller_orders']->firstWhere('seller_id', $sellerA->id);
        $soB = $multi['seller_orders']->firstWhere('seller_id', $sellerB->id);

        $soA->update(['status' => 'ready_for_pickup']);
        $soB->update(['status' => 'ready_for_pickup']);

        // Parent starts at 'processing'
        $this->assertEquals('processing', $parent->fresh()->order_status);

        // Seller A fulfills sub-order
        $this->actingAs($sellerA, 'seller')->post(route('seller.orders.fulfill', $soA));
        $this->assertEquals('fulfilled', $soA->fresh()->status);
        // Parent MUST remain processing because Seller B sub-order is not fulfilled yet
        $this->assertEquals('processing', $parent->fresh()->order_status);

        // Seller B fulfills sub-order
        $this->actingAs($sellerB, 'seller')->post(route('seller.orders.fulfill', $soB));
        $this->assertEquals('fulfilled', $soB->fresh()->status);
        // Now that both are fulfilled, parent MUST be 'completed'
        $this->assertEquals('completed', $parent->fresh()->order_status);
    }

    /**
     * Check 5: Multi-Tenant Boundary Security Check.
     * Verify that unauthorized cross-tenant requests return 403 and NEVER alter database rows.
     */
    public function test_forensic_tenancy_cross_tenant_manipulation_rejection_and_database_invariance(): void
    {
        $sellerVictim = $this->createApprovedSeller(['email' => 'victim@bazaario.com']);
        $sellerAttacker = $this->createApprovedSeller(['email' => 'attacker@bazaario.com']);
        $buyer = $this->createBuyer();

        $victimOrder = $this->createSellerOrderRecord($sellerVictim, $buyer, 5000.00, 'SO-VICTIM-01', 'ready_for_pickup');
        $victimPayout = $this->createPayoutRecord($sellerVictim, $victimOrder, 5000.00, 500.00, 4500.00, 'pending');

        // 1. Attacker attempts to GET victim order
        $respGetOrder = $this->actingAs($sellerAttacker, 'seller')->get(route('seller.orders.show', $victimOrder));
        $this->assertEquals(403, $respGetOrder->getStatusCode());

        // 2. Attacker attempts to mutate status of victim order
        $respPatchOrder = $this->actingAs($sellerAttacker, 'seller')->patch(route('seller.orders.update-status', $victimOrder), [
            'status' => 'cancelled',
        ]);
        $this->assertEquals(403, $respPatchOrder->getStatusCode());
        $this->assertEquals('ready_for_pickup', $victimOrder->fresh()->status);

        // 3. Attacker attempts to fulfill victim order
        $respFulfill = $this->actingAs($sellerAttacker, 'seller')->post(route('seller.orders.fulfill', $victimOrder));
        $this->assertEquals(403, $respFulfill->getStatusCode());
        $this->assertEquals('ready_for_pickup', $victimOrder->fresh()->status);

        // 4. Attacker attempts to view victim payout details
        $respGetPayout = $this->actingAs($sellerAttacker, 'seller')->get(route('seller.payouts.show', $victimPayout));
        $this->assertEquals(403, $respGetPayout->getStatusCode());
    }

    /**
     * Check 6: State Machine Invariants & Anti-Cheating Transition Rules.
     * Prevents invalid state jumps and protects terminal states from mutation.
     */
    public function test_forensic_state_machine_illegal_jump_and_terminal_immutability(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        // 1. Cannot skip: placed -> fulfilled
        $placedOrder = $this->createSellerOrderRecord($seller, $buyer, 1200.00, 'SO-SM-PLACED', 'placed');
        $respJump = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $placedOrder), [
            'status' => 'fulfilled',
        ]);
        $respJump->assertSessionHas('error');
        $this->assertEquals('placed', $placedOrder->fresh()->status);

        // 2. Cannot mutate terminal fulfilled order
        $fulfilledOrder = $this->createSellerOrderRecord($seller, $buyer, 1200.00, 'SO-SM-FULFILLED', 'fulfilled');
        $respMutateFulfilled = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $fulfilledOrder), [
            'status' => 'processing',
        ]);
        $respMutateFulfilled->assertSessionHas('error');
        $this->assertEquals('fulfilled', $fulfilledOrder->fresh()->status);

        // 3. Cannot mutate terminal cancelled order
        $cancelledOrder = $this->createSellerOrderRecord($seller, $buyer, 1200.00, 'SO-SM-CANCELLED', 'cancelled');
        $respMutateCancelled = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $cancelledOrder), [
            'status' => 'processing',
        ]);
        $respMutateCancelled->assertSessionHas('error');
        $this->assertEquals('cancelled', $cancelledOrder->fresh()->status);
    }

    /**
     * Check 7: Dynamic Live KPI Computations on Payouts Dashboard.
     * Verifies that KPI cards compute authentic database aggregations, not hardcoded mock numbers.
     */
    public function test_forensic_payout_kpi_aggregations_are_dynamically_computed(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        // Create 2 paid payouts (₹2,000 net, ₹5,000 net)
        $p1 = $this->createPayoutRecord($seller, null, 2500.00, 250.00, 2000.00, 'paid');
        $p2 = $this->createPayoutRecord($seller, null, 6000.00, 600.00, 5000.00, 'paid');
        // Create 1 processing payout (₹3,000 net)
        $p3 = $this->createPayoutRecord($seller, null, 3500.00, 350.00, 3000.00, 'processing');

        // Total Gross = 2500 + 6000 + 3500 = 12000.00
        // Commission = 250 + 600 + 350 = 1200.00
        // Total Settled (paid) = 2000 + 5000 = 7000.00
        // Pending / Processing = 3000.00

        $response = $this->actingAs($seller, 'seller')->get(route('seller.payouts.index'));
        $response->assertStatus(200);

        $kpis = $response->viewData('kpis');
        $this->assertEquals(12000.00, (float) $kpis['lifetime_revenue']);
        $this->assertEquals(1200.00, (float) $kpis['platform_commission']);
        $this->assertEquals(7000.00, (float) $kpis['total_settled']);
        $this->assertEquals(3000.00, (float) $kpis['pending_processing']);

        // Check view displays rendered values
        $response->assertSee('12,000.00');
        $response->assertSee('1,200.00');
        $response->assertSee('7,000.00');
        $response->assertSee('3,000.00');
    }
}
