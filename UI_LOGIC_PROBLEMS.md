# Bazaario — UI, Logic & Layout Problems Audit

> **Generated:** 2026-10-01 | **Scope:** All Blade views, routes, layouts, components
> **Priority:** 🔴 Critical → 🟠 High → 🟡 Medium → 🟢 Low

---

## Table of Contents

1. [Critical — Asset & Infrastructure Issues](#1-critical--asset--infrastructure-issues)
2. [Critical — Logic & Route Issues](#2-critical--logic--route-issues)
3. [High — Layout & Responsive Issues](#3-high--layout--responsive-issues)
4. [High — UI & Design Inconsistencies](#4-high--ui--design-inconsistencies)
5. [Medium — Seller Panel Issues](#5-medium--seller-panel-issues)
6. [Medium — Home Page Issues](#6-medium--home-page-issues)
7. [Medium — Product Pages Issues](#7-medium--product-pages-issues)
8. [Low — Footer, Nav & Minor Issues](#8-low--footer-nav--minor-issues)
9. [Summary Table](#9-summary-table)

---

## 1. Critical — Asset & Infrastructure Issues

### 🔴 P1 — Double CSS Load in `app.blade.php` and `index.blade.php`
**File:** `resources/views/layouts/app.blade.php` (line 24–26), `resources/views/index.blade.php` (line 16–17)

Both files load `@vite(...)` AND then **also** hardcode a static `<link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}">`. This causes the stylesheet to be loaded **twice** on every page, increasing parse time and risking style conflicts.

```html
<!-- PROBLEM: both lines exist -->
@vite(['resources/css/app.css', 'resources/js/app.js'])
<link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}">
```

**Fix:** Remove the hardcoded `<link>` fallback — `@vite()` already outputs the correct hashed filename from the manifest.

---

### 🔴 P2 — Tailwind CDN Still Used in Seller Layout (`layouts/seller.blade.php`)
**File:** `resources/views/layouts/seller.blade.php` (line 20)

The seller panel **still loads** the Tailwind CDN script, even though all other pages were migrated to Vite-compiled CSS. This:
- Loads ~100KB+ of unoptimized CSS on every seller panel page
- Overrides the compiled Tailwind config with a custom `tailwind.config` defined inline
- Creates a **dual design system** conflict — seller panel uses raw CDN Tailwind while customer pages use Vite-compiled Tailwind

```html
<!-- PROBLEM in layouts/seller.blade.php -->
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config = { ... }</script>
```

**Fix:** Replace with `@vite(['resources/css/app.css', 'resources/js/app.js'])` and move the custom color tokens into `resources/css/app.css`.

---

### 🔴 P3 — Alpine.js Loaded Twice on Products Index Page
**File:** `resources/views/user/products/index.blade.php` (line 24)

The products catalog page loads Alpine.js from the CDN (`cdn.jsdelivr.net`) even though Alpine.js is already bundled inside `app.js` via Vite. Having two Alpine instances causes **component initialization failures**, double event listener registration, and `x-data` conflicts.

```html
<!-- PROBLEM: Alpine already in app.js, this creates a second instance -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

**Fix:** Remove the CDN `<script>` tag from the page — rely on the bundled Alpine from `app.js`.

---

### 🔴 P4 — Alpine.js Loaded Twice on Home Page (`index.blade.php`)
**File:** `resources/views/index.blade.php` (line 1127)

Same as P3 — the home page footer area includes a `<script defer src="alpinejs@3.x.x">` after the Vite bundle is already loaded. This is especially dangerous because Alpine registers components like `bazaarioLocalization()` in the footer script, and loading Alpine twice causes those registrations to fail silently.

**Fix:** Remove the bottom `<script>` tag for Alpine from `index.blade.php`.

---

## 2. Critical — Logic & Route Issues

### 🔴 P5 — Missing Routes: `user.notifications.index`, `user.bids`, `user.auctions`, etc.
**File:** `routes/web.php`, `resources/views/components/nav-user.blade.php`

The nav-user component links to these routes in its dropdown:
- `route('user.bids')` → maps to `AuctionController@bids` — **may not exist**
- `route('user.auctions')` → maps to `AuctionController@index` — returns same page as `/auctions`
- `route('user.compare')` → maps to `view('user.account.compare')` — view **may not exist**
- `route('user.returns')` → view `user.account.returns` — **may not exist**
- `route('user.invoices')` → view `user.account.invoices` — **may not exist**

If any of these views are missing, clicking the nav link causes a **500 error** for logged-in users.

**Fix:** Audit the `resources/views/user/account/` folder and create stub views for missing pages (returns, invoices, compare) or redirect to a placeholder.

---

### 🔴 P6 — `"AI Compare"` Button on Home Links to Products Index (Not an AI Feature)
**File:** `resources/views/index.blade.php` (line 361–364)

The "Try AI Compare" CTA on the home page links to `route('products.index')`, which is just the product listing. There is no actual AI comparison engine. This is misleading to users and could hurt trust.

```html
<a href="{{ route('products.index') }}">Try AI Compare</a>
```

**Fix:** Either build the AI compare route/view, or rename the button to "Browse Products" until the feature is ready.

---

### 🔴 P7 — "Ask Bazaario AI" Floating Button Has No Action
**File:** `resources/views/index.blade.php` (line 1115–1121), `resources/views/components/footer.blade.php` (line 254–262)

Both the home page and footer have a `✦ Ask Bazaario AI` floating button:
- In `index.blade.php` it is an `<a>` tag linking to `route('products.index')` — not an AI chat
- In `footer.blade.php` it is a `<button type="button">` with no `@click` or `href` — completely non-functional

**Fix:** Either wire a real modal/chat interface or remove the button until the feature is implemented.

---

### 🔴 P8 — Seller Panel Search Bar Has No Action / Submit Route
**File:** `resources/views/layouts/seller.blade.php` (line 231–235)

The top header search bar in the seller panel is a plain `<input>` with no form wrapper, no `action`, and no Alpine.js `@keydown.enter` binding. Pressing Enter does nothing.

```html
<input type="text" placeholder="Search orders, products, auctions, payouts...">
```

**Fix:** Wrap it in a `<form action="{{ route('seller.products.index') }}" method="GET">` or wire an Alpine.js handler.

---

### 🔴 P9 — Hardcoded Fallback Data Leaks to Production
**File:** `resources/views/seller/dashboard.blade.php` (lines 11–68)

The seller dashboard uses PHP `??` fallback values to show **hardcoded demo data** when real data isn't available:
- `$totalOrders = $totalOrders ?? 248;`
- `$grossRevenue = $grossRevenue ?? 84520.00;`
- `$lowStockCount = $lowStockCount ?? 6;`

If the controller fails to pass these variables (e.g., a query error), sellers will see **fake numbers** that look real. This is a **logic and trust problem**.

**Fix:** Remove all numeric fallbacks and instead show a graceful empty state / zero-value with a message. Keep only `null` or `collect()` fallbacks.

---

### 🔴 P10 — `seller.auctions.history` Route Points to `index` (Duplicate)
**File:** `routes/web.php` (line 242)

```php
Route::get('/history', [SellerAuctionController::class, 'index'])->name('history');
```

Both `seller.auctions.index` (GET `/auctions/`) and `seller.auctions.history` (GET `/auctions/history`) call the exact same controller method `index()`. The sidebar shows them as two separate nav items but renders the same page.

**Fix:** Create a separate `history()` method in `SellerAuctionController` filtering for completed/ended auctions.

---

## 3. High — Layout & Responsive Issues

### 🟠 P11 — Seller Panel Has No Mobile Layout (Sidebar Breaks Below 1024px)
**File:** `resources/views/layouts/seller.blade.php`

The entire seller panel uses a **fixed 72-unit left sidebar** (`w-72`) with a main content area offset by `pl-72`. There are **no `lg:hidden` hamburger or mobile drawer** mechanisms in the seller layout. On tablets and phones, the sidebar collapses on top of content, making all seller panel pages completely unusable on mobile.

**Fix:** Add a mobile hamburger + slide-over drawer (like the customer nav-user component) to the seller layout. Set `pl-0 lg:pl-72` on the main wrapper and use `x-data` for the mobile toggle.

---

### 🟠 P12 — Home Page Navbar (`components/nav`) Not Found / Missing Reference
**File:** `resources/views/index.blade.php` (line 60)

```html
@include('components.nav')
```

The home page includes `components.nav` — but based on the file structure, the main navbar component is named `components/nav-user.blade.php`. If `components/nav.blade.php` doesn't exist or is a separate simpler component, the home page may render without any navbar or throw an error.

**Fix:** Confirm `components/nav.blade.php` exists and renders correctly. If it's a simplified navbar, document the difference clearly.

---

### 🟠 P13 — Mobile Bottom Nav Bar Overlaps Product Cards on Small Screens
**File:** `resources/views/components/nav-user.blade.php` (line 550)

The fixed mobile bottom nav bar (`fixed bottom-0 z-50`) with `py-2` doesn't account for the `pb-24` padding needed below content. The products index page uses `pb-24 md:pb-16` correctly, but many other pages (like `show.blade.php`) don't have enough bottom padding and the last product/content section is hidden behind the bottom bar.

**Fix:** Audit all pages using `nav-user` and ensure they have `pb-24 lg:pb-8` at minimum.

---

### 🟠 P14 — Hero Section: No Fallback When `screen.png` Image Fails to Load
**File:** `resources/views/index.blade.php` (line 116–118)

```html
<img src="{{ asset('images/screen.png') }}" ...>
```

There is no `onerror` fallback on this critical hero image. If `screen.png` is missing (e.g., in a fresh clone), the entire hero visual section shows a broken image placeholder.

**Fix:** Add `onerror="this.src='https://images.unsplash.com/...'"` or use a background gradient fallback.

---

### 🟠 P15 — Category Grid: Overflow on Mobile (8-Column Grid Shrinks to 2 on Small Screens)
**File:** `resources/views/index.blade.php` (line 233)

```html
<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3.5">
```

At `sm` (640px), 4-column category tiles work. But on very small phones (320px–375px), the 2-column layout with `gap-3.5` and emoji icons can produce cramped, misaligned tiles. The category name `line-clamp-1` can truncate mid-character for Bengali/Hindi category names.

**Fix:** Consider `grid-cols-2 xs:grid-cols-3 sm:grid-cols-4 lg:grid-cols-8` and test with long unicode category names.

---

### 🟠 P16 — Featured Sellers Section Missing When DB Has No Approved Sellers
**File:** `resources/views/index.blade.php` (line 512)

The `@forelse(($featuredSellers ?? []) as $seller)` has a static fallback with 4 hardcoded Unsplash seller cards. But these fallback cards link to hardcoded product search routes. If the site is in early production with real users and no featured sellers yet, the hardcoded ones mislead users.

**Fix:** The `@empty` fallback should show a "Be the first featured seller" CTA instead of fake seller cards.

---

## 4. High — UI & Design Inconsistencies

### 🟠 P17 — Two Separate Design Systems: Customer Pages vs Seller Panel
**Files:** All customer views use compiled Tailwind + custom tokens (`amber-action`, `slate-authority`, `font-display`). The seller layout uses CDN Tailwind with inline config tokens (`brand.*`, `surface-container-*`).

This means:
- Same color values (`#F5A623`, `#0F172A`) have different token names
- `font-display` (Space Grotesk) in customer views vs `font-heading` in seller panel — same font, different names
- `rounded-2xl` in customer vs `rounded-[14px]` in seller — nearly identical but inconsistent code
- Button styles are completely different between panels

**Fix:** Unify the design system into a single Tailwind config in `tailwind.config.js` and use consistent token names across all views.

---

### 🟠 P18 — Notification Bell Always Shows Hardcoded "3 New" Count
**File:** `resources/views/components/nav-user.blade.php` (line 231)

```html
<span class="text-[10px]...">3 New</span>
```

The notification count in the popover header is **hardcoded to "3 New"** regardless of actual notification data. The notification dot on the bell icon is also always shown (hardcoded amber dot).

**Fix:** Pass `$unreadNotificationsCount` from the controller or query it in the component's `@php` block. Hide the dot if count is 0.

---

### 🟠 P19 — Cart Mini-Popover Opens on `@mouseenter` (Hover) — Broken on Touch Devices
**File:** `resources/views/components/nav-user.blade.php` (line 264–266)

```html
@mouseenter="cartOpen = true"
@mouseleave="cartOpen = false"
```

The cart popover opens on mouse hover, which works on desktop but is **completely inaccessible on touch screens**. On mobile, there's no hover event — tapping the cart icon should navigate to `cart.index`, but the popover briefly flashes or blocks navigation.

**Fix:** On touch devices, `@mouseenter` doesn't fire. Consider using `@click.prevent` on mobile and only using hover on `lg:` screens.

---

### 🟠 P20 — Social Media Links in Footer Are All `href="#"` (Dead Links)
**File:** `resources/views/components/footer.blade.php` (lines 15–27)

All 4 social media icons (Instagram, X, YouTube, GitHub) link to `#`. These are dead links that should either be filled with real URLs or removed entirely.

---

### 🟠 P21 — Footer "Return Policy", "Escrow Guarantee", "Dispute Center", "Privacy Policy", "Terms of Service" Are All `href="#"` (Dead Links)
**File:** `resources/views/components/footer.blade.php` (lines 54, 61–64)

Five legal/policy footer links point to `#`. For a marketplace platform, missing Privacy Policy and ToS pages are a **legal compliance problem**, not just a UI issue.

**Fix:** Create stub `/privacy`, `/terms`, `/return-policy` pages at minimum and wire them up.

---

### 🟠 P22 — "Deals" Filter in Footer Links to Wrong Route
**File:** `resources/views/components/footer.blade.php` (line 37)

```php
href="{{ route('products.index', ['filter' => 'escrow']) }}"
```

The "Deals" footer link uses `filter=escrow` but the products index page likely handles `filter=deals`. This may cause the deals filter to silently fail to apply.

---

## 5. Medium — Seller Panel Issues

### 🟡 P23 — Seller Products Page: `lowStock()` and `stale()` Scopes May Not Exist
**File:** `resources/views/seller/products/index.blade.php` (lines 23–25)

```php
$lowStockCount = $lowStockCount ?? \App\Models\Product::where('seller_id', $sellerId)->lowStock()->count();
$staleCount = $staleCount ?? \App\Models\Product::where('seller_id', $sellerId)->stale()->count();
```

These use `->lowStock()` and `->stale()` local scopes. If these scopes are not defined on the `Product` model, this will throw a **500 error** when the controller doesn't pre-populate these variables.

**Fix:** Verify `scopeLowStock()` and `scopeStale()` exist in `app/Models/Product.php`. Add them if missing.

---

### 🟡 P24 — Bulk Actions in Seller Products Are `alert()` Stubs (Not Functional)
**File:** `resources/views/seller/products/index.blade.php` (line 58)

```js
@click.prevent="alert('Selected products marked active'); bulkOpen = false;"
```

All bulk action buttons use `alert()` as placeholders. These are not real form submissions and will not persist any changes.

**Fix:** Wire bulk action buttons to actual POST forms or Alpine.js fetch calls targeting real API routes.

---

### 🟡 P25 — Seller Dashboard Chart is CSS-Only Bar Chart (Not Accessible / Not Dynamic)
**File:** `resources/views/seller/dashboard.blade.php` (lines 34–42)

The 7-day revenue chart is rendered as CSS bars using PHP `height` percentages from hardcoded fallback data. It:
- Is not accessible (no `<canvas>` or SVG, no aria labels)
- Uses hardcoded `height` pixel values not scaled to real max revenue
- Does not update when real data is available

**Fix:** Use a lightweight chart library (Chart.js, ApexCharts, or even a proper SVG line chart) and feed real `$revenueChartData` from the controller.

---

### 🟡 P26 — Seller Account `notifications.blade.php` View Path Mismatch
**File:** `resources/views/seller/account/notifications.blade.php` exists, but there is **no route** in `routes/web.php` that points to it under the seller prefix.

The `seller.account.*` group has: `profile`, `location`, `security`, `settings` — but **no `notifications` route**.

**Fix:** Add `Route::get('/account/notifications', ...)` or remove the orphaned view file.

---

### 🟡 P27 — Seller Panel Top Header Notifications Button Has No Dropdown or Route
**File:** `resources/views/layouts/seller.blade.php` (line 241–244)

The notification bell in the seller header bar is a plain `<button type="button">` with no Alpine.js data, no `@click` handler, and no popover. Clicking it does nothing.

**Fix:** Either wire a popover (similar to customer nav-user) or link it to `route('seller.account.notifications')`.

---

### 🟡 P28 — `seller.account.settings` Route Redirects to Profile (Confusing)
**File:** `routes/web.php` (line 258)

```php
Route::get('/settings', fn () => redirect()->route('seller.account.profile'))->name('settings');
```

The sidebar shows "Settings" as a navigation item, but clicking it silently redirects to "Profile." The sidebar highlights "Settings" as active but the content shows the Profile page — confusing UX.

**Fix:** Create a real `seller/account/settings.blade.php` view with actual account settings (email preferences, notifications, 2FA, etc.).

---

## 6. Medium — Home Page Issues

### 🟡 P29 — Home Page Navbar Uses `components.nav` While Other Pages Use `components.nav-user`
**File:** `resources/views/index.blade.php` (line 60)

The home page includes `@include('components.nav')` — a **separate** minimal navbar component. All other customer-facing pages use `@include('components.nav-user')` which has the full cart, wishlist, notifications, and account dropdown.

This means logged-in users on the home page **cannot see their cart count, wishlist or account dropdown** unless `components/nav.blade.php` also implements them.

**Fix:** Unify: use `@include('components.nav-user')` on the home page too, or ensure `nav.blade.php` has feature parity.

---

### 🟡 P30 — Popular Search Chips Use Hardcoded Product Names (`iPhone 16 Pro`, `Leica M3`)
**File:** `resources/views/index.blade.php` (lines 100–108)

These chips hardcode specific product names that may not exist in the database. If a user clicks "iPhone 16 Pro" and no matching product exists, they see a blank catalog with 0 results, creating a poor first impression.

**Fix:** Populate chips dynamically from most-searched terms stored in the database, or remove product-specific names and use generic categories.

---

### 🟡 P31 — `$featuredAuction->bids->count()` — Eager Loading Not Guaranteed
**File:** `resources/views/index.blade.php` (line 295)

```php
{{ $featuredAuction->bids->count() }}
```

If `$featuredAuction` doesn't eager-load `bids`, this triggers an N+1 query. With many concurrent requests, this adds per-request database hits.

**Fix:** Ensure `$featuredAuction` is loaded with `->with('bids')` in the `ProductController::home()` method.

---

### 🟡 P32 — `Storage::url($profile->logo_path)` Called Without Facade Import in Blade
**File:** `resources/views/index.blade.php` (line 518)

```php
$logo = $profile->logo_path ? Storage::url($profile->logo_path) : null;
```

`Storage` is used directly without `use Illuminate\Support\Facades\Storage;` at the top of the `@php` block. While it may work due to global aliases, it's fragile and will throw a "Class 'Storage' not found" error if aliasing is misconfigured.

**Fix:** Add `use Illuminate\Support\Facades\Storage;` inside the `@php` block.

---

### 🟡 P33 — Nearby Stalls: Hardcoded Default Coordinates (Kolkata)
**File:** `resources/views/index.blade.php` (line 815)

```php
['lat' => $lat ?? 22.572646, 'lng' => $lng ?? 88.363895]
```

When no geolocation is passed, the system defaults to **Kolkata coordinates**. Users in Delhi, Mumbai, or anywhere else will see "nearby" stalls from Kolkata, making the hyperlocal section completely misleading.

**Fix:** Use browser Geolocation API (JavaScript) to detect the user's coordinates client-side and update the radius filter links dynamically, or default to a generic country-level view.

---

## 7. Medium — Product Pages Issues

### 🟡 P34 — Product Index: `maximum-scale=1.0, user-scalable=no` in Viewport Meta
**File:** `resources/views/user/products/index.blade.php` (line 13)

```html
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
```

`user-scalable=no` **prevents users from pinching to zoom** on mobile. This is a significant **accessibility violation** (WCAG 1.4.4) and also affects usability for low-vision users.

**Fix:** Remove `maximum-scale=1.0, user-scalable=no`. Use only `width=device-width, initial-scale=1.0`.

---

### 🟡 P35 — Product `show.blade.php` Still Has 4-Image Gallery Fallback from Unsplash
**File:** `resources/views/user/products/show.blade.php` (lines 35–41)

When no product images exist in the DB, the gallery shows 4 Unsplash leather bag images. This means **any product** without images shows leather bag photos — creating a confusing product page for electronics, food, or clothing sellers.

**Fix:** Use a generic placeholder image (e.g., a branded Bazaario placeholder) or show an icon with "No photos yet" message.

---

### 🟡 P36 — Category Filter in Nav-User Dropdown Links to Search Params (Not Slug Routes)
**File:** `resources/views/components/nav-user.blade.php` (lines 102–126)

All category mega-menu links use `['search' => 'Smartphone']` instead of the proper `route('products.index', ['category' => $slug])`. This means category filtering goes through text search rather than an indexed category column — less efficient and less accurate.

---

## 8. Low — Footer, Nav & Minor Issues

### 🟢 P37 — `components/footer.blade.php`: Currency Selector (UI) Exists But Has No Toggle Button Visible
**File:** `resources/views/components/footer.blade.php`

The `bazaarioLocalization()` Alpine component manages both language and currency switching, but the **currency dropdown button was removed** from the footer HTML. The `currOpen` state and `setCurrency()` function are fully implemented in JS but there's no button to open the currency selector.

---

### 🟢 P38 — Translation Engine (`applyTranslation`) Reloads Page on `'en'` Switch
**File:** `resources/views/components/footer.blade.php` (line 180)

```js
if (langCode === 'en') {
    window.location.reload();
    return;
}
```

Switching back to English causes a full page reload. This is jarring UX. The reload is needed because text was mutated in the DOM, but it could be avoided by restoring original text from a data-attribute cache.

---

### 🟢 P39 — `pages.how-it-works` Route Is Same as `about` Route (Duplicate)
**File:** `routes/web.php` (lines 53–54)

```php
Route::get('/how-it-works', ...)->name('pages.how-it-works');
Route::get('/about', ...)->name('about');
```

Both routes serve the same controller method `howItWorks()`. Footer "About Us" and "How It Works" link to different route names that render the same page — fine technically, but messy.

---

### 🟢 P40 — Seller Profile Page: `Auth::guard('seller')` May Return Null on Regular User Sessions
**File:** `resources/views/seller/products/index.blade.php` (line 7)

```php
$sellerUser = Auth::guard('seller')->user() ?? Auth::user();
```

If a regular `auth:user` guard is active and someone accesses a seller page (possible if middleware isn't airtight), `Auth::guard('seller')->user()` returns null and it falls back to `Auth::user()` which is the customer — potentially showing seller UI with customer data.

---

### 🟢 P41 — `user/products/category.blade.php` — Check for Missing Route
The file exists but the route `category.show` is defined as `Route::get('/category/{slug?}', ...)` with an optional slug. Accessing `/category/` without a slug may throw a model-not-found error if the controller doesn't handle the empty-slug case gracefully.

---

### 🟢 P42 — Mega Nav Menu Width (`w-[540px]`) Can Overflow on Small Laptop Screens
**File:** `resources/views/components/nav-user.blade.php` (line 97)

```html
class="absolute top-full ... w-[540px]"
```

On a 768px–900px wide laptop or tablet viewport, the 540px dropdown panel can extend beyond the viewport right edge if the "Categories" button is positioned in the left half of the screen.

**Fix:** Add `max-w-[calc(100vw-2rem)]` or reposition to open from the left edge of the button.

---

## 9. Summary Table

| # | Severity | Area | Problem |
|---|----------|------|---------|
| P1 | 🔴 Critical | Assets | Double CSS load (`@vite` + `<link>` in same file) |
| P2 | 🔴 Critical | Assets | Seller panel uses Tailwind CDN instead of compiled Vite CSS |
| P3 | 🔴 Critical | Assets | Alpine.js loaded twice on Products Index page |
| P4 | 🔴 Critical | Assets | Alpine.js loaded twice on Home page |
| P5 | 🔴 Critical | Logic | Missing user panel views (returns, invoices, compare) may 500 |
| P6 | 🔴 Critical | Logic | "AI Compare" CTA links to regular products page |
| P7 | 🔴 Critical | Logic | "Ask AI" floating button non-functional in footer |
| P8 | 🔴 Critical | Logic | Seller header search bar has no form/route |
| P9 | 🔴 Critical | Logic | Seller dashboard shows hardcoded fake revenue/order data on error |
| P10 | 🔴 Critical | Logic | `seller.auctions.history` route duplicates `seller.auctions.index` |
| P11 | 🟠 High | Layout | Seller panel has zero mobile layout — unusable on phones/tablets |
| P12 | 🟠 High | Layout | Home page includes `components.nav` instead of `components.nav-user` |
| P13 | 🟠 High | Layout | Mobile bottom nav bar hides last content section on many pages |
| P14 | 🟠 High | Layout | Hero `screen.png` has no fallback if image is missing |
| P15 | 🟠 High | Layout | Category grid can overflow or truncate on narrow screens |
| P16 | 🟠 High | Layout | Fake hardcoded seller cards shown when no real sellers exist |
| P17 | 🟠 High | Design | Two separate design systems (customer vs seller) — token name conflicts |
| P18 | 🟠 High | UI | Notification count hardcoded to "3 New" always |
| P19 | 🟠 High | UI | Cart popover uses `@mouseenter` — broken on touch devices |
| P20 | 🟠 High | UI | All social media footer links are dead (`href="#"`) |
| P21 | 🟠 High | UI | Privacy Policy, ToS, Return Policy footer links are dead |
| P22 | 🟠 High | UI | "Deals" footer filter uses wrong query param (`escrow` vs `deals`) |
| P23 | 🟡 Medium | Seller | `lowStock()` / `stale()` scopes may not exist on Product model |
| P24 | 🟡 Medium | Seller | Bulk actions use `alert()` — not real submissions |
| P25 | 🟡 Medium | Seller | Revenue chart is CSS-only with hardcoded heights — not dynamic |
| P26 | 🟡 Medium | Seller | `notifications.blade.php` exists but has no route |
| P27 | 🟡 Medium | Seller | Seller header notification bell does nothing on click |
| P28 | 🟡 Medium | Seller | Settings nav item silently redirects to Profile page |
| P29 | 🟡 Medium | Home | Home page uses different navbar (`nav`) than all other pages |
| P30 | 🟡 Medium | Home | Popular search chips have hardcoded product names that may not exist |
| P31 | 🟡 Medium | Home | `$featuredAuction->bids->count()` may trigger N+1 queries |
| P32 | 🟡 Medium | Home | `Storage::url()` called without facade import in `@php` |
| P33 | 🟡 Medium | Home | Nearby Stalls defaults to Kolkata coordinates for all users |
| P34 | 🟡 Medium | Products | `user-scalable=no` viewport — accessibility violation |
| P35 | 🟡 Medium | Products | Product detail fallback images are leather bags (wrong for all products) |
| P36 | 🟡 Medium | Products | Category nav uses text search params instead of slug-based routes |
| P37 | 🟢 Low | Footer | Currency switcher implemented in JS but no button rendered |
| P38 | 🟢 Low | Footer | Language switch back to English causes full page reload |
| P39 | 🟢 Low | Routes | `/how-it-works` and `/about` serve the same view |
| P40 | 🟢 Low | Auth | Seller views fall back to `Auth::user()` — may show customer data |
| P41 | 🟢 Low | Routes | `/category/` without slug may throw model-not-found error |
| P42 | 🟢 Low | UI | Category mega-menu 540px width can overflow on smaller laptops |

---

## Quick Wins (Fix in < 30 minutes each)

1. **P1** — Remove the hardcoded `<link>` CSS fallback from `app.blade.php` and `index.blade.php`
2. **P3/P4** — Remove CDN Alpine.js `<script>` tags from `index.blade.php` and `products/index.blade.php`
3. **P18** — Replace hardcoded "3 New" with dynamic count
4. **P34** — Remove `user-scalable=no` from viewport meta
5. **P20/P21** — Create stub legal pages and wire footer links
6. **P7** — Either wire the AI button to a modal or remove it

## High-Impact Fixes (1–3 hours each)

1. **P2** — Migrate seller layout from CDN Tailwind to Vite-compiled CSS
2. **P11** — Add mobile responsive sidebar/drawer to seller panel layout
3. **P17** — Align design tokens between customer and seller design systems
4. **P9** — Remove all hardcoded numeric fallbacks from seller dashboard

---

*Audit generated automatically by code analysis. Verify each item in a live browser before implementing fixes.*
