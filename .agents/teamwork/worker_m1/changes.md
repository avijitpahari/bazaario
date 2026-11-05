# Detailed Changes Log — Milestone 1: Admin Platform Configuration & Branding Settings (R1)

**Worker**: `worker_m1`  
**Date**: 2026-10-12  
**Milestone**: M1 (Requirement 1: Admin Platform Configuration & Branding Settings)  
**Status**: Completed  

---

## 1. Model Enhancements: `app/Models/SiteSetting.php`
- Added static helper methods to provide safe, cached, and defaulted access to platform branding, operational modes, and commission take-rates:
  - `getSiteName(string $default = 'Bazaario'): string`: Retrieves persistent `site_name` setting, falling back cleanly to `'Bazaario'`.
  - `getTagline(string $default = 'Smarter Shopping. Local Sellers. Live Auctions.'): string`: Retrieves persistent `site_tagline` setting.
  - `getLogoUrl(): string`: Checks if custom uploaded logo in `site_logo` exists on `Storage::disk('public')` and returns public URL; handles absolute/relative paths; gracefully falls back to `asset('images/bazaario-logo.png')`.
  - `isSingleVendor(): bool`: Returns `true` if `platform_mode === 'single_vendor'`.
  - `isMultiVendor(): bool`: Returns `true` if marketplace is operating in multi-vendor mode (`!isSingleVendor()`).
  - `getCommissionRate(float $default = 8.5): float`: Checks `commission_rate` and `platform_commission_base` with fallback to `8.5%`.

---

## 2. Route Registration: `routes/web.php`
- Registered administrative settings endpoints inside the `admin` prefix and `['auth:admin', 'admin']` middleware group:
  - `GET /admin/settings` -> `AdminDashboardController@settings` (name: `admin.settings.index`).
  - `POST /admin/settings` -> `AdminDashboardController@updateSettings` (name: `admin.settings.update`).

---

## 3. Controller Implementation: `app/Http/Controllers/Admin/AdminDashboardController.php`
- Added `settings(Request $request)`:
  - Fetches all settings as key-value dictionary using `SiteSetting::allMap()`.
  - Renders view `admin.settings.index` passing `compact('settings')`.
- Added `updateSettings(Request $request)`:
  - Validates inputs:
    - `site_name`: `nullable|string|max:100`
    - `site_tagline`: `nullable|string|max:255`
    - `commission_rate`: `nullable|numeric|min:0|max:100`
    - `platform_mode`: `nullable|string|in:multi_vendor,single_vendor`
    - `site_logo`: `nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048`
  - Atomic database transaction wrapping with `DB::transaction`:
    - Handles custom logo file upload to `branding` directory on `public` disk via `Storage::disk('public')`, updating `site_logo`.
    - Trims and sanitizes strings using `strip_tags()`.
    - Synchronizes both `commission_rate` and `platform_commission_base` to maintain legacy backward compatibility.
    - Saves `platform_mode` (`multi_vendor` / `single_vendor`).
  - Returns redirect back with flash toast `'Platform branding and operational settings successfully updated.'`.

---

## 4. Admin Settings Blade Views: `resources/views/admin/settings/`
- `index.blade.php`:
  - Unified modern settings hub adhering to the Warm Modernist design system (Plus Jakarta Sans, Inter, JetBrains Mono, rounded-xl, amber/emerald pills).
  - Telemetry summary cards showing current brand name, platform mode, take-rate, and active Gemini AI model.
  - Alpine.js tabbed navigation (`x-data="{ activeTab: '...' }"`) supporting:
    - Tab 1: General & Branding (`admin.settings.general`)
    - Tab 2: Platform Architecture & Mode (`admin.settings.platform`)
    - Tab 3: Global Commissions & Escrow (`admin.settings.commissions`)
    - Direct link tab to AI Ops Center (`admin.settings.ai`).
- `general.blade.php`:
  - Modular sub-view for branding identity management.
  - Live preview card displaying current active logo and fallback indicator.
  - Form fields for file upload (`site_logo`), site name (`site_name`), and tagline (`site_tagline`).
  - Real-time dark storefront preview banner.
- `platform.blade.php`:
  - Modular sub-view for switching platform architecture between Multi-Vendor Marketplace and Single-Vendor Direct Storefront.
  - Visual radio selection cards with distinct status pills, feature checkmarks, and operational safeguard banner.
- `commissions.blade.php`:
  - Modular sub-view for configuring global base commission take-rates.
  - Real-time settlement simulator demonstrating dynamic fee deductions on a sample ₹1,000 consignment sale (Take-rate, TDS 1%, TCS 1%, Net Seller Escrow Payout).

