# DISPATCH: Worker M2 (Dashboard & Performance Analytics Implementation)

## Task Description
You are `worker_m2_2` working in `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m2_2`.
Your parent is `orchestrator_4` (conversation ID: 6f703d77-7d87-49d3-b5fa-6d8efb15a7cc).

### MANDATORY INTEGRITY WARNING
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

### Authoritative Context & Findings to Read:
1. `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md` (timestamp 2026-09-30T04:46:52Z, R2)
2. `c:\xampp\htdocs\bazaario\PROJECT.md` (Milestone 2, Features 9–15)
3. `c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m2_2\handoff.md` (exact Blade view blueprint)
4. `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m2_controller_2\handoff.md` and `proposed_SellerDashboardController.php`
5. `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m2_tests_2\handoff.md` and `proposed_SellerDashboardTest.php`

### Implementation Steps:
1. Copy or create `app/Http/Controllers/Seller/SellerDashboardController.php` from `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m2_controller_2\proposed_SellerDashboardController.php`.
   - Ensure all 6 KPIs, 7-day revenue chart data with dynamic SVG bar scaling, order pipeline distribution, low-stock inventory list, trust score breakdown, top products sales velocity with fallback, and live auction spotlight query (`Auction::where('seller_id', $sellerProfile->id)...`) are implemented with strict multi-tenant scoping.
2. Update `routes/web.php` to wire:
   `Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');`
   Import `App\Http\Controllers\Seller\SellerDashboardController;` at the top of `routes/web.php`.
3. Create `resources/views/seller/dashboard.blade.php` using the blueprint in `c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m2_2\handoff.md` section 4.
   - Ensure it extends `layouts.seller`.
   - Ensure all 6 KPI cards, 7-day SVG revenue chart with peak day highlight, segmented order pipeline bar, low stock alerts list, 4-meter trust score breakdown, top products velocity table, live wholesale auction spotlight card with JS countdown timer, and recent store orders table are rendered.
   - Include defensive fallback defaults in the top `@php` block so it never throws 500 errors on missing variables.
4. Deploy the automated test suite `tests/Feature/Seller/SellerDashboardTest.php` from `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m2_tests_2\proposed_SellerDashboardTest.php`.
5. Run the tests:
   - `php -l app/Http/Controllers/Seller/SellerDashboardController.php`
   - `php -l routes/web.php`
   - `php artisan test --filter=SellerDashboardTest`
   - `php artisan test --filter=Seller`
   - `php artisan test` (must pass all tests with 0 failures and 0 regressions).
6. Document results in `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m2_2\handoff.md` and notify parent.

## 2026-09-30T05:48:59Z
You are worker_m2_2 working in c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m2_2.
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically 2026-09-30T04:46:52Z, R2), c:\xampp\htdocs\bazaario\PROJECT.md (Milestone 2), and c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m2_2\DISPATCH.md.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Implement Milestone 2:
1. Implement app/Http/Controllers/Seller/SellerDashboardController.php using the blueprint in c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m2_controller_2\proposed_SellerDashboardController.php.
2. Update routes/web.php to bind Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');.
3. Implement resources/views/seller/dashboard.blade.php using the blueprint in c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m2_2\handoff.md section 4.
4. Implement tests/Feature/Seller/SellerDashboardTest.php using the blueprint in c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m2_tests_2\proposed_SellerDashboardTest.php.
5. Run lint checks and tests: php -l on controller and routes, php artisan test --filter=SellerDashboardTest, and full test regression php artisan test. Ensure 100% pass with 0 failures.
6. Write full handoff report with verification commands and outputs to c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m2_2\handoff.md and send_message to parent (conversation ID: 6f703d77-7d87-49d3-b5fa-6d8efb15a7cc).

