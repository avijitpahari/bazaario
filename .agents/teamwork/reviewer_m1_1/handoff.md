# Milestone 1 Quality & Adversarial Review Report: Foundation, Schemas & Onboarding Access Control (R1)

**Agent**: `reviewer_m1_1` (Reviewer & Adversarial Critic)  
**Date**: 2026-09-30T05:25:00Z  
**Verdict**: **APPROVE**  
**Target Project**: Bazaario Marketplace (`c:\xampp\htdocs\bazaario`)  
**Assignment**: Reviewer 1 for Milestone 1: Foundation, Schemas & Onboarding Access Control (R1)

---

## 1. Observation

Direct empirical observations, file inspections, command executions, and test runs:

1. **Database Migration & Schema Extensions**:
   - Inspected `database/migrations/2026_09_30_000001_add_seller_panel_fields_to_tables.php`:
     * Line 15-40: `products` table extended with `harvest_date` (date, nullable), `expiry_days` (unsignedSmallInteger, nullable), `expiry_date` (date, nullable), `is_perishable` (boolean, default false), `auto_hide_expired` (boolean, default true), `farm_origin` (string 255, nullable), `harvest_grade` (string 50, nullable), and `low_stock_threshold` (unsignedSmallInteger, default 10).
     * Line 42-47: `seller_orders` table extended with `delivery_slot` (string 100, nullable).
     * Line 49-60: `seller_profiles` table extended with `address` (string 255, nullable), `postal_code` (string 20, nullable), and `operating_radius_km` (unsignedSmallInteger, default 25, nullable).
     * Line 16, 19, 22, 25, 28, 31, 34, 37, 44, 51, 54, 57: Every addition is guarded by `if (!Schema::hasColumn(...))`.
     * Line 67-106: `down()` method cleanly drops all added columns with reverse `Schema::hasColumn` checks.
   - Tested migration reversibility:
     * Command: `php artisan migrate:rollback --step=1; php artisan migrate`
     * Result:
       ```text
       INFO  Rolling back migrations.
       2026_09_30_000001_add_seller_panel_fields_to_tables ... 204.19ms DONE
       INFO  Running migrations.
       2026_09_30_000001_add_seller_panel_fields_to_tables ... 263.08ms DONE
       ```
   - Verified columns via Tinker:
     * Command: `php artisan tinker --execute="echo Schema::hasColumn('products', 'harvest_date') && Schema::hasColumn('seller_orders', 'delivery_slot') && Schema::hasColumn('seller_profiles', 'address') ? 'ALL_COLUMNS_EXIST' : 'MISSING_COLUMNS';"`
     * Result: `ALL_COLUMNS_EXIST`

2. **Model Enhancements & Logic**:
   - `app/Models/Product.php`:
     * Line 35-43: `$fillable` contains all 8 new fields.
     * Line 45-61: `casts(): array` properly casts `harvest_date` (`date`), `expiry_date` (`date`), `expiry_days` (`integer`), `is_perishable` (`boolean`), `auto_hide_expired` (`boolean`), `low_stock_threshold` (`integer`).
     * Line 134-141: `booted()` saving lifecycle hook computes `expiry_date = Carbon::parse($product->harvest_date)->addDays((int) $product->expiry_days)->toDateString()` when `is_perishable`, `harvest_date`, and `expiry_days` are set and `expiry_date` is omitted.
     * Line 143-162: Helper methods `isExpired()`, `isStale()`, `isLowStock()`.
     * Line 180-215: Scopes `scopeFresh()`, `scopeStale()`, `scopePublicVisible()`, `scopeLowStock()`.
   - `app/Models/SellerOrder.php`:
     * Line 24: `delivery_slot` in `$fillable`.
     * Line 65-80: `getDeliverySlotAttribute($value)` accessor returns column value if present, with regex fallback parsing `Time Slot:\s*([^|]+)` from `order->notes` for historical consignments.
     * Line 82-90: Scopes `scopeForSeller()`, `scopeStatus()`.
   - `app/Models/SellerProfile.php`:
     * Line 20, 24, 27: `address`, `postal_code`, `operating_radius_km` in `$fillable`.
     * Line 46: `'operating_radius_km' => 'integer'` cast.
     * Line 126-144: Helpers `isApproved()`, `isPending()` and scopes `scopeApproved()`, `scopePending()`.

