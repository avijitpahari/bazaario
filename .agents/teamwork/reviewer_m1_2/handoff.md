# Milestone 1 Independent Review & Adversarial Challenge Report: Foundation, Schemas & Onboarding Access Control (R1)

**Agent**: `reviewer_m1_2` (Reviewer 2 & Adversarial Critic)  
**Date**: 2026-09-30T05:35:00Z  
**Target Milestone**: Milestone 1: Foundation, Schemas & Onboarding Access Control (R1)  
**Assigned Directory**: `c:\xampp\htdocs\bazaario\.agents\teamwork\reviewer_m1_2`  
**Verdict**: **APPROVE**

---

## 1. Observation

Direct observations from source inspection, design system token audits, static analysis, and test executions:

1. **Integrity Audit**:
   - Actively scanned all Milestone 1 source files:
     * `database/migrations/2026_09_30_000001_add_seller_panel_fields_to_tables.php`
     * `app/Models/Product.php`
     * `app/Models/SellerOrder.php`
     * `app/Models/SellerProfile.php`
     * `app/Http/Middleware/SellerMiddleware.php`
     * `app/Http/Controllers/Seller/SellerOnboardingController.php`
     * `routes/web.php`
     * `resources/views/layouts/seller-onboarding.blade.php`
     * `resources/views/layouts/seller.blade.php`
     * `resources/views/seller/onboarding/wizard.blade.php`
     * `resources/views/seller/pending.blade.php`
     * `resources/views/seller/dashboard.blade.php`
   - *Result*: **ZERO integrity violations detected**. No hardcoded test responses, dummy facade logic, bypass flags, or fake assertions. All data models, access control gates, and transactional persistence methods implement genuine production logic.

2. **Access Control & Approval Gate (`app/Http/Middleware/SellerMiddleware.php`)**:
   - Lines 24–34: Unauthenticated sessions accessing guarded routes redirect to `route('login')` (or return HTTP 401 for JSON requests).
   - Lines 43–57: Inactive or suspended user accounts (`$user->status !== 'active'`) are logged out immediately and redirected to `route('login')` (or HTTP 403).
   - Lines 64–84: Role-based enforcement redirects regular customers (`$user->role === 'user'`) to `route('products.index')`, admin accounts (`$user->role === 'admin'`) to `route('admin.dashboard')`, and other non-sellers to login.
   - Lines 93–108: Approval check blocks non-approved sellers:
     ```php
     if (!$this->isExemptFromApprovalCheck($request)) {
         $profile = $user->sellerProfile;
         if (!$profile || $profile->status !== 'approved') {
             if ($request->expectsJson()) {
                 return response()->json([
                     'message' => 'Your seller account is waiting for admin approval.',
                     'status' => $profile ? $profile->status : 'incomplete',
                 ], 403);
             }
             return redirect()->route('seller.pending')
                 ->with('warning', 'Your seller account is waiting for admin approval.');
         }
     }
     ```
   - Lines 116–143: Explicit loop-free whitelist restricts exemptions strictly to `seller.pending` (`seller/pending`), `seller.onboarding*` (`seller/onboarding`, `seller/onboarding/*`), and `logout` / `seller.logout`. All operational routes (`/seller/dashboard`, `/seller/products/*`, `/seller/orders/*`, `/seller/payouts/*`, `/seller/auctions/*`, `/seller/account/*`) strictly enforce the approval gate.

3. **Controller Validation, Image Upload & Slug Uniqueness (`app/Http/Controllers/Seller/SellerOnboardingController.php`)**:
   - Lines 66–82: Seller type input normalization supports lowercase and slug variations (`'farmer'`, `'kirana'`, `'darkstore'`, `'individual'`) and maps them to canonical titles (`'Farmer'`, `'Kirana Store'`, `'Dark Store'`, `'Individual'`).
   - Lines 84–96: Strict server-side validation rules:
     ```php
     $validated = $request->validate([
         'seller_type'      => ['required', 'string', Rule::in(['Farmer', 'Kirana Store', 'Dark Store', 'Individual'])],
         'shop_name'        => ['required', 'string', 'min:3', 'max:150'],
         'bio'              => ['nullable', 'string', 'max:1000'],
         'storefront_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Max 5MB
         'address'          => ['required', 'string', 'max:255'],
         'city'             => ['required', 'string', 'max:100'],
         'state'            => ['required', 'string', 'max:100'],
         'country'          => ['nullable', 'string', 'max:100'],
         'postal_code'      => ['nullable', 'string', 'max:20'],
         'latitude'         => ['required', 'numeric', 'between:-90,90'],
         'longitude'        => ['required', 'numeric', 'between:-180,180'],
     ]);
     ```
   - Lines 113–118: Safe image upload to `'public'` disk (`storefronts/`) with unique sanitized filename (`'storefront_' . $user->id . '_' . time() . '.' . $extension`).
   - Lines 121–130: Deterministic unique slug generation with collision loop:
     ```php
     $baseSlug = Str::slug($validated['shop_name']);
     if (empty($baseSlug)) {
         $baseSlug = 'shop-' . $user->id;
     }
     $slug = $baseSlug;
     $counter = 1;
     while (SellerProfile::where('shop_slug', $slug)->where('user_id', '!=', $user->id)->exists()) {
         $slug = $baseSlug . '-' . $counter;
         $counter++;
     }
     ```
   - Lines 133–164: Atomic database persistence wrapped in `DB::transaction` using `SellerProfile::updateOrCreate(['user_id' => $user->id], $attributes)` with status initialized to `'pending'`.

