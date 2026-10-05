# Comprehensive Technical Exploration Report: Requirement R4 & Design System Unification

**Date:** 2026-10-01  
**Author:** survey_explorer_r4 (`teamwork_preview_explorer`)  
**Target Milestone:** Requirement R4 (UI Interactive Components & Navigation Integrity) and Design System Unification (P17–P22, P29–P30, P34, P36–P38)  
**Project Root:** `c:\xampp\htdocs\bazaario`  

---

## 1. Executive Summary & Baseline System Health

### 1.1 Automated Test Suite Baseline
Execution of `php artisan test` was performed against the entire test harness:
- **Total Tests:** 706 passed
- **Total Assertions:** 5,001 assertions
- **Failures / Errors:** 0
- **Duration:** 36.34s
- **Status:** **100% Green Baseline**. All 54 core marketplace features (M1 through M5) pass their automated quality gates. Any subsequent changes to Blade views, routes, or assets must strictly maintain this 706/706 test pass count.

### 1.2 Route Health Audit
Execution of `php artisan route:list` analyzed all registered HTTP routes:
- **Total Routes:** 154 registered routes
- **Broken Controller Bindings:** 0 detected
- **Missing Controller Classes:** 0 detected
- **Observations:** All existing customer, seller, and admin routes compile cleanly. However, critical legal/policy routes (`/privacy`, `/terms`, `/return-policy`) required by Acceptance Criteria are completely absent from `routes/web.php` and currently mapped to dead `#` anchors.

### 1.3 Asset Pipeline Architecture
- **Framework:** Tailwind CSS v4.0.0 (`@tailwindcss/vite` ^4.0.0, Vite ^7.0.7) configured in `vite.config.js`.
- **CSS Strategy:** In Tailwind CSS v4, theme tokens are not declared in `tailwind.config.js` by default; instead, they are declared via `@theme` directives directly inside `resources/css/app.css`.
- **Vite Build Verification:** Running `npm run build` completed cleanly in 2.83s, transforming 58 modules and emitting `public/build/assets/app-rNCRx_sZ.css` (189.49 kB).
- **Core Defect Identified (P1 & P2):** Multiple views (`layouts/app.blade.php`, `index.blade.php`, `layouts/user.blade.php`, `layouts/seller.blade.php`) load `@vite(...)` while concurrently hardcoding a static link to an outdated bundle (`app-C-FKvfT_.css`) AND importing `https://cdn.tailwindcss.com`. This causes dual CSS parsing, style overrides, and wasted network payloads.

---

## 2. Pillar 1: Design System Unification (P17)

### 2.1 The Problem: Divergent Design Systems
The Bazaario platform currently has fragmented styling across panels:
1. **Customer & Catalog Pages** (`resources/views/layouts/app.blade.php`, `index.blade.php`, `user/products/index.blade.php`):
   - Uses compiled Tailwind CSS v4 via `resources/css/app.css`.
   - Token naming: `amber-action` (`#F5A623`), `slate-authority` (`#0F172A`), `status-green` (`#16A34A`), `canvas-ivory` (`#FFFDF8`), `font-display` (`Space Grotesk`), `rounded-2xl` (`1rem`).
2. **Seller Panel** (`resources/views/layouts/seller.blade.php:20–72`):
   - Loads Tailwind CDN (`<script src="https://cdn.tailwindcss.com"></script>`).
   - Uses inline `tailwind.config` with Google Material 3 tokens: `brand.amber` (`#F5A623`), `brand.slate` (`#0F172A`), `primary` (`#0F172A`), `font-heading` (`Space Grotesk`), `rounded-custom` / `rounded-[14px]`, `surface` (`#fbf9f4`), `surface-container-*` (`#f5f3ee`, `#efeee9`, `#eae8e3`).
3. **User Account Views** (`resources/views/user/orders/index.blade.php`, `user/orders/show.blade.php`, `layouts/user.blade.php`):
   - Heavy usage of `surface-container-low`, `surface-container-high`, `text-on-surface`, `font-button-text`, `font-label-eyebrow`.

### 2.2 Complete Token Mapping

