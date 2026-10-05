# Bazaario Technical Specification & Code Survey Report
## Requirements R1 (Asset & Infrastructure) & R2 (Logic, Route & Data Reliability)
**Auditor / Miner:** `survey_miner_r1_r2` (teamwork_preview_spec_miner)  
**Date:** 2026-10-01  
**Target Codebase:** `c:\xampp\htdocs\bazaario` (Laravel 11 / Tailwind v4 / Vite / Blade)

---

## 1. Executive Summary

This specification mining report covers Requirements **R1** (Asset & Infrastructure Optimization) and **R2** (Logic, Route & Data Reliability), encompassing audit issues **P1–P4, P5–P10, P23–P28, P31–P32, and P39–P41**, as well as several discovered collateral issues.

A comprehensive survey of the Blade templates, controllers, routes (`routes/web.php`), and Eloquent models was conducted. The baseline test suite (`php artisan test`) currently passes with **706 tests (5001 assertions)**, verifying that the fundamental transactional backend is stable. However, there are significant asset redundancies, missing routes/views in the seller domain, hardcoded fake fallback data leaking in dashboards, stubbed interactive handlers (`alert()` and inert inputs), and double-inclusions of scripts and floating elements that impair production reliability.

---

## 2. Features Discovered

| # | Category | Feature | Description | Inputs | Outputs | Error Behavior | Discovered Via |
|---|----------|---------|-------------|--------|---------|----------------|----------------|
| 1 | Asset | Vite Stylesheet Loading | Dynamic hashed asset inclusion from Vite manifest | `@vite(['resources/css/app.css', 'resources/js/app.js'])` | `<link>` and `<script>` tags referencing `build/assets/...` | Fallback missing manifest throws ViteException | Code inspection in `layouts/app.blade.php:24`, `index.blade.php:16` |
| 2 | Asset | Static CSS Fallback | Hardcoded stylesheet link to pre-built asset | `<link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}">` | Double-loads CSS in conjunction with `@vite` | Breaks cache busting if CSS changes | Code inspection in `layouts/app.blade.php:25`, `index.blade.php:17` |
| 3 | Asset | Seller Panel Tailwind CDN | Runtime Tailwind CDN compilation with inline theme extension | `<script src="https://cdn.tailwindcss.com"></script>` + inline config | 100KB+ client-side JIT CSS | Overrides Vite theme tokens, slows page parse | Code inspection in `layouts/seller.blade.php:20-72` |
| 4 | Asset | Alpine.js CDN Script | Deferred runtime inclusion of Alpine.js v3 | `<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>` | Global `window.Alpine` initialization | Causes double initialization if bundled in `app.js` | Code inspection in `index.blade.php:1127`, `user/products/index.blade.php:24`, `layouts/seller.blade.php:74` |
| 5 | Route | Customer Account Invoices | Purchase invoice history for paid orders | GET `/user/invoices` (`user.invoices`) | Renders `user.account.invoices` Blade view with order table | Requires `auth:user`; empty orders renders empty state card | `routes/web.php:175`, `user/account/invoices.blade.php` |
| 6 | Route | Customer Returns & Refunds | Return requests, damage reports, and refund ledger | GET `/user/returns` (`user.returns`) | Renders `user.account.returns` view with `OrderReturn` records | Requires `auth:user`; empty state with CTA to `/user/orders` | `routes/web.php:167-173`, `user/account/returns.blade.php` |
| 7 | Route | Customer Product Compare | Side-by-side product attribute comparison | GET `/user/compare` (`user.compare`) | Renders `user.account.compare` view | Currently returns empty state stub ("No products to compare") | `routes/web.php:186`, `user/account/compare.blade.php` |
| 8 | Route | Customer Live Bids Ledger | Active and historical bids and escrow lockup | GET `/user/bids` (`user.bids`) | Calls `AuctionController@bids`, renders `user.account.bids` | Redirects to login if guest; computes escrow stats | `routes/web.php:165`, `User\AuctionController.php:134` |
| 9 | Route | Customer Auctions Hub | Live auction lots clearance floor | GET `/user/auctions` (`user.auctions`) | Calls `AuctionController@index`, renders `user.account.auctions` | Same action as `/auctions` public route | `routes/web.php:164`, `User\AuctionController.php:18` |
| 10 | Route | Seller Auction History | Filtered view of finished/ended auctions for seller | GET `/seller/auctions/history` (`seller.auctions.history`) | Currently calls `SellerAuctionController@index` (unfiltered) | Does NOT filter by completed auctions; duplicates `seller.auctions.index` | `routes/web.php:242`, `SellerAuctionController.php:20` |
| 11 | Route | Seller Account Notifications | Dedicated alert center for merchant activity | GET `/seller/account/notifications` | View `seller/account/notifications.blade.php` exists (0 bytes) | Route is missing in `routes/web.php`; file is empty | `resources/views/seller/account/notifications.blade.php` |
| 12 | Route | Seller Account Settings | Preferences, notification alerts, and operational toggles | GET `/seller/account/settings` (`seller.account.settings`) | Currently redirects to `seller.account.profile` | Silent redirect causes confusing sidebar highlight mismatch; view is 0 bytes | `routes/web.php:258`, `seller/account/settings.blade.php` |
| 13 | Route | Category Show / Catalog Filter | Category-filtered product grid | GET `/category/{slug?}` (`category.show`) | Calls `ProductController@category`, renders `user.products.category` | If `$slug` is null, searches for `category.slug == null` returning 0 products | `routes/web.php:51`, `ProductController.php:264` |
| 14 | Route | Documentation / How It Works | Platform buyer and seller guide | GET `/how-it-works` (`pages.how-it-works`) & GET `/about` (`about`) | Calls `ProductController@howItWorks`, renders `pages.how-it-works` | Redundant route names for same controller method | `routes/web.php:53-54`, `ProductController.php:356` |
| 15 | UI/Logic | Home "Try AI Compare" CTA | Homepage call-to-action for comparison feature | `<a href="{{ route('products.index') }}">` | Navigates to products catalog | Misleading destination (not an AI comparison engine) | `resources/views/index.blade.php:360-364` |
| 16 | UI/Logic | Floating "Ask Bazaario AI" Button | Persistent docked bottom-right action trigger | `<a>` in `index.blade.php:1115` & `<button>` in `footer.blade.php:254` | Links to `products.index` or inert button | Double-rendered on homepage; inert in footer | `index.blade.php:1115`, `footer.blade.php:254` |
| 17 | UI/Logic | Seller Header Global Search Bar | Top navbar search bar with ⌘K keyboard shortcut | `<input placeholder="Search orders, products, auctions, payouts...">` | Unwrapped text input without form or keydown handler | Pressing Enter does nothing | `layouts/seller.blade.php:230-235` |
| 18 | UI/Logic | Seller Header Notification Bell | Top navbar alerts trigger | `<button type="button" title="Notifications">` | Inert button without popover or link | Clicking bell does nothing | `layouts/seller.blade.php:240-244` |
| 19 | UI/Logic | Seller Dashboard KPI Fallbacks | Operational metric display cards on dashboard | `$totalOrders ?? 248`, `$grossRevenue ?? 84520.00`, etc. | Displays fake hardcoded numbers on missing variables | Leaks fake demo data to merchants when queries return zero or null | `seller/dashboard.blade.php:10-185`, `SellerDashboardController.php:260-288` |
| 20 | UI/Logic | Seller Revenue 7-Day Chart | Daily revenue bars with peak indicators | Dynamic `$revenueChartData` or hardcoded fallback array | CSS percentage bars | Inaccessible (no ARIA attributes); fallback array leaks fake data | `seller/dashboard.blade.php:34-42, 394-420` |
| 21 | Model Scope | Product Low Stock Scope | Filters products at or below threshold | `Product::lowStock()` | `WHERE stock <= low_stock_threshold OR (low_stock_threshold IS NULL AND stock <= 10)` | Safe, implemented in `Product.php:207-215` | `app/Models/Product.php:207` |
| 22 | Model Scope | Product Freshness Stale Scope | Filters perishable products past expiry | `Product::stale()` | `WHERE is_perishable = 1 AND expiry_date IS NOT NULL AND expiry_date < CURDATE()` | Safe, implemented in `Product.php:189-194` | `app/Models/Product.php:189` |
| 23 | UI/Logic | Seller Product Bulk Actions | Multi-product batch operations (Active, Discount, Archive) | Dropdown with `@click.prevent="alert(...)"` | JavaScript alerts only | Zero persistence, no backend routes or endpoints | `seller/products/index.blade.php:57-67` |
| 24 | Logic | Featured Auction Bids Count | Bids counter badge on homepage hero lot | `{{ $featuredAuction->bids->count() }}` | Displays bid count on featured auction card | Potential N+1 query if `bids` relation not eager loaded | `resources/views/index.blade.php:295` |
| 25 | Logic | Storage Facade Import in Blade | Generates public URL for uploaded seller logos and banners | `Storage::url($profile->logo_path)` | Returns `/storage/...` URL | Fragile if alias not registered; lacks explicit facade import | `resources/views/index.blade.php:518-519` |
| 26 | UI/Logic | Footer Legal Links | Privacy Policy, Terms, Return Policy | Links in `components/footer.blade.php:54, 61-64` | `href="#"` dead links | 404 / inactive legal compliance links | `resources/views/components/footer.blade.php:54-65` |

