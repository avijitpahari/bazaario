<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class Milestone1InfrastructureChallengeTest extends TestCase
{
    use RefreshDatabase;

    protected array $m1TargetViews = [
        'layouts/app' => 'resources/views/layouts/app.blade.php',
        'home_index'  => 'resources/views/index.blade.php',
        'seller'      => 'resources/views/layouts/seller.blade.php',
        'products'    => 'resources/views/user/products/index.blade.php',
    ];

    /**
     * Helper to load view content.
     */
    protected function getViewContent(string $relativePath): string
    {
        $path = base_path($relativePath);
        $this->assertFileExists($path, "Expected view file {$relativePath} to exist.");
        return File::get($path);
    }

    /**
     * 1. Adversarially verify zero occurrences of static hardcoded CSS build hash in M1 target views.
     */
    public function test_target_views_have_zero_occurrences_of_static_css_hash(): void
    {
        foreach ($this->m1TargetViews as $name => $path) {
            $content = $this->getViewContent($path);
            $this->assertStringNotContainsString(
                'app-C-FKvfT_.css',
                $content,
                "View [{$name}] still contains hardcoded static CSS hash 'app-C-FKvfT_.css'"
            );
        }
    }

    /**
     * 2. Adversarially verify zero occurrences of Tailwind CDN in M1 target views.
     */
    public function test_target_views_have_zero_occurrences_of_tailwind_cdn(): void
    {
        foreach ($this->m1TargetViews as $name => $path) {
            $content = $this->getViewContent($path);
            $this->assertStringNotContainsString(
                'cdn.tailwindcss.com',
                $content,
                "View [{$name}] still references Tailwind CDN"
            );
        }
    }

    /**
     * 3. Adversarially verify zero occurrences of Alpine CDN in M1 target views.
     */
    public function test_target_views_have_zero_occurrences_of_alpine_cdn(): void
    {
        foreach ($this->m1TargetViews as $name => $path) {
            $content = $this->getViewContent($path);
            $this->assertStringNotContainsString(
                'cdn.jsdelivr.net/npm/alpinejs',
                $content,
                "View [{$name}] still references jsdelivr Alpine CDN"
            );
            $this->assertStringNotContainsString(
                'alpinejs@3',
                $content,
                "View [{$name}] still references alpinejs@3 CDN"
            );
        }
    }

    /**
     * 4. Adversarially verify zero occurrences of user-scalable=no in M1 views and globally across all Blade views.
     */
    public function test_zero_occurrences_of_user_scalable_no_in_all_blade_views(): void
    {
        // First check M1 target views
        foreach ($this->m1TargetViews as $name => $path) {
            $content = $this->getViewContent($path);
            $this->assertStringNotContainsString(
                'user-scalable=no',
                $content,
                "View [{$name}] contains 'user-scalable=no' violating WCAG 1.4.4"
            );
            $this->assertStringNotContainsString(
                'user-scalable=0',
                $content,
                "View [{$name}] contains 'user-scalable=0' violating WCAG 1.4.4"
            );
        }

        // Broad check across all blade views
        $allBladeFiles = File::allFiles(resource_path('views'));
        $violations = [];
        foreach ($allBladeFiles as $file) {
            $content = File::get($file->getPathname());
            if (str_contains($content, 'user-scalable=no') || str_contains($content, 'user-scalable=0') || str_contains($content, 'user-scalable = no')) {
                $violations[] = $file->getRelativePathname();
            }
        }

        $this->assertEmpty($violations, 'Found user-scalable=no violations in: ' . implode(', ', $violations));
    }

    /**
     * 5. Verify @vite directive is present in all M1 target views.
     */
    public function test_target_views_contain_vite_directives(): void
    {
        foreach ($this->m1TargetViews as $name => $path) {
            $content = $this->getViewContent($path);
            $this->assertMatchesRegularExpression(
                '/@vite\s*\(\s*\[\s*[\'"]resources\/css\/app\.css[\'"]\s*,\s*[\'"]resources\/js\/app\.js[\'"]\s*\]\s*\)/',
                $content,
                "View [{$name}] is missing standard @vite(['resources/css/app.css', 'resources/js/app.js']) directive"
            );
        }
    }

    /**
     * 6. Adversarially verify public/build/manifest.json validity and that referenced assets exist on disk.
     */
    public function test_vite_manifest_exists_and_references_valid_compiled_assets(): void
    {
        $manifestPath = public_path('build/manifest.json');
        $this->assertFileExists($manifestPath, 'Vite manifest does not exist at public/build/manifest.json');

        $manifest = json_decode(File::get($manifestPath), true);
        $this->assertIsArray($manifest, 'Vite manifest is not valid JSON');

        $this->assertArrayHasKey('resources/css/app.css', $manifest, 'Manifest missing resources/css/app.css entry');
        $this->assertArrayHasKey('resources/js/app.js', $manifest, 'Manifest missing resources/js/app.js entry');

        $cssFile = public_path('build/' . $manifest['resources/css/app.css']['file']);
        $jsFile = public_path('build/' . $manifest['resources/js/app.js']['file']);

        $this->assertFileExists($cssFile, "Compiled CSS file {$cssFile} does not exist on disk");
        $this->assertFileExists($jsFile, "Compiled JS file {$jsFile} does not exist on disk");

        $this->assertGreaterThan(50000, filesize($cssFile), 'Compiled CSS file appears suspiciously small (< 50KB)');
        $this->assertGreaterThan(50000, filesize($jsFile), 'Compiled JS file appears suspiciously small (< 50KB)');
    }

    /**
     * 7. Verify P17 design tokens in resources/css/app.css and in compiled CSS.
     */
    public function test_design_system_tokens_in_app_css_and_compiled_css(): void
    {
        $appCss = File::get(resource_path('css/app.css'));

        $requiredCssTokens = [
            '--font-sans',
            '--font-display',
            '--font-heading',
            '--font-mono',
            '--color-slate-authority',
            '--color-amber-action',
            '--color-brand-amber',
            '--color-surface',
            '--color-surface-container',
            '--radius-custom',
        ];

        foreach ($requiredCssTokens as $token) {
            $this->assertStringContainsString(
                $token,
                $appCss,
                "resources/css/app.css is missing design token {$token}"
            );
        }

        // Check compiled CSS
        $manifest = json_decode(File::get(public_path('build/manifest.json')), true);
        $compiledCss = File::get(public_path('build/' . $manifest['resources/css/app.css']['file']));

        $this->assertStringContainsString('amber-action', $compiledCss, 'Compiled CSS missing amber-action token');
        $this->assertStringContainsString('surface', $compiledCss, 'Compiled CSS missing surface token');
    }

    /**
     * 8. Verify Alpine is bundled in resources/js/app.js and compiled JS.
     */
    public function test_alpinejs_bundled_in_vite_entrypoint(): void
    {
        $appJs = File::get(resource_path('js/app.js'));
        $this->assertStringContainsString("import Alpine from 'alpinejs'", $appJs);
        $this->assertStringContainsString('window.Alpine = Alpine', $appJs);
        $this->assertStringContainsString('Alpine.start()', $appJs);

        $manifest = json_decode(File::get(public_path('build/manifest.json')), true);
        $compiledJs = File::get(public_path('build/' . $manifest['resources/js/app.js']['file']));
        $this->assertStringContainsString('Alpine', $compiledJs, 'Compiled JS does not appear to contain Alpine');
    }

    /**
     * 9. Rendered HTML check: Home page GET '/' outputs valid Vite hashed tags and zero CDN.
     */
    public function test_rendered_home_page_outputs_hashed_vite_tags_and_no_cdn(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $html = $response->getContent();

        $this->assertStringContainsString('build/assets/app-', $html, 'Rendered HTML missing Vite hashed CSS/JS');
        $this->assertStringNotContainsString('app-C-FKvfT_.css', $html, 'Rendered HTML contains stale CSS hash');
        $this->assertStringNotContainsString('cdn.tailwindcss.com', $html, 'Rendered HTML contains Tailwind CDN');
        $this->assertStringNotContainsString('cdn.jsdelivr.net/npm/alpinejs', $html, 'Rendered HTML contains Alpine CDN');
        $this->assertStringNotContainsString('user-scalable=no', $html, 'Rendered HTML contains user-scalable=no');
    }

    /**
     * 10. Rendered HTML check: Products Catalog GET '/products' outputs valid Vite hashed tags and zero CDN.
     */
    public function test_rendered_products_catalog_outputs_hashed_vite_tags_and_no_cdn(): void
    {
        $response = $this->get('/products');
        $response->assertStatus(200);

        $html = $response->getContent();

        $this->assertStringContainsString('build/assets/app-', $html, 'Rendered HTML missing Vite hashed CSS/JS');
        $this->assertStringNotContainsString('app-C-FKvfT_.css', $html, 'Rendered HTML contains stale CSS hash');
        $this->assertStringNotContainsString('cdn.tailwindcss.com', $html, 'Rendered HTML contains Tailwind CDN');
        $this->assertStringNotContainsString('cdn.jsdelivr.net/npm/alpinejs', $html, 'Rendered HTML contains Alpine CDN');
        $this->assertStringNotContainsString('user-scalable=no', $html, 'Rendered HTML contains user-scalable=no');
    }

    /**
     * 11. Rendered HTML check: Seller layout via GET '/seller/dashboard' outputs valid Vite hashed tags and zero CDN.
     */
    public function test_rendered_seller_dashboard_outputs_hashed_vite_tags_and_no_cdn(): void
    {
        $seller = User::create([
            'name'               => 'Test Seller',
            'email'              => 'seller_m1_test@example.com',
            'password'           => Hash::make('Password123!'),
            'role'               => 'seller',
            'status'             => 'active',
            'preferred_language' => 'en',
            'email_verified_at'  => now(),
        ]);

        SellerProfile::create([
            'user_id'         => $seller->id,
            'shop_name'       => 'M1 Test Stall',
            'shop_slug'       => 'm1-test-stall',
            'status'          => 'approved',
            'commission_rate' => 10.00,
            'trust_score'     => 90.00,
            'city'            => 'Kolkata',
            'state'           => 'West Bengal',
            'country'         => 'India',
        ]);

        $response = $this->actingAs($seller, 'seller')->get('/seller/dashboard');
        $response->assertStatus(200);

        $html = $response->getContent();

        $this->assertStringContainsString('build/assets/app-', $html, 'Seller rendered HTML missing Vite hashed CSS/JS');
        $this->assertStringNotContainsString('cdn.tailwindcss.com', $html, 'Seller rendered HTML contains Tailwind CDN');
        $this->assertStringNotContainsString('cdn.jsdelivr.net/npm/alpinejs', $html, 'Seller rendered HTML contains Alpine CDN');
        $this->assertStringNotContainsString('app-C-FKvfT_.css', $html, 'Seller rendered HTML contains stale CSS hash');
        $this->assertStringNotContainsString('user-scalable=no', $html, 'Seller rendered HTML contains user-scalable=no');
    }

    /**
     * 12. Rendered Blade view check: Master layouts.app rendered dynamically.
     */
    public function test_rendered_app_layout_outputs_hashed_vite_tags_and_no_cdn(): void
    {
        $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bazaario_stub_' . uniqid();
        mkdir($tempDir . DIRECTORY_SEPARATOR . 'components', 0777, true);
        file_put_contents($tempDir . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'navbar.blade.php', '<nav>Stub Navbar</nav>');
        $this->app['view']->getFinder()->addLocation($tempDir);

        $blade = <<<'BLADE'
@extends('layouts.app')
@section('title', 'M1 Test Page')
@section('content')
    <div id="m1-content">Test Content</div>
@endsection
BLADE;

        $html = Blade::render($blade);

        $this->assertStringContainsString('build/assets/app-', $html, 'Master layout missing Vite hashed tags');
        $this->assertStringNotContainsString('app-C-FKvfT_.css', $html, 'Master layout contains stale CSS hash');
        $this->assertStringNotContainsString('cdn.tailwindcss.com', $html, 'Master layout contains Tailwind CDN');
        $this->assertStringNotContainsString('cdn.jsdelivr.net/npm/alpinejs', $html, 'Master layout contains Alpine CDN');
        $this->assertStringNotContainsString('user-scalable=no', $html, 'Master layout contains user-scalable=no');

        // Cleanup temp stub
        @unlink($tempDir . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'navbar.blade.php');
        @rmdir($tempDir . DIRECTORY_SEPARATOR . 'components');
        @rmdir($tempDir);
    }

    /**
     * 13. Verify all M1 target views compile cleanly without PHP syntax or Blade parsing errors.
     */
    public function test_target_views_compile_cleanly_via_blade(): void
    {
        foreach ($this->m1TargetViews as $name => $path) {
            $content = $this->getViewContent($path);
            $compiled = Blade::compileString($content);
            $this->assertNotEmpty($compiled, "Blade compilation returned empty for [{$name}]");

            // Check syntax of compiled blade string
            $tmpFile = tempnam(sys_get_temp_dir(), 'blade_check_');
            file_put_contents($tmpFile, $compiled);
            $output = [];
            $returnCode = 0;
            exec('php -l ' . escapeshellarg($tmpFile) . ' 2>&1', $output, $returnCode);
            @unlink($tmpFile);

            $this->assertSame(0, $returnCode, "Compiled Blade syntax error in [{$name}]: " . implode("\n", $output));
        }
    }

    /**
     * 14. Forensic scan of all views to catalog residual legacy asset tags outside of M1 scope.
     */
    public function test_forensic_residual_asset_inventory_outside_m1(): void
    {
        $allBladeFiles = File::allFiles(resource_path('views'));
        $legacyCssFiles = [];
        $tailwindCdnFiles = [];
        $alpineCdnFiles = [];

        foreach ($allBladeFiles as $file) {
            $relPath = str_replace('\\', '/', $file->getRelativePathname());
            // Ignore M1 files in this check
            $content = File::get($file->getPathname());

            if (str_contains($content, 'app-C-FKvfT_.css')) {
                $legacyCssFiles[] = $relPath;
            }
            if (str_contains($content, 'cdn.tailwindcss.com')) {
                $tailwindCdnFiles[] = $relPath;
            }
            if (str_contains($content, 'cdn.jsdelivr.net/npm/alpinejs')) {
                $alpineCdnFiles[] = $relPath;
            }
        }

        // Assert M1 target views are NOT in any of these residual lists
        $m1RelativePaths = [
            'layouts/app.blade.php',
            'index.blade.php',
            'layouts/seller.blade.php',
            'user/products/index.blade.php',
        ];

        foreach ($m1RelativePaths as $target) {
            $this->assertNotContains($target, $legacyCssFiles, "M1 view {$target} leaked into legacyCssFiles");
            $this->assertNotContains($target, $tailwindCdnFiles, "M1 view {$target} leaked into tailwindCdnFiles");
            $this->assertNotContains($target, $alpineCdnFiles, "M1 view {$target} leaked into alpineCdnFiles");
        }
    }
}