| Design Dimension | Customer Token (`app.css`) | Seller Inline Token (`layouts/seller.blade.php`) | Target Unified Token in `@theme` | Value |
|---|---|---|---|---|
| **Primary Brand** | `slate-authority` | `primary`, `brand.slate` | `--color-slate-authority`<br>`--color-brand-slate`<br>`--color-brand-primary` | `#0F172A` |
| **Action Accent** | `amber-action` | `brand.amber`, `secondary-container` | `--color-amber-action`<br>`--color-brand-amber`<br>`--color-secondary-container` | `#F5A623` / `#FEAE2C` |
| **Dark Amber** | — | `brand.amber-dark`, `secondary` | `--color-brand-amber-dark`<br>`--color-secondary` | `#D98205` / `#835500` |
| **Success Status** | `status-green` | `brand.green`, `on-tertiary-container` | `--color-status-green`<br>`--color-brand-green` | `#16A34A` |
| **Background / Canvas**| `canvas-ivory` | `brand.bg`, `surface` | `--color-canvas-ivory`<br>`--color-brand-bg`<br>`--color-surface` | `#FFFDF8` / `#FBF9F4` |
| **Surface Lowest** | `card-white` | `surface-container-lowest` | `--color-card-white`<br>`--color-surface-container-lowest` | `#FFFFFF` |
| **Surface Low** | `surface-container-low` (`#F8FAFC`) | `surface-container-low` (`#f5f3ee`) | `--color-surface-container-low` | `#F5F3EE` (Warm ivory harmonized) |
| **Surface Normal** | `surface-container` (`#F1F5F9`) | `surface-container` (`#efeee9`) | `--color-surface-container` | `#EFEEE9` |
| **Surface High** | `surface-container-high` (`#E2E8F0`) | `surface-container-high` (`#eae8e3`) | `--color-surface-container-high` | `#EAE8E3` |
| **Surface Highest** | — | `surface-container-highest` | `--color-surface-container-highest` | `#E4E2DE` |
| **Text Main** | `text-slate-authority` | `on-surface` | `--color-on-surface` | `#1B1C19` |
| **Text Muted** | `text-slate-500` | `on-surface-variant` | `--color-on-surface-variant` | `#45464D` |
| **Border Outline** | `border-slate-200` | `outline`, `outline-variant`, `brand.outline` | `--color-outline`<br>`--color-outline-variant`<br>`--color-brand-outline` | `#76777D`<br>`#C6C6CD`<br>`#E2DFD7` |
| **Heading Font** | `font-display` | `font-heading` | `--font-display`<br>`--font-heading` | `'Space Grotesk', sans-serif` |
| **Body Font** | `font-sans` | `font-sans` | `--font-sans` | `'Inter', system-ui, sans-serif` |
| **Mono Font** | `font-mono` | `font-mono` | `--font-mono` | `'JetBrains Mono', monospace` |
| **Card Radius** | `rounded-2xl` (`1rem` / `16px`) | `rounded-[14px]`, `rounded-custom`, `rounded-xl` | `--radius-custom: 14px;`<br>`--radius-14: 14px;`<br>`--radius-card: 1rem;` | `14px` & `16px` supported |
| **Card Shadow** | `--shadow-card-elevated` | `shadow-card`, `shadow-subtle` | `--shadow-subtle`<br>`--shadow-card` | `0 1px 8px rgba(0,0,0,0.04)`<br>`0 4px 20px -2px rgba(15,23,42,0.06)` |

### 2.3 Implementation Recommendation for Unification
1. **Extend `@theme` in `resources/css/app.css`:**  
   Add the complete set of seller and customer alias properties to `resources/css/app.css` so that both `font-display` and `font-heading`, `bg-amber-action` and `bg-brand-amber`, `bg-surface-container-*`, `rounded-custom`, etc., resolve instantly without editing thousands of lines across 20+ seller Blade templates.
2. **Remove Tailwind CDN Script from `resources/views/layouts/seller.blade.php`:**  
   - Delete line 20: `<script src="https://cdn.tailwindcss.com"></script>`
   - Delete lines 21–72: `<script>tailwind.config = { ... }</script>`
   - Add line in `<head>`: `@vite(['resources/css/app.css', 'resources/js/app.js'])`
