# Project: Bazaario Marketplace Admin Platform

## Architecture
- **Framework**: Laravel 11.x on PHP 8.2+
- **Database**: SQLite (in-memory for automated tests, file-backed for development/staging)
- **UI Design System**:
  - Headings: **Plus Jakarta Sans**
  - Body: **Inter**
  - Currencies & IDs: **JetBrains Mono**
  - Border Radius: **14px** (`rounded-xl` configured to `0.875rem` / `14px`)
- **Authentication & Security**:
  - Guard: `admin` (`auth:admin`)
  - Middleware: `App\Http\Middleware\AdminMiddleware`
  - Strict CSRF verification on all POST/PUT/DELETE forms
  - Validation error reporting via `$errors->any()` alert banner in layout
- **Database & Concurrency Architecture**:
  - Atomic transactions via `DB::transaction()`
  - Pessimistic row locking via `lockForUpdate()` on target records across all 21 mutations
  - Eager-loading (`with(...)`, `withCount(...)`) across all 16 administrative view endpoints to eliminate N+1 queries
- **Consignment Order & Financial Flow**:
  - Two-tier order structure: `orders` (parent customer checkout) and `seller_orders` (consignment sub-orders)
  - Escrow clearinghouse with 3-tier pre-flight checks: verified bank credentials, approved KYC status, non-existence of active disputes/returns
  - Dispute arbitration with automated refund triggers and voiding of merchant payouts

## Feature Inventory
| # | Feature | Description | Milestone | Source | Status |
|---|---------|-------------|-----------|--------|:------:|
| 1 | Admin Auth & Guard Hardening | Unauthenticated admin redirects to login, strict session validation, CSRF verification | M1 | ORIGINAL_REQUEST §R1 | DONE |
| 2 | Model & Relationship Integrity | Fix Auction->seller relationship, clean ghost models, add missing models (Invoice, Payment, Notification) | M1 | survey_explorer_2 | DONE |
| 3 | Concurrency & Transaction Safety | Wrap customer placeBid in lockForUpdate, verify all 21 admin mutations in DB::transaction | M1 | survey_explorer_2 | DONE |
| 4 | Design System & Validation Feedback | Configure rounded-xl to 14px, add $errors->any() alert banner to layout, defensive number_format | M1 | survey_explorer_3 | DONE |
| 5 | Merchant Hub & KYC Queue | Review GSTIN, PAN, trade license, bank details; Approve/Reject with custom reason; commission overrides | M2 | ORIGINAL_REQUEST §R1 | DONE |
| 6 | Taxonomy & Campaigns | Categories with slug auto-generation, SKU counts, category edit modal, delete safeguards; vouchers & limits | M2 | ORIGINAL_REQUEST §R1 | DONE |
| 7 | Product Catalog & Inventory | SKU/title/merchant search, category filter, active/inactive toggle, inline stock & price update form | M3 | ORIGINAL_REQUEST §R1 | DONE |
| 8 | Multi-Seller Consignment Orders | Parent and sub-orders, status transitions (pending->processing->completed/cancelled), courier telemetry | M3 | ORIGINAL_REQUEST §R1 | DONE |
| 9 | Live Auction Terminal | Pulse monitor, anti-sniping timer, bidding ladder, reserve price evaluation, Hammer Down, Cancel Lot | M4 | ORIGINAL_REQUEST §R1 | DONE |
| 10 | Escrow Payouts & Batch Settlements | Single approval, atomic batch NEFT releases, pre-flight checks (bank IFSC, approved KYC, dispute lockouts) | M4 | ORIGINAL_REQUEST §R1 | DONE |
| 11 | Dispute Mediation Desk | Buyer damage/return claims against courier telemetry, full/partial refund, dispute dismissal, void payout | M4 | ORIGINAL_REQUEST §R1 | DONE |
| 12 | AI Hub Configuration | Gemini API credentials, model picker (1.5-flash, 1.5-pro, 2.0-flash), temperature, triage thresholds | M4 | ORIGINAL_REQUEST §R1 | DONE |
| 13 | Real-Time Telemetry & Reporting | GMV volume, commission take-rates, TDS/TCS withholdings, dynamic sidebar counts, data exports | M5 | ORIGINAL_REQUEST §R2 | DONE |
| 14 | Comprehensive E2E Testing Suite | Tier 1-4 opaque-box tests covering all 40 routes, 16 views, security, and edge cases | M6 | ORIGINAL_REQUEST Acceptance Criteria | DONE |
| 15 | Adversarial Hardening & Forensic Audit | Tier 5 white-box stress testing and forensic integrity verification | M6 | Orchestrator Governance | DONE |

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| M1 | Core Integrity, Relations & Design System | Auction model relation fix, missing models, placeBid concurrency lock, Tailwind 14px, layout validation alert | none | DONE |
| M2 | Merchant KYC & Taxonomy UI Polish | Approvals custom rejection reason UI, category edit UI modal, delete safeguards | M1 | DONE |
| M3 | Product Catalog Inline Stock & Consignment Orders | Inline stock/price update controls, consignment sub-orders and tracking telemetry | M1 | DONE |
| M4 | Financial Escrow, Auctions, Disputes & AI Hub | Batch settlements, auction hammer-down, dispute arbitration, AI settings triage rules | M2, M3 | DONE |
| M5 | Real-Time Telemetry, Analytics & Data Export | Dynamic sidebar counts verification, financial metric cards, export manifests | M4 | DONE |
| M6 | E2E Testing, Adversarial Hardening & Forensic Audit | Comprehensive automated test suite (Tiers 1-5), reviewer gate, challenger stress tests, forensic audit | M5 | DONE |

