# Milestone 1 Empirical Challenge Report: Edge Cases & Concurrency

**Agent**: `challenger_m1_2` (Empirical Challenger: Critic & Specialist)  
**Date**: 2026-09-30T05:25:00Z  
**Verdict**: **APPROVE**  
**Target Milestone**: Milestone 1 (Foundation, Schemas & Onboarding Access Control)  
**Report File**: `c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m1_2\handoff.md`  

---

## 1. Observation

Direct empirical evidence obtained by authoring and executing test suites and inspecting codebases:

1. **Schema & Model Robustness — Automatic Expiry Date Calculation**:
   - `app/Models/Product.php` lines 135-141:
     ```php
     protected static function booted(): void
     {
         static::saving(function (Product $product) {
             if ($product->is_perishable && $product->harvest_date && $product->expiry_days && empty($product->expiry_date)) {
                 $product->expiry_date = \Carbon\Carbon::parse($product->harvest_date)->addDays((int) $product->expiry_days)->toDateString();
             }
         });
     }
     ```
   - Empirically verified via `test_product_expiry_date_automatic_calculation_edge_cases()`:
     * When `is_perishable = true`, `harvest_date = '2026-09-25'`, and `expiry_days = 7`, `expiry_date` automatically calculates to `'2026-10-02'`.
     * Explicit `expiry_date = '2026-10-15'` is preserved and not overwritten.
     * When `is_perishable = false`, `expiry_date` is not computed and remains `null`.
     * When `harvest_date` or `expiry_days` is null, `expiry_date` remains `null` without throwing exceptions.

2. **Schema & Model Robustness — Freshness & Staleness Across Edge Dates**:
   - `app/Models/Product.php` lines 143-155:
     ```php
     public function isExpired(): bool
     {
         if (!$this->is_perishable || !$this->expiry_date) {
             return false;
         }

         return $this->expiry_date->endOfDay()->isPast();
     }

     public function isStale(): bool
     {
         return $this->isExpired();
     }
     ```
   - Empirically verified via `test_product_expiry_and_staleness_across_edge_dates()`:
     * **Yesterday** (`expiry_date = Carbon::yesterday()`): `isExpired()` is `true`, `isStale()` is `true`. Model matches `Product::query()->stale()`, does NOT match `Product::query()->fresh()`, and is excluded from `Product::query()->publicVisible()` when `auto_hide_expired = true`.
     * **Today** (`expiry_date = Carbon::today()`): `isExpired()` is `false` (valid until 23:59:59 end-of-day), `isStale()` is `false`. Matches `Product::query()->fresh()`, does NOT match `Product::query()->stale()`, and is included in `Product::query()->publicVisible()`.
     * **Tomorrow** (`expiry_date = Carbon::tomorrow()`): `isExpired()` is `false`, `isStale()` is `false`. Matches `Product::query()->fresh()`, and is included in `Product::query()->publicVisible()`.
     * **Non-perishable with past date** (`is_perishable = false`): `isExpired()` is `false`, `isStale()` is `false`.
     * **Perishable with past date but `auto_hide_expired = false`**: `isExpired()` is `true`, `isStale()` is `true`, but remains visible in `Product::query()->publicVisible()` (for clearance items).

3. **Schema & Model Robustness — Low Stock Threshold & Database Default**:
   - Migration `database/migrations/2026_09_30_000001_add_seller_panel_fields_to_tables.php` line 38:
     `$table->unsignedSmallInteger('low_stock_threshold')->default(10)->after('stock');`
   - Tested behavior:
     * When omitted in creation payload, column defaults to `10`. Stock `<= 10` evaluates `isLowStock() === true`. Stock `11` evaluates `isLowStock() === false`.
     * In sqlite/MySQL, passing explicit `null` violates the `NOT NULL` constraint:
       `Integrity constraint violation: 19 NOT NULL constraint failed: products.low_stock_threshold`.
     * `Product.php` lines 207-215 (`scopeLowStock`) includes a defensive fallback for existing null values:
       `$q->whereColumn('stock', '<=', 'low_stock_threshold')->orWhere(function ($sub) { $sub->whereNull('low_stock_threshold')->where('stock', '<=', 10); })`.

