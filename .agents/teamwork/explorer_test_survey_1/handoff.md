# Test Infrastructure Survey Report — Bazaario Seller Panel (R1–R5)

**Author:** Test Infrastructure Explorer  
**Working Directory:** `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_test_survey_1`  
**Date:** 2026-09-30  
**Status:** Hard Handoff Complete  

---

## 1. Observation

### 1.1 Test Configuration & Environment
- **Configuration File:** `c:\xampp\htdocs\bazaario\phpunit.xml`
  - Defines two test suites:
    - `Unit`: `<directory>tests/Unit</directory>` (line 9)
    - `Feature`: `<directory>tests/Feature</directory>` (line 12)
  - Key environment overrides defined in `<php>` block:
    - Line 21: `<env name="APP_ENV" value="testing"/>`
    - Line 23: `<env name="BCRYPT_ROUNDS" value="4"/>` (fast hashing for test efficiency)
    - Line 25: `<env name="CACHE_STORE" value="array"/>`
    - Line 26: `<env name="DB_CONNECTION" value="sqlite"/>`
    - Line 27: `<env name="DB_DATABASE" value=":memory:"/>`
    - Line 29: `<env name="MAIL_MAILER" value="array"/>`
    - Line 30: `<env name="QUEUE_CONNECTION" value="sync"/>`
    - Line 31: `<env name="SESSION_DRIVER" value="array"/>`
- **Base Test Case:** `c:\xampp\htdocs\bazaario\tests\TestCase.php`
  - Extends `Illuminate\Foundation\Testing\TestCase as BaseTestCase`. Empty body; does not contain custom setup logic or traits.
- **PHP & Tooling Runtime:**
  - PHP: `8.2.12`
  - PHPUnit: `11.5.56`
  - Test runner command 1: `php artisan test`
  - Test runner command 2: `php vendor/bin/phpunit`

### 1.2 Database Setup for Testing
- **In-Memory SQLite:** Testing executes completely against SQLite in-memory (`:memory:`), avoiding external MySQL server dependencies.
- **Database Migrations:**
  - Located in `c:\xampp\htdocs\bazaario\database\migrations/` (37 migration files total).
  - All 37 migrations execute without errors under SQLite `:memory:`.
  - Pertinent schema observations:
    - `seller_profiles`: Schema defined in `2026_09_11_000003_create_seller_profiles_table.php`, extended in `2026_09_26_000001_add_kyc_fields_to_seller_profiles_table.php`, `2026_09_29_000001_add_coordinates_to_seller_profiles_table.php`, and `2026_09_29_000003_add_seller_type_to_seller_profiles_and_unit_type_to_products.php`.
      - Contains: `id`, `user_id` (unique), `shop_name`, `shop_slug` (unique), `bio`, `logo_path`, `banner_path`, `status` (`pending`, `approved`, `rejected`, `suspended`), `seller_type` (`Farmer`, `Kirana Store`, `Dark Store`, `Individual`), `commission_rate`, `trust_score`, `gstin`, `pan_number`, `trade_license_number`, `bank_account_number`, `bank_ifsc`, `fssai_number`, `rejection_reason`, `city`, `state`, `country`, `latitude`, `longitude`, `verified_at`, timestamps.
    - `products`: Schema defined in `2026_09_11_000004_create_products_table.php` and `2026_09_29_000003_add_seller_type_to_seller_profiles_and_unit_type_to_products.php`.
      - Contains: `id`, `seller_id`, `category_id`, `name`, `slug`, `short_description`, `description`, `sale_type`, `unit_type` (`piece`, `kg`, `dozen`, `bundle`, `litre`), `price`, `stock`, `sku`, `weight`, `length`, `width`, `height`, `processing_time_days`, `status`, `average_rating`, `total_reviews`, timestamps.
      - **CRITICAL FINDING:** `products` table does not currently possess `harvest_date` or `expiry_window_days` / `expiry_date` / `is_stale` / `auto_hide_expired` columns.
    - `auctions`: Schema defined in `2026_09_11_000019_create_auctions_table.php`.
      - Line 16: `$table->foreignId('seller_id')->constrained('seller_profiles')->cascadeOnDelete();`
      - **CRITICAL SCHEMA OBSERVATION:** In `auctions`, `seller_id` references `seller_profiles.id`, NOT `users.id`.
    - `bids`: Schema defined in `2026_09_11_000020_create_bids_table.php`. Model is `App\Models\AuctionBid`.
    - `seller_orders`: Schema defined in `2026_09_11_000011_create_seller_orders_table.php`.
      - Line 15: `$table->unsignedBigInteger('seller_id')->nullable();` (references `users.id` where `role = 'seller'`).
      - Fields: `subtotal`, `shipping_amount`, `commission_rate`, `commission_amount`, `payout_amount`, `status` (`placed`, `processing`, `packed`, `shipped`, `delivered`, `cancelled`, `returned`), `tracking_number`, `shipped_at`, `delivered_at`.
    - `payouts`: Schema defined in `2026_09_11_000013_create_payouts_table.php`.
      - Fields: `seller_id` (references `users.id`), `seller_order_id`, `gross_amount`, `commission_amount`, `net_amount`, `status` (`pending`, `processing`, `paid`, `failed`), `payout_reference`, `paid_at`.
