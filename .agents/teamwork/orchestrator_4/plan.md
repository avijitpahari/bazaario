# Plan — Bazaario Seller Panel UI Integration (Orchestrator 4)

## Objective
Drive completion of Milestones 2 through 6 for Bazaario Seller Panel UI Integration, maintaining zero regressions and 100% test and forensic audit certification.

## Milestones Overview

### Milestone 1: Seller Onboarding & Access Control (R1)
- Status: ✅ COMPLETE & CERTIFIED (331 tests passing, Clean Forensic Audit)

### Milestone 2: Seller Dashboard & Performance Analytics (R2)
- Features: 6 KPI metrics (Total orders, gross revenue, listed products, low-stock alerts, trust score, next settlement), dynamic SVG revenue chart with period filters, segmented order pipeline progress bar, low-stock telemetry widget, trust score compliance breakdown, top products demand velocity, live wholesale auction spotlight banner with countdown timer.
- Plan:
  1. Explorers: Map stitch template `bazaario_seller_dashboard_performance/code.html`, controller design for `SellerDashboardController`, and test plan `SellerDashboardTest.php`.
  2. Worker: Implement `SellerDashboardController`, `seller/dashboard.blade.php`, wire route `/seller/dashboard`, and test suite.
  3. Gate: 2 Reviewers, 2 Challengers, 1 Forensic Auditor. Verify 100% pass and clean audit.

### Milestone 3: Product & Inventory Management (R3)
- Features: Product CRUD with custom unit types (`kg`, `dozen`, `bundle`, `litre`, `piece`, `pack`), harvest date & shelf-life expiry window, auto-flagging stale items & auto-hiding expired perishable listings, image upload dropzone, stock adjustment modal, live buyer preview simulation.
- Plan:
  1. Explorers: Map stitch templates `bazaario_my_products_catalog_management`, `bazaario_add_edit_product`, `bazaario_inventory_stock_management`.
  2. Worker: Implement `SellerProductController`, `SellerInventoryController`, views, routes, and tests.
  3. Gate: 2 Reviewers, 2 Challengers, 1 Forensic Auditor.

### Milestone 4: Order Fulfillment & Payout Management (R4)
- Features: Tenant-isolated order filtering, order status updates (`processing`, `ready_for_pickup`, `fulfilled`), assigned delivery slots display, handover verification protocol modal, transparent commission calculation (10% + cess), upcoming settlement banner, settlements ledger.
- Plan:
  1. Explorers: Map stitch templates `bazaario_my_orders_order_workspace` and `bazaario_my_payouts_commission_breakdown`.
  2. Worker: Implement `SellerOrderController`, `SellerPayoutController`, views, routes, and tests.
  3. Gate: 2 Reviewers, 2 Challengers, 1 Forensic Auditor.

### Milestone 5: Profile & Auction Management (R5)
- Features: Shop profile branding, operating harvest days scheduler, GPS Lat/Lng geofence settings, account password update, wholesale auction creation, live bids monitor, reserve price met indicator, auction cancellation guardrail policy.
- Plan:
  1. Explorers: Map stitch templates `bazaario_shop_profile_location_account_settings` and `bazaario_auction_management_live_bidding`.
  2. Worker: Implement `SellerProfileController`, `SellerAuctionController`, views, routes, and tests.
  3. Gate: 2 Reviewers, 2 Challengers, 1 Forensic Auditor.

### Milestone 6: E2E Verification, Adversarial Hardening & Regression Pass
- Features: Full multi-tier test execution (Tiers 1-5), adversarial hardening (XSS, SQLi, multi-tenant isolation, tamper resistance), full regression against all existing marketplace tests, final forensic integrity audit.
- Plan:
  1. Test Writer / Challenger: Run full E2E workload suite and Tier 5 adversarial tests.
  2. Forensic Auditor: Final whole-system integrity audit.
  3. Handoff: Compile `handoff.md` and report to Sentinel.
