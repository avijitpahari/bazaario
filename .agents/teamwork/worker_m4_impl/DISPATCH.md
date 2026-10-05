## 2026-09-30T10:18:02Z

You are worker_m4_impl (TypeName: teamwork_preview_worker).
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m4_impl

MANDATORY FIRST STEP:
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically header ## 2026-09-30T04:46:52Z) and c:\xampp\htdocs\bazaario\PROJECT.md.
Also read the detailed handoff reports prepared by the 3 exploration agents:
1. `c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m4_1\handoff.md` (Stitch UI layout, 2-column desk, modals, badges, tokens)
2. `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m4_backend_1\handoff.md` (Controllers, models, routes, multi-tenancy, transactions)
3. `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m4_tests_1\handoff.md` (Test matrix, edge cases, assertions)
Also inspect the draft tests created by explorer_m4_tests_1:
- `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m4_tests_1\proposed_SellerOrderAndPayoutTest.php`
- `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m4_tests_1\proposed_SellerTestHelperTrait.php`

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Scope Boundaries & Exclusive Write Ownership:
You own exclusively:
- `app/Http/Controllers/Seller/SellerOrderController.php`
- `app/Http/Controllers/Seller/SellerPayoutController.php`
- `routes/web.php` (for seller order and payout routes)
- `app/Models/SellerOrder.php`
- `app/Models/Payout.php`
- `database/migrations/2026_09_30_000002_enhance_seller_orders_and_payouts_tables.php` (if needed)
- `resources/views/seller/orders/index.blade.php`
- `resources/views/seller/orders/show.blade.php`
- `resources/views/seller/payouts/index.blade.php`
- `resources/views/seller/payouts/show.blade.php`
- `tests/Feature/Seller/SellerTestHelperTrait.php`
- `tests/Feature/Seller/SellerOrderAndPayoutTest.php`

Deliverables:
1. Implement `SellerOrderController.php` with:
   - `index`: filter by status tab (`all`, `pending`, `confirmed`, `processing`, `ready_for_pickup`, `fulfilled`, `cancelled`), search by order number or customer, multi-tenancy isolation (`where('seller_id', Auth::guard('seller')->id())`), 2-column data ($orders, $focusedOrder, $statusCounts, $stats).
   - `show`: tenant isolation check (abort 403 if seller_id != Auth::guard('seller')->id()), order items, delivery slot details, customer info, handover status.
   - `updateStatus`: validate status, state transition logic (`placed`/`pending` -> `processing` -> `ready_for_pickup` -> `fulfilled`), update timestamps, parent order status sync.
   - `fulfill` / `handover`: handover verification protocol execution inside `DB::transaction`, mark as fulfilled/delivered, set `delivered_at`, schedule/create corresponding payout record.
2. Implement `SellerPayoutController.php` with:
   - `index`: multi-tenancy isolation (`where('seller_id', Auth::guard('seller')->id())`), 4 KPIs (Lifetime Revenue, Platform Commission 10%, Settled Payouts, Pending/Processing), transparent commission breakdown card, upcoming settlement banner with masked bank info, settlements ledger table with status tabs.
   - `show`: tenant isolation check (abort 403 if not owner), detailed payout receipt, bank destination, itemized deduction ledger.
3. Wire the routes in `routes/web.php` under `prefix('seller')` with `middleware(['auth:seller', 'seller'])`.
4. Create the high-fidelity Blade views in `resources/views/seller/orders/` and `resources/views/seller/payouts/` extending `layouts.seller` matching the Stitch design tokens.
5. Create `tests/Feature/Seller/SellerTestHelperTrait.php` and `tests/Feature/Seller/SellerOrderAndPayoutTest.php` covering Features 26–33 and all edge cases.
6. Run verification:
   - `php -l` on all modified/created PHP files
   - `php artisan view:clear` and `php artisan view:cache`
   - `php artisan test tests/Feature/Seller/SellerOrderAndPayoutTest.php`
   - Full regression test run: `php artisan test` (must pass 411+ existing tests without any regression)
7. Compile complete handoff report in `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m4_impl\handoff.md` and send completion message back to parent.
