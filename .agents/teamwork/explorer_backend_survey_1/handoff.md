# Backend Codebase Exploration & Architecture Survey: Seller Portal (R1–R5)

**Agent**: `explorer_backend_survey_1`  
**Date**: 2026-09-30  
**Target Project**: Bazaario Marketplace (`c:\xampp\htdocs\bazaario`)  
**Design Reference**: `c:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal`  

---

## 1. Observation

Direct observations from source code, schema migrations, existing controllers, routes, views, and test executions:

### 1.1 Authentication & Guards
- `config/auth.php` (lines 41–59) defines 3 session guards:
  - `user`: driver `session`, provider `users`
  - `seller`: driver `session`, provider `users`
  - `admin`: driver `session`, provider `users`
- `app/Http/Controllers/AuthController.php` (lines 62–127) logs sellers into `user` and `seller` guards upon credential match (`Auth::guard('seller')->login($user, $remember)`).
- Role redirection logic in `AuthController::redirectByRole()` (lines 566–593):
  ```php
  case 'seller':
      if ($user->sellerProfile && $user->sellerProfile->status === 'approved') {
          return redirect()->route('seller.dashboard');
      }
      return redirect()->route('seller.pending')
          ->with('info', 'Your seller account is waiting for admin approval.');
  ```
- `app/Http/Middleware/SellerMiddleware.php` (lines 24–87) checks:
  1. `Auth::guard('seller')->check()`
  2. `$user->status !== 'active'`
  3. `$user->role !== 'seller'`
  *Crucial Observation*: `SellerMiddleware` does **NOT** check `$user->sellerProfile->status === 'approved'`.

### 1.2 Routing & Route Definitions
- `routes/web.php` (lines 188–206) contains only two minimal seller route stubs:
  ```php
  Route::prefix('seller')->name('seller.')->group(function () {
      Route::middleware(['auth:seller', 'seller'])->group(function () {
          Route::get('/pending', function () {
              $user = Auth::guard('seller')->user();
              if ($user && $user->role === 'seller' && $user->sellerProfile && $user->sellerProfile->status === 'approved') {
                  return redirect()->route('seller.dashboard');
              }
              return view('seller.pending', compact('user'));
          })->name('pending');

          Route::get('/dashboard', function () {
              $user = Auth::guard('seller')->user();
              return view('seller.dashboard', compact('user'));
          })->name('dashboard');
      });
  });
  ```
- There are **no other seller routes** defined in `routes/web.php`.
- `routes/api.php` does **not exist** (Laravel 11 default setup).
- No seller controllers exist in `app/Http/Controllers/` (no `app/Http/Controllers/Seller/` directory).

### 1.3 Database Schema & Models
1. **Users & Seller Profiles**:
   - `database/migrations/2026_09_11_000003_create_seller_profiles_table.php` and its alters (`2026_09_26_000001_add_kyc_fields...`, `2026_09_29_000001_add_coordinates...`, `2026_09_29_000003_add_seller_type...`):
     - Columns present: `id`, `user_id` (unique), `shop_name`, `shop_slug`, `bio`, `logo_path`, `banner_path`, `status` (`'pending'|'approved'|'rejected'|'suspended'`), `seller_type` (default `'Kirana Store'`), `commission_rate`, `trust_score`, `city`, `state`, `country`, `latitude`, `longitude`, `verified_at`, `gstin`, `pan_number`, `trade_license_number`, `bank_account_number`, `bank_ifsc`, `fssai_number`, `rejection_reason`.
     - `app/Models/SellerProfile.php`:
       - `distanceTo(?float $lat, ?float $lng): float` calculates Haversine distance in km.
       - `getCityCoordinates(?string $city): array` returns default geo-coordinates.
       - Relationships: `user()` (`belongsTo(User::class)`), `products()` (`hasMany(Product::class, 'seller_id', 'user_id')`).
     - **Missing fields**: `street_address` / `address`, `operating_radius_km` (or `delivery_radius_km`).

2. **Products & Inventory**:
   - `database/migrations/2026_09_11_000004_create_products_table.php`:
     - Columns present: `id`, `seller_id` (FK to `users.id`), `category_id`, `name`, `slug`, `short_description`, `description`, `sale_type` (`fixed_price|auction`), `unit_type` (`piece|kg|dozen|bundle|litre`), `price`, `stock`, `sku`, dimensions (`weight`, `length`, `width`, `height`), `processing_time_days`, `status` (`draft|active|inactive|archived`), `average_rating`, `total_reviews`.
     - `app/Models/Product.php`:
       - Relationships: `seller()` (`belongsTo(User::class, 'seller_id')`), `category()`, `images()`, `primaryImage()`, `reviews()`, `cartItems()`, `orderItems()`, `auction()`.
     - **Missing fields for R3**: `harvest_date` (date/timestamp), `expiry_days` (unsigned integer), `expiry_date` (date/timestamp), `is_perishable` (boolean default false), `auto_hide_expired` (boolean default true), `farm_origin` (string), `harvest_grade` (string), `low_stock_threshold` (unsigned integer default 10).

