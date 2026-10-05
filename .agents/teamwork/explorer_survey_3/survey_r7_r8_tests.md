# Phase 0 Survey Report: Module R7, Module R8, and Test Infrastructure

**Survey Date**: 2026-09-29  
**Surveyor**: explorer_survey_3  
**Target Modules**:
- **Module R7**: Checkout & Order Lifecycle (Features 39–49)
- **Module R8**: User Profile & Address Management (Features 50–54)
- **Automated Test Infrastructure**: PHPUnit, Feature/Unit test suites, runner capabilities

---

## 1. Executive Summary & Status Matrix

| Module | Feature ID | Feature Name | Status | Key Artifacts | Critical Gaps / Discrepancies |
|---|---|---|---|---|---|
| **R7** | **Feature 39** | Proceed to Checkout | ⚠️ Partially Implemented / Broken | `routes/web.php` (`checkout.index`), `CheckoutController@index` | View `user.checkout.index` is completely **missing** (throws 500 error). Directory `resources/views/user/checkout` does not exist. |
| **R7** | **Feature 40** | Enter Delivery Address | ⚠️ Partially Implemented | `CheckoutController@index`, `@store` | No checkout UI exists; `store()` expects `address_id` in database; no inline address creation on checkout. |
| **R7** | **Feature 41** | Select Delivery Time Slot | ❌ Missing | None | No `delivery_time_slot` column in `orders` schema; no validation rule in `CheckoutController`; no UI picker. |
| **R7** | **Feature 42** | Select Payment Method (COD) | ⚠️ Partially Implemented | `orders.payment_method` enum (`cod`), `CheckoutController@store` | Backend enum and validation support `'cod'`, but no UI exists to select it; no `Payment` record created upon placement. |
| **R7** | **Feature 43** | Place Order | 🚨 Severe Bug / Incomplete | `CheckoutController@store` | Creates parent `Order`, but **never creates `SellerOrder` or `OrderItem` records**! Cart items cleared, leaving order with 0 items. Product stock not decremented. |
| **R7** | **Feature 44** | View Order Summary | ⚠️ Broken | `CheckoutController@success` | View `user.checkout.success` is **missing** (throws 500 error). Even if loaded, `sellerOrders` collection is empty. |
| **R7** | **Feature 45** | View Order History | ✅ Implemented | `OrderController@index`, `user.account.orders.index` | Dynamic order history with status tabs, pagination, and totals exists. (Dependent on order items existing). |
| **R7** | **Feature 46** | Track Order (Per-Seller) | ❌ Missing in UI | `seller_orders` table has `status` & `tracking_number` | `user.account.orders.show` only displays items; lacks seller-specific tracking timeline, courier badge, or tracking telemetry. |
| **R7** | **Feature 47** | View Order Status | ⚠️ Partially Implemented | `orders.order_status`, `seller_orders.status` | Parent status (`pending`, `processing`, `completed`, `cancelled`) displayed in `orders.index` and `orders.show`, but per-seller granular statuses not surfaced. |
| **R7** | **Feature 48** | Cancel Order | ❌ Missing | None | No cancellation route, no `OrderController@cancel` method, and no "Cancel Order" button in user order views. |
| **R7** | **Feature 49** | 1-Click Reorder | ❌ Missing | None | No reorder route, no `OrderController@reorder` method, and no "Reorder" action button in order views. |
| **R8** | **Feature 50** | View Profile | ⚠️ Partially Implemented | `ProfileController@show`, `user.account.profile` | Displays credentials, avatar, addresses, and status; lacks explicit buyer **Trust Rank** badge/score required by AC. |
| **R8** | **Feature 51** | Edit Profile (Name, Phone, Bio) | ⚠️ Partially Implemented | `ProfileController@edit`, `user.account.edit-profile` | `users` table **lacks `bio` column**; `ProfileController@update` does not validate/save `bio`; edit view has no `bio` field. |
| **R8** | **Feature 52** | Upload Profile Image | ✅ Implemented | `ProfileController@updatePhoto`, `user.account.profile` | Validates image file, deletes old avatar, stores in `public/profile-images`, updates `users.profile_image`. |
| **R8** | **Feature 53** | Change Password | ✅ Implemented | `SecurityController@updatePassword`, `user.account.security` | Validates `current_password`, enforces strong password rules, verifies with `Hash::check()`, updates hash. |
| **R8** | **Feature 54** | Manage Delivery Addresses (CRUD) | ⚠️ Partially Implemented (UI Gap) | `AddressController` (CRUD), `user.account.addresses` | Controller has full CRUD (`index`, `store`, `update`, `destroy`, `setDefault`), but `addresses.blade.php` **lacks Edit form/modal** (only has Add, Delete, Set Default). |
| **Tests**| **Test Suite** | Automated Testing Infrastructure | ⚠️ Imbalanced (0% for R7 & R8) | `phpunit.xml`, `tests/Feature/*`, `tests/Unit/*` | 64 tests pass (100% admin/auction/hardening). **0 tests exist for Checkout, Orders, Profile, or Addresses.** |

