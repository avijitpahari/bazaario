## 2026-09-30T10:08:51Z
You are explorer_m4_tests_1 (TypeName: teamwork_preview_explorer).
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m4_tests_1

MANDATORY FIRST STEP:
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically header ## 2026-09-30T04:46:52Z) and c:\xampp\htdocs\bazaario\PROJECT.md.

Task:
Investigate the test suite, test helper traits, and test scenarios required for Milestone 4 (Order Fulfillment & Payout Management):
1. Inspect `tests/Feature/Seller/SellerTestHelperTrait.php`, `tests/Feature/Seller/SellerOnboardingTest.php`, `tests/Feature/Seller/SellerDashboardTest.php`, and `tests/Feature/Seller/SellerProductManagementTest.php`.
2. Inspect how seller authentication, approved seller profiles, sample products, parent orders, and seller orders are created in tests.
3. Design a comprehensive test matrix for `tests/Feature/Seller/SellerOrderAndPayoutTest.php`:
   - Feature 26: Strict seller data isolation (Seller A cannot view or update Seller B's orders or payouts; returns 403 or 404).
   - Feature 27: Assigned delivery slots displayed on order list & details.
   - Feature 28: 2-column split orders workspace rendered with order queue and detailed inspector.
   - Feature 29: Order status progression: valid transitions (`processing` -> `ready_for_pickup` -> `fulfilled`) succeed; invalid transitions or unauthorized transitions fail.
   - Feature 30: Handover verification protocol modal & endpoint (courier verification updates status to fulfilled / handed over).
   - Feature 31: Transparent commission breakdown: itemized deduction formula (Gross - 10% Platform fee - APMC cess = Net payout).
   - Feature 32: Upcoming settlement banner displays scheduled transfer & bank details.
   - Feature 33: Payouts ledger displays settlement history and status badges.
   - Edge cases: Empty orders state, empty payouts state, multiple seller orders under single parent order, zero-dollar amounts.
4. Detail the test methods, assertions, and factories/fixtures needed.

Deliver your test matrix specification in `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m4_tests_1\handoff.md`.
When finished, send a brief message back to parent.