3. **Orders, Seller Orders & Delivery Slots**:
   - `database/migrations/2026_09_11_000010_create_orders_table.php` & `2026_09_11_000011_create_seller_orders_table.php`:
     - `orders`: `id`, `order_number`, `user_id`, `subtotal`, `discount_amount`, `shipping_amount`, `total_amount`, `payment_method`, `payment_status`, `order_status`, delivery address fields, `notes`, `placed_at`.
     - `seller_orders`: `id`, `order_id`, `seller_id` (FK to `users.id`), `seller_order_number`, `subtotal`, `shipping_amount`, `commission_rate`, `commission_amount`, `payout_amount`, `status` (`'placed'|'processing'|'packed'|'shipped'|'delivered'|'cancelled'|'returned'`), `tracking_number`, `shipped_at`, `delivered_at`.
     - `app/Http/Controllers/User/CheckoutController.php` (line 155–158):
       Delivery time slot is stored inside `orders.notes` string:
       `$notes = $notes ? ($notes . " | Time Slot: " . $timeSlot) : ("Time Slot: " . $timeSlot);`
     - **Missing**: A direct column or typed helper for `delivery_slot` on `seller_orders` or `orders`.

4. **Payouts & Commission**:
   - `database/migrations/2026_09_11_000013_create_payouts_table.php` & `app/Models/Payout.php`:
     - Columns: `id`, `seller_id` (references `users.id`), `seller_order_id`, `gross_amount`, `commission_amount`, `net_amount`, `status` (`pending|processing|paid|failed`), `payout_reference`, `paid_at`.
     - Relationship: `sellerOrder()` (`belongsTo(SellerOrder::class)`).
     - Both `seller_orders` and `payouts` already store:
       - `gross_amount` (or `subtotal`)
       - `commission_rate` & `commission_amount`
       - `net_amount` (or `payout_amount`)

5. **Auctions & Bids**:
   - `database/migrations/2026_09_11_000019_create_auctions_table.php` & `app/Models/Auction.php`:
     - **CRITICAL FOREIGN KEY DIFFERENCE**:
       `auctions.seller_id` references `seller_profiles.id` (`foreignId('seller_id')->constrained('seller_profiles')`).
       Contrast with `products.seller_id`, which references `users.id`!
       In `Auction.php`: `return $this->belongsTo(SellerProfile::class, 'seller_id');`.
     - Other columns: `product_id`, `starting_price`, `reserve_price`, `current_price`, `minimum_increment`, `starts_at`, `ends_at`, `status` (`scheduled|live|ended|cancelled`), `winner_id`.
     - `bids` table: `auction_id`, `user_id`, `amount`, timestamps. Model: `AuctionBid.php` (`table = 'bids'`).

### 1.4 Blade Views & Layouts
- `resources/views/seller/pending.blade.php` is complete (363 lines, 21.6 KB) with a 3-step KYC pipeline stepper, dossier card, guidance notes, and status badges.
- All other 20 files in `resources/views/seller/` are **0-byte empty stub files**:
  - `seller/dashboard.blade.php` (0 bytes)
  - `seller/products/index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php` (all 0 bytes)
  - `seller/orders/index.blade.php`, `show.blade.php` (all 0 bytes)
  - `seller/payouts/index.blade.php`, `show.blade.php` (all 0 bytes)
  - `seller/auctions/index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php` (all 0 bytes)
  - `seller/account/shop.blade.php`, `profile.blade.php`, `edit-shop.blade.php`, `security.blade.php`, `settings.blade.php`, `notifications.blade.php`, `reviews.blade.php` (all 0 bytes)
- `resources/views/layouts/seller.blade.php` does **not exist**. Only `admin.blade.php`, `app.blade.php`, and `user.blade.php` exist in `resources/views/layouts/`.

