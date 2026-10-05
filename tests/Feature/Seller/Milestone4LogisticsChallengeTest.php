<?php

namespace Tests\Feature\Seller;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payout;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class Milestone4LogisticsChallengeTest
 *
 * Empirical Challenge Suite for Milestone 4: Logistics, Delivery Slots, and Handover Protocols
 * (Features 27, 28, 30, 32, 33).
 *
 * Rigorously validates:
 *  1. Handover Verification Protocol:
 *     - Execution inside DB::transaction with strict rollback on failure
 *     - delivered_at and handover_confirmed_at timestamps populated
 *     - Payout record creation, status=pending, correct commission and net calculation
 *     - Courier attribution from modal input
 *     - State machine guards preventing illegal jumps from placed/pending or cancelled
 *  2. Delivery Slots:
 *     - Explicit delivery_slot persistence and display
 *     - Fallback regex parsing from parent order notes (pipe-delimited, case-insensitive)
 *     - Explicit slot precedence over notes fallback
 *     - Safe default fallback when no slot or notes exist
 *  3. Multi-Seller Parent Order Fulfillment:
 *     - Sub-order fulfillment isolation: fulfilling Seller A consignment does NOT mutate Seller B
 *     - Parent order remains processing while any seller consignment is unfulfilled
 *     - Parent order transitions to completed only when ALL sibling consignments are fulfilled
 *     - 3-seller consignment progression
 *     - Cancelled parent order cannot be overridden to completed
 */
class Milestone4LogisticsChallengeTest extends TestCase
{
    use RefreshDatabase, SellerTestHelperTrait;

    // =========================================================================
    // SECTION 1: HANDOVER VERIFICATION PROTOCOL & TRANSACTIONAL INTEGRITY
    // =========================================================================

    /**
     * Challenge 1.1: Handover modal submission via POST /seller/orders/{order}/fulfill
     * updates status to fulfilled, sets delivered_at and handover_confirmed_at timestamps,
     * updates courier_name, and creates a pending Payout record.
     */
    public function test_handover_verification_submitting_modal_updates_timestamps_and_creates_payout(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Organic Alphonso Mangoes', 450.00, 40, 'kg');

        $parentOrder = $this->createParentOrder($buyer, 1800.00);
        $sellerOrder = $this->createSellerOrderRecord($seller, $buyer, 1800.00, 'SO-HANDOVER-01', 'ready_for_pickup', [
            'parent_order'      => $parentOrder,
            'commission_rate'   => 10.00,
            'commission_amount' => 180.00,
            'payout_amount'     => 1593.00,
            'courier_name'      => null,
            'delivered_at'      => null,
            'handover_confirmed_at' => null,
        ]);
        $this->createOrderItemRecord($sellerOrder, $product, 4, 450.00);

        // Pre-condition verification
        $this->assertNull($sellerOrder->delivered_at);
        $this->assertNull($sellerOrder->handover_confirmed_at);
        $this->assertEquals(0, Payout::where('seller_order_id', $sellerOrder->id)->count());

        $customCourier = 'Express Hyperlocal Fleet #DEL-99';

        // Submit handover modal form to /seller/orders/{order}/fulfill
        $response = $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $sellerOrder), [
            'courier_bay' => $customCourier,
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        // 1. Verify seller_orders table mutations
        $freshOrder = $sellerOrder->fresh();
        $this->assertEquals('fulfilled', $freshOrder->status, 'Order status must be updated to fulfilled');
        $this->assertNotNull($freshOrder->delivered_at, 'delivered_at must be populated');
        $this->assertNotNull($freshOrder->handover_confirmed_at, 'handover_confirmed_at must be populated');
        $this->assertInstanceOf(Carbon::class, $freshOrder->delivered_at);
        $this->assertInstanceOf(Carbon::class, $freshOrder->handover_confirmed_at);
        $this->assertEquals($customCourier, $freshOrder->courier_name, 'Custom courier from modal must be saved');

        // 2. Verify Payout table creation and activation
        $payout = Payout::where('seller_order_id', $sellerOrder->id)->first();
        $this->assertNotNull($payout, 'Payout record must be created in payouts table');
        $this->assertEquals($seller->id, $payout->seller_id);
        $this->assertEquals(1800.00, (float) $payout->gross_amount);
        $this->assertEquals(180.00, (float) $payout->commission_amount);
        $this->assertEquals('pending', $payout->status, 'Payout status must be initialized to pending');
        $this->assertStringStartsWith('PO-', $payout->payout_reference, 'Payout reference must start with PO-');

        // Math verification: gross - commission - apmc_cess = net
        $expectedNet = round(1800.00 - 180.00 - (1800.00 * 0.015), 2);
        $this->assertEquals($expectedNet, (float) $payout->net_amount);
    }