## Interface Contracts
### Admin Layout ↔ Admin Views
- `resources/views/layouts/admin.blade.php`:
  - Provides layout shell, navbar, sidebar, toast notifications for `session('success')`, `session('error')`, and `$errors->any()`.
  - Sidebar dynamically renders `$pendingKycCount` and `$liveOrdersCount`.
  - Design tokens: `font-display` (Plus Jakarta Sans), `font-sans` (Inter), `font-mono` (JetBrains Mono), `rounded-xl` (14px).

### Product Management: View ↔ Controller
- Route: `POST /admin/products/{id}/update-stock` (`admin.products.update-stock`)
- Request parameters: `stock` (integer, min:0), `price` (numeric, min:0)
- Controller: `AdminDashboardController@updateProductStock`
- Flash session: `session('success', 'Product stock and price updated successfully.')`

### Category Management: View ↔ Controller
- Route: `PUT /admin/categories/{id}` (`admin.categories.update`)
- Request parameters: `name` (string, max:255), `slug` (nullable, max:255), `description` (nullable), `is_active` (boolean)
- Controller: `AdminDashboardController@updateCategory`
- Safeguards: prevent delete when `products()->count() > 0` or `children()->count() > 0`

### Seller KYC Rejection: View ↔ Controller
- Route: `POST /admin/sellers/{id}/reject` (`admin.sellers.reject`)
- Request parameters: `reason` (string, max:1000)
- Controller: `AdminDashboardController@rejectSeller`
- Updates `seller_profiles.status = 'rejected'`, sets `rejection_reason = $reason`

## Code Layout
- `app/Http/Controllers/Admin/AdminDashboardController.php`: Admin operational domain controller
- `app/Http/Controllers/Admin/AdminAuthController.php`: Admin authentication controller
- `app/Http/Controllers/User/AuctionController.php`: User-facing auction controller with `lockForUpdate()`
- `app/Http/Controllers/AuctionController.php`: Synchronized auction controller with `lockForUpdate()`
- `app/Models/`: Eloquent models (`User`, `SellerProfile`, `Product`, `Category`, `Order`, `SellerOrder`, `Auction`, `AuctionBid`, `Payout`, `OrderReturn`, `Coupon`, `SiteSetting`, `Invoice`, `Payment`, `Notification`, `EmailOtp`)
- `resources/views/layouts/admin.blade.php`: Administrative master layout
- `resources/views/admin/`: 16 operational admin view templates
- `tests/Feature/AdminHardeningTest.php`: Feature test suite (35 tests)
- `tests/Feature/AdminChallengerVerificationTest.php`: Verification test suite (12 tests)
- `tests/Feature/ChallengerStressTest.php`: Concurrency stress test suite (15 tests)
