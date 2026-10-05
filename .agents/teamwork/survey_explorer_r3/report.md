# Requirement R3 Technical Exploration & Investigation Report
## Responsive Layout & Design System Unification (Issues P11–P16, P33, P35, P42)

**Author:** `survey_explorer_r3` (Teamwork Explorer)  
**Date:** 2026-10-01  
**Project:** Bazaario Marketplace (Laravel 12 / Vite / Tailwind v4 / Alpine.js)  
**Working Directory:** `c:\xampp\htdocs\bazaario`

---

## 1. Executive Summary

Requirement R3 mandates comprehensive responsive layout hardening, mobile drawer implementation for the seller workspace, fallback handling for assets and geo-coordinates, and design system unification across the Bazaario marketplace.

Our investigation examined 9 specific issues across 10 critical Blade templates, layout wrappers, and controllers:
1. **P11 (Seller Panel Mobile Responsiveness)**: The seller panel currently uses an immovable `w-72` fixed sidebar with a hardcoded `pl-72` content offset and `left-72` header offset. Below `1024px` (`lg`), the sidebar obstructs 288px of viewport space, the content overflows horizontally, and there is no hamburger toggle button.
2. **P12 (Home Page Navbar Inconsistency)**: `index.blade.php` includes `components.nav` while other customer views include `components.nav-user`. A circular delegation exists between `nav` and `nav-user`. When guests visit the home page, `nav.blade.php` hides the cart link (even when `session('cart')` contains items), omits the Categories mega-dropdown, and omits the navbar search bar.
3. **P14 (Hero `screen.png` Fallback)**: `public/images/screen.png` exists in the local workspace (970 KB 3D artwork), but `index.blade.php` lacks an `onerror` handler and CSS background fallback, leading to a broken image placeholder if asset resolution fails.
4. **P15 (Category Grid Mobile Overflow & Unicode Clamping)**: The 8-column category grid (`grid-cols-2 sm:grid-cols-4 lg:grid-cols-8`) produces narrow ~77px text areas at `1024px` and lacks intermediate breakpoints (`min-[440px]:grid-cols-3`). Furthermore, `line-clamp-1` violently truncates complex unicode combining grapheme clusters in Bengali and Hindi translations (Requirement R2).
5. **P16 (Featured Sellers Fallback Mock Data)**: The `@empty` fallback on `index.blade.php:589-779` contains over 190 lines of hardcoded fictitious seller cards ("Clay & Kiln Studio", "Himalayan Forest Honey", etc.) with fake stock and dead search links, misleading buyers when no approved sellers exist.
6. **P33 (Nearby Stalls Hardcoded Kolkata Coordinates)**: `index.blade.php:815`, `ProductController.php:60`, and `SellerProfile.php:84` unconditionally default coordinates to Kolkata (`[22.572646, 88.363895]`). Users in other cities or states are treated as being in Kolkata without warning or GPS detection.
7. **P13 (Mobile Bottom Nav Bar Overlap)**: The fixed mobile bottom navigation bar (`fixed bottom-0 z-50`) in `nav-user.blade.php` and `nav.blade.php` obscures content on mobile devices because `layouts/user.blade.php` has no bottom padding (`pb-0`), and `products/show.blade.php` only has `pb-6`.
8. **P35 (Product Show Page Leather Bag Fallbacks)**: When a product has no uploaded images, `products/show.blade.php:35-41` falls back to 4 Unsplash photos of a leather messenger bag, regardless of whether the product is electronics, groceries, or handicrafts.
9. **P42 (Category Mega Menu 540px Overflow)**: `nav-user.blade.php:97` sets fixed `w-[540px]` without `max-w-[calc(100vw-2rem)]`, risking viewport overflow on compact laptop screens (768px–1024px).

---

## 2. In-Depth Codebase Observations & Root Causes

### 2.1 Issue P11: Seller Panel Mobile Responsiveness
- **Target File:** `resources/views/layouts/seller.blade.php`
- **Observed Lines:**
  - Line 97: `<aside class="fixed left-0 top-0 h-screen w-72 bg-surface-container-low z-50 flex flex-col justify-between py-4 px-3 shadow-[0_1px_8px_rgba(0,0,0,0.04)] border-r border-[#E2DFD7]/60">`
  - Line 226: `<div class="pl-72">`
  - Line 228: `<header class="fixed top-0 left-72 right-0 h-16 bg-surface/85 backdrop-blur-xl border-b border-[#E2DFD7]/60 shadow-[0_1px_8px_rgba(0,0,0,0.02)] z-40 flex items-center justify-between px-6 sm:px-8">`
  - Line 324: `<main class="relative pt-16 bg-surface min-h-screen w-full px-6 sm:px-8 py-8">`