---

## 2. Deep Dive: Module R7 — Checkout & Order Lifecycle (Features 39–49)

### Feature 39: Proceed to Checkout
- **Acceptance Criteria**: Proceed to Checkout initiates order creation workflow.
- **Route**: `GET /checkout` (Name: `checkout.index`)
- **Controller & Method**: `App\Http\Controllers\User\CheckoutController@index`
- **Middleware**: `['auth:user', 'user']`
- **Current Behavior**:
  1. Checks if cart exists and is non-empty (`$cart->items->isEmpty()`), redirecting back to `cart.index` if empty.
  2. Fetches user addresses and default address.
  3. Calculates subtotal, shipping (`₹99`), discount from `session('coupon')`, and net total.
  4. Calls `return view('user.checkout.index', compact(...));`.
- **Defect / Discrepancy**:
  - The view file `resources/views/user/checkout/index.blade.php` **does not exist**. Navigating to `/checkout` triggers `InvalidArgumentException: View [user.checkout.index] not found.`
  - The button "Proceed to Checkout" in `resources/views/user/cart/index.blade.php` (line 126) and `components/nav-user.blade.php` (line 293) routes directly to this missing view.
  - "Buy Now" in `CartController@store` (line 70) redirects to `checkout.index`, which immediately crashes.

### Feature 40: Enter Delivery Address
- **Acceptance Criteria**: Enter Delivery Address allows selecting existing or entering new address.
- **Schema & Relationships**:
  - `addresses` table: `id`, `user_id`, `type` (`home`, `work`, `other`), `full_name`, `phone`, `address_line_1`, `address_line_2`, `landmark`, `city`, `state`, `postal_code`, `country`, `latitude`, `longitude`, `is_default`.
  - Relationship: `User::hasMany(Address::class)`.
  - In `orders` table: Snapshot delivery fields: `delivery_full_name`, `delivery_phone`, `delivery_address_line_1`, `delivery_address_line_2`, `delivery_city`, `delivery_state`, `delivery_country`, `delivery_postal_code`.
- **Controller Logic**:
  - `CheckoutController@index` queries `$user->addresses()->latest()->get()`.
  - `CheckoutController@store` validates: `'address_id' => 'required|exists:addresses,id'`.
- **Defect / Discrepancy**:
  - Because `user.checkout.index` does not exist, the user cannot select an address.
  - Furthermore, `CheckoutController@store` does not support entering a new address inline during checkout (it only accepts an existing `address_id`). It should support both selecting an existing address or entering a new one that can optionally be saved to `addresses`.

### Feature 41: Select Delivery Time Slot
- **Acceptance Criteria**: Select Delivery Time Slot presents available delivery windows.
- **Schema**:
  - `orders` table has NO column for delivery time slots (e.g. `delivery_time_slot` or `preferred_delivery_slot`).
