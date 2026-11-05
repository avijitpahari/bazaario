# Comprehensive Survey Report: Admin Platform Configuration & Branding Settings (R1)

**Subagent**: `explorer_survey_r1`  
**Date**: 2026-10-12  
**Target Requirement**: Requirement 1 (R1) — Admin Platform Configuration & Branding Settings  
**Repository**: `c:\xampp\htdocs\bazaario`  

---

## Executive Summary

This report delivers an exhaustive architectural survey of the **Bazaario** marketplace codebase to map all components, models, controllers, routes, views, database structures, and testing suites necessary to implement **Requirement 1: Admin Platform Configuration & Branding Settings (R1)**.

### Core Survey Findings:
1. **Model & Schema Foundation Ready**: The `site_settings` table (migration `2026_09_11_000027_create_site_settings_table.php`) and Eloquent model `App\Models\SiteSetting` already exist with flexible key-value persistence (`setting_key`, `setting_value`), JSON casting, and bulk dictionary lookup (`allMap()`). No database schema migration changes are necessary.
2. **Settings Portal Empty State**: Within `resources/views/admin/settings/`, only `ai.blade.php` is implemented. Key views `index.blade.php`, `general.blade.php`, `platform.blade.php`, and `commissions.blade.php` are currently **0-byte empty files**. In `routes/web.php`, only AI engine settings (`admin.settings.ai`) are wired; there is no route registered for general settings.
3. **Hardcoded Platform Branding**: Site name (`Bazaario`), logo files (`bazaario-logo.png`, `Neon Bazaario Eco-Commerce Logo.png`), and tagline (`Smarter Shopping. Local Sellers. Live Auctions.`) are hardcoded across `resources/views/layouts/admin.blade.php`, `layouts/app.blade.php`, `components/nav.blade.php`, `components/nav-user.blade.php`, and `components/footer.blade.php`.
4. **Multi-Vendor vs Single-Vendor Touchpoints**:
   - Registration is handled by `AuthController::sendOtp` and `resources/views/auth/register.blade.php` where a radio card selector enables `role="seller"`.
   - Multi-vendor documentation & onboarding reside at `ProductController::becomeASeller`, `ProductController::feesAndCommission`, and `SellerOnboardingController::showWizard`.
   - Seller storefronts, nearby stalls, and trust badges are rendered on the homepage (`ProductController::home`, `resources/views/index.blade.php` Sections 5.5 and 5.6), product search (`ProductController::index`), and product detail (`resources/views/user/products/show.blade.php`).
5. **Testing Landscape**: Current test suites (`AdminHardeningTest`, `AdminChallengerVerificationTest`) strictly verify 38 admin routes and the AI settings endpoint (`admin.settings.ai`). There are zero tests covering platform branding, logo uploads, commission changes, or platform mode switching.

---

## 1. Route & Controller Architecture (Task 1)

### 1.1 Existing Route Structure (`routes/web.php`)
- **Route file location**: `routes/web.php` (no separate `routes/admin.php` exists).
- **Admin route prefix and middleware**:
  ```php
  Route::prefix('admin')->name('admin.')->group(function () {
      Route::get('/', [AdminAuthController::class, 'showLogin'])->name('login');
      Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');

      Route::middleware(['auth:admin', 'admin'])->group(function () {
          // Lines 290 - 354
      });
  });
  ```
- **Existing Settings Routes (Lines 345-348)**:
  ```php
  // AI Engine Configuration
  Route::get('/settings/ai', [AdminDashboardController::class, 'aiSettings'])->name('settings.ai');
  Route::post('/settings/ai', [AdminDashboardController::class, 'updateAiSettings'])->name('settings.ai.update');
  ```
- **Missing Settings Routes**:
  There are currently no routes for:
  - `admin.settings.index` (`GET /admin/settings`)
  - `admin.settings.update` (`POST /admin/settings` or `PUT /admin/settings`)
  - Sub-resource endpoints for `general`, `platform`, `commissions`, or `logo.delete`.

