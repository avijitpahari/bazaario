# DISPATCH: Spec Miner M2 (Dashboard UI)

## Task Description
You are `spec_miner_m2_2` working in `c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m2_2`.
Your parent is `orchestrator_4` (conversation ID: 6f703d77-7d87-49d3-b5fa-6d8efb15a7cc).

### Input Files to Inspect:
1. `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md` (specifically timestamp 2026-09-30T04:46:52Z, R2 Dashboard & Performance Analytics)
2. `c:\xampp\htdocs\bazaario\PROJECT.md` (Features 9-15 for Milestone 2)
3. `c:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal\bazaario_seller_dashboard_performance\code.html`
4. `c:\xampp\htdocs\bazaario\resources\views\layouts\seller.blade.php` (existing master seller layout)
5. `c:\xampp\htdocs\bazaario\resources\views\seller\dashboard.blade.php` (target file to be implemented)

### Objective:
Perform deep inspection of `bazaario_seller_dashboard_performance/code.html` and define the complete Blade view specification for `seller/dashboard.blade.php`.
Document:
1. All 6 KPI cards (Total Orders, Gross Revenue, Active Products, Low Stock Alerts, Seller Trust Score 94/100, Upcoming Settlement) and their HTML structure/classes.
2. SVG 7-Day Revenue bar and trend chart, metrics summary, time filter tabs (Today, 7D, 30D).
3. Order Fulfillment Pipeline segmented progress bar (Placed, Processing, Ready for Pickup, Delivered).
4. Low Stock Inventory Telemetry widget (depleted stock monitor, restock triggers).
5. Seller Trust Score breakdown card (4 compliance bars, Tier 1 Prime badge).
6. Top Products Demand Velocity table (Product name, units sold, revenue, remaining stock, star rating).
7. Active Wholesale Auction Spotlight banner (Lot #, live timer countdown, current high bid, reserve met indicator).
8. Recent Store Orders table (order ID, buyer, items, amount, status badge, action link).
9. Exact Blade variables and loops required (`$totalOrders`, `$totalRevenue`, `$activeProductsCount`, `$lowStockCount`, `$trustScore`, `$recentOrders`, `$lowStockProducts`, `$topProducts`, `$activeAuction`, `$revenueChartData`, etc.).

Write your comprehensive findings and blueprint to `c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m2_2\handoff.md`.
Send message to parent when done.

## 2026-09-30T05:41:49Z
[Message] timestamp=2026-09-30T05:41:49Z sender=6f703d77-7d87-49d3-b5fa-6d8efb15a7cc priority=MESSAGE_PRIORITY_HIGH content=You are spec_miner_m2_2 working in c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m2_2.
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically 2026-09-30T04:46:52Z, R2), c:\xampp\htdocs\bazaario\PROJECT.md (Milestone 2), and c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m2_2\DISPATCH.md.
Inspect c:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal\bazaario_seller_dashboard_performance\code.html and c:\xampp\htdocs\bazaario\resources\views\layouts\seller.blade.php.
Extract all 6 KPI cards, SVG revenue chart, order pipeline bar, low-stock monitor, trust score breakdown, top products velocity table, and live wholesale auction spotlight.
Define the exact Blade view blueprint for resources/views/seller/dashboard.blade.php with all HTML markup, Tailwind classes, and dynamic Blade expressions.
Write your complete handoff report to c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m2_2\handoff.md and send_message to parent (conversation ID: 6f703d77-7d87-49d3-b5fa-6d8efb15a7cc).