- **Controller Logic**:
  - `CheckoutController@store` validation does not include any delivery slot parameter.
- **Defect / Discrepancy**:
  - Completely missing. Requires:
    1. Schema update (migration adding `delivery_time_slot` varchar(50) nullable to `orders` table).
    2. Model update (`Order::$fillable`).
    3. Controller validation (`'delivery_time_slot' => 'nullable|string|max:50'`).
    4. Frontend selection options (e.g. Morning 8am-12pm, Afternoon 12pm-4pm, Evening 4pm-8pm, Standard Express).

### Feature 42: Select Payment Method (Cash on Delivery)
- **Acceptance Criteria**: Select Payment Method supports Cash on Delivery (COD).
- **Schema**:
  - `orders.payment_method`: enum(`'cod'`, `'card'`, `'upi'`, `'net_banking'`, `'wallet'`), default `'cod'`.
  - `orders.payment_status`: enum(`'pending'`, `'paid'`, `'failed'`, `'refunded'`), default `'pending'`.
  - `payments` table: `order_id`, `user_id`, `payment_method`, `gateway`, `transaction_id`, `amount`, `status`, `paid_at`.
- **Controller Logic**:
  - `CheckoutController@store` validates `'payment_method' => 'required|in:cod,card,upi,net_banking,wallet'`.
  - Sets `'payment_method' => $data['payment_method']` and `'payment_status' => 'pending'`.
- **Defect / Discrepancy**:
  - Missing checkout UI to choose payment method.
  - `CheckoutController@store` currently does not create a corresponding record in the `payments` table.

### Feature 43: Place Order (Multi-Seller DB Commit)
- **Acceptance Criteria**: Place Order commits order records to DB across sellers.
- **Route**: `POST /checkout` (Name: `checkout.store`)
- **Controller & Method**: `App\Http\Controllers\User\CheckoutController@store`
- **Current Implementation**:
  ```php
  $order = DB::transaction(function () use ($user, $cart, $address, $data, $subtotal, $shipping, $discount, $total, $couponId) {
      $order = Order::create([...]);
      if ($couponId) {
          CouponUsage::create([...]);
      }
      $cart->items()->delete();
      session()->forget('coupon');
      return $order;
  });
  ```
- **CRITICAL DEFECT**:
  - **Neither `SellerOrder` nor `OrderItem` records are created!**
  - Cart items have products, and products have `seller_id`.
  - The marketplace architecture requires:
    1. Grouping cart items by `$item->product->seller_id`.
    2. For each seller group:
       - Create a `SellerOrder` (`order_id`, `seller_id`, `seller_order_number`, `subtotal`, `shipping_amount`, `commission_rate`, `commission_amount`, `payout_amount`, `status` = `'placed'`).
       - For each item in that group, create `OrderItem` (`seller_order_id`, `product_id`, `product_name`, `product_image`, `sku`, `unit_price`, `quantity`, `total_price`).
    3. Decrementing product stock (`$product->decrement('stock', $item->quantity)`).
    4. Creating a `Payment` record.
  - Because this is missing, any order placed via `CheckoutController` is an orphaned shell without line items or seller allocations.

### Feature 44: View Order Summary
- **Acceptance Criteria**: View Order Summary presents order receipt breakdown.
- **Route**: `GET /checkout/success/{order}` (Name: `checkout.success`)
- **Controller & Method**: `App\Http\Controllers\User\CheckoutController@success(Order $order)`
- **Current Behavior**:
  - Validates `abort_unless($order->user_id === $user->id, 403);`.
  - Loads `$order->load(['sellerOrders.items']);`.
  - Calls `return view('user.checkout.success', compact('user', 'order'));`.
- **Defect / Discrepancy**:
  - The view file `resources/views/user/checkout/success.blade.php` **does not exist** (causes 500 error).
  - Even if rendered, `$order->sellerOrders` is empty because `store()` never created them.

