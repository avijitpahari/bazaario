<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\SiteSetting;

class AdminSettingsChallenge1Test extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $suspendedAdmin;
    protected User $customerUser;
    protected User $sellerUser;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Valid Active Admin
        $this->adminUser = User::create([
            'name' => 'Active Super Admin',
            'email' => 'superadmin@bazaario.com',
            'password' => Hash::make('AdminPass123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // 2. Suspended Admin
        $this->suspendedAdmin = User::create([
            'name' => 'Suspended Administrator',
            'email' => 'suspended_admin@bazaario.com',
            'password' => Hash::make('AdminPass123!'),
            'role' => 'admin',
            'status' => 'suspended',
        ]);

        // 3. Regular Customer
        $this->customerUser = User::create([
            'name' => 'Adversarial Customer',
            'email' => 'customer_attacker@bazaario.com',
            'password' => Hash::make('CustomerPass123!'),
            'role' => 'user',
            'status' => 'active',
        ]);

        // 4. Merchant Seller
        $this->sellerUser = User::create([
            'name' => 'Adversarial Merchant',
            'email' => 'merchant_attacker@bazaario.com',
            'password' => Hash::make('SellerPass123!'),
            'role' => 'seller',
            'status' => 'active',
        ]);
    }

    /**
     * CHALLENGE 1: Unauthorized attempts by guests to mutate platform settings must be rejected.
     */
    public function test_guest_unauthorized_attempts_to_mutate_settings_are_rejected(): void
    {
        SiteSetting::set('site_name', 'Initial Legit Name');
        SiteSetting::set('commission_rate', 8.5);
        SiteSetting::set('platform_mode', 'multi_vendor');

        // Unauthenticated standard web POST
        $response = $this->post(route('admin.settings.update'), [
            'site_name' => 'Hacked Name by Guest',
            'commission_rate' => 0.0,
            'platform_mode' => 'single_vendor',
        ]);

        $response->assertRedirect(route('admin.login'));
        $this->assertEquals('Initial Legit Name', SiteSetting::get('site_name'));
        $this->assertEquals(8.5, (float)SiteSetting::get('commission_rate'));
        $this->assertEquals('multi_vendor', SiteSetting::get('platform_mode'));

        // Unauthenticated JSON request
        $jsonResponse = $this->postJson(route('admin.settings.update'), [
            'site_name' => 'Hacked JSON by Guest',
        ]);
        $jsonResponse->assertStatus(401);
    }

    /**
     * CHALLENGE 2: Authenticated customer (role: user) cannot mutate admin platform settings.
     */
    public function test_customer_cannot_mutate_admin_settings(): void
    {
        SiteSetting::set('site_name', 'Protected Bazaario');
        SiteSetting::set('commission_rate', 10.0);

        $this->actingAs($this->customerUser, 'user');

        $response = $this->post(route('admin.settings.update'), [
            'site_name' => 'Mutated by Customer',
            'commission_rate' => 1.0,
        ]);

        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
        $this->assertEquals('Protected Bazaario', SiteSetting::get('site_name'));
        $this->assertEquals(10.0, (float)SiteSetting::get('commission_rate'));

        // GET index is also blocked
        $indexResponse = $this->get(route('admin.settings.index'));
        $this->assertTrue(in_array($indexResponse->getStatusCode(), [302, 403]));
    }

    /**
     * CHALLENGE 3: Authenticated merchant (role: seller) cannot mutate admin platform settings.
     */
    public function test_merchant_seller_cannot_mutate_admin_settings(): void
    {
        SiteSetting::set('site_name', 'Protected Bazaario');
        SiteSetting::set('commission_rate', 10.0);

        $this->actingAs($this->sellerUser, 'seller');

        $response = $this->post(route('admin.settings.update'), [
            'site_name' => 'Mutated by Seller',
            'commission_rate' => 0.0,
        ]);

        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
        $this->assertEquals('Protected Bazaario', SiteSetting::get('site_name'));
        $this->assertEquals(10.0, (float)SiteSetting::get('commission_rate'));

        // GET index is also blocked
        $indexResponse = $this->get(route('admin.settings.index'));
        $this->assertTrue(in_array($indexResponse->getStatusCode(), [302, 403]));
    }

    /**
     * CHALLENGE 4: Suspended admin is rejected from mutating admin settings.
     */
    public function test_suspended_admin_cannot_mutate_admin_settings(): void
    {
        SiteSetting::set('site_name', 'Protected Bazaario');

        $this->actingAs($this->suspendedAdmin, 'admin');

        $response = $this->post(route('admin.settings.update'), [
            'site_name' => 'Mutated by Suspended Admin',
        ]);

        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
        $this->assertEquals('Protected Bazaario', SiteSetting::get('site_name'));
    }

    /**
     * CHALLENGE 5: Rapid toggling between single_vendor and multi_vendor modes.
     * Ensure state changes persist cleanly in DB and take immediate effect on registration and homepage queries.
     */
    public function test_rapid_platform_mode_toggling_immediate_consistency(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        // State 0: Initial multi-vendor
        SiteSetting::set('platform_mode', 'multi_vendor');
        $this->assertFalse(SiteSetting::isSingleVendor());
        $this->assertTrue(SiteSetting::isMultiVendor());

        // Toggle 1: Admin switches to single_vendor
        $toggle1 = $this->post(route('admin.settings.update'), [
            'platform_mode' => 'single_vendor',
        ]);
        $toggle1->assertSessionHas('success');
        $this->assertEquals('single_vendor', SiteSetting::get('platform_mode'));
        $this->assertTrue(SiteSetting::isSingleVendor());
        $this->assertFalse(SiteSetting::isMultiVendor());

        // Immediate Effect 1A: Seller registration blocked via OTP endpoint
        $sellerPayload = [
            'name' => 'Candidate Merchant',
            'email' => 'candidate@merchant.com',
            'password' => 'Pass12345!',
            'password_confirmation' => 'Pass12345!',
            'role' => 'seller',
            'terms' => true,
        ];
        $otpRes1 = $this->postJson(route('register.send-otp'), $sellerPayload);
        $otpRes1->assertStatus(422);
        $otpRes1->assertJsonFragment([
            'message' => 'Seller registration is currently disabled in Single-Vendor mode.',
        ]);

        // Immediate Effect 1B: Register view suppresses seller role radio
        $regViewRes1 = $this->get(route('register'));
        $regViewRes1->assertStatus(200);
        $regViewRes1->assertDontSee('id="card-type-seller"', false);

        // Immediate Effect 1C: Homepage suppresses nearby stalls
        $homeRes1 = $this->get(route('home'));
        $homeRes1->assertStatus(200);
        $homeRes1->assertDontSee('id="nearby-stalls"', false);

        // Toggle 2: Admin immediately toggles back to multi_vendor
        $toggle2 = $this->post(route('admin.settings.update'), [
            'platform_mode' => 'multi_vendor',
        ]);
        $toggle2->assertSessionHas('success');
        $this->assertEquals('multi_vendor', SiteSetting::get('platform_mode'));
        $this->assertFalse(SiteSetting::isSingleVendor());
        $this->assertTrue(SiteSetting::isMultiVendor());

        // Immediate Effect 2A: Register view shows seller role card again
        $regViewRes2 = $this->get(route('register'));
        $regViewRes2->assertStatus(200);
        $regViewRes2->assertSee('id="card-type-seller"', false);

        // Immediate Effect 2B: Homepage restores seller shelves
        $homeRes2 = $this->get(route('home'));
        $homeRes2->assertStatus(200);
        $homeRes2->assertSee('Featured Sellers');
        $homeRes2->assertSee('Nearby Stalls');

        // Immediate Effect 2C: Seller registration is no longer blocked by single-vendor mode
        $otpRes2 = $this->postJson(route('register.send-otp'), $sellerPayload);
        // It should either return 200 (OTP sent) or not 422 with the single-vendor message
        $this->assertNotEquals('Seller registration is currently disabled in Single-Vendor mode.', $otpRes2->json('message'));

        // Toggle 3: Rapid switch back to single_vendor
        $toggle3 = $this->post(route('admin.settings.update'), [
            'platform_mode' => 'single_vendor',
        ]);
        $toggle3->assertSessionHas('success');
        $this->assertTrue(SiteSetting::isSingleVendor());

        $otpRes3 = $this->postJson(route('register.send-otp'), $sellerPayload);
        $otpRes3->assertStatus(422);
        $otpRes3->assertJsonFragment([
            'message' => 'Seller registration is currently disabled in Single-Vendor mode.',
        ]);
    }

    /**
     * CHALLENGE 6: Invalid platform_mode inputs are rejected by validation.
     */
    public function test_invalid_platform_mode_input_is_rejected(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        SiteSetting::set('platform_mode', 'multi_vendor');

        $response = $this->post(route('admin.settings.update'), [
            'platform_mode' => 'invalid_hybrid_mode',
        ]);

        $response->assertSessionHasErrors(['platform_mode']);
        $this->assertEquals('multi_vendor', SiteSetting::get('platform_mode'));
    }

    /**
     * CHALLENGE 7: Extreme inputs for commission rate: 0%, 100%, and decimals.
     */
    public function test_commission_rate_boundaries_zero_and_hundred_percent(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        // Test 0.0% (Free commission model)
        $resZero = $this->post(route('admin.settings.update'), [
            'commission_rate' => 0.0,
        ]);
        $resZero->assertSessionHas('success');
        $this->assertEquals(0.0, SiteSetting::getCommissionRate());
        $this->assertEquals(0.0, (float)SiteSetting::get('commission_rate'));
        $this->assertEquals(0.0, (float)SiteSetting::get('platform_commission_base'));

        // Test 100.0% (Full consignment retention model)
        $resHundred = $this->post(route('admin.settings.update'), [
            'commission_rate' => 100.0,
        ]);
        $resHundred->assertSessionHas('success');
        $this->assertEquals(100.0, SiteSetting::getCommissionRate());
        $this->assertEquals(100.0, (float)SiteSetting::get('commission_rate'));
        $this->assertEquals(100.0, (float)SiteSetting::get('platform_commission_base'));

        // Test standard fractional commission (e.g. 12.375%)
        $resFraction = $this->post(route('admin.settings.update'), [
            'commission_rate' => 12.375,
        ]);
        $resFraction->assertSessionHas('success');
        $this->assertEquals(12.375, SiteSetting::getCommissionRate());
    }

    /**
     * CHALLENGE 8: Invalid commission rates (negative, >100%, non-numeric) are rejected.
     */
    public function test_commission_rate_negative_and_out_of_bounds_rejected(): void
    {
        $this->actingAs($this->adminUser, 'admin');
        SiteSetting::set('commission_rate', 8.5);

        // Case A: Negative commission (-5.0%)
        $resNegative = $this->post(route('admin.settings.update'), [
            'commission_rate' => -5.0,
        ]);
        $resNegative->assertSessionHasErrors(['commission_rate']);
        $this->assertEquals(8.5, SiteSetting::getCommissionRate());

        // Case B: Greater than 100% (100.5%)
        $resOver = $this->post(route('admin.settings.update'), [
            'commission_rate' => 100.5,
        ]);
        $resOver->assertSessionHasErrors(['commission_rate']);
        $this->assertEquals(8.5, SiteSetting::getCommissionRate());

        // Case C: Non-numeric string
        $resString = $this->post(route('admin.settings.update'), [
            'commission_rate' => 'twelve_percent',
        ]);
        $resString->assertSessionHasErrors(['commission_rate']);
        $this->assertEquals(8.5, SiteSetting::getCommissionRate());

        // Case D: Injection attempt in numeric field
        $resInject = $this->post(route('admin.settings.update'), [
            'commission_rate' => '10; DROP TABLE site_settings;',
        ]);
        $resInject->assertSessionHasErrors(['commission_rate']);
        $this->assertEquals(8.5, SiteSetting::getCommissionRate());
    }

    /**
     * CHALLENGE 9: Long strings and HTML/script injection attempts in site name and tagline.
     * Verify proper sanitization in controller and proper escaping in Blade templates.
     */
    public function test_html_script_injection_and_sanitization_in_site_name_and_tagline(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $xssName = '<script>alert("PWNED_NAME")</script>Bazaario Prime';
        $xssTagline = '<img src=x onerror="alert(\'PWNED_TAGLINE\')">Handmade Crafts & Gifts';

        $response = $this->post(route('admin.settings.update'), [
            'site_name' => $xssName,
            'site_tagline' => $xssTagline,
        ]);

        $response->assertSessionHas('success');

        // Check storage sanitization: strip_tags removes script tags
        $storedName = SiteSetting::get('site_name');
        $storedTagline = SiteSetting::get('site_tagline');

        $this->assertStringNotContainsString('<script>', $storedName);
        $this->assertStringNotContainsString('</script>', $storedName);
        $this->assertStringNotContainsString('<img', $storedTagline);

        // Verify template rendering: GET settings view
        $viewResponse = $this->get(route('admin.settings.index'));
        $viewResponse->assertStatus(200);

        // Ensure executable scripts or img onerror tags are NOT rendered raw
        $content = $viewResponse->getContent();
        $this->assertStringNotContainsString('<script>alert("PWNED_NAME")</script>', $content);
        $this->assertStringNotContainsString('<img src=x onerror=', $content);

        // Verify admin layout header rendering
        $dashboardResponse = $this->get(route('admin.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashContent = $dashboardResponse->getContent();
        $this->assertStringNotContainsString('<script>alert("PWNED_NAME")</script>', $dashContent);
        $this->assertStringNotContainsString('<img src=x onerror=', $dashContent);
    }

    /**
     * CHALLENGE 10: Excessively long strings exceeding database limits are rejected by validation.
     */
    public function test_excessive_length_strings_rejected(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        // Site name max is 100
        $overlengthName = str_repeat('A', 101);
        $resName = $this->post(route('admin.settings.update'), [
            'site_name' => $overlengthName,
        ]);
        $resName->assertSessionHasErrors(['site_name']);

        // Site tagline max is 255
        $overlengthTagline = str_repeat('B', 256);
        $resTagline = $this->post(route('admin.settings.update'), [
            'site_tagline' => $overlengthTagline,
        ]);
        $resTagline->assertSessionHasErrors(['site_tagline']);
    }

    /**
     * CHALLENGE 11: Malicious file types and oversized logo uploads are rejected.
     */
    public function test_malicious_and_oversized_logo_uploads_rejected(): void
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser, 'admin');

        // Case A: PHP executable script disguised as logo
        $phpFile = UploadedFile::fake()->create('backdoor.php', 10, 'application/x-php');
        $resPhp = $this->post(route('admin.settings.update'), [
            'site_logo' => $phpFile,
        ]);
        $resPhp->assertSessionHasErrors(['site_logo']);

        // Case B: Executable shell script
        $shFile = UploadedFile::fake()->create('exploit.sh', 10, 'text/x-shellscript');
        $resSh = $this->post(route('admin.settings.update'), [
            'site_logo' => $shFile,
        ]);
        $resSh->assertSessionHasErrors(['site_logo']);

        // Case C: Oversized image (over 2048 KB limit, e.g. 3500 KB)
        $oversizedImage = UploadedFile::fake()->create('giant_banner.png', 3500, 'image/png');
        $resLarge = $this->post(route('admin.settings.update'), [
            'site_logo' => $oversizedImage,
        ]);
        $resLarge->assertSessionHasErrors(['site_logo']);
    }

    /**
     * CHALLENGE 12: SiteSetting helpers handle empty/null strings and non-existent logo files gracefully.
     */
    public function test_site_setting_helpers_handle_edge_cases(): void
    {
        // 1. Whitespace-only or empty strings fallback to defaults
        SiteSetting::set('site_name', '   ');
        $this->assertEquals('Bazaario', SiteSetting::getSiteName());

        SiteSetting::set('site_tagline', '');
        $this->assertEquals('Smarter Shopping. Local Sellers. Live Auctions.', SiteSetting::getTagline());

        // 2. Non-existent storage logo falls back to default asset without crash
        SiteSetting::set('site_logo', 'branding/non_existent_deleted_logo.png');
        $logoUrl = SiteSetting::getLogoUrl();
        $this->assertStringContainsString('bazaario-logo.png', $logoUrl);

        // 3. Absolute HTTP external logo is supported
        SiteSetting::set('site_logo', 'https://cdn.example.com/external-logo.png');
        $this->assertEquals('https://cdn.example.com/external-logo.png', SiteSetting::getLogoUrl());

        // 4. Relative web path logo is supported
        SiteSetting::set('site_logo', '/images/custom-relative.svg');
        $this->assertEquals('/images/custom-relative.svg', SiteSetting::getLogoUrl());
    }

    /**
     * CHALLENGE 13: Partial updates only modify supplied fields; existing fields are preserved.
     */
    public function test_partial_settings_updates_preserve_existing_values(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        SiteSetting::set('site_name', 'Persistent Name');
        SiteSetting::set('site_tagline', 'Persistent Tagline');
        SiteSetting::set('commission_rate', 14.5);
        SiteSetting::set('platform_mode', 'single_vendor');

        // Only update commission_rate
        $res = $this->post(route('admin.settings.update'), [
            'commission_rate' => 18.0,
        ]);
        $res->assertSessionHas('success');

        $this->assertEquals('Persistent Name', SiteSetting::get('site_name'));
        $this->assertEquals('Persistent Tagline', SiteSetting::get('site_tagline'));
        $this->assertEquals(18.0, SiteSetting::getCommissionRate());
        $this->assertEquals('single_vendor', SiteSetting::get('platform_mode'));
    }

    /**
     * CHALLENGE 14: Unicode and emoji characters in site name and tagline are handled safely.
     */
    public function test_unicode_and_special_characters_in_branding(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $unicodeName = 'Bazaario 🛍️ Artisans';
        $unicodeTagline = 'Handmade in India — शुद्ध एवं प्रामाणिक ✨';

        $response = $this->post(route('admin.settings.update'), [
            'site_name' => $unicodeName,
            'site_tagline' => $unicodeTagline,
        ]);
        $response->assertSessionHas('success');

        $this->assertEquals($unicodeName, SiteSetting::getSiteName());
        $this->assertEquals($unicodeTagline, SiteSetting::getTagline());

        // View renders correctly without 500 error
        $viewRes = $this->get(route('admin.settings.index'));
        $viewRes->assertStatus(200);
        $viewRes->assertSee('Bazaario 🛍️ Artisans');
    }
}