### 1.5 Stitch Reference Templates
Located in `C:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal`:
1. `bazaario_seller_onboarding_approval` (Step 1-6 Onboarding Wizard & Pending Approval)
2. `bazaario_seller_dashboard_performance` (Seller Dashboard with 6 KPI metrics, revenue charts, recent orders, live auctions pulse)
3. `bazaario_my_products_catalog_management` (Catalog list, SKU search, category filters, quick edit, drawer)
4. `bazaario_add_edit_product` (5-Section Product Creation/Editing: Title & Category, Pricing & Units, Stock, Agronomic Ledger / Harvest & Expiry, Media Uploads)
5. `bazaario_inventory_stock_management` (Inventory & stock control, restock updates, low-stock threshold triggers)
6. `bazaario_my_orders_order_workspace` (2-column Orders workspace: queue management, delivery slot cards, status updates, fulfillment modal)
7. `bazaario_my_payouts_commission_breakdown` (Payouts ledger, 4 KPI cards, transparent commission formula callout, settlement history)
8. `bazaario_auction_management_live_bidding` (Auction terminal, countdown timer, bidding ladder, lot creation form, cancel/end actions)
9. `bazaario_shop_profile_location_account_settings` (3-Tab settings: Shop profile, Farm location & Lat/Lng coordinates, Security/Password)
10. `warm_modernist_commerce/DESIGN.md` (Design tokens: Space Grotesk, Inter, JetBrains Mono, 14px border radius, `#FFFDF8` canvas)

### 1.6 Existing Test Suite Health
Ran `php artisan test`:
- Result: **278 passed** (2063 assertions, duration 9.94s, exit code 0).
- Existing features M1–M4 are completely green.

---

## 2. Logic Chain

From the observations to the architectural conclusions and implementation roadmap:

```
[Observation 1.1 & 1.4: SellerMiddleware does not check sellerProfile->status === 'approved', and seller/dashboard.blade.php is 0 bytes]
     │
     ▼
[Step 1: Access Control Gap]
A pending (or non-approved) seller who visits /seller/dashboard currently bypasses approval checks and receives an empty 200 view.
The acceptance criteria explicitly demands:
"Non-approved sellers cannot access dashboard until status is approved by admin."
     │
     ▼
[Required Action 1: Enforce Approval Middleware]
Update SellerMiddleware (or introduce an EnsureSellerApproved middleware/check) such that if $user->sellerProfile->status !== 'approved', the user is redirected to route('seller.pending') with a flash warning. Route /seller/pending and onboarding routes must be exempted from this approval check.

─────────────────────────────────────────────────────────────────────────────

[Observation 1.3: products table lacks harvest_date, expiry_days, is_perishable, auto_hide_expired, farm_origin, harvest_grade]
     │
     ▼
[Step 2: Schema Gap for R3]
Requirement R3 demands: "Set harvest date / expiry window and auto-flag/hide stale perishable listings."
Without these database fields, neither seller input forms nor catalog filtering can persist or evaluate perishable expiry state.
     │
     ▼
[Required Action 2: Product Freshness Migration]
Create a migration to add to products:
- harvest_date (date, nullable)
- expiry_days (unsignedSmallInteger, nullable)
- expiry_date (date, nullable)
- is_perishable (boolean, default false)
- auto_hide_expired (boolean, default true)
- farm_origin (string, nullable)
- harvest_grade (string, nullable)
- low_stock_threshold (unsignedSmallInteger, default 10)
Update Product model with fillables, casts, and scopeNotExpired() / auto-flag logic.

─────────────────────────────────────────────────────────────────────────────

[Observation 1.3: auctions.seller_id references seller_profiles.id, while products.seller_id references users.id]
     │
     ▼
[Step 3: Relationship Discrepancy]
When creating an auction lot for an authenticated seller, assigning $user->id to auctions.seller_id will trigger a SQL foreign key constraint failure if user.id does not match seller_profile.id.
     │
     ▼
[Required Action 3: Exact Seller ID Mapping in Auctions]
In SellerAuctionController and Auction models, always assign $user->sellerProfile->id to auctions.seller_id, and assign $user->id to products.seller_id.

─────────────────────────────────────────────────────────────────────────────

[Observation 1.3: Delivery slots are stored inside orders.notes without a dedicated column on seller_orders]
     │
     ▼
[Step 4: Delivery Slot Visibility in Seller Orders Workspace]
Requirement R4 demands: "view assigned delivery slots and mark orders as fulfilled."
     │
     ▼
[Required Action 4: Delivery Slot Persistence & Extraction]
Add delivery_slot column to seller_orders (and orders) via migration, or provide a robust accessor on SellerOrder:
public function getDeliverySlotAttribute(): string
that parses 'Time Slot: ...' from the parent order notes if delivery_slot is null, guaranteeing non-empty slot displays in the workspace.

─────────────────────────────────────────────────────────────────────────────

[Observation 1.2, 1.4 & 1.5: 20 empty stub view files in resources/views/seller/, no seller layout, no Seller controllers]
     │
     ▼
[Step 5: Full Seller Panel Build Plan]
The entire UI and backend controller layer for R1–R5 must be created using the stitch templates as pixel-perfect foundations while binding dynamic Eloquent models.
```

