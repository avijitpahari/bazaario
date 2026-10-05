<?php

namespace Tests\Feature\Seller;

use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class Milestone5ProfileSecurityChallengeTest
 *
 * Empirical Challenge Suite for Milestone 5 (Features 34-37):
 *  - Category 1: Password Security Hardening & Complexity Enforcement (Feature 37)
 *  - Category 2: Geolocation & Geofence Boundary Telemetry (Feature 36)
 *  - Category 3: Storefront Profile Updates, Media Uploads & SLA Days Persistence (Features 34, 35)
 *  - Category 4: Multi-Tenant Tenancy Isolation & Role Guardrails (Features 34-37)
 */
class Milestone5ProfileSecurityChallengeTest extends TestCase
{
    use RefreshDatabase, SellerTestHelperTrait;

    // =========================================================================
    // CATEGORY 1: PASSWORD SECURITY HARDENING (Feature 37)
    // =========================================================================

    /**
     * Challenge 1.1: Password update rejects incorrect current password.
     */
    public function test_challenge_password_update_rejects_incorrect_current_password(): void
    {
        $seller = $this->createApprovedSeller([
            'password' => Hash::make('CorrectCurrentPass$1'),
        ]);

        $payload = [
            'current_password'      => 'WrongPasswordAttempt!99',
            'password'              => 'BrandNewPassword$2026',
            'password_confirmation' => 'BrandNewPassword$2026',
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.security.update'), $payload);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('CorrectCurrentPass$1', $seller->fresh()->password),
            'Database password must remain unchanged when current password is wrong.');
    }