### 1.2 Existing Admin Controllers (`app/Http/Controllers/Admin/`)
The directory contains three controllers:
1. `AdminAuthController.php` (2,316 bytes): Authentication, login submission, session lifecycle.
2. `AdminDashboardController.php` (51,480 bytes): Central monolith handling merchants, products, categories, orders, auctions, payouts, disputes, customers, coupons, and AI settings.
   - `aiSettings()`: Lines 1201–1205. Calls `SiteSetting::allMap()` and renders `admin.settings.ai`.
   - `updateAiSettings(Request $request)`: Lines 1208–1251. Validates Gemini parameters, executes inside `DB::transaction`, and persists via `SiteSetting::set($key, $val)`.
3. `ImpersonationController.php` (2,536 bytes): User/seller session impersonation with security auditing.

### 1.3 Recommended Controller Action Plan
Add dedicated methods to `AdminDashboardController` (or introduce `AdminSettingsController` and map cleanly):
- `settings()`: Fetches `SiteSetting::allMap()`, returns view `admin.settings.index`.
- `updateSettings(Request $request)`:
  - Validates `site_name` (max:100), `site_tagline` (max:255), `site_logo` (image|mimes:png,jpg,jpeg,svg,webp|max:2048), `platform_commission_base` (numeric|min:0|max:100), `platform_mode` (in:multi_vendor,single_vendor).
  - Handles logo upload to `public/branding` disk storage and updates `site_logo` path in `site_settings`.
  - Persists all keys inside `DB::transaction` with sanitization (`trim(strip_tags(...))`).
- `deleteLogo()`: Removes custom uploaded logo, reverting to default system assets.
- Alias routes for `general`, `platform`, `commissions` if separate tabs/views are rendered.

---

## 2. Admin Views & UI Design System (Task 2)

### 2.1 View Inventory (`resources/views/admin/settings/`)
| File | Size (Bytes) | Line Count | Status | Notes |
|------|-------------:|-----------:|--------|-------|
| `ai.blade.php` | 12,816 | 206 | **Populated** | Extends `layouts.admin`, uses Amber Modernist UI |
| `index.blade.php` | 0 | 1 | **Empty** | Target for main unified settings portal |
| `general.blade.php` | 0 | 1 | **Empty** | Can serve as General / Branding settings tab |
| `platform.blade.php` | 0 | 1 | **Empty** | Can serve as Platform Mode toggle tab |
| `commissions.blade.php` | 0 | 1 | **Empty** | Can serve as Global Commission & Escrow tab |
| `auction.blade.php` | 0 | 1 | **Empty** | Auction lifecycle hyperparameters |
| `payments.blade.php` | 0 | 1 | **Empty** | Payment gateway settings |
| `security.blade.php` | 0 | 1 | **Empty** | Security & 2FA policies |

### 2.2 Design System Conformity
- **Layout**: `resources/views/layouts/admin.blade.php`
- **Typography**:
  - Headings: Plus Jakarta Sans (`font-headline-lg font-bold text-[#0F172A]`)
  - Body: Inter (`font-body-md text-xs sm:text-sm text-slate-500`)
  - Currency / IDs: JetBrains Mono (`font-mono`)
- **Tokens & Colors**:
  - Primary Accent: Amber `#F5A623` (hover: `#fbbf24`, text: `text-slate-950 font-bold`)
  - Surface Background: Canvas Ivory / Slate (`bg-white rounded-xl p-6 border border-slate-200/90 shadow-2xs`)
  - Inputs: `bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-[#0F172A] focus:border-amber-500 font-mono`
- **Sidebar Integration**:
  In `resources/views/layouts/admin.blade.php` (line 238), the sidebar has "AI Engine Ops" with icon `psychology`. A "Platform Settings" navigation item (`settings` icon) pointing to `route('admin.settings.index')` should be added to the navigation block.

---

## 3. Database Schema & Model Survey (Task 3)

### 3.1 Migration Schema
Migration `2026_09_11_000027_create_site_settings_table.php` defines:
```php
Schema::create('site_settings', function (Blueprint $table) {
    $table->id();
    $table->string('setting_key', 100)->unique();
    $table->longText('setting_value')->nullable();
    $table->timestamp('created_at')->nullable()->useCurrent();
    $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
});
```

### 3.2 Eloquent Model: `App\Models\SiteSetting`
Located at `app/Models/SiteSetting.php`:
- `get(string $key, mixed $default = null): mixed`:
  Retrieves setting, performs JSON decoding if payload is valid JSON/boolean/array.