3. **Remove Outdated Fallback Link & CDN from All Layouts:**
   - In `resources/views/layouts/app.blade.php`: remove line 25 (`<link ... app-C-FKvfT_.css>`) and line 26 (`<script src="cdn.tailwindcss.com">`).
   - In `resources/views/index.blade.php`: remove line 17 (`<link ... app-C-FKvfT_.css>`).
   - In `resources/views/layouts/user.blade.php`: remove line 18 and lines 19–120.

---

## 3. Pillar 2: Interactive Components (P18, P19, P34, P37, P38)

### 3.1 P18 — Dynamic Notification Bell & Unread Counter
- **File:** `resources/views/components/nav-user.blade.php`
- **Location:** Line 219 (amber indicator dot), Line 231 (badge text "3 New"), Lines 234–252 (hardcoded mock list).
- **Exact Code Observed:**
  ```html
  <!-- Line 219 -->
  <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-amber-500 rounded-full ring-2 ring-white"></span>
  ...
  <!-- Line 231 -->
  <span class="text-[10px] font-mono text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded font-bold">3 New</span>
  ```
- **Codebase Mechanism:**  
  The `App\Models\Notification` model is already mapped to the `notifications` table (`id`, `user_id`, `type`, `title`, `message`, `data`, `read_at`, `created_at`). `NotificationController::index()` already queries unread count using:
  ```php
  $unreadCount = DB::table('notifications')->where('user_id', $user->id)->whereNull('read_at')->count();
  ```
- **Actionable Solution:**
  At the top of `nav-user.blade.php` (inside the `@php` block around line 30):
  ```php
  $unreadNotificationsCount = 0;
  $recentNotifications = collect();
  if ($currentUser) {
      $unreadNotificationsCount = \App\Models\Notification::where('user_id', $currentUser->id)
          ->whereNull('read_at')
          ->count();
      $recentNotifications = \App\Models\Notification::where('user_id', $currentUser->id)
          ->latest()
          ->take(5)
          ->get();
  }
  ```
  In the template:
  - Guard the badge dot: `@if($unreadNotificationsCount > 0) ... @endif`
  - Render dynamic count: `{{ $unreadNotificationsCount }} New`
  - Populate the popover with `@forelse($recentNotifications as $notif)` showing `$notif->title`, `$notif->message`, and `$notif->created_at->diffForHumans()`, with a clean empty state when count is 0.

### 3.2 P19 — Touch-Resilient Cart Popover
- **File:** `resources/views/components/nav-user.blade.php`
- **Location:** Lines 263–266
- **Exact Code Observed:**
  ```html
  <div class="relative" 
       @mouseenter="cartOpen = true" 
       @mouseleave="cartOpen = false" 
       @click.outside="cartOpen = false">
      <a href="{{ route('cart.index') }}" ...>
  ```
- **Analysis:**  
  On touch screens, the device lacks hover state. Tapping the link fires `@mouseenter` simultaneously with click, which causes the mini-cart dropdown to flash or intercept the tap before browser navigation completes. On desktop hybrid devices (touch laptops/tablets), this causes UI freezing.
- **Actionable Solution:**  
  Restrict the hover listeners to pointer devices that genuinely support hover using CSS Media Query evaluation:
  ```html
  <div class="relative" 
       @mouseenter="window.matchMedia('(hover: hover)').matches && (cartOpen = true)" 
       @mouseleave="window.matchMedia('(hover: hover)').matches && (cartOpen = false)" 
       @click.outside="cartOpen = false">
  ```
  On touch screens (`(hover: none)`), hover events do not execute, and tapping the cart link immediately and cleanly navigates to `route('cart.index')`.

### 3.3 P34 — Viewport Meta Tag WCAG Accessibility Violation
- **Target Files & Lines:**
  1. `resources/views/user/products/index.blade.php` (line 13)
  2. `resources/views/docs/fees-and-commission.blade.php` (line 5)
  3. `resources/views/docs/become-a-seller.blade.php` (line 5)
  4. `resources/views/pages/how-it-works.blade.php` (line 5)