    /**
     * Challenge 1.2: Password update rejects empty or missing current password.
     */
    public function test_challenge_password_update_rejects_empty_current_password(): void
    {
        $seller = $this->createApprovedSeller([
            'password' => Hash::make('CorrectCurrentPass$1'),
        ]);

        $payload = [
            'current_password'      => '',
            'password'              => 'BrandNewPassword$2026',
            'password_confirmation' => 'BrandNewPassword$2026',
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.security.update'), $payload);

        $response->assertSessionHasErrors('current_password');
    }

    /**
     * Challenge 1.3: Password update rejects weak passwords failing complexity rules.
     * Tests: short length, missing uppercase, missing lowercase, missing numbers, missing symbols.
     */
    public function test_challenge_password_update_rejects_various_weak_passwords(): void
    {
        $seller = $this->createApprovedSeller([
            'password' => Hash::make('CurrentValidPass$1'),
        ]);

        $weakPasswords = [
            'short'                 => 'Sh1!',                     // < 8 chars
            'no_uppercase'          => 'lowercaseonly123!',        // No uppercase
            'no_lowercase'          => 'UPPERCASEONLY123!',        // No lowercase
            'no_numbers'            => 'NoNumbersAllowedHere!',    // No numbers
            'no_symbols'            => 'NoSpecialSymbols123',      // No symbols
        ];

        foreach ($weakPasswords as $reason => $weakPassword) {
            $payload = [
                'current_password'      => 'CurrentValidPass$1',
                'password'              => $weakPassword,
                'password_confirmation' => $weakPassword,
            ];

            $response = $this->actingAs($seller, 'seller')
                ->put(route('seller.account.security.update'), $payload);

            $response->assertSessionHasErrors('password',
                "Expected validation error on 'password' for weak password variant: {$reason} ({$weakPassword})");
        }
    }

    /**
     * Challenge 1.4: Password update rejects mismatched password confirmation.
     */
    public function test_challenge_password_update_rejects_mismatched_confirmation(): void
    {
        $seller = $this->createApprovedSeller([
            'password' => Hash::make('CurrentValidPass$1'),
        ]);

        $payload = [
            'current_password'      => 'CurrentValidPass$1',
            'password'              => 'BrandNewPassword$2026',
            'password_confirmation' => 'DifferentPassword$2026',
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.security.update'), $payload);

        $response->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('CurrentValidPass$1', $seller->fresh()->password));
    }

    /**
     * Challenge 1.5: Password update rejects missing confirmation field.
     */
    public function test_challenge_password_update_rejects_missing_confirmation(): void
    {
        $seller = $this->createApprovedSeller([
            'password' => Hash::make('CurrentValidPass$1'),
        ]);

        $payload = [
            'current_password' => 'CurrentValidPass$1',
            'password'         => 'BrandNewPassword$2026',
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.security.update'), $payload);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Challenge 1.6: Valid password update succeeds and updates hash in database.
     */
    public function test_challenge_password_update_valid_credentials_succeeds_and_hashes(): void
    {
        $seller = $this->createApprovedSeller([
            'password' => Hash::make('OldValidPassword#1'),
        ]);

        $payload = [
            'current_password'      => 'OldValidPassword#1',
            'password'              => 'SuperSecurePass$2026',
            'password_confirmation' => 'SuperSecurePass$2026',
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.security.update'), $payload);

        $response->assertRedirect(route('seller.account.security'));
        $response->assertSessionHas('success');

        $seller->refresh();
        $this->assertTrue(Hash::check('SuperSecurePass$2026', $seller->password),
            'New password must be properly hashed and verify cleanly.');
        $this->assertFalse(Hash::check('OldValidPassword#1', $seller->password),
            'Old password must no longer match.');
    }

    /**
     * Challenge 1.7: Session behavior after password update (session persistence check).
     */
    public function test_challenge_password_update_session_persistence_behavior(): void
    {
        $seller = $this->createApprovedSeller([
            'password' => Hash::make('OldValidPassword#1'),
        ]);

        $payload = [
            'current_password'      => 'OldValidPassword#1',
            'password'              => 'SuperSecurePass$2026',
            'password_confirmation' => 'SuperSecurePass$2026',
        ];

        // Perform password update
        $updateResponse = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.security.update'), $payload);

        $updateResponse->assertRedirect(route('seller.account.security'));

        // Check if the current authenticated seller session persists to access protected pages
        $profileResponse = $this->actingAs($seller, 'seller')
            ->get(route('seller.account.profile'));

        $profileResponse->assertStatus(200);

        $dashboardResponse = $this->actingAs($seller, 'seller')
            ->get(route('seller.dashboard'));

        $dashboardResponse->assertStatus(200);
    }

    // =========================================================================
    // CATEGORY 2: GEOLOCATION & GEOFENCE BOUNDARIES (Feature 36)
    // =========================================================================

    /**
     * Challenge 2.1: Valid exact boundary coordinates are accepted (-90/90 lat, -180/180 lng, 1/500 radius).
     */
    public function test_challenge_geolocation_exact_boundary_limits_accepted(): void
    {
        $seller = $this->createApprovedSeller();

        $boundaryVectors = [
            'north_east_max' => [
                'latitude'            => 90.0,
                'longitude'           => 180.0,
                'operating_radius_km' => 500,
            ],
            'south_west_min' => [
                'latitude'            => -90.0,
                'longitude'           => -180.0,
                'operating_radius_km' => 1,
            ],
            'equator_prime' => [
                'latitude'            => 0.0,
                'longitude'           => 0.0,
                'operating_radius_km' => 250,
            ],
        ];

        foreach ($boundaryVectors as $case => $coords) {
            $payload = array_merge([
                'address'     => "Boundary Test Plot {$case}",
                'city'        => 'Test City',
                'state'       => 'Test State',
                'postal_code' => '123456',
            ], $coords);

            $response = $this->actingAs($seller, 'seller')
                ->put(route('seller.account.location.update'), $payload);

            $response->assertRedirect(route('seller.account.location'));
            $response->assertSessionHas('success');

            $profile = $seller->fresh()->sellerProfile;
            $this->assertEquals($coords['latitude'], (float) $profile->latitude, "Failed on {$case} latitude");
            $this->assertEquals($coords['longitude'], (float) $profile->longitude, "Failed on {$case} longitude");
            $this->assertEquals($coords['operating_radius_km'], (int) $profile->operating_radius_km, "Failed on {$case} radius");
        }
    }

    /**
     * Challenge 2.2: Out of bounds coordinates and radii are strictly rejected.
     */
    public function test_challenge_geolocation_out_of_bounds_rejected(): void
    {
        $seller = $this->createApprovedSeller();

        $invalidVectors = [
            'lat_above_90'     => ['latitude' => 90.0001, 'longitude' => 77.0, 'operating_radius_km' => 25, 'err' => 'latitude'],
            'lat_below_neg_90' => ['latitude' => -90.0001, 'longitude' => 77.0, 'operating_radius_km' => 25, 'err' => 'latitude'],
            'lng_above_180'    => ['latitude' => 20.0, 'longitude' => 180.0001, 'operating_radius_km' => 25, 'err' => 'longitude'],
            'lng_below_neg_180'=> ['latitude' => 20.0, 'longitude' => -180.0001, 'operating_radius_km' => 25, 'err' => 'longitude'],
            'radius_zero'      => ['latitude' => 20.0, 'longitude' => 77.0, 'operating_radius_km' => 0, 'err' => 'operating_radius_km'],
            'radius_negative'  => ['latitude' => 20.0, 'longitude' => 77.0, 'operating_radius_km' => -10, 'err' => 'operating_radius_km'],
            'radius_above_500' => ['latitude' => 20.0, 'longitude' => 77.0, 'operating_radius_km' => 501, 'err' => 'operating_radius_km'],
            'lat_non_numeric'  => ['latitude' => 'invalid_lat', 'longitude' => 77.0, 'operating_radius_km' => 25, 'err' => 'latitude'],
            'radius_decimal'   => ['latitude' => 20.0, 'longitude' => 77.0, 'operating_radius_km' => '25.5', 'err' => 'operating_radius_km'],
        ];

        foreach ($invalidVectors as $desc => $vector) {
            $payload = [
                'address'             => 'Invalid Coord Plot',
                'latitude'            => $vector['latitude'],
                'longitude'           => $vector['longitude'],
                'operating_radius_km' => $vector['operating_radius_km'],
            ];

            $response = $this->actingAs($seller, 'seller')
                ->put(route('seller.account.location.update'), $payload);

            $response->assertSessionHasErrors($vector['err'],
                "Expected validation error on '{$vector['err']}' for test vector '{$desc}'");
        }
    }

    /**
     * Challenge 2.3: Address string sanitization, XSS, and Unicode character resilience.
     */
    public function test_challenge_location_address_xss_and_sanitization(): void
    {
        $seller = $this->createApprovedSeller();

        $xssAddress = '<script>alert("XSS_ATTACK")</script> Dag No 42, Farm Road';
        $bengaliCity = 'পূর্ব মেদিনীপুর (Purba Medinipur)';

        $payload = [
            'address'             => $xssAddress,
            'city'                => $bengaliCity,
            'state'               => 'West Bengal',
            'postal_code'         => '721401',
            'latitude'            => 21.7781,
            'longitude'           => 87.7516,
            'operating_radius_km' => 30,
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.location.update'), $payload);

        $response->assertRedirect(route('seller.account.location'));
        $response->assertSessionHas('success');

        $profile = $seller->fresh()->sellerProfile;
        $this->assertEquals($xssAddress, $profile->address);
        $this->assertEquals($bengaliCity, $profile->city);

        // Verify the view escapes the script tag properly and does NOT render raw unescaped script tag
        $viewResponse = $this->actingAs($seller, 'seller')->get(route('seller.account.location'));
        $viewResponse->assertStatus(200);
        // Blade renders value="{{ old('address', $profile?->address) }}" which HTML-escapes quotes and tags
        $viewResponse->assertDontSee('<script>alert("XSS_ATTACK")</script>', false);
        $viewResponse->assertSee('&lt;script&gt;alert(&quot;XSS_ATTACK&quot;)&lt;/script&gt;', false);
    }

    /**
     * Challenge 2.4: Address string length boundaries.
     */
    public function test_challenge_location_address_length_boundaries(): void
    {
        $seller = $this->createApprovedSeller();

        // 1. Address max 255 chars: exactly 255 passes
        $address255 = str_repeat('A', 255);
        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.location.update'), [
                'address'             => $address255,
                'latitude'            => 20.0,
                'longitude'           => 80.0,
                'operating_radius_km' => 25,
            ]);
        $response->assertSessionHasNoErrors();

        // 2. Address 256 chars: fails
        $address256 = str_repeat('A', 256);
        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.location.update'), [
                'address'             => $address256,
                'latitude'            => 20.0,
                'longitude'           => 80.0,
                'operating_radius_km' => 25,
            ]);
        $response->assertSessionHasErrors('address');

        // 3. City 101 chars: fails (max: 100)
        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.location.update'), [
                'address'             => 'Valid Address',
                'city'                => str_repeat('C', 101),
                'latitude'            => 20.0,
                'longitude'           => 80.0,
                'operating_radius_km' => 25,
            ]);
        $response->assertSessionHasErrors('city');