- **Seeders:**
  - Three seeder classes exist in `database/seeders`: `DatabaseSeeder.php`, `MarketplaceDataSeeder.php`, `AdminOperationsDataSeeder.php`.
  - In `MarketplaceDataSeeder.php` (lines 38–58), the seeder attempts remote HTTP requests (`file_get_contents($url)`) to fetch demo images, with local SVG generation as fallback.
  - Existing automated tests **do not run seeders**; tests execute with `use RefreshDatabase;` and synthesize minimal, deterministic records directly in memory.

### 1.3 Existing Test Coverage & Execution
- Full test suite execution:
  - Command: `php artisan test`
  - Result: **278 passed (2063 assertions)** in **10.03 seconds**.
  - All test files are located in `tests/Feature/` (18 test classes) and `tests/Unit/` (1 test class):
    1. `tests/Unit/ExampleTest.php` (1 test)
    2. `tests/Feature/ExampleTest.php` (1 test)
    3. `tests/Feature/AuthAndLocalizationTest.php` (10 tests, 71 assertions — F1–F8, F50–F54)
    4. `tests/Feature/ChallengerM1AuthLocalizationTest.php` (M1 edge cases & seller redirect checks)
    5. `tests/Feature/UserProfileAndAddressTest.php` (Profile & address management CRUD)
    6. `tests/Feature/ChallengerProfileAddressEmpiricalTest.php` (M1 adversarial address/profile tests)
    7. `tests/Feature/CatalogAndDiscoveryTest.php` (F9–F23, catalog, search, proximity)
    8. `tests/Feature/Milestone2EmpiricalChallengeTest.php` (M2 boundary & coordinate tests)
    9. `tests/Feature/CatalogSearchAndFacetFilterChallengeTest.php` (M2 search/facets)
    10. `tests/Feature/ProductDetailAndCartTest.php` (F24–F38, cart grouping, coupons)
    11. `tests/Feature/AuditorM3EmpiricalVerificationTest.php` (M3 audit & coupon validation)
    12. `tests/Feature/CheckoutAndOrderLifecycleTest.php` (F39–F49, checkout, order split, cancel, reorder)
    13. `tests/Feature/ReviewerM4BEmpiricalSecurityTest.php` (M4 security, pessimistic locks)
    14. `tests/Feature/AuditorM4EmpiricalVerificationTest.php` (M4 audit)
    15. `tests/Feature/MarketplaceE2EWorkloadTest.php` (Tier 4 end-to-end workload)
    16. `tests/Feature/AdminHardeningTest.php` (40 admin routes, CSRF, transactions, 16 views)
    17. `tests/Feature/AdminChallengerVerificationTest.php` (Admin challenges, dynamic badges)
    18. `tests/Feature/AdversarialHardeningTest.php` (XSS, SQLi, privilege escalation)
    19. `tests/Feature/ChallengerStressTest.php` (Concurrency & stress tests)

### 1.4 State of Seller Panel & Existing Factories
- **Factories in `database/factories/`:**
  - Only `UserFactory.php` exists.
  - **No factory files exist** for `SellerProfile`, `Product`, `Order`, `SellerOrder`, `Auction`, `AuctionBid`, or `Payout`.
- **Existing Test Model Helpers:**
  - In `tests/Feature/CheckoutAndOrderLifecycleTest.php` (lines 61–142):
    - `createCustomer(array $attributes = []): User`
    - `createSeller(array $userAttributes = [], array $profileAttributes = []): User` (creates User + `SellerProfile`)
    - `createAddress(User $user, array $attributes = []): Address`
    - `createProduct(array $attributes = []): Product`
  - In `tests/Feature/AdminHardeningTest.php` (lines 36–59):
    - `setUp()` constructs `$this->adminUser`, `$this->regularUser`, and `$this->sellerUser`.
