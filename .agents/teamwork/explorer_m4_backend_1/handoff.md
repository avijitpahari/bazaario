# Milestone 4 Backend Architecture & Implementation Roadmap: Order Fulfillment & Payout Management

**Agent**: `explorer_m4_backend_1`  
**Milestone**: Milestone 4 (Order Fulfillment & Payout Management — Features 26–33)  
**Date**: 2026-09-30  
**Working Directory**: `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m4_backend_1`  

---

## 1. Observation

Direct code and database inspections yielded the following findings across the Bazaario application:

### 1.1 Existing Routes in `routes/web.php`
In `routes/web.php` lines 220–229, order and payout endpoints under the `auth:seller` and `seller` middleware group are currently dummy inline closures returning views or `back()`:
```php
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', fn () => view('seller.orders.index'))->name('index');
        Route::get('/{order}', fn () => view('seller.orders.show'))->name('show');
        Route::patch('/{order}/status', fn () => back())->name('update-status');
    });

    Route::prefix('payouts')->name('payouts.')->group(function () {
        Route::get('/', fn () => view('seller.payouts.index'))->name('index');
        Route::get('/{payout}', fn () => view('seller.payouts.show'))->name('show');
    });
```
The named routes configured are:
- `seller.orders.index` (`GET /seller/orders`)
- `seller.orders.show` (`GET /seller/orders/{order}`)
- `seller.orders.update-status` (`PATCH /seller/orders/{order}/status`)
- `seller.payouts.index` (`GET /seller/payouts`)
- `seller.payouts.show` (`GET /seller/payouts/{payout}`)

### 1.2 Model Architectures & Relationships
1. **`app/Models/SellerOrder.php`** (Lines 14–41, 45–64):
   - `$fillable`:
     ```php
     protected $fillable = [
         'order_id', 'seller_id', 'seller_order_number', 'subtotal',
         'shipping_amount', 'commission_rate', 'commission_amount',
         'payout_amount', 'status', 'delivery_slot', 'tracking_number',
         'shipped_at', 'delivered_at',
     ];
     ```
   - Casts: `subtotal`, `shipping_amount`, `commission_rate`, `commission_amount`, `payout_amount` to `'decimal:2'`; `shipped_at`, `delivered_at` to `'datetime'`.
   - Relationships:
     - `order()`: `BelongsTo(Order::class)`
     - `seller()`: `BelongsTo(User::class, 'seller_id')`
     - `items()`: `HasMany(OrderItem::class)`
     - `payouts()`: `HasMany(Payout::class)`
   - Accessor `getDeliverySlotAttribute($value)` (Lines 65–80): provides fallback extraction from parent `order.notes` using regex `Time Slot:\s*([^|]+)`.
   - Scopes: `scopeForSeller($query, int $sellerId)`, `scopeStatus($query, string $status)`.

2. **`app/Models/Payout.php`** (Lines 13–32, 36–52):
   - `$fillable`:
     ```php
     protected $fillable = [
         'seller_id', 'seller_order_id', 'gross_amount',
         'commission_amount', 'net_amount', 'status',
         'payout_reference', 'paid_at',
     ];
     ```
   - Casts: `gross_amount`, `commission_amount`, `net_amount` to `'decimal:2'`; `paid_at` to `'datetime'`.
   - Relationships:
     - `seller()`: `BelongsTo(User::class, 'seller_id')`
     - `sellerOrder()`: `BelongsTo(SellerOrder::class)`
   - Scope: `scopePaid($query)` (`where('status', 'paid')`).

3. **`app/Models/Order.php`** (Lines 16–49, 53–72):
   - Fillable: `order_number`, `user_id`, `coupon_id`, `order_type`, `subtotal`, `discount_amount`, `shipping_amount`, `total_amount`, `payment_method`, `payment_status`, `order_status`, `delivery_full_name`, `delivery_phone`, `delivery_address_line_1`, `delivery_address_line_2`, `delivery_city`, `delivery_state`, `delivery_country`, `delivery_postal_code`, `notes`, `placed_at`.
   - Relationships:
     - `user()`: `BelongsTo(User::class)`
     - `sellerOrders()`: `HasMany(SellerOrder::class)`
     - `items()`: `HasManyThrough(OrderItem::class, SellerOrder::class)`
     - `payments()`: `HasMany(Payment::class)`

