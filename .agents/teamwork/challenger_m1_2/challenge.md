# Adversarial Challenge Report: File Uploads & Single-Vendor Boundary Security

**Challenger**: Challenger 2 (`challenger_m1_2`)  
**Target Milestone**: Milestone 1 (R1: Admin Platform Configuration & Branding Settings)  
**Date**: 2026-10-12  
**Test Suite**: `tests/Feature/AdminSettingsChallenge2Test.php`  
**Verdict**: **REQUEST_CHANGES**  

---

## Challenge Summary

**Overall risk assessment**: **HIGH**

While the core admin authentication guards, standard PNG/JPG image uploads, oversized file rejection, and initial OTP initiation gating for sellers are functioning properly, empirical stress-testing identified two critical flaws:

1. **Branding SVG Upload Rejection (Functional Bug)**: Administrators attempting to upload valid SVG vector logos are blocked with validation errors (`"The site logo field must be an image."`) because Laravel's default `image` validation rule excludes SVG unless `allow_svg` is explicitly enabled.
2. **Single-Vendor Registration Bypass Vulnerability (Security Boundary Flaw)**: An attacker can bypass single-vendor restrictions and complete registration as a seller because `AuthController::verifyOtpAndRegister()` fails to check `SiteSetting::isSingleVendor()`. If a session was initiated prior to a mode toggle or manipulated, the user and seller profile are created in the database despite single-vendor mode being active.

---

## Challenges

### [High] Challenge 1: Legitimate SVG Brand Logos Are Blocked by Strict Image Validator

- **Assumption challenged**: The worker assumed `'site_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048'` in `AdminDashboardController.php` would allow all listed file types including SVG.
- **Attack / Failure scenario**: An administrator uploads a genuine vector logo in SVG format (e.g. `<svg xmlns="http://www.w3.org/2000/svg"...`).
- **Blast radius**: The admin settings form rejects the upload with `"The site logo field must be an image."`. Administrators cannot upload SVG brand assets despite SVG being explicitly stated in the requirement and listed in the controller's `mimes` rule.
- **Root Cause**: In Laravel 11 (`Illuminate\Validation\Concerns\ValidatesAttributes::validateImage`), the `image` rule defaults to `['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']`. If `svg` is present in `mimes`, the preceding `image` rule triggers a validation failure before `mimes` is evaluated.
- **Mitigation**: Update validation in `app/Http/Controllers/Admin/AdminDashboardController.php` line 1218 from:
  ```php
  'site_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
  ```
  to:
  ```php
  'site_logo' => 'nullable|image:allow_svg|mimes:png,jpg,jpeg,svg,webp|max:2048',
  ```
  or:
  ```php
  'site_logo' => 'nullable|mimes:png,jpg,jpeg,svg,webp|max:2048',
  ```

---

### [High] Challenge 2: Single-Vendor Mode Bypass in Registration Finalization (`verifyOtpAndRegister`)

- **Assumption challenged**: The worker assumed gating `AuthController::sendOtp()` with `if (SiteSetting::isSingleVendor() && $request->input('role') === 'seller')` was sufficient to enforce single-vendor boundary security.
- **Attack scenario**:
  1. An attacker initiates registration with `role = 'seller'` while platform mode is `multi_vendor` (or before single-vendor mode is enabled), obtaining a valid `pending_registration` session and OTP.
  2. The platform switches to `single_vendor` mode.
  3. The attacker posts their OTP to `/register/verify-otp`.
  4. Alternatively, any session desynchronization or stale registration session attempts verification after the mode switch.
- **Blast radius**: `AuthController::verifyOtpAndRegister()` reads `$pending['role']`, creates a new record in `users` with `role = 'seller'`, and creates a new record in `seller_profiles`, directly breaching the single-vendor boundary and generating unauthorized merchants during single-vendor mode.
- **Root Cause**: `AuthController::verifyOtpAndRegister()` (lines 345–520) completely lacks a guard check for `SiteSetting::isSingleVendor()`.
- **Mitigation**: Add a guard check in `app/Http/Controllers/AuthController.php` inside `verifyOtpAndRegister()` immediately after retrieving `$pending`:
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

---

## Stress Test Results

Executed via `php artisan test tests/Feature/AdminSettingsChallenge2Test.php`:

| # | Scenario | Expected Behavior | Actual Behavior | Result |
|---|----------|-------------------|-----------------|--------|
| 1 | PHP script (`backdoor.php`) uploaded as logo | Validation fails on `site_logo`, no file stored | Validation failed with session errors | **PASS** |
| 2 | Plain text file renamed as `fake_logo.png` | Validation fails on `site_logo` | Validation failed with session errors | **PASS** |
| 3 | Oversized image (2049 KB > 2MB limit) | Validation fails on `site_logo` | Validation failed with session errors | **PASS** |
| 4 | HTML/JS file (`xss_payload.html`) uploaded as logo | Validation fails on `site_logo` | Validation failed with session errors | **PASS** |
| 5 | Binary executable (`malware.exe`) uploaded as logo | Validation fails on `site_logo` | Validation failed with session errors | **PASS** |
| 6 | Genuine PNG binary upload (base64 1x1) | Stored to `branding/`, URL resolves to valid path | Stored to disk, `SiteSetting::getLogoUrl()` resolves to `/storage/branding/...` | **PASS** |
| 7 | Genuine JPG binary upload (base64 1x1) | Stored to disk, URL resolves to valid path | Stored to disk, URL resolves | **PASS** |
| 8 | Genuine WebP binary upload (base64 1x1) | Stored to disk, URL resolves to valid path | Stored to disk, URL resolves | **PASS** |
| 9 | Genuine SVG vector upload (`logo.svg`) | Stored to disk, URL resolves to valid path | **Rejected with error: "The site logo field must be an image."** | **FAIL** |
| 10 | Consecutive logo updates | Second upload updates DB path and resolves new URL | Setting updated to second file | **PASS** |
| 11 | Fallback logo URL when missing/empty/deleted | Resolves to default `images/bazaario-logo.png` | Resolves safely to default | **PASS** |
| 12 | Direct API `/register/send-otp` with `role='seller'` in single-vendor mode | 422 rejected, no OTP in DB, no mail sent | 422 rejected with specific single-vendor error | **PASS** |
| 13 | Direct API `/register/send-otp` with `role='user'` in single-vendor mode | 200 accepted, OTP stored in DB | 200 accepted, OTP generated | **PASS** |
| 14 | Direct API `/register/verify-otp` with no session | 422 rejected ("Session expired") | 422 rejected | **PASS** |
| 15 | Direct API `/register/verify-otp` with forged/invalid OTP | 422 rejected ("Invalid OTP"), no user created | 422 rejected | **PASS** |
| 16 | Parameter tampering (`role='seller'` in verify OTP request body) | Role elevation ignored, created as `'user'` | Created with role `'user'`, no seller profile | **PASS** |
| 17 | Guest and non-seller access to `/seller/onboarding` | Blocked / redirected | Redirected (302) | **PASS** |
| 18 | Registration completion with stale `role='seller'` session in single-vendor mode | Rejected with 422, seller creation prevented | **User created with `role='seller'` and `SellerProfile` generated** | **FAIL** |

**Summary**: 18 tests executed; 16 passed, 2 failed.

---

## Unchallenged Areas

- **Storage Symlink on OS/Web Server**: The tests verify files written to `storage/app/public/branding/` and URL string generation via `Storage::disk('public')->url()`. Web server symlink creation (`php artisan storage:link`) is handled at the infrastructure deployment level.
- **Hyperlocal Geolocation Calculation**: Excluded from Milestone 1 scope per Project specification.