---

## 3. Edge Cases Discovered

| # | Feature | Input | Observed Behavior |
|---|---------|-------|-------------------|
| E1 | Category Route (`category.show`) | `GET /category` (no slug parameter provided) | Route allows optional slug (`{slug?}`). When null, `ProductController@category` tests `$slug !== 'all'`, evaluates as true, and executes `where('slug', null)` which matches 0 products and shows empty catalog. |
| E2 | Seller Dashboard Empty State | New approved seller with 0 orders and 0 revenue | Controller sends `$totalOrders = 0`, but `fulfillment_text` defaults to `"235 / 248 on time"` and `reviews_text` defaults to `"182 verified reviews"`. If controller variables are missing or null, Blade defaults show `$totalOrders = 248`, `$grossRevenue = 84520.00`, and fake 7-day revenue chart. |
| E3 | Floating AI Button on Homepage | User views Homepage (`/`) | Both `index.blade.php` (line 1115) and `<x-footer />` (line 254) inject `<div class="fixed bottom-6 right-6 z-40">`. Two floating buttons are rendered directly on top of each other: one links to `products.index`, the other is an inert `<button>`. |
| E4 | Seller Header Search Bar | User types query in seller header and presses Enter | Input has no enclosing `<form>` tag and no `name` attribute. Pressing Enter causes no action, no network request, and no search submission. |
| E5 | Seller Bulk Actions | User selects checkboxes and clicks "Update Status: Active" | Triggers browser `alert('Selected products marked active')`. Checkbox selections are ignored, no HTTP request is made, and product statuses remain unchanged in the database. |
| E6 | Alpine.js Script Inclusion | User visits catalog (`/products`) or homepage (`/`) | If `@vite` bundles Alpine.js, the additional `<script defer src="alpinejs@3.x.x">` loads a second runtime instance of Alpine, re-registering directives and conflicting with existing component state. Currently, `resources/js/app.js` only imports Axios without Alpine; removing the CDN tag without bundling Alpine in `app.js` will break all Alpine interactive components (`x-data`, popovers, cart drawer). |
| E7 | Seller Panel Mobile Viewport | Viewport width < 1024px | `layouts/seller.blade.php` has a fixed sidebar `w-72` and offset `pl-72` with no hamburger button or mobile drawer. The sidebar permanently obstructs content on mobile/tablet viewports. |
| E8 | Footer Legal Links | User clicks "Privacy Policy", "Terms of Service", or "Return Policy" | All five policy links point to `href="#"`. Clicking scrolls to top of page without displaying legal terms or return instructions. |