4. **`app/Models/OrderItem.php`** (Lines 16–35, 38–46):
   - Fillable: `seller_order_id`, `product_id`, `product_name`, `product_image`, `sku`, `unit_price`, `quantity`, `total_price`.
   - Relationships:
     - `sellerOrder()`: `BelongsTo(SellerOrder::class)`
     - `product()`: `BelongsTo(Product::class)`

5. **`app/Models/User.php`** (Lines 18–29, 45–53):
   - Role: `'seller'`, `'user'`, `'admin'`.
   - `products()`: `HasMany(Product::class, 'seller_id')`
   - `sellerProfile()`: `HasOne(SellerProfile::class)`

6. **`app/Models/SellerProfile.php`** (Lines 9–48, 116–130):
   - Key attributes: `user_id`, `shop_name`, `shop_slug`, `seller_type`, `status` (`'pending'`, `'approved'`, `'rejected'`, `'suspended'`), `commission_rate` (default 10.00), `trust_score` (default 94.00), `bank_account_number`, `bank_ifsc`, `address`, `city`, `state`, `postal_code`, `operating_radius_km`.
   - `isApproved()`: `return $this->status === 'approved';`

### 1.3 Database Migrations & Actual Schema Columns
1. **`seller_orders` table** (`2026_09_11_000011_create_seller_orders_table.php` & `2026_09_30_000001_add_seller_panel_fields_to_tables.php`):
   - `id`: unsignedBigInteger, auto_increment, PK
   - `order_id`: unsignedBigInteger, indexed
   - `seller_id`: unsignedBigInteger, nullable, indexed
   - `seller_order_number`: string(60), unique
   - `subtotal`: decimal(12, 2)
   - `shipping_amount`: decimal(12, 2), default 0.00
   - `commission_rate`: decimal(5, 2), default 0.00
   - `commission_amount`: decimal(12, 2), default 0.00
   - `payout_amount`: decimal(12, 2), default 0.00
   - `status`: enum in base migration (`['placed', 'processing', 'packed', 'shipped', 'delivered', 'cancelled', 'returned']`), default `'placed'`
   - `delivery_slot`: string(100), nullable (added by migration `2026_09_30_000001`)
   - `tracking_number`: string(150), nullable
   - `shipped_at`: timestamp, nullable
   - `delivered_at`: timestamp, nullable
   - `created_at`, `updated_at`: timestamps
   *Delta Needed*: `courier_name` (string, nullable), `handover_confirmed_at` (timestamp, nullable), and ensuring `status` column accepts `'ready_for_pickup'` and `'fulfilled'`.

2. **`payouts` table** (`2026_09_11_000013_create_payouts_table.php`):
   - `id`: unsignedBigInteger, auto_increment, PK
   - `seller_id`: unsignedBigInteger, indexed
   - `seller_order_id`: unsignedBigInteger, nullable, indexed
   - `gross_amount`: decimal(12, 2), default 0.00
   - `commission_amount`: decimal(12, 2), default 0.00
   - `net_amount`: decimal(12, 2), default 0.00
   - `status`: enum(`['pending', 'processing', 'paid', 'failed']`), default `'pending'`
   - `payout_reference`: string(150), nullable
   - `paid_at`: timestamp, nullable
   - `created_at`, `updated_at`: timestamps
   *Delta Needed*: `apmc_cess` (decimal(12, 2), default 0.00) column or dynamic accessor, plus alias accessors (`amount`, `commission_fee`, `reference_number`).

3. **`orders` table** (`2026_09_11_000010_create_orders_table.php`):
   - `id`, `order_number`, `user_id`, `coupon_id`, `order_type`, `subtotal`, `discount_amount`, `shipping_amount`, `total_amount`, `payment_method`, `payment_status`, `order_status`, `delivery_full_name`, `delivery_phone`, `delivery_address_line_1`, `delivery_address_line_2`, `delivery_city`, `delivery_state`, `delivery_country`, `delivery_postal_code`, `notes`, `placed_at`, `created_at`, `updated_at`.