- `set(string $key, mixed $value): static`:
  Encodes boolean/array to JSON, uses `updateOrCreate(['setting_key' => $key], ['setting_value' => $encoded])`.
- `allMap(): array`:
  Returns associative array `pluck('setting_value', 'setting_key')->toArray()`.

### 3.3 Default Settings Seeded
In `database/seeders/AdminOperationsDataSeeder.php` (lines 453-470):
- `platform_commission_base`: `8.5`
- `escrow_cooling_period_days`: `7`
- `tds_rate`: `1.0`
- `tcs_rate`: `1.0`
- `system_maintenance_mode`: `false`
- `gemini_model`: `'gemini-1.5-flash'`
- `temperature`: `'0.7'`
- `auto_triage_enabled`: `true`
- `dispute_confidence_threshold`: `85`
- `recommendation_engine_enabled`: `true`

### 3.4 Settings Required for R1
- `site_name`: Platform title, default `'Bazaario'`.
- `site_logo`: Path to uploaded custom logo (e.g. `'branding/logo.png'`), default `null` (falls back to bundled asset).
- `site_tagline`: Platform tagline, default `'Smarter Shopping. Local Sellers. Live Auctions.'`.
- `platform_commission_base`: Global take-rate %, default `8.5`.
- `platform_mode`: `'multi_vendor'` (default) or `'single_vendor'`.

---

## 4. Platform Branding Render Locations (Task 4)

| Location | File | Current Code / Element | Recommended Dynamic Binding |
|----------|------|------------------------|-----------------------------|
| **Admin Sidebar Logo** | `layouts/admin.blade.php:108` | `<img src="{{ asset('images/Neon Bazaario Eco-Commerce Logo.png') }}" alt="Bazaario Admin" class="h-12 w-auto object-contain">` | `SiteSetting::getLogoUrl()` or fallback to asset |
| **Admin Sidebar Tagline** | `layouts/admin.blade.php:114` | `<p class="mt-2 font-body-sm text-[11px] text-[#94A3B8] leading-tight">Smarter Shopping. Local Sellers. Live Auctions.</p>` | `SiteSetting::get('site_tagline', '...')` |
| **Customer Nav Logo** | `components/nav.blade.php:58-60` | Light: `asset('images/bazaario-logo.png')`<br>Dark: `asset('images/Neon Bazaario Eco-Commerce Logo.png')` | Dynamic logo via `SiteSetting::getLogoUrl()` |
| **Customer Nav (Logged In)** | `components/nav-user.blade.php:80-82` | Light & dark hardcoded PNG assets | Dynamic logo via `SiteSetting::getLogoUrl()` |
| **Global Footer Logo & Bio** | `components/footer.blade.php:8-12` | Light & dark PNG assets; hardcoded marketplace description | Dynamic logo & dynamic tagline/description |
| **App Title** | `layouts/app.blade.php:14` | `@yield('title', 'Bazaario — Shop Smart, Live Better')` | Use dynamic `SiteSetting::get('site_name', 'Bazaario')` |
| **Seller Portal Header** | `layouts/seller.blade.php:85` | `asset('images/bazaario-logo.png')` | Dynamic logo |
| **Seller Onboarding Header** | `layouts/seller-onboarding.blade.php:81` | `asset('images/bazaario-logo.png')` | Dynamic logo |

### Recommended Helper Architecture in `SiteSetting`:
```php
public static function getSiteName(): string
{
    return static::get('site_name', 'Bazaario');
}

public static function getTagline(): string
{
    return static::get('site_tagline', 'Smarter Shopping. Local Sellers. Live Auctions.');
}

public static function getLogoUrl(): string
{
    $logo = static::get('site_logo');
    if ($logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($logo)) {
        return \Illuminate\Support\Facades\Storage::url($logo);
    }
    return asset('images/bazaario-logo.png');
}

public static function isSingleVendor(): bool
{
    return static::get('platform_mode', 'multi_vendor') === 'single_vendor';
}

public static function isMultiVendor(): bool
{
    return !static::isSingleVendor();
}
```

---

## 5. Multi-Vendor vs Single-Vendor Behavior Matrix (Task 5)

The acceptance criteria specify:
> *"Toggling between Multi-Vendor and Single-Vendor mode updates platform behavior (disabling or hiding multi-vendor registration/stores when in Single-Vendor mode)."*