- **Defects Identified:**
  1. The `<aside>` sidebar is unconditionally fixed at `left: 0` with `w-72` (288px) across all viewports. There are no responsive visibility or transform classes (no `lg:flex`, no `-translate-x-full lg:translate-x-0`).
  2. On a standard mobile screen (e.g., iPhone 375px or Android 360px), the sidebar covers 80% to 90% of the screen.
  3. The main wrapper `<div>` at line 226 has a hardcoded padding-left `pl-72` (288px), pushing the entire page content 288px to the right. On a 375px screen, only `375px - 288px = 87px` of visible width remains for the content canvas, causing severe horizontal scrolling and breaking table/grid layouts.
  4. The fixed top header at line 228 is anchored to `left-72 right-0`. On mobile, it starts 288px from the left, cutting off the global search bar and user profile badges.
  5. There is no hamburger toggle button (`<button class="lg:hidden ...">`) in the header or layout. Once on a small screen, the user cannot hide the sidebar or access header controls.

### 2.2 Issue P12: Home Page Navbar Component Inconsistencies
- **Target Files:**
  - `resources/views/index.blade.php` (line 60)
  - `resources/views/components/nav.blade.php` (lines 1–35, 68–102)
  - `resources/views/components/nav-user.blade.php` (lines 1–40, 74–97)
- **Observed Lines:**
  - `index.blade.php:60`: `@include('components.nav')`
  - `components/nav.blade.php:9-15`:
    ```php
    $currentUser = Auth::guard('user')->user() ?? Auth::guard('seller')->user();
    if ($currentUser && !defined('NAV_DELEGATING')) {
        define('NAV_DELEGATING', true);
        echo view('components.nav-user', get_defined_vars())->render();
        return;
    }
    ```
  - `components/nav-user.blade.php:16-20`:
    ```php
    if (!$currentUser && !defined('NAV_DELEGATING')) {
        define('NAV_DELEGATING', true);
        echo view('components.nav', get_defined_vars())->render();
        return;
    }
    ```
- **Defects Identified:**
  1. **Circular Delegation & Dual Maintenance:** `nav.blade.php` delegates to `nav-user.blade.php` when a user is logged in, while `nav-user.blade.php` delegates to `nav.blade.php` when a user is unauthenticated (guest).
  2. **Guest Cart Suppression in `nav.blade.php`:** Line 91 of `components/nav.blade.php` wraps the desktop Cart icon in `@if($currentUser)`. If a guest adds products to their session cart (`session('cart')`), the cart icon is completely hidden on any page using `nav.blade.php` (including `index.blade.php`). In contrast, `nav-user.blade.php` lines 260–290 properly displays the cart icon and badge for guests whenever `$cartCount > 0`.
  3. **Missing Navigation Features in `nav.blade.php`:** For guests, `nav.blade.php` only provides "Shop" and "⚡ Auctions" (lines 68–85). It lacks the Categories mega-dropdown, the navbar search input, and the wishlist icon present in `nav-user.blade.php`.
  4. **Container Width Discrepancy:** `nav.blade.php:48` uses `max-w-6xl`, while `nav-user.blade.php:54` uses `max-w-7xl`, causing a layout shift when logging in or navigating between the homepage and catalog.

### 2.3 Issue P14: Hero `screen.png` Image Fallback
- **Target File:** `resources/views/index.blade.php` (lines 114–118)
- **Observed Lines:**
  ```html
  <div class="relative aspect-[16/11] w-full rounded-2xl overflow-hidden bg-gradient-to-br from-amber-50/60 to-orange-50/40">
      <!-- Official 3D Isometric Artwork -->
      <img alt="Bazaario 3D Marketplace Platform"
           class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105"
           src="{{ asset('images/screen.png') }}">
  ```
- **Defects Identified:**
  1. Verification in filesystem: `public/images/screen.png` exists locally (970 KB PNG file).
  2. However, there is no `onerror` attribute or fallback DOM node. If the image fails to resolve in production (e.g. symlink misconfiguration, CDN rate limits, or deployment artifact filtering), the entire hero visual collapses into an empty box with a broken image icon.
  3. The underlying background `from-amber-50/60 to-orange-50/40` is too faint to serve as a meaningful visual placeholder if the image fails.

### 2.4 Issue P15: Category Grid Mobile Overflow & Unicode Clamping
- **Target File:** `resources/views/index.blade.php` (lines 231–250)
- **Observed Lines:**
  ```html
  <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3.5">
      @forelse(($dbCategories ?? collect())->take(8) as $catIndex => $cat)
          ...
          <a class="glass-panel hover:bg-white p-4 rounded-2xl flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group" ...>
              <div class="w-14 h-14 rounded-2xl bg-gradient-to-br {{ $catColor }} flex items-center justify-center mb-2.5 ...">
                  <span class="text-2xl">{{ $catEmoji }}</span>
              </div>
              <span class="font-display font-bold text-xs text-slate-900 line-clamp-1">{{ $cat->name }}</span>
              <span class="font-mono text-[10px] text-slate-500 mt-0.5">{{ $cat->products_count ?? 0 }} items</span>
          </a>
  ```
