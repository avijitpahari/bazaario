# DISPATCH: Explorer M3 Controller & Tests (Backend & Query Architecture)

## 2026-09-30T06:31:12Z
[Message] timestamp=2026-09-30T06:31:12Z sender=6f703d77-7d87-49d3-b5fa-6d8efb15a7cc priority=MESSAGE_PRIORITY_HIGH content=You are explorer_m3_controller_1 working in c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m3_controller_1.
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (timestamp 2026-09-30T04:46:52Z, R3), c:\xampp\htdocs\bazaario\PROJECT.md (Milestone 3), and c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m3_controller_1\DISPATCH.md.
Inspect Product.php, Category.php, ProductImage.php, SellerOrder.php, Auction.php, and existing seller routes.
Design app/Http/Controllers/Seller/SellerProductController.php (CRUD, image uploads, UoM types, harvest/expiry calculation, automated freshness auto-hide/flag, safe deletion guardrail, inventory stock adjustments) and tests/Feature/Seller/SellerProductManagementTest.php across Tiers 1-4.
Generate and verify proposed_SellerProductController.php and proposed_SellerProductManagementTest.php with php -l.
Write full handoff report to c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m3_controller_1\handoff.md and send_message to parent (conversation ID: 6f703d77-7d87-49d3-b5fa-6d8efb15a7cc).

## Task Description
You are `explorer_m3_controller_1` working in `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m3_controller_1`.
Your parent is `orchestrator_4` (conversation ID: 6f703d77-7d87-49d3-b5fa-6d8efb15a7cc).

### Authoritative Files to Read:
1. `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md` (timestamp 2026-09-30T04:46:52Z, R3: Product & Inventory Management)
2. `c:\xampp\htdocs\bazaario\PROJECT.md` (Milestone 3, Features 16–25)
3. Existing Models:
   - `c:\xampp\htdocs\bazaario\app\Models\Product.php` (already has `harvest_date`, `expiry_days`, `expiry_date`, `is_perishable`, `auto_hide_expired`, `farm_origin`, `harvest_grade`, `low_stock_threshold`, scopes `scopeFresh`, `scopeStale`, `scopeLowStock`, `scopePublicVisible`, `isExpired()`, `isLowStock()`)
   - `c:\xampp\htdocs\bazaario\app\Models\ProductImage.php`
   - `c:\xampp\htdocs\bazaario\app\Models\Category.php`
   - `c:\xampp\htdocs\bazaario\app\Models\SellerOrder.php`
   - `c:\xampp\htdocs\bazaario\app\Models\Auction.php`
4. Existing Tests: `tests/Feature/Seller/SellerDashboardTest.php`, `tests/Feature/Seller/SellerOnboardingTest.php`.

### Objectives:
1. Design `app/Http/Controllers/Seller/SellerProductController.php`:
   - `index(Request $request)`: List authenticated seller's products (`where('seller_id', $user->id)`) with search, category filtering, tab filtering (`all`, `active`, `low_stock`, `stale`), and freshness counts.
   - `create()`: Render `seller.products.create` with categories list and UoM options (`kg`, `dozen`, `bundle`, `litre`, `piece`, `pack`).
   - `store(Request $request)`: Server-side validation, slug auto-generation, SKU auto-generation, image uploads to `public/storage/products`, calculation of `expiry_date` if `harvest_date` + `expiry_days` provided, set `is_perishable`, set `auto_hide_expired`.
   - `edit(Product $product)`: Authorize seller ownership (`$product->seller_id === $user->id`), render `seller.products.edit`.
   - `update(Request $request, Product $product)`: Authorize ownership, update attributes and optional new images.
   - `destroy(Product $product)`: Safe deletion guardrail: reject deletion if product is part of active unfulfilled seller orders or active auctions; otherwise delete or archive safely.
   - `inventory(Request $request)`: Render `seller.products.inventory` with stock levels and restock telemetry.
   - `adjustStock(Request $request, Product $product)`: Increment/decrement/override stock with audit logging.
2. Design routes in `routes/web.php` for all product endpoints under `prefix('seller')->name('seller.')`.
3. Design complete automated test suite `tests/Feature/Seller/SellerProductManagementTest.php` across Tiers 1–4:
   - Tier 1: Product listing, creation with custom unit types (`kg`, `dozen`, `bundle`, `litre`), editing, stock update.
   - Tier 2: Boundary validation (negative price/stock, unsupported unit types), strict tenant isolation (Seller A cannot edit/delete Seller B's product), unapproved seller blocked.
   - Tier 3: Freshness engine (perishable product flagged stale within expiry window, auto-hidden when expired), safe deletion blocked on active orders/auctions.
   - Tier 4: Full agricultural product lifecycle (create fresh mango lot in `kg` with harvest date and 5-day expiry -> verify in seller catalog -> stock adjustment -> public visibility).
4. Save verified blueprints `proposed_SellerProductController.php` and `proposed_SellerProductManagementTest.php` (validated with `php -l`).
5. Write your complete handoff report to `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m3_controller_1\handoff.md` and send_message to parent.