4. **`order_items` table** (`2026_09_11_000012_create_order_items_table.php`):
   - `id`, `seller_order_id`, `product_id`, `product_name`, `product_image`, `sku`, `unit_price`, `quantity`, `total_price`, `created_at`.

### 1.4 Missing Controllers & Views
- `app/Http/Controllers/Seller/SellerOrderController.php` — **Does not exist yet**
- `app/Http/Controllers/Seller/SellerPayoutController.php` — **Does not exist yet**
- `resources/views/seller/orders/index.blade.php` — Currently 0 bytes
- `resources/views/seller/orders/show.blade.php` — Currently 0 bytes
- `resources/views/seller/payouts/index.blade.php` — Currently 0 bytes
- `resources/views/seller/payouts/show.blade.php` — Currently 0 bytes

### 1.5 UI Templates in `stitch_bazaario_seller_onboarding_portal`
- `bazaario_my_orders_order_workspace/code.html` (687 lines):
  - 2-Column Split Workspace: 8 cols table on left + 4 cols sticky inspector on right
  - Assigned Delivery Slot Card: "TODAY: 4:00 PM – 6:00 PM", "Courier Slot Confirmed", "Bazaario Hyperlocal Fleet #BLR-44", packing countdown
  - Status progression stepper: Placed -> Confirmed -> Packed/Processing -> Ready for Pickup -> Courier Handover & Fulfilled
  - Itemized deduction breakdown: Gross - 10% Bazaario Commission - 1.5% APMC Mandi Cess = Net Seller Payout
  - Handover Verification Modal: `#modalOrderId`, `#modalCustomerName`, `#modalGrossAmount`, `#modalNetAmount`, `#modalCourier`, confirm fulfillment trigger
- `bazaario_my_payouts_commission_breakdown/code.html` (565 lines):
  - Upcoming Settlement Banner: Next scheduled NEFT transfer (Friday), bank account (HDFC Bank •••• 4092, IFSC HDFC0001245)
  - 4 KPI cards: Total Revenue (Lifetime), Marketplace Commission (Fixed 10.0%), Total Settled (Paid), Pending / Processing
  - Transparent Commission Rate Structure Card: Tier 1 Prime Farmer, 10% Platform fee, APMC Cess, 0 listing fee, 0 gateway fee, free weekly NEFT
  - Two-Column Settlement Workspace: Settlements Table with status filter tabs (All, Processing, Paid, Pending, Failed) + Settlement Inspector with Bank Routing details and NEFT lifecycle timeline

---

## 2. Logic Chain

### 2.1 Multi-Tenancy Isolation Enforcement
1. **Observation 1.1 & 1.2**: All `/seller/*` routes are protected by middleware `['auth:seller', 'seller']`. In `SellerMiddleware.php`, unauthenticated users redirect to login and unapproved sellers redirect to `/seller/pending`.
2. **Observation 1.4 & Existing Patterns**: In `SellerProductController.php` (lines 75–80):
   ```php
   protected function authorizeProductOwnership(User $user, Product $product): void
   {
       if ((int) $product->seller_id !== (int) $user->id) {
           abort(403, 'Unauthorized. You do not have permission to manage this product.');
       }
   }
   ```
3. **Logic Inference**: For both `SellerOrderController` and `SellerPayoutController`:
   - All collection queries (`index()`) MUST be scoped to the authenticated seller:
     `$sellerId = Auth::guard('seller')->id() ?? Auth::id();`
     `SellerOrder::where('seller_id', $sellerId)...`
     `Payout::where('seller_id', $sellerId)...`
   - Any single resource action (`show()`, `updateStatus()`, `fulfill()`, `handover()`):
     If `$order->seller_id !== $sellerId` or `$payout->seller_id !== $sellerId`, the controller MUST immediately issue `abort(403, 'Unauthorized access to this resource.')`.
   - This prevents cross-tenant data leakage and satisfies Feature 26 (Seller Data Isolation Guardrail) and Tier 2 adversarial tests.