3. **Access Control & Loop-Free Middleware**:
   - `app/Http/Middleware/SellerMiddleware.php`:
     * Line 24-34: Checks `Auth::guard('seller')->check()`.
     * Line 43-57: Checks `$user->status === 'active'`.
     * Line 64-84: Checks `$user->role === 'seller'`.
     * Line 93-108: Evaluates `$profile->status !== 'approved'` and redirects unapproved sellers to `seller.pending`.
     * Line 116-143: `isExemptFromApprovalCheck(Request $request)` provides loop prevention, exempting `seller/pending`, `seller/onboarding*`, and `logout` / `seller/logout`.

4. **Controller Architecture & Data Mutation**:
   - `app/Http/Controllers/Seller/SellerOnboardingController.php`:
     * Line 27-49: `showWizard()` redirects approved sellers to `seller.dashboard`, renders wizard for pending/unapproved sellers.
     * Line 57-181: `submitWizard()`:
       - Line 67-82: Normalizes lowercase/slugified types (`'farmer'`, `'kirana'`, `'darkstore'`, `'individual'`) to capitalized titles (`'Farmer'`, `'Kirana Store'`, `'Dark Store'`, `'Individual'`).
       - Line 84-110: Strict server-side validation rules covering all fields, coordinate bounds (`between:-90,90` for lat, `between:-180,180` for lng), image mimes (`jpeg,png,jpg,webp`), and max size (5120 KB).
       - Line 113-119: Secure storefront image storage to `public` disk in `storefronts/`.
       - Line 121-130: Deterministic unique slug generation with collision incrementation (`shop-name-1`, `shop-name-2`).
       - Line 133-164: Atomic `DB::transaction` performing `updateOrCreate` on `SellerProfile`, setting `status = 'pending'`.
       - Line 167-176: Activity logging with timestamp and coordinates.
       - Line 179-181: Flash feedback redirect to `seller.pending`.
     * Line 186-202: `pending()` redirects approved sellers to `seller.dashboard`, renders status terminal for unapproved sellers.

5. **Blade Layouts & Views**:
   - `resources/views/layouts/seller-onboarding.blade.php`: High-fidelity Warm Modernist design, Bazaario brand logo, Space Grotesk / Inter / JetBrains Mono fonts, Material Symbols, flash toasts, Seller Help modal.
   - `resources/views/layouts/seller.blade.php`: Workspace layout with fixed 72-unit sidebar, active route pills, search bar with `⌘K` badge, verified merchant pill, flash toasts.
   - `resources/views/seller/onboarding/wizard.blade.php`: 5-step interactive wizard with 4 selection cards (Farmer, Kirana Store, Dark Store, Individual), HTML5 GPS auto-detect, manual Lat/Lng coordinate inputs, radar preview, dropzone image upload, 1-click edit jumps in review matrix, and modal confirmation.
   - `resources/views/seller/pending.blade.php`: Approval terminal featuring 5-state client simulator (`pending`, `approved`, `more_info`, `rejected`, `suspended`), 3-stage progress timeline (Submitted -> Compliance Audit -> Marketplace Clearance), summary info strip, security restriction notice.

6. **Test Execution & Regression Audit**:
   - Syntax check: `php -l` on all 12 files passed with `No syntax errors detected`.
   - Feature test: `php artisan test tests/Feature/Seller/SellerOnboardingTest.php` -> **9 passed (59 assertions)** in 0.65s.
   - Empirical challenge suite: `php artisan test tests/Feature/Seller/Milestone1SellerEdgeCaseChallengeTest.php` -> **7 passed (73 assertions)**.
   - Full regression suite: `php artisan test` -> **331 passed, 0 failures, 2418 assertions** in 10.64s.

---

## 2. Logic Chain

1. **Schema & Tenancy Separation**:
   - *Observation*: Migration `2026_09_30_000001_add_seller_panel_fields_to_tables.php` defines perishable, freshness, delivery slot, and address columns with `hasColumn` guards and clean reversible down drops.
   - *Inference*: Future milestones (M2 through M5) can immediately leverage certified columns without risking schema drift or failed migrations in fresh or rolled-back environments.