---

## 4. Deep-Dive Findings & Exact Code Inspection

### 4.1 Asset & Infrastructure Optimization (P1–P4)

#### 🔴 P1 — Double CSS Load in `app.blade.php` and `index.blade.php`
- **Location 1:** `resources/views/layouts/app.blade.php`
  - Line 24: `@vite(['resources/css/app.css', 'resources/js/app.js'])`
  - Line 25: `<link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}">`
  - Lines 26–38: `<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>` with inline `tailwind.config`.
- **Location 2:** `resources/views/index.blade.php`
  - Line 16: `@vite(['resources/css/app.css', 'resources/js/app.js'])`
  - Line 17: `<link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}">`
- **Impact:** The compiled CSS bundle is downloaded and parsed twice on every page load. Furthermore, `app.blade.php` also loads the full Tailwind CDN and injects duplicate inline config overrides, causing race conditions between compiled Tailwind v4 styles and CDN JIT styles.
- **Actionable Remediation:**
  1. Remove Line 25 (`<link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}">`) from `resources/views/layouts/app.blade.php`.
  2. Remove Lines 26–38 (Tailwind CDN script and inline `tailwind.config`) from `resources/views/layouts/app.blade.php`.
  3. Remove Line 17 (`<link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}">`) from `resources/views/index.blade.php`.

---

#### 🔴 P2 — Tailwind CDN Still Used in Seller Layout (`layouts/seller.blade.php`)
- **Location:** `resources/views/layouts/seller.blade.php` (Lines 19–72)
  - Line 20: `<script src="https://cdn.tailwindcss.com"></script>`
  - Lines 21–72: `<script>tailwind.config = { ... }</script>`
  - Line 74: `<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>`
- **Impact:** The seller panel operates on an isolated runtime CDN design system rather than the Vite-compiled pipeline. The inline config defines custom tokens:
  - Surfaces: `surface` (`#fbf9f4`), `surface-container-low` (`#f5f3ee`), `surface-container` (`#efeee9`), `surface-container-high` (`#eae8e3`), `surface-container-highest` (`#e4e2de`), `surface-container-lowest` (`#ffffff`), `on-surface` (`#1b1c19`), `on-surface-variant` (`#45464d`).
  - Brand: `brand.bg` (`#FFFDF8`), `brand.slate` (`#0F172A`), `brand.amber` (`#F5A623`), `brand.amber-dark` (`#D98205`), `brand.green` (`#16A34A`), `brand.muted` (`#45464D`), `brand.outline` (`#E2DFD7`).
  - Containers: `secondary-container` (`#feae2c`), `on-secondary-container` (`#6b4500`), `on-tertiary-container` (`#009842`).
- **Actionable Remediation:**
  1. Add `@vite(['resources/css/app.css', 'resources/js/app.js'])` to `resources/views/layouts/seller.blade.php`.
  2. Map all seller surface and brand color tokens into `resources/css/app.css` under the `@theme` directive so Vite compiles utilities for `bg-surface`, `bg-surface-container-low`, `text-on-surface`, `bg-secondary-container`, etc.
  3. Remove the `<script src="https://cdn.tailwindcss.com"></script>` and inline `tailwind.config` block from `layouts/seller.blade.php`.

---

#### 🔴 P3 & P4 — Alpine.js Double Inclusion on Products Index and Home Page
- **Location 1:** `resources/views/user/products/index.blade.php` (Line 24)
  - `<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>`
- **Location 2:** `resources/views/index.blade.php` (Line 1127)
  - `<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>`
