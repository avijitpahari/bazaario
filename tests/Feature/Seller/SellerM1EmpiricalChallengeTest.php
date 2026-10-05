<?php

namespace Tests\Feature\Seller;

use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerM1EmpiricalChallengeTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // SECTION 1: ACCESS CONTROL GATE EMPIRICAL VERIFICATION
    // =========================================================================

    /**
     * Scenario 1.1: Unauthenticated guest hits /seller/dashboard -> must redirect to login.
     */
    public function test_gate_unauthenticated_guest_hits_dashboard_redirects_to_login(): void
    {
        $response = $this->get('/seller/dashboard');
        $response->assertRedirect(route('login'));
    }

    /**
     * Scenario 1.2: Unauthenticated guest hits /seller/pending -> must redirect to login.
     */
    public function test_gate_unauthenticated_guest_hits_pending_redirects_to_login(): void
    {
        $response = $this->get('/seller/pending');
        $response->assertRedirect(route('login'));
    }

    /**
     * Scenario 1.3: Unauthenticated guest hits /seller/onboarding -> must redirect to login.
     */
    public function test_gate_unauthenticated_guest_hits_onboarding_redirects_to_login(): void
    {
        $response = $this->get('/seller/onboarding');
        $response->assertRedirect(route('login'));
    }

    /**
     * Scenario 1.4: Regular user (role='user') hits /seller/dashboard -> must be rejected/redirected.
     */
    public function test_gate_regular_user_hits_dashboard_is_redirected(): void
    {
        $user = User::factory()->create([
            'role'   => 'user',
            'status' => 'active',
        ]);

        // When authenticated via seller guard
        $response = $this->actingAs($user, 'seller')->get('/seller/dashboard');
        $response->assertRedirect(route('products.index'));

        // When authenticated via user guard
        $responseUserGuard = $this->actingAs($user, 'user')->get('/seller/dashboard');
        $responseUserGuard->assertRedirect(route('products.index'));
    }

    /**
     * Scenario 1.5: Admin user (role='admin') hits /seller/dashboard -> redirects to admin dashboard.
     */
    public function test_gate_admin_user_hits_dashboard_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role'   => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin, 'seller')->get('/seller/dashboard');
        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Scenario 1.6: Inactive seller user hits /seller/dashboard -> logged out and redirected to login.
     */
    public function test_gate_inactive_seller_hits_dashboard_logged_out_and_redirects(): void
    {
        $seller = User::factory()->create([
            'role'   => 'seller',
            'status' => 'inactive',
        ]);

        $response = $this->actingAs($seller, 'seller')->get('/seller/dashboard');
        $response->assertRedirect(route('login'));
        $this->assertGuest('seller');
    }

    /**
     * Scenario 1.7: Pending seller (status='pending') hits /seller/dashboard -> must redirect to /seller/pending.
     */
    public function test_gate_pending_seller_hits_dashboard_redirects_to_pending(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id'   => $seller->id,
            'shop_name' => 'Waiting Farm',
            'shop_slug' => 'waiting-farm',
            'status'    => 'pending',
        ]);

        $response = $this->actingAs($seller, 'seller')->get('/seller/dashboard');
        $response->assertRedirect(route('seller.pending'));
        $response->assertSessionHas('warning');
    }

    /**
     * Scenario 1.8: Rejected seller (status='rejected') hits /seller/dashboard -> must redirect to /seller/pending.
     */
    public function test_gate_rejected_seller_hits_dashboard_redirects_to_pending(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id'   => $seller->id,
            'shop_name' => 'Rejected Farm',
            'shop_slug' => 'rejected-farm',
            'status'    => 'rejected',
        ]);

        $response = $this->actingAs($seller, 'seller')->get('/seller/dashboard');
        $response->assertRedirect(route('seller.pending'));
        $response->assertSessionHas('warning');
    }

    /**
     * Scenario 1.9: Suspended seller (status='suspended') hits /seller/dashboard -> must redirect to /seller/pending.
     */
    public function test_gate_suspended_seller_hits_dashboard_redirects_to_pending(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id'   => $seller->id,
            'shop_name' => 'Suspended Shop',
            'shop_slug' => 'suspended-shop',
            'status'    => 'suspended',
        ]);

        $response = $this->actingAs($seller, 'seller')->get('/seller/dashboard');
        $response->assertRedirect(route('seller.pending'));
        $response->assertSessionHas('warning');
    }

    /**
     * Scenario 1.10: Seller with missing SellerProfile record hits /seller/dashboard -> must redirect to /seller/pending.
     */
    public function test_gate_seller_without_profile_hits_dashboard_redirects_to_pending(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        // No SellerProfile created

        $response = $this->actingAs($seller, 'seller')->get('/seller/dashboard');
        $response->assertRedirect(route('seller.pending'));
        $response->assertSessionHas('warning');
    }

    /**
     * Scenario 1.11: Approved seller (status='approved') hits /seller/dashboard -> must return HTTP 200.
     */
    public function test_gate_approved_seller_hits_dashboard_returns_http_200(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id'   => $seller->id,
            'shop_name' => 'Approved Market Stall',
            'shop_slug' => 'approved-market-stall',
            'status'    => 'approved',
        ]);

        $response = $this->actingAs($seller, 'seller')->get('/seller/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Welcome back, Approved Market Stall');
        $response->assertSee('Merchant Operations • Live Feed');
    }

    /**
     * Scenario 1.12: Approved seller hits /seller/pending -> must redirect to /seller/dashboard.
     */
    public function test_gate_approved_seller_hits_pending_redirects_to_dashboard(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id'   => $seller->id,
            'shop_name' => 'Approved Market Stall',
            'shop_slug' => 'approved-market-stall',
            'status'    => 'approved',
        ]);

        $response = $this->actingAs($seller, 'seller')->get('/seller/pending');
        $response->assertRedirect(route('seller.dashboard'));
    }

    /**
     * Scenario 1.13: Approved seller hits /seller/onboarding -> must redirect to /seller/dashboard.
     */
    public function test_gate_approved_seller_hits_onboarding_redirects_to_dashboard(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id'   => $seller->id,
            'shop_name' => 'Approved Market Stall',
            'shop_slug' => 'approved-market-stall',
            'status'    => 'approved',
        ]);

        $response = $this->actingAs($seller, 'seller')->get('/seller/onboarding');
        $response->assertRedirect(route('seller.dashboard'));
    }

    /**
     * Scenario 1.14: Pending seller hits /seller/pending -> returns HTTP 200 without redirect loop.
     */
    public function test_gate_pending_seller_hits_pending_returns_http_200(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id'   => $seller->id,
            'shop_name' => 'Waiting Farm',
            'shop_slug' => 'waiting-farm',
            'status'    => 'pending',
        ]);

        $response = $this->actingAs($seller, 'seller')->get('/seller/pending');
        $response->assertStatus(200);
    }

    /**
     * Scenario 1.15: Pending seller hits /seller/onboarding -> returns HTTP 200 without redirect loop.
     */
    public function test_gate_pending_seller_hits_onboarding_returns_http_200(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id'   => $seller->id,
            'shop_name' => 'Waiting Farm',
            'shop_slug' => 'waiting-farm',
            'status'    => 'pending',
        ]);

        $response = $this->actingAs($seller, 'seller')->get('/seller/onboarding');
        $response->assertStatus(200);
    }

    /**
     * Scenario 1.16: Operational sub-routes (/seller/products, /seller/orders, etc.) redirect pending seller.
     */
    public function test_gate_pending_seller_blocked_from_all_operational_subroutes(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id'   => $seller->id,
            'shop_name' => 'Pending Seller',
            'shop_slug' => 'pending-seller',
            'status'    => 'pending',
        ]);

        $routes = [
            '/seller/products',
            '/seller/orders',
            '/seller/payouts',
            '/seller/auctions',
            '/seller/account/profile',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($seller, 'seller')->get($route);
            $response->assertRedirect(route('seller.pending'));
        }
    }

    // =========================================================================
    // SECTION 2: ONBOARDING FORM VALIDATION EMPIRICAL STRESS-TESTING
    // =========================================================================

    /**
     * Scenario 2.1: Submit invalid seller types -> must fail validation.
     */
    public function test_validation_rejects_invalid_seller_types(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);

        $invalidTypes = ['Hacker', 'corporation', 'broker', 'Wholesaler', 'invalid_type', '123', ''];

        foreach ($invalidTypes as $invalidType) {
            $payload = [
                'seller_type' => $invalidType,
                'shop_name'   => 'Test Valid Shop Name',
                'address'     => '123 Test Street',
                'city'        => 'Kolkata',
                'state'       => 'West Bengal',
                'latitude'    => 22.5726,
                'longitude'   => 88.3639,
            ];

            $response = $this->actingAs($seller, 'seller')->post('/seller/onboarding', $payload);
            $response->assertSessionHasErrors(['seller_type']);
        }
    }

    /**
     * Scenario 2.2: Submit valid seller types (standard and normalized forms) -> passes validation.
     */
    public function test_validation_accepts_all_valid_seller_types_and_normalizes(): void
    {
        $validTypes = [
            'Farmer'       => 'Farmer',
            'farmer'       => 'Farmer',
            'Kirana Store' => 'Kirana Store',
            'kirana'       => 'Kirana Store',
            'kirana store' => 'Kirana Store',
            'Dark Store'   => 'Dark Store',
            'darkstore'    => 'Dark Store',
            'dark store'   => 'Dark Store',
            'Individual'   => 'Individual',
            'individual'   => 'Individual',
        ];

        foreach ($validTypes as $input => $expectedDbValue) {
            $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);

            $payload = [
                'seller_type' => $input,
                'shop_name'   => 'Valid Shop ' . uniqid(),
                'address'     => '123 Test Road',
                'city'        => 'Contai',
                'state'       => 'West Bengal',
                'latitude'    => 21.7781,
                'longitude'   => 87.7516,
            ];

            $response = $this->actingAs($seller, 'seller')->post('/seller/onboarding', $payload);
            $response->assertRedirect(route('seller.pending'));
            $response->assertSessionHasNoErrors();

            $profile = SellerProfile::where('user_id', $seller->id)->first();
            $this->assertNotNull($profile);
            $this->assertEquals($expectedDbValue, $profile->seller_type);
        }
    }

    /**
     * Scenario 2.3: Submit invalid latitude coordinates (> 90, < -90, non-numeric) -> fails validation.
     */
    public function test_validation_rejects_invalid_latitude(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);

        $invalidLats = [90.0001, 91.0, 150.5, -90.0001, -91.0, -180.0, 'not-a-number', ''];

        foreach ($invalidLats as $lat) {
            $payload = [
                'seller_type' => 'Farmer',
                'shop_name'   => 'Valid Shop Name',
                'address'     => '123 Test Street',
                'city'        => 'Contai',
                'state'       => 'West Bengal',
                'latitude'    => $lat,
                'longitude'   => 87.7516,
            ];

            $response = $this->actingAs($seller, 'seller')->post('/seller/onboarding', $payload);
            $response->assertSessionHasErrors(['latitude']);
        }
    }

    /**
     * Scenario 2.4: Submit invalid longitude coordinates (> 180, < -180, non-numeric) -> fails validation.
     */
    public function test_validation_rejects_invalid_longitude(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);

        $invalidLngs = [180.0001, 181.0, 250.0, -180.0001, -181.0, -200.0, 'not-a-number', ''];

        foreach ($invalidLngs as $lng) {
            $payload = [
                'seller_type' => 'Farmer',
                'shop_name'   => 'Valid Shop Name',
                'address'     => '123 Test Street',
                'city'        => 'Contai',
                'state'       => 'West Bengal',
                'latitude'    => 21.7781,
                'longitude'   => $lng,
            ];

            $response = $this->actingAs($seller, 'seller')->post('/seller/onboarding', $payload);
            $response->assertSessionHasErrors(['longitude']);
        }
    }

    /**
     * Scenario 2.5: Valid boundary coordinates (-90, 90, -180, 180, 0, 0) -> passes validation.
     */
    public function test_validation_accepts_boundary_coordinates(): void
    {
        $boundaries = [
            [-90.0, -180.0],
            [90.0, 180.0],
            [0.0, 0.0],
            [-89.9999, 179.9999],
        ];

        foreach ($boundaries as [$lat, $lng]) {
            $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);

            $payload = [
                'seller_type' => 'Dark Store',
                'shop_name'   => 'Boundary Store ' . uniqid(),
                'address'     => 'Edge Road',
                'city'        => 'Equator City',
                'state'       => 'Equator State',
                'latitude'    => $lat,
                'longitude'   => $lng,
            ];

            $response = $this->actingAs($seller, 'seller')->post('/seller/onboarding', $payload);
            $response->assertRedirect(route('seller.pending'));
            $response->assertSessionHasNoErrors();
        }
    }

    /**
     * Scenario 2.6: Empty required fields -> fails validation with specific error keys.
     */
    public function test_validation_rejects_empty_required_fields(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);

        $response = $this->actingAs($seller, 'seller')->post('/seller/onboarding', []);

        $response->assertSessionHasErrors([
            'seller_type',
            'shop_name',
            'address',
            'city',
            'state',
            'latitude',
            'longitude',
        ]);
    }

    /**
     * Scenario 2.7: Shop name constraints (min:3, max:150) -> enforced.
     */
    public function test_validation_enforces_shop_name_length_bounds(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);

        // Too short (< 3 chars)
        $shortResponse = $this->actingAs($seller, 'seller')->post('/seller/onboarding', [
            'seller_type' => 'Farmer',
            'shop_name'   => 'AB',
            'address'     => '123 Farm Rd',
            'city'        => 'Contai',
            'state'       => 'WB',
            'latitude'    => 21.7781,
            'longitude'   => 87.7516,
        ]);
        $shortResponse->assertSessionHasErrors(['shop_name']);

        // Too long (> 150 chars)
        $longResponse = $this->actingAs($seller, 'seller')->post('/seller/onboarding', [
            'seller_type' => 'Farmer',
            'shop_name'   => str_repeat('A', 151),
            'address'     => '123 Farm Rd',
            'city'        => 'Contai',
            'state'       => 'WB',
            'latitude'    => 21.7781,
            'longitude'   => 87.7516,
        ]);
        $longResponse->assertSessionHasErrors(['shop_name']);
    }

    /**
     * Scenario 2.8: Storefront image file validation (reject non-images and oversize).
     */
    public function test_validation_rejects_non_image_files_for_storefront(): void
    {
        Storage::fake('public');
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);

        $fakeScript = UploadedFile::fake()->create('malicious.php', 100, 'application/x-php');

        $response = $this->actingAs($seller, 'seller')->post('/seller/onboarding', [
            'seller_type'      => 'Farmer',
            'shop_name'        => 'Security Test Shop',
            'storefront_image' => $fakeScript,
            'address'          => '123 Farm Rd',
            'city'             => 'Contai',
            'state'            => 'WB',
            'latitude'         => 21.7781,
            'longitude'        => 87.7516,
        ]);

        $response->assertSessionHasErrors(['storefront_image']);
    }

    /**
     * Scenario 2.9: JSON request handling returns 401/403 with appropriate json payload.
     */
    public function test_json_api_responses_for_access_control(): void
    {
        // Unauthenticated JSON
        $unauthResponse = $this->getJson('/seller/dashboard');
        $unauthResponse->assertStatus(401);
        $unauthResponse->assertJson(['message' => 'Unauthenticated.']);

        // Pending seller JSON
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id'   => $seller->id,
            'shop_name' => 'Pending API Stall',
            'shop_slug' => 'pending-api-stall',
            'status'    => 'pending',
        ]);

        $pendingResponse = $this->actingAs($seller, 'seller')->getJson('/seller/dashboard');
        $pendingResponse->assertStatus(403);
        $pendingResponse->assertJson([
            'message' => 'Your seller account is waiting for admin approval.',
            'status'  => 'pending',
        ]);
    }

    /**
     * Scenario 2.10: XSS payloads in shop_name are escaped when rendered in views.
     */
    public function test_security_xss_payload_in_shop_name_is_html_escaped(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);

        $payload = [
            'seller_type' => 'Farmer',
            'shop_name'   => '<script>alert("xss")</script> Agro',
            'address'     => '123 Farm Rd',
            'city'        => 'Contai',
            'state'       => 'WB',
            'latitude'    => 21.7781,
            'longitude'   => 87.7516,
        ];

        $response = $this->actingAs($seller, 'seller')->post('/seller/onboarding', $payload);
        $response->assertRedirect(route('seller.pending'));

        // Visit pending page to verify raw XSS script tag is NEVER rendered
        $pendingView = $this->actingAs($seller, 'seller')->get('/seller/pending');
        $pendingView->assertStatus(200);
        $pendingView->assertDontSee('<script>alert("xss")</script>', false);
        $pendingView->assertSee('alert');
    }

    /**
     * Scenario 2.11: Deterministic slug generation handles collisions across multiple sellers.
     */
    public function test_slug_generation_resolves_collisions(): void
    {
        $seller1 = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $seller2 = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $seller3 = User::factory()->create(['role' => 'seller', 'status' => 'active']);

        $payload = [
            'seller_type' => 'Kirana Store',
            'shop_name'   => 'Super Bazaar',
            'address'     => 'Main Market',
            'city'        => 'Contai',
            'state'       => 'West Bengal',
            'latitude'    => 21.7781,
            'longitude'   => 87.7516,
        ];

        $this->actingAs($seller1, 'seller')->post('/seller/onboarding', $payload);
        $this->actingAs($seller2, 'seller')->post('/seller/onboarding', $payload);
        $this->actingAs($seller3, 'seller')->post('/seller/onboarding', $payload);

        $profile1 = SellerProfile::where('user_id', $seller1->id)->first();
        $profile2 = SellerProfile::where('user_id', $seller2->id)->first();
        $profile3 = SellerProfile::where('user_id', $seller3->id)->first();

        $this->assertEquals('super-bazaar', $profile1->shop_slug);
        $this->assertEquals('super-bazaar-1', $profile2->shop_slug);
        $this->assertEquals('super-bazaar-2', $profile3->shop_slug);
    }

    /**
     * Scenario 2.12: Multiple submissions by same seller update profile rather than duplicate records.
     */
    public function test_resubmission_updates_existing_profile_without_duplication(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);

        $payload1 = [
            'seller_type' => 'Farmer',
            'shop_name'   => 'First Farm Name',
            'address'     => '123 Farm Rd',
            'city'        => 'Contai',
            'state'       => 'WB',
            'latitude'    => 21.7781,
            'longitude'   => 87.7516,
        ];
        $this->actingAs($seller, 'seller')->post('/seller/onboarding', $payload1);
        $this->assertEquals(1, SellerProfile::where('user_id', $seller->id)->count());

        $payload2 = [
            'seller_type' => 'Dark Store',
            'shop_name'   => 'Updated Dark Store',
            'address'     => '456 Warehouse Rd',
            'city'        => 'Kolkata',
            'state'       => 'WB',
            'latitude'    => 22.5726,
            'longitude'   => 88.3639,
        ];
        $this->actingAs($seller, 'seller')->post('/seller/onboarding', $payload2);

        $this->assertEquals(1, SellerProfile::where('user_id', $seller->id)->count());
        $profile = SellerProfile::where('user_id', $seller->id)->first();
        $this->assertEquals('Dark Store', $profile->seller_type);
        $this->assertEquals('Updated Dark Store', $profile->shop_name);
        $this->assertEquals('Kolkata', $profile->city);
        $this->assertEquals('pending', $profile->status);
    }

    /**
     * Scenario 2.13: Customer role (role='user') cannot submit onboarding form.
     */
    public function test_customer_cannot_submit_seller_onboarding(): void
    {
        $user = User::factory()->create(['role' => 'user', 'status' => 'active']);

        $payload = [
            'seller_type' => 'Farmer',
            'shop_name'   => 'Sneaky Shop',
            'address'     => '123 Fake Rd',
            'city'        => 'Contai',
            'state'       => 'WB',
            'latitude'    => 21.7781,
            'longitude'   => 87.7516,
        ];

        $response = $this->actingAs($user, 'seller')->post('/seller/onboarding', $payload);
        $response->assertRedirect(route('products.index'));

        $this->assertEquals(0, SellerProfile::where('shop_name', 'Sneaky Shop')->count());
    }

    /**
     * Scenario 2.14: Bio length exceeding 1000 characters is rejected by validation.
     */
    public function test_validation_rejects_bio_exceeding_1000_chars(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);

        $payload = [
            'seller_type' => 'Farmer',
            'shop_name'   => 'Valid Shop',
            'bio'         => str_repeat('A', 1001),
            'address'     => '123 Farm Rd',
            'city'        => 'Contai',
            'state'       => 'WB',
            'latitude'    => 21.7781,
            'longitude'   => 87.7516,
        ];

        $response = $this->actingAs($seller, 'seller')->post('/seller/onboarding', $payload);
        $response->assertSessionHasErrors(['bio']);
    }
}