### 2.2 Status Progression & Lifecycle Transitions
1. **Observation 1.1, 1.3 & 1.5**: In the database schema, initial status is `'placed'` (or `'pending'`). The stitch template and PROJECT.md specify:
   `placed` / `pending` → `processing` → `ready_for_pickup` → `fulfilled`.
2. **Logic Inference for Transition Rules**:
   - `placed` / `pending` → `processing`: Allowed (merchant starts packing).
   - `processing` → `ready_for_pickup`: Allowed (merchant finishes packing, parcels staged in dispatch bay).
   - `ready_for_pickup` → `fulfilled`: Allowed (courier physical custody transfer confirmed).
   - Direct jump `processing` → `fulfilled`: Allowed for express dispatch flow.
   - Any state → `cancelled`: Allowed ONLY if not already `fulfilled` or `delivered`.
   - `fulfilled` → any other state: Strictly FORBIDDEN (terminal immutable state).
   - `cancelled` → any other state: Strictly FORBIDDEN (terminal immutable state).
3. **Audit Timestamp & Fulfillment Trigger**:
   - Marking `ready_for_pickup`: Sets tracking timestamp.
   - Marking `fulfilled`: Must execute inside an atomic `DB::transaction`:
     1. Update `seller_orders.status = 'fulfilled'`.
     2. Record `handover_confirmed_at = now()` and `delivered_at = now()`.
     3. Create or update associated `Payout` record in `payouts` table:
        - `seller_id`: `$sellerOrder->seller_id`
        - `seller_order_id`: `$sellerOrder->id`
        - `gross_amount`: `$sellerOrder->subtotal`
        - `commission_amount`: `$sellerOrder->commission_amount` (10%)
        - `apmc_cess`: `round($sellerOrder->subtotal * 0.015, 2)` (1.5%)
        - `net_amount`: `round($sellerOrder->subtotal - $commissionAmount - $apmcCess, 2)`
        - `status`: `'pending'` (escrow held awaiting weekly bank NEFT release)
        - `payout_reference`: `'PAY-' . strtoupper(Str::random(10))`
     4. Check parent `Order`: if all child `SellerOrder` records for the parent order are fulfilled, transition parent `order.order_status = 'completed'`.

### 2.3 Financial Calculation Formula (Feature 31)
1. **Observation 1.5 & PROJECT.md line 139**:
   Formula: `Net Payout = subtotal - (subtotal * commission_rate) - (subtotal * apmc_cess)`
2. **Concrete Example from Stitch Template**:
   - Gross Subtotal: ₹1,950.00
   - Platform Commission (10%): `1950 * 0.10 = ₹195.00`
   - APMC Mandi Cess / Tech Fee (1.5%): `1950 * 0.015 = ₹29.25`
   - Net Seller Payout: `1950 - 195 - 29.25 = ₹1,725.75`
3. **Logic Inference**: Both models and controllers should standardize this exact calculation so views and API responses match.

---

## 3. Implementation Roadmap

### Phase 1: Database Migration & Model Extensions
1. **Migration (`database/migrations/2026_09_30_000002_add_order_fulfillment_fields_to_tables.php`)**:
   ```php
   Schema::table('seller_orders', function (Blueprint $table) {
       if (!Schema::hasColumn('seller_orders', 'courier_name')) {
           $table->string('courier_name', 150)->nullable()->after('delivery_slot');
       }
       if (!Schema::hasColumn('seller_orders', 'handover_confirmed_at')) {
           $table->timestamp('handover_confirmed_at')->nullable()->after('delivered_at');
       }
   });

   Schema::table('payouts', function (Blueprint $table) {
       if (!Schema::hasColumn('payouts', 'apmc_cess')) {
           $table->decimal('apmc_cess', 12, 2)->default(0.00)->after('commission_amount');
       }
   });
   ```