---

## 3. Detailed Gap Analysis & Requirements Mapping

### R1. Seller Onboarding Module
- **Current State**: `AuthController` creates a default `SellerProfile` with `status = 'pending'`, and redirects to `seller.pending`. No interactive multi-step onboarding wizard exists.
- **Required**:
  1. Route `GET /seller/onboarding` and `POST /seller/onboarding`:
     - Guided wizard:
       - Step 1: Account info (name, email, phone)
       - Step 2: Seller type selection (`Farmer`, `Kirana Store`, `Dark Store`, `Individual`)
       - Step 3: Shop/Farm details (shop name, bio, storefront image upload)
       - Step 4: Location details (address, city, state, country, postal code, Lat/Lng coordinates with browser GPS auto-detect and manual overrides)
       - Step 5: Review & Confirmation
       - Step 6: Awaiting Admin Approval state (`seller.pending`)
  2. Middleware protection: Prevent unapproved sellers from accessing `/seller/dashboard`.

### R2. Seller Dashboard & Performance Analytics
- **Current State**: `resources/views/seller/dashboard.blade.php` is 0 bytes. Route returns an empty view.
- **Required**:
  1. Controller `App\Http\Controllers\Seller\SellerDashboardController`:
     - Computes real-time metrics:
       - Total Orders: `SellerOrder::where('seller_id', $user->id)->count()`
       - Total Revenue: `SellerOrder::where('seller_id', $user->id)->where('status', '!=', 'cancelled')->sum('subtotal')`
       - Active Listed Products: `Product::where('seller_id', $user->id)->count()`
       - Low-Stock Alerts: `Product::where('seller_id', $user->id)->where('stock', '<=', 10)->count()`
       - Seller Trust Score: `$user->sellerProfile->trust_score ?? 95.0`
       - Next Scheduled Payout: pending payout sum
       - Recent orders list with customer and fulfillment status
       - Active live auctions pulse
  2. Integrated Blade view `resources/views/seller/dashboard.blade.php` based on `bazaario_seller_dashboard_performance/code.html`.

### R3. Product & Inventory Management
- **Current State**: Views in `seller/products/` are empty. Product model lacks harvest and perishable fields.
- **Required**:
  1. Migration adding `harvest_date`, `expiry_days`, `expiry_date`, `is_perishable`, `auto_hide_expired`, `farm_origin`, `harvest_grade`, `low_stock_threshold` to `products`.
  2. Controller `App\Http\Controllers\Seller\SellerProductController`:
     - `index`: Paginated products with search by name/SKU, category filter, stock status filter.
     - `create`: Form with category selector, unit types (`kg`, `dozen`, `bundle`, `litre`, `piece`, `pack`), harvest date picker, freshness shelf window (`3`, `5`, `7`, `10`, `14+` days), image upload dropzone.
     - `store`: Handles image upload to `public/storage/products`, slug generation, SKU generation, perishable calculation (`expiry_date = harvest_date + expiry_days`).
     - `edit`: Edit existing product details.
     - `update`: Update product attributes and image.
     - `destroy`: Safe delete or soft archive.
  3. Controller `App\Http\Controllers\Seller\SellerInventoryController`:
     - Dedicated inventory workspace with quick stock adjustments and low-stock indicators.
  4. Auto-hide/flag logic: Perishable products whose `expiry_date < now()` are flagged as expired/stale in seller view and excluded from active buyer catalog if `auto_hide_expired` is true.

### R4. Order & Payout Management
- **Current State**: `seller_orders` table has monetary amounts and status enum. `seller/orders/` and `seller/payouts/` views are 0 bytes.
- **Required**:
  1. Controller `App\Http\Controllers\Seller\SellerOrderController`:
     - `index`: Lists orders filtered strictly to authenticated seller (`seller_orders.seller_id = Auth::id()`), with status tabs (`all`, `placed`, `processing`, `packed`, `shipped`, `delivered`, `cancelled`).
     - `show`: Detailed order modal/page with customer details, itemized SKUs, delivery slot banner, delivery courier telemetry.
     - `updateStatus`: Allows updating status to `processing`, `packed` (Ready for Pickup), `shipped` (Out for Delivery), or `delivered` (Fulfilled).
     - `fulfill`: Quick one-click action to mark order as fulfilled (`delivered`).
  2. Controller `App\Http\Controllers\Seller\SellerPayoutController`:
     - Displays 4 financial summary cards: Lifetime Revenue, Marketplace Commission, Total Settled, Pending/Processing Settlements.
     - Transparent commission rate callout (e.g. 10.0% fixed commission, Net Payout = Gross - Commission).
     - Itemized settlements ledger with order reference, gross amount, commission deduction, net payout, and payout status.

