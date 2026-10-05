# DISPATCH: Explorer M2 Tests (Test Strategy & Coverage)

## Task Description
You are `explorer_m2_tests_2` working in `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m2_tests_2`.
Your parent is `orchestrator_4` (conversation ID: 6f703d77-7d87-49d3-b5fa-6d8efb15a7cc).

### Input Files to Inspect:
1. `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md` (timestamp 2026-09-30T04:46:52Z, R2)
2. `c:\xampp\htdocs\bazaario\PROJECT.md`
3. `c:\xampp\htdocs\bazaario\tests\Feature\Seller\SellerOnboardingTest.php` (M1 test patterns)
4. `c:\xampp\htdocs\bazaario\tests\Feature\Seller\SellerIntegrityAuditCheckTest.php`

### Objective:
Design a comprehensive automated test suite for Milestone 2 (`tests/Feature/Seller/SellerDashboardTest.php`).
Map out tests across all tiers:
- Tier 1 (Feature & Happy Path):
  1. Approved seller can access `/seller/dashboard` (HTTP 200).
  2. Unapproved seller is redirected to `/seller/pending`.
  3. Unauthenticated user is redirected to login.
  4. View receives and displays accurate metrics: Total Orders count, Gross Revenue formatted in ₹, Active Products count, Low Stock count, Trust Score.
- Tier 2 (Boundary & Multi-Tenant Isolation):
  5. Zero-state: New seller with 0 orders, 0 products renders gracefully with 0s and no errors.
  6. Strict tenant isolation: Seller A cannot see orders, revenue, or low-stock alerts belonging to Seller B.
- Tier 3 (Combinatorial & Dynamic State):
  7. Placing a new seller order increments total orders and updates gross revenue.
  8. Modifying product stock to below threshold immediately increments low-stock KPI count.
  9. Active wholesale auction appears in auction spotlight card.
  10. Completed/ended auction does not show in live spotlight card.
- Tier 4 (Real-World Pipeline):
  11. Multi-status order pipeline correctly reflects distribution of orders across placed, processing, and delivered.

Write your complete test architecture specification and code blueprint to `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m2_tests_2\handoff.md`.
Send message to parent when done.

## 2026-09-30T05:41:49Z
You are explorer_m2_tests_2 working in c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m2_tests_2.
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically 2026-09-30T04:46:52Z, R2), c:\xampp\htdocs\bazaario\PROJECT.md (Milestone 2), and c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m2_tests_2\DISPATCH.md.
Inspect tests/Feature/Seller/SellerOnboardingTest.php and test patterns in the repo.
Design the complete automated test suite for Milestone 2: tests/Feature/Seller/SellerDashboardTest.php across Tiers 1-4 (access control, KPI calculation accuracy, zero-state resilience, strict multi-tenant isolation, stock triggers, and live auction spotlight).
Write your complete handoff report to c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m2_tests_2\handoff.md and send_message to parent (conversation ID: 6f703d77-7d87-49d3-b5fa-6d8efb15a7cc).