### Feature 45: View Order History
- **Acceptance Criteria**: View Order History displays all past customer orders.
- **Route**: `GET /user/orders` (Name: `user.orders.index`)
- **Controller & Method**: `App\Http\Controllers\User\OrderController@index`
- **View**: `resources/views/user/account/orders/index.blade.php`
- **Features Verified**:
  - Status filter tabs: `all`, `pending`, `processing`, `completed`, `cancelled`.
  - Counts badge per status (`$statusCounts`).
  - Pagination (`paginate(10)->withQueryString()`).
  - Card rendering with order number, placed date, payment status, total amount, and link to details.
  - *Note*: An orphaned static HTML mockup exists at `resources/views/user/orders/index.blade.php`, but the actual controller points cleanly to `user.account.orders.index`.

### Feature 46: Track Order (Per-Seller)
- **Acceptance Criteria**: Track Order (Per Seller) displays shipping/courier progress per merchant.
- **Current State**:
  - Database schema has:
    - `seller_orders.status`: `['placed', 'processing', 'packed', 'shipped', 'delivered', 'cancelled', 'returned']`
    - `seller_orders.tracking_number`: `string(150)`
    - `seller_orders.shipped_at`, `delivered_at`
  - In `resources/views/user/account/orders/show.blade.php`:
    - Only iterates over items in each `sellerOrder`:
      ```blade
      @foreach($order->sellerOrders as $sellerOrder)
          @foreach($sellerOrder->items as $item)
          ...
      ```
    - **No seller information, status pill, tracking number, courier details, or progress timeline is rendered!**
  - Note: A rich tracking pipeline design was mocked in static HTML (`resources/views/user/orders/show.blade.php`), but it was never integrated into the dynamic Blade view `resources/views/user/account/orders/show.blade.php`.

### Feature 47: View Order Status
- **Acceptance Criteria**: View Order Status shows live status (`Pending`, `Processing`, `Shipped`, `Delivered`, `Cancelled`).
- **Current State**:
  - Supported on parent `orders.order_status` (`pending`, `processing`, `completed`, `cancelled`, `refunded`).
  - Displayed in `user.account.orders.index` and `show`.
  - Gap: Needs consistent synchronization between `seller_orders.status` and `orders.order_status`, and clear per-seller badge rendering.

### Feature 48: Cancel Order
- **Acceptance Criteria**: Cancel Order allows canceling orders before shipment.
- **Defect / Discrepancy**:
  - **No route exists** in `routes/web.php` for customer order cancellation.
  - **No controller method** in `OrderController` exists.
  - **No cancel button/form** in `resources/views/user/account/orders/show.blade.php` or `index.blade.php`.
  - Required Implementation:
    - Route: `POST /user/orders/{order}/cancel` (`user.orders.cancel`).
    - Controller method: `OrderController@cancel(Order $order)`:
      - Verify `$order->user_id === Auth::id()`.
      - Verify `$order->order_status === 'pending' || $order->order_status === 'processing'`. (Disallow if already `'completed'`, `'cancelled'`, or if seller orders are `'shipped'`).
      - Within `DB::transaction`:
        - Update `$order->update(['order_status' => 'cancelled'])`.
        - Update all `$order->sellerOrders()->whereNotIn('status', ['shipped', 'delivered'])->update(['status' => 'cancelled'])`.
        - Restore stock for each product in `$order->items`.
        - Void pending payouts if any.

### Feature 49: 1-Click Reorder
- **Acceptance Criteria**: Reorder populates cart with items from a previous order.
- **Defect / Discrepancy**:
  - **No route exists** in `routes/web.php` for reordering.
  - **No controller method** in `OrderController` exists.
  - **No Reorder button** in order views.
  - Required Implementation:
    - Route: `POST /user/orders/{order}/reorder` (`user.orders.reorder`).
    - Controller method: `OrderController@reorder(Order $order)`:
      - Verify `$order->user_id === Auth::id()`.
      - Fetch items from `$order->items` (or `$order->sellerOrders->flatMap->items`).
      - Retrieve or create user's cart (`$user->cart()->firstOrCreate(['user_id' => $user->id])`).
      - For each item, verify product exists and is active; add or increment quantity in `cart_items`.
      - Redirect to `route('cart.index')` with success message (`Items from order #... added to cart`).

