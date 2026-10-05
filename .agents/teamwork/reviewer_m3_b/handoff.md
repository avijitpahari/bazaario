# Milestone 3 Independent Review & Adversarial Audit Report (Features 34 to 38)

**Reviewer**: reviewer_m3_b  
**Date**: 2026-09-29T10:21:00Z  
**Target Milestone**: Milestone 3: Cart & Multi-Seller Operations (Features 34 to 38)  
**Verdict**: **APPROVE**  

---

## 1. Observation

Direct investigation and tool execution revealed the following concrete evidence across the codebase and runtime test environments:

1. **Feature 34: Seller-Grouped Cart Items**:
   - In `app/Http/Controllers/User/CartController.php` (lines 38-41):
     ```php
     // Feature 34: Group cart items by seller
     $groupedItems = $cart->items->groupBy(function ($item) {
         return $item->product->seller_id ?? 0;
     });
     ```
   - In `resources/views/user/cart/index.blade.php` (lines 31-84):
     Iterates through `$sellerGroups as $sellerId => $sellerItems`, rendering distinct merchant blocks with shop name, seller type badge (`Farmer`, `Kirana Store`, `Dark Store`, `Individual`), seller trust score (`🛡️ {{ number_format($trustScore, 1) }}% Trust`), and seller location.

2. **Feature 35: Quantity Update Validation & Subtotal Recalculation**:
   - In `app/Http/Controllers/User/CartController.php` (lines 133-142):
     ```php
     public function update(Request $request, CartItem $cartItem)
     {
         $request->validate(['quantity' => 'required|integer|min:1|max:99']);

         abort_unless($cartItem->cart->user_id === Auth::id(), 403);

         $cartItem->update(['quantity' => $request->quantity]);

         return redirect()->route('cart.index')->with('success', 'Cart updated.');
     }
     ```
   - In `resources/views/user/cart/index.blade.php` (lines 125-134):
     Interactive quantity steppers submit `PUT /cart/{id}` with `@csrf` and `@method('PUT')`. Line item total is dynamically rendered as `₹{{ number_format(($product->price ?? 0) * $item->quantity, 2) }}`.

3. **Feature 36: Clean Item Removal**:
   - In `app/Http/Controllers/User/CartController.php` (lines 147-153):
     ```php
     public function destroy(CartItem $cartItem)
     {
         abort_unless($cartItem->cart->user_id === Auth::id(), 403);
         $cartItem->delete();

         return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
     }
     ```
   - In `resources/views/user/cart/index.blade.php` (lines 140-145):
     Dedicated removal form submits `DELETE /cart/{id}` with `@csrf` and `@method('DELETE')`. Empty cart gracefully falls back to empty state banner with "Start Shopping" CTA (lines 231-244).

4. **Feature 37: Seller-Wise Subtotal Breakdown**:
   - In `app/Http/Controllers/User/CartController.php` (lines 43-46):
     ```php
     // Feature 37: Seller-wise Subtotals
     foreach ($groupedItems as $sellerId => $items) {
         $sellerSubtotals[$sellerId] = (float) $items->sum(fn ($i) => ($i->product->price ?? 0) * $i->quantity);
     }
     ```
   - In `resources/views/user/cart/index.blade.php` (lines 151-155):
     Renders `Merchant Consignment Subtotal: ₹{{ number_format($merchantSubtotal, 2) }}` at the bottom of each merchant block.

5. **Feature 38: Coupon Validation Engine & Financial Guardrails**:
   - In `app/Http/Controllers/User/CartController.php`:
     - `applyCoupon` (lines 158-193):
       - Validates code parameter: `'coupon_code' => 'required|string'`.
       - Rejects nonexistent or inactive coupons: `$coupon->status !== 'active'`.
       - Rejects expired coupons: `$coupon->expires_at && $coupon->expires_at <= now()`.
       - Rejects future coupons: `$coupon->starts_at && $coupon->starts_at > now()`.
       - Rejects exhausted coupons: `$coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit`.
       - Enforces minimum order amount check on active cart: `$subtotal < $coupon->minimum_order_amount`.
     - `index` (lines 48-73):
       - Re-evaluates coupon eligibility on every render. If coupon expired/became inactive, purges via `session()->forget('coupon')`.
       - If cart subtotal drops below `minimum_order_amount`, sets `$discount = 0`.
       - Computes percentage (`$subtotal * $coupon->discount_value / 100`) or fixed discount.
       - Enforces `maximum_discount_amount` cap if configured.
       - Restricts discount from exceeding subtotal: `$discount = min($discount, $subtotal)`.
       - Restricts order total from dropping below zero: `$total = max(0, $subtotal + $shipping - $discount)`.

