## 2026-09-30T04:48:14Z
From: parent (26364ffc-7fb7-4878-8526-d74c7c0ab55a)
To: orchestrator_3

You are the PROJECT ORCHESTRATOR for Bazaario Seller Panel UI Integration.
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_3

The user's authoritative request has been recorded in:
c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (under header ## 2026-09-30T04:46:52Z).

Summary of Requirements:
Build a complete, responsive, dynamic Seller Panel UI for Bazaario integrating stitch templates located at C:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal.
Working directory: c:\xampp\htdocs\bazaario
Integrity mode: development

Requirements:
- R1. Seller Onboarding Module: Step-by-step guided onboarding wizard allowing selection of seller type (Farmer, Kirana Store, Dark Store, Individual); collect shop/farm details and geo-location (Lat/Lng); pending approval state ("Await Admin Approval") before granting access to seller dashboard.
- R2. Seller Dashboard & Performance Analytics: Display total orders, revenue, listed products, low-stock alerts, and own trust score; dynamic data binding connected to Laravel models/views with real-time stats.
- R3. Product & Inventory Management: Add, edit, delete products with unit types (kg, dozen, bundle, litre); set harvest date / expiry window and auto-flag/hide stale perishable listings; product image upload and stock quantity management.
- R4. Order & Payout Management: Filter orders to view own orders only, update order status, view assigned delivery slots, and mark orders as fulfilled; transparent commission rate breakdown and payout summary view.
- R5. Profile & Auction Management: Manage shop profile, edit location (Lat/Lng), and update password; create auctions (starting price, reserve price, minimum increment, start/end time), monitor live bids, highest bid indicator, and cancel/manage eligible auctions.

Acceptance Criteria:
- Seller Onboarding & Access Control: Onboarding wizard correctly captures seller type and redirects pending sellers to the approval waiting page; non-approved sellers cannot access dashboard until status is approved by admin.
- Dashboard & Analytics: Dashboard displays accurate dynamic order counts, revenue figures, product counts, and low-stock alerts.
- Product & Auction Capabilities: Sellers can add products with custom unit types (kg, dozen, bundle, litre) and harvest dates; perishable items past expiry window are automatically hidden or flagged; auctions can be created with reserve prices, start/end timestamps, and live bid monitoring.
- Order Fulfillment & Payouts: Order status updates (Processing, Out for Delivery, Fulfilled) correctly persist and reflect assigned delivery slots; payout module accurately calculates net seller payout minus platform commission.

Your Responsibilities:
1. Initialize your BRIEFING.md, plan.md, and progress.md in your working directory (c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_3).
2. Decompose the mission into clear milestones/workstreams.
3. Spawn specialized subagents (explorers, implementers/workers, reviewers, challengers, testers) to perform the codebase exploration, view and controller implementations, data binding, routes, and comprehensive automated test suites.
4. Maintain tight lifecycle tracking, quality gates, and automated test regression passes for all seller panel features and existing marketplace tests.
5. Continuously update your progress.md.
6. When all requirements and acceptance criteria are fully met and verified with passing test suites, compile handoff.md and send a completion message to the Sentinel.