---

## 3. Deep Dive: Module R8 — User Profile & Address Management (Features 50–54)

### Feature 50: View Profile
- **Acceptance Criteria**: View Profile displays user personal info & trust rank.
- **Route**: `GET /user/profile` (Name: `user.profile`)
- **Controller & Method**: `App\Http\Controllers\User\ProfileController@show`
- **View**: `resources/views/user/account/profile.blade.php`
- **Current Behavior**:
  - Renders user initials/avatar, full name, email, phone (masked), member since date, account status (`active`), preferred language, email verified status.
  - Renders 3 latest saved addresses.
  - Links to edit profile, change photo, security settings, address management.
- **Defect / Discrepancy**:
  - Lacks an explicit **Trust Rank** badge/display for the user (e.g. "Buyer Trust Rank: Tier-1 Verified", "Trust Score: 98%"). While sellers have a `trust_score` column in `seller_profiles`, customer trust rank needs a visible UI representation (such as computed from verified email + order completion history, or a default trust tier badge).

### Feature 51: Edit Profile (Name, Phone, Bio)
- **Acceptance Criteria**: Edit Profile allows updating name, phone, and bio.
- **Route**: `GET /user/profile/edit` (`user.profile.edit`), `PUT /user/profile` (`user.profile.update`)
- **Controller & Method**: `App\Http\Controllers\User\ProfileController@edit`, `@update`
- **View**: `resources/views/user/account/edit-profile.blade.php`
- **Current Implementation in Controller**:
  ```php
  $data = $request->validate([
      'name'               => 'required|string|max:100',
      'phone'              => ['nullable', 'string', 'max:30', Rule::unique('users')->ignore($user->id)],
      'preferred_language' => 'nullable|string|max:10',
  ]);
  $user->update($data);
  ```
- **CRITICAL DEFECT / DISCREPANCY**:
  - **`users` table DOES NOT have a `bio` column!** (Only `seller_profiles` has `bio`).
  - `ProfileController@update` does not validate or save `bio`.
  - `edit-profile.blade.php` does not have a `bio` textarea.
  - `profile.blade.php` does not display a `bio`.
  - **Fix Required**:
    1. Migration: `add_bio_to_users_table.php` (`$table->text('bio')->nullable()->after('profile_image');`).
    2. Add `'bio'` to `User::$fillable`.
    3. Add `'bio' => 'nullable|string|max:1000'` to validation in `ProfileController@update`.
    4. Add Bio `<textarea>` to `edit-profile.blade.php` and display it in `profile.blade.php`.

### Feature 52: Upload Profile Image
- **Acceptance Criteria**: Upload Profile Image updates avatar image file.
- **Route**: `POST /user/profile/photo` (Name: `user.profile.photo`)
- **Controller & Method**: `App\Http\Controllers\User\ProfileController@updatePhoto`
- **Current Behavior**:
  - Validates `profile_image` (`required|image|mimes:jpg,jpeg,png,webp|max:2048`).
  - Deletes old image file via `Storage::delete($user->profile_image)`.
  - Stores file in `public/profile-images`.
  - Updates `$user->update(['profile_image' => $path])`.
- **Status**: ✅ Fully functional and implemented.

### Feature 53: Change Password
- **Acceptance Criteria**: Change Password verifies old password and updates to new password.
- **Route**: `GET /user/security` (`user.security`), `POST /user/security/password` (`user.security.password`)
- **Controller & Method**: `App\Http\Controllers\User\SecurityController@index`, `@updatePassword`
- **View**: `resources/views/user/account/security.blade.php`
- **Current Behavior**:
  - Validates `current_password` and new `password` (`required|confirmed|min:8|mixedCase|numbers`).
  - Verifies current password using `Hash::check($request->current_password, $user->password)`.
  - Updates password using `Hash::make($request->password)`.
