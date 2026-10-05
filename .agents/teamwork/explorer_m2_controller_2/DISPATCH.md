# DISPATCH: Explorer M2 Controller (Backend & Queries)

## Task Description
You are `explorer_m2_controller_2` working in `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m2_controller_2`.
Your parent is `orchestrator_4` (conversation ID: 6f703d77-7d87-49d3-b5fa-6d8efb15a7cc).

### Input Files to Inspect:
1. `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md` (timestamp 2026-09-30T04:46:52Z, R2)
2. `c:\xampp\htdocs\bazaario\PROJECT.md`
3. `c:\xampp\htdocs\bazaario\routes\web.php` (lines 185-230, seller routes)
4. `c:\xampp\htdocs\bazaario\app\Models\SellerOrder.php`
5. `c:\xampp\htdocs\bazaario\app\Models\Product.php`
6. `c:\xampp\htdocs\bazaario\app\Models\SellerProfile.php`
7. `c:\xampp\htdocs\bazaario\app\Models\Auction.php`
8. `c:\xampp\htdocs\bazaario\app\Models\Payout.php`

### Objective:
Design `app/Http/Controllers/Seller/SellerDashboardController.php` and its query architecture.
Investigate:
1. How to authenticate the seller (`Auth::guard('seller')->user()`).
2. Exact query logic for the 6 KPIs:
   - Total Orders count: `SellerOrder::where('seller_id', $user->id)->count()`
   - Gross Revenue sum: `SellerOrder::where('seller_id', $user->id)->where('status', '!=', 'cancelled')->sum('subtotal')`
   - Active Listed Products: `Product::where('seller_id', $user->id)->where('status', 'active')->count()`
   - Low Stock Alerts count: `Product::where('seller_id', $user->id)->lowStock()->count()`
   - Seller Trust Score: `$profile->trust_score ?? 95.0`
   - Next Payout estimate: sum of pending payouts / orders
3. Order pipeline breakdown: counts of orders in `placed`, `processing`, `packed`/`ready_for_pickup`, `shipped`/`delivered`.
4. Low stock products list (top 5 depleted items with stock <= threshold).
5. Top products by sales velocity: aggregated from `order_items` join `seller_orders` or `order_items.product_id` where `seller_orders.seller_id = $user->id`.
6. Live wholesale auction spotlight: `Auction::where('seller_id', $profile->id)->where('status', 'live')->where('ends_at', '>', now())->latest()->first()`.
7. Revenue chart data for 7-day window (daily date labels and daily revenue sums).
8. Route definition in `routes/web.php` for `Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');`.

Write your full architecture blueprint and verified Eloquent queries to `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m2_controller_2\handoff.md`.
Send message to parent when done.

## 2026-09-30T05:41:49Z
Sender: 6f703d77-7d87-49d3-b5fa-6d8efb15a7cc
You are explorer_m2_controller_2 working in c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m2_controller_2.
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically 2026-09-30T04:46:52Z, R2), c:\xampp\htdocs\bazaario\PROJECT.md (Milestone 2), and c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m2_controller_2\DISPATCH.md.
Inspect existing Eloquent models (SellerOrder.php, Product.php, SellerProfile.php, Auction.php, Payout.php) and routes/web.php.
Design app/Http/Controllers/Seller/SellerDashboardController.php with all query aggregations: 6 KPIs (Total Orders, Gross Revenue, Active Products, Low Stock Alerts, Trust Score, Next Payout), 7-day revenue chart data, order pipeline breakdown, low stock items list, top products velocity, and live auction spotlight query.
Ensure strict multi-tenant scoping to Auth::guard('seller')->id().
Write your complete handoff report to c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m2_controller_2\handoff.md and send_message to parent (conversation ID: 6f703d77-7d87-49d3-b5fa-6d8efb15a7cc).