2. **Access Control Without Redirect Loops**:
   - *Observation*: `SellerMiddleware` checks `$profile->status !== 'approved'` and redirects to `seller.pending`. The method `isExemptFromApprovalCheck` explicitly exempts `seller/pending`, `seller/onboarding*`, and `logout`.
   - *Inference*: A pending seller is strictly barred from `/seller/dashboard` and operational routes, but will not trigger an infinite redirect cycle when browsing `/seller/pending` or completing `/seller/onboarding`.
3. **State Machine Consistency**:
   - *Observation*: `SellerOnboardingController` redirects approved sellers visiting `/seller/onboarding` or `/seller/pending` directly to `/seller/dashboard`.
   - *Inference*: Approved sellers cannot get trapped in the onboarding wizard or pending gate.
4. **Data Integrity & Adversarial Robustness**:
   - *Observation*: `submitWizard` normalizes seller type casing, validates coordinates strictly between [-90, 90] and [-180, 180], wraps mutations in `DB::transaction`, and resolves slug collisions via iterative uniqueness checks.
   - *Inference*: Malicious or malformed inputs cannot corrupt tenant state or bypass data integrity rules.
5. **Anti-Cheating & Integrity Verification**:
   - *Observation*: Source code across models, middleware, and controllers contains zero hardcoded test inputs, zero dummy stubs, and zero bypassed tasks. Tests dynamically instantiate factories and verify real database persistence.
   - *Inference*: The implementation satisfies full authentic production standards.

---

## 3. Caveats

1. **Browser Geolocation API**:
   - The HTML5 `navigator.geolocation.getCurrentPosition()` requires HTTPS or localhost in production browsers. The wizard handles permission denial or timeouts gracefully by presenting manual coordinate inputs with immediate radar feedback.
2. **Local Storage Symlink**:
   - Uploaded storefront images are placed in `storage/app/public/storefronts/`. On local deployments, `php artisan storage:link` must be created to serve public storefront images.

---

## 4. Conclusion

**Verdict: APPROVE**

Milestone 1 (Foundation, Schemas & Onboarding Access Control) fulfills all requirements outlined in `ORIGINAL_REQUEST.md`, complies with `PROJECT.md` architecture and the Warm Modernist design system, and demonstrates exceptional code quality and security hardening:
- All database columns are migrated, idempotent, and reversible.
- Models (`Product`, `SellerOrder`, `SellerProfile`) provide certified helper methods, scopes, and saving hooks.
- Access control gate in `SellerMiddleware` cleanly isolates unapproved merchants without redirect loops.
- `SellerOnboardingController` and Blade templates integrate stitch designs into responsive, interactive workflows.
- Full test suite passes: **331 tests passed (2418 assertions), 0 failures, 0 regressions**.

---

## 5. Verification Method

To independently reproduce this verification:

1. **Verify Schema & Idempotent Reversibility**:
   ```powershell
   php artisan migrate:rollback --step=1
   php artisan migrate
   php artisan tinker --execute="echo Schema::hasColumn('products', 'harvest_date') && Schema::hasColumn('seller_orders', 'delivery_slot') && Schema::hasColumn('seller_profiles', 'address') ? 'ALL_COLUMNS_EXIST' : 'MISSING_COLUMNS';"
   ```
   *Expected output*: `ALL_COLUMNS_EXIST`.

2. **Verify PHP Syntax Across Files**:
   ```powershell
   php -l database/migrations/2026_09_30_000001_add_seller_panel_fields_to_tables.php
   php -l app/Models/Product.php
   php -l app/Models/SellerOrder.php
   php -l app/Models/SellerProfile.php
   php -l app/Http/Middleware/SellerMiddleware.php
   php -l app/Http/Controllers/Seller/SellerOnboardingController.php
   php -l routes/web.php
   php -l tests/Feature/Seller/SellerOnboardingTest.php
   ```
   *Expected output*: All files report `No syntax errors detected`.

3. **Run Milestone 1 Feature Test Suite**:
   ```powershell
   php artisan test tests/Feature/Seller/SellerOnboardingTest.php
   ```
   *Expected output*: `9 passed (59 assertions)`.

4. **Run Full Test Suite**:
   ```powershell
   php artisan test
   ```
   *Expected output*: `331 passed (2418 assertions), 0 failures`.
