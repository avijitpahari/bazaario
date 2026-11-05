## 2026-10-12T05:58:51Z
You are Worker M1 Fix tasked with resolving the 2 specific defects identified by Challenger 2 for Milestone 1: Admin Platform Configuration & Branding Settings (R1).
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m1_fix
Project root: c:\xampp\htdocs\bazaario
Original Request file: c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (timestamp ## 2026-10-12T05:17:47Z).
Project Specification: c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_10\PROJECT.md
Challenger 2 Report: c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m1_2\challenge.md
Challenger 2 Handoff: c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m1_2\handoff.md

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

File Ownership:
You exclusively own and may edit:
- `app/Http/Controllers/Admin/AdminDashboardController.php`
- `app/Http/Controllers/AuthController.php`

Defects to Resolve:
1. Fix SVG Brand Logo Validation in `app/Http/Controllers/Admin/AdminDashboardController.php`:
   - Line 1218 currently uses `'site_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048'`.
   - In Laravel, the `image` rule rejects SVG by default. Change this validation rule to:
     `'site_logo' => 'nullable|mimes:png,jpg,jpeg,svg,webp|max:2048',`
     (or `'nullable|image:allow_svg|mimes:png,jpg,jpeg,svg,webp|max:2048',`).
2. Close Single-Vendor Registration Finalization Bypass in `app/Http/Controllers/AuthController.php`:
   - Inside `verifyOtpAndRegister(Request $request)` (around lines 360-370, after retrieving `$pending`), add:
     ```php
     if (\App\Models\SiteSetting::isSingleVendor() && ($pending['role'] ?? null) === 'seller') {
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

Verification:
Run the test suites to ensure 100% pass:
- `php artisan test tests/Feature/AdminSettingsChallenge2Test.php` (must pass all 18 tests)
- `php artisan test tests/Feature/AdminPlatformSettingsTest.php` (must pass all 13 tests)
- `php artisan test tests/Feature/AdminSettingsChallenge1Test.php` (must pass all 14 tests)

Deliverables:
- Write changes to `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m1_fix\changes.md`.
- Write handoff report to `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m1_fix\handoff.md`.
- Send completion message to parent upon finishing.
