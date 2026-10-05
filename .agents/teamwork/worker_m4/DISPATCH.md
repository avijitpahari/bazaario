# Dispatch Assignment: Worker M4 (Checkout & Order Lifecycle Engine)

## Mission
Implement and harden Milestone 4: Checkout & Order Lifecycle Engine (Features 39 to 49).

## Exclusive Write Ownership
- `app/Http/Controllers/User/CheckoutController.php`
- `app/Http/Controllers/User/OrderController.php`
- `resources/views/user/checkout/index.blade.php`
- `resources/views/user/checkout/success.blade.php`
- `resources/views/user/account/orders/show.blade.php`
- `resources/views/user/account/orders/index.blade.php`
- `routes/web.php` (checkout, order cancellation, and reorder routes)

## Acceptance Criteria to Implement
1. Feature 39: Checkout flow navigates cleanly from `/checkout` with authenticated user and active cart items.
2. Feature 40: Delivery Address Selection / Creation:
   - User can choose from saved addresses or input a new delivery address directly during checkout.
3. Feature 41: Delivery Time Slot Selection:
   - User chooses delivery time slot (`Morning: 8 AM - 12 PM`, `Afternoon: 12 PM - 4 PM`, `Evening: 4 PM - 8 PM`).
4. Feature 42: Cash on Delivery (COD) Payment Option:
   - COD payment option selected and recorded with status `'pending'`.
5. Feature 43: Place Order atomic commitment:
   - Inside `DB::transaction()` with pessimistic locking (`lockForUpdate` on products):
     a. Validate and calculate final pricing, subtotal, shipping fee, discount.
     b. Create parent `Order` in `orders` table.
     c. Group items by `seller_id` and create sub-orders in `seller_orders` table (with unique seller sub-order numbers).
     d. Create line items in `order_items` table linked to both parent `order_id` and `seller_order_id`.
     e. Decrement product stock in `products` table.
     f. Create `Payment` record (`method = 'cod'`, `status = 'pending'`).
     g. Empty the user's cart.
6. Feature 44: Order Summary Breakdown:
   - Order confirmation / success page (`user/checkout/success.blade.php`) renders order number, estimated delivery date, time slot, delivery address, items breakdown, and COD instructions.
7. Feature 45: Order History lists all placed orders with statuses, dates, and order numbers (`user/account/orders/index.blade.php`).
8. Feature 46: Per-Seller Tracking displays separate shipment tracking status and courier tracking numbers for each seller in a multi-seller order (`user/account/orders/show.blade.php`).
9. Feature 47: Order Status tracking displays lifecycle progression (`pending` -> `processing` -> `shipped` -> `delivered`).
10. Feature 48: Order Cancellation:
    - `POST /user/orders/{order}/cancel` allows buyer to cancel order while status is `pending` or `processing`.
    - Automatically restores product stock for all cancelled line items.
11. Feature 49: 1-Click Reorder:
    - `POST /user/orders/{order}/reorder` adds all available items from past order into user's cart and redirects to `/cart`.

## Authoritative Inputs
- `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md` (specifically under ## 2026-09-29T05:45:59Z)
- `c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_2\PROJECT.md`
- `tests/Feature/CheckoutAndOrderLifecycleTest.php`

## Verification Requirements
- `php artisan test tests/Feature/CheckoutAndOrderLifecycleTest.php` passes 100%.
- Full regression suite `php artisan test` passes 100%.
- `php -l` on all modified PHP files.
- Report all results in `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m4\handoff.md`.

## 2026-09-29T10:25:28Z
Received dispatch to implement and harden Milestone 4: Checkout & Order Lifecycle Engine (Features 39 to 49).
Exclusive Write Ownership:
- `app/Http/Controllers/User/CheckoutController.php`
- `app/Http/Controllers/User/OrderController.php`
- `resources/views/user/checkout/index.blade.php`
- `resources/views/user/checkout/success.blade.php`
- `resources/views/user/account/orders/show.blade.php`
- `resources/views/user/account/orders/index.blade.php`
- `routes/web.php`

