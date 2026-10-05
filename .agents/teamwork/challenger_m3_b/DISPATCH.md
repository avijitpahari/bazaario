# Dispatch Assignment: Challenger M3-B (Cart Multi-Seller & Coupon Edge Cases)

## Mission
Empirically stress test Cart multi-seller grouping and coupon validation edge cases (Features 34 to 38):
- Challenge coupon thresholds: order subtotal exactly equal to `minimum_order_amount`, order subtotal 1 cent below, discount exceeding `maximum_discount_amount` cap, expired coupons, inactive coupons, coupons reaching `usage_limit`.
- Challenge multi-seller cart grouping: add items from 3 different sellers, verify each seller block has correct items and exact seller-wise subtotal, update quantities, remove items from one seller without affecting others.
- Verify navbar cart count accurately reflects sum of items across sellers for both guest and authenticated users.

## Authoritative Inputs
- `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md` (specifically under ## 2026-09-29T05:45:59Z)
- `c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_2\PROJECT.md`
- `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3_impl\handoff.md`

## Deliverables
- Run tests and automated challenge assertions.
- Record explicit verdict (`APPROVE` or `CHALLENGE_FAILED`) in `c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m3_b\handoff.md`.
- Report back with `send_message`.

## 2026-09-29T10:16:01Z
You are challenger_m3_b.
Your working directory is c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m3_b.
Write all your challenge code, logs, and handoffs strictly into your working directory.

Read the authoritative user request at:
c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically under ## 2026-09-29T05:45:59Z).
Read the project scope at:
c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_2\PROJECT.md
Read the worker handoff at:
c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3_impl\handoff.md
Read your dispatch instructions at:
c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m3_b\DISPATCH.md

Your Mission: Empirically stress test Multi-Seller Cart grouping and Coupon validation edge cases (Features 34 to 38).
Challenge Scenarios:
1. Multi-Seller Cart Grouping:
   - Add items from 3 distinct sellers to the cart.
   - Verify cart page groups items into 3 separate merchant blocks.
   - Verify each block displays its seller name, seller type badge, trust score, and exact merchant subtotal.
   - Update quantity of one item in seller A's block: verify seller A's subtotal updates while seller B and C remain unaffected.
   - Remove an item from seller B's block: verify only that item is deleted.
2. Coupon Validation Edge Cases:
   - Test coupon with `minimum_order_amount = 500`:
     - Cart subtotal = ₹499 -> Coupon rejected with descriptive error.
     - Cart subtotal = ₹500 -> Coupon applied successfully.
   - Test coupon with `maximum_discount_amount = 100`:
     - 50% discount on ₹1000 order -> Discount strictly capped at ₹100, not ₹500.
   - Test expired coupon -> Rejected.
   - Test coupon exceeding `usage_limit` -> Rejected.
3. Cart Badge Sync:
   - Verify navbar cart badge count reflects total items count across all sellers.

Tasks:
- Run automated tests or execute empirical challenge tests.
- Record explicit verdict (APPROVE or CHALLENGE_FAILED) in `c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m3_b\handoff.md`.
- Report back with send_message when done.

