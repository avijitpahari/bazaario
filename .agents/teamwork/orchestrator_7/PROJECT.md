# Project: Bazaario UI, Logic, Layout & Infrastructure Remediation

## Architecture
- **Framework & Runtime**: Laravel 11, PHP 8.2, SQLite (testing) / MySQL (runtime), Vite 7.3, Tailwind CSS v4.
- **Frontend Asset Stack**:
  - Tailwind CSS v4 using `@theme` definitions in `resources/css/app.css` (no `tailwind.config.js`).
  - Bundled scripts managed via Vite (`resources/js/app.js`).
  - Alpine.js 3.x unified build (removing redundant CDN script tags from views to prevent double initialization).
- **Design System**: Warm Modernist Commerce (`#FFFDF8` canvas, `#FFFFFF` elevated surface, `#0F172A` slate text, `#F5A623` amber accent). Unify seller tokens (`surface`, `brand.*`, `font-heading`) into `app.css` `@theme` aliasing to eliminate runtime CDN dependencies.
- **Layout Architecture**:
  - `layouts.app`: Master customer layout with Vite compilation, dynamic notifications, and bottom mobile navigation.
  - `layouts.seller`: Merchant operations layout with responsive slide-over drawer on `< lg` (< 1024px) screens and fixed 72-unit sidebar on `>= lg`.
- **Quality Standard**: Zero regressions on existing 706 automated test cases, zero route errors, zero integrity violations, clean WCAG 1.4.4 viewport scaling.

## Code Layout
- `resources/css/app.css` — Tailwind v4 `@theme` definitions, seller color tokens, design system aliases.
- `resources/js/app.js` — Vite frontend entry point, Alpine.js bundle.
- `package.json` — Frontend dependencies and build scripts.
- `routes/web.php` — Route mappings (user, seller, public legal pages).
- `app/Http/Controllers/ProductController.php` — Legal pages, category fallback, optimized home queries.
- `app/Http/Controllers/Seller/SellerAuctionController.php` — Filtered auction history.
- `app/Http/Controllers/Seller/SellerProductController.php` — Bulk actions handler.
- `app/Http/Controllers/Seller/SellerDashboardController.php` — Null-safe dashboard KPIs without mock numbers.
- `resources/views/layouts/app.blade.php` — Customer master layout.
- `resources/views/layouts/seller.blade.php` — Seller master layout with responsive drawer.
- `resources/views/layouts/user.blade.php` — User account master layout.
- `resources/views/index.blade.php` — Marketplace home page.
- `resources/views/components/nav-user.blade.php` — Customer navigation header & mobile bottom bar.
- `resources/views/components/footer.blade.php` — Unified footer with working policy links, currency selector, and non-destructive localization.
- `resources/views/pages/privacy.blade.php`, `terms.blade.php`, `return-policy.blade.php` — Legal policy pages.
- `resources/views/seller/dashboard.blade.php` — Dynamic merchant dashboard with zero-fallback handling.
- `resources/views/seller/products/index.blade.php` — Seller catalog workstation with bulk actions.
- `resources/views/seller/account/notifications.blade.php`, `settings.blade.php` — Merchant account views.
- `resources/views/user/products/index.blade.php`, `show.blade.php` — Product catalog and product details.