- **Location 3:** `resources/views/user/products/show.blade.php` (Line 790), `resources/views/user/products/category.blade.php` (Line 16), `resources/views/layouts/seller.blade.php` (Line 74), `resources/views/layouts/user.blade.php` (Line 164).
- **Critical Technical Observation:** Currently, `package.json` does NOT include `alpinejs`, and `resources/js/app.js` only contains `import './bootstrap';` (which imports Axios). If a developer merely deletes the CDN `<script>` tag from `index.blade.php` and `user/products/index.blade.php` without bundling Alpine into `app.js`, **all Alpine functionality across the home page and catalog will crash immediately** (`Alpine is not defined`).
- **Actionable Remediation:**
  1. Install Alpine.js via npm: `npm install alpinejs` (or bundle Alpine in `resources/js/app.js`: `import Alpine from 'alpinejs'; window.Alpine = Alpine; Alpine.start();`).
  2. Run `npm run build` so Vite generates `app.js` containing bundled Alpine.
  3. Remove the redundant CDN `<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>` tags from `index.blade.php` (Line 1127) and `user/products/index.blade.php` (Line 24).

---

### 4.2 Routes & Views Reliability (P5–P7, P10, P26, P28, P39, P41)

#### 🔴 P5 — Verification of Linked Routes & Views
Audit of routes linked in `components/nav-user.blade.php` dropdown:
- `route('user.bids')` (Line 425): Maps to `User\AuctionController@bids`. **Verified functional.** Method exists at line 134 of `AuctionController.php`; view `resources/views/user/account/bids.blade.php` exists (59,002 bytes).
- `route('user.auctions')` (Line 429): Maps to `User\AuctionController@index`. **Verified functional.** Method exists at line 18 of `AuctionController.php`; view `resources/views/user/account/auctions.blade.php` exists (31,734 bytes).
- `route('user.compare')` (Line 433): Maps to closure returning `view('user.account.compare')`. **Verified.** View `resources/views/user/account/compare.blade.php` exists (1,739 bytes).
- `route('user.returns')` (Line 408): Maps to closure querying `OrderReturn` and returning `view('user.account.returns')`. **Verified.** View `resources/views/user/account/returns.blade.php` exists (4,492 bytes).
- `route('user.invoices')` (Line 412): Maps to closure returning `view('user.account.invoices')`. **Verified.** View `resources/views/user/account/invoices.blade.php` exists (2,650 bytes).
- `route('user.notifications.index')` (Line 255): Maps to `User\NotificationController@index`. **Verified.** Method exists; view `resources/views/user/account/notifications.blade.php` exists (4,565 bytes).
- **Summary Finding:** None of these views trigger 500 errors in isolation, but `compare.blade.php` is an empty-state stub that informs the user to add products from catalog pages.

---

#### 🔴 P6 — "Try AI Compare" CTA Destination
- **Location:** `resources/views/index.blade.php` (Lines 360–364)
  ```html
  <a class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs px-5 py-2.5 rounded-full transition-all shadow-md active:scale-95" 
     href="{{ route('products.index') }}">
      <span>Try AI Compare</span>
      <span class="material-symbols-outlined text-[16px] text-amber-400">auto_awesome</span>
  </a>
  ```
- **Impact:** Misleading UX. Promising an "AI Compare" engine but routing to standard product listing `/products`.
- **Actionable Remediation:**
  - Route the button to `route('user.compare')` (if authenticated) or `route('products.index', ['view' => 'compare'])` / `route('user.compare')`.
  - Alternatively, if the AI compare feature is not yet built, update button text to "Browse Marketplace" or "Explore Catalog" to set accurate expectations.

---

#### 🔴 P7 — "Ask Bazaario AI" Floating Button Non-Functional & Duplicated
- **Location 1:** `resources/views/index.blade.php` (Lines 1115–1121)
  ```html
  <div class="fixed bottom-6 right-6 z-40">
      <a href="{{ route('products.index') }}" class="...">✦ Ask Bazaario AI</a>
  </div>
  ```
- **Location 2:** `resources/views/components/footer.blade.php` (Lines 253–262)
  ```html
  <div class="fixed bottom-6 right-6 z-40">
      <button class="..." type="button">✦ Ask Bazaario AI</button>
  </div>
  ```
- **Impact:** On the homepage (`/`), `index.blade.php` includes `<x-footer />`. As a result, **two floating buttons are rendered directly on top of each other** at `fixed bottom-6 right-6 z-40`. The button in `footer.blade.php` is an inert `<button type="button">` with no event listener or link. The button in `index.blade.php` links to `products.index`.
- **Actionable Remediation:**
  1. Remove the floating button block from `index.blade.php` (Lines 1114–1121).
  2. In `resources/views/components/footer.blade.php`, wire the button to an Alpine.js modal drawer or search popover (e.g. `@click="aiChatOpen = true"` or `@click="window.location.href='{{ route('products.index') }}'"`).

---

#### 🔴 P10 — `seller.auctions.history` Duplicate Route
- **Location:** `routes/web.php` (Line 242)
  ```php
  Route::get('/history', [SellerAuctionController::class, 'index'])->name('history');
  ```