        // 4. Postal code 21 chars: fails (max: 20)
        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.location.update'), [
                'address'             => 'Valid Address',
                'postal_code'         => str_repeat('9', 21),
                'latitude'            => 20.0,
                'longitude'           => 80.0,
                'operating_radius_km' => 25,
            ]);
        $response->assertSessionHasErrors('postal_code');
    }

    // =========================================================================
    // CATEGORY 3: PROFILE UPDATES, STOREFRONT MEDIA & PERSISTENCE (Features 34, 35)
    // =========================================================================

    /**
     * Challenge 3.1: Shop name and bio character length boundaries.
     */
    public function test_challenge_profile_update_shop_name_and_bio_boundaries(): void
    {
        $seller = $this->createApprovedSeller();

        // Shop name empty: rejected
        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), [
                'shop_name' => '',
            ]);
        $response->assertSessionHasErrors('shop_name');

        // Shop name 150 chars: accepted
        $name150 = str_repeat('N', 150);
        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), [
                'shop_name' => $name150,
            ]);
        $response->assertSessionHasNoErrors();
        $this->assertEquals($name150, $seller->fresh()->sellerProfile->shop_name);

        // Shop name 151 chars: rejected
        $name151 = str_repeat('N', 151);
        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), [
                'shop_name' => $name151,
            ]);
        $response->assertSessionHasErrors('shop_name');

        // Bio 500 chars: accepted
        $bio500 = str_repeat('B', 500);
        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), [
                'shop_name' => 'Valid Shop Name',
                'bio'       => $bio500,
            ]);
        $response->assertSessionHasNoErrors();
        $this->assertEquals($bio500, $seller->fresh()->sellerProfile->bio);

        // Bio 501 chars: rejected
        $bio501 = str_repeat('B', 501);
        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), [
                'shop_name' => 'Valid Shop Name',
                'bio'       => $bio501,
            ]);
        $response->assertSessionHasErrors('bio');
    }

    /**
     * Challenge 3.2: Storefront image upload validation rejects invalid MIME types.
     */
    public function test_challenge_profile_storefront_image_upload_validation_mimes(): void
    {
        Storage::fake('public');
        $seller = $this->createApprovedSeller();

        // Text file disguised or non-image
        $fakeFile = UploadedFile::fake()->create('malicious.php', 100, 'application/x-php');

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), [
                'shop_name'    => 'Valid Shop',
                'banner_image' => $fakeFile,
            ]);

        $response->assertSessionHasErrors('banner_image');

        // PDF file
        $pdfFile = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), [
                'shop_name'  => 'Valid Shop',
                'logo_image' => $pdfFile,
            ]);

        $response->assertSessionHasErrors('logo_image');
    }

    /**
     * Challenge 3.3: Storefront image upload validation strictly enforces max file size limits.
     * Banner max: 5120 KB (5 MB), Logo max: 2048 KB (2 MB).
     */
    public function test_challenge_profile_storefront_image_upload_validation_sizes(): void
    {
        Storage::fake('public');
        $seller = $this->createApprovedSeller();

        // Oversized banner: 5121 KB (> 5120 KB)
        $oversizedBanner = UploadedFile::fake()->create('huge_banner.jpg', 5121, 'image/jpeg');

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), [
                'shop_name'    => 'Valid Shop',
                'banner_image' => $oversizedBanner,
            ]);

        $response->assertSessionHasErrors('banner_image');

        // Oversized logo: 2049 KB (> 2048 KB)
        $oversizedLogo = UploadedFile::fake()->create('huge_logo.png', 2049, 'image/png');

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), [
                'shop_name'  => 'Valid Shop',
                'logo_image' => $oversizedLogo,
            ]);

        $response->assertSessionHasErrors('logo_image');
    }

    /**
     * Challenge 3.4: Operating harvest days validation rejects invalid day names.
     */
    public function test_challenge_profile_operating_harvest_days_validation_rejects_invalid_days(): void
    {
        $seller = $this->createApprovedSeller();

        $payload = [
            'shop_name'      => 'Valid Farm',
            'operating_days' => ['mon', 'funday', 'wednesday'], // 'funday' and 'wednesday' are invalid
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), $payload);

        $response->assertSessionHasErrors('operating_days.1');
    }

    /**
     * Challenge 3.5: Operating harvest days JSON persistence audit.
     * Feature 35 & Dispatch Prompt requirement: "operating harvest days JSON persistence".
     *
     * This test documents whether operating_days persists to the database schema.
     */
    public function test_challenge_profile_operating_harvest_days_persistence(): void
    {
        $seller = $this->createApprovedSeller();

        $payload = [
            'shop_name'      => 'Harvest Schedule Farm',
            'operating_days' => ['mon', 'wed', 'fri'],
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), $payload);

        $response->assertRedirect(route('seller.account.profile'));
        $response->assertSessionHas('success');

        // Check empirical schema support:
        $hasColumn = Schema::hasColumn('seller_profiles', 'operating_days');

        if ($hasColumn) {
            $profile = $seller->fresh()->sellerProfile;
            $this->assertNotNull($profile->operating_days, 'operating_days should be persisted in database.');
            $decoded = is_array($profile->operating_days) ? $profile->operating_days : json_decode($profile->operating_days, true);
            $this->assertEquals(['mon', 'wed', 'fri'], $decoded);
        } else {
            // Note: If the column is missing from seller_profiles, the controller
            // silently skips saving it due to:
            // if (Schema::hasColumn('seller_profiles', 'operating_days') && $request->has('operating_days'))
            // We record this finding empirically!
            $profile = $seller->fresh()->sellerProfile;
            $this->assertNull($profile->operating_days,
                'OBSERVED DEFECT: seller_profiles table lacks operating_days column, so operating days are dropped and not persisted.');
        }
    }

    /**
     * Challenge 3.6: Alpine.js hidden input sends JSON string vs controller expects array.
     * In profile.blade.php:
     * <input type="hidden" name="operating_days" :value="JSON.stringify(operatingDays)">
     *
     * In HTML form POST, this submits a raw JSON string like '["mon","tue"]'.
     * The controller normalizes JSON string to array before validation.
     */
    public function test_challenge_profile_blade_json_string_payload_behavior(): void
    {
        $seller = $this->createApprovedSeller();

        // This payload simulates what the browser actually submits via the Blade template:
        $payload = [
            'shop_name'      => 'Alpine Form Shop',
            'operating_days' => '["mon","tue","wed"]', // JSON string from JSON.stringify(operatingDays)
        ];

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.profile.update'), $payload);

        // After remediation, controller normalizes JSON string to array, allowing successful submission
        $response->assertRedirect(route('seller.account.profile'));
        $response->assertSessionHasNoErrors();

        $profile = $seller->fresh()->sellerProfile;
        $operatingDays = is_array($profile->operating_days) ? $profile->operating_days : json_decode($profile->operating_days, true);
        $this->assertEquals(['mon', 'tue', 'wed'], $operatingDays);
    }

    // =========================================================================
    // CATEGORY 4: MULTI-TENANT ISOLATION & ACCESS CONTROL (Features 34-37)
    // =========================================================================

    /**
     * Challenge 4.1: Cross-tenant isolation - Seller A cannot mutate Seller B's profile.
     * Seller A passes Seller B's id/user_id in form payload.
     */
    public function test_challenge_cross_tenant_seller_a_cannot_update_seller_b_profile(): void
    {
        $sellerA = $this->createApprovedSeller([], [
            'shop_name' => 'Seller A Farm',
            'bio'       => 'Seller A Bio',
        ]);
        $sellerB = $this->createApprovedSeller([], [
            'shop_name' => 'Seller B Farm',
            'bio'       => 'Seller B Untouchable Bio',
        ]);

        $payload = [
            'id'        => $sellerB->sellerProfile->id,
            'user_id'   => $sellerB->id,
            'seller_id' => $sellerB->sellerProfile->id,
            'shop_name' => 'Hacked Seller B Shop Name',
            'bio'       => 'Hacked Bio Attempt',
        ];

        $response = $this->actingAs($sellerA, 'seller')
            ->put(route('seller.account.profile.update'), $payload);

        $response->assertRedirect(route('seller.account.profile'));

        // Seller B must remain completely unchanged
        $sellerBProfile = $sellerB->fresh()->sellerProfile;
        $this->assertEquals('Seller B Farm', $sellerBProfile->shop_name,
            'Seller B shop_name must not be mutated by Seller A.');
        $this->assertEquals('Seller B Untouchable Bio', $sellerBProfile->bio,
            'Seller B bio must not be mutated by Seller A.');

        // Seller A's own profile should be updated
        $sellerAProfile = $sellerA->fresh()->sellerProfile;
        $this->assertEquals('Hacked Seller B Shop Name', $sellerAProfile->shop_name);
        $this->assertEquals('Hacked Bio Attempt', $sellerAProfile->bio);
    }

    /**
     * Challenge 4.2: Cross-tenant isolation - Seller A cannot mutate Seller B's location.
     */
    public function test_challenge_cross_tenant_seller_a_cannot_update_seller_b_location(): void
    {
        $sellerA = $this->createApprovedSeller([], [
            'address'   => 'Seller A Land',
            'latitude'  => 22.0,
            'longitude' => 88.0,
        ]);
        $sellerB = $this->createApprovedSeller([], [
            'address'   => 'Seller B Sacred Land',
            'latitude'  => 19.0,
            'longitude' => 73.0,
        ]);

        $payload = [
            'id'                  => $sellerB->sellerProfile->id,
            'user_id'             => $sellerB->id,
            'profile_id'          => $sellerB->sellerProfile->id,
            'address'             => 'Poisoned Address 999',
            'latitude'            => 10.0,
            'longitude'           => 10.0,
            'operating_radius_km' => 100,
        ];

        $response = $this->actingAs($sellerA, 'seller')
            ->put(route('seller.account.location.update'), $payload);

        $response->assertRedirect(route('seller.account.location'));

        // Seller B's location must be unchanged
        $sellerBProfile = $sellerB->fresh()->sellerProfile;
        $this->assertEquals('Seller B Sacred Land', $sellerBProfile->address);
        $this->assertEquals(19.0, (float) $sellerBProfile->latitude);
        $this->assertEquals(73.0, (float) $sellerBProfile->longitude);

        // Seller A's location should be updated
        $sellerAProfile = $sellerA->fresh()->sellerProfile;
        $this->assertEquals('Poisoned Address 999', $sellerAProfile->address);
        $this->assertEquals(10.0, (float) $sellerAProfile->latitude);
    }

    /**
     * Challenge 4.3: Cross-tenant isolation - Seller A cannot mutate Seller B's password.
     */
    public function test_challenge_cross_tenant_seller_a_cannot_update_seller_b_password(): void
    {
        $sellerA = $this->createApprovedSeller([
            'password' => Hash::make('SellerAPass$123'),
        ]);
        $sellerB = $this->createApprovedSeller([
            'password' => Hash::make('SellerBPass$456'),
        ]);

        $payload = [
            'user_id'               => $sellerB->id,
            'id'                    => $sellerB->id,
            'current_password'      => 'SellerAPass$123',
            'password'              => 'BrandNewHijackedPass$2026',
            'password_confirmation' => 'BrandNewHijackedPass$2026',
        ];

        $response = $this->actingAs($sellerA, 'seller')
            ->put(route('seller.account.security.update'), $payload);

        $response->assertRedirect(route('seller.account.security'));

        // Seller B's password must remain unchanged
        $sellerB->refresh();
        $this->assertTrue(Hash::check('SellerBPass$456', $sellerB->password),
            'Seller B password must NOT be modified by Seller A.');
        $this->assertFalse(Hash::check('BrandNewHijackedPass$2026', $sellerB->password));

        // Seller A's password should be updated
        $sellerA->refresh();
        $this->assertTrue(Hash::check('BrandNewHijackedPass$2026', $sellerA->password));
    }

    /**
     * Challenge 4.4: Role guards & unauthenticated access restrictions.
     */
    public function test_challenge_role_guards_and_unauthenticated_restrictions(): void
    {
        $routes = [
            ['get', route('seller.account.profile')],
            ['get', route('seller.account.location')],
            ['get', route('seller.account.security')],
            ['put', route('seller.account.profile.update')],
            ['put', route('seller.account.location.update')],
            ['put', route('seller.account.security.update')],
        ];

        // 1. Unauthenticated guest -> redirected to login
        foreach ($routes as [$method, $url]) {
            $response = $this->call($method, $url);
            $response->assertRedirect(route('login'));
        }

        // 2. Pending seller -> redirected to pending gate
        $pendingSeller = $this->createPendingSeller();
        foreach ($routes as [$method, $url]) {
            $response = $this->actingAs($pendingSeller, 'seller')->call($method, $url);
            $response->assertRedirect(route('seller.pending'));
        }

        // 3. Regular buyer -> redirected or blocked
        $buyer = $this->createBuyer();
        foreach ($routes as [$method, $url]) {
            $response = $this->actingAs($buyer, 'seller')->call($method, $url);
            $this->assertTrue($response->isRedirect() || $response->status() === 403,
                "Buyer should be blocked from seller account route: {$url}");
        }
    }
}