6. **Automated Verification Execution**:
   - Command: `php artisan test tests/Feature/ProductDetailAndCartTest.php`
     Result: `Tests: 26 passed (63 assertions), Duration: 21.16s`
   - Command: `php artisan test`
     Result: `Tests: 246 passed (1733 assertions), Duration: 39.02s`
   - Zero test failures, zero syntax errors, zero regressions across M1, M2, and M3.

---

## 2. Logic Chain

1. **Multi-Seller Grouping & Presentation (Feature 34 & 37)**:
   - In `CartController@index`, cart items are grouped by `product.seller_id`.
   - In `cart/index.blade.php`, merchant blocks cleanly present each seller's inventory separately, displaying seller shop name, seller type badge, trust score badge, item rows, and seller consignment subtotal.
   - This directly fulfills Acceptance Criteria for Features 34 and 37.

2. **Cart Mutation Integrity & Security Isolation (Feature 35 & 36)**:
   - In `CartController@update` and `CartController@destroy`, the authorization check `abort_unless($cartItem->cart->user_id === Auth::id(), 403)` ensures complete user isolation. No authenticated buyer can mutate or delete items from another buyer's cart.
   - Validation rule `min:1|max:99` prohibits negative or zero quantities as well as integer overflow attacks.
   - Deletion removes records cleanly from the database and updates navbar item badge counters.
   - This satisfies Acceptance Criteria for Features 35 and 36.

3. **Coupon Engine Robustness & Overflow/Underflow Defense (Feature 38)**:
   - Coupon validation is dual-layered: pre-validation occurs during application (`applyCoupon`), and continuous validation occurs on cart rendering (`index`).
   - If cart contents change (e.g., items removed, reducing subtotal below `minimum_order_amount`), the discount is adjusted dynamically.
   - Caps on `maximum_discount_amount`, boundary clamp `min($discount, $subtotal)`, and `max(0, $subtotal + $shipping - $discount)` prevent negative totals and mathematical underflow.
   - This satisfies Acceptance Criteria for Feature 38.

4. **Integrity & Authenticity Assessment**:
   - No mock stubs, hardcoded test results, or facades were found in `CartController.php` or `resources/views/user/cart/index.blade.php`.
   - The test suite `ProductDetailAndCartTest` exercises genuine HTTP requests and asserts database state transitions (`assertDatabaseHas`, `assertDatabaseMissing`, `assertStatus(403)`).
   - Full regression suite passes cleanly (246/246).

---

## 3. Caveats

- **No Caveats**: All five assigned features (34 through 38) and their associated security and arithmetic boundaries operate as specified in `ORIGINAL_REQUEST.md`. No regressions exist.

---

## 4. Conclusion

**Verdict: APPROVE**

The Cart & Multi-Seller Operations implementation (Features 34–38) meets all acceptance criteria:
- Features 34 & 37: Items are cleanly organized by seller with individual merchant consignment subtotals.
- Features 35 & 36: Item quantities update and items delete with strict user isolation (403 on IDOR) and numeric validation bounds.
- Feature 38: Coupon code engine validates active status, date validity, usage caps, minimum order amounts, and maximum discount caps with overflow protection.
- 100% test pass rate across 246 unit, feature, and end-to-end workload tests.

---

## 5. Verification Method

To independently verify this verdict:

1. **Verify Cart Feature Tests**:
   ```powershell
   php artisan test tests/Feature/ProductDetailAndCartTest.php
   ```
   *Expected outcome*: 26 passed (63 assertions).

2. **Verify Full Marketplace Test Suite**:
   ```powershell
   php artisan test
   ```
   *Expected outcome*: 246 passed (1733 assertions).

3. **Verify Code Integrity**:
   - Inspect `app/Http/Controllers/User/CartController.php`: confirm lines 38-76 (grouping, subtotals, coupon engine), line 137 (403 user isolation on update), line 149 (403 user isolation on destroy), and line 186 (minimum order amount check).
   - Inspect `resources/views/user/cart/index.blade.php`: confirm merchant blocks, seller type badges, trust badges, and subtotal per seller block.
