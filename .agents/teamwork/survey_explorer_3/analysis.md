# Comprehensive Architectural Survey & Mapping Report: Bazaario Admin Platform

**Surveyor**: `survey_explorer_3` (Blade Views, UI Design System, Telemetry, Tests)  
**Date**: September 28, 2026  
**Workspace**: `c:\xampp\htdocs\bazaario`  
**Reference Document**: `.agents/teamwork/ORIGINAL_REQUEST.md`

---

## 1. Executive Summary

This survey provides a complete architectural map of the administrative Blade templates, UI design system tokens, real-time telemetry pipelines, sidebar status badge origins, and automated test infrastructure of the **Bazaario Marketplace Admin Platform**.

Key findings:
1. **16 Administrative Views + 1 Auth View**: Exactly 16 operational admin views are rendered by `AdminDashboardController`, matching the 16 core screens validated in `AdminHardeningTest.php`. Additionally, 1 login view is rendered by `AdminAuthController`. 13 dormant 0-byte `.blade.php` stubs exist in subdirectories and are not referenced by any route or controller.
2. **Design System & Typography**: Fonts (**Plus Jakarta Sans** headings, **Inter** body, **JetBrains Mono** currencies in `₹` and IDs) are loaded via Google Fonts and integrated into Tailwind CDN configuration. However, Tailwind's `borderRadius.xl` is currently set to `0.75rem` (12px) rather than the required `14px` (`0.875rem`).
3. **Sidebar Dynamic Badge Counters**: Badge counts for pending KYC (`$pendingKycCount`) and live processing orders (`$liveOrdersCount`) are queried **directly inline via `@php` Eloquent calls** inside `resources/views/layouts/admin.blade.php`, rather than through a Laravel View Composer or controller payload.
4. **Form Redirect Error Handling Gap**: `layouts/admin.blade.php` renders flash toasts for `session('success')` and `session('error')`, but **completely lacks an `@if($errors->any())` block**. When form validation fails on any administrative mutation, Laravel redirects back with `$errors`, causing silent failures without any user-visible error alert.
5. **Functional UI Gap in Product Catalog**: The backend route `admin.products.update-stock` and controller method `updateProductStock(Request $request, $id)` are fully implemented and tested, but `resources/views/admin/products/index.blade.php` lacks an inline stock & price editing form or modal trigger to execute it.
6. **Robust Testing Infrastructure**: The test suite uses PHPUnit 11 with Laravel 11 over an in-memory SQLite database (`:memory:`). All 37 tests (187 assertions) in `AdminHardeningTest.php` pass cleanly in ~3.7 seconds. All Blade templates pass `php -l` syntax checking.

---

## 2. Administrative Blade Views Inventory & Mapping

### 2.1 The 16 Primary Administrative Views

