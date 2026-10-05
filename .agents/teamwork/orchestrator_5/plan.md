# Plan — Bazaario Seller Panel UI Integration (Orchestrator 5)

## Objective
Deliver Milestone 4 (Order Fulfillment & Payout Management), Milestone 5 (Profile & Auction Management), and Milestone 6 (E2E Hardening & Full Regression) with zero test failures and full Forensic Audit certification.

## Milestone 4: Order Fulfillment & Payout Management (Features 26–33)
1. **Exploration**:
   - Dispatch `teamwork_preview_explorer` to inspect:
     * Existing routes in `routes/web.php` for `seller/orders` and `seller/payouts`
     * Controllers: `app/Http/Controllers/Seller/SellerOrderController.php`, `SellerPayoutController.php`
     * Models: `SellerOrder.php`, `Payout.php`, `Order.php`, `OrderItem.php`
     * Templates: `stitch_bazaario_seller_onboarding_portal/bazaario_my_orders_order_workspace` and `bazaario_my_payouts_commission_breakdown`
     * Existing tests in `tests/Feature/Seller/`
2. **Implementation**:
   - Dispatch `teamwork_preview_worker` to implement:
     * Multi-tenancy order isolation (`where('seller_id', Auth::guard('seller')->id())`)
     * Assigned delivery slots display (`SellerOrder.delivery_slot`)
     * 2-column split orders workspace (`resources/views/seller/orders/index.blade.php`, `show.blade.php`)
     * Order status progression (`pending`/`processing` -> `ready_for_pickup` -> `fulfilled` / `completed`)
     * Handover verification protocol modal (courier handover confirmation)
     * Transparent commission calculation: Gross - 10% Platform Fee - APMC Cess = Net Payout
     * Upcoming settlement banner & payouts ledger table (`resources/views/seller/payouts/index.blade.php`, `show.blade.php`)
     * Comprehensive feature tests in `tests/Feature/Seller/SellerOrderAndPayoutTest.php`
3. **Verification Gate**:
   - 2 independent Reviewers (`teamwork_preview_reviewer`)
   - 2 empirical Challengers (`teamwork_preview_challenger`)
   - 1 Forensic Integrity Auditor (`teamwork_preview_auditor`)
   - Gate certification upon unanimous APPROVE and CLEAN.

## Milestone 5: Profile & Auction Management (Features 34–43)
1. Exploration -> Worker Implementation -> Reviewers, Challengers, Auditor -> Gate certification.

## Milestone 6: E2E Test Suite Execution, Coverage Hardening, and Regression Testing (Features 44–46)
1. Tier 1-4 tests -> Tier 5 Adversarial tests -> Full regression pass -> Final Forensic Audit -> Complete Sentinel Handoff.
