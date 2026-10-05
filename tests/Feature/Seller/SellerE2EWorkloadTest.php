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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SellerE2EWorkloadTest
 *
 * Feature 44: Comprehensive Automated E2E Workload Test Suite (Tiers 1-4).
 * Covers the full end-to-end multi-step seller journey and commercial workload:
 *
 *  - Tier 1: Onboarding wizard submission (Farmer/Kirana/Dark Store/Individual) with geo-coordinates,
 *            redirection to pending waiting gate, admin approval simulation, and dashboard KPI access.
 *  - Tier 2: Product Catalog CRUD across custom unit types ('kg', 'dozen', 'bundle', 'litre'),
 *            harvest dates, shelf-life expiry calculations, auto-flagging stale perishables, and
 *            inventory warehouse telemetry inline stock adjustments with audit logging.
 *  - Tier 3: Multi-tenant order fulfillment workflow: tenant-isolated orders, assigned delivery slot
 *            tracking, lifecycle status progression (placed -> processing -> ready_for_pickup -> fulfilled),
 *            courier handover verification, 10% commission + APMC cess calculation, and settlements ledger.
 *  - Tier 4: Wholesale auction creation, live bidding terminal 4 hero tiles, dynamic reserve met
 *            evaluation, live bid stream, cancellation policy enforcement, and storefront branding /
 *            operating harvest days JSON / location radar / password security updates.
 *  - Tier 4 (Integrated): Realistic multi-tenant commercial day workload simulation.
 */
class SellerE2EWorkloadTest extends TestCase
{
    use RefreshDatabase, SellerTestHelperTrait;

    protected Category $produceCategory;
    protected Category $dairyCategory;
    protected Category $spicesCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->produceCategory = Category::create([
            'name'   => 'Fresh Produce & Fruits',
            'slug'   => 'fresh-produce-fruits',
            'status' => 'active',
        ]);

        $this->dairyCategory = Category::create([
            'name'   => 'Dairy & Farm Liquids',
            'slug'   => 'dairy-farm-liquids',
            'status' => 'active',
        ]);

