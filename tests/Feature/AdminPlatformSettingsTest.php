<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\SiteSetting;

class AdminPlatformSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected User $sellerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Admin Boss',
            'email' => 'admin@bazaario.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->regularUser = User::create([
            'name' => 'Regular Buyer',
            'email' => 'buyer@bazaario.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->sellerUser = User::create([
            'name' => 'Merchant Seller',
            'email' => 'seller@bazaario.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'seller',
            'status' => 'active',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_admin_settings(): void
    {
        $response = $this->get(route('admin.settings.index'));
        $response->assertRedirect(route('admin.login'));

        $updateResponse = $this->post(route('admin.settings.update'), [
            'site_name' => 'Hacked Name',
        ]);
        $updateResponse->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_users_cannot_access_admin_settings(): void
    {
        // Regular user
        $this->actingAs($this->regularUser, 'user');
        $response = $this->get(route('admin.settings.index'));
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));

        // Seller user
        $this->actingAs($this->sellerUser, 'seller');
        $response = $this->get(route('admin.settings.index'));
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
    }

    public function test_admin_can_view_settings_index_page(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->get(route('admin.settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Platform Configuration & Branding', false);
        $response->assertSee('General & Branding', false);
        $response->assertSee('Platform Architecture & Mode', false);
        $response->assertSee('Global Commissions & Escrow', false);
    }

    public function test_admin_can_update_site_name_and_tagline(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->post(route('admin.settings.update'), [
            'site_name' => 'Bazaario Express',
            'site_tagline' => 'Handcrafted Excellence and Fast Delivery',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('Bazaario Express', SiteSetting::getSiteName());
        $this->assertEquals('Handcrafted Excellence and Fast Delivery', SiteSetting::getTagline());
    }

    public function test_admin_can_update_commission_rate(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->post(route('admin.settings.update'), [
            'commission_rate' => 12.50,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(12.50, SiteSetting::getCommissionRate());
        $this->assertEquals(12.50, (float)SiteSetting::get('platform_commission_base'));
    }

    public function test_admin_can_upload_custom_logo(): void
    {
        Storage::fake('public');

        $this->actingAs($this->adminUser, 'admin');

        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $file = UploadedFile::fake()->createWithContent('custom_store_logo.png', $pngContent);

        $response = $this->post(route('admin.settings.update'), [
            'site_logo' => $file,
        ]);

        $response->assertSessionHas('success');

        $storedPath = SiteSetting::get('site_logo');
        $this->assertNotNull($storedPath);
        Storage::disk('public')->assertExists($storedPath);

        $logoUrl = SiteSetting::getLogoUrl();
        $this->assertStringContainsString($storedPath, $logoUrl);
    }

    public function test_admin_can_toggle_platform_mode_to_single_vendor(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->post(route('admin.settings.update'), [
            'platform_mode' => 'single_vendor',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(SiteSetting::isSingleVendor());
        $this->assertFalse(SiteSetting::isMultiVendor());
    }

    public function test_single_vendor_mode_disables_seller_registration(): void
    {
        SiteSetting::set('platform_mode', 'single_vendor');

        $payload = [
            'name' => 'Prospective Artisan',
            'email' => 'artisan@bazaario.com',
            'password' => 'SecretPass123!',
            'password_confirmation' => 'SecretPass123!',
            'role' => 'seller',
            'terms' => true,
        ];

        $response = $this->postJson(route('register.send-otp'), $payload);
        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Seller registration is currently disabled in Single-Vendor mode.',
        ]);
    }

    public function test_single_vendor_mode_hides_seller_role_in_register_view(): void
    {
        SiteSetting::set('platform_mode', 'single_vendor');

        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertDontSee('id="card-type-seller"', false);
    }

    public function test_single_vendor_mode_hides_seller_shelves_on_homepage(): void
    {
        SiteSetting::set('platform_mode', 'single_vendor');

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertDontSee('5.5 FEATURED SELLERS SHELF', false);
        $response->assertDontSee('id="nearby-stalls"', false);
    }

    public function test_single_vendor_mode_redirects_seller_documentation_pages(): void
    {
        SiteSetting::set('platform_mode', 'single_vendor');

        $response1 = $this->get(route('docs.become-a-seller'));
        $response1->assertRedirect(route('home'));
        $response1->assertSessionHas('info');

        $response2 = $this->get(route('docs.fees-and-commission'));
        $response2->assertRedirect(route('home'));
        $response2->assertSessionHas('info');
    }

    public function test_multi_vendor_mode_restores_seller_features(): void
    {
        SiteSetting::set('platform_mode', 'multi_vendor');

        // Register view has seller card
        $registerRes = $this->get(route('register'));
        $registerRes->assertStatus(200);
        $registerRes->assertSee('id="card-type-seller"', false);

        // Homepage has featured sellers & nearby stalls
        $homeRes = $this->get(route('home'));
        $homeRes->assertStatus(200);
        $homeRes->assertSee('Featured Sellers');
        $homeRes->assertSee('Nearby Stalls');

        // Docs accessible
        $docs1 = $this->get(route('docs.become-a-seller'));
        $docs1->assertStatus(200);

        $docs2 = $this->get(route('docs.fees-and-commission'));
        $docs2->assertStatus(200);
    }

    public function test_site_setting_helpers_provide_safe_defaults(): void
    {
        // When settings are cleared
        SiteSetting::whereIn('setting_key', ['site_name', 'site_tagline', 'site_logo', 'platform_mode', 'commission_rate', 'platform_commission_base'])->delete();

        $this->assertEquals('Bazaario', SiteSetting::getSiteName());
        $this->assertEquals('Smarter Shopping. Local Sellers. Live Auctions.', SiteSetting::getTagline());
        $this->assertStringContainsString('bazaario-logo.png', SiteSetting::getLogoUrl());
        $this->assertFalse(SiteSetting::isSingleVendor());
        $this->assertTrue(SiteSetting::isMultiVendor());
        $this->assertEquals(8.5, SiteSetting::getCommissionRate());
    }
}