- **Defects Identified:**
  1. **Grid Column Bottleneck at 1024px Desktop (`lg`):** At `1024px` viewport width, inside a container with `px-4 sm:px-6` (48px total padding), the usable width is `976px`. Divided among 8 columns with 7 gaps of 14px (98px total gap), each category tile is only `(976 - 98) / 8 = 109.75px` wide. With `p-4` (32px horizontal padding), the available text width is just `77.75px`.
  2. **Mobile Viewport Clamping (320px–375px):** On small mobile viewports, 2 columns with `gap-3.5` and `p-4` leaves only ~105px to ~132px for content. The jump from 2 columns directly to 4 columns at `sm: 640px` misses an intermediate 3-column layout for phablets / large phones (`440px–639px`).
  3. **Unicode Grapheme Truncation (`line-clamp-1`):** Category names such as "Artisan & Handmade Crafts", "Rare Collectibles & Antiques", or "Books & Fine Stationery" are severely truncated. More critically, under Vernacular Localization (Requirement R2):
     - Bengali: "বই ও ফাইন স্টেশনারি", "হস্তশিল্প ও কারুশিল্প", "বিরল সংগ্রহযোগ্য ও অ্যান্টিক"
     - Hindi: "किताबें और स्टेशनरी", "हस्तशिल्प और कलाकृतियां", "दुर्लभ संग्रहणीय वस्तुएं"
     Truncating complex multi-byte Brahmic scripts with `line-clamp-1` across a 77px width cuts through combining vowel signs (hasant, matras, nuktas) and conjunct consonants, rendering mutilated glyphs or visual glitches.

### 2.5 Issue P16: Featured Sellers Fallback & Mock Data Leak
- **Target File:** `resources/views/index.blade.php` (lines 511–780)
- **Observed Lines:**
  - Line 512: `@forelse(($featuredSellers ?? []) as $seller)`
  - Lines 589–779: `@empty` block contains 4 hardcoded cards:
    - "Clay & Kiln Studio" (Jaipur, Rajasthan, 48 products, 98% trust)
    - "Himalayan Forest Honey" (Dehradun, Uttarakhand, 16 products, 99% trust)
    - "Bengal Loom & Charkha" (Santiniketan, West Bengal, 32 products, 97% trust)
    - "Kerala Spice Trail" (Wayanad, Kerala, 24 products, 99% trust)
- **Defects Identified:**
  1. The fallback cards contain mock data that does not exist in the database.
  2. The buttons link to keyword searches (`route('products.index', ['search' => 'Ceramics'])`) disguised as verified stall links (`Visit Stall`).
  3. For a live platform, displaying fictitious sellers damages platform credibility and misleads users.
  4. The mock code bloats `index.blade.php` by 190 lines of static markup.

### 2.6 Issue P33: Nearby Stalls Hardcoded Default Coordinates
- **Target Files:**
  - `resources/views/index.blade.php` (lines 814–820, 891)
  - `app/Http/Controllers/ProductController.php` (lines 60–63)
  - `app/Models/SellerProfile.php` (lines 72, 84, 88, 100)
- **Observed Lines:**
  - `index.blade.php:815`:
    ```html
    <a href="{{ route('home', ['radius' => $rVal, 'lat' => $lat ?? 22.572646, 'lng' => $lng ?? 88.363895]) }}#nearby-stalls" ...>
    ```
  - `ProductController.php:60`:
    ```php
    $lat = (float)$request->input('lat', 22.572646); // Default Kolkata coordinates
    $lng = (float)$request->input('lng', 88.363895);
    ```
- **Defects Identified:**
  1. If a user visits the homepage without location parameters, the application hardcodes coordinates to Kolkata (`[22.572646, 88.363895]`).
  2. Users browsing from Mumbai, Bangalore, Delhi, Jaipur, or Contai are shown "Nearby Stalls" in West Bengal. A stall 15 km away from Kolkata is flagged as "1.2 km away" even if the visitor is 1,500 km away in Bangalore.
  3. Authenticated users who have saved delivery addresses in other cities (e.g. Mumbai) are ignored—the controller does not inspect the authenticated user's address records.
  4. The UI never informs the visitor that Kolkata is being used as a default, nor provides a 1-click option to trigger browser geolocation.

