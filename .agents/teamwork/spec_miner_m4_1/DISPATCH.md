## 2026-09-30T10:08:51Z
You are spec_miner_m4_1 (TypeName: teamwork_preview_spec_miner).
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m4_1

MANDATORY FIRST STEP:
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically header ## 2026-09-30T04:46:52Z) and c:\xampp\htdocs\bazaario\PROJECT.md.

Task:
Mine the following stitch templates for Milestone 4 (Order Fulfillment & Payout Management):
1. `c:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal\bazaario_my_orders_order_workspace\code.html`
2. `c:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal\bazaario_my_payouts_commission_breakdown\code.html`

Extract and document in detail:
1. Layout structure: The 2-column split orders workspace (order queue on left, detailed order inspector on right), responsive breakpoint behavior, search & filter bars.
2. Order cards, status tabs (`all`, `pending`, `processing`, `ready_for_pickup`, `fulfilled`, `cancelled`), badges, styling tokens.
3. Delivery slot display format (e.g., slot badges, fleet ID, packing countdown).
4. Handover verification protocol modal (courier verification, OTP / confirmation action, security notes).
5. Upcoming settlement banner, 4 KPI cards (Upcoming Settlement, Total Disbursed, Escrow Hold, Platform Commission Rate).
6. Transparent commission breakdown card: Gross item subtotal, 10% platform fee calculation, APMC cess calculation, net seller payout calculation.
7. Settlements ledger table: columns, transaction IDs / UTR, date, order batch reference, gross, deductions, net amount, status badges.
8. Exact HTML/Tailwind markup patterns to be used in `resources/views/seller/orders/` and `resources/views/seller/payouts/` extending `layouts.seller`.

Deliver your findings in `c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m4_1\handoff.md`.
When finished, send a brief message back to parent.
