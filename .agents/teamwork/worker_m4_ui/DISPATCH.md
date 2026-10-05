## 2026-10-05T08:51:45Z
You are worker_m4_ui working in c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m4_ui.
Your parent is orchestrator_9 (conversation ID: 70bc0236-b504-4d06-a5f6-f183cf1120fd).

MANDATORY FIRST STEP:
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md.
Also read c:\xampp\htdocs\bazaario\UI_LOGIC_PROBLEMS.md and c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_9\PROJECT.md.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Mission: Implement Milestone 4 (Design System Unification & UI Components: P18, P19, P23–P25, P27, P29–P30, P36–P38, P40).

Detailed Tasks:
1. P18: Dynamically bind notification bell badge count in resources/views/components/nav-user.blade.php and resources/views/layouts/seller.blade.php. Remove hardcoded "3 New" text and static amber dot. Calculate unread notification count dynamically from the authenticated user / notification data (or default 0), display real count, and hide the notification dot when count is 0.
2. P19: Fix cart mini-popover trigger on touch devices in resources/views/components/nav-user.blade.php. Avoid pure hover @mouseenter/@mouseleave breaking touch interactions. Support touch-friendly toggling (e.g. click/tap opens popover or click outside closes it with @click.outside="cartOpen = false", desktop hover still supported or unified click toggle).
3. P23: Verify and validate scopeLowStock() and scopeStale() queries in app/Models/Product.php and resources/views/seller/products/index.blade.php. Ensure scopeLowStock($query) (e.g. stock <= 5 or threshold) and scopeStale($query) (e.g. updated_at or created_at older than 30/60 days or untouched) exist and run with 0 errors.
4. P24: Wire bulk actions in resources/views/seller/products/index.blade.php to genuine form submissions / routes (replace placeholder alert() calls). Ensure bulk actions (activate, deactivate, delete, etc.) submit to the seller product bulk action endpoint (e.g. seller.products.bulk in SellerProductController.php) with proper CSRF token, selected product IDs, and flash messages.
5. P25: Make seller dashboard 7-day revenue chart in resources/views/seller/dashboard.blade.php dynamic with real data and clean empty state. Feed real aggregated revenue data from SellerDashboardController (or safe dynamic calculation) and render a clean, accessible SVG or bar chart with proper labels, showing an elegant empty state if there are 0 sales in the last 7 days.
6. P27: Wire seller header notification bell button in resources/views/layouts/seller.blade.php to route('seller.account.notifications') or a functional dropdown.
7. P29: Ensure complete navbar feature parity between home page (resources/views/components/nav.blade.php) and customer pages (resources/views/components/nav-user.blade.php).
8. P30: Replace non-existent popular search chips in resources/views/index.blade.php (iPhone 16 Pro, Leica M3) with real catalog categories or terms (e.g. Electronics, Fresh Vegetables, Handcrafted, Spices, Fashion).
9. P36: Fix category nav-user dropdown links in resources/views/components/nav-user.blade.php to use category slug routes (e.g. route('products.index', ['category' => $cat->slug]) or route('products.category', $cat->slug)) instead of query search params.
10. P37: Render a visible toggle button for the currency selector in resources/views/components/footer.blade.php alongside the language switcher, hooking into the existing bazaarioLocalization() component.
11. P38: Implement non-destructive translation text cache in resources/views/components/footer.blade.php avoiding window.location.reload() on 'en' language switch (cache original text in element data attributes or JS memory and restore without page reload).
12. P40: Strengthen seller layout authentication guard check in resources/views/layouts/seller.blade.php and seller views to ensure seller access is authenticated and prevent customer data leakage.
13. Design system token unification: Ensure any remaining stitch tokens/classes are aliased in resources/css/app.css @theme definitions.