---

## 5. Admin Layout Dynamic Branding: `resources/views/layouts/admin.blade.php`
- Replaced hardcoded title string with `{{ \App\Models\SiteSetting::getSiteName() }}`.
- Replaced hardcoded logo image src and alt attributes with `{{ \App\Models\SiteSetting::getLogoUrl() }}` and `{{ \App\Models\SiteSetting::getSiteName() }} Admin`.
- Replaced hardcoded sidebar tagline with `{{ \App\Models\SiteSetting::getTagline() }}`.
- Added "Platform Settings" navigation link in sidebar linking to `route('admin.settings.index')` with active route highlight when on `admin.settings.*` (excluding `admin.settings.ai`).

---

## 6. Single-Vendor Mode Behavioral Enforcement:
- **Registration Controller (`app/Http/Controllers/AuthController.php`)**:
  - In `sendOtp`: if `SiteSetting::isSingleVendor()` and `role === 'seller'`, rejects request with 422 JSON validation error: `"Seller registration is currently disabled in Single-Vendor mode."`.
- **Registration Blade View (`resources/views/auth/register.blade.php`)**:
  - Wrapped seller account type radio card (`#card-type-seller`) in `@unless(\App\Models\SiteSetting::isSingleVendor()) ... @endunless`.
  - Adjusted grid class to `grid {{ \App\Models\SiteSetting::isSingleVendor() ? 'grid-cols-1' : 'grid-cols-2' }} gap-3` so Customer card cleanly expands when in Single-Vendor mode.
- **Product Controller (`app/Http/Controllers/ProductController.php`)**:
  - In `home()`: if `SiteSetting::isSingleVendor()`, returns empty collections `collect()` for `$featuredSellers` and `$nearbyStalls`, avoiding unnecessary database seller queries.
  - Pre-initializes `$lat`, `$lng`, and `$radius` to ensure `compact()` always succeeds regardless of mode.
  - In `becomeASeller()`: redirects to `home` with info flash if `SiteSetting::isSingleVendor()`.
  - In `feesAndCommission()`: redirects to `home` with info flash if `SiteSetting::isSingleVendor()`.
- **Homepage Blade View (`resources/views/index.blade.php`)**:
  - Wrapped Section 5.5 (Featured Sellers Shelf) in `@unless(\App\Models\SiteSetting::isSingleVendor()) ... @endunless`.
  - Wrapped Section 5.6 (Nearby Stalls & Hyperlocal Discovery) in `@unless(\App\Models\SiteSetting::isSingleVendor()) ... @endunless`.

---

## 7. Verification Test Suite: `tests/Feature/AdminPlatformSettingsTest.php`
- Comprehensive feature tests covering 13 test methods (50 assertions, 100% pass):
  1. `test_unauthenticated_user_cannot_access_admin_settings`: Verifies 302 redirect to admin login for both GET and POST endpoints.
  2. `test_non_admin_users_cannot_access_admin_settings`: Verifies regular users and sellers are rejected with 403 / 302.
  3. `test_admin_can_view_settings_index_page`: Verifies 200 OK and presence of branding/platform/commission sections.
  4. `test_admin_can_update_site_name_and_tagline`: Verifies database persistence and helper accessors for custom branding text.
  5. `test_admin_can_update_commission_rate`: Verifies database persistence of base take-rate percentage.
  6. `test_admin_can_upload_custom_logo`: Verifies fake storage upload, file persistence to `branding` directory, and URL resolution without GD dependency.
  7. `test_admin_can_toggle_platform_mode_to_single_vendor`: Verifies toggling `platform_mode` to `single_vendor`.
  8. `test_single_vendor_mode_disables_seller_registration`: Verifies 422 rejection when requesting seller registration.
  9. `test_single_vendor_mode_hides_seller_role_in_register_view`: Verifies `#card-type-seller` is suppressed from HTML.
  10. `test_single_vendor_mode_hides_seller_shelves_on_homepage`: Verifies Section 5.5 and Section 5.6 are suppressed from homepage HTML.
  11. `test_single_vendor_mode_redirects_seller_documentation_pages`: Verifies `/seller/become-a-seller` and `/seller/fees-and-commission` redirect to homepage.
  12. `test_multi_vendor_mode_restores_seller_features`: Verifies seller cards, shelves, and docs are available in `multi_vendor` mode.
  13. `test_site_setting_helpers_provide_safe_defaults`: Verifies safe fallbacks when database settings are unseeded or cleared.