4. **Schema & Model Robustness — SellerOrder Delivery Slot Accessor & Legacy Fallback**:
   - `app/Models/SellerOrder.php` lines 65-80:
     ```php
     public function getDeliverySlotAttribute($value): ?string
     {
         if (!empty($value)) {
             return $value;
         }

         if ($this->relationLoaded('order') || $this->order_id) {
             $notes = $this->order?->notes;
             if ($notes && preg_match('/Time Slot:\s*([^|]+)/i', $notes, $matches)) {
                 return trim($matches[1]);
             }
         }

         return null;
     }
     ```
   - Empirically verified via `test_seller_order_delivery_slot_direct_and_regex_fallback()`:
     * Direct column priority: When `delivery_slot = 'Morning 8AM - 11AM'` and order notes contains `'Time Slot: Evening 4PM - 7PM'`, `$so->delivery_slot` returns `'Morning 8AM - 11AM'`.
     * Fallback extraction: When direct column is `null` or empty string `""`, it extracts `'Evening 4PM - 7PM'` from `'Time Slot: Evening 4PM - 7PM | Leave at gate'`.
     * Case-insensitivity & formatting: Correctly extracts from `'time slot:   Early Bird 6AM - 8AM  '` without pipes.
     * Safe nulls: Returns `null` when notes contain no time slot, notes is null, or order relation is missing.

5. **File Upload Resilience — Storefront Image Formats & Storage**:
   - `app/Http/Controllers/Seller/SellerOnboardingController.php` lines 88, 113-118, 155-158:
     Validation rule: `'storefront_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120']`.
     Storage handling: `$imagePath = $file->storeAs('storefronts', $filename, 'public');`.
     Attributes saved: `logo_path` and `banner_path`.
   - Empirically verified via `test_storefront_image_upload_formats_and_storage_persistence()`:
     * Valid PNG: Upload succeeds, redirected to `seller.pending`, file stored at `storefronts/storefront_{user_id}_{timestamp}.png`, verified via `Storage::disk('public')->assertExists(...)`.
     * Valid JPG: Upload succeeds, file stored with `.jpg` extension.
     * Valid WEBP: Upload succeeds, file stored with `.webp` extension.
     * Rejection of invalid mime (e.g. PDF): Validation fails with session error on `storefront_image`.
     * Rejection of oversized files (> 5MB, e.g. 6MB): Validation fails with session error on `storefront_image`.

6. **Access Control & Concurrency Guardrails**:
   - Empirically verified via `test_shop_slug_collision_and_resubmission_idempotency()`:
     * Seller A registers "Golden Harvest Organics" -> slug `golden-harvest-organics`.
     * Seller B registers "Golden Harvest Organics" -> slug `golden-harvest-organics-1`.
     * Seller C registers "Golden Harvest Organics" -> slug `golden-harvest-organics-2`.
     * Seller A re-submits their profile -> slug remains `golden-harvest-organics` without incrementing.
   - Empirically verified via `test_coordinate_boundaries_and_seller_type_normalization()`:
     * Boundary coordinates (-90/90 lat, -180/180 lng) accepted.
     * Coordinates exceeding bounds (lat 95.5, lng 195.0) rejected.
     * Input types `'farmer'`, `'kirana'`, `'darkstore'`, `'individual'` normalized cleanly.
   - Empirically verified via `test_seller_middleware_comprehensive_isolation_and_lockouts()`:
     * Pending/unapproved sellers attempting to hit `/seller/dashboard`, `/seller/products`, `/seller/orders`, `/seller/payouts`, `/seller/auctions`, or `/seller/account/profile` are intercepted by `SellerMiddleware` and redirected to `seller.pending`.
     * Suspended seller profiles redirected to `seller.pending`.
     * Inactive user accounts logged out and redirected to `login`.
     * Regular customers attempting to access seller dashboard redirected to `products.index`.

