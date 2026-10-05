# Forensic Audit Report: Milestone 4 (Checkout & Order Lifecycle Engine)

**Work Product**: Milestone 4 Implementation (Features 39 to 49)  
**Auditor**: `auditor_m4_a`  
**Profile**: General Project  
**Integrity Mode**: Development Mode (Authoritative source: `ORIGINAL_REQUEST.md` under `## 2026-09-29T05:45:59Z`)  
**Verdict**: **CLEAN**

---

### Phase Results

- **Static Analysis & Bypass Scan**: **PASS** — Zero `if (testing) return ...`, zero environment bypasses, zero dummy facades found across all M4 controllers, models, and views.
- **Atomic Transaction & Pessimistic Locking**: **PASS** — Verified `DB::transaction` with `Product::whereIn(...)->lockForUpdate()` in `CheckoutController@store` preventing race conditions and ensuring rollback on stock depletion.
- **Multi-Seller Sub-Order Splitting**: **PASS** — Verified authentic grouping by `seller_id`, creating individual `seller_orders` records with unique `seller_order_number`, proportional shipping allocation, commission calculation, and tracking numbers.
- **Stock Decrement Upon Checkout**: **PASS** — Empirically verified `products.stock` is decremented in real-time upon order placement (`AuditorM4EmpiricalVerificationTest::test_checkout_decrements_product_stock_authentically`).
- **Stock Restoration Upon Cancellation**: **PASS** — Empirically verified `products.stock` is restored in real-time upon order cancellation (`AuditorM4EmpiricalVerificationTest::test_order_cancellation_restores_stock_and_marks_orders_cancelled`).
- **1-Click Reorder Implementation**: **PASS** — Empirically verified `OrderController@reorder` retrieves past items, verifies active product status, caps quantity at available inventory, and populates `cart_items` (`AuditorM4EmpiricalVerificationTest::test_reorder_adds_items_to_cart_and_caps_at_current_stock`).
- **Security & Authorization Enforcement**: **PASS** — Verified cross-user isolation: customer cannot view, cancel, or reorder another customer's order (HTTP 403), nor hijack another user's delivery address.
- **Full Test Suite Execution**: **PASS** — 270/270 tests pass (1929 assertions) across the entire platform with 0 failures and 0 warnings.

---

## 1. Observation

### Target Files Inspected
1. `app/Http/Controllers/User/CheckoutController.php` (291 lines)
2. `app/Http/Controllers/User/OrderController.php` (126 lines)
3. `resources/views/user/checkout/index.blade.php` (411 lines)
4. `resources/views/user/checkout/success.blade.php` (238 lines)
5. `resources/views/user/account/orders/index.blade.php` (171 lines)
6. `resources/views/user/account/orders/show.blade.php` (357 lines)
7. `routes/web.php` (lines 50–70, 110–116)
8. `tests/Feature/CheckoutAndOrderLifecycleTest.php` (658 lines)
9. `tests/Feature/AuditorM4EmpiricalVerificationTest.php` (11 tests, 93 assertions)

### Verbatim Code Evidence

1. **Pessimistic Locking & Atomic Transaction** (`app/Http/Controllers/User/CheckoutController.php:119-129`):
   ```php
   $order = DB::transaction(function () use ($user, $cart, $address, $data, $request) {
       // Pessimistic locking on products to prevent stock race conditions
       $productIds = $cart->items->pluck('product_id')->filter()->all();
       $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

       foreach ($cart->items as $item) {
           $prod = $products->get($item->product_id);
           if (!$prod || $prod->stock < $item->quantity) {
               throw new \Exception("Product '" . ($prod ? $prod->name : 'Item') . "' does not have sufficient stock.");
           }
       }
   ```