### 2.7 Issue P13: Mobile Bottom Navigation Bar Overlap & Spacing Audit
- **Target Files:**
  - `resources/views/components/nav-user.blade.php` (lines 550–576)
  - `resources/views/components/nav.blade.php` (lines 320–348)
  - `resources/views/layouts/user.blade.php` (line 136)
  - `resources/views/user/products/show.blade.php` (line 153)
  - `resources/views/user/products/category.blade.php` (line 42)
  - `resources/views/user/account/bids.blade.php` (line 81)
  - `resources/views/user/account/auctions.blade.php` (line 94)
  - `resources/views/user/account/auction-show.blade.php` (line 66)
  - `resources/views/index.blade.php` (line 62)
- **Observed Lines:**
  - `nav-user.blade.php:550`:
    ```html
    <div class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200 py-2 px-4 flex items-center justify-around text-slate-600 text-[10px] font-semibold">
    ```
  - `layouts/user.blade.php:136`:
    ```html
    <main class="w-full pt-0 bg-canvas-ivory flex-1 flex flex-col">
    ```
  - `user/products/show.blade.php:153`:
    ```html
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-10 flex-1 w-full">
    ```
- **Defects Identified:**
  1. The mobile bottom nav bar is fixed at `bottom: 0` with `z-50` and an effective height of 56px–72px (including padding and icons).
  2. In `layouts/user.blade.php`, `<main>` has zero bottom padding (`pt-0` only). As a result, all 17 child account and checkout views (`user/cart/index.blade.php`, `user/checkout/index.blade.php`, `user/account/dashboard.blade.php`, `user/account/orders/index.blade.php`, etc.) have their lowest form buttons, pagination links, or summary totals covered by the fixed bottom bar on mobile screens.
  3. In `user/products/show.blade.php`, `<main>` uses `py-6` (24px padding bottom). The related products carousel, review form, and sticky add-to-cart buttons collide with the bottom navigation bar.
  4. Standalone customer views (`category.blade.php`, `bids.blade.php`, `auctions.blade.php`, `auction-show.blade.php`) only use `pb-16` (64px), which does not account for mobile browser UI bars (e.g. Safari address bar + iOS home indicator).

### 2.8 Issue P35: Product Show Page Image Gallery Fallbacks
- **Target File:** `resources/views/user/products/show.blade.php` (lines 34–41, 190–225)
- **Observed Lines:**
  ```php
  if (empty($galleryImages)) {
      $galleryImages = [
          'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=800&auto=format&fit=crop&q=80',
          'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&auto=format&fit=crop&q=80',
          'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?w=800&auto=format&fit=crop&q=80',
          'https://images.unsplash.com/photo-1544816155-12df9643f363?w=800&auto=format&fit=crop&q=80',
      ];
  }
  ```
  - Line 195: `onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=800&auto=format&fit=crop&q=80';"`
  - Line 220: `onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400&auto=format&fit=crop&q=80';"`
- **Defects Identified:**
  1. The 4 fallback URLs are Unsplash photos of a leather messenger bag. If a merchant lists tea, electronics, honey, or pottery without images, buyers see leather bags.
  2. When `$galleryImages` falls back to these mock images, the thumbnail rail (line 212) renders 4 separate thumbnail buttons and the cycling arrows (lines 199–208) remain active.
  3. Reliance on external third-party Unsplash links introduces latency, tracking cookies, and potential HTTP 404/429 failures. Local category images already exist in `public/images/categories/` (`electronics.jpg`, `artisan_craft.jpg`, `fashion.jpg`, `home_living.jpg`, etc.) and `public/images/bazaario-logo.png`.

### 2.9 Issue P42: Category Mega Menu 540px Overflow
- **Target File:** `resources/views/components/nav-user.blade.php` (line 97)
- **Observed Lines:**
  ```html
  <div x-cloak x-show="categoriesOpen" ...
       class="absolute top-full mt-2 left-0 w-[540px] bg-white rounded-2xl shadow-2xl border border-slate-200 p-6 z-50 grid grid-cols-3 gap-6">
  ```
- **Defects Identified:**
  1. The mega menu panel has a fixed width `w-[540px]` with no viewport-relative ceiling (`max-w-[calc(100vw-2rem)]`).
  2. The parent desktop `<nav>` is active at `lg:` (1024px). On a 1024px screen (e.g. tablet landscape or laptop with 125% DPI display scaling), the "Categories" button is offset from the left edge of the page by approximately 220px.
  3. `220px + 540px = 760px`. When combined with the global search bar (`max-w-xs` = 320px) and the right-hand action icons (cart, profile, currency = 240px), the total horizontal footprint exceeds 1024px.
  4. If the browser window is resized between 1024px and 1150px, the 540px dropdown can cause horizontal overflow clipping and trigger unwanted scrollbars.

---

## 3. Design System Alignment & Token Mapping

An underlying driver of the layout inconsistencies is the divergence between the customer front-end design system and the seller workspace:

| Attribute | Customer Frontend (`resources/css/app.css` & `layouts/user.blade.php`) | Seller Panel (`layouts/seller.blade.php`) | Target Unified Token |
|---|---|---|---|
| **Tailwind Engine** | Vite-compiled Tailwind v4 (`@import 'tailwindcss'`) | CDN script (`cdn.tailwindcss.com`) + inline config | Vite-compiled Tailwind v4 |
| **Primary Slate** | `--color-slate-authority: #0F172A;` (`slate-authority`) | `primary: "#0F172A"`, `brand.slate: '#0F172A'` | `slate-authority` / `primary` alias |
| **Brand Accent** | `--color-amber-action: #F5A623;` (`amber-action`) | `brand.amber: '#F5A623'`, `secondary-container: '#feae2c'` | `amber-action` / `brand-amber` |
| **Status Green** | `--color-status-green: #16A34A;` (`status-green`) | `brand.green: '#16A34A'`, `on-tertiary-container: '#009842'` | `status-green` |
| **Canvas Background** | `--color-canvas-ivory: #FFFDF8;` (`canvas-ivory`) | `brand.bg: '#FFFDF8'`, `surface: '#fbf9f4'` | `canvas-ivory` / `surface` |
| **Headings Font** | `--font-display: 'Space Grotesk'` (`font-display`) | `fontFamily.heading: ['Space Grotesk']` (`font-heading`) | Dual alias (`font-display` & `font-heading`) |
| **Body Font** | `--font-sans: 'Inter'` (`font-sans`) | `fontFamily.sans: ['Inter']` (`font-sans`) | `font-sans` |
| **Monospace Font** | `--font-mono: 'JetBrains Mono'` (`font-mono`) | `fontFamily.mono: ['JetBrains Mono']` (`font-mono`) | `font-mono` |
| **Card Border Radius** | `--radius-card: 1rem;` (`rounded-card` = 16px) | `borderRadius.xl: '14px'`, `custom: '14px'` | `rounded-2xl` (16px) / `rounded-xl` (14px) |

By declaring the seller panel surface and container tokens in `resources/css/app.css` under `@theme`, the seller layout can retire `cdn.tailwindcss.com` and use the compiled Vite bundle cleanly without token conflicts.

---

## 4. Concrete Implementation Blueprint & Recommendations

### 4.1 Solution for Issue P11: Seller Panel Mobile Drawer & Shell Adjustment

**Target File:** `resources/views/layouts/seller.blade.php`

1. **Root Alpine.js State:**
   In `<body class="bg-surface font-sans antialiased min-h-screen" x-data="{ mobileNavOpen: false }">`.

2. **Mobile Backdrop:**
   Insert right before `<aside>`:
   ```html
   <!-- Mobile Slide-Over Backdrop -->
   <div x-cloak 
        x-show="mobileNavOpen" 
        @click="mobileNavOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 lg:hidden">
   </div>
   ```

3. **Responsive Aside Sidebar:**
   Update `<aside>` classes at line 97:
   ```html
   <aside class="fixed left-0 top-0 h-screen w-72 bg-surface-container-low z-50 flex flex-col justify-between py-4 px-3 shadow-[0_1px_8px_rgba(0,0,0,0.04)] border-r border-[#E2DFD7]/60 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0"
          :class="mobileNavOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
   ```
   Add a mobile close button in the sidebar brand header (lines 100–108):
   ```html
   <div class="flex items-center justify-between">
       <a href="{{ route('seller.dashboard') }}" class="px-3 py-2 flex items-center gap-3 group">
           <div class="w-9 h-9 bg-primary rounded-[12px] flex items-center justify-center text-brand-amber font-heading font-bold text-xl shadow-sm transition-transform group-hover:scale-105">
               B
           </div>
           <div class="flex flex-col">
               <span class="font-heading text-lg font-bold tracking-tight text-primary leading-tight">BAZAARIO</span>
               <span class="font-mono text-[10px] tracking-wider uppercase text-secondary font-bold">SELLER CENTER</span>
           </div>
       </a>
       <button type="button" @click="mobileNavOpen = false" class="lg:hidden p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high transition-colors" aria-label="Close menu">
           <span class="material-symbols-outlined text-[20px]">close</span>
       </button>
   </div>
   ```

4. **Main Wrapper & Fixed Header Offsets:**
   - Change line 226 from `<div class="pl-72">` to:
     `<div class="pl-0 lg:pl-72 min-h-screen transition-all duration-300">`
   - Change line 228 from `<header class="fixed top-0 left-72 right-0 ...">` to:
     `<header class="fixed top-0 left-0 lg:left-72 right-0 h-16 bg-surface/85 backdrop-blur-xl border-b border-[#E2DFD7]/60 shadow-[0_1px_8px_rgba(0,0,0,0.02)] z-30 flex items-center justify-between px-4 sm:px-6 lg:px-8 transition-all duration-300">`
   - Add hamburger button in header (line 230):
     ```html
     <div class="flex items-center gap-2 sm:gap-3 flex-1 max-w-lg min-w-0">
         <button type="button" 
                 @click="mobileNavOpen = true" 
                 class="lg:hidden p-2 rounded-xl text-on-surface-variant hover:bg-surface-container-high transition-colors shrink-0" 
                 aria-label="Open navigation menu">
             <span class="material-symbols-outlined text-[24px]">menu</span>
         </button>
         <!-- Global Search -->
         ...
     </div>
     ```
   - Update main content canvas at line 324:
     `<main class="relative pt-16 bg-surface min-h-screen w-full px-4 sm:px-6 lg:px-8 py-6 sm:py-8">`