### Detailed Touchpoints & Impact:

```
┌────────────────────────────────────────────────────────────────────────┐
│                   PLATFORM MODE SWITCH ARCHITECTURE                    │
├────────────────────────────────────────────────────────────────────────┤
│                                                                        │
│   site_settings['platform_mode'] = 'multi_vendor'                      │
│   ├─ Registration: Seller account type card enabled                   │
│   ├─ Seller Onboarding: /seller/become-a-seller & wizard active        │
│   ├─ Navigation: "Sell on Bazaario →" visible in nav & footer          │
│   ├─ Homepage: "Featured Sellers" & "Nearby Stalls" rendered           │
│   └─ Products: Merchant badge, Trust Score, "Visit Stall" visible      │
│                                                                        │
│   site_settings['platform_mode'] = 'single_vendor'                     │
│   ├─ Registration: Seller card HIDDEN; sendOtp rejects 'seller' role  │
│   ├─ Seller Onboarding: /seller/become-a-seller redirects / notice     │
│   ├─ Middleware: SellerMiddleware blocks new onboarding / redirects    │
│   ├─ Navigation: "Sell on Bazaario →" HIDDEN in nav & footer           │
│   ├─ Homepage: "Featured Sellers" & "Nearby Stalls" SUPPRESSED         │
│   └─ Products: Merchant store badge replaced by official store         │
└────────────────────────────────────────────────────────────────────────┘
```

#### 1. Registration (`/register`):
- **UI File**: `resources/views/auth/register.blade.php` (lines 472–490).
  - Contains "Seller Selection Card" radio button (`name="role" value="seller"`).
  - In Single-Vendor mode: Wrap this card in `@if(SiteSetting::isMultiVendor()) ... @endif` so only "Customer" is selectable.
- **Backend Controller**: `app/Http/Controllers/AuthController.php` (lines 177, 474).
  - In `sendOtp(Request $request)`: If `SiteSetting::isSingleVendor()` and `$request->role === 'seller'`, reject with 422 JSON validation error: `"Seller registration is disabled in single-vendor mode."`

#### 2. Seller Public Documentation & Onboarding:
- **Routes & Controller**: `ProductController::becomeASeller` and `feesAndCommission` (`routes/web.php:201-202`).
  - In Single-Vendor mode: Redirect to `home` with warning alert: `"Multi-vendor seller onboarding is currently disabled."`
- **Wizard Route**: `/seller/onboarding` (`SellerOnboardingController::showWizard`).
  - In Single-Vendor mode: Block with redirect to `home`.
- **Middleware Guard**: `app/Http/Middleware/SellerMiddleware.php`.
  - In Single-Vendor mode: Guard intercepts non-admin seller onboarding requests.

#### 3. Navigation Bar & Footer Links:
- `resources/views/components/nav.blade.php` (line 423) and `components/nav-user.blade.php` (line 571):
  - "Sell on Bazaario →": Conditionally show only `@if(SiteSetting::isMultiVendor())`.
- `resources/views/components/footer.blade.php` (lines 42–48):
  - "Sell" column (Become a Seller, Fees & Commission): Conditionally show only `@if(SiteSetting::isMultiVendor())`.

#### 4. Homepage Stalls & Nearby Discovery (`resources/views/index.blade.php`):
- **Section 5.5**: "FEATURED SELLERS SHELF" (lines 504–802)
  - Shows verified stalls, artisan cards, and "Visit Stall" CTAs.
  - Wrap entire `<section>` in `@if(SiteSetting::isMultiVendor()) ... @endif`.
- **Section 5.6**: "NEARBY STALLS & HYPERLOCAL DISCOVERY" (lines 804–940)
  - Hyperlocal radius spatial proximity discovery.
  - Wrap entire `<section>` in `@if(SiteSetting::isMultiVendor()) ... @endif`.
- **Controller Optimization**: In `ProductController::home` (lines 48–95), if `SiteSetting::isSingleVendor()`, pass empty collections for `$featuredSellers` and `$nearbyStalls` to avoid redundant queries.

#### 5. Product Catalog & Details (`resources/views/user/products/`):
- `index.blade.php` (lines 311–314, 368–371):
  - Seller name and verified badge on grid and list cards.
  - In Single-Vendor mode: Render `SiteSetting::getSiteName()` instead of individual merchant shop names.