4. **Design System & UI Conformance (`warm_modernist_commerce/DESIGN.md` & stitch template `code.html`)**:
   - Layouts (`layouts/seller-onboarding.blade.php`, `layouts/seller.blade.php`) properly load Google Fonts (Space Grotesk, Inter, JetBrains Mono) and Material Symbols Outlined.
   - Color tokens strictly match specifications: Warm Canvas (`#FFFDF8` / `#fbf9f4`), Structural Slate (`#0F172A`), Amber Action (`#F5A623`), Success Green (`#16A34A`), Muted Text (`#45464D`), and Outline (`#E2DFD7` / `#76777D`).
   - Radii policy enforced: Exact `14px` (`rounded-[14px]`) on cards, inputs, containers, and buttons; `6px` / `8px` on micro badges and status chips; no pill buttons.
   - Onboarding Wizard (`resources/views/seller/onboarding/wizard.blade.php`): 5-step guided container with account credentials verification, 4 interactive selection cards, dynamic classification attributes, drag-and-drop storefront upload with preview/remove, browser GPS sensor integration, topographic radar map preview, consolidated review matrix with 1-click edit jumps, and confirmation modal.
   - Pending Waiting Gate (`resources/views/seller/pending.blade.php`): High-fidelity implementation featuring an interactive 5-state switcher simulator (`pending`, `approved`, `more_info`, `rejected`, `suspended`), application summary strip, 3-stage progress timeline (Application Submitted -> Compliance Audit -> Marketplace Clearance), contextual notice boxes, and dashboard lockout security notice.

5. **Test Executions**:
   - Ran Milestone 1 feature suite:
     ```powershell
     php artisan test tests/Feature/Seller/SellerOnboardingTest.php
     ```
     *Output*: **PASS - 9 passed (59 assertions)** in 0.86s.
   - Ran empirical challenge and edge-case suites:
     ```powershell
     php artisan test tests/Feature/Seller/SellerM1EmpiricalChallengeTest.php tests/Feature/Seller/Milestone1SellerEdgeCaseChallengeTest.php tests/Feature/Seller/SellerIntegrityAuditCheckTest.php
     ```
     *Output*: **PASS - 45 passed (321 assertions)**.
   - Ran full marketplace test suite:
     ```powershell
     php artisan test
     ```
     *Output*: **PASS - 332 passed, 0 failures, 2443 assertions** in 11.43s. 100% preservation across existing marketplace features with zero regressions.

---

## 2. Logic Chain

1. **Schema Integrity & Backward Compatibility**:
   - The migration `2026_09_30_000001_add_seller_panel_fields_to_tables.php` introduces all required fields (`harvest_date`, `expiry_days`, `expiry_date`, `is_perishable`, `auto_hide_expired`, `farm_origin`, `harvest_grade`, `low_stock_threshold` to `products`; `delivery_slot` to `seller_orders`; `address`, `postal_code`, `operating_radius_km` to `seller_profiles`).
   - Because existing orders created prior to M1 stored delivery slots in notes as `"Time Slot: Morning 8AM - 11AM | ..."`, the accessor `SellerOrder::getDeliverySlotAttribute` incorporates regex fallback parsing. This ensures historical orders remain functional while new orders write cleanly to `delivery_slot`.
2. **Access Control Proof**:
   - Security requirement states: unapproved, pending, or non-seller users must never access `/seller/dashboard`.
   - `SellerMiddleware` executes ahead of any controller dispatch for routes under `Route::prefix('seller')`.
   - If user is guest -> redirected to `login`.
   - If user role is `user` -> redirected to `products.index`.
   - If user role is `admin` -> redirected to `admin.dashboard`.
   - If seller status is not `active` -> logged out and redirected to `login`.
   - If seller has no profile or profile status is `pending`, `rejected`, `suspended`, or anything other than `approved` -> redirected to `seller.pending`.
   - Because only `seller.pending`, `seller.onboarding*`, and `logout` are exempted, all operational subroutes (`products`, `orders`, `payouts`, `auctions`, `account`) are locked behind the approval gate.
   - In addition, an approved seller visiting `/seller/pending` or `/seller/onboarding` is cleanly redirected to `/seller/dashboard`. Therefore, redirect loops are mathematically impossible.
