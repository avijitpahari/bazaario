## 2026-10-05T05:05:03Z
You are worker_m2_logic, a teamwork_preview_worker.
Working directory: c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m2_logic
Project root: c:\xampp\htdocs\bazaario

MANDATORY FIRST STEP: Read ORIGINAL_REQUEST.md at c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md before starting work.
Also read UI_LOGIC_PROBLEMS.md at c:\xampp\htdocs\bazaario\UI_LOGIC_PROBLEMS.md.
Also read PROJECT.md at c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_7\PROJECT.md.
Also read survey report: c:\xampp\htdocs\bazaario\.agents\teamwork\survey_miner_r1_r2\report.md.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Your objective:
Implement Milestone 2: Logic, Route & Data Reliability (Issues P5, P6, P7, P8, P9, P10, P20, P21, P22, P26, P28, P31, P32, P39, P41).

Exclusively owned files for Milestone 2:
1. routes/web.php
2. app/Http/Controllers/ProductController.php
3. app/Http/Controllers/Seller/SellerAuctionController.php
4. app/Http/Controllers/Seller/SellerProfileController.php
5. app/Http/Controllers/Seller/SellerDashboardController.php
6. resources/views/seller/dashboard.blade.php
7. resources/views/layouts/seller.blade.php (top header search bar & notification bell wire)
8. resources/views/seller/account/notifications.blade.php
9. resources/views/seller/account/settings.blade.php
10. resources/views/pages/privacy.blade.php
11. resources/views/pages/terms.blade.php
12. resources/views/pages/return-policy.blade.php
13. resources/views/components/footer.blade.php
14. resources/views/index.blade.php (AI CTA, Storage facade, withCount('bids'))

Tasks:
1. P21 & P20 & P22 (Legal routes, views, footer links):
   - Add routes in routes/web.php: /privacy, /terms, /return-policy pointing to ProductController methods privacy(), terms(), returnPolicy().
   - Create resources/views/pages/privacy.blade.php, terms.blade.php, return-policy.blade.php (complete, clean, responsive marketplace policy pages returning HTTP 200).
   - In components/footer.blade.php: wire Return Policy, Privacy Policy, Terms of Service links to route('pages.return-policy'), route('pages.privacy'), route('pages.terms').
   - In components/footer.blade.php: fix Deals filter parameter from filter=escrow to filter=deals (P22).
   - In components/footer.blade.php: replace dead social media href="#" with safe placeholder URLs (https://x.com, https://instagram.com, etc.) or javascript:void(0) (P20).
2. P6, P7, P31, P32 (Homepage CTAs and optimizations):
   - In resources/views/index.blade.php: change "Try AI Compare" CTA from misleading products.index link to "Browse Catalog" / "Explore Products" or link cleanly.
   - For floating "Ask Bazaario AI" button (in index.blade.php and footer.blade.php): wire it to an informative modal ("Bazaario AI Assistant - Coming Soon") or clean fallback behavior so it doesn't navigate to unrelated pages or sit inert (P7).
   - In resources/views/index.blade.php: add use Illuminate\Support\Facades\Storage; inside the @php block at line ~518 (P32).
   - In app/Http/Controllers/ProductController.php (home method): ensure $featuredAuction uses ->withCount('bids') and in index.blade.php:295 read $featuredAuction->bids_count to prevent N+1 query (P31).
3. P8 (Seller Header Search Bar):
   - In resources/views/layouts/seller.blade.php (lines ~231-235): wrap search input in <form action="{{ route('seller.products.index') }}" method="GET"> with name="search" and value="{{ request('search') }}".
4. P9 (Seller Dashboard Fake Fallback Data Removal):
   - In resources/views/seller/dashboard.blade.php: remove all hardcoded numeric fallbacks ($totalOrders ?? 248, $grossRevenue ?? 84520.00, $lowStockCount ?? 6, fake revenueChartData arrays, fake top products). Replace with real 0 and empty collections. Show clean empty state when seller has 0 orders.
   - In app/Http/Controllers/Seller/SellerDashboardController.php: ensure fulfillment_text and reviews_text do not output hardcoded fake strings ("235 / 248 on time", "182 verified reviews") for sellers with 0 data.
5. P10 (Seller Auctions History Route):
   - In app/Http/Controllers/Seller/SellerAuctionController.php: implement history(Request $request) method returning completed/ended auctions (status = 'ended').
   - In routes/web.php: update seller.auctions.history route to point to [SellerAuctionController::class, 'history'].
6. P26 & P28 (Seller Notifications & Settings):
   - In routes/web.php: add Route::get('/notifications', [SellerProfileController::class, 'notifications'])->name('notifications'); under seller.account prefix.
   - In app/Http/Controllers/Seller/SellerProfileController.php: add notifications() and settings() methods.
   - Populate resources/views/seller/account/notifications.blade.php with clean notifications list and empty state.
   - Populate resources/views/seller/account/settings.blade.php with account preferences (store notification settings, payout preferences, display settings) and stop the confusing redirect from settings to profile.
7. P41 (Category slug handling):
   - In app/Http/Controllers/ProductController.php: in category($slug = null) method, handle null/empty slug gracefully (redirect to products.index or display all active products).
8. Verification:
   - Run php artisan route:list: verify zero missing routes or broken bindings.
   - Test legal routes: curl or artisan test to ensure /privacy, /terms, /return-policy return HTTP 200.
   - Run php artisan test: verify all tests pass (expecting 726+ tests, 0 failures).

Document all changes, exact diffs, and verification commands/outputs in c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m2_logic\changes.md and handoff.md.
Then send a completion message back to parent.