2. **`SellerOrder.php` Extensions**:
   - Add `'courier_name'`, `'handover_confirmed_at'` to `$fillable`.
   - Cast `'handover_confirmed_at' => 'datetime'`.
   - Helper accessors:
     - `getCourierNameAttribute($val)`: returns `$val ?: 'Bazaario Hyperlocal Fleet #BLR-44'`.
     - `getApmcCessAttribute()`: returns `round(((float) $this->subtotal) * 0.015, 2)`.
     - `getNetPayoutCalculatedAttribute()`: returns `max(0, (float) $this->subtotal - (float) $this->commission_amount - $this->apmc_cess)`.
   - Relationship: `public function payout(): HasOne { return $this->hasOne(Payout::class); }`.
   - Scopes: `scopePending`, `scopeProcessing`, `scopeReadyForPickup`, `scopeFulfilled`, `scopeCancelled`.

3. **`Payout.php` Extensions**:
   - Add `'apmc_cess'` to `$fillable` and `$casts`.
   - Add alias accessors:
     - `getAmountAttribute()`: returns `$this->gross_amount`.
     - `getCommissionFeeAttribute()`: returns `$this->commission_amount`.
     - `getReferenceNumberAttribute()`: returns `$this->payout_reference`.
     - `getApmcCessAttribute($val)`: returns `$val ?? round(((float) $this->gross_amount) * 0.015, 2)`.

### Phase 2: Route Updates in `routes/web.php`
Replace lines 220–229 in `routes/web.php` with:
```php
    use App\Http\Controllers\Seller\SellerOrderController;
    use App\Http\Controllers\Seller\SellerPayoutController;

    // Milestone 4: Order Fulfillment & Payout Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [SellerOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [SellerOrderController::class, 'show'])->name('show');
        Route::patch('/{order}/status', [SellerOrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{order}/fulfill', [SellerOrderController::class, 'fulfill'])->name('fulfill');
        Route::post('/{order}/handover', [SellerOrderController::class, 'handover'])->name('handover');
    });

    Route::prefix('payouts')->name('payouts.')->group(function () {
        Route::get('/', [SellerPayoutController::class, 'index'])->name('index');
        Route::get('/{payout}', [SellerPayoutController::class, 'show'])->name('show');
    });
```

### Phase 3: Controller Implementations in `app/Http/Controllers/Seller/`

#### 1. `SellerOrderController.php`
- **`index(Request $request)`**:
  - Authenticate seller via `Auth::guard('seller')->user() ?? Auth::user()`.
  - Base query: `SellerOrder::where('seller_id', $seller->id)->with(['order.user', 'items.product.images', 'payouts'])`.
  - Search filter: Order number, customer name, phone, product name, tracking number.
  - Tab filter: `all`, `pending` (`['placed', 'pending']`), `processing`, `ready` (`['ready_for_pickup', 'packed']`), `fulfilled` (`['fulfilled', 'delivered']`), `cancelled` (`['cancelled', 'returned']`).
  - Date filter: `all`, `today`, `yesterday`, `last_7_days`.
  - Tab counts: aggregate counts for all status tabs.
  - Telemetry KPIs: Capacity percentage (crates sealed), SLA on-time record, Pending Payout Accrual sum.
  - Active focused order: `selectedOrder = $orders->firstWhere('id', $request->query('selected')) ?? $orders->first()`.
  - Render view: `seller.orders.index`.

- **`show(Request $request, $order)`**:
  - Resolve `$sellerOrder = $order instanceof SellerOrder ? $order : SellerOrder::findOrFail($order)`.
  - Authorize: `abort_unless((int) $sellerOrder->seller_id === (int) $seller->id, 403)`.
  - Load relations: `load(['order.user', 'items.product.images', 'payouts'])`.
  - If `$request->wantsJson()`, return JSON payload.
  - Otherwise render `seller.orders.show` (or redirect to index with selected order query parameter).

- **`updateStatus(Request $request, $order)`**:
  - Authorize ownership (403 on mismatch).
  - Validate: `status` required, in `['processing', 'ready_for_pickup', 'fulfilled', 'cancelled', 'packed', 'delivered']`.
  - Guardrail: if current status is `'cancelled'` or `'fulfilled'`, return back with error.
  - If target status is `'fulfilled'`, redirect/delegate to `fulfill()` logic.
  - Inside `DB::transaction`: update status, touch timestamps, return back with success message.