---

### 4.2 Solution for Issue P12: Home Page Navbar Parity

**Target Files:** `resources/views/index.blade.php:60`, `resources/views/components/nav.blade.php`, `resources/views/components/nav-user.blade.php`

1. **Guest Cart Visibility in `components/nav.blade.php`:**
   In `nav.blade.php` lines 91–102 and lines 333–343, remove the strict `@if($currentUser)` guard so guests who add items to cart (`count(session('cart', [])) > 0`) can see and click the cart icon with its live badge count:
   ```html
   <!-- Cart Icon (Desktop) — Available for both Authenticated and Guest Users -->
   @if($currentUser || $cartCount > 0)
   <a href="{{ route('cart.index') }}" 
      class="relative p-2 text-slate-700 hover:text-slate-900 rounded-full hover:bg-slate-100 transition-colors flex items-center"
      aria-label="Cart">
       <span class="material-symbols-outlined text-2xl">shopping_cart</span>
       @if($cartCount > 0)
           <span class="absolute top-1 right-1 w-4 h-4 bg-slate-900 text-amber-400 rounded-full text-[10px] font-bold flex items-center justify-center shadow-xs">
               {{ $cartCount }}
           </span>
       @endif
   </a>
   @endif
   ```
2. **Standardize Navbar Container Width:**
   Ensure both `nav.blade.php:48` and `nav-user.blade.php:54` use `max-w-7xl mx-auto` to eliminate width popping during page navigation.

---

### 4.3 Solution for Issue P14: Hero `screen.png` Fallback

**Target File:** `resources/views/index.blade.php` (lines 114–118)

Update the hero image element to include an error handler and provide an enriched fallback gradient on the parent frame:
```html
<div class="relative aspect-[16/11] w-full rounded-2xl overflow-hidden bg-gradient-to-br from-amber-100/90 via-orange-50/70 to-slate-100 shadow-inner">
    <!-- Official 3D Isometric Artwork with Verified Fallback -->
    <img alt="Bazaario 3D Marketplace Platform"
         class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105"
         src="{{ asset('images/screen.png') }}"
         onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=1200&auto=format&fit=crop&q=80';">
```

---

### 4.4 Solution for Issue P15: Responsive Category Grid & Label Clamping

**Target File:** `resources/views/index.blade.php` (lines 233–250)

Update the grid layout and card typography:
```html
<div class="grid grid-cols-2 min-[440px]:grid-cols-3 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-8 gap-2.5 sm:gap-3.5">
    @forelse(($dbCategories ?? collect())->take(8) as $catIndex => $cat)
        @php
            $catEmoji = $catEmojis[$cat->name] ?? '🛍️';
            $catColor = $catColors[$catIndex % count($catColors)];
        @endphp
        <a class="glass-panel hover:bg-white p-2.5 sm:p-3.5 lg:p-3 rounded-2xl flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group"
           href="{{ route('products.index', ['category' => $cat->slug]) }}">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-br {{ $catColor }} flex items-center justify-center mb-2 group-hover:scale-110 transition-transform shadow-xs">
                <span class="text-2xl">{{ $catEmoji }}</span>
            </div>
            <span class="font-display font-bold text-[11px] sm:text-xs text-slate-900 line-clamp-2 h-7 sm:h-8 flex items-center justify-center text-center leading-tight">
                {{ $cat->name }}
            </span>
            <span class="font-mono text-[10px] text-slate-500 mt-1">{{ $cat->products_count ?? 0 }} items</span>
        </a>
    @empty
        <div class="col-span-full text-center py-8 text-slate-500 font-sans text-sm">No categories active right now.</div>
    @endforelse
</div>
```

---

### 4.5 Solution for Issue P16: Clean Empty-State CTA for Featured Sellers

**Target File:** `resources/views/index.blade.php` (lines 589–779)