- **Impact:** The sidebar displays two separate navigation tabs under Auctions: "My Auctions" (`seller.auctions.index`) and "Auction History" (`seller.auctions.history`). Clicking either tab executes `SellerAuctionController@index` with `$status = 'all'`, displaying identical auction lists.
- **Actionable Remediation:**
  1. In `app/Http/Controllers/Seller/SellerAuctionController.php`, implement a distinct `history` method:
     ```php
     public function history(Request $request): View|RedirectResponse
     {
         $request->merge(['status' => 'ended']);
         return $this->index($request);
     }
     ```
  2. In `routes/web.php` Line 242, update the route binding:
     ```php
     Route::get('/history', [SellerAuctionController::class, 'history'])->name('history');
     ```

---

#### 🟡 P26 — `seller/account/notifications.blade.php` Missing Route & View
- **Location:** `resources/views/seller/account/notifications.blade.php` (0 bytes, empty file).
- **Route Status:** `routes/web.php` defines routes for `profile`, `location`, `security`, and `settings` under `seller.account.*`, but completely omits `notifications`.
- **Actionable Remediation:**
  1. Add route in `routes/web.php` inside the `seller.account` prefix group:
     ```php
     Route::get('/notifications', [SellerProfileController::class, 'notifications'])->name('notifications');
     ```
  2. Add method `notifications()` to `app/Http/Controllers/Seller/SellerProfileController.php`:
     ```php
     public function notifications(): View|RedirectResponse
     {
         $seller = Auth::guard('seller')->user() ?? Auth::user();
         if (!$seller) return redirect()->route('login');
         $profile = $seller->sellerProfile;
         return view('seller.account.notifications', compact('seller', 'profile'));
     }
     ```
  3. Populate `resources/views/seller/account/notifications.blade.php` with a standard seller notifications list (order alerts, auction bids, payout releases).

---

#### 🟡 P28 — `seller.account.settings` Route Redirects to Profile
- **Location:** `routes/web.php` (Line 258)
  ```php
  Route::get('/settings', fn () => redirect()->route('seller.account.profile'))->name('settings');
  ```
- **View Status:** `resources/views/seller/account/settings.blade.php` is 0 bytes (empty file).
- **Impact:** The sidebar links to `seller.account.settings`. When clicked, it redirects to `seller.account.profile`, which causes the active sidebar indicator to jump from "Settings" to "Shop Profile".
- **Actionable Remediation:**
  1. Implement `settings()` method in `SellerProfileController.php`.
  2. Update `routes/web.php` Line 258:
     ```php
     Route::get('/settings', [SellerProfileController::class, 'settings'])->name('settings');
     Route::put('/settings', [SellerProfileController::class, 'updateSettings'])->name('settings.update');
     ```
  3. Populate `resources/views/seller/account/settings.blade.php` with seller operating preferences (auto-hide expired listings toggle, default unit type, payout notification frequency).

---

#### 🟢 P39 — Duplicate `pages.how-it-works` & `about` Routes
- **Location:** `routes/web.php` (Lines 53–54)
  ```php
  Route::get('/how-it-works', [ProductController::class, 'howItWorks'])->name('pages.how-it-works');
  Route::get('/about', [ProductController::class, 'howItWorks'])->name('about');
  ```
- **Impact:** Both `/how-it-works` and `/about` call `ProductController@howItWorks` which returns `view('pages.how-it-works')`.
- **Actionable Remediation:**
  Either retain both for backward compatibility (ensure footer links use consistent names), or create a dedicated `about` view if distinct company information is required.

---

#### 🟢 P41 — Missing/Empty Slug Handling in `category.show` Route
- **Location:** `routes/web.php` (Line 51), `app/Http/Controllers/ProductController.php` (Lines 264–284)
  ```php
  Route::get('/category/{slug?}', [ProductController::class, 'category'])->name('category.show');
  ```
  ```php
  public function category(Request $request, $slug = 'all')
  {
      $categories = Category::where('status', 'active')->get();
      $category = null;
      if ($slug !== 'all') {
          $category = $categories->firstWhere('slug', $slug) ?? Category::where('slug', $slug)->first();
      }
      $query = Product::with(['category', 'seller.sellerProfile', 'images', 'primaryImage'])->where('status', 'active');
      if ($category) {
          $query->where('category_id', $category->id);
      } elseif ($slug !== 'all') {
          $query->whereHas('category', function ($q) use ($slug) {
              $q->where('slug', $slug);
          });
      }
      ...
  ```
- **Impact:** When a user visits `/category` (without a slug), `$slug` defaults to `null`. The condition `if ($slug !== 'all')` evaluates to `true`, and because `$category` is `null`, it executes `whereHas('category', fn($q) => $q->where('slug', null))`. This query returns 0 products.
- **Actionable Remediation:**
  In `ProductController::category`:
  ```php
  if (empty($slug) || $slug === 'all') {
      $slug = 'all';
      $category = null;
  } else {
      $category = $categories->firstWhere('slug', $slug) ?? Category::where('slug', $slug)->first();
  }
  ```
  If `$slug === 'all'`, load all active products across all categories without appending the `whereHas('category')` constraint.

---

### 4.3 Controller & Model Logic (P8, P9, P23, P24, P25, P27, P31, P32)