- `show.blade.php` (lines 269–274, 399–424):
  - "Seller: Artisan" badge and "Sold by Merchant" card with `[ View Stall Catalog → ]`.
  - In Single-Vendor mode: Hide individual merchant card or display direct official brand guarantee.

---

## 6. Testing Strategy & Gap Analysis (Task 6)

### 6.1 Current Test Coverage
- `tests/Feature/AdminHardeningTest.php`: Passes (35 tests, 185 assertions). Validates admin authentication, empty state rendering for 16 views, transaction safety for payouts/disputes/sellers/auctions, and CSRF token presence on mutation screens including `admin.settings.ai`.
- `tests/Feature/AdminChallengerVerificationTest.php`: Passes (12 tests, 664 assertions). Validates access controls on 38 admin routes and CSRF enforcement.

### 6.2 Identified Testing Gaps for R1
1. No test verifies `GET /admin/settings` loads with status 200 and renders branding/mode/commission forms.
2. No test verifies `POST /admin/settings` updates `site_name`, `site_tagline`, and `platform_commission_base`.
3. No test verifies file upload of `site_logo` (using `UploadedFile::fake()->image('logo.png')`) persists to disk and updates `site_settings`.
4. No test verifies toggling `platform_mode` between `multi_vendor` and `single_vendor`.
5. No test verifies Single-Vendor enforcement:
   - `/register` hides seller role option.
   - `sendOtp` rejects `role => 'seller'` in single-vendor mode.
   - `/seller/become-a-seller` redirects or displays disabled notice.
   - Homepage does not render Featured Sellers or Nearby Stalls sections.

### 6.3 Proposed Dedicated Test File
Create `tests/Feature/AdminPlatformSettingsTest.php` covering:
- `test_unauthenticated_user_cannot_access_admin_settings()`
- `test_admin_can_view_settings_portal_with_branding_and_mode_controls()`
- `test_admin_can_update_site_name_tagline_and_commission_rate()`
- `test_admin_can_upload_custom_site_logo()`
- `test_admin_can_toggle_platform_mode_to_single_vendor()`
- `test_single_vendor_mode_blocks_seller_registration()`
- `test_single_vendor_mode_hides_seller_stalls_from_homepage()`
- `test_multi_vendor_mode_restores_seller_features()`

---

## 7. Implementation Roadmap for R1

1. **Model Helpers (`app/Models/SiteSetting.php`)**:
   Add convenience helper accessors: `isSingleVendor()`, `isMultiVendor()`, `getSiteName()`, `getTagline()`, `getLogoUrl()`, `getCommissionRate()`.
2. **Routes (`routes/web.php`)**:
   Add `Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings.index');` and `Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');` inside the admin route group.
3. **Controller (`app/Http/Controllers/Admin/AdminDashboardController.php`)**:
   Implement `settings()` and `updateSettings(Request $request)` with validation, transaction wrapping, image upload handling, and flash toasts.
4. **Views (`resources/views/admin/settings/`)**:
   - Build comprehensive `index.blade.php` adhering to the Warm Modernist design system with tabs for Branding, Platform Mode, Commissions, and AI config.
   - Populate `general.blade.php`, `platform.blade.php`, and `commissions.blade.php` to prevent empty view errors.
   - Update `resources/views/layouts/admin.blade.php` sidebar to include the Platform Settings link.
5. **Multi-Vendor vs Single-Vendor Behavior Enforcement**:
   - `resources/views/auth/register.blade.php` & `AuthController::sendOtp`: Conditionally show / validate seller role.
   - `ProductController::becomeASeller` & `feesAndCommission`: Guard against single-vendor mode.
   - `resources/views/index.blade.php`: Conditionally suppress Sections 5.5 and 5.6 in single-vendor mode.
   - `resources/views/components/nav.blade.php` & `components/footer.blade.php`: Conditionally hide seller onboarding CTAs.
   - `resources/views/user/products/index.blade.php` & `show.blade.php`: Adapt seller badges.
6. **Automated Verification**:
   - Write and execute `tests/Feature/AdminPlatformSettingsTest.php`.
   - Run full regression: `php artisan test`.