- **Exact Code Observed:**
  ```html
  <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
  ```
- **Analysis:**  
  Disabling pinch-to-zoom with `user-scalable=no` and `maximum-scale=1.0` violates **WCAG 1.4.4 (Resize text)** and hinders usability for visually impaired mobile users.
- **Actionable Solution:**  
  Replace all 4 instances with the standard responsive viewport meta tag:
  ```html
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  ```

### 3.4 P37 — Footer Currency Switcher UI
- **File:** `resources/views/components/footer.blade.php`
- **Location:** Lines 74–109 (markup), Lines 116–176 (script)
- **Analysis:**  
  The Alpine component `bazaarioLocalization()` fully implements multi-currency logic:
  - State: `currOpen: false`, `currencies: [{code: 'INR', symbol: '₹', ...}, {code: 'USD', symbol: '$', ...}, {code: 'EUR', symbol: '€', ...}]`, `currentCurr: {...}`
  - Methods: `setCurrency(curr)` (line 157) and `applyCurrency()` (line 164) which converts all DOM elements with `[data-price-inr]`.
  - Stored in `localStorage.getItem('bazaario_curr')`.
  - **The Defect:** In lines 74–109, only the Language Dropdown button is rendered. The currency dropdown button markup was accidentally deleted or omitted from the DOM!
- **Actionable Solution:**  
  Add the matching Currency Selector button and popover directly adjacent to the Language Dropdown (around line 109):
  ```html
  <!-- Currency Dropdown -->
  <div class="relative">
      <button @click="currOpen = !currOpen" 
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:border-amber-500 text-slate-800 transition-colors shadow-xs active:scale-95">
          <span class="font-bold text-amber-600" x-text="currentCurr.symbol"></span>
          <span class="font-semibold" x-text="currentCurr.code"></span>
          <span class="material-symbols-outlined text-[15px] text-slate-400 transition-transform duration-200" :class="{'rotate-180': currOpen}">expand_more</span>
      </button>

      <div x-cloak x-show="currOpen" @click.outside="currOpen = false"
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="opacity-0 translate-y-2 scale-95"
           x-transition:enter-end="opacity-100 translate-y-0 scale-100"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="opacity-100 translate-y-0 scale-100"
           x-transition:leave-end="opacity-0 translate-y-2 scale-95"
           class="absolute top-full mt-2 right-0 w-36 bg-white rounded-2xl shadow-2xl border border-slate-200/90 py-2 z-50 overflow-hidden">
          <div class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 mb-1">Select Currency</div>
          <template x-for="curr in currencies" :key="curr.code">
              <button @click="setCurrency(curr)"
                      class="w-full text-left px-3 py-2 hover:bg-amber-50/80 flex items-center justify-between text-xs font-medium text-slate-800 transition-colors">
                  <span class="flex items-center gap-2">
                      <span class="font-bold" x-text="curr.symbol"></span>
                      <span x-text="curr.code"></span>
                  </span>
                  <span x-show="currentCurr.code === curr.code" class="material-symbols-outlined text-xs text-amber-600 font-bold">check</span>
              </button>
          </template>
      </div>
  </div>
  ```

### 3.5 P38 — Translation Switch Back to English Without Full Page Reload
- **File:** `resources/views/components/footer.blade.php`
- **Location:** Lines 178–248
- **Exact Code Observed:**
  ```javascript
  applyTranslation(langCode) {
      if (langCode === 'en') {
          window.location.reload();
          return;
      }
      ...
      const walkNodes = (node) => {
          if (node.nodeType === Node.TEXT_NODE) {
              let text = node.nodeValue.trim();
              if (dict[text]) {
                  node.nodeValue = node.nodeValue.replace(text, dict[text]);
              }
          }
      ...
  ```
- **Analysis:**  
  When translating to Bengali (`bn`) or Hindi (`hi`), `node.nodeValue` is destructively overwritten in the DOM. To switch back to English, the developer resorted to `window.location.reload()`. This destroys form state, scroll position, and Alpine reactive memory.