#### 🔴 P8 — Seller Top Header Global Search Bar
- **Location:** `resources/views/layouts/seller.blade.php` (Lines 230–236)
  ```html
  <!-- Global Search with ⌘K -->
  <div class="flex items-center flex-1 max-w-lg">
      <div class="flex items-center w-full px-3.5 py-2 bg-surface-container-lowest rounded-[12px] ...">
          <span class="material-symbols-outlined text-on-surface-variant text-[20px] mr-2">search</span>
          <input type="text" class="w-full bg-transparent text-xs text-on-surface placeholder:text-on-surface-variant outline-none font-sans" placeholder="Search orders, products, auctions, payouts...">
          <kbd class="hidden sm:inline-flex items-center px-1.5 py-0.5 font-mono text-[10px] bg-surface-container text-on-surface-variant rounded border border-surface-container-high">⌘K</kbd>
      </div>
  </div>
  ```
- **Impact:** The search input is not wrapped in a `<form>` element, lacks a `name` attribute, and has no keydown event handler. Entering search terms and pressing Enter produces no response.
- **Actionable Remediation:**
  Wrap the input container in a valid GET form submitting to `seller.products.index`:
  ```html
  <form method="GET" action="{{ route('seller.products.index') }}" class="flex items-center flex-1 max-w-lg">
      <div class="flex items-center w-full px-3.5 py-2 bg-surface-container-lowest rounded-[12px] shadow-[0_1px_4px_rgba(0,0,0,0.02)] border border-surface-container-highest focus-within:border-brand-amber transition">
          <span class="material-symbols-outlined text-on-surface-variant text-[20px] mr-2">search</span>
          <input type="text" name="search" value="{{ request('search') }}" class="w-full bg-transparent text-xs text-on-surface placeholder:text-on-surface-variant outline-none font-sans" placeholder="Search orders, products, auctions, payouts...">
          <kbd class="hidden sm:inline-flex items-center px-1.5 py-0.5 font-mono text-[10px] bg-surface-container text-on-surface-variant rounded border border-surface-container-high">⌘K</kbd>
      </div>
  </form>
  ```

---

#### 🔴 P9 — Hardcoded Fake Fallback Data Leaking in Seller Dashboard
- **Location 1:** `resources/views/seller/dashboard.blade.php` (Lines 10–185)
  ```php
  $totalOrders = $totalOrders ?? 248;
  $ordersGrowth = $ordersGrowth ?? '18.4';
  $pendingOrdersCount = $pendingOrdersCount ?? 12;
  $grossRevenue = $grossRevenue ?? $totalRevenue ?? 84520.00;
  $revenueGrowth = $revenueGrowth ?? '22.5';
  $averageOrderValue = $averageOrderValue ?? $aov ?? ($totalOrders > 0 ? ($grossRevenue / $totalOrders) : 0.0);
  $activeProductsCount = $activeProductsCount ?? 42;
  $lowStockCount = $lowStockCount ?? 6;
  $criticalStockCount = $criticalStockCount ?? 2;
  $nextSettlementAmount = $nextSettlementAmount ?? $nextPayout ?? 12450.00;
  $revenueChartData = $revenueChartData ?? [ ... 7 hardcoded days ... ];
  $pipelineCounts = $pipelineCounts ?? [ 'pending' => 12, 'placed' => 12, 'processing' => 24, 'ready' => 18, 'delivered' => 194 ];
  $lowStockProducts = $lowStockProducts ?? collect([ (object)['name' => 'Organic Alphonso Mango', ...], ... ]);
  $topProducts = $topProducts ?? collect([ ... ]);
  ```
- **Location 2:** `app/Http/Controllers/Seller/SellerDashboardController.php` (Lines 260–288)
  - Line 281: `'fulfillment_text' => $totalOrders > 0 ? "{$deliveredCount} / {$totalOrders} on time" : "235 / 248 on time"`
  - Line 283: `'reviews_text' => $totalReviewCount > 0 ? "{$totalReviewCount} verified reviews" : "182 verified reviews"`
- **Impact:** When a new seller logs in with 0 orders, or if a query returns null, the dashboard presents fictitious numbers ($84,520 gross revenue, 248 orders, 6 low stock alerts, fake Alphonso Mango products).
- **Actionable Remediation:**
  1. In `seller/dashboard.blade.php`, replace all hardcoded demo numeric fallbacks with neutral defaults:
     ```php
     $totalOrders = $totalOrders ?? 0;
     $ordersGrowth = $ordersGrowth ?? '0.0';
     $pendingOrdersCount = $pendingOrdersCount ?? 0;
     $grossRevenue = $grossRevenue ?? $totalRevenue ?? 0.00;
     $revenueGrowth = $revenueGrowth ?? '0.0';
     $averageOrderValue = $averageOrderValue ?? $aov ?? 0.0;
     $activeProductsCount = $activeProductsCount ?? 0;
     $lowStockCount = $lowStockCount ?? 0;
     $criticalStockCount = $criticalStockCount ?? 0;
     $nextSettlementAmount = $nextSettlementAmount ?? $nextPayout ?? 0.00;
     $revenueChartData = $revenueChartData ?? [];
     $pipelineCounts = $pipelineCounts ?? ['pending' => 0, 'placed' => 0, 'processing' => 0, 'ready' => 0, 'delivered' => 0, 'cancelled' => 0];
     $lowStockProducts = $lowStockProducts ?? collect([]);
     $topProducts = $topProducts ?? collect([]);
     ```
  2. In `SellerDashboardController.php`:
     - Line 281: change `"235 / 248 on time"` to `"0 / 0 on time (No orders yet)"`.
     - Line 283: change `"182 verified reviews"` to `"0 verified reviews"`.