Replace the 190 lines of mock seller cards inside `@empty` with a high-conversion onboarding banner:
```html
@empty
<div class="col-span-full glass-panel rounded-3xl p-8 sm:p-12 text-center bg-white/70 backdrop-blur-md border border-slate-200/80 shadow-sm flex flex-col items-center">
    <div class="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-600 flex items-center justify-center mb-4 shadow-xs">
        <span class="material-symbols-outlined text-3xl">storefront</span>
    </div>
    <h3 class="font-display font-bold text-slate-900 text-lg sm:text-xl">Be the First Featured Stall on Bazaario</h3>
    <p class="text-xs sm:text-sm text-slate-600 mt-1.5 max-w-md mx-auto leading-relaxed">
        Are you a verified farmer, artisan, or local merchant? Open your digital stall today to reach thousands of buyers with escrow-backed payments and zero hidden fees.
    </p>
    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
        <a href="{{ route('seller.register') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white font-semibold text-xs sm:text-sm transition-all shadow-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">add_business</span>
            <span>Open Your Stall Today</span>
        </a>
        <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm transition-colors">
            Browse All Marketplace Products
        </a>
    </div>
</div>
@endforelse
```

---

### 4.6 Solution for Issue P33: Graceful Hyperlocal Location Fallback

**Target Files:** `app/Http/Controllers/ProductController.php:59-86`, `resources/views/index.blade.php:811-821`

1. **Controller Logic Enhancement (`ProductController.php`):**
   ```php
   // Check explicit query parameters
   $hasExplicitLocation = $request->filled('lat') && $request->filled('lng');
   
   if ($hasExplicitLocation) {
       $lat = (float)$request->input('lat');
       $lng = (float)$request->input('lng');
       $locationLabel = 'Your Location';
   } else {
       // Check authenticated user's default delivery address
       $defaultAddress = Auth::guard('user')->user()?->addresses()->where('is_default', 1)->first()
           ?? Auth::guard('user')->user()?->addresses()->first();
           
       if ($defaultAddress && $defaultAddress->city) {
           [$lat, $lng] = SellerProfile::getCoordinatesForCity($defaultAddress->city);
           $locationLabel = $defaultAddress->city;
       } else {
           // Graceful national fallback (defaults to Kolkata coordinates)
           $lat = 22.572646;
           $lng = 88.363895;
           $locationLabel = 'Kolkata (Default)';
       }
   }
   $radius = (float)$request->input('radius', 100);
   ```

2. **View Logic Enhancement (`index.blade.php:811-821`):**
   Add a location context pill and GPS detection button:
   ```html
   <div class="flex flex-wrap items-center gap-2">
       <!-- Location indicator & GPS prompt -->
       <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 border border-slate-200/80 text-slate-700 text-xs font-medium"
            x-data="{ detecting: false }">
           <span class="material-symbols-outlined text-[15px] text-amber-600">location_on</span>
           <span>{{ $locationLabel ?? 'Kolkata (Default)' }}</span>
           <button type="button" 
                   @click="detecting = true; navigator.geolocation.getCurrentPosition(
                       pos => { window.location.href = '{{ route('home') }}?lat=' + pos.coords.latitude + '&lng=' + pos.coords.longitude + '&radius={{ $radius ?? 100 }}#nearby-stalls'; },
                       err => { detecting = false; alert('Location permission denied or unavailable.'); },
                       { timeout: 7000 }
                   )"
                   class="ml-1 text-[11px] font-bold text-amber-600 hover:text-amber-700 underline underline-offset-2 flex items-center gap-1">
               <span class="material-symbols-outlined text-[13px]" :class="{ 'animate-spin': detecting }">my_location</span>
               <span x-text="detecting ? 'Detecting...' : 'Detect'">Detect</span>
           </button>
       </div>

       <!-- Radius Filter Pills -->
       <div class="flex items-center gap-1.5 bg-white/80 backdrop-blur-md p-1.5 rounded-2xl border border-slate-200/90 shadow-2xs">
           <span class="font-mono text-[10px] uppercase font-bold text-slate-400 px-2">Radius:</span>
           @foreach([15 => '15 km', 50 => '50 km', 100 => '100 km', 500 => 'Statewide'] as $rVal => $rLabel)
               <a href="{{ route('home', array_filter(['radius' => $rVal, 'lat' => request('lat'), 'lng' => request('lng')])) }}#nearby-stalls" 
                  class="px-3 py-1 rounded-xl text-xs font-semibold transition-all {{ ($radius ?? 100) == $rVal ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                   {{ $rLabel }}
               </a>
           @endforeach
       </div>
   </div>
   ```

---

### 4.7 Solution for Issue P13: Mobile Bottom Nav Padding Audit

1. **`resources/views/layouts/user.blade.php:136`:**
   Change:
   ```html
   <main class="w-full pt-0 bg-canvas-ivory flex-1 flex flex-col">
   ```
   To:
   ```html
   <main class="w-full pt-0 pb-24 lg:pb-8 bg-canvas-ivory flex-1 flex flex-col">
   ```
   *Impact:* Instantly resolves bottom navigation bar collision across all 17 user account, checkout, and cart pages.