- **Actionable Solution:**  
  Cache the original English string directly on each DOM Text Node (`node._originalText`) upon first visit. When switching back to `'en'`, simply restore `node._originalText` without triggering any page reload:
  ```javascript
  applyTranslation(langCode) {
      const dictionary = { bn: { ... }, hi: { ... } };
      const dict = dictionary[langCode] || null;

      const walkNodes = (node) => {
          if (node.nodeType === Node.TEXT_NODE) {
              if (node._originalText === undefined) {
                  node._originalText = node.nodeValue;
              }
              if (langCode === 'en') {
                  node.nodeValue = node._originalText;
              } else if (dict) {
                  let text = node._originalText.trim();
                  if (dict[text]) {
                      node.nodeValue = node._originalText.replace(text, dict[text]);
                  }
              }
          } else if (node.nodeType === Node.ELEMENT_NODE && !['SCRIPT', 'STYLE', 'INPUT', 'TEXTAREA'].includes(node.tagName)) {
              node.childNodes.forEach(walkNodes);
          }
      };

      walkNodes(document.body);
  }
  ```
  This is 100% instantaneous, memory-safe, and removes the jarring page reload completely.

---

## 4. Pillar 3: Navigation & Links (P20, P21, P22, P29, P30, P36)

### 4.1 P20 & P21 — Footer Dead Links (Social & Legal / Policy Links)
- **File:** `resources/views/components/footer.blade.php`
- **Location:** Lines 15–27 (social badges), Lines 54, 61–65 (policy & platform links)
- **Exact Code Observed:**
  ```html
  <!-- Social Badges: All href="#" -->
  <a aria-label="Instagram" ... href="#">ig</a>
  <a aria-label="X Twitter" ... href="#">x</a>
  <a aria-label="YouTube" ... href="#">yt</a>
  <a aria-label="GitHub" ... href="#">gh</a>
  ...
  <!-- Legal & Platform: All href="#" -->
  <li><a ... href="#">Return Policy</a></li>
  <li><a ... href="#">Escrow Guarantee</a></li>
  <li><a ... href="#">Dispute Center</a></li>
  <li><a ... href="#">Privacy Policy</a></li>
  <li><a ... href="#">Terms of Service</a></li>
  ```
- **Audit Findings:**  
  1. No routes exist in `routes/web.php` for `/privacy`, `/terms`, or `/return-policy`.
  2. Acceptance criteria states: `"Footer legal links (/privacy, /terms, /return-policy) respond with HTTP 200."`
- **Actionable Solution:**
  1. Add public informational routes in `routes/web.php`:
     ```php
     Route::view('/privacy', 'pages.privacy')->name('pages.privacy');
     Route::view('/terms', 'pages.terms')->name('pages.terms');
     Route::view('/return-policy', 'pages.return-policy')->name('pages.return-policy');
     ```
  2. Create clean Blade views (`resources/views/pages/privacy.blade.php`, `terms.blade.php`, `return-policy.blade.php`) extending `layouts.app` or an informational layout with complete Bazaario marketplace policies.
  3. In `resources/views/components/footer.blade.php`:
     - Wire legal links: `href="{{ route('pages.privacy') }}"`, `href="{{ route('pages.terms') }}"`, `href="{{ route('pages.return-policy') }}"`.
     - Wire Escrow & Dispute links: point to `href="{{ route('pages.how-it-works') }}#escrow"` and `href="{{ route('pages.how-it-works') }}#disputes"`.
     - Wire social links: point to official URLs (e.g. `https://github.com`, `https://x.com`, etc.) with `target="_blank" rel="noopener noreferrer"`.

### 4.2 P22 — "Deals" Filter Parameter Mismatch
- **File:** `resources/views/components/footer.blade.php` (Line 37)
- **Exact Code Observed:**
  ```blade
  <li><a class="hover:text-amber-action transition-colors" href="{{ route('products.index', ['filter' => 'escrow']) }}">Deals</a></li>
  ```
