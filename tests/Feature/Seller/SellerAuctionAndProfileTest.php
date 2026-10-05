<?php

namespace Tests\Feature\Seller;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class SellerAuctionAndProfileTest
 *
 * Comprehensive Automated Test Suite for Milestone 5:
 * Features 34-43 covering Seller Profile, Account Settings, Wholesale Auctions, and Live Terminal.
 *
 *  - Tier 1: Core Workspaces & Happy Path CRUD (Features 34-43)
 *  - Tier 2: Boundary Security, Input Sanitization & Multi-Tenant Tenancy Isolation
 *  - Tier 3: Business Logic State Machines & Safety Guardrails (Reserve Met & Cancellation)
 *  - Tier 4: Real-World Workloads, Resilience & Adversarial Hardening
 */
class SellerAuctionAndProfileTest extends TestCase
{
    use RefreshDatabase, SellerTestHelperTrait;

    // =========================================================================
    // SECTION 1: TIER 1 - CORE WORKSPACES & HAPPY PATH CRUD
    // =========================================================================

    /**
     * Test 1.1: Approved seller can access shop profile page (/seller/account/profile) with HTTP 200.
     * Feature 34
     */
    public function test_tier1_approved_seller_can_access_profile_page_with_http_200(): void
    {
        $seller = $this->createApprovedSeller([], [
            'shop_name'   => 'Green Valley Farms',
            'bio'         => 'Certified organic orchard producing premium Ratnagiri Alphonso mangoes.',
            'trust_score' => 94.00,
            'seller_type' => 'Farmer',
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.account.profile'));

        $response->assertStatus(200);
        $response->assertViewIs('seller.account.profile');
        $response->assertSee('Green Valley Farms');
        $response->assertSee('Shop Profile &amp; Settings', false);
        $response->assertSee('94/100', false);
        $response->assertSee('Farmer');
    }

    /**
     * Test 1.2: Approved seller can update shop profile details.
     * Feature 34
     */
    public function test_tier1_approved_seller_can_update_shop_profile_details(): void
    {
        $seller = $this->createApprovedSeller();

        $payload = [
            'shop_name' => 'Ratnagiri Heritage Groves',
            'bio'       => 'High-altitude organic mangoes with GI tag certification.',
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), $payload);

        $response->assertRedirect(route('seller.account.profile'));
        $response->assertSessionHas('success');

        $profile = $seller->fresh()->sellerProfile;
        $this->assertEquals('Ratnagiri Heritage Groves', $profile->shop_name);
        $this->assertEquals('High-altitude organic mangoes with GI tag certification.', $profile->bio);
    }

    /**
     * Test 1.3: Approved seller can upload storefront banner and logo images.
     * Feature 34
     */
    public function test_tier1_approved_seller_can_upload_storefront_banner_and_logo(): void
    {
        Storage::fake('public');

        $seller = $this->createApprovedSeller();
        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $banner = UploadedFile::fake()->createWithContent('banner.png', $pngContent);
        $logo = UploadedFile::fake()->createWithContent('logo.png', $pngContent);

        $payload = [
            'shop_name'    => 'Banner Tested Farm',
            'bio'          => 'Updated with new branding imagery.',
            'banner_image' => $banner,
            'logo_image'   => $logo,
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), $payload);

        $response->assertRedirect(route('seller.account.profile'));
        $response->assertSessionHas('success');

        $profile = $seller->fresh()->sellerProfile;
        $this->assertNotNull($profile->banner_path);
        $this->assertNotNull($profile->logo_path);
        Storage::disk('public')->assertExists($profile->banner_path);
        Storage::disk('public')->assertExists($profile->logo_path);
    }

    /**
     * Test 1.4: Approved seller can update operating harvest days and dispatch SLA.
     * Feature 35
     */
    public function test_tier1_approved_seller_can_update_operating_harvest_days_and_dispatch_sla(): void
    {
        $seller = $this->createApprovedSeller();

        $payload = [
            'shop_name'      => 'SLA Tested Farm',
            'operating_days' => ['mon', 'tue', 'wed', 'thu', 'fri'],
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), $payload);