### R5. Profile & Auction Management
- **Current State**: Views in `seller/account/` and `seller/auctions/` are 0 bytes. `auctions` table exists.
- **Required**:
  1. Controller `App\Http\Controllers\Seller\SellerProfileController`:
     - `edit`: 3-tab settings page (Shop Profile, Farm Location & Lat/Lng, Security/Password).
     - `updateProfile`: Updates shop name, bio, storefront image, banner image.
     - `updateLocation`: Updates street address, city, state, country, latitude, longitude.
     - `updatePassword`: Verifies current password before updating to new secure password.
  2. Controller `App\Http\Controllers\Seller\SellerAuctionController`:
     - `index`: Dual-pane live bidding terminal: active live auction monitor (countdown timer, highest bid indicator, bid history ladder, reserve met status) + master auction table.
     - `create`: Form to select fixed-price product from seller's catalog, set starting price, reserve price, minimum increment, start time, end time.
     - `store`: Validates that product belongs to authenticated seller, creates auction with `seller_id = $user->sellerProfile->id`.
     - `cancel`: Cancels eligible auction lot before bids or per platform rules.
     - `end`: Ends lot early if eligible.

---

## 4. Caveats

- **No Caveats**. All aspects of the existing codebase, routes, models, migrations, auth systems, and stitch templates were directly inspected and verified against the running application and test suite.

---

## 5. Conclusion & Actionable Execution Plan

The backend survey confirms the existing Laravel foundation is stable, cleanly structured, and passes all 278 existing automated tests. Implementing R1–R5 requires:

1. **Database Migrations**:
   - `add_address_and_radius_to_seller_profiles_table`: adds `address` (varchar), `operating_radius_km` (int).
   - `add_freshness_and_perishable_fields_to_products_table`: adds `harvest_date` (date), `expiry_days` (int), `expiry_date` (date), `is_perishable` (bool), `auto_hide_expired` (bool), `auto_flag_stale` (bool), `farm_origin` (varchar), `harvest_grade` (varchar), `low_stock_threshold` (int).
   - `add_delivery_slot_to_seller_orders_table`: adds `delivery_slot` (varchar) to `seller_orders` and `orders`.

2. **Middleware & Security**:
   - Add approval enforcement in `SellerMiddleware` to redirect pending sellers to `seller.pending`.
   - Ensure `seller.pending` and `seller.onboarding` routes remain accessible.

3. **Controllers (App\Http\Controllers\Seller)**:
   - `SellerDashboardController`
   - `SellerOnboardingController`
   - `SellerProductController` & `SellerInventoryController`
   - `SellerOrderController`
   - `SellerPayoutController`
   - `SellerAuctionController`
   - `SellerProfileController`

4. **Views & Layout (`resources/views/seller`)**:
   - Create `resources/views/layouts/seller.blade.php` based on `warm_modernist_commerce/DESIGN.md`.
   - Populate `seller/onboarding.blade.php` and update `seller/pending.blade.php`.
   - Populate `seller/dashboard.blade.php`.
   - Populate `seller/products/index.blade.php`, `create.blade.php`, `edit.blade.php`, `inventory.blade.php`.
   - Populate `seller/orders/index.blade.php`, `show.blade.php`.
   - Populate `seller/payouts/index.blade.php`.
   - Populate `seller/auctions/index.blade.php`, `create.blade.php`.
   - Populate `seller/account/profile.blade.php`.

5. **Route Registrations (`routes/web.php`)**:
   - Wire all routes under `prefix('seller')->name('seller.')`.

---

## 6. Verification Method

To verify the survey and subsequent implementations independently:

1. **Run full regression test suite**:
   ```powershell
   php artisan test
   ```
   Must pass all 278 existing tests with zero regressions.

2. **Verify Route Registration**:
   ```powershell
   php artisan route:list --path=seller
   ```
   Must list all seller endpoints without collision or syntax errors.

3. **Verify Blade View Syntax**:
   ```powershell
   php -l resources/views/seller/dashboard.blade.php
   php -l resources/views/layouts/seller.blade.php
   ```

4. **Verify Database Integrity**:
   Inspect newly added columns via `php artisan migrate:status` and Tinker:
   ```powershell
   php artisan tinker --execute="echo App\Models\Product::first();"
   ```
