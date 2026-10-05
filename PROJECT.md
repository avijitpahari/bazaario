# Project: Bazaario Seller Panel UI Integration

## Architecture
- **Framework & Backend**: Laravel 11, PHP 8.2, SQLite `:memory:` for testing, MySQL for development.
- **Session Guards**: `auth:seller` guarding `/seller/*` routes, mapping to `User` with `role = 'seller'`.
- **Approval Gate**: `SellerMiddleware` / `EnsureSellerApproved` preventing pending or non-approved sellers from accessing `/seller/dashboard` and operational routes; unapproved sellers are redirected to `/seller/pending`.
- **UI Design System**: Warm Modernist Commerce (`warm_modernist_commerce/DESIGN.md`) integrated from stitch templates in `C:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal`:
  - Canvas: `#FFFDF8`, Elevated Surface: `#FFFFFF`, Slate: `#0F172A`, Amber: `#F5A623`, Success: `#16A34A`, Error: `#BA1A1A`.
  - Typography: Space Grotesk (headings/display), Inter (body/forms), JetBrains Mono (metrics, prices, codes).
  - Radii: 14px on containers/cards/inputs/buttons (`rounded-[14px]`); 6px on badges/chips (`rounded-[6px]`). No pill buttons.
  - Icons: Google Material Symbols Outlined. Reactive behavior: Alpine.js 3.x.
- **Dual Layout Structure**:
  - `layouts.seller`: Operating workspace with fixed 72-unit sidebar, topbar, navigation pills, flash alerts, and content area.
  - `layouts.seller-onboarding`: Distraction-free container for wizard and approval gate.

## Code Layout
- **Database Migrations**:
  - `database/migrations/2026_09_30_000001_add_seller_panel_fields_to_tables.php` (adds harvest/expiry fields to `products`, delivery slot to `seller_orders`, and address/radius to `seller_profiles`).
- **Models**:
  - `app/Models/SellerProfile.php` (extended fillables, helper scopes).
  - `app/Models/Product.php` (harvest_date, expiry_days, expiry_date, is_perishable, auto_hide_expired, low_stock_threshold, scopeFresh(), scopeStale(), isExpired()).
  - `app/Models/SellerOrder.php` (delivery_slot accessor, status transitions).
  - `app/Models/Auction.php` (foreign key to seller_profiles, live bids logic, reserve met evaluator, cancellation guard).
  - `app/Models/Payout.php` (net payout calculation, commission breakdown).
- **Controllers (`app/Http/Controllers/Seller/`)**:
  - `SellerOnboardingController.php`
  - `SellerDashboardController.php`
  - `SellerProductController.php`
  - `SellerOrderController.php`
  - `SellerPayoutController.php`
  - `SellerAuctionController.php`
  - `SellerProfileController.php`
- **Middleware**:
  - `app/Http/Middleware/SellerMiddleware.php` (updated with approval enforcement).
- **Views (`resources/views/`)**:
  - `layouts/seller.blade.php`
  - `layouts/seller-onboarding.blade.php`
  - `seller/onboarding/wizard.blade.php`
  - `seller/pending.blade.php`
  - `seller/dashboard.blade.php`
  - `seller/products/index.blade.php`, `create.blade.php`, `edit.blade.php`, `inventory.blade.php`, `show.blade.php`
  - `seller/orders/index.blade.php`, `show.blade.php`
  - `seller/payouts/index.blade.php`, `show.blade.php`
  - `seller/auctions/index.blade.php`, `create.blade.php`, `live.blade.php`, `show.blade.php`
  - `seller/account/profile.blade.php`, `location.blade.php`, `security.blade.php`
- **Tests (`tests/Feature/Seller/`)**:
  - `SellerTestHelperTrait.php`
  - `SellerOnboardingTest.php`
  - `SellerDashboardTest.php`
  - `SellerProductManagementTest.php`
  - `SellerOrderAndPayoutTest.php`
  - `SellerAuctionAndProfileTest.php`
  - `SellerE2EWorkloadTest.php`
  - `SellerAdversarialHardeningTest.php`