---

#### 🟡 P23 — Seller Products Scope Inspection (`lowStock()` and `stale()`)
- **Inspection Result:**
  - `app/Models/Product.php` lines 207–215 defines `scopeLowStock($query)`:
    ```php
    public function scopeLowStock($query)
    {
        return $query->where(function ($q) {
            $q->whereColumn('stock', '<=', 'low_stock_threshold')
              ->orWhere(function ($sub) {
                  $sub->whereNull('low_stock_threshold')->where('stock', '<=', 10);
              });
        });
    }
    ```
  - `app/Models/Product.php` lines 189–194 defines `scopeStale($query)`:
    ```php
    public function scopeStale($query)
    {
        return $query->where('is_perishable', true)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now()->toDateString());
    }
    ```
- **Conclusion:** Both scopes are present and verified functional by `SellerProductManagementTest` and `SellerIntegrityAuditCheckTest`.
- **Optimization Recommendation:** In `seller/products/index.blade.php` lines 20–25, Blade executes queries if variables are not passed. `SellerProductController@index` already passes these counts; ensure the controller remains the single authoritative source.

---

#### 🟡 P24 — Bulk Actions in Seller Products Are `alert()` Stubs
- **Location:** `resources/views/seller/products/index.blade.php` (Lines 57–70)
  ```html
  <a href="#" @click.prevent="alert('Selected products marked active'); bulkOpen = false;" class="...">
      <span class="material-symbols-outlined text-[16px] text-on-tertiary-container">check_circle</span> Update Status: Active
  </a>
  <a href="#" @click.prevent="alert('Listing discounts triggered'); bulkOpen = false;" class="...">
      <span class="material-symbols-outlined text-[16px] text-secondary">sell</span> Apply Harvest Discount (15%)
  </a>
  <a href="#" @click.prevent="alert('Archived selected listings'); bulkOpen = false;" class="...">
      <span class="material-symbols-outlined text-[16px] text-error">archive</span> Archive Selected SKUs
  </a>
  ```
  ```html
  <button type="button" onclick="alert('Exporting products to CSV format...')" class="...">
  ```
- **Impact:** Sellers cannot perform bulk operations; clicking the actions displays JavaScript alerts with zero database persistence.
- **Actionable Remediation:**
  1. Add a bulk action route in `routes/web.php`:
     ```php
     Route::post('/products/bulk', [SellerProductController::class, 'bulkAction'])->name('products.bulk');
     ```
  2. Implement `bulkAction(Request $request)` in `SellerProductController.php` with input validation (`product_ids` array, `action` in `['active', 'discount', 'archive']`) wrapped in `DB::transaction`.
  3. Wrap the product table in a `<form>` or wire an Alpine.js submission sending selected IDs to `route('seller.products.bulk')`.

---

#### 🟡 P25 — Seller Dashboard 7-Day Revenue Chart
- **Location:** `resources/views/seller/dashboard.blade.php` (Lines 394–420)
- **Impact:** The chart renders CSS `div` bars with `style="height: ...px;"`. When all 7 days have zero revenue, each bar renders at a minimum height of 10px with zero revenue formatted. There are no ARIA labels (`role="img"`, `aria-label`).
- **Actionable Remediation:**
  1. Add accessibility labels: `aria-label="{{ $bar['day'] }}: {{ $bar['formatted'] }}"` to each bar column.
  2. If the total 7-day revenue is zero (`$chartTotalRevenue == 0`), render an accessible empty-state message: "No sales recorded in the past 7 days."

---

#### 🟡 P27 — Seller Top Header Notification Bell
- **Location:** `resources/views/layouts/seller.blade.php` (Lines 240–245)
  ```html
  <!-- Notifications -->
  <button type="button" class="relative p-2 rounded-[10px] hover:bg-surface-container-high text-on-surface-variant transition-colors" title="Notifications">
      <span class="material-symbols-outlined text-[22px]">notifications</span>
      <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-secondary-container"></span>
  </button>
  ```
- **Impact:** Completely inert; clicking has no effect.
- **Actionable Remediation:**
  Convert the `<button>` to an `<a>` tag linking to `route('seller.account.notifications')`, or wire an Alpine.js dropdown displaying recent seller alerts.

---

#### 🟡 P31 — `$featuredAuction->bids->count()` Eager Loading
- **Location:** `resources/views/index.blade.php` (Line 295)
  ```html
  {{ $featuredAuction->bids->count() }} Bids
  ```
- **Impact:** In `ProductController@home` (Line 33):
  `Auction::with(['product.primaryImage', 'product.images', 'bids'])`
  The `bids` relation is currently eager-loaded in cache. However, calling `->bids->count()` loads all bid models into memory just to obtain the count.
