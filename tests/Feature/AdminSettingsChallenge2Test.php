<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\SiteSetting;
use App\Models\SellerProfile;

class AdminSettingsChallenge2Test extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;

    protected string $validPngContent;
    protected string $validJpgContent;
    protected string $validWebpContent;
    protected string $validSvgContent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Challenge Admin',
            'email' => 'admin_challenge2@bazaario.com',
            'password' => Hash::make('AdminPass123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->regularUser = User::create([
            'name' => 'Challenge Buyer',
            'email' => 'buyer_challenge2@bazaario.com',
            'password' => Hash::make('BuyerPass123!'),
            'role' => 'user',
            'status' => 'active',
        ]);

        // Base64 encoded valid 1x1 image binaries (environment independent, no GD dependency)
        $this->validPngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $this->validJpgContent = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        $this->validWebpContent = base64_decode('UklGRiQAAABXRUJQVlA4IBgAAAAwAQCdASoBAAEAAwA0JaQAA3AA/vuUAAA=');
        $this->validSvgContent = '<?xml version="1.0" encoding="UTF-8"?><svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="#4F46E5"/></svg>';
    }

    /*
    |--------------------------------------------------------------------------
    | SECTION 1: MALICIOUS FILE UPLOAD STRESS-TESTS
    |--------------------------------------------------------------------------
    */

    /**
     * EMPIRICAL TEST: PHP script disguised as logo is rejected.
     */
    public function test_php_script_upload_disguised_as_logo_is_rejected(): void
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser, 'admin');

        $phpScript = UploadedFile::fake()->createWithContent(
            'backdoor.php',
            '<?php phpinfo(); ?>',
            'application/x-php'
        );

        $response = $this->post(route('admin.settings.update'), [
            'site_logo' => $phpScript,
        ]);

        $response->assertSessionHasErrors(['site_logo']);
        $this->assertNull(SiteSetting::get('site_logo'));
        $this->assertEmpty(Storage::disk('public')->files('branding'));
    }

    /**
     * EMPIRICAL TEST: Plain text file renamed as .png with text/plain mime is rejected by image validation.
     */
    public function test_plain_text_file_renamed_as_png_is_rejected(): void
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser, 'admin');

        $fakeImage = UploadedFile::fake()->create(
            'fake_logo.png',
            10,
            'text/plain'
        );

        $response = $this->post(route('admin.settings.update'), [
            'site_logo' => $fakeImage,
        ]);

        $response->assertSessionHasErrors(['site_logo']);
        $this->assertNull(SiteSetting::get('site_logo'));
        $this->assertEmpty(Storage::disk('public')->files('branding'));
    }

    /**
     * EMPIRICAL TEST: Oversized file exceeding 2MB (2048 KB) limit is rejected.
     */
    public function test_oversized_image_exceeding_2mb_is_rejected(): void
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser, 'admin');

        // 2049 KB exceeds max:2048
        $oversizedFile = UploadedFile::fake()->create(
            'huge_logo.png',
            2049,
            'image/png'
        );

        $response = $this->post(route('admin.settings.update'), [
            'site_logo' => $oversizedFile,
        ]);

        $response->assertSessionHasErrors(['site_logo']);
        $this->assertNull(SiteSetting::get('site_logo'));
        $this->assertEmpty(Storage::disk('public')->files('branding'));
    }

    /**
     * EMPIRICAL TEST: HTML/JavaScript payload disguised as image is rejected.
     */
    public function test_html_javascript_file_disguised_as_image_is_rejected(): void
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser, 'admin');

        $xssFile = UploadedFile::fake()->createWithContent(
            'xss_payload.html',
            '<script>alert("XSS")</script>',
            'text/html'
        );

        $response = $this->post(route('admin.settings.update'), [
            'site_logo' => $xssFile,
        ]);

        $response->assertSessionHasErrors(['site_logo']);
        $this->assertNull(SiteSetting::get('site_logo'));
    }

    /**
     * EMPIRICAL TEST: Binary executable (.exe) file is rejected.
     */
    public function test_executable_file_is_rejected(): void
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser, 'admin');

        $exeFile = UploadedFile::fake()->create('malware.exe', 500, 'application/x-msdownload');

        $response = $this->post(route('admin.settings.update'), [
            'site_logo' => $exeFile,
        ]);

        $response->assertSessionHasErrors(['site_logo']);
        $this->assertNull(SiteSetting::get('site_logo'));
    }

    /*
    |--------------------------------------------------------------------------
    | SECTION 2: GENUINE IMAGE UPLOAD & URL RESOLUTION
    |--------------------------------------------------------------------------
    */

    /**
     * EMPIRICAL TEST: Genuine PNG image upload is accepted, stored to disk, and resolves to URL.
     */
    public function test_genuine_png_upload_is_accepted_stored_and_resolves_url(): void
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser, 'admin');

        $pngFile = UploadedFile::fake()->createWithContent('brand_logo.png', $this->validPngContent);

        $response = $this->post(route('admin.settings.update'), [
            'site_logo' => $pngFile,
        ]);

        $response->assertSessionHas('success');

        $storedPath = SiteSetting::get('site_logo');
        $this->assertNotNull($storedPath);
        $this->assertStringStartsWith('branding/', $storedPath);
        Storage::disk('public')->assertExists($storedPath);

        $url = SiteSetting::getLogoUrl();
        $this->assertStringContainsString($storedPath, $url);
        $this->assertTrue(str_starts_with($url, 'http') || str_starts_with($url, '/storage/'));
    }

    /**
     * EMPIRICAL TEST: Genuine JPG/JPEG image upload is accepted, stored to disk, and resolves to URL.
     */
    public function test_genuine_jpg_upload_is_accepted_stored_and_resolves_url(): void
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser, 'admin');

        $jpgFile = UploadedFile::fake()->createWithContent('brand_logo.jpg', $this->validJpgContent);

        $response = $this->post(route('admin.settings.update'), [
            'site_logo' => $jpgFile,
        ]);

        $response->assertSessionHas('success');

        $storedPath = SiteSetting::get('site_logo');
        $this->assertNotNull($storedPath);
        Storage::disk('public')->assertExists($storedPath);

        $url = SiteSetting::getLogoUrl();
        $this->assertStringContainsString($storedPath, $url);
        $this->assertTrue(str_starts_with($url, 'http') || str_starts_with($url, '/storage/'));
    }

    /**
     * EMPIRICAL TEST: Genuine WebP image upload is accepted, stored to disk, and resolves to URL.
     */
    public function test_genuine_webp_upload_is_accepted_stored_and_resolves_url(): void
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser, 'admin');

        $webpFile = UploadedFile::fake()->createWithContent('brand_logo.webp', $this->validWebpContent);

        $response = $this->post(route('admin.settings.update'), [
            'site_logo' => $webpFile,
        ]);

        $response->assertSessionHas('success');

        $storedPath = SiteSetting::get('site_logo');
        $this->assertNotNull($storedPath);
        Storage::disk('public')->assertExists($storedPath);

        $url = SiteSetting::getLogoUrl();
        $this->assertStringContainsString($storedPath, $url);
        $this->assertTrue(str_starts_with($url, 'http') || str_starts_with($url, '/storage/'));
    }

    /**
     * EMPIRICAL TEST: Genuine SVG image upload handling and URL resolution.
     */
    public function test_genuine_svg_upload_is_accepted_stored_and_resolves_url(): void
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser, 'admin');

        $svgFile = UploadedFile::fake()->createWithContent('logo.svg', $this->validSvgContent);

        $response = $this->post(route('admin.settings.update'), [
            'site_logo' => $svgFile,
        ]);

        // If validation rejects SVG due to Laravel's default 'image' rule lacking 'allow_svg':
        if (session()->has('errors')) {
            $errors = session('errors')->get('site_logo');
            $this->fail('SVG upload was rejected by validation rule: ' . json_encode($errors));
        }

        $response->assertSessionHas('success');
        $storedPath = SiteSetting::get('site_logo');
        $this->assertNotNull($storedPath);
        Storage::disk('public')->assertExists($storedPath);

        $url = SiteSetting::getLogoUrl();
        $this->assertStringContainsString($storedPath, $url);
    }

    /**
     * EMPIRICAL TEST: Successive logo updates overwrite previous path and resolve new logo URL.
     */
    public function test_consecutive_logo_uploads_update_setting_and_url(): void
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser, 'admin');

        // First upload
        $logo1 = UploadedFile::fake()->createWithContent('first_logo.png', $this->validPngContent);
        $this->post(route('admin.settings.update'), ['site_logo' => $logo1])->assertSessionHas('success');
        $path1 = SiteSetting::get('site_logo');

        // Second upload
        $logo2 = UploadedFile::fake()->createWithContent('second_logo.jpg', $this->validJpgContent);
        $this->post(route('admin.settings.update'), ['site_logo' => $logo2])->assertSessionHas('success');
        $path2 = SiteSetting::get('site_logo');

        $this->assertNotEquals($path1, $path2);
        Storage::disk('public')->assertExists($path2);
        $this->assertStringContainsString($path2, SiteSetting::getLogoUrl());
    }

    /**
     * EMPIRICAL TEST: SiteSetting::getLogoUrl() falls back to default asset when no logo is set or file is deleted.
     */
    public function test_logo_url_fallback_when_logo_is_missing(): void
    {
        // 1. When setting is null
        SiteSetting::set('site_logo', null);
        $this->assertStringContainsString('bazaario-logo.png', SiteSetting::getLogoUrl());

        // 2. When setting is empty string
        SiteSetting::set('site_logo', '');
        $this->assertStringContainsString('bazaario-logo.png', SiteSetting::getLogoUrl());

        // 3. When setting references a non-existent file on public disk
        Storage::fake('public');
        SiteSetting::set('site_logo', 'branding/deleted_missing_logo.png');
        $this->assertStringContainsString('bazaario-logo.png', SiteSetting::getLogoUrl());
    }

    /*
    |--------------------------------------------------------------------------
    | SECTION 3: SINGLE-VENDOR BOUNDARY SECURITY
    |--------------------------------------------------------------------------
    */

    /**
     * EMPIRICAL TEST: Direct API attempt to request OTP for seller role in Single-Vendor mode is strictly blocked.
     */
    public function test_direct_api_send_otp_for_seller_role_blocked_in_single_vendor_mode(): void
    {
        SiteSetting::set('platform_mode', 'single_vendor');
        Mail::fake();

        $sellerPayload = [
            'name' => 'Intruding Merchant',
            'email' => 'intruder@merchant.com',
            'phone' => '1234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'seller',
            'terms' => true,
        ];

        $response = $this->postJson(route('register.send-otp'), $sellerPayload);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Seller registration is currently disabled in Single-Vendor mode.',
            'errors' => [
                'role' => ['Seller registration is currently disabled in Single-Vendor mode.'],
            ],
        ]);

        // Verify no OTP was written to database
        $this->assertDatabaseMissing('email_otps', [
            'email' => 'intruder@merchant.com',
        ]);

        // Verify no user was created
        $this->assertDatabaseMissing('users', [
            'email' => 'intruder@merchant.com',
        ]);

        // Verify no mail was sent
        Mail::assertNothingSent();
    }

    /**
     * EMPIRICAL TEST: Direct API attempt to request OTP for buyer/user role in Single-Vendor mode remains allowed.
     */
    public function test_direct_api_send_otp_for_user_role_allowed_in_single_vendor_mode(): void
    {
        SiteSetting::set('platform_mode', 'single_vendor');
        Mail::fake();

        $userPayload = [
            'name' => 'Legit Buyer',
            'email' => 'buyer@example.com',
            'phone' => '1234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'user',
            'terms' => true,
        ];

        $response = $this->postJson(route('register.send-otp'), $userPayload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Verify OTP was stored
        $this->assertDatabaseHas('email_otps', [
            'email' => 'buyer@example.com',
        ]);
    }

    /**
     * EMPIRICAL TEST: Complete registration bypass attempt with no active session is rejected.
     */
    public function test_verify_otp_without_session_is_rejected(): void
    {
        SiteSetting::set('platform_mode', 'single_vendor');

        $response = $this->postJson(route('register.verify-otp'), [
            'otp' => '123456',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Session expired. Please fill the form again.',
        ]);
    }

    /**
     * EMPIRICAL TEST: Complete registration bypass attempt with forged/invalid OTP is rejected.
     */
    public function test_verify_otp_with_invalid_otp_is_rejected(): void
    {
        SiteSetting::set('platform_mode', 'single_vendor');

        // Create a valid OTP record for an email
        DB::table('email_otps')->insert([
            'email' => 'victim@example.com',
            'otp' => '999999',
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        // Set session
        $this->withSession([
            'pending_registration' => [
                'name' => 'Victim User',
                'email' => 'victim@example.com',
                'password' => bcrypt('Password123!'),
                'role' => 'user',
            ],
        ]);

        // Attacker guesses wrong OTP
        $response = $this->postJson(route('register.verify-otp'), [
            'otp' => '000000',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Invalid OTP. Please try again.',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'victim@example.com',
        ]);
    }

    /**
     * EMPIRICAL TEST: Attempting to elevate role from 'user' to 'seller' during OTP verification is blocked.
     */
    public function test_role_elevation_tampering_at_verify_otp_is_ineffective(): void
    {
        SiteSetting::set('platform_mode', 'single_vendor');

        DB::table('email_otps')->insert([
            'email' => 'elevate@example.com',
            'otp' => '123456',
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        // Legitimate user pending registration in session
        $this->withSession([
            'pending_registration' => [
                'name' => 'Sneaky User',
                'email' => 'elevate@example.com',
                'phone' => '1234567890',
                'password' => bcrypt('Password123!'),
                'role' => 'user',
            ],
        ]);

        // Attacker passes role => 'seller' in the verify OTP payload
        $response = $this->postJson(route('register.verify-otp'), [
            'otp' => '123456',
            'role' => 'seller',
        ]);

        $response->assertStatus(200);

        // Verify the user was created as 'user', NOT elevated to 'seller'
        $user = User::where('email', 'elevate@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('user', $user->role);

        // Verify NO seller profile was generated
        $this->assertNull($user->sellerProfile);
        $this->assertDatabaseMissing('seller_profiles', [
            'user_id' => $user->id,
        ]);
    }

    /**
     * EMPIRICAL TEST: Unauthenticated and non-seller users cannot access seller onboarding in single-vendor mode.
     */
    public function test_non_seller_cannot_access_seller_onboarding_in_single_vendor_mode(): void
    {
        SiteSetting::set('platform_mode', 'single_vendor');

        // Guest attempt
        $guestResponse = $this->get(route('seller.onboarding'));
        $guestResponse->assertRedirect(route('login'));

        // Regular customer attempt
        $this->actingAs($this->regularUser, 'user');
        $customerResponse = $this->get(route('seller.onboarding'));
        $this->assertTrue(in_array($customerResponse->getStatusCode(), [302, 403]));
    }

    /**
     * EMPIRICAL TEST: Registration completion bypass attempt when pending_registration has role='seller' during single-vendor mode.
     * When single-vendor mode is active, any attempt to finalize registration as a seller MUST be rejected.
     */
    public function test_seller_registration_completion_attempt_in_single_vendor_mode(): void
    {
        SiteSetting::set('platform_mode', 'single_vendor');

        DB::table('email_otps')->insert([
            'email' => 'preexisting_seller@example.com',
            'otp' => '654321',
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        // Attacker got this session before mode switch or via session manipulation
        $this->withSession([
            'pending_registration' => [
                'name' => 'Stale Seller Session',
                'email' => 'preexisting_seller@example.com',
                'phone' => '1234567890',
                'password' => bcrypt('Password123!'),
                'role' => 'seller',
            ],
        ]);

        $response = $this->postJson(route('register.verify-otp'), [
            'otp' => '654321',
        ]);

        // If the system fails to check isSingleVendor() in verifyOtpAndRegister,
        // it creates a seller account during single-vendor mode!
        $sellerUser = User::where('email', 'preexisting_seller@example.com')->first();
        
        if ($sellerUser && $sellerUser->role === 'seller') {
            $this->fail(
                'VULNERABILITY: Complete registration bypass succeeded! A seller account (user ID ' .
                $sellerUser->id . ') was created while single_vendor mode was active because ' .
                'AuthController::verifyOtpAndRegister fails to check SiteSetting::isSingleVendor().'
            );
        }

        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', [
            'email' => 'preexisting_seller@example.com',
        ]);
    }
}