| # | Blade View Path | Route Name | HTTP Verb & URI | Controller Action | Purpose / Operational Scope |
|---|----------------|------------|-----------------|-------------------|-----------------------------|
| 1 | `resources/views/admin/dashboard.blade.php` | `admin.dashboard` | `GET admin/dashboard` | `AdminDashboardController@dashboard` | Executive overview, 6 KPI cards, hourly GMV velocity SVG curve, recent orders table, pending KYC queue, top regional hubs, live auction pulse. |
| 2 | `resources/views/admin/sellers/index.blade.php` | `admin.sellers.index` | `GET admin/sellers` | `AdminDashboardController@sellers` | Merchant directory, regional hub and KYC filters, search, metrics, status toggling, export manifest. |
| 3 | `resources/views/admin/sellers/approvals.blade.php` | `admin.sellers.approvals` | `GET admin/sellers/approvals` | `AdminDashboardController@sellerApprovals` | Two-column KYC audit queue; submission priority queue (left); dossier with GSTIN, PAN, trade license, bank IFSC/penny-drop, FSSAI; approve/reject actions (right). |
| 4 | `resources/views/admin/sellers/show.blade.php` | `admin.sellers.show` | `GET admin/sellers/{id}` | `AdminDashboardController@sellerDetail` | Full merchant dossier, compliance, statutory credentials, custom commission tier editor, listed catalog SKUs table, suspend/reactivate toggle. |
| 5 | `resources/views/admin/products/index.blade.php` | `admin.products.index` | `GET admin/products` | `AdminDashboardController@products` | Catalog moderation, SKU search, category/sale-type filters, price in `₹`, stock indicators, active/inactive toggle, delete safeguard. |
| 6 | `resources/views/admin/categories/index.blade.php` | `admin.categories.index` | `GET admin/categories` | `AdminDashboardController@categories` | Marketplace taxonomy matrix, collapsible drawer for creating new categories, listed SKU counts, delete safeguard (locked if products or subcategories exist). |
| 7 | `resources/views/admin/orders/index.blade.php` | `admin.orders.index` | `GET admin/orders` | `AdminDashboardController@orders` | Logistics & orders directory, search, fulfillment status filter, payment status filter, multi-seller split counts, settled GMV. |
| 8 | `resources/views/admin/orders/show.blade.php` | `admin.orders.show` | `GET admin/orders/{id}` | `AdminDashboardController@orderDetail` | Consignment order dossier, carrier tracking telemetry, itemized line items with unit/total price, sub-order splits, commission breakdown, order status transition dropdown. |
| 9 | `resources/views/admin/auctions/index.blade.php` | `admin.auctions.index` | `GET admin/auctions` | `AdminDashboardController@auctions` | Live auction terminal, filter pills (All, Live Now, Hammer Down), featured lot card with anti-sniping indicator, reserve evaluation, Hammer Down & Cancel Lot actions. |
| 10 | `resources/views/admin/auctions/show.blade.php` | `admin.auctions.show` | `GET admin/auctions/{id}` | `AdminDashboardController@auctionDetail` | Lot control desk, reserve price evaluation, live bidding progression ladder table with Leader #1, bidder identity, amount in `₹`, timestamp, hammer fall button. |
| 11 | `resources/views/admin/payouts/index.blade.php` | `admin.payouts.index` | `GET admin/payouts` | `AdminDashboardController@payouts` | Escrow payouts & settlements, batch NEFT release form button (`admin.payouts.batch-release`), individual release action (`admin.payouts.release`), bank IFSC verification indicator. |
| 12 | `resources/views/admin/disputes/index.blade.php` | `admin.disputes.index` | `GET admin/disputes` | `AdminDashboardController@disputes` | Escrow dispute mediation desk, buyer claim evidence (reason, description, photo proof signal), merchant courier fulfillment telemetry, arbitration judgment buttons (Reject vs Approve Refund). |
| 13 | `resources/views/admin/customers/index.blade.php` | `admin.customers.index` | `GET admin/customers` | `AdminDashboardController@customers` | Buyer directory, search by name/email/phone, status filters, order counts, joined date, account activation/suspension toggling. |
| 14 | `resources/views/admin/customers/show.blade.php` | `admin.customers.show` | `GET admin/customers/{id}` | `AdminDashboardController@customerDetail` | Customer trust dossier, lifetime spend in `₹`, order count, verified shipping addresses, recent transactions ledger, account status toggle. |
| 15 | `resources/views/admin/coupons/index.blade.php` | `admin.coupons.index` | `GET admin/coupons` | `AdminDashboardController@coupons` | Marketing voucher campaigns, collapsible create coupon drawer (auto-uppercase code, percentage/fixed, min spend), usage tracking, toggle status, delete safeguard (locked if redeemed). |
| 16 | `resources/views/admin/settings/ai.blade.php` | `admin.settings.ai` | `GET admin/settings/ai` | `AdminDashboardController@aiSettings` | AI Engine Hub & Model Ops, Gemini API key, model selector (`gemini-1.5-flash`, `gemini-1.5-pro`, `gemini-2.0-flash`), temperature, dispute confidence threshold %, escrow cooling period, auto-triage toggle. |