## Feature Inventory
| # | Feature | Description | Milestone | Source |
|---|---------|-------------|-----------|--------|
| 1 | Database Schema Extensions | Add harvest_date, expiry_days, expiry_date, is_perishable, auto_hide_expired, low_stock_threshold to products; delivery_slot to seller_orders; address & radius to seller_profiles | M1 | survey |
| 2 | Master & Onboarding Layouts | Implement `layouts.seller` and `layouts.seller-onboarding` with Warm Modernist design tokens, fonts, icons | M1 | survey |
| 3 | Seller Approval Gate Middleware | Enforce approval check in `SellerMiddleware`, redirect pending sellers to `/seller/pending` | M1 | survey |
| 4 | Onboarding Account & Seller Type Wizard | Multi-step wizard capturing seller types: Farmer, Kirana Store, Dark Store, Individual | M1 | survey |
| 5 | Shop & Farm Details Capture | Form capturing commercial and agronomic shop details with storefront image upload | M1 | survey |
| 6 | Geolocation Lat/Lng Capture | Address fields, browser GPS detection, manual Lat/Lng coordinate overrides with radar preview | M1 | survey |
| 7 | Application Review & Submission | Consolidated review matrix with 1-click edit jumps, submission transitioning seller to pending | M1 | survey |
| 8 | Await Admin Approval Waiting Gate | Dedicated status page (`/seller/pending`) displaying review dossier, timeline, and help channels | M1 | survey |
| 9 | Operational Dashboard KPI Metrics | 6 metrics cards: Total Orders, Gross Revenue, Active Products, Low Stock Alerts, Trust Score, Next Payout | M2 | survey |
| 10 | Revenue & Trend Analytics Visualization | Financial pulse revenue bar/trend visualizer with time filters (Today, 7D, 30D) | M2 | survey |
| 11 | Order Fulfillment Pipeline Bar | Segmented fulfillment progress bar displaying distribution across order stages | M2 | survey |
| 12 | Low Stock Telemetry Widget | Depleted stock monitor highlighting items below threshold with restock triggers | M2 | survey |
| 13 | Seller Trust Score Breakdown | Compliance card showing trust score (e.g. 94/100), SLA metrics, and rating indicators | M2 | survey |
| 14 | Top Products Demand Velocity | Table ranking top revenue and unit driving products for current period | M2 | survey |
| 15 | Live Wholesale Auction Spotlight | Dashboard banner highlighting active lot with countdown timer and bid status | M2 | survey |
| 16 | Product Catalog Master Table | Searchable, filterable catalog table with SKU, category, price, stock, freshness tags | M3 | survey |
| 17 | Custom Unit Types (UoM) Support | Product pricing & inventory supporting `kg`, `dozen`, `bundle`, `litre`, `piece`, `pack` | M3 | survey |
| 18 | Add / Edit Product Workstation | Full 5-section product creation & edit forms with slug/SKU auto-generation | M3 | survey |
| 19 | Product Image Upload Dropzone | Multiple product image upload, primary image selection, preview mosaic | M3 | survey |
| 20 | Agronomic Ledger & Freshness Window | Form fields for harvest date, shelf-life window days, calculation of expiry timestamp | M3 | survey |
| 21 | Automated Freshness Engine | Auto-flag stale perishable items and auto-hide expired listings from public catalog | M3 | survey |
| 22 | Inventory Stock Telemetry Table | High-speed warehouse table with inline quick-adjustment steppers (+/-) | M3 | survey |
| 23 | Quick Restock / Adjustment Modal | Stock modal supporting Add, Remove, Direct Override with audit reason logging | M3 | survey |
| 24 | Safe Product Deletion Guardrail | Product deletion protected against active unfulfilled orders or live auctions | M3 | survey |
| 25 | Live Buyer View Simulation Card | Synchronized preview card showing live customer-facing presentation | M3 | survey |
| 26 | Seller Data Isolation Guardrail | Strict tenancy isolation ensuring sellers only query/mutate their own orders | M4 | survey |
| 27 | Assigned Delivery Slots Display | Logistics card displaying assigned courier pickup window, fleet ID, and packing countdown | M4 | survey |
| 28 | 2-Column Split Orders Workspace | Order queue table on left + detailed order inspector on right with customer & item info | M4 | survey |
| 29 | Order Status Progression Workflow | Lifecycle transitions: placed -> processing -> ready_for_pickup -> fulfilled | M4 | survey |
| 30 | Handover Verification Protocol | Confirmation modal to verify physical custody transfer to delivery courier | M4 | survey |
| 31 | Transparent Commission Calculator | Itemized deduction ledger: Gross - 10% Platform Commission - APMC Cess = Net Payout | M4 | survey |
| 32 | Upcoming Settlement Banner | Prominent financial banner displaying next scheduled NEFT bank transfer and bank details | M4 | survey |
| 33 | Payouts Ledger & Settlements Table | Complete historical log of settlements with UTR tracking, order batch links, and status | M4 | survey |
| 34 | Shop Profile & Storefront Branding | Visual store profile managing cover banner, logo, business details, verified seller tag | M5 | survey |
| 35 | Operating Harvest Days & SLA Scheduler | Day-of-week operating selector, order cutoff time, and courier dispatch window settings | M5 | survey |
| 36 | Location Telemetry & Geofence Settings | Farm origin address, Lat/Lng coordinates, elevation, geofence radius map preview | M5 | survey |
| 37 | Account Security & Password Update | Password change form with current password verification and 4-tier complexity meter | M5 | survey |
| 38 | Wholesale Bidding Live Terminal | Real-time monitoring terminal for wholesale lots with countdown clock and hero tiles | M5 | survey |
| 39 | Reserve Price Met Indicator | Dynamic badge indicating whether current bid has met or exceeded seller's secret reserve | M5 | survey |
| 40 | Create Auction Listing Form | Auction listing creation with product selection, starting price, reserve, min increment, times | M5 | survey |
| 41 | Anonymized Live Bid Activity Stream | Privacy-preserving bidding log showing real-time bid progression | M5 | survey |
| 42 | Auction Cancellation Guardrail Policy | Strict cancellation policy: permitted with 0 bids, strictly blocked once bids/reserve met | M5 | survey |
| 43 | Master Auctions Registry Table | Historical overview of all seller auctions across scheduled, live, completed, cancelled | M5 | survey |
| 44 | Comprehensive Automated E2E Test Suite | Full multi-tier test suite (Tiers 1-4) in `tests/Feature/Seller/` | M6 | survey |
| 45 | Adversarial Coverage Hardening | Tier 5 adversarial tests for XSS, SQLi, privilege escalation, cross-tenant isolation | M6 | survey |
| 46 | Full Marketplace Regression Pass | Ensure 100% pass across all 278 baseline tests and zero regression | M6 | survey |

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| 1 | M1: Foundation, Schemas & Onboarding Access Control | Features 1–8: Database migrations, layouts (`layouts.seller`, `layouts.seller-onboarding`), `SellerMiddleware` approval enforcement, multi-step onboarding wizard, Lat/Lng GPS capture, pending waiting gate, `SellerOnboardingController` | none | DONE |
| 2 | M2: Seller Dashboard & Performance Analytics | Features 9–15: `SellerDashboardController`, real-time dynamic KPIs (orders, revenue, products, low stock, trust score), revenue chart, pipeline bar, low stock alerts, live auction spotlight | M1 | DONE |
| 3 | M3: Product & Inventory Management (Custom Units & Perishables) | Features 16–25: `SellerProductController`, Product CRUD, custom units (`kg`, `dozen`, `bundle`, `litre`), harvest dates, shelf-life expiry auto-flag & auto-hide, stock telemetry, restock modal, live buyer preview | M1 | DONE |
| 4 | M4: Order Fulfillment & Payout Management | Features 26–33: `SellerOrderController`, `SellerPayoutController`, tenant-isolated orders workspace, assigned delivery slots, status progression to fulfilled, transparent commission calculation (10%), settlements ledger | M1 | DONE |
| 5 | M5: Profile & Auction Management | Features 34–43: `SellerProfileController`, `SellerAuctionController`, shop profile branding, Lat/Lng location settings, password complexity, wholesale auction creation, live bids monitor, reserve indicator, cancellation guard | M1 | DONE |
| 6 | M6: E2E Verification, Adversarial Hardening & Regression Pass | Features 44–46: Complete automated test suite (`tests/Feature/Seller/`), regression run of all 278 existing tests, forensic integrity audit | M1, M2, M3, M4, M5 | DONE |