- **`fulfill(Request $request, $order)` & `handover(Request $request, $order)`**:
  - Authorize ownership (403 on mismatch).
  - Guardrail: if already `'fulfilled'` or `'cancelled'`, reject.
  - Inside atomic `DB::transaction`:
    1. Update `$sellerOrder->update(['status' => 'fulfilled', 'delivered_at' => now(), 'handover_confirmed_at' => now(), 'courier_name' => $request->input('courier_name', $sellerOrder->courier_name)])`.
    2. Upsert `Payout` record for `$sellerOrder`.
    3. Update parent `Order` if all sibling seller orders are fulfilled.
  - Return JSON if AJAX or redirect back with flash toast.

#### 2. `SellerPayoutController.php`
- **`index(Request $request)`**:
  - Authenticate seller.
  - Base query: `Payout::where('seller_id', $seller->id)->with(['sellerOrder.order', 'seller.sellerProfile'])`.
  - Status tabs: `all`, `processing`, `paid`, `pending`, `failed`.
  - Search filter: payout reference, order number.
  - 4 Summary KPIs:
    1. Lifetime Gross Revenue
    2. Total Marketplace Commission
    3. Total Settled (Paid)
    4. Pending / Processing Sum
  - Settlement Account details from `$seller->sellerProfile` (`bank_account_number`, `bank_ifsc`, `shop_name`).
  - Active focused payout: `selectedPayout = $payouts->firstWhere('id', $request->query('selected')) ?? $payouts->first()`.
  - Render view: `seller.payouts.index`.

- **`show(Request $request, $payout)`**:
  - Resolve `$payout = $payout instanceof Payout ? $payout : Payout::findOrFail($payout)`.
  - Authorize: `abort_unless((int) $payout->seller_id === (int) $seller->id, 403)`.
  - Load relations: `load(['sellerOrder.order.user', 'sellerOrder.items', 'seller.sellerProfile'])`.
  - If `$request->wantsJson()`, return JSON; otherwise render `seller.payouts.show`.

### Phase 4: Blade View Implementations
1. **`resources/views/seller/orders/index.blade.php`**:
   - Extends `layouts.seller`.
   - Integrates `stitch_bazaario_seller_onboarding_portal/bazaario_my_orders_order_workspace/code.html`.
   - Dynamic binding: Search bar, status tabs with live badge counts, 2-column split workspace, assigned delivery slot window, customer & items table, Handover Verification modal (`fulfillmentModal`) submitting CSRF POST to `seller.orders.fulfill`.
2. **`resources/views/seller/orders/show.blade.php`**:
   - Extends `layouts.seller`.
   - Dedicated standalone inspection view for order details, items, courier dispatch telemetry, and invoice printing.
3. **`resources/views/seller/payouts/index.blade.php`**:
   - Extends `layouts.seller`.
   - Integrates `stitch_bazaario_seller_onboarding_portal/bazaario_my_payouts_commission_breakdown/code.html`.
   - Dynamic binding: Upcoming transfer banner with bank account telemetry, 4 KPI cards, transparent commission structure card (10% platform + 1.5% APMC cess formula), settlements table with status filters, settlement detail inspector column with NEFT timeline.
4. **`resources/views/seller/payouts/show.blade.php`**:
   - Extends `layouts.seller`.
   - Full settlement receipt view with breakdown, linked batch orders, and UTR reference number.

### Phase 5: Automated Test Suite (`tests/Feature/Seller/SellerOrderAndPayoutTest.php`)
Create comprehensive 5-Tier test suite:
- **Tier 1 (Functional Route Coverage)**:
  - Approved seller can view orders index (`HTTP 200`).
  - Approved seller can view single order detail (`HTTP 200`).
  - Approved seller can view payouts index (`HTTP 200`).
  - Approved seller can view single payout detail (`HTTP 200`).
- **Tier 2 (Strict Multi-Tenancy & Authorization)**:
  - Seller A cannot view Seller B's order (`HTTP 403`).
  - Seller A cannot update status of Seller B's order (`HTTP 403`).
  - Seller A cannot fulfill Seller B's order (`HTTP 403`).
  - Seller A cannot view Seller B's payout record (`HTTP 403`).
  - Orders and payouts index only display authenticated seller's data.
  - Unauthenticated guest is redirected to login.
  - Unapproved pending seller is redirected to `/seller/pending`.