7. **Test Executions**:
   - `tests/Feature/Seller/Milestone1SellerEdgeCaseChallengeTest.php`: 8 passed, 98 assertions (0.74s).
   - `tests/Feature/Seller/SellerOnboardingTest.php`: 9 passed, 59 assertions (0.62s).
   - `php artisan test` (Full Regression): **332 passed, 2443 assertions, 0 failures, duration 11.13s**.

---

## 2. Logic Chain

1. **Schema & Model Consistency**:
   - The Product model's saving hook uses `empty($product->expiry_date)`. Because an explicit expiry date evaluates to non-empty, the calculation hook respects manual date overrides while calculating expiration whenever `is_perishable = true`, `harvest_date`, and `expiry_days` are provided.
   - Staleness logic relies on Carbon's `endOfDay()->isPast()`. Since `Carbon::today()->endOfDay()` represents 23:59:59 of the current date, any product expiring today remains fresh until the day has completely elapsed. This cleanly prevents prematurely hiding products on the day of expiry while correctly marking yesterday's items as stale.
2. **Backward-Compatible Telemetry**:
   - By structuring `SellerOrder::getDeliverySlotAttribute()` to check the direct column first before falling back to order notes regex parsing, existing orders from past milestones remain compatible without requiring batch data backfilling. New orders written directly to `seller_orders.delivery_slot` bypass regex evaluation entirely.
3. **Storage Security & Integrity**:
   - Storefront file uploads are validated for `mimes:jpeg,png,jpg,webp` and capped at 5MB, preventing script or executable uploads. The generated filenames use deterministic timestamps and user IDs (`storefront_{user_id}_{timestamp}.{ext}`) stored on the `public` disk, preventing path traversal.
4. **Tenant Isolation**:
   - Every operational seller route is nested under the `['auth:seller', 'seller']` middleware stack. Unapproved sellers cannot access operational views and are redirected to `seller.pending`. Approved sellers are permitted, and re-visiting pending or onboarding routes automatically forwards them to their active dashboard.

---

## 3. Caveats

1. **`products.low_stock_threshold` Nullability vs Defaults**:
   - Column `low_stock_threshold` in `database/migrations/2026_09_30_000001_add_seller_panel_fields_to_tables.php` is defined as `NOT NULL DEFAULT 10`. If a future form in Milestone 3 allows an empty string input that converts to `null`, inserting `null` directly will trigger an integrity constraint error. Workers in Milestone 3 should ensure the controller/form request defaults null inputs to `10` or the model sets a default mutator.
2. **PHP CLI Environment and GD Extension**:
   - The local PHP CLI does not have the GD library enabled (`function_exists('imagecreatetruecolor') === false`). Tests utilizing Laravel's `UploadedFile::fake()->image()` will fail with `LogicException: GD extension is not installed`. Tests must construct valid image binaries via `UploadedFile::fake()->createWithContent(...)` using valid byte headers for PNG, JPG, and WEBP.

---

## 4. Conclusion

**Verdict: APPROVE**

Milestone 1 satisfies all criteria for Schema & Model robustness, Access Control Gate enforcement, Storefront image upload handling, and Zero-Regression across the test suite:
- Freshness, expiry, and low stock calculations verified across edge dates (yesterday, today, tomorrow).
- Delivery slot direct column and fallback regex verified across various text encodings.
- Storefront image uploads verified across PNG, JPG, and WEBP with disk persistence.
- Multi-tenant operational lockouts verified without redirect loops.
- Full regression suite of 332 tests passes cleanly (2443 assertions, 0 failures).

---

## 5. Verification Method

To independently verify these empirical results:

1. **Run the Milestone 1 Empirical Challenge Test Suite**:
   ```powershell
   php artisan test tests/Feature/Seller/Milestone1SellerEdgeCaseChallengeTest.php
   ```
   *Expected result*: 8 passed, 98 assertions.

2. **Run the Milestone 1 Onboarding Feature Suite**:
   ```powershell
   php artisan test tests/Feature/Seller/SellerOnboardingTest.php
   ```
   *Expected result*: 9 passed, 59 assertions.

3. **Run the Full Project Regression Pass**:
   ```powershell
   php artisan test
   ```
   *Expected result*: 332 passed, 2443 assertions, 0 failures.