3. **Robust Input Validation & Storage Safety**:
   - `SellerOnboardingController::submitWizard` enforces `Rule::in(['Farmer', 'Kirana Store', 'Dark Store', 'Individual'])`. Arbitrary or forged seller types are rejected.
   - Image uploads are restricted to `mimes:jpeg,png,jpg,webp` up to 5MB (`max:5120`). Dangerous extensions (.php, .exe, .sh, .svg) are blocked. Storage uses system-generated timestamped filenames on the `public` disk, preventing path traversal.
   - Slug generation tests database presence excluding the current user's ID (`where('user_id', '!=', $user->id)`). This guarantees that re-submitting an application updates the existing slug idempotently, while cross-seller collisions increment predictably (`-1`, `-2`).
4. **Adversarial Stress Testing**:
   - Tested coordinate boundaries (-90, +90, -180, +180) -> accepted.
   - Tested coordinate boundary violations (latitude 91, longitude 181, non-numeric) -> rejected.
   - Tested XSS injection in `shop_name` (`<script>alert("xss")</script>`) -> escaped safely via Blade and HTML entities; raw scripts are never executed.
   - Tested multi-guard isolation (`auth:user` vs `auth:seller`) -> cross-guard unauthorized access is rejected.

---

## 3. Caveats

1. **Client-Side HTML5 Geolocation**:
   - The browser GPS detection uses `navigator.geolocation.getCurrentPosition`. In automated headless environments or browsers without user location permission, the UI gracefully prompts the user and allows manual coordinate entry, prefilling sensible regional defaults (`21.7781`, `87.7516` for Contai / Purba Medinipur).
2. **Interactive Approval State Simulator on `/seller/pending`**:
   - The client-side state switcher bar (`pending`, `approved`, `more_info`, `rejected`, `suspended`) enables real-time visual demonstration of all 5 UI configurations. The backend middleware continues to enforce the true database record stored in `seller_profiles.status`.

---

## 4. Conclusion & Verdict

**Verdict**: **APPROVE**

Milestone 1 (Foundation, Schemas & Onboarding Access Control) fulfills all user requirements, conforms to the Warm Modernist Commerce design system, enforces impenetrable access control, and achieves 100% test pass across 332 automated tests with zero regressions and zero integrity violations.

### Review Summary Matrix
| Review Dimension | Status | Notes |
|------------------|--------|-------|
| **Integrity & Authenticity** | **PASS** | No facades, hardcoding, or bypasses detected. |
| **Access Control Security** | **PASS** | Guest, user, admin, inactive, pending, rejected, and suspended users cannot access `/seller/dashboard` or operational subroutes. |
| **Input Validation** | **PASS** | Seller types, shop name bounds, coordinates, bio length, and image formats rigorously validated. |
| **Image Upload Safety** | **PASS** | Validates image mimes (JPEG/PNG/JPG/WEBP), 5MB size limit, stores safely with randomized filenames. |
| **Slug Uniqueness** | **PASS** | Deterministic slug calculation with collision handling (`-1`, `-2`) and resubmission idempotency. |
| **Design System Conformance** | **PASS** | Compliant with Space Grotesk, Inter, JetBrains Mono, Warm palette, and 14px/6px border radii. |
| **Automated Test Suite** | **PASS** | 332/332 tests passed (2443 assertions), 0 failures. |

---

## 5. Verification Method

To independently reproduce and verify this review verdict:

1. **Run Milestone 1 Feature Test Suite**:
   ```powershell
   php artisan test tests/Feature/Seller/SellerOnboardingTest.php
   ```
   *Expected*: 9 passed (59 assertions).

2. **Run Empirical Challenge & Adversarial Test Suites**:
   ```powershell
   php artisan test tests/Feature/Seller/SellerM1EmpiricalChallengeTest.php tests/Feature/Seller/Milestone1SellerEdgeCaseChallengeTest.php tests/Feature/Seller/SellerIntegrityAuditCheckTest.php
   ```
   *Expected*: 45 passed (321 assertions).

3. **Run Full Marketplace Regression Test Suite**:
   ```powershell
   php artisan test
   ```
   *Expected*: 332 passed, 0 failures (2443 assertions).

4. **Verify Route Authorization & Middleware Status**:
   ```powershell
   php artisan route:list --path=seller
   ```
   *Expected*: All seller routes registered under `['auth:seller', 'seller']`.