- **Analysis:**  
  In both `nav.blade.php` (line 66, 297) and `nav-user.blade.php` (line 72, 145, 527), the navbar highlights and routes "Today's Deals" via `['filter' => 'deals']`:
  ```php
  $isOnDeals = $currentRoute === 'products.index' && request('filter') === 'deals';
  ```
  The footer erroneously links to `['filter' => 'escrow']`. When clicked from the footer, the navbar active state fails to highlight and `ProductController` cannot recognize it as the deals query.
- **Actionable Solution:**  
  Change line 37 in `components/footer.blade.php` to:
  ```blade
  <li><a class="hover:text-amber-action transition-colors" href="{{ route('products.index', ['filter' => 'deals']) }}">Deals</a></li>
  ```
  Also in `components/footer.blade.php` line 34, change `['filter' => 'auctions']` to `route('auctions.index')` (or `['sale_type' => 'auction']`) to match the catalog auction filter parameter.

### 4.3 P29 — Home Page Navbar vs Customer Nav-User Unification
- **Files:** `resources/views/index.blade.php:60`, `resources/views/components/nav.blade.php`, `resources/views/components/nav-user.blade.php`
- **Analysis:**  
  1. `index.blade.php:60` includes `@include('components.nav')`.
  2. All other customer views (`user/products/index.blade.php`, `layouts/user.blade.php`) include `@include('components.nav-user')`.
  3. In `components/nav.blade.php` lines 9–15:
     ```php
     $currentUser = Auth::guard('user')->user() ?? Auth::guard('seller')->user();
     if ($currentUser && !defined('NAV_DELEGATING')) {
         define('NAV_DELEGATING', true);
         echo view('components.nav-user', get_defined_vars())->render();
         return;
     }
     ```
  4. In `components/nav-user.blade.php` lines 16–20:
     ```php
     if (!$currentUser && !defined('NAV_DELEGATING')) {
         define('NAV_DELEGATING', true);
         echo view('components.nav', get_defined_vars())->render();
         return;
     }
     ```
  5. **The Critical Discrepancy:**  
     Because of this cross-delegation ping-pong, guest users are ALWAYS bounced to `components.nav.blade.php`.
     In `components.nav.blade.php`:
     - Line 91 hides the Cart icon completely for guests (`@if($currentUser) ... @endif`), even though guests can add items to `session('cart')`!
     - There is NO search bar in the desktop navbar for guests.
     - There is NO category dropdown for guests.
     In contrast, `components.nav-user.blade.php` ALREADY contains full guest rendering (lines 485–494 for Sign in/Sign up buttons, lines 86–128 for Categories, lines 149–188 for Search, lines 262–285 for Cart reading `session('cart')`).
- **Actionable Solution:**
  - Remove the artificial delegation blocks (`NAV_DELEGATING`) from `components/nav-user.blade.php` (lines 16–20).
  - Update `index.blade.php` line 60 to `@include('components.nav-user')`.
  - Maintain `components/nav.blade.php` as a clean forwarder to `components.nav-user` or ensure parity so both logged-in users and guests enjoy the complete navbar experience everywhere.

### 4.4 P30 — Popular Search Chips Hardcoded Product Names
- **File:** `resources/views/index.blade.php` (lines 100–108)
- **Exact Code Observed:**
  ```html
  <div class="flex flex-wrap items-center gap-2 font-mono text-[11px] text-slate-600 pt-1">
      <span class="font-bold text-slate-900">Popular:</span>
      <a ... href="{{ route('products.index', ['search' => 'iPhone 16 Pro']) }}">iPhone 16 Pro</a>
      <a ... href="{{ route('products.index', ['search' => 'Leica M3']) }}">Leica M3</a>
      <a ... href="{{ route('products.index', ['search' => 'Mechanical Keys']) }}">Mechanical Keys</a>
      <a ... href="{{ route('products.index', ['search' => 'Sneakers']) }}">Sneakers</a>
  </div>
  ```
- **Audit Findings:**  
  The seeded products in Bazaario (`MarketplaceDataSeeder.php`) are:
  - *Handcrafted Heritage Leather Messenger Bag*
  - *Lumik V87 Pro Wireless Mechanical Keyboard*
  - *Hand-thrown Ceramic Coffee Dripper & Mug Set*
  - *Acoustic Pro Active Noise-Cancelling Headphones*
  - *Pure Organic Mulberry Silk Scarf (Indigo Dye)*
  - *Vintage Brass Desk Compass & Sundial (1940s Replica)*
  Therefore, clicking "iPhone 16 Pro", "Leica M3", or "Sneakers" yields **0 search results**, giving buyers an impression of an empty broken marketplace.