### 2.2 Administrative Authentication View

| # | Blade View Path | Route Name | HTTP Verb & URI | Controller Action | Purpose / Operational Scope |
|---|----------------|------------|-----------------|-------------------|-----------------------------|
| 17 | `resources/views/admin/auth/login.blade.php` | `admin.login` | `GET admin/login` | `AdminAuthController@showLogin` | Dark slate modernist login screen, CSRF protection, demo credential auto-fill helper, password visibility toggle, validation error alert block. |

### 2.3 Dormant 0-Byte Placeholder Stubs

The following 13 files in `resources/views/admin/` have 0 bytes and are **not referenced** by any route or controller (all operations are implemented inline or via drawers on the primary views):
1. `admin/auctions/edit.blade.php`
2. `admin/categories/create.blade.php`
3. `admin/categories/edit.blade.php`
4. `admin/products/edit.blade.php`
5. `admin/products/show.blade.php`
6. `admin/sellers/edit.blade.php`
7. `admin/settings/auction.blade.php`
8. `admin/settings/commissions.blade.php`
9. `admin/settings/general.blade.php`
10. `admin/settings/index.blade.php`
11. `admin/settings/payments.blade.php`
12. `admin/settings/platform.blade.php`
13. `admin/settings/security.blade.php`

---

## 3. UI Design System, Layout & Typography Audit

### 3.1 Design System Tokens (`resources/views/layouts/admin.blade.php`)

- **Color Palette (Warm Modernist Admin)**:
  - Canvas / Background: `#FFFDF8` (Warm Ivory / Paper Cream)
  - Sidebar: `#0F172A` (Deep Authoritative Slate) with `#1E293B` borders
  - Primary Accent: `#835500` / `#F5A623` (Warm Amber Gold)
  - Status Indicators: Emerald (`#16A34A` / `bg-emerald-50` / `text-emerald-700`), Blue (`bg-blue-50` / `text-blue-700`), Amber (`bg-amber-100` / `text-amber-900`), Rose (`bg-rose-50` / `text-rose-900`)
- **Typography**:
  - Headings: **Plus Jakarta Sans** (`font-headline-lg`, `font-headline-md`, `font-headline-sm`, `font-display`).
  - Body Text: **Inter** (`font-body-lg`, `font-body-md`, `font-body-sm`).
  - Currencies & Identifiers: **JetBrains Mono** (`font-label-lg`, `font-label-md`, `font-label-sm`, `font-mono`).
  - Font inclusion in `<head>` (lines 9-12):
    ```html
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    ```
- **Border Radius Configuration & Discrepancy**:
  - In `resources/views/layouts/admin.blade.php` lines 47-53:
    ```javascript
    borderRadius: {
        "DEFAULT": "0.25rem",
        "lg": "0.5rem",
        "xl": "0.75rem", // <= Currently 12px
        "2xl": "1rem",   // <= 16px
        "full": "9999px"
    }
    ```
  - **Discrepancy**: The project specification requires `14px` border radiuses for `rounded-xl`. At `0.75rem`, `rounded-xl` compiles to `12px` (assuming 16px base font size). Changing `"xl": "0.75rem"` to `"xl": "0.875rem"` or `"xl": "14px"` will align the Tailwind runtime with the exact 14px design token.

### 3.2 Sidebar Navigation & Dynamic Status Badge Counters

In `resources/views/layouts/admin.blade.php`:
1. **Pending KYC Application Counter** (lines 144-147):
   ```blade
   @php $pendingKycCount = \App\Models\SellerProfile::where('status', 'pending')->count(); @endphp
   @if($pendingKycCount > 0)
       <span class="px-1.5 py-0.5 rounded-full bg-primary-container text-on-primary-container font-mono text-[10px] font-bold">{{ $pendingKycCount }}</span>
   @endif
   ```
