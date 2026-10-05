# Milestone 2: Logic, Route & Data Reliability Changes

## Overview
Milestone 2 addresses issues P5, P6, P7, P8, P9, P10, P20, P21, P22, P26, P28, P31, P32, P39, P41 across routes, controllers, and Blade templates.

---

## 1. Routes & Legal Pages (P20, P21, P22, P39)
### Files Modified / Created:
- `routes/web.php`:
  - Added public routes `/privacy` (`pages.privacy`), `/terms` (`pages.terms`), and `/return-policy` (`pages.return-policy`) pointing to `ProductController@privacy`, `ProductController@terms`, `ProductController@returnPolicy`.
- `app/Http/Controllers/ProductController.php`:
  - Implemented `privacy()`, `terms()`, and `returnPolicy()` controller methods returning their respective Blade views.
- `resources/views/pages/privacy.blade.php`:
  - Created complete, responsive Privacy Policy page with DPDPA/GDPR compliance, information categories, data usage rules, and DPO contact.
- `resources/views/pages/terms.blade.php`:
  - Created complete, responsive Terms of Service page with escrow settlement mechanics, live auction rules, merchant obligations, and dispute mediation.
- `resources/views/pages/return-policy.blade.php`:
  - Created complete, responsive Return & Refund Policy page with 48h perishable and 7-day handcrafted return rules, escrow refund timelines, and claim workflows.
- `resources/views/components/footer.blade.php`:
  - Wired `Privacy Policy` link to `route('pages.privacy')`.
  - Wired `Terms of Service` link to `route('pages.terms')`.
  - Wired `Return Policy` link to `route('pages.return-policy')`.
  - Wired `Escrow Guarantee` and `Dispute Center` links to `route('pages.how-it-works')`.
  - Fixed Deals filter parameter from `filter=escrow` to `filter=deals` (P22).
  - Replaced dead social media `href="#"` with safe external destinations: `https://instagram.com`, `https://x.com`, `https://youtube.com`, `https://github.com` (P20).

---

## 2. Homepage CTAs & Query Optimizations (P6, P7, P31, P32)
### Files Modified:
- `resources/views/index.blade.php`:
  - Changed "Try AI Compare" CTA button to "Explore Products" (P6).
  - Eager bid count: Updated `$featuredAuction->bids->count()` to read `$featuredAuction->bids_count ?? $featuredAuction->bids->count()` (P31).
  - Resolved `use Illuminate\Support\Facades\Storage;` syntax issues by using fully-qualified `\Illuminate\Support\Facades\Storage::url(...)` (P32).
  - Removed duplicate floating AI button block from `index.blade.php` to prevent stacking on top of footer button (P7).
- `resources/views/components/footer.blade.php`:
  - Wired floating "✦ Ask Bazaario AI" button to an interactive Alpine.js modal dialog ("Bazaario AI Assistant - Coming Soon") explaining the upcoming AI features and providing a quick link to browse the catalog (P7).
- `app/Http/Controllers/ProductController.php`:
  - In `home()` method, appended `->withCount('bids')` to the `home_featured_auction` query (P31).

---

## 3. Seller Header Global Search Bar & Notifications (P8)
### Files Modified:
- `resources/views/layouts/seller.blade.php`:
  - Wrapped top header search input in `<form action="{{ route('seller.products.index') }}" method="GET" class="flex items-center flex-1 max-w-lg">` with `name="search"` and `value="{{ request('search') }}"` (P8).
  - Converted the header notification bell button into an anchor tag linking to `route('seller.account.notifications')`.

---

## 4. Seller Dashboard Fake Fallback Data Removal (P9)
### Files Modified:
- `resources/views/seller/dashboard.blade.php`:
  - Replaced all hardcoded fallback mock data (`$totalOrders ?? 248`, `$grossRevenue ?? 84520.00`, `$lowStockCount ?? 6`, `$revenueChartData` hardcoded 7-day array, `$lowStockProducts` mock mangoes, `$topProducts` mock products, `$recentOrders` mock orders) with neutral 0, 0.00, and empty collections.
  - When sellers have 0 orders, dashboard displays genuine zero KPIs and clean empty states ("No orders received yet", "All inventory healthy! No products below minimum threshold.", "No sales velocity recorded yet this month.").
  - Properly formatted integers for trust scores (`(int) $trustScore`), accurate dynamic pipeline counts (`{{ $totalOrders }} Total`), verified buyers on auctions (`verified buyers`), and clean order IDs (`{{ $order->seller_order_number ?? ... }}`).
- `app/Http/Controllers/Seller/SellerDashboardController.php`:
  - Updated fallback strings:
    - `'fulfillment_text' => $totalOrders > 0 ? "{$deliveredCount} / {$totalOrders} on time" : "0 / 0 on time (No orders yet)"`
    - `'reviews_text' => $totalReviewCount > 0 ? "{$totalReviewCount} verified reviews" : "0 verified reviews"`

---

## 5. Seller Auctions History Route (P10)
### Files Modified:
- `app/Http/Controllers/Seller/SellerAuctionController.php`:
  - Implemented `history(Request $request): View|RedirectResponse` method which merges `['status' => 'ended']` and calls `index($request)`.
- `routes/web.php`:
  - Updated `seller.auctions.history` route binding from `[SellerAuctionController::class, 'index']` to `[SellerAuctionController::class, 'history']`.

---

## 6. Seller Notifications & Settings (P26, P28)
### Files Modified / Created:
- `routes/web.php`:
  - Added `Route::get('/notifications', [SellerProfileController::class, 'notifications'])->name('notifications');` under `seller.account`.
  - Replaced redirect route for `/settings` with `Route::get('/settings', [SellerProfileController::class, 'settings'])->name('settings');` and `Route::put('/settings', [SellerProfileController::class, 'updateSettings'])->name('settings.update');`.
- `app/Http/Controllers/Seller/SellerProfileController.php`:
  - Implemented `notifications()` returning `view('seller.account.notifications')`.
  - Implemented `settings()` returning `view('seller.account.settings')`.
  - Implemented `updateSettings(Request $request)` saving seller operating preferences and redirecting with success toast.
- `resources/views/seller/account/notifications.blade.php`:
  - Populated complete merchant notifications screen with operational categories (Orders, Auctions, Payouts, System), status filters, mark-all-read controls, and empty state.
- `resources/views/seller/account/settings.blade.php`:
  - Populated comprehensive store preferences workstation with automated listing rules (auto-hide expired perishables), low-stock thresholds, notification channel preferences, and settlement banking overview.

---

## 7. Category Slug Handling (P41)
### Files Modified:
- `app/Http/Controllers/ProductController.php`:
  - In `category(Request $request, $slug = 'all')`:
    - Checks `if (empty($slug) || $slug === 'all')`: sets `$slug = 'all'` and `$category = null`.
    - Avoids querying where `category.slug == null`. Loads all active products gracefully without 404 or empty crash.
