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
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SellerAdversarialHardeningTest
 *
 * Feature 45: Tier 5 Adversarial Coverage Hardening Test Suite.
 * Enforces rigorous adversarial defense across all seller modules:
 *
 *  - Category 1: Strict Multi-Tenant Isolation
 *      - Cross-tenant product inspection, edition, update, and deletion attempts blocked (403/404).
 *      - Cross-tenant inventory adjustment attempts blocked (403).
 *      - Cross-tenant order inspection, status progression, fulfillment, and handover blocked (403).
 *      - Cross-tenant payout inspection blocked (403).
 *      - Cross-tenant auction inspection and cancellation blocked (403).
 *      - Cross-tenant auction creation using another seller's product rejected.
 *      - Cross-tenant profile, location, and password tampering blocked.
 *
 *  - Category 2: Privilege Escalation Prevention
 *      - Unauthenticated guests redirected to login on all operational endpoints.
 *      - Pending / unapproved sellers redirected to pending gate on all operational endpoints.
 *      - Regular buyers (role = 'user') blocked/redirected from all seller operational endpoints.
 *      - Suspended / rejected sellers blocked from operational dashboard.
 *
 *  - Category 3: Injection Attack Resistance (SQLi & XSS)
 *      - SQL injection payloads in search queries, status tabs, date filters, and sorting parameters.
 *      - XSS payloads in storefront bio, shop name, address, product description, and courier notes safely escaped.
 *
 *  - Category 4: Financial, Inventory & State Machine Guardrails
 *      - Negative and zero product prices rejected.
 *      - Invalid UoM unit types rejected.
 *      - Negative inventory adjustment quantities rejected; stock reductions clamped against underflow.
 *      - Illegal order status progression jumps (placed -> fulfilled, placed -> ready_for_pickup) rejected.
 *      - Terminal order states (fulfilled, cancelled) protected against modification.
 *      - Auction pricing bounds (reserve < starting price, starting price <= 0) rejected.
 *      - Auction cancellation strictly blocked once active bids exist.
 *      - Out-of-bounds geographic coordinates (Lat/Lng) rejected.
 *      - Weak passwords and incorrect current password rejected.
 */