2. **Active / Processing Orders Counter** (lines 177-180):
   ```blade
   @php $liveOrdersCount = \App\Models\Order::whereIn('order_status', ['pending', 'processing'])->count(); @endphp
   @if($liveOrdersCount > 0)
       <span class="px-1.5 py-0.5 rounded-full bg-[#334155] text-surface-container-lowest font-mono text-[10px]">{{ $liveOrdersCount }}</span>
   @endif
   ```
3. **Source Analysis**:
   - Neither counter comes from a Laravel `View::composer()` nor from controller parameters.
   - Both counters are executed as **inline Eloquent queries** directly inside the Blade layout template.
   - While functional, refactoring these into a `View::composer('layouts.admin', ...)` inside `AppServiceProvider` or a dedicated composer would decouple data queries from template rendering and enable optional query caching.

### 3.3 Flash Toast Notifications & Form Error Handling

In `resources/views/layouts/admin.blade.php` (lines 335-363):
- Handled Flashes:
  - `@if(session('success'))`: Renders an emerald alert card with `check_circle` icon and dismiss button.
  - `@if(session('error'))`: Renders a rose alert card with `error` icon and dismiss button.
- **Critical Gap Identified**:
  - `layouts/admin.blade.php` **does not have any handler for `$errors->any()`**!
  - When form validation fails on any administrative POST/PUT/DELETE route (e.g. invalid coupon code, invalid AI temperature, negative stock, invalid category name), Laravel automatically redirects back `withErrors(...)`.
  - Because `layouts/admin.blade.php` only inspects `session('success')` and `session('error')`, the user is returned to the page with **no error banner or toast**, making validation failures completely invisible.
  - **Proposed Fix**: Add a validation error notification block inside `layouts/admin.blade.php`:
    ```blade
    @if(isset($errors) && $errors->any())
        <div id="flash-validation" class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200/80 text-rose-900 flex items-start justify-between shadow-2xs transition-all animate-fadeIn">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-rose-600 text-[22px] mt-0.5">warning</span>
                <div>
                    <span class="font-headline-sm text-xs font-bold block">Validation Errors</span>
                    <ul class="text-xs text-rose-800 list-disc list-inside mt-1 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('flash-validation').remove()" class="text-rose-700 hover:text-rose-900 p-1 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
    @endif
    ```

---

## 4. Operational Domain Screens Evaluation

### 4.1 Merchant Hub & KYC Queue
- **Views**: `admin/sellers/index.blade.php`, `admin/sellers/approvals.blade.php`, `admin/sellers/show.blade.php`.
- **Completeness**:
  - `index.blade.php`: Multi-hub city filters (Kolkata, Shantipur, Canning, Kurseong), KYC status filters, live search across GSTIN/name/phone, metric cards, merchant directory table, instant toggle status action.
  - `approvals.blade.php`: Split 4-col queue / 8-col dossier layout. Priority sorted by submission date. Detailed inspection of GSTIN, PAN, municipality trade license, penny-drop bank verification, FSSAI certificates. One-click Approve (`admin.sellers.approve`) and Reject (`admin.sellers.reject`) with confirmation modals. Dynamic commission override form (`admin.sellers.commission`).
  - `show.blade.php`: Full merchant profile, statutory compliance cards, custom commission tier editor, listed catalog SKUs table, toggle status button. Null seller record safely handled (`@if(!$seller)`).
- **Status**: Production-ready.

### 4.2 Product Catalog & Inventory
- **View**: `admin/products/index.blade.php`.
- **Completeness**:
  - Search by SKU/title/merchant, category dropdown filter, sale type filter (Direct Purchase vs Live Auction Lot), status filter.
  - Metric cards for total SKUs, live auction lots, low stock trigger (< 5), active categories.
  - Table shows SKU, category, verified merchant badge, price in `₹`, stock level badge, status badge.
  - Row actions: view listing live, toggle active/inactive status (`admin.products.toggle-status`), delete product with confirmation (`admin.products.destroy`).