- **Existing Seller Routes & Views:**
  - `routes/web.php` (lines 189–206):
    - Only `seller.pending` (`/seller/pending`) and `seller.dashboard` (`/seller/dashboard`) are declared under `prefix('seller')`.
    - Routes are protected by `middleware(['auth:seller', 'seller'])`.
  - `app/Http/Middleware/SellerMiddleware.php`:
    - Checks `Auth::guard('seller')->check()`.
    - Checks `$user->status === 'active'`.
    - Checks `$user->role === 'seller'`.
  - `resources/views/seller/`:
    - `pending.blade.php`: Fully implemented (21,636 bytes).
    - `dashboard.blade.php`: Empty placeholder (0 bytes).
    - `products/`: `create`, `edit`, `index`, `show` are empty placeholders (0 bytes).
    - `orders/`: `index`, `show` are empty placeholders (0 bytes).
    - `payouts/`: `index`, `show` are empty placeholders (0 bytes).
    - `auctions/`: `create`, `edit`, `index`, `show` are empty placeholders (0 bytes).
    - `account/`: `edit-shop`, `notifications`, `profile`, `reviews`, `security`, `settings`, `shop` are empty placeholders (0 bytes).
  - No `app/Http/Controllers/Seller/` directory exists yet.

---

## 2. Logic Chain

1. **Test Runner Feasibility:**
   - From Observation 1.1 and 1.3, `php artisan test` and `phpunit` execute all 278 tests in 10.03 seconds on SQLite `:memory:` without any database setup overhead.
   - Therefore, all new Seller Panel automated tests should target SQLite `:memory:` with the `RefreshDatabase` trait, ensuring instant turnaround and CI/local parity.

2. **Schema Alignment for Model Creation:**
   - From Observation 1.2, `auctions.seller_id` references `seller_profiles.id`, whereas `seller_orders.seller_id` and `products.seller_id` reference `users.id`.
   - Therefore, any test helper or factory for `Auction` must ensure that `seller_id` is assigned from `$sellerProfile->id`, not `$user->id`, preventing foreign key constraint violations in SQLite.
   - For R3 (harvest date & freshness expiry), from Observation 1.2 and stitch template `bazaario_add_edit_product/code.html`, the `products` table requires columns for `harvest_date` (date/timestamp) and `expiry_window_days` / `expiry_date` (or `is_stale` / `auto_hide_expired`) to support automated staleness flagging and hiding.

3. **Factory & Test Helper Architecture:**
   - From Observation 1.4, the project relies primarily on inline helper methods or direct `create()` invocations rather than separate factory classes in `database/factories/`.
   - To keep test development rapid, idiomatic, and consistent with existing suites (`CheckoutAndOrderLifecycleTest`, `MarketplaceE2EWorkloadTest`), a dedicated trait (e.g. `Tests\Support\SellerTestTrait` or helper methods within the test classes) should provide standardized creation helpers: `createApprovedSeller()`, `createPendingSeller()`, `createSellerProduct()`, `createSellerOrder()`, `createAuctionForSeller()`, and `createPayoutForSeller()`.

