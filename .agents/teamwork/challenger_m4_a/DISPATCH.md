# Dispatch Assignment: Challenger M4-A (Checkout & Multi-Seller Split Stress Test)

## Mission
Empirically stress test Checkout, Multi-Seller Order creation, and Stock decrement logic (Features 39 to 44):
- Test order placement with items from multiple distinct sellers:
  - Verify exactly 1 parent `Order` is created.
  - Verify exactly N `SellerOrder` records are created (one per distinct seller) with unique seller order numbers.
  - Verify `OrderItem` records are correctly associated with both parent order and corresponding seller order.
- Test stock decrement: verify product stock is reduced exactly by ordered quantity.
- Test concurrency / out-of-stock boundary: attempt to place order when quantity exceeds available stock (verify graceful rejection without partial writes).
- Test address creation on the fly vs selecting saved address.

## Authoritative Inputs
- `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md` (specifically under ## 2026-09-29T05:45:59Z)
- `c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_2\PROJECT.md`
- `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m4\handoff.md`

## Deliverables
- Run automated empirical stress tests.
- Record explicit verdict (`APPROVE` or `CHALLENGE_FAILED`) in `c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m4_a\handoff.md`.
- Report back with `send_message`.

## 2026-09-29T10:36:11Z
You are challenger_m4_a.
Your working directory is c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m4_a.
Write all your challenge code, logs, and handoffs strictly into your working directory.

Read the authoritative user request at:
c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically under ## 2026-09-29T05:45:59Z).
Read the project scope at:
c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_2\PROJECT.md
Read the worker handoff at:
c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m4\handoff.md
Read your dispatch instructions at:
c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m4_a\DISPATCH.md

Your Mission: Empirically stress test Checkout, Multi-Seller Order creation, and Stock decrement logic (Features 39 to 44).
Challenge Scenarios:
1. Multi-Seller Order Placement:
   - Create cart with products from 3 distinct sellers.
   - Execute checkout commit: verify 1 parent `Order`, exactly 3 `SellerOrder` records with unique seller order numbers, and all `OrderItem` records linked properly.
2. Stock Decrement & Concurrency:
   - Check stock before and after placement: verify exact decrement by ordered quantity.
   - Attempt checkout with quantity > stock: verify rejection and transaction rollback.
3. Delivery Address & Slots:
   - Test inline new address creation vs saved address selection.
   - Test time slot persistence and rendering on success page.

Tasks:
- Run automated tests or execute empirical challenge tests.
- Record explicit verdict (APPROVE or CHALLENGE_FAILED) in `c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m4_a\handoff.md`.
- Report back with send_message when done.