2. **`resources/views/user/products/show.blade.php:153`:**
   Change:
   ```html
   <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-10 flex-1 w-full">
   ```
   To:
   ```html
   <main class="max-w-7xl mx-auto px-4 sm:px-6 pt-6 pb-24 lg:pb-8 space-y-10 flex-1 w-full">
   ```

3. **Standalone Customer Views:**
   - `user/products/category.blade.php:42`: change `pb-16` to `pb-24 lg:pb-16`.
   - `user/account/bids.blade.php:81`: change `pb-16` to `pb-24 lg:pb-16`.
   - `user/account/auctions.blade.php:94`: change `pb-16` to `pb-24 lg:pb-16`.
   - `user/account/auction-show.blade.php:66`: change `pb-16` to `pb-24 lg:pb-16`.
   - `index.blade.php:62`: change `pb-16` to `pb-24 lg:pb-16`.

---

### 4.8 Solution for Issue P35: Branded Category Image Fallbacks on Product Show

**Target File:** `resources/views/user/products/show.blade.php` (lines 34–43, 195, 212–220)

1. **Category-Aware Local Fallback Resolution (lines 34–43):**
   ```php
   if (empty($galleryImages)) {
       $catSlug = $prod?->category?->slug ?? 'artisan_craft';
       $categoryImagePath = 'images/categories/' . $catSlug . '.jpg';
       
       if (file_exists(public_path($categoryImagePath))) {
           $galleryImages = [asset($categoryImagePath)];
       } else {
           $galleryImages = [asset('images/categories/artisan_craft.jpg')];
       }
   }
   $mainImage = $galleryImages[0];
   ```

2. **Update Image Error Handlers (lines 195, 220):**
   Replace external Unsplash leather bag fallback with local asset fallback:
   ```html
   onerror="this.onerror=null; this.src='{{ asset('images/categories/artisan_craft.jpg') }}';"
   ```

3. **Conditional Thumbnail & Controls Rendering (line 199, 212):**
   Wrap the previous/next arrows and thumbnail strip in `@if(count($galleryImages) > 1)` so that single-image products do not render redundant controls or clone thumbnails.

---

### 4.9 Solution for Issue P42: Constraining Mega Menu Dropdown Width

**Target File:** `resources/views/components/nav-user.blade.php` (line 97)

Change:
```html
<div x-cloak x-show="categoriesOpen"
     x-transition:enter="transition ease-out duration-150 transform"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-100 transform"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-2"
     class="absolute top-full mt-2 left-0 w-[540px] bg-white rounded-2xl shadow-2xl border border-slate-200 p-6 z-50 grid grid-cols-3 gap-6">
```
To:
```html
<div x-cloak x-show="categoriesOpen"
     x-transition:enter="transition ease-out duration-150 transform"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-100 transform"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-2"
     class="absolute top-full mt-2 left-0 w-[540px] max-w-[calc(100vw-2rem)] sm:max-w-[540px] bg-white rounded-2xl shadow-2xl border border-slate-200 p-5 sm:p-6 z-50 grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6">
```
*Impact:* Guarantees the dropdown fits within the visible viewport bounds on 768px–1024px displays and wraps cleanly without horizontal overflow.

---

## 5. Verification & Test Plan

To independently verify all proposed fixes during and after implementation:

1. **Automated Unit & Feature Tests:**
   ```bash
   php artisan test --filter=CatalogAndDiscoveryTest
   php artisan test --filter=Milestone2EmpiricalChallengeTest
   php artisan test
   ```
2. **Route List Integrity:**
   ```bash
   php artisan route:list --path=seller
   php artisan route:list --path=products
   ```
3. **Template Syntax Linter:**
   ```bash
   php -l resources/views/layouts/seller.blade.php
   php -l resources/views/index.blade.php
   php -l resources/views/components/nav.blade.php
   php -l resources/views/components/nav-user.blade.php
   php -l resources/views/user/products/show.blade.php
   ```
4. **Responsive DOM & Viewport Checks:**
   - **Seller Panel (< 1024px):** Verify hamburger icon renders at 375px; clicking opens drawer with backdrop; clicking backdrop or close button closes drawer; main wrapper margin is 0 on mobile and 288px on `lg`.
   - **Homepage Category Grid:** Verify 3 columns render at 480px, 4 columns at 640px, 8 columns at 1024px; verify Bengali (`/lang/bn`) and Hindi (`/lang/hi`) category titles render on two lines without glyph corruption.
   - **Nearby Stalls:** Verify "Detect Location" button prompts browser GPS; verify radius links toggle cleanly without hardcoding Kolkata coordinates.
   - **Mobile Bottom Nav Padding:** Verify scrolling to the bottom of `/cart`, `/checkout`, `/account/orders`, and `/products/{slug}` displays the final elements above the 56px bottom navigation bar.