- **Tier 3 (Status Progression & Lifecycle Transitions)**:
  - Transition `placed` → `processing` updates status.
  - Transition `processing` → `ready_for_pickup` updates status and delivery slot telemetry.
  - Fulfill / Handover transitions order to `fulfilled`, sets `handover_confirmed_at` and creates `Payout` record.
  - Cannot revert `fulfilled` order to `pending` or `processing`.
  - Cannot fulfill a `cancelled` order.
- **Tier 4 (Financial Settlement & Commission Accuracy)**:
  - Verifies commission calculation (10%) and APMC cess (1.5%).
  - Payout ledger displays accurate gross, commission, and net amounts.
  - Payout KPIs compute accurate lifetime totals.
- **Tier 5 (Adversarial & Concurrency Hardening)**:
  - Atomic `DB::transaction` rollbacks on database failure.
  - Input sanitization on status and notes.

---

## 4. Caveats

1. **MySQL vs SQLite Enum Handling**:
   - In SQLite `:memory:` used during testing, column types are loosely typed, meaning values like `'ready_for_pickup'` can be inserted into an enum column without error.
   - However, in MySQL (production/development), inserting `'ready_for_pickup'` or `'fulfilled'` into an enum column defined as `enum('placed', 'processing', 'packed', 'shipped', 'delivered', 'cancelled', 'returned')` will trigger SQL errors.
   - *Mitigation*: The migration MUST modify `seller_orders.status` to `string('status', 50)->default('placed')` or explicitly extend the enum values.
2. **APMC Cess Persistence**:
   - The original `payouts` table migration did not contain an `apmc_cess` column.
   - *Mitigation*: Add `apmc_cess` column in migration and provide an accessor in `Payout.php` and `SellerOrder.php` that dynamically computes `subtotal * 0.015` if the column is null or 0.
3. **No Code Implementation in Explorer Turn**:
   - In accordance with the Explorer archetype constraints, no modifications to application code or migrations were made during this investigation. All findings and proposals are documented here for the implementer worker.

---

## 5. Conclusion

The codebase is well-prepared for Milestone 4:
- The design tokens, layouts (`layouts.seller`), and stitch HTML templates (`bazaario_my_orders_order_workspace` and `bazaario_my_payouts_commission_breakdown`) provide pixel-perfect blueprints.
- Existing models (`SellerOrder`, `Payout`, `Order`, `OrderItem`) have established Eloquent relationships, needing only minor attribute fillables, accessors, and migration extensions (`courier_name`, `handover_confirmed_at`, `apmc_cess`, flexible status string).
- Multi-tenancy must follow the established pattern in `SellerProductController`: strictly querying by `Auth::guard('seller')->id()` and throwing `abort(403)` on cross-tenant access attempts.
- The financial calculation is verified: `Gross - 10% Commission - 1.5% APMC Cess = Net Payout` (e.g. ₹1,950 - ₹195 - ₹29.25 = ₹1,725.75).
- The transition from stub closures to `SellerOrderController` and `SellerPayoutController` is clean, modular, and directly verifiable with a 5-tier test suite.

---

## 6. Verification Method

To independently verify the findings of this investigation:
1. **Inspect Route Stubs**:
   ```bash
   php artisan route:list --path=seller/orders
   php artisan route:list --path=seller/payouts
   ```
   *Expected Observation*: Confirms routes currently point to `Closure` instead of controllers.
2. **Inspect Existing Seller Tests**:
   ```bash
   php artisan test tests/Feature/Seller/SellerDashboardTest.php
   php artisan test tests/Feature/Seller/SellerProductManagementTest.php
   ```
   *Expected Observation*: All 19 dashboard tests and 31 product management tests pass cleanly, confirming authentication, approval gating, and tenancy patterns.
3. **Post-Implementation Gate Verification**:
   When the implementer worker completes M4, run:
   ```bash
   php artisan test tests/Feature/Seller/SellerOrderAndPayoutTest.php
   php artisan test tests/Feature/Seller/
   ```
   *Expected Observation*: 100% pass across all Seller test suites without regressions.