2. **Genuine Multi-Seller Sub-Order Splitting** (`app/Http/Controllers/User/CheckoutController.php:186-220`):
   ```php
   // 2. Group items by seller_id and create SellerOrders
   $itemsBySeller = $cart->items->groupBy(function ($item) use ($products) {
       $prod = $products->get($item->product_id);
       return $prod?->seller_id ?: 0;
   });

   $sellerCount = $itemsBySeller->count();
   $sellerShipping = $sellerCount > 0 ? round($shipping / $sellerCount, 2) : 0.00;

   $sellerIndex = 1;
   foreach ($itemsBySeller as $sellerId => $sellerItems) {
       $sellerSubtotal = $sellerItems->sum(function ($item) use ($products) {
           $price = $products[$item->product_id]->price ?? $item->unit_price;
           return $price * $item->quantity;
       });

       $seller = $sellerId ? User::with('sellerProfile')->find($sellerId) : null;
       $commissionRate = $seller?->sellerProfile?->commission_rate ?? 10.00;
       $commissionAmount = round($sellerSubtotal * ($commissionRate / 100), 2);
       $payoutAmount = max(0, $sellerSubtotal - $commissionAmount);

       $sellerOrderNumber = 'SO-' . $order->id . '-' . $sellerIndex . '-' . strtoupper(Str::random(4));

       $sellerOrder = SellerOrder::create([
           'order_id'            => $order->id,
           'seller_id'           => $sellerId ?: null,
           'seller_order_number' => $sellerOrderNumber,
           'subtotal'            => $sellerSubtotal,
           'shipping_amount'     => $sellerShipping,
           'commission_rate'     => $commissionRate,
           'commission_amount'   => $commissionAmount,
           'payout_amount'       => $payoutAmount,
           'status'              => 'placed',
           'tracking_number'     => 'BZ-TRK-' . strtoupper(Str::random(8)),
       ]);
   ```

3. **Authentic Stock Decrement** (`app/Http/Controllers/User/CheckoutController.php:239-242`):
   ```php
   if ($prod) {
       $prod->decrement('stock', $item->quantity);
   }
   ```

4. **Authentic Stock Restoration on Cancellation** (`app/Http/Controllers/User/OrderController.php:61-74`):
   ```php
   DB::transaction(function () use ($order) {
       $order->update(['order_status' => 'cancelled']);
       $order->sellerOrders()->update(['status' => 'cancelled']);

       // Restore product stock for all cancelled line items
       $order->load(['sellerOrders.items']);
       foreach ($order->sellerOrders as $sellerOrder) {
           foreach ($sellerOrder->items as $item) {
               if ($item->product_id) {
                   Product::where('id', $item->product_id)->increment('stock', $item->quantity);
               }
           }
       }
   ```

5. **Reorder with Real Stock Capping** (`app/Http/Controllers/User/OrderController.php:96-114`):
   ```php
   $product = Product::find($item->product_id);
   if ($product && $product->status === 'active' && $product->stock > 0) {
       $qty = min($item->quantity, $product->stock);
       $cartItem = $cart->items()->where('product_id', $product->id)->first();
       if ($cartItem) {
           $newQty = min($cartItem->quantity + $qty, $product->stock);
           $cartItem->update([
               'quantity'   => $newQty,
               'unit_price' => $product->price,
           ]);
       } else {
           $cart->items()->create([
               'product_id' => $product->id,
               'quantity'   => $qty,
               'unit_price' => $product->price,
           ]);
       }
       $addedCount++;
   }
   ```

### Command Execution Evidence

- **Empirical Audit Test Suite Execution**:
  ```
  php artisan test tests/Feature/AuditorM4EmpiricalVerificationTest.php

     PASS  Tests\Feature\AuditorM4EmpiricalVerificationTest
    ✓ checkout decrements product stock authentically                                                              0.54s  
    ✓ multi seller cart splits into distinct seller orders with commission                                         0.04s  
    ✓ insufficient stock prevents checkout and rolls back                                                          0.03s  
    ✓ order cancellation restores stock and marks orders cancelled                                                 0.06s  
    ✓ completed order cannot be cancelled                                                                          0.05s  
    ✓ unauthorized user cannot cancel another order                                                                0.07s  
    ✓ reorder adds items to cart and caps at current stock                                                         0.03s  
    ✓ coupon discount applied and usage recorded at checkout                                                       0.04s  
    ✓ checkout supports inline new address creation                                                                0.04s  
    ✓ checkout rejects address belonging to another user                                                           0.03s  
    ✓ m4 blade views render comprehensively                                                                        0.14s  

    Tests:    11 passed (93 assertions)
    Duration: 1.25s
  ```

