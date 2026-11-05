## 2026-10-12T05:33:07Z
You are Worker M1 tasked with implementing Milestone 1: Admin Platform Configuration & Branding Settings (R1) for Bazaario.
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m1
Project root: c:\xampp\htdocs\bazaario
Original Request file: c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (read timestamp ## 2026-10-12T05:17:47Z).
Project Specification: c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_10\PROJECT.md
Explorer Survey Report: c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_r1\report.md
Explorer Survey Handoff: c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_r1\handoff.md

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

File Ownership:
You own exclusively:
- `app/Models/SiteSetting.php`
- `app/Http/Controllers/Admin/AdminDashboardController.php` (settings methods)
- `routes/web.php` (admin.settings routes)
- `resources/views/admin/settings/index.blade.php`, `general.blade.php`, `platform.blade.php`, `commissions.blade.php`
- `resources/views/layouts/admin.blade.php` (sidebar settings link & dynamic branding)
- `resources/views/auth/register.blade.php` (single-vendor mode toggle)
- `app/Http/Controllers/AuthController.php` (single-vendor registration validation)
- `app/Http/Controllers/ProductController.php` (single-vendor seller features handling)
- `resources/views/index.blade.php` (hide seller shelves when single-vendor)
- `tests/Feature/AdminPlatformSettingsTest.php`

Implementation Requirements:
1. `SiteSetting.php`:
   - Add static helper methods:
     - `getSiteName(string $default = 'Bazaario'): string`
     - `getTagline(string $default = 'Smarter Shopping. Local Sellers. Live Auctions.'): string`
     - `getLogoUrl(): string` (returns uploaded logo URL if present, otherwise returns `asset('images/bazaario-logo.png')` or `asset('images/Neon Bazaario Eco-Commerce Logo.png')`)
     - `isSingleVendor(): bool` (returns true if `platform_mode === 'single_vendor'`)
     - `getCommissionRate(float $default = 8.5): float`
2. `routes/web.php`:
   - Under admin authenticated & role guard group, register:
     - `Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings.index');`
     - `Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');`
3. `AdminDashboardController.php`:
   - Implement `settings(Request $request)`: renders `admin.settings.index` with current site settings mapped dictionary.
   - Implement `updateSettings(Request $request)`: validates and saves `site_name`, `site_tagline`, `commission_rate`, `platform_mode` (`in:multi_vendor,single_vendor`), and handles `site_logo` image upload (stored to public disk). Flashes success toast and redirects back.
4. `resources/views/admin/settings/`:
   - Populate `index.blade.php` with a unified modern tabbed settings dashboard (General/Branding, Platform Mode Switch, Global Commission Rates, AI Ops link).
   - Also populate `general.blade.php`, `platform.blade.php`, `commissions.blade.php` as modular sub-views or standalone tab views so no 0-byte placeholder files remain.
   - Follow Warm Modernist design system (Plus Jakarta Sans / Space Grotesk, Inter, rounded-[14px], emerald/amber badges).
5. `resources/views/layouts/admin.blade.php`:
   - Add "Platform Settings" navigation link in sidebar linking to `route('admin.settings.index')`.
   - Use `SiteSetting::getSiteName()` and `SiteSetting::getLogoUrl()` in header/brand.
6. Single-Vendor Mode Behavior Enforcement:
   - In `AuthController::sendOtp` (and registration validation): if `SiteSetting::isSingleVendor()` and `role === 'seller'`, reject with 422 / error redirect: "Seller registration is currently disabled in Single-Vendor mode."
   - In `resources/views/auth/register.blade.php`: wrap the seller role selector in `@unless(\App\Models\SiteSetting::isSingleVendor())` so it is hidden.
   - In `ProductController::home`: if `SiteSetting::isSingleVendor()`, ensure `featuredSellers` and `nearbyStalls` are empty collections.
   - In `resources/views/index.blade.php`: hide Featured Sellers and Nearby Stalls sections if `\App\Models\SiteSetting::isSingleVendor()`.
   - In `ProductController::becomeASeller` and `feesAndCommission`: if single-vendor mode, redirect or flash message indicating multi-vendor marketplace mode is disabled.
7. Verification & Tests:
   - Create comprehensive feature test suite in `tests/Feature/AdminPlatformSettingsTest.php`:
     - Admin can view settings index page.
     - Admin can update site name, tagline, commission rate, and platform mode.
     - Admin can upload custom logo image.
     - Single-vendor mode disables seller registration.
     - Single-vendor mode hides seller options in register view and homepage shelves.
     - Non-admin / unauthenticated users cannot access settings routes (403/302).
   - Execute tests:
     `php artisan test tests/Feature/AdminPlatformSettingsTest.php`
     `php artisan test --filter=AdminHardeningTest`
   - Ensure all tests pass with 0 failures.

Deliverables:
- Write detailed log of changes to `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m1\changes.md`.
- Write handoff report to `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m1\handoff.md`.
- Send message to parent upon completion.