- **Actionable Remediation:**
  1. In `app/Http/Controllers/ProductController.php` Line 33, add `withCount('bids')`.
  2. In `resources/views/index.blade.php` Line 295, update to `{{ $featuredAuction->bids_count ?? $featuredAuction->bids->count() }} Bids`.

---

#### 🟡 P32 — `Storage::url()` Facade Import in Blade
- **Location:** `resources/views/index.blade.php` (Lines 518–519)
  ```php
  $logo = $profile->logo_path ? Storage::url($profile->logo_path) : null;
  $banner = $profile->banner_path ? Storage::url($profile->banner_path) : null;
  ```
- **Impact:** `Storage` is referenced without an explicit `use Illuminate\Support\Facades\Storage;` statement inside the `@php` block.
- **Actionable Remediation:**
  Add `use Illuminate\Support\Facades\Storage;` at the top of the `@php` block in `index.blade.php` (Line 513) or qualify with `\Illuminate\Support\Facades\Storage::url(...)`.

---

## 5. Discovered Collateral Issues

1. **Footer Legal Stubs Missing (Acceptance Criteria Mandate):**
   - Footer links for `/privacy`, `/terms`, and `/return-policy` in `components/footer.blade.php` lines 54, 63, 64 are `href="#"`.
   - The Acceptance Criteria specifically requires: *"Footer legal links (/privacy, /terms, /return-policy) respond with HTTP 200."*
   - Routes `/privacy`, `/terms`, `/return-policy` must be registered in `routes/web.php` pointing to static Blade views or controller stubs.
2. **Duplicated Floating AI Button:**
   - Both `resources/views/index.blade.php` (Line 1115) and `resources/views/components/footer.blade.php` (Line 254) render a floating docked button. Since `index.blade.php` includes `<x-footer />`, both appear stacked.
3. **Hardcoded Notification Count in Navbar:**
   - `resources/views/components/nav-user.blade.php` Line 231 hardcodes `<span ...>3 New</span>` and Line 219 always displays an amber dot regardless of unread notifications.

---

## 6. Implementation Action Plan

| Phase | Tasks | Target Files |
|-------|-------|--------------|
| **Phase 1: Asset Cleanup (R1)** | 1. Remove duplicate `<link>` in `app.blade.php` and `index.blade.php`.<br>2. Remove Tailwind CDN script & inline config in `app.blade.php`.<br>3. Bundle Alpine.js via Vite in `resources/js/app.js` and run `npm run build`.<br>4. Remove duplicate CDN Alpine scripts from `index.blade.php` and `user/products/index.blade.php`.<br>5. Add seller theme color tokens to `resources/css/app.css` and migrate `layouts/seller.blade.php` to `@vite`. | `resources/views/layouts/app.blade.php`<br>`resources/views/index.blade.php`<br>`resources/views/layouts/seller.blade.php`<br>`resources/views/user/products/index.blade.php`<br>`resources/css/app.css`<br>`resources/js/app.js` |
| **Phase 2: Route & View Wiring (R2)** | 1. Create `seller.auctions.history` method in `SellerAuctionController.php` filtering for ended auctions.<br>2. Register `seller.account.notifications` and `seller.account.settings` routes in `routes/web.php`.<br>3. Populate `seller/account/notifications.blade.php` and `seller/account/settings.blade.php`.<br>4. Register legal routes `/privacy`, `/terms`, `/return-policy` and create views.<br>5. Fix empty slug handling in `ProductController@category`. | `routes/web.php`<br>`app/Http/Controllers/Seller/SellerAuctionController.php`<br>`app/Http/Controllers/Seller/SellerProfileController.php`<br>`resources/views/seller/account/notifications.blade.php`<br>`resources/views/seller/account/settings.blade.php`<br>`app/Http/Controllers/ProductController.php` |
| **Phase 3: Controller & Data Polish (R2)** | 1. Remove fake numeric fallbacks in `seller/dashboard.blade.php` and `SellerDashboardController.php`.<br>2. Wrap seller header search bar in `<form action="{{ route('seller.products.index') }}" method="GET">`.<br>3. Wire header notification bell to `seller.account.notifications`.<br>4. Implement bulk actions endpoint in `SellerProductController.php` and wire table checkboxes.<br>5. Add `withCount('bids')` to `ProductController@home` and import `Storage` facade in `index.blade.php`.<br>6. Remove duplicate floating AI button in `index.blade.php` and wire footer button. | `resources/views/seller/dashboard.blade.php`<br>`app/Http/Controllers/Seller/SellerDashboardController.php`<br>`resources/views/layouts/seller.blade.php`<br>`app/Http/Controllers/Seller/SellerProductController.php`<br>`resources/views/seller/products/index.blade.php`<br>`app/Http/Controllers/ProductController.php`<br>`resources/views/index.blade.php`<br>`resources/views/components/footer.blade.php` |
| **Phase 4: Regression Verification** | 1. Run `php artisan test` to verify all 706+ tests pass.<br>2. Run `php artisan route:list` to ensure zero route binding errors.<br>3. Verify HTTP 200 on legal pages (`/privacy`, `/terms`, `/return-policy`).<br>4. Inspect DOM to ensure no duplicate CSS/JS tags. | CLI / Test Suites |