- **Actionable Solution:**  
  Update the search chips in `index.blade.php` to terms that actually exist in the database or match active categories:
  ```html
  <div class="flex flex-wrap items-center gap-2 font-mono text-[11px] text-slate-600 pt-1">
      <span class="font-bold text-slate-900">Popular:</span>
      <a class="px-2.5 py-0.5 rounded-full bg-white/80 border border-slate-200 hover:border-amber-500 hover:text-amber-600 text-slate-700 transition-colors"
         href="{{ route('products.index', ['search' => 'Mechanical Keyboard']) }}">Mechanical Keyboard</a>
      <a class="px-2.5 py-0.5 rounded-full bg-white/80 border border-slate-200 hover:border-amber-500 hover:text-amber-600 text-slate-700 transition-colors"
         href="{{ route('products.index', ['search' => 'Leather Bag']) }}">Leather Bag</a>
      <a class="px-2.5 py-0.5 rounded-full bg-white/80 border border-slate-200 hover:border-amber-500 hover:text-amber-600 text-slate-700 transition-colors"
         href="{{ route('products.index', ['search' => 'Headphones']) }}">Headphones</a>
      <a class="px-2.5 py-0.5 rounded-full bg-white/80 border border-slate-200 hover:border-amber-500 hover:text-amber-600 text-slate-700 transition-colors"
         href="{{ route('products.index', ['search' => 'Silk Scarf']) }}">Silk Scarf</a>
  </div>
  ```

### 4.5 P36 — Category Mega Menu Route Migration (Search Params vs Category Slugs)
- **File:** `resources/views/components/nav-user.blade.php` (lines 102–126)
- **Exact Code Observed:**
  ```html
  <!-- Lines 102-105 -->
  <li><a href="{{ route('products.index', ['search' => 'Smartphone']) }}">Smartphones</a></li>
  <li><a href="{{ route('products.index', ['search' => 'Laptop']) }}">Laptops</a></li>
  <!-- Lines 112-115 -->
  <li><a href="{{ route('products.index', ['search' => 'Men']) }}">Men's Wear</a></li>
  <li><a href="{{ route('products.index', ['search' => 'Leather']) }}">Leather Goods</a></li>
  <!-- Lines 122-124 -->
  <li><a href="{{ route('products.index', ['search' => 'Ceramic']) }}">Handmade Ceramics</a></li>
  <li><a href="{{ route('products.index', ['search' => 'Furniture']) }}">Modern Furniture</a></li>
  ```
- **Analysis:**  
  `ProductController@index` has a dedicated, indexed category filter:
  ```php
  // Lines 134-143 in ProductController.php
  $rawCategory = $request->input('category');
  ...
  $query->whereHas('category', function ($cq) use ($selectedCategory) {
      $cq->where('slug', $selectedCategory)->orWhere('name', $selectedCategory);
  });
  ```
  Passing `search=Men` or `search=Smartphone` bypasses the category relationship and runs a fuzzy wildcard text match against product titles and descriptions.
- **Actionable Solution:**  
  In `components/nav-user.blade.php`:
  Bind categories to their real category slugs (`electronics`, `fashion`, `artisan-craft`, `home-living`, `collectibles`, `books-stationery`):
  ```blade
  <li><a href="{{ route('products.index', ['category' => 'electronics']) }}" class="hover:text-amber-600 transition-colors">Electronics & Gadgets</a></li>
  <li><a href="{{ route('products.index', ['category' => 'fashion']) }}" class="hover:text-amber-600 transition-colors">Fashion & Apparel</a></li>
  <li><a href="{{ route('products.index', ['category' => 'artisan-craft']) }}" class="hover:text-amber-600 transition-colors">Artisan & Handmade</a></li>
  <li><a href="{{ route('products.index', ['category' => 'home-living']) }}" class="hover:text-amber-600 transition-colors">Home & Living</a></li>
  <li><a href="{{ route('products.index', ['category' => 'collectibles']) }}" class="hover:text-amber-600 transition-colors">Rare Collectibles</a></li>
  <li><a href="{{ route('products.index', ['category' => 'books-stationery']) }}" class="hover:text-amber-600 transition-colors">Books & Stationery</a></li>
  ```
  Or render them dynamically using `$navCategories` loaded in line 24.