- **M4 Combined Test Suite**:
  ```
  php artisan test tests/Feature/CheckoutAndOrderLifecycleTest.php tests/Feature/AuditorM4EmpiricalVerificationTest.php

    Tests:    25 passed (119 assertions)
    Duration: 1.45s
  ```

- **Full Regression Test Suite**:
  ```
  php artisan test

    Tests:    270 passed (1929 assertions)
    Duration: 12.04s
  ```

---

## 2. Logic Chain

1. **Integrity Mode Context**: Under `ORIGINAL_REQUEST.md` (2026-09-29T05:45:59Z), the project operates in **development mode**. Prohibited patterns comprise hardcoded test bypasses, facade implementations with placeholder logic, and fabricated outputs.
2. **Absence of Bypasses**: Comprehensive regex and grep searches across all M4 code confirmed zero conditional test bypasses (e.g. `if (app()->environment('testing'))`), zero hardcoded constants replacing genuine business logic, and zero mock couriers.
3. **Transactional Integrity**: `CheckoutController::store` locks products with `Product::whereIn(...)->lockForUpdate()` inside `DB::transaction()`. If inventory is insufficient, an exception is thrown, rolling back all queries and redirecting back with error notifications.
4. **Multi-Seller Sub-Order Splitting**: Items are partitioned by `seller_id` to generate individual records in `seller_orders` with dynamic tracking numbers (`BZ-TRK-*`), calculated merchant commission, and payout amounts.
5. **Inventory Consistency**:
   - Placement decrements inventory: Verified that placing an order with quantity 4 on a product with stock 30 leaves exactly 26 items in `products.stock`.
   - Cancellation restores inventory: Verified that cancelling the order increments `products.stock` back from 15 to 20.
6. **1-Click Reorder Verification**: Past line items are retrieved, checked against current active listing status, capped against available inventory (`min($qty, $product->stock)`), and inserted/updated in the user's active cart.
7. **Security Guardrails**:
   - Unauthorized access is blocked by `abort_unless($order->user_id === $user->id, 403)` on `show`, `cancel`, and `reorder`.
   - Address hijacking across users is rejected by validating `user->addresses()->find($address_id)`.

---

## 3. Caveats

1. **Non-Integrity Defect in Percentage Coupon Capping**: In `CheckoutController.php:46` and `CheckoutController.php:145`, the code checks `$coupon->max_discount_amount`, whereas the database column defined in `create_coupons_table.php` is `maximum_discount_amount`. Because of this column name mismatch, percentage coupon discounts are not capped by `maximum_discount_amount` (they are capped by `$subtotal`). Fixed discount coupons work as expected. This is an application-level attribute naming defect, not a fraudulent facade or integrity bypass.
2. **Testing SQLite In-Memory Database**: Automated tests ran on SQLite in-memory database (`DB_CONNECTION=sqlite`). In SQLite, `lockForUpdate()` is accepted without syntax error, but full row-level lock concurrency behavior is enforced by SQLite's database-level write lock. In production MySQL/PostgreSQL, `lockForUpdate()` will enforce InnoDB row locks as expected.

---

## 4. Conclusion

Milestone 4 (Checkout & Order Lifecycle Engine, Features 39 to 49) passes all forensic integrity checks. The implementation contains genuine database transactions, real pessimistic locking, authentic multi-seller sub-order splitting, dynamic tracking numbers, real-time inventory decrement and restoration, and robust user authorization barriers.

**Final Binary Verdict: CLEAN**

---

## 5. Verification Method

To independently verify this verdict:

1. **Run the Independent Forensic Verification Test**:
   ```bash
   php artisan test tests/Feature/AuditorM4EmpiricalVerificationTest.php
   ```
   *Expected*: 11 passed (93 assertions).

2. **Run the Milestone 4 Feature Suite**:
   ```bash
   php artisan test tests/Feature/CheckoutAndOrderLifecycleTest.php
   ```
   *Expected*: 14 passed (26 assertions).

3. **Run the Full Platform Regression Suite**:
   ```bash
   php artisan test
   ```
   *Expected*: 270 passed (1929 assertions).

4. **Verify Absence of Test Bypasses**:
   ```bash
   rg -i "testing" app/Http/Controllers/User/CheckoutController.php app/Http/Controllers/User/OrderController.php
   ```
   *Expected*: 0 matches.