- **Status**: ✅ Fully functional and implemented.

### Feature 54: Manage Delivery Addresses (CRUD)
- **Acceptance Criteria**: Manage Delivery Addresses provides full CRUD for customer addresses.
- **Routes**:
  - `GET /user/addresses` (Name: `user.addresses.index`)
  - `POST /user/addresses` (Name: `user.addresses.store`)
  - `PUT /user/addresses/{address}` (Name: `user.addresses.update`)
  - `DELETE /user/addresses/{address}` (Name: `user.addresses.destroy`)
  - `POST /user/addresses/{address}/default` (Name: `user.addresses.default`)
- **Controller**: `App\Http\Controllers\User\AddressController`
- **View**: `resources/views/user/account/addresses.blade.php`
- **Current Behavior**:
  - **Create**: Working. Validates all address fields, manages `is_default` flag.
  - **Read**: Working. Displays address cards with type icon (`home`, `work`, `other`), phone, full address, and default pill.
  - **Delete**: Working. Has CSRF token and `@method('DELETE')`.
  - **Set Default**: Working. Resets other addresses to non-default, marks selected address default.
- **UI DEFECT / DISCREPANCY**:
  - **Edit/Update is missing from the view**: The controller has `update(Request $request, Address $address)` and the route is registered, but `addresses.blade.php` has NO edit button, NO edit modal, and NO inline edit form!
  - In `addresses.blade.php` line 6, `editId: null` is defined in Alpine.js `x-data`, but never used anywhere in the template. Users have no way to edit an existing address from the UI.

---

## 4. Deep Dive: Automated Test Infrastructure

### Configuration & Runner Capabilities
- **PHPUnit Version**: PHPUnit 11.5
- **Configuration File**: `phpunit.xml`
- **Database Driver for Testing**: SQLite in-memory (`:memory:`)
  - Works seamlessly with Laravel `RefreshDatabase` trait.
  - All 34 migrations execute in ~0.5s.
- **Cache / Session Drivers**: `CACHE_STORE=array`, `SESSION_DRIVER=array`.
- **Execution Speed**: 64 tests executed in **4.17s** with **922 assertions**. 100% pass rate.

### Existing Test Inventory
```
tests/
├── Feature/
│   ├── AdminChallengerVerificationTest.php (12 tests) - Admin routes auth, views 200, CSRF
│   ├── AdminHardeningTest.php              (36 tests) - DB transactions, KYC, payouts, disputes
│   ├── ChallengerStressTest.php            (15 tests) - Rollbacks, auction anti-sniping, locks
│   └── ExampleTest.php                     (1 test)   - Home page 200
├── Unit/
│   └── ExampleTest.php                     (1 test)   - true is true
└── TestCase.php
```

### Coverage Gaps for Modules R7 & R8
- **Module R7 (Checkout & Order Lifecycle)**: **0 Tests** (0% coverage)
  - No test for `CheckoutController@index` (checking empty vs populated cart).
  - No test for `CheckoutController@store` (validating address, time slot, COD, and multi-seller order splitting).
  - No test for multi-seller consignment creation (`SellerOrder` and `OrderItem` records).
  - No test for product stock decrement on order placement.
  - No test for order cancellation before shipment (`OrderController@cancel`).
  - No test for order reordering (`OrderController@reorder`).
  - No test for per-seller tracking display.
- **Module R8 (User Profile & Address Management)**: **0 Tests** (0% coverage)
  - No test for `ProfileController@show` and `ProfileController@edit`.
  - No test for `ProfileController@update` (updating name, phone, bio).
  - No test for avatar upload (`ProfileController@updatePhoto`).
  - No test for password change verification & validation (`SecurityController@updatePassword`).
  - No test for Address CRUD (`AddressController` index, store, update, delete, set default).

---