---

## 5. Implementation Roadmap & Verification Gates

```
Phase 1: Design System & Asset Cleanup (P1, P2, P17)
  ├── 1. Add unified aliases to resources/css/app.css (@theme block)
  ├── 2. Remove Tailwind CDN and inline config from layouts/seller.blade.php
  ├── 3. Remove hardcoded app-C-FKvfT_.css & redundant Tailwind scripts from layouts/app, index, layouts/user
  └── 4. Run `npm run build` to verify CSS compiles cleanly

Phase 2: Interactive Components (P18, P19, P34, P37, P38)
  ├── 1. Dynamic notification unread count & popover list in nav-user.blade.php
  ├── 2. Touch-friendly hover protection for cart popover in nav-user.blade.php
  ├── 3. Fix viewport meta tags (remove user-scalable=no) in products/index and 3 doc views
  ├── 4. Add missing currency dropdown markup to footer.blade.php
  └── 5. Implement non-destructive text caching in footer translation engine (no reload)

Phase 3: Navigation & Route Integrity (P20, P21, P22, P29, P30, P36)
  ├── 1. Add /privacy, /terms, /return-policy routes in routes/web.php and create Blade views
  ├── 2. Fix dead footer links and change Deals filter to filter=deals
  ├── 3. Unify index.blade.php to use components.nav-user and remove delegation block
  ├── 4. Replace fake popular search chips in index.blade.php with real catalog terms
  └── 5. Switch category mega menu in nav-user to category slug query parameters

Phase 4: Verification & Regression Gate
  ├── 1. Execute `php artisan test` (must achieve >= 706 passing tests, 0 failures)
  ├── 2. Execute `php artisan route:list` (must verify 200 responses on /privacy, /terms, /return-policy)
  └── 3. Check browser console / DOM for zero duplicate asset warnings
```

---

## 6. Summary of Target Files

| File | Lines | Issue Covered | Action Required |
|---|---|---|---|
| `resources/css/app.css` | 8–25 | P17 | Add complete design tokens and color aliases in `@theme` |
| `resources/views/layouts/seller.blade.php` | 20–72 | P2, P17 | Replace Tailwind CDN with `@vite`, eliminate inline config |
| `resources/views/layouts/app.blade.php` | 24–27 | P1 | Remove hardcoded link to old CSS file and Tailwind CDN |
| `resources/views/index.blade.php` | 16–17, 60, 100–108 | P1, P29, P30 | Remove old CSS link, switch to `nav-user`, update search chips |
| `resources/views/components/nav-user.blade.php` | 16–20, 102–126, 219, 231, 264 | P18, P19, P29, P36 | Query unread count, fix cart touch hover, remove delegation, use category slugs |
| `resources/views/components/nav.blade.php` | 11–15, 91 | P29 | Maintain parity / unify guest cart display |
| `resources/views/components/footer.blade.php` | 15–27, 34, 37, 54, 61–65, 74–109, 178–182 | P20, P21, P22, P37, P38 | Add currency dropdown, remove translation reload, fix links & deals query |
| `resources/views/user/products/index.blade.php` | 13, 24 | P3, P34 | Remove duplicate Alpine CDN script, remove `user-scalable=no` |
| `resources/views/docs/fees-and-commission.blade.php` | 5 | P34 | Remove `user-scalable=no` |
| `resources/views/docs/become-a-seller.blade.php` | 5 | P34 | Remove `user-scalable=no` |
| `resources/views/pages/how-it-works.blade.php` | 5 | P34 | Remove `user-scalable=no` |
| `routes/web.php` | New routes | P20, P21 | Add `/privacy`, `/terms`, `/return-policy` routes |