    /**
     * Challenge 1.2: Handover route alias POST /seller/orders/{order}/handover behaves identically.
     */
    public function test_handover_route_alias_behaves_identically(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $parentOrder = $this->createParentOrder($buyer, 1000.00);
        $sellerOrder = $this->createSellerOrderRecord($seller, $buyer, 1000.00, 'SO-HANDOVER-ALIAS', 'ready_for_pickup', [
            'parent_order' => $parentOrder,
        ]);

        $response = $this->actingAs($seller, 'seller')->post(route('seller.orders.handover', $sellerOrder), [
            'courier_name' => 'Bazaario Electric Van #EV-12',
        ]);

        $response->assertSessionHas('success');
        $freshOrder = $sellerOrder->fresh();
        $this->assertEquals('fulfilled', $freshOrder->status);
        $this->assertNotNull($freshOrder->delivered_at);
        $this->assertNotNull($freshOrder->handover_confirmed_at);
        $this->assertNotNull(Payout::where('seller_order_id', $sellerOrder->id)->first());
    }

    /**
     * Challenge 1.3: Transaction Atomicity: If an exception occurs inside the fulfillment routine,
     * the entire transaction rolls back cleanly (no status change, no timestamps, no payout).
     */
    public function test_handover_verification_executes_inside_atomic_db_transaction_with_rollback(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $parentOrder = $this->createParentOrder($buyer, 1500.00);
        $sellerOrder = $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-ROLLBACK-TEST', 'ready_for_pickup', [
            'parent_order'          => $parentOrder,
            'delivered_at'          => null,
            'handover_confirmed_at' => null,
        ]);

        // Register a model hook on Payout to throw an exception midway through transaction
        Payout::saving(function ($payout) {
            if ($payout->seller_order_id && str_contains($payout->payout_reference ?? '', 'PO-')) {
                throw new \RuntimeException('Simulated payment gateway / ledger storage failure during transaction');
            }
        });

        try {
            $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $sellerOrder), [
                'courier_bay' => 'Bazaario Fleet Fail-Sim',
            ]);
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('Simulated payment gateway', $e->getMessage());
        }

        // Clear the model event listener
        Payout::flushEventListeners();

        // Verify clean rollback in database
        $freshOrder = $sellerOrder->fresh();
        $this->assertEquals('ready_for_pickup', $freshOrder->status, 'Order status must remain ready_for_pickup after rollback');
        $this->assertNull($freshOrder->delivered_at, 'delivered_at must remain null after rollback');
        $this->assertNull($freshOrder->handover_confirmed_at, 'handover_confirmed_at must remain null after rollback');
        $this->assertNull(Payout::where('seller_order_id', $sellerOrder->id)->first(), 'No orphaned Payout record must exist in DB');
    }

    /**
     * Challenge 1.4: Pre-condition guard: Handover cannot be performed directly on placed/pending orders.
     */
    public function test_handover_rejected_on_unprocessed_placed_orders(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 1200.00, 'SO-ILLEGAL-HANDOVER', 'placed');

        $response = $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $order), [
            'courier_bay' => 'Bazaario Fleet',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals('placed', $order->fresh()->status);
        $this->assertNull($order->fresh()->delivered_at);
        $this->assertNull(Payout::where('seller_order_id', $order->id)->first());
    }

    /**
     * Challenge 1.5: Pre-condition guard: Handover cannot be performed on cancelled orders.
     */
    public function test_handover_rejected_on_cancelled_orders(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 1200.00, 'SO-CANCELLED-HANDOVER', 'cancelled');

        $response = $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $order));

        $response->assertSessionHas('error');
        $this->assertEquals('cancelled', $order->fresh()->status);
        $this->assertNull(Payout::where('seller_order_id', $order->id)->first());
    }

    /**
     * Challenge 1.6: Terminal state guard: Handover cannot be re-executed on an already fulfilled order.
     */
    public function test_handover_rejected_on_already_fulfilled_orders(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $originalDeliveredAt = Carbon::now()->subDays(2);
        $order = $this->createSellerOrderRecord($seller, $buyer, 1200.00, 'SO-ALREADY-FULFILLED', 'fulfilled', [
            'delivered_at'          => $originalDeliveredAt,
            'handover_confirmed_at' => $originalDeliveredAt,
        ]);

        $response = $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $order));

        $response->assertSessionHas('error');
        $this->assertEquals('fulfilled', $order->fresh()->status);
        $this->assertEquals($originalDeliveredAt->toDateTimeString(), $order->fresh()->delivered_at->toDateTimeString());
    }

    /**
     * Challenge 1.7: Cross-tenant handover attempt is strictly forbidden with HTTP 403.
     */
    public function test_cross_tenant_handover_attempt_is_strictly_forbidden(): void
    {
        $sellerA = $this->createApprovedSeller(['email' => 'sellerA_h@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['email' => 'sellerB_h@bazaario.com']);
        $buyer = $this->createBuyer();

        $orderB = $this->createSellerOrderRecord($sellerB, $buyer, 3500.00, 'SO-B-LOCKED', 'ready_for_pickup');

        $response = $this->actingAs($sellerA, 'seller')->post(route('seller.orders.fulfill', $orderB));

        $this->assertTrue(in_array($response->getStatusCode(), [403, 404]));
        $this->assertEquals('ready_for_pickup', $orderB->fresh()->status);
        $this->assertNull($orderB->fresh()->delivered_at);
        $this->assertEquals(0, Payout::where('seller_order_id', $orderB->id)->count());
    }

    // =========================================================================
    // SECTION 2: DELIVERY SLOTS (PERSISTENCE, FALLBACK & PRECEDENCE)
    // =========================================================================

    /**
     * Challenge 2.1: Explicit delivery_slot in seller_orders table is returned directly and rendered in views.
     */
    public function test_explicit_delivery_slot_persistence_and_display(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $explicitSlot = 'Tomorrow, 07:00 AM – 09:00 AM Early Morning';

        $order = $this->createSellerOrderRecord($seller, $buyer, 1400.00, 'SO-EXP-SLOT', 'placed', [
            'delivery_slot' => $explicitSlot,
        ]);

        // Model attribute check
        $this->assertEquals($explicitSlot, $order->fresh()->delivery_slot);

        // View rendering check
        $response = $this->actingAs($seller, 'seller')->get(route('seller.orders.index'));
        $response->assertStatus(200);
        $response->assertSee($explicitSlot);

        $showResponse = $this->actingAs($seller, 'seller')->get(route('seller.orders.show', $order));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($explicitSlot);
    }

    /**
     * Challenge 2.2: Fallback extraction from parent Order notes when delivery_slot column is null.
     */
    public function test_fallback_delivery_slot_parsing_from_parent_order_notes(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        $parentOrder = $this->createParentOrder($buyer, 2200.00, [
            'notes' => 'Customer Note: Please leave at security | Time Slot: Today, 3:00 PM – 5:00 PM | Contact: +919876543210',
        ]);

        $order = $this->createSellerOrderRecord($seller, $buyer, 2200.00, 'SO-FALLBACK-SLOT', 'processing', [
            'parent_order'  => $parentOrder,
            'delivery_slot' => null, // Explicitly NULL in database
        ]);

        $this->assertNull(DB::table('seller_orders')->where('id', $order->id)->value('delivery_slot'));

        // Accessor extracts slot from parent notes
        $extractedSlot = $order->fresh(['order'])->delivery_slot;
        $this->assertEquals('Today, 3:00 PM – 5:00 PM', $extractedSlot);

        // View renders extracted slot
        $response = $this->actingAs($seller, 'seller')->get(route('seller.orders.index'));
        $response->assertStatus(200);
        $response->assertSee('Today, 3:00 PM – 5:00 PM');
    }

    /**
     * Challenge 2.3: Fallback extraction works with case-insensitivity and minimal notes format.
     */
    public function test_fallback_delivery_slot_parsing_case_insensitive_and_minimal(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        $parentOrder = $this->createParentOrder($buyer, 1500.00, [
            'notes' => 'time slot: Evening Express 8 PM - 10 PM',
        ]);

        $order = $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-FALLBACK-MIN', 'placed', [
            'parent_order'  => $parentOrder,
            'delivery_slot' => null,
        ]);

        $this->assertEquals('Evening Express 8 PM - 10 PM', $order->fresh(['order'])->delivery_slot);
    }

    /**
     * Challenge 2.4: Precedence: Explicit slot overrides parent notes slot if both exist.
     */
    public function test_explicit_slot_takes_precedence_over_notes_fallback(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        $parentOrder = $this->createParentOrder($buyer, 1500.00, [
            'notes' => 'Time Slot: Fallback Slot 10 AM - 12 PM',
        ]);

        $order = $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-PRECEDENCE', 'placed', [
            'parent_order'  => $parentOrder,
            'delivery_slot' => 'Explicit Preferred Slot 4 PM - 6 PM',
        ]);

        $this->assertEquals('Explicit Preferred Slot 4 PM - 6 PM', $order->fresh(['order'])->delivery_slot);
    }

    /**
     * Challenge 2.5: Graceful fallback when both delivery_slot column and notes are null/empty.
     */
    public function test_graceful_fallback_when_slot_and_notes_are_both_null(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        $parentOrder = $this->createParentOrder($buyer, 1200.00, [
            'notes' => null,
        ]);

        $order = $this->createSellerOrderRecord($seller, $buyer, 1200.00, 'SO-NULL-SLOT', 'placed', [
            'parent_order'  => $parentOrder,
            'delivery_slot' => null,
        ]);

        $this->assertNull($order->fresh(['order'])->delivery_slot);

        // Blade view renders default placeholder without failing
        $response = $this->actingAs($seller, 'seller')->get(route('seller.orders.index'));
        $response->assertStatus(200);
        $response->assertSee('Today, 4:00 PM – 6:00 PM');
    }

    // =========================================================================
    // SECTION 3: MULTI-SELLER PARENT ORDER FULFILLMENT & STATE ISOLATION
    // =========================================================================

    /**
     * Challenge 3.1: Multi-Seller Parent Order Fulfillment (2 Sellers):
     * Fulfill Seller A consignment while Seller B consignment remains processing.
     * Verify parent order is NOT marked completed prematurely.
     * Verify parent order is marked completed only after Seller B fulfills.
     */
    public function test_multi_seller_parent_order_does_not_complete_prematurely(): void
    {
        $sellerA = $this->createApprovedSeller(['name' => 'Seller Alpha', 'email' => 'alpha@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['name' => 'Seller Beta', 'email' => 'beta@bazaario.com']);
        $buyer = $this->createBuyer();

        $productA = $this->createProductRecord($sellerA, 'Alpha Organic Wheat', 60.00, 100, 'kg');
        $productB = $this->createProductRecord($sellerB, 'Beta Fresh Mustard Oil', 220.00, 50, 'litre');

        $multi = $this->createMultiSellerOrder($buyer, [
            ['seller' => $sellerA, 'items' => [['product' => $productA, 'qty' => 10]]], // ₹600.00
            ['seller' => $sellerB, 'items' => [['product' => $productB, 'qty' => 4]]],  // ₹880.00
        ]);

        $parentOrder = $multi['parent_order'];
        $sellerOrders = $multi['seller_orders'];

        $orderA = $sellerOrders->firstWhere('seller_id', $sellerA->id);
        $orderB = $sellerOrders->firstWhere('seller_id', $sellerB->id);

        // Advance both orders to processing initially
        $orderA->update(['status' => 'processing']);
        $orderB->update(['status' => 'processing']);
        $parentOrder->update(['order_status' => 'processing']);

        $this->assertEquals('processing', $parentOrder->fresh()->order_status);
        $this->assertEquals('processing', $orderA->fresh()->status);
        $this->assertEquals('processing', $orderB->fresh()->status);

        // Step 1: Seller A advances their consignment to ready_for_pickup, then fulfills
        $orderA->update(['status' => 'ready_for_pickup']);
        $responseA = $this->actingAs($sellerA, 'seller')->post(route('seller.orders.fulfill', $orderA), [
            'courier_bay' => 'Fleet Alpha Van',
        ]);
        $responseA->assertSessionHas('success');

        // Verify Seller A consignment is fulfilled
        $this->assertEquals('fulfilled', $orderA->fresh()->status);
        $this->assertNotNull($orderA->fresh()->delivered_at);
        $this->assertNotNull($orderA->fresh()->handover_confirmed_at);
        $this->assertEquals(1, Payout::where('seller_order_id', $orderA->id)->count());

        // CRITICAL CHECK 1: Seller B consignment is untouched
        $this->assertEquals('processing', $orderB->fresh()->status, 'Seller B consignment must remain in processing');
        $this->assertNull($orderB->fresh()->delivered_at, 'Seller B consignment must not have delivered_at');
        $this->assertEquals(0, Payout::where('seller_order_id', $orderB->id)->count(), 'No payout for Seller B yet');

        // CRITICAL CHECK 2: Parent order is NOT completed prematurely!
        $this->assertEquals(
            'processing',
            $parentOrder->fresh()->order_status,
            'Parent order must NOT be marked completed while Seller B consignment is processing'
        );

        // Step 2: Now Seller B advances their consignment to ready_for_pickup, then fulfills
        $orderB->update(['status' => 'ready_for_pickup']);
        $responseB = $this->actingAs($sellerB, 'seller')->post(route('seller.orders.fulfill', $orderB), [
            'courier_bay' => 'Fleet Beta Van',
        ]);
        $responseB->assertSessionHas('success');

        // Verify Seller B consignment is fulfilled
        $this->assertEquals('fulfilled', $orderB->fresh()->status);
        $this->assertNotNull($orderB->fresh()->delivered_at);
        $this->assertEquals(1, Payout::where('seller_order_id', $orderB->id)->count());

        // CRITICAL CHECK 3: Now that ALL sibling consignments are fulfilled, parent order MUST be completed
        $this->assertEquals(
            'completed',
            $parentOrder->fresh()->order_status,
            'Parent order MUST be marked completed when all child consignments are fulfilled'
        );
    }

    /**
     * Challenge 3.2: Multi-Seller Consignment Fulfillment with 3 Sellers:
     * Parent order remains processing when Seller 1 fulfills, remains processing when Seller 2 fulfills,
     * and completes only when Seller 3 fulfills.
     */
    public function test_three_seller_parent_order_fulfillment_progression(): void
    {
        $seller1 = $this->createApprovedSeller(['name' => 'Seller One', 'email' => 's1@bazaario.com']);
        $seller2 = $this->createApprovedSeller(['name' => 'Seller Two', 'email' => 's2@bazaario.com']);
        $seller3 = $this->createApprovedSeller(['name' => 'Seller Three', 'email' => 's3@bazaario.com']);
        $buyer = $this->createBuyer();

        $p1 = $this->createProductRecord($seller1, 'Produce 1', 100.00);
        $p2 = $this->createProductRecord($seller2, 'Produce 2', 200.00);
        $p3 = $this->createProductRecord($seller3, 'Produce 3', 300.00);

        $multi = $this->createMultiSellerOrder($buyer, [
            ['seller' => $seller1, 'items' => [['product' => $p1, 'qty' => 1]]],
            ['seller' => $seller2, 'items' => [['product' => $p2, 'qty' => 1]]],
            ['seller' => $seller3, 'items' => [['product' => $p3, 'qty' => 1]]],
        ]);

        $parent = $multi['parent_order'];
        $so1 = $multi['seller_orders']->firstWhere('seller_id', $seller1->id);
        $so2 = $multi['seller_orders']->firstWhere('seller_id', $seller2->id);
        $so3 = $multi['seller_orders']->firstWhere('seller_id', $seller3->id);

        $so1->update(['status' => 'ready_for_pickup']);
        $so2->update(['status' => 'ready_for_pickup']);
        $so3->update(['status' => 'ready_for_pickup']);
        $parent->update(['order_status' => 'processing']);

        // Fulfill Seller 1
        $this->actingAs($seller1, 'seller')->post(route('seller.orders.fulfill', $so1));
        $this->assertEquals('fulfilled', $so1->fresh()->status);
        $this->assertEquals('processing', $parent->fresh()->order_status, 'Parent order must still be processing after 1 of 3 fulfilled');

        // Fulfill Seller 2
        $this->actingAs($seller2, 'seller')->post(route('seller.orders.fulfill', $so2));
        $this->assertEquals('fulfilled', $so2->fresh()->status);
        $this->assertEquals('processing', $parent->fresh()->order_status, 'Parent order must still be processing after 2 of 3 fulfilled');

        // Fulfill Seller 3
        $this->actingAs($seller3, 'seller')->post(route('seller.orders.fulfill', $so3));
        $this->assertEquals('fulfilled', $so3->fresh()->status);
        $this->assertEquals('completed', $parent->fresh()->order_status, 'Parent order MUST complete after all 3 fulfilled');
    }

    /**
     * Challenge 3.3: Cancelled parent order status is NEVER overwritten to completed upon sub-order fulfillment.
     */
    public function test_cancelled_parent_order_status_is_preserved_when_suborder_fulfilled(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $parentOrder = $this->createParentOrder($buyer, 1500.00, [
            'order_status' => 'cancelled',
        ]);
        $sellerOrder = $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-PARENT-CANCELLED', 'ready_for_pickup', [
            'parent_order' => $parentOrder,
        ]);

        $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $sellerOrder));

        $this->assertEquals('fulfilled', $sellerOrder->fresh()->status);
        $this->assertEquals(
            'cancelled',
            $parentOrder->fresh()->order_status,
            'Cancelled parent order must NOT be mutated to completed'
        );
    }

    /**
     * Challenge 3.4: Multi-seller sibling order with one cancelled sub-order does not mark parent completed.
     */
    public function test_multi_seller_order_with_cancelled_sibling_does_not_mark_parent_completed(): void
    {
        $sellerA = $this->createApprovedSeller(['email' => 'sellerA_sib@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['email' => 'sellerB_sib@bazaario.com']);
        $buyer = $this->createBuyer();

        $pA = $this->createProductRecord($sellerA, 'Item A', 100.00);
        $pB = $this->createProductRecord($sellerB, 'Item B', 200.00);

        $multi = $this->createMultiSellerOrder($buyer, [
            ['seller' => $sellerA, 'items' => [['product' => $pA, 'qty' => 1]]],
            ['seller' => $sellerB, 'items' => [['product' => $pB, 'qty' => 1]]],
        ]);

        $parent = $multi['parent_order'];
        $soA = $multi['seller_orders']->firstWhere('seller_id', $sellerA->id);
        $soB = $multi['seller_orders']->firstWhere('seller_id', $sellerB->id);

        $parent->update(['order_status' => 'processing']);
        $soA->update(['status' => 'cancelled']);
        $soB->update(['status' => 'ready_for_pickup']);

        // Seller B fulfills their sub-order
        $this->actingAs($sellerB, 'seller')->post(route('seller.orders.fulfill', $soB));

        $this->assertEquals('fulfilled', $soB->fresh()->status);
        $this->assertEquals('cancelled', $soA->fresh()->status);
        $this->assertNotEquals(
            'completed',
            $parent->fresh()->order_status,
            'Parent order must NOT be marked completed when a sibling order is cancelled'
        );
    }

    /**
     * Challenge 1.8: Handover JSON request returns structured JSON with success flag and loaded payout.
     */
    public function test_handover_json_request_returns_structured_payload(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 2500.00, 'SO-JSON-HANDOVER', 'ready_for_pickup');

        $response = $this->actingAs($seller, 'seller')->postJson(route('seller.orders.fulfill', $order), [
            'courier_name' => 'Bazaario Rapid JSON Fleet #01',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonPath('order.status', 'fulfilled');
        $response->assertJsonPath('order.courier_name', 'Bazaario Rapid JSON Fleet #01');
        $this->assertNotNull($response->json('order.delivered_at'));
        $this->assertNotNull($response->json('order.handover_confirmed_at'));
        $this->assertNotNull($response->json('order.payout'));
        $this->assertEquals(2500.00, (float) $response->json('order.payout.gross_amount'));
    }

    /**
     * Challenge 1.9: Payout creation is idempotent when a Payout record already exists.
     */
    public function test_handover_payout_generation_is_idempotent(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 3000.00, 'SO-IDEMPOTENT-01', 'ready_for_pickup');

        // Pre-create payout record in pending state
        $existingPayout = $this->createPayoutRecord($seller, $order, 3000.00, 300.00, 2655.00, 'pending', [
            'payout_reference' => 'PO-ORIGINAL-999',
        ]);

        $this->assertEquals(1, Payout::where('seller_order_id', $order->id)->count());

        // Perform handover
        $response = $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $order));
        $response->assertSessionHas('success');

        // Verify count is still exactly 1 (not duplicated)
        $this->assertEquals(1, Payout::where('seller_order_id', $order->id)->count());
        $freshPayout = Payout::where('seller_order_id', $order->id)->first();
        $this->assertEquals($existingPayout->id, $freshPayout->id);
        $this->assertEquals('PO-ORIGINAL-999', $freshPayout->payout_reference);
    }

    /**
     * Challenge 2.6: Delivery slot parsing handles unicode emojis and multiple pipe delimiters gracefully.
     */
    public function test_delivery_slot_parsing_handles_emojis_and_multiple_pipes(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        $parentOrder = $this->createParentOrder($buyer, 1800.00, [
            'notes' => 'Call before arrival | Time Slot: 🌅 Early Bird 06:00 AM – 08:00 AM 🚚 | Gate code: #4092 | Handle with care',
        ]);

        $order = $this->createSellerOrderRecord($seller, $buyer, 1800.00, 'SO-EMOJI-SLOT', 'placed', [
            'parent_order'  => $parentOrder,
            'delivery_slot' => null,
        ]);

        $this->assertEquals('🌅 Early Bird 06:00 AM – 08:00 AM 🚚', $order->fresh(['order'])->delivery_slot);
    }

    /**
     * Challenge 3.5: Multi-seller financial and payout isolation upon fulfillment.
     */
    public function test_multi_seller_financial_and_payout_isolation_upon_fulfillment(): void
    {
        $sellerA = $this->createApprovedSeller(['name' => 'Seller A Finance', 'email' => 'sellerA_fin@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['name' => 'Seller B Finance', 'email' => 'sellerB_fin@bazaario.com']);
        $buyer = $this->createBuyer();

        $pA = $this->createProductRecord($sellerA, 'Premium Mangoes', 500.00);
        $pB = $this->createProductRecord($sellerB, 'Basmati Rice', 250.00);

        $multi = $this->createMultiSellerOrder($buyer, [
            ['seller' => $sellerA, 'items' => [['product' => $pA, 'qty' => 4]]], // ₹2000
            ['seller' => $sellerB, 'items' => [['product' => $pB, 'qty' => 4]]], // ₹1000
        ]);

        $soA = $multi['seller_orders']->firstWhere('seller_id', $sellerA->id);
        $soB = $multi['seller_orders']->firstWhere('seller_id', $sellerB->id);

        $soA->update(['status' => 'ready_for_pickup']);
        $soB->update(['status' => 'processing']);

        // Seller A fulfills
        $this->actingAs($sellerA, 'seller')->post(route('seller.orders.fulfill', $soA));

        // Seller A's payout exists
        $payoutA = Payout::where('seller_order_id', $soA->id)->first();
        $this->assertNotNull($payoutA);
        $this->assertEquals($sellerA->id, $payoutA->seller_id);

        // Seller B's payout does NOT exist
        $payoutB = Payout::where('seller_order_id', $soB->id)->first();
        $this->assertNull($payoutB);

        // Seller A visits payouts workspace - sees payoutA
        $respA = $this->actingAs($sellerA, 'seller')->get(route('seller.payouts.index'));
        $respA->assertStatus(200);
        $respA->assertSee($payoutA->payout_reference);

        // Seller B visits payouts workspace - does NOT see payoutA
        $respB = $this->actingAs($sellerB, 'seller')->get(route('seller.payouts.index'));
        $respB->assertStatus(200);
        $respB->assertDontSee($payoutA->payout_reference);

        // Seller B cannot view Seller A's payout
        $respCross = $this->actingAs($sellerB, 'seller')->get(route('seller.payouts.show', $payoutA));
        $this->assertTrue(in_array($respCross->getStatusCode(), [403, 404]));
    }

    /**
     * Challenge 4.1: Custom commission rate (15%) and APMC cess (1.5%) precision on handover fulfillment when uncomputed.
     */
    public function test_custom_commission_rate_and_apmc_cess_deduction_precision(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        // 15% custom commission rate on ₹3,450.50 with initial payout_amount = 0.00
        $subtotal = 3450.50;
        $order = $this->createSellerOrderRecord($seller, $buyer, $subtotal, 'SO-CUSTOM-COMM', 'ready_for_pickup', [
            'commission_rate' => 15.00,
            'payout_amount'   => 0.00,
        ]);

        $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $order));

        $payout = Payout::where('seller_order_id', $order->id)->first();
        $this->assertNotNull($payout);

        $expectedCommission = round(3450.50 * 0.15, 2); // 517.58
        $expectedApmc = round(3450.50 * 0.015, 2);      // 51.76
        $expectedNet = round(3450.50 - $expectedCommission - $expectedApmc, 2); // 2881.16

        $this->assertEquals($subtotal, (float) $payout->gross_amount);
        $this->assertEquals($expectedCommission, (float) $payout->commission_amount);
        $this->assertEquals($expectedApmc, (float) $payout->apmc_cess);
        $this->assertEquals($expectedNet, (float) $payout->net_amount);
        $this->assertEquals($expectedNet, (float) $order->fresh()->payout_amount);
    }

    /**
     * Challenge 4.2: Handover fulfillment respects pre-computed payout_amount on SellerOrder.
     */
    public function test_handover_fulfillment_respects_precomputed_payout_amount(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        $subtotal = 2000.00;
        $precomputedNet = 1770.00; // 2000 - 200 (10%) - 30 (1.5% cess) = 1770
        $order = $this->createSellerOrderRecord($seller, $buyer, $subtotal, 'SO-PRECOMPUTED', 'ready_for_pickup', [
            'commission_rate'   => 10.00,
            'commission_amount' => 200.00,
            'payout_amount'     => $precomputedNet,
        ]);

        $this->actingAs($seller, 'seller')->post(route('seller.orders.fulfill', $order));

        $payout = Payout::where('seller_order_id', $order->id)->first();
        $this->assertNotNull($payout);
        $this->assertEquals($precomputedNet, (float) $payout->net_amount);
        $this->assertEquals(30.00, (float) $payout->apmc_cess);
    }
}