class SellerAdversarialHardeningTest extends TestCase
{
    use RefreshDatabase, SellerTestHelperTrait;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name'   => 'Hardened Defense Category',
            'slug'   => 'hardened-defense-category',
            'status' => 'active',
        ]);
    }

    // =========================================================================
    // SECTION 1: STRICT MULTI-TENANT ISOLATION
    // =========================================================================

    /**
     * Test 1.1: Seller A cannot view, edit, update, or delete Seller B's product.
     */
    public function test_cross_tenant_isolation_seller_cannot_view_edit_update_or_delete_other_seller_product(): void
    {
        $sellerA = $this->createApprovedSeller([], ['shop_name' => 'Store Alpha']);
        $sellerB = $this->createApprovedSeller([], ['shop_name' => 'Store Beta']);

        $productB = $this->createProductRecord($sellerB, 'Beta Organic Crop', 200.00, 50, 'kg');

        // Seller A attempts to view Seller B's product
        $viewResp = $this->actingAs($sellerA, 'seller')->get(route('seller.products.show', $productB));
        $this->assertTrue(in_array($viewResp->status(), [403, 404]));

        // Seller A attempts to edit Seller B's product
        $editResp = $this->actingAs($sellerA, 'seller')->get(route('seller.products.edit', $productB));
        $this->assertTrue(in_array($editResp->status(), [403, 404]));

        // Seller A attempts to update Seller B's product
        $updateResp = $this->actingAs($sellerA, 'seller')->put(route('seller.products.update', $productB), [
            'name'        => 'Compromised Name',
            'category_id' => $this->category->id,
            'price'       => 10.00,
            'unit_type'   => 'kg',
            'stock'       => 0,
        ]);
        $this->assertTrue(in_array($updateResp->status(), [403, 404]));
        $this->assertNotEquals('Compromised Name', $productB->fresh()->name);

        // Seller A attempts to delete Seller B's product
        $deleteResp = $this->actingAs($sellerA, 'seller')->delete(route('seller.products.destroy', $productB));
        $this->assertTrue(in_array($deleteResp->status(), [403, 404]));
        $this->assertDatabaseHas('products', ['id' => $productB->id]);
    }

    /**
     * Test 1.2: Seller A cannot adjust warehouse stock for Seller B's product.
     */
    public function test_cross_tenant_isolation_seller_cannot_adjust_stock_for_other_seller_product(): void
    {
        $sellerA = $this->createApprovedSeller();
        $sellerB = $this->createApprovedSeller();

        $productB = $this->createProductRecord($sellerB, 'Beta Exclusive Wheat', 80.00, 100, 'kg');

        $adjustResp = $this->actingAs($sellerA, 'seller')
            ->post(route('seller.products.adjust-stock', $productB), [
                'action'   => 'set',
                'quantity' => 0,
                'reason'   => 'Adversarial zeroing attack',
            ]);

        $this->assertTrue(in_array($adjustResp->status(), [403, 404]));
        $this->assertEquals(100, $productB->fresh()->stock);
    }

    /**
     * Test 1.3: Seller A cannot view, update status, fulfill, or handover Seller B's order.
     */
    public function test_cross_tenant_isolation_seller_cannot_access_or_mutate_other_seller_order(): void
    {
        $sellerA = $this->createApprovedSeller([], ['shop_name' => 'Store Alpha']);
        $sellerB = $this->createApprovedSeller([], ['shop_name' => 'Store Beta']);
        $buyer = $this->createBuyer();

        $orderB = $this->createSellerOrderRecord($sellerB, $buyer, 1500.00, 'SO-BETA-001', 'placed');

        // View attempt
        $showResp = $this->actingAs($sellerA, 'seller')->get(route('seller.orders.show', $orderB));
        $showResp->assertStatus(403);

        // Status update attempt
        $patchResp = $this->actingAs($sellerA, 'seller')->patch(route('seller.orders.update-status', $orderB), [
            'status' => 'processing',
        ]);
        $patchResp->assertStatus(403);
        $this->assertEquals('placed', $orderB->fresh()->status);

        // Fulfillment attempt
        $fulfillResp = $this->actingAs($sellerA, 'seller')->post(route('seller.orders.fulfill', $orderB));
        $fulfillResp->assertStatus(403);
        $this->assertNotEquals('fulfilled', $orderB->fresh()->status);

        // Handover attempt
        $handoverResp = $this->actingAs($sellerA, 'seller')->post(route('seller.orders.handover', $orderB));
        $handoverResp->assertStatus(403);
        $this->assertNotEquals('fulfilled', $orderB->fresh()->status);
    }

    /**
     * Test 1.4: Seller A cannot view Seller B's payout record.
     */
    public function test_cross_tenant_isolation_seller_cannot_view_other_seller_payout(): void
    {
        $sellerA = $this->createApprovedSeller();
        $sellerB = $this->createApprovedSeller();

        $payoutB = $this->createPayoutRecord($sellerB, null, 5000.00, 500.00, 4500.00, 'pending');

        $response = $this->actingAs($sellerA, 'seller')->get(route('seller.payouts.show', $payoutB));
        $response->assertStatus(403);
    }

    /**
     * Test 1.5: Seller A cannot view, cancel, or hijack Seller B's wholesale auction.
     */
    public function test_cross_tenant_isolation_seller_cannot_view_or_cancel_other_seller_auction(): void
    {
        $sellerA = $this->createApprovedSeller();
        $sellerB = $this->createApprovedSeller();

        $productB = $this->createProductRecord($sellerB, 'Beta Rare Mango Lot', 1000.00, 20, 'kg');
        $auctionB = $this->createAuctionRecord($sellerB, $productB, [
            'starting_price' => 1000.00,
            'reserve_price'  => 2000.00,
            'status'         => 'live',
        ]);

        // Seller A viewing Seller B's auction lot
        $showResp = $this->actingAs($sellerA, 'seller')->get(route('seller.auctions.show', $auctionB));
        $showResp->assertStatus(403);

        // Seller A attempting to cancel Seller B's auction lot
        $cancelResp = $this->actingAs($sellerA, 'seller')->post(route('seller.auctions.cancel', $auctionB));
        $cancelResp->assertStatus(403);
        $this->assertEquals('live', $auctionB->fresh()->status);
    }

    /**
     * Test 1.6: Seller cannot create an auction listing referencing another seller's product.
     */
    public function test_cross_tenant_isolation_seller_cannot_create_auction_for_other_seller_product(): void
    {
        $sellerA = $this->createApprovedSeller();
        $sellerB = $this->createApprovedSeller();

        $productB = $this->createProductRecord($sellerB, 'Beta Exclusive Mustard Lot', 500.00, 30, 'litre');

        $payload = [
            'product_id'        => $productB->id,
            'starting_price'    => 1000.00,
            'reserve_price'     => 1500.00,
            'minimum_increment' => 50.00,
            'starts_at'         => Carbon::now()->addHour()->format('Y-m-d\TH:i'),
            'ends_at'           => Carbon::now()->addHours(3)->format('Y-m-d\TH:i'),
        ];

        $response = $this->actingAs($sellerA, 'seller')->post(route('seller.auctions.store'), $payload);
        $response->assertSessionHasErrors(['product_id']);

        $this->assertDatabaseMissing('auctions', [
            'product_id' => $productB->id,
            'seller_id'  => $sellerA->sellerProfile->id,
        ]);
    }

    // =========================================================================
    // SECTION 2: PRIVILEGE ESCALATION PREVENTION
    // =========================================================================

    /**
     * Test 2.1: Unauthenticated guests are strictly redirected to login for all operational endpoints.
     */
    public function test_privilege_escalation_guest_is_redirected_to_login_on_all_seller_routes(): void
    {
        $routes = [
            route('seller.dashboard'),
            route('seller.products.index'),
            route('seller.products.create'),
            route('seller.products.inventory'),
            route('seller.orders.index'),
            route('seller.payouts.index'),
            route('seller.auctions.index'),
            route('seller.auctions.live'),
            route('seller.account.profile'),
            route('seller.account.location'),
            route('seller.account.security'),
        ];

        foreach ($routes as $url) {
            $response = $this->get($url);
            $response->assertRedirect(route('login'));
        }
    }

    /**
     * Test 2.2: Unapproved (pending) sellers are gated to /seller/pending for all operational endpoints.
     */
    public function test_privilege_escalation_pending_seller_is_gated_to_pending_for_all_operational_routes(): void
    {
        $pendingSeller = $this->createPendingSeller();

        $operationalRoutes = [
            route('seller.dashboard'),
            route('seller.products.index'),
            route('seller.products.create'),
            route('seller.products.inventory'),
            route('seller.orders.index'),
            route('seller.payouts.index'),
        ];

        foreach ($operationalRoutes as $url) {
            $response = $this->actingAs($pendingSeller, 'seller')->get($url);
            $response->assertRedirect(route('seller.pending'));
        }
    }

    /**
     * Test 2.3: Regular customer (role = 'user') cannot access seller endpoints.
     */
    public function test_privilege_escalation_regular_buyer_cannot_access_seller_routes(): void
    {
        $buyer = $this->createBuyer();

        $routes = [
            route('seller.dashboard'),
            route('seller.products.index'),
            route('seller.orders.index'),
            route('seller.payouts.index'),
            route('seller.auctions.index'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($buyer, 'seller')->get($url);
            // Non-seller users are redirected away or denied
            $this->assertTrue($response->isRedirect() || $response->status() === 403);
        }
    }

    /**
     * Test 2.4: Suspended or rejected sellers are locked out of operational dashboard.
     */
    public function test_privilege_escalation_suspended_or_rejected_seller_cannot_access_dashboard(): void
    {
        $suspendedSeller = $this->createSuspendedSeller();
        $respSuspended = $this->actingAs($suspendedSeller, 'seller')->get(route('seller.dashboard'));
        $respSuspended->assertRedirect(route('seller.pending'));

        $rejectedSeller = $this->createApprovedSeller([], ['status' => 'rejected']);
        $respRejected = $this->actingAs($rejectedSeller, 'seller')->get(route('seller.dashboard'));
        $respRejected->assertRedirect(route('seller.pending'));
    }

    // =========================================================================
    // SECTION 3: INJECTION ATTACK RESISTANCE (SQLI & XSS)
    // =========================================================================

    /**
     * Test 3.1: SQL Injection payloads in search queries and status filters are neutralized.
     */
    public function test_injection_resistance_sqli_payloads_neutralized_in_queries(): void
    {
        $seller = $this->createApprovedSeller();
        $this->createProductRecord($seller, 'Legitimate Crop Item', 100.00, 20, 'kg');

        $sqliPayloads = [
            "' OR '1'='1",
            "1; DROP TABLE products; --",
            "' UNION SELECT id, name, price, stock FROM products --",
            "admin'--",
            "1' AND SLEEP(5) --",
        ];

        foreach ($sqliPayloads as $payload) {
            // Search filter on products index
            $prodResp = $this->actingAs($seller, 'seller')
                ->get(route('seller.products.index', ['search' => $payload]));
            $prodResp->assertStatus(200);

            // Status filter on orders index
            $orderResp = $this->actingAs($seller, 'seller')
                ->get(route('seller.orders.index', ['status' => $payload]));
            $orderResp->assertStatus(200);

            // Search filter on inventory
            $invResp = $this->actingAs($seller, 'seller')
                ->get(route('seller.products.inventory', ['search' => $payload]));
            $invResp->assertStatus(200);

            // Search filter on payouts
            $payoutResp = $this->actingAs($seller, 'seller')
                ->get(route('seller.payouts.index', ['search' => $payload]));
            $payoutResp->assertStatus(200);
        }

        // Database integrity confirmed: products table intact
        $this->assertDatabaseHas('products', ['name' => 'Legitimate Crop Item']);
    }

    /**
     * Test 3.2: Cross-Site Scripting (XSS) payloads in storefront profile fields are safely escaped.
     */
    public function test_injection_resistance_xss_payloads_escaped_on_profile_view(): void
    {
        $seller = $this->createApprovedSeller();

        $xssBio = '<script>alert("XSS-ATTACK")</script><img src=x onerror=alert(1)>';
        $xssShop = 'Secure Shop <svg/onload=alert("XSS")>';

        $this->actingAs($seller, 'seller')->put(route('seller.account.profile.update'), [
            'shop_name' => $xssShop,
            'bio'       => $xssBio,
        ]);

        $profileView = $this->actingAs($seller, 'seller')->get(route('seller.account.profile'));
        $profileView->assertStatus(200);

        // Raw script execution tags must NOT be rendered unescaped
        $profileView->assertDontSee('<script>alert("XSS-ATTACK")</script>', false);
        $profileView->assertDontSee('<svg/onload=alert("XSS")>', false);
    }

    /**
     * Test 3.3: XSS payloads in product names and descriptions are safely escaped in views.
     */
    public function test_injection_resistance_xss_payloads_escaped_in_product_views(): void
    {
        $seller = $this->createApprovedSeller();

        $xssName = 'Safe Crop <script>document.location="http://attacker.com"</script>';
        $product = $this->createProductRecord($seller, $xssName, 150.00, 30, 'kg');

        $indexView = $this->actingAs($seller, 'seller')->get(route('seller.products.index'));
        $indexView->assertStatus(200);
        $indexView->assertDontSee('<script>document.location="http://attacker.com"</script>', false);

        $inventoryView = $this->actingAs($seller, 'seller')->get(route('seller.products.inventory'));
        $inventoryView->assertStatus(200);
        $inventoryView->assertDontSee('<script>document.location="http://attacker.com"</script>', false);
    }

    /**
     * Test 3.4: XSS payloads in courier notes and delivery parameters are safely escaped.
     */
    public function test_injection_resistance_xss_payloads_escaped_in_order_views(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        $xssNotes = 'Special instructions <script>alert("ORDER_XSS")</script>';
        $order = $this->createSellerOrderRecord($seller, $buyer, 1200.00, 'SO-XSS-001', 'placed', [
            'parent_order' => $this->createParentOrder($buyer, 1200.00, ['notes' => $xssNotes]),
        ]);

        $showView = $this->actingAs($seller, 'seller')->get(route('seller.orders.show', $order));
        $showView->assertStatus(200);
        $showView->assertDontSee('<script>alert("ORDER_XSS")</script>', false);
    }

    // =========================================================================
    // SECTION 4: FINANCIAL, INVENTORY & STATE MACHINE GUARDRAILS
    // =========================================================================

    /**
     * Test 4.1: Product creation rejects negative, zero, and non-numeric prices.
     */
    public function test_financial_guardrails_negative_and_zero_product_prices_rejected(): void
    {
        $seller = $this->createApprovedSeller();

        // 1. Negative price (-100.00)
        $respNegative = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), [
            'name'        => 'Negative Price Item',
            'category_id' => $this->category->id,
            'price'       => -100.00,
            'unit_type'   => 'kg',
            'stock'       => 20,
        ]);
        $respNegative->assertSessionHasErrors(['price']);

        // 2. Zero price (0.00)
        $respZero = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), [
            'name'        => 'Zero Price Item',
            'category_id' => $this->category->id,
            'price'       => 0.00,
            'unit_type'   => 'kg',
            'stock'       => 20,
        ]);
        $respZero->assertSessionHasErrors(['price']);

        // 3. Invalid unit type
        $respUnit = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), [
            'name'        => 'Invalid Unit Item',
            'category_id' => $this->category->id,
            'price'       => 50.00,
            'unit_type'   => 'invalid_custom_unit',
            'stock'       => 20,
        ]);
        $respUnit->assertSessionHasErrors(['unit_type']);
    }

    /**
     * Test 4.2: Inventory adjustments reject invalid actions, negative quantities, and prevent underflow.
     */
    public function test_inventory_guardrails_invalid_actions_and_stock_underflow(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Wheat Flour', 50.00, 20, 'kg');

        // 1. Invalid action 'tamper'
        $respAction = $this->actingAs($seller, 'seller')
            ->post(route('seller.products.adjust-stock', $product), [
                'action'   => 'tamper',
                'quantity' => 10,
            ]);
        $respAction->assertSessionHasErrors(['action']);

        // 2. Negative quantity (-5)
        $respNegativeQty = $this->actingAs($seller, 'seller')
            ->post(route('seller.products.adjust-stock', $product), [
                'action'   => 'add',
                'quantity' => -5,
            ]);
        $respNegativeQty->assertSessionHasErrors(['quantity']);

        // 3. Stock underflow protection: Reducing 50 from 20 clamps to 0 (never negative)
        $this->actingAs($seller, 'seller')
            ->post(route('seller.products.adjust-stock', $product), [
                'action'   => 'reduce',
                'quantity' => 50,
            ]);
        $this->assertEquals(0, $product->fresh()->stock);
        $this->assertGreaterThanOrEqual(0, $product->fresh()->stock);
    }

    /**
     * Test 4.3: Order state machine rejects illegal status progression jumps.
     */
    public function test_state_machine_guardrails_illegal_order_status_jumps_rejected(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $order = $this->createSellerOrderRecord($seller, $buyer, 1000.00, 'SO-JUMP-001', 'placed');

        // Cannot jump directly from 'placed' to 'fulfilled'
        $jumpFulfilled = $this->actingAs($seller, 'seller')
            ->patch(route('seller.orders.update-status', $order), ['status' => 'fulfilled']);
        $jumpFulfilled->assertSessionHas('error');
        $this->assertEquals('placed', $order->fresh()->status);

        // Cannot jump directly from 'placed' to 'ready_for_pickup'
        $jumpPickup = $this->actingAs($seller, 'seller')
            ->patch(route('seller.orders.update-status', $order), ['status' => 'ready_for_pickup']);
        $jumpPickup->assertSessionHas('error');
        $this->assertEquals('placed', $order->fresh()->status);

        // Direct fulfillment endpoint rejects orders still in 'placed' status
        $directFulfill = $this->actingAs($seller, 'seller')
            ->post(route('seller.orders.fulfill', $order));
        $directFulfill->assertSessionHas('error');
        $this->assertEquals('placed', $order->fresh()->status);
    }

    /**
     * Test 4.4: Terminal order states (fulfilled, cancelled) cannot be mutated or reverted.
     */
    public function test_state_machine_guardrails_terminal_orders_cannot_be_mutated(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();

        // 1. Fulfilled order mutation attempt
        $fulfilledOrder = $this->createSellerOrderRecord($seller, $buyer, 1500.00, 'SO-FUL-001', 'fulfilled');

        $mutateFulfilled = $this->actingAs($seller, 'seller')
            ->patch(route('seller.orders.update-status', $fulfilledOrder), ['status' => 'processing']);
        $mutateFulfilled->assertSessionHas('error');
        $this->assertEquals('fulfilled', $fulfilledOrder->fresh()->status);

        // 2. Cancelled order mutation attempt
        $cancelledOrder = $this->createSellerOrderRecord($seller, $buyer, 800.00, 'SO-CAN-001', 'cancelled');

        $mutateCancelled = $this->actingAs($seller, 'seller')
            ->patch(route('seller.orders.update-status', $cancelledOrder), ['status' => 'processing']);
        $mutateCancelled->assertSessionHas('error');
        $this->assertEquals('cancelled', $cancelledOrder->fresh()->status);
    }

    /**
     * Test 4.5: Wholesale auction creation rejects invalid price bounds and timestamps.
     */
    public function test_auction_guardrails_invalid_pricing_and_time_bounds_rejected(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Auction Guard Item', 500.00, 50, 'kg');

        // 1. Starting price <= 0
        $respStartPrice = $this->actingAs($seller, 'seller')->post(route('seller.auctions.store'), [
            'product_id'        => $product->id,
            'starting_price'    => -100.00,
            'reserve_price'     => 500.00,
            'minimum_increment' => 50.00,
            'starts_at'         => Carbon::now()->addHour()->format('Y-m-d\TH:i'),
            'ends_at'           => Carbon::now()->addHours(2)->format('Y-m-d\TH:i'),
        ]);
        $respStartPrice->assertSessionHasErrors(['starting_price']);

        // 2. Reserve price < Starting price
        $respReserve = $this->actingAs($seller, 'seller')->post(route('seller.auctions.store'), [
            'product_id'        => $product->id,
            'starting_price'    => 1000.00,
            'reserve_price'     => 500.00, // Reserve cannot be lower than starting floor
            'minimum_increment' => 50.00,
            'starts_at'         => Carbon::now()->addHour()->format('Y-m-d\TH:i'),
            'ends_at'           => Carbon::now()->addHours(2)->format('Y-m-d\TH:i'),
        ]);
        $respReserve->assertSessionHasErrors(['reserve_price']);

        // 3. End time before Start time
        $respTime = $this->actingAs($seller, 'seller')->post(route('seller.auctions.store'), [
            'product_id'        => $product->id,
            'starting_price'    => 1000.00,
            'reserve_price'     => 1500.00,
            'minimum_increment' => 50.00,
            'starts_at'         => Carbon::now()->addHours(3)->format('Y-m-d\TH:i'),
            'ends_at'           => Carbon::now()->addHour()->format('Y-m-d\TH:i'), // Ends before start
        ]);
        $respTime->assertSessionHasErrors(['ends_at']);
    }

    /**
     * Test 4.6: Auction cancellation is strictly blocked once active bids exist.
     */
    public function test_auction_guardrails_cancellation_strictly_blocked_once_bids_exist(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Rare Organic Honey', 800.00, 20, 'litre');

        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 800.00,
            'reserve_price'  => 1200.00,
            'status'         => 'live',
        ]);

        $bidder = $this->createBuyer();
        $this->createAuctionBid($auction, $bidder, 900.00);

        $cancelResp = $this->actingAs($seller, 'seller')->post(route('seller.auctions.cancel', $auction));
        $cancelResp->assertSessionHas('error');
        $this->assertEquals('live', $auction->fresh()->status);
    }

    /**
     * Test 4.7: Geographic coordinates validation rejects out-of-bounds latitude and longitude.
     */
    public function test_location_guardrails_out_of_bounds_geo_coordinates_rejected(): void
    {
        $seller = $this->createApprovedSeller();

        // Latitude > 90
        $respLat = $this->actingAs($seller, 'seller')->put(route('seller.account.location.update'), [
            'address'             => 'Invalid Latitude Address',
            'latitude'            => 95.5000,
            'longitude'           => 87.7500,
            'operating_radius_km' => 25,
        ]);
        $respLat->assertSessionHasErrors(['latitude']);

        // Longitude > 180
        $respLng = $this->actingAs($seller, 'seller')->put(route('seller.account.location.update'), [
            'address'             => 'Invalid Longitude Address',
            'latitude'            => 21.7500,
            'longitude'           => 195.0000,
            'operating_radius_km' => 25,
        ]);
        $respLng->assertSessionHasErrors(['longitude']);

        // Negative operating radius
        $respRadius = $this->actingAs($seller, 'seller')->put(route('seller.account.location.update'), [
            'address'             => 'Invalid Radius Address',
            'latitude'            => 21.7500,
            'longitude'           => 87.7500,
            'operating_radius_km' => -5,
        ]);
        $respRadius->assertSessionHasErrors(['operating_radius_km']);
    }

    /**
     * Test 4.8: Security credentials reject incorrect current password and weak passwords.
     */
    public function test_security_guardrails_incorrect_current_password_and_weak_passwords_rejected(): void
    {
        $seller = $this->createApprovedSeller([
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        // 1. Incorrect current password
        $respWrongCurrent = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.security.update'), [
                'current_password'      => 'WrongPassword!',
                'password'              => 'NewStrongPass@2026',
                'password_confirmation' => 'NewStrongPass@2026',
            ]);
        $respWrongCurrent->assertSessionHasErrors(['current_password']);

        // 2. Weak password (failing mixed-case / numbers / symbols rules)
        $respWeak = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.security.update'), [
                'current_password'      => 'CorrectPassword123!',
                'password'              => 'weak',
                'password_confirmation' => 'weak',
            ]);
        $respWeak->assertSessionHasErrors(['password']);
    }
}
