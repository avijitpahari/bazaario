# Dispatch Assignment: Challenger M4-B (Cancellation, Stock Restoration & Reorder Stress Test)

## Mission
Empirically stress test Order Cancellation, Stock Restoration, and 1-Click Reorder (Features 45 to 49):
- Test cancellation workflow:
  - Cancel order in `pending` or `processing` status: verify status updates to `cancelled` and product stock is restored in the database.
  - Attempt to cancel an order already `shipped` or `delivered`: verify rejection.
  - Attempt to cancel another user's order: verify 403 Forbidden.
- Test 1-click reorder:
  - Reorder past order: verify items are re-added to cart with matching quantities and redirected to `/cart`.
  - Reorder when an item is now out of stock: verify graceful handling.
- Test per-seller shipment tracking numbers display.

## Authoritative Inputs
- `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md` (specifically under ## 2026-09-29T05:45:59Z)
- `c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_2\PROJECT.md`
- `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m4\handoff.md`

## Deliverables
- Run automated empirical stress tests.
- Record explicit verdict (`APPROVE` or `CHALLENGE_FAILED`) in `c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m4_b\handoff.md`.
- Report back with `send_message`.

## 2026-09-29T10:36:11Z
From: c38fdcd9-6d3a-4198-9c03-fab09802e7a6 (parent)
You are challenger_m4_b.
Your working directory is c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m4_b.
Write all your challenge code, logs, and handoffs strictly into your working directory.

Read the authoritative user request at:
c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically under ## 2026-09-29T05:45:59Z).
Read the project scope at:
c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_2\PROJECT.md
Read the worker handoff at:
c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m4\handoff.md
Read your dispatch instructions at:
c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m4_b\DISPATCH.md

Your Mission: Empirically stress test Order Cancellation, Stock Restoration, and 1-Click Reorder (Features 45 to 49).
Challenge Scenarios:
1. Order Cancellation & Stock Restoration:
   - Place an order for 5 units of product (stock decreases from 10 to 5).
   - Cancel the order via `POST /user/orders/{order}/cancel`.
   - Verify order status updates to `cancelled`, seller orders update to `cancelled`, and product stock is accurately restored to 10.
   - Attempt to cancel an order with status `shipped` or `delivered`: verify rejection.
   - Attempt to cancel another user's order: verify 403 Forbidden.
2. 1-Click Reorder:
   - Submit `POST /user/orders/{order}/reorder` for a completed past order: verify items and quantities are added to customer's cart and redirected to `/cart`.
   - Attempt reorder when an item is out of stock: verify graceful handling.
3. Tracking & Order Status:
   - Verify per-seller shipment tracking numbers and progression steps (`pending` -> `processing` -> `shipped` -> `delivered`).

Tasks:
- Run automated tests or execute empirical challenge tests.
- Record explicit verdict (APPROVE or CHALLENGE_FAILED) in `c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m4_b\handoff.md`.
- Report back with send_message when done.