## Feature Inventory (42 Issues Mapped to Milestones)
| # | Issue ID | Description | Milestone | Source |
|---|----------|-------------|-----------|--------|
| 1 | P1 | Remove double CSS load (@vite + static <link>) in app.blade.php & index.blade.php | M1 | UI_LOGIC_PROBLEMS.md (DONE) |
| 2 | P2 | Remove Tailwind CDN and inline config from layouts/seller.blade.php; integrate tokens into app.css | M1 | UI_LOGIC_PROBLEMS.md (DONE) |
| 3 | P3 | Remove duplicate CDN Alpine.js from user/products/index.blade.php | M1 | UI_LOGIC_PROBLEMS.md (DONE) |
| 4 | P4 | Remove duplicate CDN Alpine.js from index.blade.php | M1 | UI_LOGIC_PROBLEMS.md (DONE) |
| 5 | P17 | Unify customer & seller design system tokens (amber-action vs brand.amber, fonts, radii) in app.css | M1 | UI_LOGIC_PROBLEMS.md (DONE) |
| 6 | P34 | Remove user-scalable=no viewport meta from products/index and info pages (WCAG 1.4.4) | M1 | UI_LOGIC_PROBLEMS.md (DONE) |
| 7 | P5 | Verify & ensure user account routes/views exist (returns, invoices, compare, bids, notifications) | M2 | UI_LOGIC_PROBLEMS.md |
| 8 | P6 | Fix misleading "AI Compare" CTA destination on home page | M2 | UI_LOGIC_PROBLEMS.md |
| 9 | P7 | Fix floating "Ask Bazaario AI" button to provide functional modal or clean fallback | M2 | UI_LOGIC_PROBLEMS.md |
| 10 | P8 | Wire seller header search bar to valid form action/route | M2 | UI_LOGIC_PROBLEMS.md |
| 11 | P9 | Remove hardcoded numeric fallbacks (248 orders, 84k revenue) from seller dashboard | M2 | UI_LOGIC_PROBLEMS.md |
| 12 | P10 | Differentiate seller.auctions.history route from index with filtered completed auctions | M2 | UI_LOGIC_PROBLEMS.md |
| 13 | P20 | Replace dead social media links in footer with valid stubs/links | M2 | UI_LOGIC_PROBLEMS.md |
| 14 | P21 | Implement /privacy, /terms, and /return-policy routes and views (HTTP 200) | M2 | UI_LOGIC_PROBLEMS.md |
| 15 | P22 | Fix footer Deals query parameter (filter=deals instead of filter=escrow) | M2 | UI_LOGIC_PROBLEMS.md |
| 16 | P26 | Add seller.account.notifications route and populate notifications.blade.php | M2 | UI_LOGIC_PROBLEMS.md |
| 17 | P28 | Implement seller.account.settings view and stop confusing redirect to profile | M2 | UI_LOGIC_PROBLEMS.md |
| 18 | P31 | Ensure $featuredAuction->bids->count() uses withCount('bids') to prevent N+1 queries | M2 | UI_LOGIC_PROBLEMS.md |
| 19 | P32 | Ensure Storage facade import in index.blade.php @php block | M2 | UI_LOGIC_PROBLEMS.md |
| 20 | P39 | Differentiate or clean up /how-it-works vs /about routes | M2 | UI_LOGIC_PROBLEMS.md |
| 21 | P41 | Handle null slug gracefully in ProductController@category (/category/) | M2 | UI_LOGIC_PROBLEMS.md |
| 22 | P11 | Implement responsive sidebar drawer with hamburger toggle in layouts/seller.blade.php | M3 | UI_LOGIC_PROBLEMS.md |
| 23 | P12 | Unify home page navbar to use components.nav-user or establish full feature parity | M3 | UI_LOGIC_PROBLEMS.md |
| 24 | P13 | Fix mobile bottom nav bar overlap (pb-24 spacing) across all user & product views | M3 | UI_LOGIC_PROBLEMS.md |
| 25 | P14 | Add fallback handling (onerror + gradient) for hero screen.png image | M3 | UI_LOGIC_PROBLEMS.md |
| 26 | P15 | Optimize category grid responsiveness and prevent unicode mid-character truncation | M3 | UI_LOGIC_PROBLEMS.md |
| 27 | P16 | Replace fake fallback seller cards with clean "Be the first featured seller" empty state CTA | M3 | UI_LOGIC_PROBLEMS.md |
| 28 | P33 | Replace hardcoded Kolkata default location in Nearby Stalls with graceful fallback logic | M3 | UI_LOGIC_PROBLEMS.md |
| 29 | P35 | Replace hardcoded Unsplash leather bag fallback images with generic product placeholders | M3 | UI_LOGIC_PROBLEMS.md |
| 30 | P42 | Prevent category mega-menu width (w-[540px]) overflow on smaller laptop displays | M3 | UI_LOGIC_PROBLEMS.md |
| 31 | P18 | Dynamically bind notification bell badge count (remove hardcoded "3 New") | M4 | UI_LOGIC_PROBLEMS.md |
| 32 | P19 | Fix cart popover trigger on touch devices (avoid pure hover @mouseenter breaking touch) | M4 | UI_LOGIC_PROBLEMS.md |
| 33 | P23 | Verify and validate scopeLowStock() and scopeStale() queries in seller products view | M4 | UI_LOGIC_PROBLEMS.md |
| 34 | P24 | Wire bulk actions in seller products view to genuine form submission/routes (replace alerts) | M4 | UI_LOGIC_PROBLEMS.md |
| 35 | P25 | Make seller dashboard 7-day revenue chart dynamic with real data and clean empty state | M4 | UI_LOGIC_PROBLEMS.md |
| 36 | P27 | Wire seller header notification bell button to dropdown or notifications route | M4 | UI_LOGIC_PROBLEMS.md |
| 37 | P29 | Ensure complete navbar feature parity between home page and customer pages | M4 | UI_LOGIC_PROBLEMS.md |
| 38 | P30 | Replace non-existent popular search chips (iPhone 16 Pro, Leica M3) with catalog items | M4 | UI_LOGIC_PROBLEMS.md |
| 39 | P36 | Fix category nav-user dropdown links to use category slug routes instead of search query | M4 | UI_LOGIC_PROBLEMS.md |
| 40 | P37 | Render visible toggle button for currency selector in components/footer.blade.php | M4 | UI_LOGIC_PROBLEMS.md |
| 41 | P38 | Implement non-destructive translation text cache avoiding window.location.reload() on 'en' | M4 | UI_LOGIC_PROBLEMS.md |
| 42 | P40 | Strengthen seller layout authentication guard check to avoid customer data leakage | M4 | UI_LOGIC_PROBLEMS.md |

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| M1 | Asset & Infrastructure Optimization | P1, P2, P3, P4, P17, P34 | None | DONE |
| M2 | Logic, Route & Data Reliability | P5, P6, P7, P8, P9, P10, P20, P21, P22, P26, P28, P31, P32, P39, P41 | M1 | PLANNED |
| M3 | Responsive Layout & Mobile Navigation | P11, P12, P13, P14, P15, P16, P33, P35, P42 | M1 | PLANNED |
| M4 | UI Interactive Components & Seller Workstation | P18, P19, P23, P24, P25, P27, P29, P30, P36, P37, P38, P40 | M2, M3 | PLANNED |
| M5 | E2E Regression Pass & System Certification | All 42 issues, full 706+ test suite, route compilation, DOM validation | M1-M4 | PLANNED |
