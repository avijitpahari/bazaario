# DISPATCH: Spec Miner M3 (Product & Inventory Stitch UI)

## Task Description
You are `spec_miner_m3_1` working in `c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m3_1`.
Your parent is `orchestrator_4` (conversation ID: 6f703d77-7d87-49d3-b5fa-6d8efb15a7cc).

### Authoritative Files to Read:
1. `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md` (timestamp 2026-09-30T04:46:52Z, R3: Product & Inventory Management)
2. `c:\xampp\htdocs\bazaario\PROJECT.md` (Milestone 3, Features 16–25)
3. `c:\xampp\htdocs\bazaario\resources\views\layouts\seller.blade.php` (master layout)
4. Source Stitch Templates:
   - `c:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal\bazaario_my_products_catalog_management\code.html`
   - `c:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal\bazaario_add_edit_product\code.html`
   - `c:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal\bazaario_inventory_stock_management\code.html`

### Objectives:
1. Deep-dive into the 3 stitch templates and extract exact HTML, Tailwind classes, and Blade data bindings for:
   - `resources/views/seller/products/index.blade.php`: Catalog master table, search & filter toolbar, status tabs (All, Active, Drafts, Low Stock, Out of Stock, Stale), automated freshness engine banner, inspect drawer, delete modal.
   - `resources/views/seller/products/create.blade.php`: Add Product workstation (5 sections: Basic details, Pricing & 6 UoM buttons (`kg`, `dozen`, `bundle`, `litre`, `piece`, `pack`), Inventory stock, Agronomic ledger with harvest date and shelf-life window days, Media upload mosaic, and live buyer view simulation card).
   - `resources/views/seller/products/edit.blade.php`: Edit Product workstation pre-filled with existing model attributes.
   - `resources/views/seller/products/inventory.blade.php`: Warehouse stock telemetry table with inline quick-adjustment steppers (+/-) and Quick Restock / Stock Adjustment modal.
2. Provide copy-paste ready Blade blueprints extending `layouts.seller` with defensive `@php` fallbacks for missing variables to guarantee zero 500 errors.
3. Write your complete handoff report to `c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m3_1\handoff.md` and send_message to parent.

## 2026-09-30T06:31:12Z
You are spec_miner_m3_1 working in c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m3_1.
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (timestamp 2026-09-30T04:46:52Z, R3), c:\xampp\htdocs\bazaario\PROJECT.md (Milestone 3), and c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m3_1\DISPATCH.md.
Inspect stitch templates:
- stitch_bazaario_seller_onboarding_portal/bazaario_my_products_catalog_management/code.html
- stitch_bazaario_seller_onboarding_portal/bazaario_add_edit_product/code.html
- stitch_bazaario_seller_onboarding_portal/bazaario_inventory_stock_management/code.html
and master layout resources/views/layouts/seller.blade.php.
Formulate production-ready Blade blueprints for:
- resources/views/seller/products/index.blade.php
- resources/views/seller/products/create.blade.php
- resources/views/seller/products/edit.blade.php
- resources/views/seller/products/inventory.blade.php
Ensure all UoM buttons (kg, dozen, bundle, litre, piece, pack), harvest dates, shelf life window, automated freshness banner, live simulation card, stock steppers, and restock modal are included.
Write full handoff report to c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m3_1\handoff.md and send_message to parent (conversation ID: 6f703d77-7d87-49d3-b5fa-6d8efb15a7cc).

