# Worker Dispatch: Milestone 6 E2E Test Suite & Adversarial Hardening

## Mission
Author and execute the comprehensive automated E2E Test Suite and Adversarial Coverage Hardening test suites for the Bazaario Seller Panel UI Integration (Features 44, 45, and 46).

## Scope & Concrete Tasks
1. **Author `tests/Feature/Seller/SellerE2EWorkloadTest.php`** (Feature 44 — Tiers 1-4 E2E Workload):
   - End-to-end multi-step seller journey:
     - Onboarding wizard submission (Farmer/Kirana/Dark Store/Individual) with geo-coordinates.
     - Redirection to `/seller/pending` waiting gate.
     - Admin approval simulation (`status = 'approved'`).
     - Dashboard operational KPIs rendering (Orders, Revenue, Products, Stock, Trust Score, Next Payout).
     - Product Catalog CRUD with custom units (`kg`, `dozen`, `bundle`, `litre`), harvest dates, shelf-life window, auto-flagging stale perishables.
     - Inventory warehouse telemetry inline stock adjustment and audit reasons.
     - Order fulfillment workflow: viewing tenant-isolated orders, assigned delivery slots, status progression (`placed` -> `processing` -> `ready_for_pickup` -> `fulfilled`), courier handover.
     - Transparent commission breakdown (10% commission, APMC cess, net payout calculation) and settlements ledger.
     - Wholesale auction creation, live bidding terminal 4 hero tiles, dynamic reserve met evaluation, live bid stream, cancellation policy enforcement.
     - Profile branding, operating harvest days JSON persistence, location radar settings, and password update.
   - Realistic multi-tenant workload scenario with realistic commerce transactions.

2. **Author `tests/Feature/Seller/SellerAdversarialHardeningTest.php`** (Feature 45 — Tier 5 Adversarial Hardening):
   - Strict tenant isolation: Seller A injecting Seller B IDs cannot access or mutate products, inventory, orders, payouts, auctions, profile, location, or password.
   - Privilege escalation defense: Guests, unapproved sellers, and regular buyers cannot access seller operational endpoints or trigger actions.
   - Boundary value and injection attacks: SQLi payloads in status filters, XSS payloads in shop bio / addresses / product descriptions safely escaped.
   - Financial and inventory guardrails: Negative prices, invalid increments, unauthorized order cancellations, stock underflows prevented.

3. **Execute Verification Tests**:
   - `php artisan test tests/Feature/Seller/SellerE2EWorkloadTest.php`
   - `php artisan test tests/Feature/Seller/SellerAdversarialHardeningTest.php`
   - `php artisan test tests/Feature/Seller/` (ensure all seller tests pass)
   - `php artisan test` (ensure full regression pass across the entire application)

4. **Update `PROJECT.md`**:
   - Update milestone status in `PROJECT.md` if appropriate or report findings.

## Mandatory Integrity Warning
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

## References
- `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md`
- `c:\xampp\htdocs\bazaario\PROJECT.md`
- `tests/Feature/Seller/SellerTestHelperTrait.php`

## Output
Write report to `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m6_test_writer\handoff.md` with complete details of tests created, commands run, and exact test results, then message orchestrator.

## 2026-10-01T06:57:57Z
You are worker_m6_test_writer (TypeName: teamwork_preview_worker).
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m6_test_writer