## Interface Contracts
### SellerProfile ↔ User
- `User` has `role = 'seller'`, `status = 'active'`.
- `User` `hasOne(SellerProfile::class, 'user_id')`.
- `SellerProfile.status` in `['pending', 'approved', 'rejected', 'suspended']`.
- Only `SellerProfile.status === 'approved'` permits access to `/seller/dashboard` and operational routes.

### Product ↔ Seller
- `Product.seller_id` references `users.id` (where role is seller).
- `Product.unit_type` in `['piece', 'kg', 'dozen', 'bundle', 'litre', 'pack']`.
- `Product.harvest_date` (nullable date), `Product.expiry_days` (nullable unsigned integer), `Product.expiry_date` (nullable date), `Product.is_perishable` (boolean).
- If `is_perishable = true` and `expiry_date < now()`, product is flagged as stale; if `auto_hide_expired = true`, it is excluded from public search and catalog.

### Auction ↔ SellerProfile
- `Auction.seller_id` references `seller_profiles.id` (foreign key to `seller_profiles`).
- `Auction.status` in `['scheduled', 'live', 'ended', 'cancelled']`.
- Cancellation is allowed ONLY when `AuctionBid::where('auction_id', $auction->id)->count() === 0`.

### SellerOrder ↔ Payout
- `SellerOrder.seller_id` references `users.id`.
- `SellerOrder.delivery_slot` captures customer's requested delivery slot (e.g. "Morning 8AM-11AM", "Evening 4PM-7PM").
- `Payout.seller_id` references `users.id`.
- `Payout.seller_order_id` references `seller_orders.id`.
- Net Payout formula: `subtotal - (subtotal * commission_rate) - (subtotal * apmc_cess)`.
