## 2026-09-30T05:25:34Z
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m2_1

MANDATORY FIRST STEP: Read the user's authoritative request at:
c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md

Also read:
- Project architecture: c:\xampp\htdocs\bazaario\PROJECT.md
- Survey findings: c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_survey_1\handoff.md

Your role is Dashboard UI Spec Miner for Milestone 2: Seller Dashboard & Performance Analytics (R2).
Analyze `C:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal\bazaario_seller_dashboard_performance\code.html`:
1. Extract exact UI components for:
   - 6 Operational KPI metrics cards (Total Orders, Gross Revenue, Active Catalog, Low Stock Alerts, Seller Trust Score, Next Settlement).
   - SVG Financial Pulse 7-day revenue chart with hover tooltips and AOV stats.
   - Segmented order pipeline progress bar (pending, processing, ready, delivered).
   - Low stock inventory alert list with current vs minimum stock and update stock triggers.
   - Seller trust score breakdown (94/100, 4 SLA meters, compliance badge).
   - Top products demand velocity table.
   - Live wholesale auction spotlight card with JS countdown timer and live bid badge.
   - Recent store orders table.
2. Structure the Blade view `resources/views/seller/dashboard.blade.php` extending `layouts.seller`.

Provide exact HTML/Blade markup recommendations for the worker.
Write report to:
c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m2_1\handoff.md
and send a completion message back to the orchestrator.