- **Gap Identified**:
  - **Inline Stock & Price Update UI is Missing**: Route `POST admin/products/{id}/update-stock` (`admin.products.update-stock`) exists in `routes/web.php` and controller method `updateProductStock()` is fully implemented with validation (`stock` integer, `price` numeric). However, `products/index.blade.php` does not provide an inline form, popover, or modal to let the admin adjust stock or price directly from the table.

### 4.3 Multi-Seller Consignment Orders
- **Views**: `admin/orders/index.blade.php`, `admin/orders/show.blade.php`.
- **Completeness**:
  - `index.blade.php`: Full order search, status filter (`pending`, `processing`, `completed`, `cancelled`, `refunded`), payment status filter, 4 KPI cards, order split breakdown displaying seller counts and merchant names.
  - `show.blade.php`: Consignment order dossier, breadcrumbs, placed date and payment method, print packing slip trigger (`window.print()`), status update dropdown form with terminal state protections.
  - Sub-order breakdowns: Sub-order ID, merchant shop name, itemized line items with unit price, quantity, total price, carrier tracking telemetry (`$so->tracking_number`), consignment payout calculation. Order summary card with subtotal, shipping, discount, and total settled amount.
- **Status**: Production-ready.

### 4.4 Live Auction Terminal
- **Views**: `admin/auctions/index.blade.php`, `admin/auctions/show.blade.php`.
- **Completeness**:
  - `index.blade.php`: Filter pills (All, Live Now, Hammer Down), KPI cards, featured active lot card with anti-sniping indicator, reserve evaluation, Hammer Down (`admin.auctions.end`) and Cancel Lot (`admin.auctions.cancel`) actions with confirmation dialogs.
  - `show.blade.php`: Lot control desk, reserve price evaluation, full bidding ladder table (rank, Leader #1 badge, bidder identity, account status, bid amount in `₹`, timestamp), cancel lot and hammer fall buttons. Null auction record safely handled (`@if(!$auction)`).
- **Status**: Production-ready.

### 4.5 Escrow Payouts & Batch Settlements
- **View**: `admin/payouts/index.blade.php`.
- **Completeness**:
  - Filter pills (All, Pending Escrow, Disbursed), batch release form button with pending settlement sum and count (`admin.payouts.batch-release`).
  - KPI cards: Disbursed to merchants, Escrow vault balance (T+3 escrow), TDS/TCS deducted 1.0% (Section 194-O), commission retained direct revenue.
  - Settlement table: Payout ID (`#PO-xxxxx`), merchant name, bank account & IFSC verification state, gross GMV, commission deducted, net payout, individual release button (`admin.payouts.release`) or settled reference & timestamp.
- **Status**: Production-ready.

### 4.6 Dispute Mediation Desk
- **View**: `admin/disputes/index.blade.php`.
- **Completeness**:
  - Filter pills (All Claims, Under Mediation, Refunded, Dismissed), 4 KPI cards.
  - Dispute case cards: Case `#DSP-xxxx`, claimant buyer name, seller merchant name, SKU, escrow refund amount in `₹`, buyer claim box (reason, description, photo proof attached signal, buyer trust score), seller fulfillment box (telemetry, courier waybill scanned signal, compliance status).
  - Arbitration action bar: "Reject Claim & Release to Seller" button (`decision=reject`) and "Approve Full Refund" button (`decision=approve`) with confirmation dialogs.
- **Status**: Production-ready.

### 4.7 Taxonomy & Campaigns
- **Views**: `admin/categories/index.blade.php`, `admin/coupons/index.blade.php`.
- **Completeness**:
  - `categories/index.blade.php`: Collapsible drawer for deploying new categories (`name`, `description`), KPI cards, taxonomy matrix table, slug identifier `/slug`, listed SKUs badge linking to filtered products, active status badge, delete safeguard (disabled with lock icon if products or subcategories exist).
  - `coupons/index.blade.php`: Collapsible drawer for creating promotional vouchers (`code`, `discount_type`, `discount_value`, `minimum_order_amount`, `usage_limit`, `expires_at`), KPI cards, coupons table, code badge, discount value in % or `₹`, min spend, usage tracking, status badge, expiry, toggle status, delete safeguard (disabled with lock icon if used).
- **Status**: Production-ready.

### 4.8 AI Hub Configuration
- **View**: `admin/settings/ai.blade.php`.
- **Completeness**:
  - Form with CSRF posting to `admin.settings.ai.update`. Header with active model pill (e.g. `GEMINI 1.5 FLASH ACTIVE`).
  - Telemetry cards: Average inference latency (142 ms), autonomous support resolution (84.6%), multilingual translation quality (4.9 / 5.0), smart recommendation lift (+22.4%).
  - Panel 1: Gemini API key password input, target foundation model select (`gemini-1.5-flash`, `gemini-1.5-pro`, `gemini-2.0-flash`), temperature slider (0.0-1.0), dispute confidence threshold % (50-100%).
  - Panel 2: Base platform commission %, escrow cooling period days, autonomous dispute auto-triage toggle, vector recommendation engine toggle.
- **Status**: Production-ready.

### 4.9 Analytics, Telemetry & Export Manifests
- **View**: `admin/dashboard.blade.php` and table views.
- **Completeness**:
  - 6 high-density KPI cards: GMV, order volume, verified merchants, registered buyers, pending KYC audits, escrow funds in transit.
  - Dual SVG chart with linear gradients: Direct retail (slate area) vs live wholesale auction bids (amber area) with Oct peak volume tooltip (`₹6,42,100`).
  - Multi-seller split orders table.
  - Action queue with pending KYC applicants.
  - Regional hubs progress bars (Kolkata Metro 78%, Contai & Digha Coastal 58%, Howrah & Midnapore 38%).
  - Live auction pulse ticker with lot numbers and live bidding prices.
  - Export capabilities: `window.print()` formatted for print/PDF export on `sellers.index`, `products.index`, `orders.index`, `orders.show` ("Print Packing Slip"), and "Download Daily Report" on `dashboard`.
- **Status**: Production-ready.

---

## 5. Potential 500 Errors, Asset Links & Resilience Review

| Component | Potential Risk / Defect | Observed State | Assessment / Recommendation |
|-----------|-------------------------|----------------|-----------------------------|
| **Image Assets** | 404 broken image links | No `<img>` tags found in any admin Blade view. | **Zero risk**. All visual elements use SVGs, Material Symbols, or CSS badges. |
| **Null Eloquent Relationships** | `Trying to get property of non-object` | Views use null-safe operator `?->` throughout (e.g. `$order->user?->name`, `$auc->product?->category?->name`). | **Zero risk**. Well guarded across all 16 views. |
| **Missing Object Records** | 500 error when ID not found | `sellers.show`, `orders.show`, `auctions.show`, `customers.show` all have `@if(!$object)` fallback templates, and `AdminDashboardController` handles missing IDs gracefully with redirects. | **Zero risk**. Fully tested in `AdminHardeningTest.php`. |
| **`number_format` Nullable Deprecations** | Passing `null` to `number_format` in PHP 8.1+ triggers deprecation or error | `admin/orders/index.blade.php:160` calls `number_format($order->total_amount, 2)` without fallback. | **Low risk**. Recommended to cast `(float)($order->total_amount ?? 0)`. |
| **Form Validation Error Display** | Admins submit invalid data and receive no feedback | `layouts/admin.blade.php` does not render `$errors->any()`. | **High UX Impact**. Form redirects back, but no toast or error alert appears. |

---

## 6. Testing Infrastructure Audit

### 6.1 Test Suite Structure

```
tests/
├── TestCase.php                     # Base test case extending Illuminate\Foundation\Testing\TestCase
├── phpunit.xml                      # PHPUnit configuration (SQLite :memory:, testing env)
├── Feature/
│   ├── AdminHardeningTest.php       # 1,281 lines, 35 comprehensive admin tests
│   └── ExampleTest.php              # Basic HTTP 200 on root route
└── Unit/
    └── ExampleTest.php              # Basic assertion
```

### 6.2 Test Environment Configuration (`phpunit.xml`)
- Database: `sqlite` with `:memory:`.
- Cache & Session: `array`.
- Queue: `sync`.
- Mail: `array`.
- Broadcast: `null`.

### 6.3 Existing Test Coverage in `AdminHardeningTest.php`
- **Security & Authorization**: Unauthenticated redirection to login, non-admin rejection, admin credential validation, admin logout, suspended admin access denial, CSRF token enforcement.
- **View Rendering (Clean 200)**:
  - `test_all_16_admin_views_render_cleanly_on_empty_database()`: Verifies that an empty database never causes a 500 exception across all 16 screens.
  - `test_all_16_admin_views_render_200_with_populated_records()`: Populates categories, products, orders, auctions, bids, payouts, disputes, coupons, and AI settings, then asserts HTTP 200 on all 16 screens.
- **Transaction Safety & Guardrails**:
  - `approveSeller` and `rejectSeller` execute in `DB::transaction`.
  - `endAuction` assigns winner atomically.
  - `releasePayout` and `batchReleasePayouts` execute atomically with rollback on exception.
  - Payout blocked if seller lacks bank account, if open dispute exists, or if order is cancelled/refunded.
  - `arbitrateDispute` updates dispute, initiates buyer refund, voids pending seller payouts, and rolls back cleanly on exception.
  - Mutation validation: Rejects negative stock/price, commission > 100%, negative coupon discounts, invalid arbitration decisions.
  - Category and coupon sanitization (HTML stripping, slug duplicates, uppercase code).
  - Deletion safeguards: Deleting categories with subcategories is blocked; deleting products with scheduled auctions is blocked.

### 6.4 Verification Commands

```powershell
# 1. Run all tests
php artisan test

# 2. Run Admin Hardening feature tests specifically
php artisan test --filter=AdminHardeningTest

# 3. Route List Validation
php artisan route:list --path=admin

# 4. Blade syntax linting (php -l)
Get-ChildItem -Path "resources/views/admin", "resources/views/layouts" -Filter "*.blade.php" -Recurse | ForEach-Object { php -l $_.FullName }
```

**Verification Results**:
- `php artisan test`: 37 passed, 187 assertions in 3.69s.
- `php -l`: All 33 Blade templates in `admin/` and `layouts/` reported `No syntax errors detected`.
- `php artisan route:list --path=admin`: All 40 administrative routes compiled with zero conflicts.

---

## 7. Recommended Action Items for Implementers

1. **Add Validation Errors Block to `layouts/admin.blade.php`**: Insert an `@if(isset($errors) && $errors->any())` flash toast container above `@yield('content')` so administrators receive visible feedback on failed form submissions.
2. **Correct Border Radius Token in `layouts/admin.blade.php`**: In `tailwind.config`, update `"xl": "0.75rem"` to `"xl": "0.875rem"` (or `"14px"`) to strictly fulfill the 14px design requirement.
3. **Implement Inline Stock & Price Update Form in `products/index.blade.php`**: Add an inline popover or small modal form in the Stock / Price columns connecting to `route('admin.products.update-stock', $product->id)` to fulfill the R1 specification for inline stock & price updates.
4. **Harden `number_format` Null Safety**: In `admin/orders/index.blade.php:160` and `admin/sellers/show.blade.php:170`, wrap values in `(float)($var ?? 0)` before calling `number_format` to prevent PHP 8.1+ deprecation warnings on null values.
5. **Add Assertions for Sidebar Badges & Form Errors to Test Suite**: Extend `AdminHardeningTest.php` with assertions verifying that `$pendingKycCount` and `$liveOrdersCount` appear in the rendered HTML, and that validation error messages are displayed upon redirect.