4. **Multi-Tier Test Architecture for R1–R5:**
   - In accordance with the project's testing pattern established across M1–M4:
     - **Tier 1 (Feature & Happy Path):** Validates primary route endpoints, HTTP 200/302 responses, Blade view data bindings, and database record assertions for R1–R5.
     - **Tier 2 (Boundary, Edge Cases & Security Isolation):** Tests invalid parameters (e.g. invalid seller types, non-image uploads, out-of-range coordinates, negative prices/stock), unauthenticated/unauthorized access, and strict cross-seller tenant data isolation (preventing Seller A from viewing or mutating Seller B's products, orders, or payouts).
     - **Tier 3 (Combinatorial & Multi-Feature Logic):** Tests complex interactions such as admin approval triggering instant dashboard unlock, stock level changes affecting low-stock KPI alerts, harvest date expiry flagging products as stale and auto-hiding them from public catalog, and order fulfillment updating payout calculations.
     - **Tier 4 (Real-World End-to-End Workflows):** Tests complete operational lifecycles from seller onboarding → admin approval → product listing with harvest date → live bidding on auction → order fulfillment with delivery slot → net commission payout calculation.

5. **Regression Safety:**
   - Because existing tests (e.g. `ChallengerM1AuthLocalizationTest`) already test `/seller/pending` and `/seller/dashboard`, new routes, controllers, and middleware adjustments must not break existing assertions:
     - Pending sellers visiting `/seller/pending` must see HTTP 200.
     - Approved sellers visiting `/seller/pending` must redirect to `/seller/dashboard`.
     - Non-seller users or guests must be redirected to login or catalog.

---

## 3. Caveats

1. **Products Table Schema Extension:**
   - The current `products` table does not have `harvest_date` or `expiry_window_days` / `expiry_date` columns. When the implementation agent adds these columns via migration, test helpers must accommodate them with sensible defaults so existing catalog tests are not affected.
2. **Delivery Slot Location in Orders:**
   - Delivery time slot is stored in `orders.notes` (formatted as `"Time Slot: <slot>"`) in `CheckoutController.php` (line 158). Order workspace tests for R4 must verify slot extraction either from `orders.notes` or from a dedicated column if added.
3. **No External Network Dependencies in Seeders:**
   - As observed in `MarketplaceDataSeeder.php`, attempting to run seeders during tests is discouraged because of web asset download logic. Tests must remain purely self-contained and run against `:memory:` SQLite.
4. **Stitch HTML Templates:**
   - The templates in `c:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal` use Tailwind CDN and custom CSS classes. Blade integration should adapt layout components into shared layouts (`layouts.seller` or `@extends`) while preserving form field names (`seller_type`, `shop_name`, `latitude`, `longitude`, `unit_type`, `harvest_date`, etc.).

---

## 4. Conclusion & Recommendations

### 4.1 Recommended Test File Organization

Place all new seller tests in `tests/Feature/Seller/`:
```
tests/Feature/Seller/
├── SellerOnboardingTest.php          # R1: Onboarding wizard, seller types, GPS, pending approval gate
├── SellerDashboardTest.php           # R2: Dashboard KPIs, order count, revenue, low stock, trust score
├── SellerProductManagementTest.php   # R3: Product CRUD, unit types, harvest date, perishable expiry hiding
├── SellerOrderAndPayoutTest.php      # R4: Order isolation, status update, delivery slot, net payout calculation
├── SellerAuctionAndProfileTest.php   # R5: Shop profile, lat/lng, password, auction creation, live bids
└── SellerE2EWorkloadTest.php         # Tier 4: Comprehensive end-to-end lifecycle workload
```

### 4.2 Reusable Factory Helpers Specification

Implement a reusable trait `tests/Feature/Seller/SellerTestHelperTrait.php` (or incorporate into base test cases):

```php
namespace Tests\Feature\Seller;

use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\SellerOrder;
use App\Models\OrderItem;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Payout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait SellerTestHelperTrait
{
    protected function createSellerUser(array $attributes = [], array $profileAttributes = []): User
    {
        $seller = User::create(array_merge([
            'name'              => 'Farmer Narayan ' . uniqid(),
            'email'             => 'farmer_' . uniqid() . '@bazaario.com',
            'phone'             => '+91 98' . rand(10000000, 99999999),
            'password'          => Hash::make('SellerSecret123!'),
            'role'              => 'seller',
            'status'            => 'active',
            'email_verified_at' => now(),
        ], $attributes));

        $status = $profileAttributes['status'] ?? 'approved';
        $sellerType = $profileAttributes['seller_type'] ?? 'Farmer';

        SellerProfile::create(array_merge([
            'user_id'         => $seller->id,
            'shop_name'       => $seller->name . ' Organic Estate',
            'shop_slug'       => Str::slug('estate-' . $seller->name . '-' . $seller->id),
            'seller_type'     => $sellerType,
            'status'          => $status,
            'commission_rate' => 10.00,
            'trust_score'     => 95.00,
            'city'            => 'Contai',
            'state'           => 'West Bengal',
            'country'         => 'India',
            'latitude'        => 21.778124,
            'longitude'       => 87.751624,
            'bank_account_number' => '98765432101234',
            'bank_ifsc'       => 'SBIN0001234',
        ], $profileAttributes));

        return $seller;
    }

    protected function createPendingSeller(array $userAttributes = [], array $profileAttributes = []): User
    {
        return $this->createSellerUser($userAttributes, array_merge(['status' => 'pending'], $profileAttributes));
    }

    protected function createSellerProduct(User $seller, array $attributes = []): Product
    {
        $category = Category::firstOrCreate(
            ['slug' => 'farm-fresh'],
            ['name' => 'Farm Fresh Produce', 'status' => 'active']
        );

        return Product::create(array_merge([
            'seller_id'         => $seller->id,
            'category_id'       => $category->id,
            'name'              => 'Organic Alphonso Mango ' . uniqid(),
            'slug'              => 'alphonso-mango-' . uniqid(),
            'short_description' => 'Direct from Ratnagiri orchard.',
            'description'       => 'Harvested fresh with high Brix sweetness.',
            'sale_type'         => 'fixed_price',
            'unit_type'         => 'kg',
            'price'             => 450.00,
            'stock'             => 25,
            'sku'               => 'MNG-' . strtoupper(Str::random(6)),
            'status'            => 'active',
            'harvest_date'      => now()->format('Y-m-d'),
        ], $attributes));
    }

    protected function createAuction(User $seller, Product $product, array $attributes = []): Auction
    {
        $profile = $seller->sellerProfile;

        return Auction::create(array_merge([
            'product_id'        => $product->id,
            'seller_id'         => $profile->id, // NOTE: references seller_profiles.id
            'starting_price'    => 500.00,
            'reserve_price'     => 1200.00,
            'current_price'     => 500.00,
            'minimum_increment' => 50.00,
            'starts_at'         => now()->subHour(),
            'ends_at'           => now()->addDays(2),
            'status'            => 'live',
        ], $attributes));
    }

    protected function createSellerOrder(User $seller, array $attributes = []): SellerOrder
    {
        $buyer = User::create([
            'name'              => 'Customer ' . uniqid(),
            'email'             => 'buyer_' . uniqid() . '@example.com',
            'password'          => Hash::make('Secret123!'),
            'role'              => 'user',
            'status'            => 'active',
        ]);

        $order = Order::create([
            'order_number'            => 'BZ-ORD-' . strtoupper(Str::random(8)),
            'user_id'                 => $buyer->id,
            'order_type'              => 'cart',
            'subtotal'                => 1000.00,
            'total_amount'            => 1000.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'processing',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Main Road, Block A',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'West Bengal',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'notes'                   => 'Time Slot: Morning (8 AM - 12 PM)',
            'placed_at'               => now(),
        ]);

        $subtotal = $attributes['subtotal'] ?? 1000.00;
        $commissionRate = 10.00;
        $commissionAmount = round($subtotal * ($commissionRate / 100), 2);
        $payoutAmount = $subtotal - $commissionAmount;

        return SellerOrder::create(array_merge([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => 'SO-' . strtoupper(Str::random(8)),
            'subtotal'            => $subtotal,
            'shipping_amount'     => 0.00,
            'commission_rate'     => $commissionRate,
            'commission_amount'   => $commissionAmount,
            'payout_amount'       => $payoutAmount,
            'status'              => 'placed',
        ], $attributes));
    }
}
```

### 4.3 Detailed Tier 1–4 Test Matrix for R1–R5

| Req | Tier 1: Feature / Contract | Tier 2: Boundary & Security | Tier 3: Combinatorial & Cross-Feature | Tier 4: Real-World E2E Scenario |
| :--- | :--- | :--- | :--- | :--- |
| **R1: Seller Onboarding** | `test_r1_onboarding_form_renders`<br>`test_r1_stores_seller_type_details_and_coordinates`<br>`test_r1_pending_status_redirects_to_await_approval` | `test_r1_rejects_invalid_seller_type`<br>`test_r1_rejects_invalid_lat_lng`<br>`test_r1_customer_cannot_access_seller_onboarding` | `test_r1_admin_approval_unlocks_seller_dashboard_immediately`<br>`test_r1_rejected_seller_sees_rejection_reason` | **Onboarding Journey:** Register with OTP → Guided wizard selecting `Farmer` → GPS capture → Approval queue → Admin approves in admin panel → Direct redirect to Seller Dashboard. |
| **R2: Dashboard & KPIs** | `test_r2_dashboard_renders_200`<br>`test_r2_displays_total_orders_count`<br>`test_r2_displays_gross_revenue`<br>`test_r2_displays_active_catalog_count`<br>`test_r2_displays_trust_score` | `test_r2_zero_state_renders_without_error`<br>`test_r2_strict_tenant_isolation_no_metrics_leakage` | `test_r2_stock_drop_below_threshold_increments_low_stock_kpi`<br>`test_r2_order_status_completed_updates_revenue_kpi` | **Multi-Tenant Pulse:** Two active sellers operate concurrently. Orders placed on each are strictly partitioned; KPIs dynamically reflect individual business activity. |
| **R3: Products & Inventory** | `test_r3_product_index_renders_own_catalog`<br>`test_r3_create_product_with_unit_types` (`kg`, `dozen`, `bundle`, `litre`)<br>`test_r3_edit_and_delete_product`<br>`test_r3_update_stock_quantity` | `test_r3_rejects_negative_stock_or_price`<br>`test_r3_rejects_unsupported_unit_type`<br>`test_r3_seller_cannot_edit_another_sellers_product` | `test_r3_perishable_flagged_as_stale_within_24h_of_expiry`<br>`test_r3_perishable_auto_hidden_when_expiry_lapses`<br>`test_r3_image_upload_and_primary_assignment` | **Fresh Harvest Lifecycle:** Farmer lists perishable greens with 3-day shelf window; product is visible in public catalog with unit badge; upon expiry window lapse, listing is auto-hidden from buyers. |
| **R4: Orders & Payouts** | `test_r4_orders_index_filters_own_orders`<br>`test_r4_order_detail_shows_delivery_slot`<br>`test_r4_update_order_status_to_shipped_and_fulfilled`<br>`test_r4_payout_summary_displays_commission_breakdown` | `test_r4_seller_cannot_view_or_update_other_seller_orders`<br>`test_r4_cannot_reopen_cancelled_order`<br>`test_r4_exact_commission_deduction_math` | `test_r4_order_fulfillment_triggers_payout_record`<br>`test_r4_multi_seller_order_fulfills_independently` | **Fulfillment to Payout:** Buyer places order with assigned time slot ("Morning 8-12"); seller reviews slot in workspace, packs & marks shipped with tracking number, marks delivered; net payout is verified minus platform commission. |
| **R5: Profile & Auctions** | `test_r5_view_and_edit_shop_profile`<br>`test_r5_update_coordinates_lat_lng`<br>`test_r5_update_password`<br>`test_r5_create_auction_with_reserve_and_dates`<br>`test_r5_view_live_bids` | `test_r5_rejects_end_date_before_start_date`<br>`test_r5_rejects_incorrect_current_password`<br>`test_r5_cannot_cancel_auction_with_active_winning_bids` | `test_r5_bid_placed_updates_live_bid_stream`<br>`test_r5_reserve_met_indicator_triggers_accurately`<br>`test_r5_profile_coordinate_update_updates_nearby_distance` | **Wholesale Auction Lifecycle:** Farmer creates live auction for mango lot with reserve price ₹2,500; buyers place competing bids; terminal updates live; highest bidder winning state persists at auction close. |

---

## 5. Verification Method

### 5.1 Independent Reproduction Commands
Run the following commands in powershell from `c:\xampp\htdocs\bazaario`:

1. **Verify Baseline Test Execution (Must pass 278/278 tests):**
   ```powershell
   php artisan test
   ```
   *Expected output:* `Tests: 278 passed (2063 assertions)`.

2. **Verify Direct PHPUnit Runner Execution:**
   ```powershell
   php vendor/bin/phpunit --testsuite=Feature
   ```
   *Expected output:* `OK (277 tests, 2062 assertions)`.

3. **Verify Existing Seller Middleware & Pending Tests:**
   ```powershell
   php artisan test --filter=ChallengerM1AuthLocalizationTest
   ```
   *Expected output:* All seller redirect & pending view tests pass.

4. **Verify Database Migrations on In-Memory SQLite:**
   ```powershell
   php artisan migrate:status
   ```
   *Expected output:* All 37 migrations are registered and executed cleanly.

5. **Verify New Test Execution Once Created (Target Command):**
   ```powershell
   php artisan test --filter=Seller
   ```
   *Expected output:* All R1–R5 tests pass cleanly across Tiers 1–4.

### 5.2 Invalidation Conditions
- If any existing tests fail during `php artisan test`, regression occurred.
- If `products` or `seller_profiles` table schema is modified without a proper Laravel migration, SQLite `:memory:` migrations will fail.
- If `auctions.seller_id` is assigned a `users.id` rather than a `seller_profiles.id`, foreign key cascade checks in SQLite will throw a constraint exception.