        $response->assertRedirect(route('seller.account.profile'));
        $response->assertSessionHas('success');
    }

    /**
     * Test 1.5: Approved seller can access location settings page (/seller/account/location).
     * Feature 36
     */
    public function test_tier1_approved_seller_can_access_location_settings_page(): void
    {
        $seller = $this->createApprovedSeller([], [
            'address'             => 'Survey No. 142/3, Dapoli Orchard',
            'city'                => 'Ratnagiri',
            'state'               => 'Maharashtra',
            'postal_code'         => '415712',
            'latitude'            => 17.7534,
            'longitude'           => 73.1895,
            'operating_radius_km' => 30,
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.account.location'));

        $response->assertStatus(200);
        $response->assertViewIs('seller.account.location');
        $response->assertSee('Location Telemetry &amp; Geofence', false);
        $response->assertSee('Survey No. 142/3, Dapoli Orchard');
        $response->assertSee('415712');
    }

    /**
     * Test 1.6: Approved seller can update farm coordinates, address, and geofence radius.
     * Feature 36
     */
    public function test_tier1_approved_seller_can_update_coordinates_and_geofence_radius(): void
    {
        $seller = $this->createApprovedSeller();

        $payload = [
            'address'             => 'Plot 55, High-Tech Agro Park',
            'city'                => 'Contai',
            'state'               => 'West Bengal',
            'postal_code'         => '721401',
            'latitude'            => 21.7781,
            'longitude'           => 87.7516,
            'operating_radius_km' => 45,
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.location.update'), $payload);

        $response->assertRedirect(route('seller.account.location'));
        $response->assertSessionHas('success');

        $profile = $seller->fresh()->sellerProfile;
        $this->assertEquals('Plot 55, High-Tech Agro Park', $profile->address);
        $this->assertEquals(21.7781, $profile->latitude);
        $this->assertEquals(87.7516, $profile->longitude);
        $this->assertEquals(45, $profile->operating_radius_km);
    }

    /**
     * Test 1.7: Haversine distance calculation behaves accurately with coordinates.
     * Feature 36
     */
    public function test_tier1_haversine_distance_calculation_with_updated_coordinates(): void
    {
        $seller = $this->createApprovedSeller([], [
            'latitude'  => 22.5726, // Kolkata
            'longitude' => 88.3639,
        ]);

        $profile = $seller->sellerProfile;

        // Distance from Kolkata to Contai (approx 115-125 km)
        $distance = $profile->distanceTo(21.7781, 87.7516);
        $this->assertGreaterThan(100, $distance);
        $this->assertLessThan(150, $distance);

        // Distance to identical point is 0
        $this->assertEquals(0.0, $profile->distanceTo(22.5726, 88.3639));
    }

    /**
     * Test 1.8: Approved seller can access security page (/seller/account/security).
     * Feature 37
     */
    public function test_tier1_approved_seller_can_access_security_page(): void
    {
        $seller = $this->createApprovedSeller();

        $response = $this->actingAs($seller, 'seller')->get(route('seller.account.security'));

        $response->assertStatus(200);
        $response->assertViewIs('seller.account.security');
        $response->assertSee('Security &amp; Access Credentials', false);
        $response->assertSee('Current Password');
        $response->assertSee('New Password');
        $response->assertSee('Complexity');
    }

    /**
     * Test 1.9: Approved seller can update password with valid credentials.
     * Feature 37
     */
    public function test_tier1_approved_seller_can_update_password_with_valid_credentials(): void
    {
        $seller = $this->createApprovedSeller([
            'password' => Hash::make('Password123!'),
        ]);

        $payload = [
            'current_password'      => 'Password123!',
            'password'              => 'AlphonsoHarvest$2026',
            'password_confirmation' => 'AlphonsoHarvest$2026',
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.security.update'), $payload);

        $response->assertRedirect(route('seller.account.security'));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('AlphonsoHarvest$2026', $seller->fresh()->password));
    }

    /**
     * Test 1.10: Approved seller can access auction creation workstation (/seller/auctions/create).
     * Feature 40
     */
    public function test_tier1_approved_seller_can_access_auction_creation_workstation(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Alphonso Premium Lot', 1200.00, 50, 'kg');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.auctions.create'));

        $response->assertStatus(200);
        $response->assertViewIs('seller.auctions.create');
        $response->assertSee('Create Wholesale Auction Lot');
        $response->assertSee('Alphonso Premium Lot');
    }

    /**
     * Test 1.11: Approved seller can create a valid wholesale auction listing.
     * Feature 40
     */
    public function test_tier1_approved_seller_can_create_valid_auction_listing(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Organic Alphonso Mangoes', 1500.00);

        $startsAt = Carbon::now()->addHour()->toDateTimeString();
        $endsAt = Carbon::now()->addHours(6)->toDateTimeString();

        $payload = [
            'product_id'        => $product->id,
            'starting_price'    => 1500.00,
            'reserve_price'     => 2500.00,
            'minimum_increment' => 100.00,
            'starts_at'         => $startsAt,
            'ends_at'           => $endsAt,
        ];

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.store'), $payload);

        $response->assertRedirect(route('seller.auctions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('auctions', [
            'seller_id'         => $seller->sellerProfile->id,
            'product_id'        => $product->id,
            'starting_price'    => 1500.00,
            'reserve_price'     => 2500.00,
            'current_price'     => 1500.00,
            'minimum_increment' => 100.00,
            'status'            => 'scheduled',
        ]);
    }

    /**
     * Test 1.12: Approved seller can access live bidding terminal for active lot.
     * Feature 38
     */
    public function test_tier1_approved_seller_can_access_live_bidding_terminal_for_active_lot(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Grade A+ Ratnagiri Alphonso', 1500.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'status' => 'live',
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));

        $response->assertStatus(200);
        $response->assertViewIs('seller.auctions.live');
        $response->assertSee('Wholesale Bidding Terminal');
        $response->assertSee('Grade A+ Ratnagiri Alphonso');
        $response->assertSee('LIVE BIDDING');
    }

    /**
     * Test 1.13: Live terminal renders all four hero tiles.
     * Feature 38
     */
    public function test_tier1_live_terminal_renders_all_four_hero_tiles(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Fresh Lot 104', 1500.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 1500.00,
            'reserve_price'  => 2500.00,
            'current_price'  => 1800.00,
            'status'         => 'live',
        ]);
        $this->createAuctionBid($auction, $buyer, 1800.00);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));

        $response->assertStatus(200);
        // Tile 1: Current Highest Bid
        $response->assertSee('Current Highest Leading Bid');
        $response->assertSee('1,800.00');
        // Tile 2: Time Remaining
        $response->assertSee('Time Remaining');
        // Tile 3: Reserve Spec
        $response->assertSee('Reserve Spec');
        // Tile 4: Telemetry / Bids placed
        $response->assertSee('Bids Placed');
    }

    /**
     * Test 1.14: Master auctions registry renders all seller auctions.
     * Feature 43
     */
    public function test_tier1_master_auctions_registry_renders_all_seller_auctions(): void
    {
        $seller = $this->createApprovedSeller();
        $product1 = $this->createProductRecord($seller, 'Product Lot One', 1000.00);
        $product2 = $this->createProductRecord($seller, 'Product Lot Two', 2000.00);

        $this->createAuctionRecord($seller, $product1, ['status' => 'live']);
        $this->createAuctionRecord($seller, $product2, ['status' => 'scheduled']);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.auctions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('seller.auctions.index');
        $response->assertSee('Product Lot One');
        $response->assertSee('Product Lot Two');
        $response->assertSee('All Auctions Master Registry');
    }

    // =========================================================================
    // SECTION 2: TIER 2 - BOUNDARY SECURITY & TENANT ISOLATION
    // =========================================================================

    /**
     * Test 2.1: Strict tenant isolation: Seller A cannot view or mutate Seller B profile.
     */
    public function test_tier2_strict_tenant_isolation_seller_a_cannot_view_or_edit_seller_b_profile(): void
    {
        $sellerA = $this->createApprovedSeller([], ['shop_name' => 'Shop A']);
        $sellerB = $this->createApprovedSeller([], ['shop_name' => 'Shop B']);

        $response = $this->actingAs($sellerA, 'seller')->get(route('seller.account.profile'));
        $response->assertStatus(200);
        $response->assertSee('Shop A');
        $response->assertDontSee('Shop B');

        // Mutate profile while acting as Seller A
        $this->actingAs($sellerA, 'seller')
            ->put(route('seller.account.profile.update'), [
                'shop_name' => 'Shop A Updated',
            ]);

        $this->assertEquals('Shop A Updated', $sellerA->fresh()->sellerProfile->shop_name);
        $this->assertEquals('Shop B', $sellerB->fresh()->sellerProfile->shop_name);
    }

    /**
     * Test 2.2: Strict tenant isolation: Seller A cannot view Seller B auction details.
     */
    public function test_tier2_strict_tenant_isolation_seller_a_cannot_view_seller_b_auction(): void
    {
        $sellerA = $this->createApprovedSeller();
        $sellerB = $this->createApprovedSeller();

        $productB = $this->createProductRecord($sellerB, 'Seller B Mango Lot', 2000.00);
        $auctionB = $this->createAuctionRecord($sellerB, $productB);

        $response = $this->actingAs($sellerA, 'seller')
            ->get(route('seller.auctions.show', $auctionB));

        $response->assertStatus(403);
    }

    /**
     * Test 2.3: Strict tenant isolation: Seller A cannot cancel Seller B auction.
     */
    public function test_tier2_strict_tenant_isolation_seller_a_cannot_cancel_seller_b_auction(): void
    {
        $sellerA = $this->createApprovedSeller();
        $sellerB = $this->createApprovedSeller();

        $productB = $this->createProductRecord($sellerB, 'Seller B Lot', 2000.00);
        $auctionB = $this->createAuctionRecord($sellerB, $productB, ['status' => 'scheduled']);

        $response = $this->actingAs($sellerA, 'seller')
            ->post(route('seller.auctions.cancel', $auctionB));

        $response->assertStatus(403);
        $this->assertEquals('scheduled', $auctionB->fresh()->status);
    }

    /**
     * Test 2.4: Strict tenant isolation: Master table only displays own auctions.
     */
    public function test_tier2_strict_tenant_isolation_master_table_only_displays_own_auctions(): void
    {
        $sellerA = $this->createApprovedSeller();
        $sellerB = $this->createApprovedSeller();

        $productA = $this->createProductRecord($sellerA, 'Unique Lot Alpha 99', 1000.00);
        $productB = $this->createProductRecord($sellerB, 'Unique Lot Beta 88', 2000.00);

        $this->createAuctionRecord($sellerA, $productA);
        $this->createAuctionRecord($sellerB, $productB);

        $response = $this->actingAs($sellerA, 'seller')->get(route('seller.auctions.index'));

        $response->assertStatus(200);
        $response->assertSee('Unique Lot Alpha 99');
        $response->assertDontSee('Unique Lot Beta 88');
    }

    /**
     * Test 2.5: Seller cannot create auction for another seller's product.
     */
    public function test_tier2_seller_cannot_create_auction_for_another_sellers_product(): void
    {
        $sellerA = $this->createApprovedSeller();
        $sellerB = $this->createApprovedSeller();

        $productB = $this->createProductRecord($sellerB, 'Seller B Exclusive', 2000.00);

        $payload = [
            'product_id'        => $productB->id,
            'starting_price'    => 1500.00,
            'reserve_price'     => 2500.00,
            'minimum_increment' => 100.00,
            'starts_at'         => now()->addHour()->toDateTimeString(),
            'ends_at'           => now()->addHours(3)->toDateTimeString(),
        ];

        $response = $this->actingAs($sellerA, 'seller')
            ->post(route('seller.auctions.store'), $payload);

        $response->assertSessionHasErrors('product_id');
        $this->assertDatabaseMissing('auctions', [
            'seller_id'  => $sellerA->sellerProfile->id,
            'product_id' => $productB->id,
        ]);
    }

    /**
     * Test 2.6: Unapproved pending seller is redirected to pending gate.
     */
    public function test_tier2_unapproved_pending_seller_is_redirected_to_pending_gate(): void
    {
        $pendingSeller = $this->createPendingSeller();

        $response = $this->actingAs($pendingSeller, 'seller')
            ->get(route('seller.auctions.index'));

        $response->assertRedirect(route('seller.pending'));
    }

    /**
     * Test 2.7: Unauthenticated guest is redirected to login.
     */
    public function test_tier2_unauthenticated_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('seller.auctions.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test 2.8: Regular customer is denied access to seller auctions.
     */
    public function test_tier2_regular_customer_is_denied_access_to_seller_auctions(): void
    {
        $buyer = $this->createBuyer();

        $response = $this->actingAs($buyer, 'seller')->get(route('seller.auctions.index'));
        $this->assertTrue($response->isRedirect() || $response->status() === 403);
    }

    /**
     * Test 2.9: Password update rejects incorrect current password.
     */
    public function test_tier2_password_update_rejects_incorrect_current_password(): void
    {
        $seller = $this->createApprovedSeller([
            'password' => Hash::make('Password123!'),
        ]);

        $payload = [
            'current_password'      => 'WrongPassword!',
            'password'              => 'ValidNewPassword$2026',
            'password_confirmation' => 'ValidNewPassword$2026',
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.security.update'), $payload);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('Password123!', $seller->fresh()->password));
    }

    /**
     * Test 2.10: Password update rejects weak passwords failing complexity rules.
     */
    public function test_tier2_password_update_rejects_weak_passwords_failing_complexity(): void
    {
        $seller = $this->createApprovedSeller([
            'password' => Hash::make('Password123!'),
        ]);

        // Missing uppercase and special characters
        $payload = [
            'current_password'      => 'Password123!',
            'password'              => 'weakpass123',
            'password_confirmation' => 'weakpass123',
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.security.update'), $payload);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test 2.11: Password update rejects mismatched password confirmation.
     */
    public function test_tier2_password_update_rejects_mismatched_password_confirmation(): void
    {
        $seller = $this->createApprovedSeller([
            'password' => Hash::make('Password123!'),
        ]);

        $payload = [
            'current_password'      => 'Password123!',
            'password'              => 'ComplexPass$2026',
            'password_confirmation' => 'DifferentPass$2026',
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.security.update'), $payload);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test 2.12: Auction creation rejects negative or zero starting price.
     */
    public function test_tier2_auction_creation_rejects_negative_or_zero_starting_price(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Test Product', 1000.00);

        $payload = [
            'product_id'        => $product->id,
            'starting_price'    => 0,
            'minimum_increment' => 10,
            'starts_at'         => now()->addHour()->toDateTimeString(),
            'ends_at'           => now()->addHours(2)->toDateTimeString(),
        ];

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.store'), $payload);

        $response->assertSessionHasErrors('starting_price');
    }

    /**
     * Test 2.13: Auction creation rejects reserve price lower than starting price.
     */
    public function test_tier2_auction_creation_rejects_reserve_price_lower_than_starting_price(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Test Product', 1000.00);

        $payload = [
            'product_id'        => $product->id,
            'starting_price'    => 2000.00,
            'reserve_price'     => 1000.00, // < starting_price
            'minimum_increment' => 10,
            'starts_at'         => now()->addHour()->toDateTimeString(),
            'ends_at'           => now()->addHours(2)->toDateTimeString(),
        ];

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.store'), $payload);

        $response->assertSessionHasErrors('reserve_price');
    }

    /**
     * Test 2.14: Auction creation rejects end date before start date.
     */
    public function test_tier2_auction_creation_rejects_end_date_before_start_date(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Test Product', 1000.00);

        $payload = [
            'product_id'        => $product->id,
            'starting_price'    => 1000.00,
            'minimum_increment' => 10,
            'starts_at'         => now()->addHours(5)->toDateTimeString(),
            'ends_at'           => now()->addHours(2)->toDateTimeString(), // before starts_at
        ];

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.store'), $payload);

        $response->assertSessionHasErrors('ends_at');
    }

    /**
     * Test 2.15: Auction creation rejects non-existent product id.
     */
    public function test_tier2_auction_creation_rejects_non_existent_product_id(): void
    {
        $seller = $this->createApprovedSeller();

        $payload = [
            'product_id'        => 999999,
            'starting_price'    => 1000.00,
            'minimum_increment' => 10,
            'starts_at'         => now()->addHour()->toDateTimeString(),
            'ends_at'           => now()->addHours(2)->toDateTimeString(),
        ];

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.store'), $payload);

        $response->assertSessionHasErrors('product_id');
    }

    /**
     * Test 2.16: Location validation rejects out of bounds coordinates.
     */
    public function test_tier2_location_validation_rejects_out_of_bounds_coordinates(): void
    {
        $seller = $this->createApprovedSeller();

        $payload = [
            'address'             => 'Invalid Coords Plot',
            'latitude'            => 120.00, // > 90
            'longitude'           => -250.00, // < -180
            'operating_radius_km' => -5,
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.location.update'), $payload);

        $response->assertSessionHasErrors(['latitude', 'longitude', 'operating_radius_km']);
    }

    // =========================================================================
    // SECTION 3: TIER 3 - STATE MACHINES & SAFETY GUARDRAILS
    // =========================================================================

    /**
     * Test 3.1: Reserve Met indicator renders "Reserve Met" when highest bid exceeds reserve.
     * Feature 39
     */
    public function test_tier3_reserve_met_indicator_renders_reserve_met_when_highest_bid_exceeds_reserve(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Premium Alphonso Crate', 1500.00);

        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 1500.00,
            'reserve_price'  => 2500.00,
            'current_price'  => 1500.00,
            'status'         => 'live',
        ]);

        $this->createAuctionBid($auction, $buyer, 2850.00);

        $this->assertTrue($auction->fresh()->isReserveMet());

        $response = $this->actingAs($seller, 'seller')
            ->get(route('seller.auctions.show', $auction));

        $response->assertStatus(200);
        $response->assertSee('Reserve Met', false);
        $response->assertDontSee('Reserve Not Met', false);
    }

    /**
     * Test 3.2: Reserve Met indicator renders "Reserve Met" when highest bid exactly equals reserve.
     * Feature 39
     */
    public function test_tier3_reserve_met_indicator_renders_reserve_met_when_highest_bid_equals_reserve(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Alphonso Lot', 1500.00);

        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 1500.00,
            'reserve_price'  => 2500.00,
            'current_price'  => 1500.00,
            'status'         => 'live',
        ]);

        $this->createAuctionBid($auction, $buyer, 2500.00);

        $this->assertTrue($auction->fresh()->isReserveMet());

        $response = $this->actingAs($seller, 'seller')
            ->get(route('seller.auctions.show', $auction));

        $response->assertStatus(200);
        $response->assertSee('Reserve Met', false);
    }

    /**
     * Test 3.3: Reserve Met indicator renders "Reserve Not Met" when highest bid is below reserve.
     * Feature 39
     */
    public function test_tier3_reserve_met_indicator_renders_reserve_not_met_when_highest_bid_below_reserve(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Alphonso Lot', 1500.00);

        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 1500.00,
            'reserve_price'  => 2500.00,
            'current_price'  => 1500.00,
            'status'         => 'live',
        ]);

        $this->createAuctionBid($auction, $buyer, 2200.00);

        $this->assertFalse($auction->fresh()->isReserveMet());

        $response = $this->actingAs($seller, 'seller')
            ->get(route('seller.auctions.show', $auction));

        $response->assertStatus(200);
        $response->assertSee('Reserve Not Met', false);
    }

    /**
     * Test 3.4: Anonymized live bid stream strictly masks bidder identity.
     * Feature 41
     */
    public function test_tier3_anonymized_live_bid_stream_masks_bidder_identity(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer([
            'name'  => 'Vikramaditya Roy',
            'email' => 'vikram@agromart.com',
            'phone' => '9876543210',
        ]);
        $product = $this->createProductRecord($seller, 'Privacy Tested Lot', 1000.00);
        $auction = $this->createAuctionRecord($seller, $product, ['status' => 'live']);

        $this->createAuctionBid($auction, $buyer, 1500.00);

        $response = $this->actingAs($seller, 'seller')
            ->get(route('seller.auctions.show', $auction));

        $response->assertStatus(200);
        $response->assertSee('Bidder #');
        $response->assertDontSee('vikram@agromart.com');
        $response->assertDontSee('Vikramaditya Roy');
    }

    /**
     * Test 3.5: Auction cancellation is permitted when zero bids exist.
     * Feature 42
     */
    public function test_tier3_auction_cancellation_allowed_when_zero_bids_exist(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Zero Bid Lot', 1000.00);
        $auction = $this->createAuctionRecord($seller, $product, ['status' => 'scheduled']);

        $this->assertTrue($auction->canBeCancelled());

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));

        $response->assertRedirect(route('seller.auctions.index'));
        $response->assertSessionHas('success');
        $this->assertEquals('cancelled', $auction->fresh()->status);
    }

    /**
     * Test 3.6: Auction cancellation is strictly blocked once bids exist.
     * Feature 42
     */
    public function test_tier3_auction_cancellation_strictly_blocked_once_bids_exist(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Bidded Lot', 1000.00);
        $auction = $this->createAuctionRecord($seller, $product, ['status' => 'live']);

        $this->createAuctionBid($auction, $buyer, 1200.00);

        $this->assertFalse($auction->fresh()->canBeCancelled());

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));

        $this->assertTrue($response->isRedirect() || $response->status() === 403);
        $this->assertEquals('live', $auction->fresh()->status);
    }

    /**
     * Test 3.7: Auction cancellation strictly blocked when reserve met.
     * Feature 42
     */
    public function test_tier3_auction_cancellation_strictly_blocked_when_reserve_met(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'High Demand Lot', 1000.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 1000.00,
            'reserve_price'  => 1500.00,
            'status'         => 'live',
        ]);

        $this->createAuctionBid($auction, $buyer, 1800.00);

        $this->assertFalse($auction->fresh()->canBeCancelled());

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));

        $this->assertEquals('live', $auction->fresh()->status);
    }

    /**
     * Test 3.8: Terminal auction status (ended/cancelled) cannot be re-cancelled.
     * Feature 42
     */
    public function test_tier3_terminal_auction_status_cannot_be_re_cancelled(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Ended Lot', 1000.00);
        $auction = $this->createAuctionRecord($seller, $product, ['status' => 'ended']);

        $this->assertFalse($auction->canBeCancelled());

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));

        $this->assertEquals('ended', $auction->fresh()->status);
    }

    // =========================================================================
    // SECTION 4: TIER 4 - RESILIENCE, INTEGRATION & ADVERSARIAL HARDENING
    // =========================================================================

    /**
     * Test 4.1: Master registry status tab filtering.
     * Feature 43
     */
    public function test_tier4_master_registry_status_tab_filtering(): void
    {
        $seller = $this->createApprovedSeller();
        $p1 = $this->createProductRecord($seller, 'Scheduled Item', 1000.00);
        $p2 = $this->createProductRecord($seller, 'Live Item', 2000.00);

        $this->createAuctionRecord($seller, $p1, ['status' => 'scheduled']);
        $this->createAuctionRecord($seller, $p2, ['status' => 'live']);

        // Filter: scheduled
        $response = $this->actingAs($seller, 'seller')
            ->get(route('seller.auctions.index', ['status' => 'scheduled']));
        $response->assertStatus(200);
        $response->assertSee('Scheduled Item');
        $response->assertDontSee('Live Item');

        // Filter: live
        $responseLive = $this->actingAs($seller, 'seller')
            ->get(route('seller.auctions.index', ['status' => 'live']));
        $responseLive->assertStatus(200);
        $responseLive->assertSee('Live Item');
        $responseLive->assertDontSee('Scheduled Item');
    }

    /**
     * Test 4.2: Zero-state resilience: Seller with no auctions renders cleanly.
     * Feature 43
     */
    public function test_tier4_zero_state_resilience_seller_with_no_auctions_renders_cleanly(): void
    {
        $seller = $this->createApprovedSeller();

        $response = $this->actingAs($seller, 'seller')->get(route('seller.auctions.index'));

        $response->assertStatus(200);
        $response->assertSee('No auctions found matching this filter');
        $response->assertSee('Create Auction');
    }

    /**
     * Test 4.3: Zero-state resilience: Live terminal with no active lot renders fallback gracefully.
     * Feature 38
     */
    public function test_tier4_zero_state_resilience_live_terminal_with_no_active_lot(): void
    {
        $seller = $this->createApprovedSeller();

        $response = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));

        $response->assertStatus(200);
        $response->assertSee('No Active Live Auctions Currently');
    }

    /**
     * Test 4.4: Profile update XSS sanitization.
     */
    public function test_tier4_profile_update_xss_sanitization(): void
    {
        $seller = $this->createApprovedSeller();

        $payload = [
            'shop_name' => '<script>alert("pwned")</script> Farm',
            'bio'       => 'Bio with <img src=x onerror=alert(1)> description',
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), $payload);

        $response->assertRedirect(route('seller.account.profile'));

        $viewResponse = $this->actingAs($seller, 'seller')->get(route('seller.account.profile'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertDontSee('<script>alert("pwned")</script>', false);
        $viewResponse->assertSee('&lt;script&gt;alert(&quot;pwned&quot;)&lt;/script&gt; Farm', false);
    }

    /**
     * Test 4.5: Complete auction lifecycle from creation to bidding to reserve met to cancellation guardrail.
     */
    public function test_tier4_full_auction_lifecycle_from_creation_to_bidding_to_reserve_to_guardrail(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer1 = $this->createBuyer();
        $buyer2 = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Lifecycle Alphonso Lot', 1000.00);

        // 1. Create auction
        $response = $this->actingAs($seller, 'seller')->post(route('seller.auctions.store'), [
            'product_id'        => $product->id,
            'starting_price'    => 1000.00,
            'reserve_price'     => 1500.00,
            'minimum_increment' => 50.00,
            'starts_at'         => now()->subMinute()->toDateTimeString(),
            'ends_at'           => now()->addHours(2)->toDateTimeString(),
        ]);
        $response->assertRedirect(route('seller.auctions.index'));

        $auction = Auction::where('product_id', $product->id)->first();
        $this->assertNotNull($auction);
        $this->assertEquals('live', $auction->status);
        $this->assertTrue($auction->canBeCancelled());
        $this->assertFalse($auction->isReserveMet());

        // 2. Buyer 1 places bid below reserve
        $this->createAuctionBid($auction, $buyer1, 1200.00);
        $auction->refresh();
        $this->assertFalse($auction->canBeCancelled());
        $this->assertFalse($auction->isReserveMet());

        // 3. Attempt cancellation -> must be blocked
        $cancelResponse = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));
        $this->assertEquals('live', $auction->fresh()->status);

        // 4. Buyer 2 places bid meeting reserve
        $this->createAuctionBid($auction, $buyer2, 1600.00);
        $auction->refresh();
        $this->assertTrue($auction->isReserveMet());
        $this->assertFalse($auction->canBeCancelled());

        // 5. Inspect view has reserve met badge
        $showResponse = $this->actingAs($seller, 'seller')
            ->get(route('seller.auctions.show', $auction));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Reserve Met', false);
    }
}
