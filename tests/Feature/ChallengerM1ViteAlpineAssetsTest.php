<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

class ChallengerM1ViteAlpineAssetsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Verify manifest.json contains valid compiled CSS and JS entries
     * and that the referenced asset files actually exist on disk.
     */
    public function test_manifest_entries_exist_and_point_to_valid_files()
    {
        $manifestPath = public_path('build/manifest.json');
        $this->assertFileExists($manifestPath, 'public/build/manifest.json must exist');

        $manifest = json_decode(File::get($manifestPath), true);
        $this->assertIsArray($manifest);

        $this->assertArrayHasKey('resources/css/app.css', $manifest);
        $this->assertArrayHasKey('resources/js/app.js', $manifest);

        $cssFile = public_path('build/' . $manifest['resources/css/app.css']['file']);
        $jsFile = public_path('build/' . $manifest['resources/js/app.js']['file']);

        $this->assertFileExists($cssFile, 'Compiled CSS file must exist on disk');
        $this->assertFileExists($jsFile, 'Compiled JS file must exist on disk');

        $this->assertGreaterThan(50000, filesize($cssFile), 'Compiled CSS must be non-trivial (>50KB)');
        $this->assertGreaterThan(50000, filesize($jsFile), 'Compiled JS must be non-trivial (>50KB)');
    }

    /**
     * Test 2: Verify window.Alpine export in compiled JS bundle.
     */
    public function test_compiled_js_bundle_exports_window_alpine()
    {
        $manifestPath = public_path('build/manifest.json');
        $manifest = json_decode(File::get($manifestPath), true);
        $jsFile = public_path('build/' . $manifest['resources/js/app.js']['file']);

        $jsContent = File::get($jsFile);

        $this->assertStringContainsString('window.Alpine', $jsContent, 'Compiled JS bundle must assign window.Alpine');
        $this->assertMatchesRegularExpression('/window\.Alpine\s*=\s*[a-zA-Z0-9_$]+/', $jsContent, 'window.Alpine assignment must be present');
    }

    /**
     * Test 3: Verify Tailwind v4 @theme seller tokens compile into CSS utilities.
     */
    public function test_compiled_css_contains_seller_theme_utilities()
    {
        $manifestPath = public_path('build/manifest.json');
        $manifest = json_decode(File::get($manifestPath), true);
        $cssFile = public_path('build/' . $manifest['resources/css/app.css']['file']);

        $cssContent = File::get($cssFile);

        // Core seller surface utilities
        $this->assertStringContainsString('.bg-surface', $cssContent, 'CSS must contain .bg-surface');
        $this->assertStringContainsString('.bg-surface-container', $cssContent, 'CSS must contain .bg-surface-container');
        $this->assertStringContainsString('.bg-surface-container-low', $cssContent, 'CSS must contain .bg-surface-container-low');
        $this->assertStringContainsString('.bg-surface-container-highest', $cssContent, 'CSS must contain .bg-surface-container-highest');

        // Brand colors
        $this->assertStringContainsString('.text-brand-amber', $cssContent, 'CSS must contain .text-brand-amber');
        $this->assertStringContainsString('--color-brand-amber', $cssContent, 'CSS must define --color-brand-amber');
        $this->assertStringContainsString('--color-surface', $cssContent, 'CSS must define --color-surface');

        // Typography & layout tokens
        $this->assertStringContainsString('.font-heading', $cssContent, 'CSS must contain .font-heading');
        $this->assertStringContainsString('.rounded-custom', $cssContent, 'CSS must contain .rounded-custom');
    }

    /**
     * Test 4: Verify rendered HTML of Home page (index) has no double CSS and no double Alpine.
     */
    public function test_home_page_rendered_html_has_no_duplicate_assets()
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $html = $response->getContent();

        // Must NOT contain static link fallback
        $this->assertStringNotContainsString('app-C-FKvfT_.css', $html, 'Home page must not link to legacy app-C-FKvfT_.css');

        // Must contain exactly 1 stylesheet link
        preg_match_all('/<link[^>]+rel="stylesheet"[^>]+href="[^"]*build\/assets\/app-[^"]*\.css"[^>]*>/i', $html, $stylesheetMatches);
        $this->assertCount(1, $stylesheetMatches[0], 'Home page must contain exactly 1 compiled CSS stylesheet link');

        // Must NOT load Alpine from CDN
        $this->assertStringNotContainsString('cdn.jsdelivr.net/npm/alpinejs', $html, 'Home page must not load Alpine from CDN');

        // Viewport must not have user-scalable=no
        $this->assertStringNotContainsString('user-scalable=no', $html, 'Home page must not disable user scaling');
    }

    /**
     * Test 5: Verify rendered HTML of Products Index has no duplicate assets and has valid viewport.
     */
    public function test_products_index_rendered_html_has_no_duplicate_assets()
    {
        $response = $this->get(route('products.index'));
        $response->assertStatus(200);

        $html = $response->getContent();

        // Must NOT contain static link fallback
        $this->assertStringNotContainsString('app-C-FKvfT_.css', $html, 'Products index must not link to legacy app-C-FKvfT_.css');

        // Must contain exactly 1 stylesheet link
        preg_match_all('/<link[^>]+rel="stylesheet"[^>]+href="[^"]*build\/assets\/app-[^"]*\.css"[^>]*>/i', $html, $stylesheetMatches);
        $this->assertCount(1, $stylesheetMatches[0], 'Products index must contain exactly 1 compiled CSS stylesheet link');

        // Must NOT load Alpine from CDN
        $this->assertStringNotContainsString('cdn.jsdelivr.net/npm/alpinejs', $html, 'Products index must not load Alpine from CDN');

        // Must not contain user-scalable=no
        $this->assertStringNotContainsString('user-scalable=no', $html, 'Products index must not have user-scalable=no (WCAG 1.4.4)');
    }

    /**
     * Test 6: Verify Seller layout uses Vite assets and has no Tailwind CDN or Alpine CDN.
     */
    public function test_seller_layout_uses_vite_and_no_cdn()
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id' => $seller->id,
            'shop_name' => 'Test Shop',
            'shop_slug' => 'test-shop',
            'seller_type' => 'Farmer',
            'status' => 'approved',
            'commission_rate' => 5.0,
            'trust_score' => 95.0,
            'trading_access' => true,
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $response->assertStatus(200);

        $html = $response->getContent();

        // Must NOT load Tailwind CDN
        $this->assertStringNotContainsString('cdn.tailwindcss.com', $html, 'Seller dashboard must not load Tailwind CDN');

        // Must NOT load Alpine CDN
        $this->assertStringNotContainsString('cdn.jsdelivr.net/npm/alpinejs', $html, 'Seller dashboard must not load Alpine CDN');

        // Must contain Vite compiled CSS and JS
        $this->assertMatchesRegularExpression('/<link[^>]+href="[^"]*build\/assets\/app-[^"]*\.css"[^>]*>/i', $html);
        $this->assertMatchesRegularExpression('/<script[^>]+src="[^"]*build\/assets\/app-[^"]*\.js"[^>]*>/i', $html);
    }
}
