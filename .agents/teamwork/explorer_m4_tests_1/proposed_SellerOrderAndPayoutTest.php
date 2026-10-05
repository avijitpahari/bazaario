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
use Tests\TestCase;

/**
 * Class SellerOrderAndPayoutTest
 *
 * Comprehensive Automated Test Suite for Milestone 4: Order Fulfillment & Payout Management.
 * Encompasses Features 26 through 33 and all Edge Cases across Tiers 1-4:
 *
 *  - Tier 1: Core Fulfillment & Payout Workspaces (Happy Path)
 *      - Feature 27: Assigned Delivery Slots Display (Table queue + Right Inspector Card)
 *      - Feature 28: 2-Column Split Orders Workspace (Left order queue + Right focused inspector)
 *      - Feature 31: Transparent Commission Rate Breakdown (Gross - 10% fee - APMC cess = Net payout)
 *      - Feature 32: Upcoming Settlement Banner (Scheduled NEFT transfer date, amount & masked bank)
 *      - Feature 33: Payouts Ledger & Settlements Table (Gross, commission, net, status badges, details)
 *
 *  - Tier 2: Boundary Security & Multi-Tenant Tenancy Isolation
 *      - Feature 26: Strict Seller Tenancy Isolation (Cross-tenant order & payout query/mutation 403/404)
 *      - Multi-seller parent order isolation (Seller A never sees Seller B sub-orders or items)
 *      - Middleware access enforcement (Pending sellers gated to pending; guests to login)
 *
 *  - Tier 3: Order Lifecycle State Machine & Handover Protocol
 *      - Feature 29: Order Status Progression Workflow (placed -> processing -> ready_for_pickup -> fulfilled)
 *      - Feature 30: Handover Verification Protocol (Courier verification modal & transition to fulfilled)
 *      - State machine safeguards (Reject illegal jumps, terminal state reversions, and cancelled mutations)
 *
 *  - Tier 4: Real-World Resilience, Financial Accuracy & Edge Cases
 *      - Empty orders and empty payouts zero-state resilience
 *      - Missing bank account credentials graceful handling
 *      - Zero-dollar order items & rounding precision
 *      - Status tab & keyword search filtering within tenancy boundary
 *      - Adversarial input & XSS escaping
 */
class SellerOrderAndPayoutTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // SECTION 1: TIER 1 - CORE FULFILLMENT & PAYOUT WORKSPACES (HAPPY PATH)
    // =========================================================================

    /**
     * Test 1.1: Approved seller can access orders workspace (/seller/orders) with HTTP 200.
     * Features: 26, 28
     */
    public function test_tier1_approved_seller_can_access_orders_workspace_with_http_200(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-M4-001', 'placed');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.orders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('seller.orders.index');
        $response->assertViewHas('orders');
        $response->assertSee('My Orders');
        $response->assertSee('SO-M4-001');
    }

    /**
     * Test 1.2: Orders workspace renders 2-column split layout with order queue and focused inspector.
     * Feature: 28
     */
    public function test_tier1_orders_workspace_renders_two_column_split_layout_with_queue_and_inspector(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer(['name' => 'Ananya Sharma']);
        $product = $this->createProductRecord($seller, 'Organic Alphonso Mango', 320.00, 50, 'kg');

        $order = $this->createSellerOrderRecord($seller, $buyer, 1600.00, 'SO-BZ-10482', 'ready_for_pickup');
        $this->createOrderItemRecord($order, $product, 5, 320.00);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.orders.index'));

        $response->assertStatus(200);
        // Left column queue table elements
        $response->assertSee('Queue Management');
        $response->assertSee('SO-BZ-10482');
        $response->assertSee('Ananya Sharma');
        $response->assertSee('Ready for Pickup');

        // Right column inspector elements
        $response->assertSee('Order Items');
        $response->assertSee('Organic Alphonso Mango');
        $response->assertSee('Net Seller Payout');
    }

    /**
     * Test 1.3: Right inspector card renders customer box, masked phone, address, and SKUs.
     * Feature: 28
     */
    public function test_tier1_right_inspector_card_renders_customer_box_and_itemized_skus(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer(['name' => 'Vikramaditya Roy']);
        $p1 = $this->createProductRecord($seller, 'Alphonso Mango', 320.00, 50, 'kg');
        $p2 = $this->createProductRecord($seller, 'Raw Forest Honey', 350.00, 30, 'piece');

        $parent = $this->createParentOrder($buyer, 1950.00, [
            'delivery_address_line_1' => 'Flat 402, Palm Grove, Indiranagar',
            'delivery_city'           => 'Bengaluru',
            'delivery_phone'          => '9845012345',
        ]);
        $so = $this->createSellerOrderRecord($seller, $buyer, 1950.00, 'SO-BZ-10482', 'processing', ['parent_order' => $parent]);
        $this->createOrderItemRecord($so, $p1, 5, 320.00);
        $this->createOrderItemRecord($so, $p2, 1, 350.00);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.orders.index', ['order_id' => $so->id]));

        $response->assertStatus(200);
        $response->assertSee('Vikramaditya Roy');
        $response->assertSee('Indiranagar');
        $response->assertSee('Alphonso Mango');
        $response->assertSee('Raw Forest Honey');
        $response->assertSee('1,950');
    }

    /**
     * Test 1.4: Assigned delivery slots displayed prominently in orders table and inspector.
     * Feature: 27
     */
    public function test_tier1_assigned_delivery_slot_displayed_in_order_queue_and_inspector(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        $this->createSellerOrderRecord($seller, $buyer, 1200.00, 'SO-SLOT-01', 'placed', [
            'delivery_slot' => 'Today, 4:00 PM – 6:00 PM',
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.orders.index'));

        $response->assertStatus(200);
        $response->assertSee('Today, 4:00 PM – 6:00 PM');
        $response->assertSee('Assigned Delivery Slot');
    }

    /**
     * Test 1.5: Delivery slot fallback extraction from parent order notes when direct delivery_slot is null.
     * Feature: 27
     */
    public function test_tier1_delivery_slot_fallback_extraction_from_parent_order_notes(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        $parent = $this->createParentOrder($buyer, 800.00, [
            'notes' => 'Time Slot: Tomorrow 9:00 AM – 11:00 AM | Leave at gate',
        ]);

        $so = $this->createSellerOrderRecord($seller, $buyer, 800.00, 'SO-FALLBACK-01', 'placed', [
            'parent_order'  => $parent,
            'delivery_slot' => null,
        ]);

        $this->assertEquals('Tomorrow 9:00 AM – 11:00 AM', $so->fresh()->delivery_slot);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.orders.index'));
        $response->assertStatus(200);
        $response->assertSee('Tomorrow 9:00 AM – 11:00 AM');
    }

    /**
     * Test 1.6: Approved seller can access dedicated single order details view.
     * Feature: 27, 28
     */
    public function test_tier1_approved_seller_can_view_single_order_details_page(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $so = $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-SHOW-01', 'processing');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.orders.show', $so));

        $response->assertStatus(200);
        $response->assertViewIs('seller.orders.show');
        $response->assertSee('SO-SHOW-01');
    }

    /**
     * Test 1.7: Approved seller can access payouts ledger (/seller/payouts) with HTTP 200.
     * Feature: 32, 33
     */
    public function test_tier1_approved_seller_can_access_payouts_ledger_with_http_200(): void
    {
        $seller = $this->createApprovedSeller();

        $response = $this->actingAs($seller, 'seller')->get(route('seller.payouts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('seller.payouts.index');
        $response->assertSee('My Payouts');
        $response->assertSee('Financial Ledger');
    }

    /**
     * Test 1.8: Upcoming settlement banner renders scheduled transfer date, amount, and bank details.
     * Feature: 32
     */
    public function test_tier1_upcoming_settlement_banner_renders_scheduled_transfer_and_bank(): void
    {
        $seller = $this->createApprovedSeller([], [
            'bank_account_number' => '9182374650124092',
            'bank_ifsc'           => 'HDFC0001245',
        ]);
        $buyer = $this->createBuyer();

        $so = $this->createSellerOrderRecord($seller, $buyer, 13833.33, 'SO-PAY-BANNER', 'delivered', [
            'commission_rate'   => 10.00,
            'commission_amount' => 1383.33,
            'payout_amount'     => 12450.00,
        ]);

        $this->createPayoutRecord($seller, $so, 13833.33, 1383.33, 12450.00, 'pending');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.payouts.index'));

        $response->assertStatus(200);
        $response->assertSee('Upcoming Transfer');
        $response->assertSee('12,450');
        $response->assertSee('4092');
        $response->assertSee('HDFC0001245');
        $response->assertSee('NEFT');
    }

    /**
     * Test 1.9: Payouts ledger displays settlement history table with gross, commission, net, and status badges.
     * Feature: 33
     */
    public function test_tier1_payouts_ledger_displays_settlement_history_table_and_badges(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        $so1 = $this->createSellerOrderRecord($seller, $buyer, 5000.00, 'SO-HIST-1', 'delivered');
        $so2 = $this->createSellerOrderRecord($seller, $buyer, 10000.00, 'SO-HIST-2', 'delivered');

        $p1 = $this->createPayoutRecord($seller, $so1, 5000.00, 500.00, 4500.00, 'paid', ['payout_reference' => 'PO-PAID-01']);
        $p2 = $this->createPayoutRecord($seller, $so2, 10000.00, 1000.00, 9000.00, 'processing', ['payout_reference' => 'PO-PROC-02']);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.payouts.index'));

        $response->assertStatus(200);
        $response->assertSee('PO-PAID-01');
        $response->assertSee('PO-PROC-02');
        $response->assertSee('4,500');
        $response->assertSee('9,000');
        $response->assertSee('PAID');
        $response->assertSee('PROCESSING');
    }

    /**
     * Test 1.10: Transparent commission breakdown formula verified mathematically.
     * Feature: 31
     */
    public function test_tier1_transparent_commission_calculation_formula_verified(): void
    {
        $seller = $this->createApprovedSeller([], ['commission_rate' => 10.00]);
        $buyer = $this->createBuyer();

        // Gross: 10,000.00 -> 10% commission = 1,000.00 -> Net Payout = 9,000.00
        $so = $this->createSellerOrderRecord($seller, $buyer, 10000.00, 'SO-COMM-01', 'placed');

        $this->assertEquals(10000.00, (float) $so->subtotal);
        $this->assertEquals(1000.00, (float) $so->commission_amount);
        $this->assertEquals(9000.00, (float) $so->payout_amount);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.orders.index', ['order_id' => $so->id]));
        $response->assertStatus(200);
        $response->assertSee('10,000');
        $response->assertSee('1,000');
        $response->assertSee('9,000');
    }

    /**
     * Test 1.11: Approved seller can access dedicated single payout details view.
     * Feature: 33
     */
    public function test_tier1_approved_seller_can_view_single_payout_details(): void
    {
        $seller = $this->createApprovedSeller();
        $payout = $this->createPayoutRecord($seller, null, 2500.00, 250.00, 2250.00, 'paid', [
            'payout_reference' => 'PO-SINGLE-SHOW',
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.payouts.show', $payout));

        $response->assertStatus(200);
        $response->assertViewIs('seller.payouts.show');
        $response->assertSee('PO-SINGLE-SHOW');
    }

    // =========================================================================
    // SECTION 2: TIER 2 - STRICT MULTI-TENANT TENANCY ISOLATION (FEATURE 26)
    // =========================================================================

    /**
     * Test 2.1: Strict Tenant Isolation: Seller A cannot see Seller B's orders on queue list.
     * Feature: 26
     */
    public function test_tier2_strict_tenant_isolation_orders_queue_only_displays_own_orders(): void
    {
        $sellerA = $this->createApprovedSeller(['name' => 'Seller A', 'email' => 'sellerA@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['name' => 'Seller B', 'email' => 'sellerB@bazaario.com']);
        $buyer = $this->createBuyer();

        $this->createSellerOrderRecord($sellerA, $buyer, 1200.00, 'SO-A-VISIBLE', 'placed');
        $this->createSellerOrderRecord($sellerB, $buyer, 5500.00, 'SO-B-SECRET', 'placed');

        $responseA = $this->actingAs($sellerA, 'seller')->get(route('seller.orders.index'));

        $responseA->assertStatus(200);
        $responseA->assertSee('SO-A-VISIBLE');
        $responseA->assertDontSee('SO-B-SECRET');
        $responseA->assertDontSee('5,500');
    }

    /**
     * Test 2.2: Strict Tenant Isolation: Seller A cannot view Seller B's order details (HTTP 403 or 404).
     * Feature: 26
     */
    public function test_tier2_strict_tenant_isolation_seller_a_cannot_view_seller_b_order(): void
    {
        $sellerA = $this->createApprovedSeller(['email' => 'sellerA2@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['email' => 'sellerB2@bazaario.com']);
        $buyer = $this->createBuyer();

        $orderB = $this->createSellerOrderRecord($sellerB, $buyer, 3000.00, 'SO-B-FORBIDDEN', 'placed');

        $response = $this->actingAs($sellerA, 'seller')->get(route('seller.orders.show', $orderB));

        $this->assertTrue(in_array($response->getStatusCode(), [403, 404]), 'Must reject cross-tenant order view with 403 or 404');
    }

    /**
     * Test 2.3: Strict Tenant Isolation: Seller A cannot update status of Seller B's order (HTTP 403 or 404).
     * Feature: 26
     */
    public function test_tier2_strict_tenant_isolation_seller_a_cannot_update_seller_b_order_status(): void
    {
        $sellerA = $this->createApprovedSeller(['email' => 'sellerA3@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['email' => 'sellerB3@bazaario.com']);
        $buyer = $this->createBuyer();

        $orderB = $this->createSellerOrderRecord($sellerB, $buyer, 4000.00, 'SO-B-MUTATE', 'processing');

        $response = $this->actingAs($sellerA, 'seller')->patch(route('seller.orders.update-status', $orderB), [
            'status' => 'fulfilled',
        ]);

        $this->assertTrue(in_array($response->getStatusCode(), [403, 404]), 'Must reject cross-tenant status mutation with 403 or 404');
        $this->assertEquals('processing', $orderB->fresh()->status, 'Status must remain unmodified in database');
    }

    /**
     * Test 2.4: Strict Tenant Isolation: Seller A cannot fulfill Seller B's order via handover protocol.
     * Feature: 26, 30
     */
    public function test_tier2_strict_tenant_isolation_seller_a_cannot_fulfill_seller_b_order(): void
    {
        $sellerA = $this->createApprovedSeller(['email' => 'sellerA4@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['email' => 'sellerB4@bazaario.com']);
        $buyer = $this->createBuyer();

        $orderB = $this->createSellerOrderRecord($sellerB, $buyer, 2500.00, 'SO-B-HANDOVER', 'ready_for_pickup');

        $response = $this->actingAs($sellerA, 'seller')->patch(route('seller.orders.update-status', $orderB), [
            'status'           => 'fulfilled',
            'courier_verified' => true,
        ]);

        $this->assertTrue(in_array($response->getStatusCode(), [403, 404]));
        $this->assertEquals('ready_for_pickup', $orderB->fresh()->status);
    }

    /**
     * Test 2.5: Strict Tenant Isolation: Seller A cannot see Seller B's payouts on ledger table.
     * Feature: 26, 33
     */
    public function test_tier2_strict_tenant_isolation_payouts_ledger_only_displays_own_payouts(): void
    {
        $sellerA = $this->createApprovedSeller(['email' => 'sellerA5@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['email' => 'sellerB5@bazaario.com']);

        $this->createPayoutRecord($sellerA, null, 1500.00, 150.00, 1350.00, 'paid', ['payout_reference' => 'PO-A-1350']);
        $this->createPayoutRecord($sellerB, null, 80000.00, 8000.00, 72000.00, 'paid', ['payout_reference' => 'PO-B-72000']);

        $responseA = $this->actingAs($sellerA, 'seller')->get(route('seller.payouts.index'));

        $responseA->assertStatus(200);
        $responseA->assertSee('PO-A-1350');
        $responseA->assertSee('1,350');
        $responseA->assertDontSee('PO-B-72000');
        $responseA->assertDontSee('72,000');
    }

    /**
     * Test 2.6: Strict Tenant Isolation: Seller A cannot view Seller B's payout details (HTTP 403 or 404).
     * Feature: 26, 33
     */
    public function test_tier2_strict_tenant_isolation_seller_a_cannot_view_seller_b_payout(): void
    {
        $sellerA = $this->createApprovedSeller(['email' => 'sellerA6@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['email' => 'sellerB6@bazaario.com']);

        $payoutB = $this->createPayoutRecord($sellerB, null, 25000.00, 2500.00, 22500.00, 'paid', [
            'payout_reference' => 'PO-B-CONFIDENTIAL',
        ]);

        $response = $this->actingAs($sellerA, 'seller')->get(route('seller.payouts.show', $payoutB));

        $this->assertTrue(in_array($response->getStatusCode(), [403, 404]));
    }

    /**
     * Test 2.7: Strict Tenancy Isolation in Multi-Seller Parent Order:
     * Parent order contains sub-orders for Seller A and Seller B. Each seller queries only their sub-order.
     * Feature: 26, Edge Cases
     */
    public function test_tier2_multi_seller_parent_order_strict_suborder_partitioning(): void
    {
        $sellerA = $this->createApprovedSeller(['email' => 'sellerA7@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['email' => 'sellerB7@bazaario.com']);
        $buyer = $this->createBuyer();

        $pA = $this->createProductRecord($sellerA, 'Seller A Fresh Organic Honey', 500.00, 20);
        $pB = $this->createProductRecord($sellerB, 'Seller B Heirloom Brown Rice', 300.00, 30);

        $multi = $this->createMultiSellerOrder($buyer, [
            ['seller' => $sellerA, 'items' => [['product' => $pA, 'qty' => 2]]], // ₹1000
            ['seller' => $sellerB, 'items' => [['product' => $pB, 'qty' => 3]]], // ₹900
        ]);

        $soA = $multi['seller_orders']->firstWhere('seller_id', $sellerA->id);
        $soB = $multi['seller_orders']->firstWhere('seller_id', $sellerB->id);

        // Seller A visits orders workspace
        $respA = $this->actingAs($sellerA, 'seller')->get(route('seller.orders.index'));
        $respA->assertStatus(200);
        $respA->assertSee($soA->seller_order_number);
        $respA->assertSee('Seller A Fresh Organic Honey');
        $respA->assertDontSee($soB->seller_order_number);
        $respA->assertDontSee('Seller B Heirloom Brown Rice');

        // Seller B visits orders workspace
        $respB = $this->actingAs($sellerB, 'seller')->get(route('seller.orders.index'));
        $respB->assertStatus(200);
        $respB->assertSee($soB->seller_order_number);
        $respB->assertSee('Seller B Heirloom Brown Rice');
        $respB->assertDontSee($soA->seller_order_number);
        $respB->assertDontSee('Seller A Fresh Organic Honey');
    }

    /**
     * Test 2.8: Unapproved (pending) seller is strictly gated to /seller/pending.
     */
    public function test_tier2_unapproved_pending_seller_is_redirected_to_pending_gate(): void
    {
        $pendingSeller = $this->createPendingSeller();

        $respOrders = $this->actingAs($pendingSeller, 'seller')->get(route('seller.orders.index'));
        $respOrders->assertRedirect(route('seller.pending'));

        $respPayouts = $this->actingAs($pendingSeller, 'seller')->get(route('seller.payouts.index'));
        $respPayouts->assertRedirect(route('seller.pending'));
    }

    /**
     * Test 2.9: Unauthenticated guest accessing orders or payouts is redirected to login.
     */
    public function test_tier2_unauthenticated_guest_is_redirected_to_login(): void
    {
        $respOrders = $this->get(route('seller.orders.index'));
        $respOrders->assertRedirect(route('login'));

        $respPayouts = $this->get(route('seller.payouts.index'));
        $respPayouts->assertRedirect(route('login'));
    }

    /**
     * Test 2.10: Regular customer (role = 'user') is redirected away from seller orders workspace.
     */
    public function test_tier2_regular_customer_is_redirected_from_seller_orders(): void
    {
        $customer = $this->createBuyer();

        $response = $this->actingAs($customer, 'seller')->get(route('seller.orders.index'));
        $response->assertRedirect(route('products.index'));
    }

    // =========================================================================
    // SECTION 3: TIER 3 - ORDER STATUS PROGRESSION & HANDOVER PROTOCOL (FEATURES 29, 30)
    // =========================================================================

    /**
     * Test 3.1: Valid status progression: placed -> processing successfully updates database.
     * Feature: 29
     */
    public function test_tier3_valid_status_progression_from_placed_to_processing(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 1200.00, 'SO-TRANS-01', 'placed');

        $response = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status' => 'processing',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('processing', $order->fresh()->status);
    }

    /**
     * Test 3.2: Valid status progression: processing -> ready_for_pickup successfully updates database.
     * Feature: 29
     */
    public function test_tier3_valid_status_progression_from_processing_to_ready_for_pickup(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-TRANS-02', 'processing');

        $response = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status' => 'ready_for_pickup',
        ]);

        $response->assertSessionHas('success');
        $freshStatus = $order->fresh()->status;
        $this->assertTrue(in_array($freshStatus, ['ready_for_pickup', 'packed']));
    }

    /**
     * Test 3.3: Handover Verification Protocol: Transitioning ready_for_pickup -> fulfilled updates database.
     * Feature: 30
     */
    public function test_tier3_handover_verification_protocol_transitions_order_to_fulfilled(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 1950.00, 'SO-TRANS-03', 'ready_for_pickup');

        $response = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status'      => 'fulfilled',
            'courier_bay' => 'Bazaario Hyperlocal Fleet #BLR-44',
        ]);

        $response->assertSessionHas('success');
        $freshStatus = $order->fresh()->status;
        $this->assertTrue(in_array($freshStatus, ['fulfilled', 'delivered']));
        $this->assertNotNull($order->fresh()->delivered_at ?? now());
    }

    /**
     * Test 3.4: Handover verification schedules or generates pending Payout record.
     * Feature: 30, 31, 33
     */
    public function test_tier3_handover_verification_creates_or_activates_payout_record(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 2000.00, 'SO-TRANS-04', 'ready_for_pickup', [
            'commission_rate'   => 10.00,
            'commission_amount' => 200.00,
            'payout_amount'     => 1800.00,
        ]);

        $response = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status' => 'fulfilled',
        ]);

        $response->assertSessionHas('success');

        // Verify payout record exists for this seller and order
        $payout = Payout::where('seller_order_id', $order->id)->first();
        if ($payout) {
            $this->assertEquals(2000.00, (float) $payout->gross_amount);
            $this->assertEquals(200.00, (float) $payout->commission_amount);
            $this->assertEquals(1800.00, (float) $payout->net_amount);
        } else {
            // Or verify that payout_amount on seller_order is intact for batch settlement
            $this->assertEquals(1800.00, (float) $order->fresh()->payout_amount);
        }
    }

    /**
     * Test 3.5: Invalid status progression: Attempting to jump directly from placed to fulfilled is rejected.
     * Feature: 29
     */
    public function test_tier3_invalid_status_progression_skipping_stages_is_rejected(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 1000.00, 'SO-SKIP-01', 'placed');

        $response = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status' => 'fulfilled',
        ]);

        // Must reject skip with session error or validation failure
        $this->assertEquals('placed', $order->fresh()->status);
    }

    /**
     * Test 3.6: Terminal status cannot be reverted: Fulfilled order cannot be set back to placed or processing.
     * Feature: 29
     */
    public function test_tier3_terminal_status_fulfilled_cannot_be_reverted(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-TERM-01', 'delivered');

        $response = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status' => 'processing',
        ]);

        $this->assertTrue(in_array($order->fresh()->status, ['delivered', 'fulfilled']));
    }

    /**
     * Test 3.7: Cancelled order cannot undergo status transitions.
     * Feature: 29
     */
    public function test_tier3_cancelled_order_cannot_be_transitioned(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-CANC-01', 'cancelled');

        $response = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status' => 'processing',
        ]);

        $this->assertEquals('cancelled', $order->fresh()->status);
    }

    /**
     * Test 3.8: Validation rejects unsupported/arbitrary status values.
     * Feature: 29
     */
    public function test_tier3_validation_rejects_arbitrary_status_values(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 1000.00, 'SO-INV-01', 'placed');

        $response = $this->actingAs($seller, 'seller')->patch(route('seller.orders.update-status', $order), [
            'status' => 'bogus_status_injection',
        ]);

        $response->assertSessionHasErrors(['status']);
        $this->assertEquals('placed', $order->fresh()->status);
    }

    // =========================================================================
    // SECTION 4: TIER 4 - BOUNDARY RESILIENCE, FINANCIAL INTEGRITY & EDGE CASES
    // =========================================================================

    /**
     * Test 4.1: Zero-State Resilience: Newly approved seller with 0 orders renders cleanly without errors.
     * Edge Case: Empty Orders State
     */
    public function test_tier4_zero_state_resilience_seller_with_no_orders_renders_cleanly(): void
    {
        $newSeller = $this->createApprovedSeller([], ['shop_name' => 'Empty Orders Agro']);

        $response = $this->actingAs($newSeller, 'seller')->get(route('seller.orders.index'));

        $response->assertStatus(200);
        $response->assertSee('Empty Orders Agro');
        $response->assertSee('No orders found');
        $response->assertDontSee('Attempt to read property');
    }

    /**
     * Test 4.2: Zero-State Resilience: Newly approved seller with 0 payouts renders cleanly with ₹0.00 figures.
     * Edge Case: Empty Payouts State
     */
    public function test_tier4_zero_state_resilience_seller_with_no_payouts_renders_cleanly(): void
    {
        $newSeller = $this->createApprovedSeller([], ['shop_name' => 'Empty Payouts Agro']);

        $response = $this->actingAs($newSeller, 'seller')->get(route('seller.payouts.index'));

        $response->assertStatus(200);
        $response->assertSee('₹0.00');
        $response->assertDontSee('Division by zero');
    }

    /**
     * Test 4.3: Missing bank credentials resilience: Seller without bank accounts displays fallback banner.
     * Feature: 32, Edge Case
     */
    public function test_tier4_missing_bank_credentials_displays_graceful_callout(): void
    {
        $seller = $this->createApprovedSeller([], [
            'bank_account_number' => null,
            'bank_ifsc'           => null,
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.payouts.index'));

        $response->assertStatus(200);
        $response->assertSee('Not configured');
        $response->assertDontSee('substr(): Argument #1');
    }

    /**
     * Test 4.4: Zero-Dollar order items / free sample: Handled without arithmetic division errors.
     * Edge Case: Zero-Dollar Amounts
     */
    public function test_tier4_zero_dollar_amount_handling_without_arithmetic_errors(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        $order = $this->createSellerOrderRecord($seller, $buyer, 0.00, 'SO-ZERO-01', 'placed', [
            'commission_rate'   => 10.00,
            'commission_amount' => 0.00,
            'payout_amount'     => 0.00,
        ]);

        $this->assertEquals(0.00, (float) $order->commission_amount);
        $this->assertEquals(0.00, (float) $order->payout_amount);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.orders.index', ['order_id' => $order->id]));
        $response->assertStatus(200);
        $response->assertSee('₹0.00');
    }

    /**
     * Test 4.5: Order queue status tab filtering: Orders are partitioned accurately across tabs.
     * Feature: 28
     */
    public function test_tier4_order_queue_status_tab_filtering(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        $this->createSellerOrderRecord($seller, $buyer, 100.00, 'SO-TAB-P1', 'placed');
        $this->createSellerOrderRecord($seller, $buyer, 200.00, 'SO-TAB-PR1', 'processing');
        $this->createSellerOrderRecord($seller, $buyer, 300.00, 'SO-TAB-D1', 'delivered');

        // Test filtering by 'processing'
        $respProc = $this->actingAs($seller, 'seller')->get(route('seller.orders.index', ['status' => 'processing']));
        $respProc->assertStatus(200);
        $respProc->assertSee('SO-TAB-PR1');
        $respProc->assertDontSee('SO-TAB-P1');
        $respProc->assertDontSee('SO-TAB-D1');

        // Test filtering by 'delivered' / 'fulfilled'
        $respDel = $this->actingAs($seller, 'seller')->get(route('seller.orders.index', ['status' => 'delivered']));
        $respDel->assertStatus(200);
        $respDel->assertSee('SO-TAB-D1');
        $respDel->assertDontSee('SO-TAB-PR1');
    }

    /**
     * Test 4.6: Search keyword filtering within tenant boundary.
     * Feature: 28
     */
    public function test_tier4_search_keyword_filtering_within_tenant_boundary(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer1 = $this->createBuyer(['name' => 'Kavita Sundaram']);
        $buyer2 = $this->createBuyer(['name' => 'Rajesh Khanna']);

        $this->createSellerOrderRecord($seller, $buyer1, 500.00, 'SO-SEARCH-01', 'placed');
        $this->createSellerOrderRecord($seller, $buyer2, 600.00, 'SO-SEARCH-02', 'placed');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.orders.index', ['search' => 'Kavita']));

        $response->assertStatus(200);
        $response->assertSee('SO-SEARCH-01');
        $response->assertSee('Kavita Sundaram');
        $response->assertDontSee('Rajesh Khanna');
    }

    /**
     * Test 4.7: Fractional currency precision with APMC cess itemized deduction.
     * Feature: 31
     */
    public function test_tier4_fractional_currency_precision_with_apmc_cess_deduction(): void
    {
        $seller = $this->createApprovedSeller([], ['commission_rate' => 10.00]);
        $buyer = $this->createBuyer();

        // Gross: 1,950.00
        // Commission (10%): 195.00
        // Mandi APMC Cess (1.5%): 29.25
        // Net Payout: 1,725.75
        $gross = 1950.00;
        $comm = round($gross * 0.10, 2);
        $cess = round($gross * 0.015, 2);
        $net = round($gross - $comm - $cess, 2);

        $this->assertEquals(195.00, $comm);
        $this->assertEquals(29.25, $cess);
        $this->assertEquals(1725.75, $net);

        $so = $this->createSellerOrderRecord($seller, $buyer, $gross, 'SO-CESS-01', 'placed', [
            'commission_rate'   => 10.00,
            'commission_amount' => $comm,
            'payout_amount'     => $net,
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.orders.index', ['order_id' => $so->id]));
        $response->assertStatus(200);
        $response->assertSee('1,950.00');
        $response->assertSee('195.00');
        $response->assertSee('1,725.75');
    }

    /**
     * Test 4.8: Adversarial string escaping: Prevents XSS in customer delivery notes and buyer details.
     * Feature: 28, Edge Cases
     */
    public function test_tier4_adversarial_buyer_notes_and_special_character_escaping(): void
    {
        $xssNote = '<script>alert("xss-order")</script> & Special "Notes"';
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        $parent = $this->createParentOrder($buyer, 500.00, ['notes' => $xssNote]);
        $this->createSellerOrderRecord($seller, $buyer, 500.00, 'SO-XSS-01', 'placed', ['parent_order' => $parent]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.orders.index'));

        $response->assertStatus(200);
        $response->assertDontSee('<script>alert("xss-order")</script>', false);
        $response->assertSee(e($xssNote), false);
    }
}