## 5. Architectural Blueprint for Implementation

To bring Modules R7 and R8 to 100% acceptance compliance, the following implementation roadmap is required:

### 1. Database Migrations
1. `add_delivery_time_slot_to_orders_table.php`:
   - Adds `delivery_time_slot` string (e.g. `'Morning (8 AM - 12 PM)'`, `'Afternoon (12 PM - 4 PM)'`, `'Evening (4 PM - 8 PM)'`) to `orders` table.
2. `add_bio_to_users_table.php`:
   - Adds `bio` text nullable to `users` table.

### 2. Routes (`routes/web.php`)
- Add order cancellation:
  - `POST /user/orders/{order}/cancel` → `OrderController::class, 'cancel'` (Name: `user.orders.cancel`)
- Add 1-click reorder:
  - `POST /user/orders/{order}/reorder` → `OrderController::class, 'reorder'` (Name: `user.orders.reorder`)

### 3. Controller Hardening
- **`CheckoutController@store`**:
  - Validate `delivery_time_slot` (`required|string|max:100`).
  - Support both existing `address_id` OR inline address creation (`full_name`, `phone`, `address_line_1`, `city`, `state`, `postal_code`).
  - Inside `DB::transaction`:
    - Create parent `Order`.
    - Group cart items by `product.seller_id`.
    - For each seller group, create `SellerOrder` with unique `seller_order_number` (e.g., `SO-YEAR-RANDOM`), itemized subtotal, and default status `'placed'`.
    - For each cart item, create `OrderItem` linked to `seller_order_id`, snapshotting title, price, SKU, image.
    - Decrement product stock: `$product->decrement('stock', $item->quantity)`.
    - Create `Payment` record with method `'cod'` and status `'pending'`.
- **`OrderController@cancel`**:
  - Ensure order belongs to user and is in `'pending'` or `'processing'` status.
  - Update `order_status` to `'cancelled'`.
  - Update seller orders to `'cancelled'`.
  - Restore product stock.
- **`OrderController@reorder`**:
  - Iterate through order items, add active products to cart, redirect to cart.
- **`ProfileController@update`**:
  - Accept and save `'bio' => 'nullable|string|max:1000'`.

### 4. Blade Views
1. **Create `resources/views/user/checkout/index.blade.php`**:
   - Modern Bazaario UI matching `layouts.user`.
   - Saved address selector + option to enter new delivery address.
   - Delivery time slot picker (Morning, Afternoon, Evening, Anytime).
   - Payment method selector with COD prominently featured.
   - Cart summary grouped by seller with item subtotals, coupon discount, and order total.
   - CSRF protected form posting to `route('checkout.store')`.
2. **Create `resources/views/user/checkout/success.blade.php`**:
   - Order receipt, order number, delivery address, time slot, COD payment note.
   - Seller-wise item breakdown.
   - Action buttons: "Track Order" and "Continue Shopping".
3. **Enhance `resources/views/user/account/orders/show.blade.php`**:
   - Add Per-Seller Consignment tracking cards with status badges and tracking numbers.
   - Add "Cancel Order" button for pending/processing orders with confirmation.
   - Add "1-Click Reorder" button.
4. **Enhance `resources/views/user/account/orders/index.blade.php`**:
   - Add "Reorder" button on order cards.
   - Add "Cancel" button if pending.
5. **Enhance `resources/views/user/account/profile.blade.php` & `edit-profile.blade.php`**:
   - Add Trust Rank badge to profile view.
   - Add `bio` textarea to edit profile form and display `bio` in profile view.
6. **Enhance `resources/views/user/account/addresses.blade.php`**:
   - Add Edit Address modal / collapsible form to satisfy full CRUD in the UI.

### 5. Automated Feature Test Suites
Create comprehensive PHPUnit tests:
- `tests/Feature/CheckoutAndOrderLifecycleTest.php` (covering Features 39–49)
- `tests/Feature/UserProfileAndAddressTest.php` (covering Features 50–54)