        $this->spicesCategory = Category::create([
            'name'   => 'Organic Spices & Herbs',
            'slug'   => 'organic-spices-herbs',
            'status' => 'active',
        ]);
    }

    // =========================================================================
    // SECTION 1: TIER 1 - SELLER ONBOARDING TO APPROVAL & DASHBOARD GATE
    // =========================================================================

    /**
     * Test 1.1: Full Onboarding Journey:
     * - Newly registered seller visits dashboard -> redirected to onboarding wizard or pending gate.
     * - Submits onboarding wizard with type 'Farmer', Contai coordinates, and storefront photo.
     * - Transitions to pending waiting gate (`/seller/pending`) rendering timeline and shop details.
     * - Admin approves seller (`status = 'approved'`).
     * - Seller can now access dashboard (`/seller/dashboard`) with HTTP 200, and is redirected from pending.
     */
    public function test_tier1_seller_onboarding_wizard_to_pending_gate_to_approved_dashboard(): void
    {
        Storage::fake('public');

        // Step 1: Create fresh active seller user without a profile yet
        $seller = User::factory()->create([
            'name'     => 'Bikram Mondal',
            'email'    => 'bikram.mondal@agro.in',
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('SecretPass123!'),
        ]);

        // Step 2: Unapproved/unprofiled seller visiting dashboard is redirected
        $dashResponse = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $this->assertTrue(
            $dashResponse->isRedirect(route('seller.pending')) ||
            $dashResponse->isRedirect(route('seller.onboarding'))
        );

        // Step 3: Access onboarding wizard workstation
        $wizardView = $this->actingAs($seller, 'seller')->get(route('seller.onboarding'));
        $wizardView->assertStatus(200);
        $wizardView->assertSee('Seller Type');

        // Step 4: Submit onboarding wizard payload with geo-coordinates and storefront image
        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $storefront = UploadedFile::fake()->createWithContent('storefront_farm.png', $pngContent);

        $payload = [
            'seller_type'      => 'Farmer',
            'shop_name'        => 'Maa Sarada Agro Farm',
            'bio'              => 'Natural chemical-free vegetables and paddy from Contai coastal basin.',
            'storefront_image' => $storefront,
            'address'          => 'Plot 88, Kanthi Bypass Road',
            'city'             => 'Contai',
            'state'            => 'West Bengal',
            'postal_code'      => '721401',
            'latitude'         => 21.7781,
            'longitude'        => 87.7516,
        ];

        $submitResponse = $this->actingAs($seller, 'seller')
            ->post(route('seller.onboarding.submit'), $payload);

        $submitResponse->assertRedirect(route('seller.pending'));
        $submitResponse->assertSessionHas('success');

        // Step 5: Verify profile creation in database in 'pending' status
        $this->assertDatabaseHas('seller_profiles', [
            'user_id'     => $seller->id,
            'seller_type' => 'Farmer',
            'shop_name'   => 'Maa Sarada Agro Farm',
            'shop_slug'   => 'maa-sarada-agro-farm',
            'city'        => 'Contai',
            'status'      => 'pending',
        ]);

        $profile = SellerProfile::where('user_id', $seller->id)->first();
        $this->assertNotNull($profile);
        $this->assertTrue($profile->isPending());
        $this->assertFalse($profile->isApproved());
        $this->assertEquals(21.7781, (float) $profile->latitude);
        $this->assertEquals(87.7516, (float) $profile->longitude);

        // Step 6: Pending seller visits pending page -> renders review progress timeline
        $seller = $seller->fresh();
        $pendingPage = $this->actingAs($seller, 'seller')->get(route('seller.pending'));
        $pendingPage->assertStatus(200);
        $pendingPage->assertSee('Maa Sarada Agro Farm');
        $pendingPage->assertSee('Awaiting Review');

        // While pending, accessing dashboard remains locked
        $gatedResponse = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $gatedResponse->assertRedirect(route('seller.pending'));

        // Step 7: Simulate Admin approval of the seller application
        $profile->update([
            'status'      => 'approved',
            'trust_score' => 95.00,
        ]);
        $this->assertTrue($profile->fresh()->isApproved());

        // Step 8: Approved seller now accesses operational dashboard
        $seller = $seller->fresh(['sellerProfile']);
        $approvedDash = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $approvedDash->assertStatus(200);
        $approvedDash->assertViewIs('seller.dashboard');
        $approvedDash->assertSee('Maa Sarada Agro Farm');
        $approvedDash->assertSee('95');

        // Approved seller visiting pending or onboarding is redirected to dashboard
        $this->actingAs($seller, 'seller')->get(route('seller.pending'))
            ->assertRedirect(route('seller.dashboard'));
        $this->actingAs($seller, 'seller')->get(route('seller.onboarding'))
            ->assertRedirect(route('seller.dashboard'));
    }

    // =========================================================================
    // SECTION 2: TIER 2 - PRODUCT CATALOG, CUSTOM UOM, FRESHNESS & INVENTORY
    // =========================================================================

    /**
     * Test 2.1: Product Catalog CRUD across custom unit types ('kg', 'dozen', 'bundle', 'litre'),
     * harvest dates, shelf-life window, and automated freshness engine evaluation.
     */
    public function test_tier2_product_catalog_lifecycle_custom_uom_freshness_engine_and_crud(): void
    {
        $seller = $this->createApprovedSeller([], ['shop_name' => 'Kanthi Organic Hub']);

        // 1. Create a perishable product with custom unit 'kg' and harvest date
        $kgPayload = [
            'name'                => 'Ratnagiri Alphonso Mango',
            'category_id'         => $this->produceCategory->id,
            'price'               => 450.00,
            'unit_type'           => 'kg',
            'stock'               => 100,
            'low_stock_threshold' => 15,
            'harvest_date'        => Carbon::today()->toDateString(),
            'expiry_days'         => 7,
            'is_perishable'       => 1,
            'auto_hide_expired'   => 1,
            'farm_origin'         => 'East Midnapore Orchards',
            'harvest_grade'       => 'Grade A Export',
            'description'         => 'Sweet aromatic naturally tree-ripened Alphonso mangoes.',
        ];

        $responseKg = $this->actingAs($seller, 'seller')
            ->post(route('seller.products.store'), $kgPayload);
        $responseKg->assertRedirect(route('seller.products.index'));

        $mango = Product::where('seller_id', $seller->id)->where('unit_type', 'kg')->first();
        $this->assertNotNull($mango);
        $this->assertEquals('Ratnagiri Alphonso Mango', $mango->name);
        $this->assertEquals(450.00, (float) $mango->price);
        $this->assertEquals('kg', $mango->unit_type);
        $this->assertEquals(Carbon::today()->addDays(7)->toDateString(), $mango->expiry_date->toDateString());
        $this->assertFalse($mango->isExpired());
        $this->assertFalse($mango->isStale());
        $this->assertFalse($mango->isLowStock());

        // 2. Create products with other custom units: 'dozen', 'bundle', 'litre'
        // Unit: 'dozen' (Farm Fresh Eggs / Bananas)
        $this->actingAs($seller, 'seller')->post(route('seller.products.store'), [
            'name'                => 'Robusta Bananas Cluster',
            'category_id'         => $this->produceCategory->id,
            'price'               => 60.00,
            'unit_type'           => 'dozen',
            'stock'               => 40,
            'low_stock_threshold' => 10,
            'harvest_date'        => Carbon::today()->toDateString(),
            'expiry_days'         => 4,
            'is_perishable'       => 1,
        ]);

        // Unit: 'bundle' (Fresh Organic Spinach)
        $this->actingAs($seller, 'seller')->post(route('seller.products.store'), [
            'name'                => 'Organic Palak Spinach Bundle',
            'category_id'         => $this->produceCategory->id,
            'price'               => 30.00,
            'unit_type'           => 'bundle',
            'stock'               => 50,
            'low_stock_threshold' => 10,
            'harvest_date'        => Carbon::today()->toDateString(),
            'expiry_days'         => 2,
            'is_perishable'       => 1,
        ]);

        // Unit: 'litre' (Cold Pressed Pure Mustard Oil)
        $this->actingAs($seller, 'seller')->post(route('seller.products.store'), [
            'name'                => 'Pure Kachi Ghani Mustard Oil',
            'category_id'         => $this->dairyCategory->id,
            'price'               => 180.00,
            'unit_type'           => 'litre',
            'stock'               => 60,
            'low_stock_threshold' => 10,
            'is_perishable'       => 0,
        ]);

        // Verify all 4 custom UoMs exist in the catalog
        $this->assertEquals(4, Product::where('seller_id', $seller->id)->count());
        $this->assertEquals(1, Product::where('seller_id', $seller->id)->where('unit_type', 'kg')->count());
        $this->assertEquals(1, Product::where('seller_id', $seller->id)->where('unit_type', 'dozen')->count());
        $this->assertEquals(1, Product::where('seller_id', $seller->id)->where('unit_type', 'bundle')->count());
        $this->assertEquals(1, Product::where('seller_id', $seller->id)->where('unit_type', 'litre')->count());

        // 3. Test Automated Freshness Engine: Expired item auto-flagging and auto-hiding
        $expiredProduct = Product::create([
            'seller_id'           => $seller->id,
            'category_id'         => $this->produceCategory->id,
            'name'                => 'Overripe Strawberries',
            'slug'                => 'overripe-strawberries-' . uniqid(),
            'sku'                 => 'BZ-STR-OLD',
            'sale_type'           => 'fixed_price',
            'unit_type'           => 'kg',
            'price'               => 80.00,
            'stock'               => 15,
            'low_stock_threshold' => 5,
            'harvest_date'        => Carbon::today()->subDays(10)->toDateString(),
            'expiry_days'         => 3,
            'expiry_date'         => Carbon::today()->subDays(7)->toDateString(),
            'is_perishable'       => true,
            'auto_hide_expired'   => true,
            'status'              => 'active',
        ]);

        $this->assertTrue($expiredProduct->isExpired());
        $this->assertTrue($expiredProduct->isStale());

        // Assert query scopes
        $freshProducts = Product::where('seller_id', $seller->id)->fresh()->get();
        $staleProducts = Product::where('seller_id', $seller->id)->stale()->get();
        $publicVisible = Product::where('seller_id', $seller->id)->publicVisible()->get();

        $this->assertEquals(4, $freshProducts->count());
        $this->assertEquals(1, $staleProducts->count());
        $this->assertFalse($publicVisible->contains('id', $expiredProduct->id));

        // 4. Update Product (Edit Workstation)
        $updatePayload = [
            'name'        => 'Ratnagiri Alphonso Mango (Export Grade 1)',
            'category_id' => $this->produceCategory->id,
            'price'       => 480.00,
            'unit_type'   => 'kg',
            'stock'       => 120,
        ];
        $updateResp = $this->actingAs($seller, 'seller')
            ->put(route('seller.products.update', $mango), $updatePayload);
        $updateResp->assertRedirect(route('seller.products.index'));

        $this->assertEquals(480.00, (float) $mango->fresh()->price);
        $this->assertEquals(120, $mango->fresh()->stock);

        // 5. Catalog Index View renders products with UoM tags
        $catalogResp = $this->actingAs($seller, 'seller')->get(route('seller.products.index'));
        $catalogResp->assertStatus(200);
        $catalogResp->assertSee('Ratnagiri Alphonso Mango');
        $catalogResp->assertSee('dozen');
        $catalogResp->assertSee('bundle');
        $catalogResp->assertSee('litre');
    }

    /**
     * Test 2.2: Inventory Warehouse Telemetry and Inline Stock Adjustments:
     * - Access warehouse telemetry dashboard (`/seller/products/inventory`).
     * - Health metrics rendering (Healthy, Low Stock, Depleted).
     * - Inline stock adjustment actions: 'add', 'reduce', 'set' with audit logging.
     * - Low-stock threshold alert triggering.
     */
    public function test_tier2_inventory_telemetry_and_stock_adjustments(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Contai Black Rice Lot', 120.00, 50, 'kg', 10);

        // 1. Visit warehouse inventory telemetry
        $invResp = $this->actingAs($seller, 'seller')->get(route('seller.products.inventory'));
        $invResp->assertStatus(200);
        $invResp->assertSee('Contai Black Rice Lot');
        $invResp->assertSee('50');

        // 2. Action: 'add' (+25 units via field harvest intake)
        $addResp = $this->actingAs($seller, 'seller')
            ->post(route('seller.products.adjust-stock', $product), [
                'action'   => 'add',
                'quantity' => 25,
                'reason'   => 'Fresh harvest intake from Barn #4',
            ]);
        $addResp->assertSessionHas('success');
        $this->assertEquals(75, $product->fresh()->stock);

        // 3. Action: 'reduce' (-15 units via sorting discard / transit damage)
        $redResp = $this->actingAs($seller, 'seller')
            ->post(route('seller.products.adjust-stock', $product), [
                'action'   => 'reduce',
                'quantity' => 15,
                'reason'   => 'Moisture damage culled during quality sort',
            ]);
        $redResp->assertSessionHas('success');
        $this->assertEquals(60, $product->fresh()->stock);

        // 4. Action: 'set' to 6 units (below low_stock_threshold of 10)
        $setResp = $this->actingAs($seller, 'seller')
            ->post(route('seller.products.adjust-stock', $product), [
                'action'   => 'set',
                'quantity' => 6,
                'reason'   => 'Physical audit recount at closing',
            ]);
        $setResp->assertSessionHas('success');
        $this->assertEquals(6, $product->fresh()->stock);
        $this->assertTrue($product->fresh()->isLowStock());

        // 5. Verify low-stock tab filter on inventory page
        $lowStockView = $this->actingAs($seller, 'seller')
            ->get(route('seller.products.inventory', ['tab' => 'low_stock']));
        $lowStockView->assertStatus(200);
        $lowStockView->assertSee('Contai Black Rice Lot');
    }

    // =========================================================================
    // SECTION 3: TIER 3 - MULTI-TENANT ORDER FULFILLMENT & SETTLEMENTS
    // =========================================================================

    /**
     * Test 3.1: Multi-Tenant Order Fulfillment Workflow:
     * - Multi-seller order placed by buyer with assigned delivery slot ("Today, 4:00 PM – 6:00 PM").
     * - Strict tenancy boundary: Seller A sees only Seller A's sub-order; Seller B sees only Seller B's.
     * - Status progression: placed -> processing -> ready_for_pickup -> fulfilled (courier handover).
     * - Transparent fee calculation: Gross - 10% commission - APMC cess = Net payout.
     * - Payout record auto-generation and payouts ledger rendering.
     */
    public function test_tier3_order_fulfillment_delivery_slots_and_commission_settlement(): void
    {
        $sellerA = $this->createApprovedSeller([], ['shop_name' => 'Seller A Agro']);
        $sellerB = $this->createApprovedSeller([], ['shop_name' => 'Seller B Spices']);
        $buyer = $this->createBuyer(['name' => 'Subhashree Roy']);

        $productA = $this->createProductRecord($sellerA, 'Malda Mangoes', 500.00, 20, 'kg');
        $productB = $this->createProductRecord($sellerB, 'Black Pepper', 300.00, 10, 'kg');

        // Create multi-seller order with delivery slot in parent notes
        $orderData = $this->createMultiSellerOrder($buyer, [
            [
                'seller' => $sellerA,
                'items'  => [['product' => $productA, 'qty' => 4]], // Subtotal: ₹2,000.00
            ],
            [
                'seller' => $sellerB,
                'items'  => [['product' => $productB, 'qty' => 2]], // Subtotal: ₹600.00
            ],
        ]);

        /** @var SellerOrder $orderA */
        $orderA = $orderData['seller_orders']->firstWhere('seller_id', $sellerA->id);
        /** @var SellerOrder $orderB */
        $orderB = $orderData['seller_orders']->firstWhere('seller_id', $sellerB->id);

        $orderA->update(['delivery_slot' => 'Today, 4:00 PM – 6:00 PM']);
        $orderB->update(['delivery_slot' => 'Tomorrow, 8:00 AM – 10:00 AM']);

        // 1. Tenancy Isolation: Seller A cannot see Seller B's order
        $indexA = $this->actingAs($sellerA, 'seller')->get(route('seller.orders.index'));
        $indexA->assertStatus(200);
        $indexA->assertSee($orderA->seller_order_number);
        $indexA->assertDontSee($orderB->seller_order_number);

        // Seller A accessing Seller B's order directly gets 403 Forbidden
        $unauthResp = $this->actingAs($sellerA, 'seller')->get(route('seller.orders.show', $orderB));
        $unauthResp->assertStatus(403);

        // 2. Assigned Delivery Slot Display
        $showA = $this->actingAs($sellerA, 'seller')->get(route('seller.orders.show', $orderA));
        $showA->assertStatus(200);
        $showA->assertSee('Today, 4:00 PM – 6:00 PM');
        $showA->assertSee('Subhashree Roy');
        $showA->assertSee('Malda Mangoes');

        // 3. Status Progression Lifecycle
        // Step 3a: Transition 'placed' -> 'processing'
        $procResp = $this->actingAs($sellerA, 'seller')
            ->patch(route('seller.orders.update-status', $orderA), [
                'status' => 'processing',
            ]);
        $procResp->assertSessionHas('success');
        $this->assertEquals('processing', $orderA->fresh()->status);

        // Step 3b: Transition 'processing' -> 'ready_for_pickup' with assigned courier
        $pickupResp = $this->actingAs($sellerA, 'seller')
            ->patch(route('seller.orders.update-status', $orderA), [
                'status'       => 'ready_for_pickup',
                'courier_name' => 'Bazaario Hyperlocal Van #08',
            ]);
        $pickupResp->assertSessionHas('success');
        $this->assertEquals('ready_for_pickup', $orderA->fresh()->status);
        $this->assertEquals('Bazaario Hyperlocal Van #08', $orderA->fresh()->courier_name);

        // Step 3c: Physical Handover Protocol -> transitions to 'fulfilled'
        $handoverResp = $this->actingAs($sellerA, 'seller')
            ->post(route('seller.orders.handover', $orderA), [
                'courier_name' => 'Bazaario Hyperlocal Van #08',
            ]);
        $handoverResp->assertSessionHas('success');

        $freshOrderA = $orderA->fresh();
        $this->assertEquals('fulfilled', $freshOrderA->status);
        $this->assertNotNull($freshOrderA->delivered_at);
        $this->assertNotNull($freshOrderA->handover_confirmed_at);

        // 4. Transparent Commission Calculation Verification
        // Subtotal = 2,000.00; 10% commission = 200.00; Net payout = 1,800.00 (or minus APMC cess)
        $this->assertEquals(2000.00, (float) $freshOrderA->subtotal);
        $this->assertEquals(200.00, (float) $freshOrderA->commission_amount);
        $this->assertTrue((float) $freshOrderA->payout_amount > 0);

        // 5. Payout Record Verification
        $payout = Payout::where('seller_order_id', $orderA->id)->first();
        $this->assertNotNull($payout);
        $this->assertEquals($sellerA->id, $payout->seller_id);
        $this->assertEquals(2000.00, (float) $payout->gross_amount);
        $this->assertEquals(200.00, (float) $payout->commission_amount);
        $this->assertEquals((float) $freshOrderA->payout_amount, (float) $payout->net_amount);
        $this->assertEquals('pending', $payout->status);

        // 6. Payouts Ledger Workspace Rendering
        $payoutsIndex = $this->actingAs($sellerA, 'seller')->get(route('seller.payouts.index'));
        $payoutsIndex->assertStatus(200);
        $payoutsIndex->assertSee('Financial Ledger');
        $payoutsIndex->assertSee($payout->payout_reference);
        $payoutsIndex->assertSee('2,000');

        // Payout detail screen
        $payoutShow = $this->actingAs($sellerA, 'seller')->get(route('seller.payouts.show', $payout));
        $payoutShow->assertStatus(200);
        $payoutShow->assertSee($payout->payout_reference);
    }

    // =========================================================================
    // SECTION 4: TIER 4 - WHOLESALE LIVE AUCTIONS & STORE SETTINGS
    // =========================================================================

    /**
     * Test 4.1: Wholesale Auction Lifecycle:
     * - Create auction listing with starting price, reserve price, min increment, and timestamps.
     * - Live bidding terminal renders 4 hero tiles (Lot details, Reserve Met, Countdown, Bid stream).
     * - Live bidding simulation:
     *   - Below reserve -> Reserve Met badge indicates 'Reserve Not Met'.
     *   - Attempting cancellation once bids exist is strictly BLOCKED by policy guardrail.
     *   - Bid meeting/exceeding reserve -> Reserve Met badge switches to 'Reserve Met'.
     */
    public function test_tier4_wholesale_auction_lifecycle_live_terminal_and_guardrails(): void
    {
        $seller = $this->createApprovedSeller([], ['shop_name' => 'Mondal Wholesale Agro']);
        $product = $this->createProductRecord($seller, 'Premium Himsagar Lot 100kg', 300.00, 100, 'kg');

        // 1. Create wholesale auction listing
        $auctionPayload = [
            'product_id'        => $product->id,
            'starting_price'    => 2000.00,
            'reserve_price'     => 3500.00,
            'minimum_increment' => 200.00,
            'starts_at'         => Carbon::now()->subMinutes(15)->format('Y-m-d\TH:i'),
            'ends_at'           => Carbon::now()->addHours(2)->format('Y-m-d\TH:i'),
        ];

        $storeAuctionResp = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.store'), $auctionPayload);
        $storeAuctionResp->assertRedirect(route('seller.auctions.index'));

        $auction = Auction::where('product_id', $product->id)->first();
        $this->assertNotNull($auction);
        $this->assertEquals('live', $auction->status);
        $this->assertEquals(2000.00, (float) $auction->starting_price);
        $this->assertEquals(3500.00, (float) $auction->reserve_price);
        $this->assertEquals(2000.00, (float) $auction->current_price);

        // 2. Access Live Wholesale Bidding Terminal
        $liveTerminal = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));
        $liveTerminal->assertStatus(200);
        $liveTerminal->assertSee('Live Bidding Terminal');
        $liveTerminal->assertSee('Premium Himsagar Lot 100kg');

        // 3. Initial Reserve State: Highest Bid (2000) < Reserve Price (3500)
        $this->assertFalse($auction->isReserveMet());

        // 4. Place bid 1: ₹2,600 (still below reserve of ₹3,500)
        $bidder1 = $this->createBuyer(['name' => 'Trader Amitava']);
        $this->createAuctionBid($auction, $bidder1, 2600.00);

        $this->assertFalse($auction->fresh()->isReserveMet());
        $this->assertEquals(2600.00, (float) $auction->fresh()->current_price);

        // 5. Guardrail: Seller cannot cancel an auction once active bids have been registered
        $cancelAttempt = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));
        $cancelAttempt->assertSessionHas('error');
        $this->assertNotEquals('cancelled', $auction->fresh()->status);

        // 6. Place bid 2: ₹3,800 (exceeds reserve price of ₹3,500)
        $bidder2 = $this->createBuyer(['name' => 'Wholesaler Debjit']);
        $this->createAuctionBid($auction, $bidder2, 3800.00);

        $this->assertTrue($auction->fresh()->isReserveMet());
        $this->assertEquals(3800.00, (float) $auction->fresh()->current_price);

        // 7. Verify live terminal reflects the updated auction lot
        $updatedTerminal = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));
        $updatedTerminal->assertStatus(200);
        $updatedTerminal->assertSee('3,800');
    }

    /**
     * Test 4.2: Storefront Profile, Operating Harvest Days JSON, Location Radar & Password Lifecycle:
     * - Update profile branding (shop name, bio, logo).
     * - Persist operating harvest days JSON array (`['mon', 'wed', 'fri']`).
     * - Update farm location coordinates (Contai) and geofence radius.
     * - Verify Haversine distance calculation.
     * - Update account password with complexity verification.
     */
    public function test_tier4_profile_branding_operating_days_location_radar_and_password(): void
    {
        Storage::fake('public');

        $seller = $this->createApprovedSeller([
            'password' => Hash::make('OldSecurePassword123!'),
        ], [
            'shop_name'           => 'Initial Farm Name',
            'city'                => 'Contai',
            'latitude'            => 21.7781,
            'longitude'           => 87.7516,
            'operating_radius_km' => 20,
        ]);

        // 1. Profile Branding & Operating Days JSON Update
        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $logo = UploadedFile::fake()->createWithContent('brand_logo.png', $pngContent);

        $profilePayload = [
            'shop_name'      => 'East Midnapore Agro Consortium',
            'bio'            => 'Pioneering organic cultivation across Contai and Digha coastal belt.',
            'logo_image'     => $logo,
            'operating_days' => ['mon', 'wed', 'fri', 'sat'],
        ];

        $updateProfResp = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), $profilePayload);
        $updateProfResp->assertRedirect(route('seller.account.profile'));

        $freshProfile = $seller->fresh()->sellerProfile;
        $this->assertEquals('East Midnapore Agro Consortium', $freshProfile->shop_name);
        $this->assertEquals('Pioneering organic cultivation across Contai and Digha coastal belt.', $freshProfile->bio);
        $this->assertEquals(['mon', 'wed', 'fri', 'sat'], $freshProfile->operating_days);
        $this->assertNotNull($freshProfile->logo_path);

        // 2. Location Radar & Geofence Coordinates Update
        $locationPayload = [
            'address'             => 'Plot 104, Tamluk Highway, Contai',
            'city'                => 'Contai',
            'state'               => 'West Bengal',
            'postal_code'         => '721401',
            'latitude'            => 21.7800,
            'longitude'           => 87.7500,
            'operating_radius_km' => 35,
        ];

        $updateLocResp = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.location.update'), $locationPayload);
        $updateLocResp->assertRedirect(route('seller.account.location'));

        $freshProfile = $freshProfile->fresh();
        $this->assertEquals('Plot 104, Tamluk Highway, Contai', $freshProfile->address);
        $this->assertEquals(21.7800, (float) $freshProfile->latitude);
        $this->assertEquals(87.7500, (float) $freshProfile->longitude);
        $this->assertEquals(35, $freshProfile->operating_radius_km);

        // Haversine distance verification: Contai to Digha (~30km)
        $distanceToDigha = $freshProfile->distanceTo(21.6266, 87.5074);
        $this->assertGreaterThan(15.0, $distanceToDigha);
        $this->assertLessThan(45.0, $distanceToDigha);

        // 3. Password Security Update with Complexity Enforcement
        $passwordPayload = [
            'current_password'      => 'OldSecurePassword123!',
            'password'              => 'NewHarvestPass@2026',
            'password_confirmation' => 'NewHarvestPass@2026',
        ];

        $pwdResp = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.security.update'), $passwordPayload);
        $pwdResp->assertRedirect(route('seller.account.security'));
        $pwdResp->assertSessionHas('success');

        $this->assertTrue(Hash::check('NewHarvestPass@2026', $seller->fresh()->password));
    }

    // =========================================================================
    // SECTION 5: TIER 4 (INTEGRATED) - REALISTIC COMMERCIAL WORKLOAD
    // =========================================================================

    /**
     * Test 4.3: Integrated Multi-Tenant Commercial Day Workload:
     * - Multi-seller setup: Farmer Ramesh (Contai) & Kirana Priya (Kolkata).
     * - Multiple buyers placing multi-item orders.
     * - Multiple product units ('kg', 'dozen', 'litre').
     * - Stock adjustments and order fulfillments.
     * - Independent dashboard KPI verification confirming strict tenancy isolation.
     */
    public function test_tier4_integrated_realistic_multi_tenant_commercial_workload(): void
    {
        // 1. Establish two approved sellers
        $sellerRamesh = $this->createApprovedSeller([
            'name'  => 'Ramesh Patel',
            'email' => 'ramesh.farmer@agro.in',
        ], [
            'shop_name'   => 'Ramesh Agro Farms',
            'seller_type' => 'Farmer',
            'city'        => 'Contai',
            'trust_score' => 96.00,
        ]);

        $sellerPriya = $this->createApprovedSeller([
            'name'  => 'Priya Sen',
            'email' => 'priya.kirana@bazaario.in',
        ], [
            'shop_name'   => 'Priya Daily Essentials',
            'seller_type' => 'Kirana Store',
            'city'        => 'Kolkata',
            'trust_score' => 91.00,
        ]);

        // 2. Populate product catalogs
        $mangoes = $this->createProductRecord($sellerRamesh, 'Export Mangoes', 300.00, 80, 'kg', 10);
        $potatoes = $this->createProductRecord($sellerRamesh, 'Jyoti Potatoes', 30.00, 200, 'kg', 25);

        $spices = $this->createProductRecord($sellerPriya, 'Cumin Seeds', 150.00, 40, 'dozen', 5);
        $mustardOil = $this->createProductRecord($sellerPriya, 'Mustard Oil Bottle', 190.00, 60, 'litre', 10);

        // 3. Buyer 1 places order with both sellers
        $buyer1 = $this->createBuyer(['name' => 'Rahul Roy']);
        $multiOrder = $this->createMultiSellerOrder($buyer1, [
            [
                'seller' => $sellerRamesh,
                'items'  => [
                    ['product' => $mangoes, 'qty' => 5],   // ₹1,500.00
                    ['product' => $potatoes, 'qty' => 10], // ₹300.00
                ], // Ramesh subtotal: ₹1,800.00
            ],
            [
                'seller' => $sellerPriya,
                'items'  => [
                    ['product' => $spices, 'qty' => 2],      // ₹300.00
                    ['product' => $mustardOil, 'qty' => 3],  // ₹570.00
                ], // Priya subtotal: ₹870.00
            ],
        ]);

        /** @var SellerOrder $soRamesh */
        $soRamesh = $multiOrder['seller_orders']->firstWhere('seller_id', $sellerRamesh->id);
        /** @var SellerOrder $soPriya */
        $soPriya = $multiOrder['seller_orders']->firstWhere('seller_id', $sellerPriya->id);

        // 4. Buyer 2 places order strictly with Ramesh
        $buyer2 = $this->createBuyer(['name' => 'Anil Ghosh']);
        $soRamesh2 = $this->createSellerOrderRecord($sellerRamesh, $buyer2, 900.00, 'SO-RAMESH-2', 'placed');

        // 5. Ramesh fulfills order 1
        $this->actingAs($sellerRamesh, 'seller')
            ->patch(route('seller.orders.update-status', $soRamesh), ['status' => 'processing']);
        $this->actingAs($sellerRamesh, 'seller')
            ->patch(route('seller.orders.update-status', $soRamesh), ['status' => 'ready_for_pickup', 'courier_name' => 'FastFleet #01']);
        $this->actingAs($sellerRamesh, 'seller')
            ->post(route('seller.orders.fulfill', $soRamesh), ['courier_name' => 'FastFleet #01']);

        // 6. Verify Ramesh's Dashboard reflects Ramesh's exact figures
        // Total orders: 2; Revenue: ₹1,800.00 + ₹900.00 = ₹2,700.00
        $dashRamesh = $this->actingAs($sellerRamesh, 'seller')->get(route('seller.dashboard'));
        $dashRamesh->assertStatus(200);
        $dashRamesh->assertSee('Ramesh Agro Farms');
        $dashRamesh->assertSee('2,700');
        $dashRamesh->assertDontSee('Priya Daily Essentials');

        // 7. Verify Priya's Dashboard reflects Priya's exact figures
        // Total orders: 1; Revenue: ₹870.00
        $dashPriya = $this->actingAs($sellerPriya, 'seller')->get(route('seller.dashboard'));
        $dashPriya->assertStatus(200);
        $dashPriya->assertSee('Priya Daily Essentials');
        $dashPriya->assertSee('870');
        $dashPriya->assertDontSee('Ramesh Agro Farms');
    }
}
