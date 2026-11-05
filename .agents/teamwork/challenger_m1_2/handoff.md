# Handoff Report: Empirical Challenge 2 (File Uploads & Single-Vendor Boundary Security)

**Agent**: `challenger_m1_2`  
**Milestone**: M1 (Admin Platform Configuration & Branding Settings)  
**Parent Agent**: `76853128-08ef-4616-b702-9b46294663b7`  
**Verdict**: **REQUEST_CHANGES**  
**Date**: 2026-10-12  

---

## 1. Observation

1. **Test Suite Execution**:
   - Test suite written: `tests/Feature/AdminSettingsChallenge2Test.php` (18 empirical challenge test methods).
   - Verbatim execution command:
     ```bash
     php artisan test tests/Feature/AdminSettingsChallenge2Test.php
     ```
   - Verbatim execution output:
     ```
        FAIL  Tests\Feature\AdminSettingsChallenge2Test
       ✓ php script upload disguised as logo is rejected
       ✓ plain text file renamed as png is rejected
       ✓ oversized image exceeding 2mb is rejected
       ✓ html javascript file disguised as image is rejected
       ✓ executable file is rejected
       ✓ genuine png upload is accepted stored and resolves url
       ✓ genuine jpg upload is accepted stored and resolves url
       ✓ genuine webp upload is accepted stored and resolves url
       ⨯ genuine svg upload is accepted stored and resolves url
       ✓ consecutive logo uploads update setting and url
       ✓ logo url fallback when logo is missing
       ✓ direct api send otp for seller role blocked in single vendor mode
       ✓ direct api send otp for user role allowed in single vendor mode
       ✓ verify otp without session is rejected
       ✓ verify otp with invalid otp is rejected
       ✓ role elevation tampering at verify otp is ineffective
       ✓ non seller cannot access seller onboarding in single vendor mode
       ⨯ seller registration completion attempt in single vendor mode
     
       Tests:    2 failed, 16 passed (65 assertions)
       Duration: 1.21s
     ```

2. **Verbatim Error 1 (SVG Upload Rejection)**:
   - Target File: `app/Http/Controllers/Admin/AdminDashboardController.php`, line 1218:
     ```php
     $request->validate([
         'site_name'        => 'nullable|string|max:100',
         'site_tagline'     => 'nullable|string|max:255',
         'commission_rate'  => 'nullable|numeric|min:0|max:100',
         'platform_mode'    => 'nullable|string|in:multi_vendor,single_vendor',
         'site_logo'        => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
     ]);
     ```
   - Verbatim Test Failure:
     ```
     FAILED Tests\Feature\AdminSettingsChallenge2Test > genuine svg upload is accepted stored and resolves url
     SVG upload was rejected by validation rule: ["The site logo field must be an image."]
     at tests\Feature\AdminSettingsChallenge2Test.php:271
     ```

3. **Verbatim Error 2 (Single-Vendor Registration Finalization Bypass)**:
   - Target File: `app/Http/Controllers/AuthController.php`, lines 345–500 (`verifyOtpAndRegister`):
     ```php
     public function verifyOtpAndRegister(Request $request)
     {
         $request->validate([
             'otp' => ['required', 'digits:6'],
         ]);
         $pending = $request->session()->get('pending_registration');
         ...
         $user = DB::transaction(function () use ($pending, $profileImagePath) {
             $user = User::create([
                 'name'               => $pending['name'],
                 'email'              => $pending['email'],
                 'phone'              => $pending['phone'],
                 'password'           => $pending['password'],
                 'role'               => $pending['role'],
                 ...
             ]);
             if ($user->role === 'seller') {
                 SellerProfile::create([
                     'user_id'   => $user->id,
                     'shop_name' => $user->name . "'s Shop",
                     'shop_slug' => Str::slug($user->name . '-' . $user->id),
                     'status'    => 'pending',
                 ]);
             }
             return $user;
         });
     ```
   - Verbatim Test Failure:
     ```
     FAILED Tests\Feature\AdminSettingsChallenge2Test > seller registration completion attempt in single vendor mode
     VULNERABILITY: Complete registration bypass succeeded! A seller account (user ID 3) was created while single_vendor mode was active because AuthController::verifyOtpAndRegister fails to check SiteSetting::isSingleVendor().
     at tests\Feature\AdminSettingsChallenge2Test.php:561
     ```

---

## 2. Logic Chain

1. **SVG Validation Mechanism**: In Laravel's core validator (`Illuminate\Validation\Concerns\ValidatesAttributes::validateImage`), the `image` rule evaluates allowed MIME extensions against a hardcoded list: `['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']` unless `:allow_svg` parameter is explicitly given. Because `AdminDashboardController.php:1218` specifies `image|mimes:png,jpg,jpeg,svg,webp|max:2048`, any valid `.svg` file triggers a validation error from `image` before `mimes` can permit it (Observation 2). This prevents platform administrators from uploading standard vector brand assets.
2. **Single-Vendor Guard Asymmetry**: `AuthController.php` checks `SiteSetting::isSingleVendor()` only during Step 1 (`sendOtp` at line 172), but does not check `SiteSetting::isSingleVendor()` during Step 2 (`verifyOtpAndRegister` at line 345) (Observation 3).
3. **Bypass Vector**: When a prospective seller initiates registration before the platform mode is toggled to `single_vendor` (or has an existing pending registration in session), submitting valid OTP verification to `/register/verify-otp` bypasses the single-vendor boundary completely. The system executes `User::create(['role' => 'seller'])` and `SellerProfile::create(...)`, directly violating the single-vendor platform invariant (Observation 3).
4. **Empirical Defect Confirmation**: Because both defects are proven through reproducible test failures (Observation 1), the current implementation cannot be approved without remediation.

---

## 3. Caveats

1. **Review-Only Constraint**: As an EMPIRICAL CHALLENGER under strict review-only constraints, no worker implementation files were altered. The fixes must be applied by Worker M1.
2. **Environment GD Extension**: The local CLI environment lacks PHP ext-gd (`imagecreatetruecolor`), which does not affect web server production behavior but required test binary generation to use real base64 byte streams (`createWithContent`) rather than synthetic `FileFactory::image()` fixtures.
3. **Multi-Vendor Baseline**: When `platform_mode` is `'multi_vendor'`, both user and seller registrations function as intended.

---

## 4. Conclusion

**Verdict: REQUEST_CHANGES**

Worker M1 must apply two targeted fixes:

1. **Fix SVG Validation**:
   In `app/Http/Controllers/Admin/AdminDashboardController.php` line 1218, change:
   ```php
   'site_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
   ```
   to:
   ```php
   'site_logo' => 'nullable|image:allow_svg|mimes:png,jpg,jpeg,svg,webp|max:2048',
   ```
   *(or `'nullable|mimes:png,jpg,jpeg,svg,webp|max:2048'`)*.

2. **Close Single-Vendor Registration Bypass**:
   In `app/Http/Controllers/AuthController.php` inside `verifyOtpAndRegister()` (around line 366), add:
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

Once these two fixes are made, all 18 challenge tests in `tests/Feature/AdminSettingsChallenge2Test.php` will pass 100%.

---

## 5. Verification Method

To independently reproduce the findings, run:

```bash
php artisan test tests/Feature/AdminSettingsChallenge2Test.php
```

Expected output prior to worker fixes:
- 16 passed, 2 failed:
  - `genuine svg upload is accepted stored and resolves url` (Failed: "The site logo field must be an image.")
  - `seller registration completion attempt in single vendor mode` (Failed: Seller account created while single_vendor active)
