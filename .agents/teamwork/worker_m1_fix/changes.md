# Milestone 1 Fix Changes (Challenger 2 Defect Remediation)

**Date**: 2026-10-12  
**Agent**: Worker M1 Fix (`worker_m1_fix`)  
**Target Files**:
1. `app/Http/Controllers/Admin/AdminDashboardController.php`
2. `app/Http/Controllers/AuthController.php`

---

## 1. Fix SVG Brand Logo Validation
- **File**: `app/Http/Controllers/Admin/AdminDashboardController.php` (line 1218)
- **Problem**: Laravel's default `image` validation rule rejects SVG uploads even when `svg` is listed in `mimes:png,jpg,jpeg,svg,webp` because `image` defaults to excluding SVG unless `allow_svg` is specified.
- **Change**: Updated the rule from:
  ```php
  'site_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
  ```
  to:
  ```php
  'site_logo' => 'nullable|image:allow_svg|mimes:png,jpg,jpeg,svg,webp|max:2048',
  ```
- **Result**: Valid SVG vector logos are accepted and saved to public storage (`branding/`), while malicious scripts and non-image files remain strictly blocked.

---

## 2. Close Single-Vendor Registration Finalization Bypass
- **File**: `app/Http/Controllers/AuthController.php` (lines 367-376)
- **Problem**: In `verifyOtpAndRegister(Request $request)`, the controller only checked if `$pending` existed in session and if the OTP matched. If an attacker had initiated registration with `role = 'seller'` prior to single-vendor mode being enabled (or manipulated session state), completing OTP verification allowed creating a seller account and seller profile in the database, bypassing the single-vendor boundary.
- **Change**: Added an explicit single-vendor mode boundary check right after checking `$pending`:
  ```php
  if (SiteSetting::isSingleVendor() && ($pending['role'] ?? null) === 'seller') {
      $request->session()->forget('pending_registration');
      return response()->json([
          'success' => false,
          'message' => 'Seller registration is currently disabled in Single-Vendor mode.',
          'errors'  => [
              'role' => ['Seller registration is currently disabled in Single-Vendor mode.'],
          ],
      ], 422);
  }
  ```
- **Result**: In Single-Vendor mode, any attempt to verify OTP and finalize registration as a seller is rejected with HTTP 422, the pending registration session is purged, and no user or seller profile record is created.

---

## 3. Verification Test Results
Executed: `php artisan test tests/Feature/AdminSettingsChallenge2Test.php tests/Feature/AdminPlatformSettingsTest.php tests/Feature/AdminSettingsChallenge1Test.php`

- `AdminSettingsChallenge2Test.php`: **18 passed** (69 assertions) — 100% pass (resolved both failing tests)
- `AdminPlatformSettingsTest.php`: **13 passed** (50 assertions) — 100% pass
- `AdminSettingsChallenge1Test.php`: **14 passed** (102 assertions) — 100% pass
- **Total**: **45 passed** (221 assertions) with 0 regressions.
