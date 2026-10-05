# DISPATCH: Worker M3 (Product & Inventory Management Implementation)

## Task Description
You are `worker_m3_2` working in `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3_2`.
Your parent is `orchestrator_4` (conversation ID: 6f703d77-7d87-49d3-b5fa-6d8efb15a7cc).

### MANDATORY INTEGRITY WARNING
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

### Authoritative Context & Findings to Read:
1. `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md` (timestamp 2026-09-30T04:46:52Z, R3)
2. `c:\xampp\htdocs\bazaario\PROJECT.md` (Milestone 3, Features 16–25)
3. `c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m3_1\handoff.md` (all 4 Blade blueprints)
4. `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m3_controller_1\handoff.md`
5. `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m3_controller_1\proposed_SellerProductController.php`
6. `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m3_controller_1\proposed_SellerProductManagementTest.php`

### Implementation Steps:
1. Deploy `app/Http/Controllers/Seller/SellerProductController.php` using the blueprint in `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m3_controller_1\proposed_SellerProductController.php`.
   - Ensure all actions (`index`, `create`, `store`, `edit`, `update`, `destroy`, `inventory`, `adjustStock`) are implemented with strict multi-tenant scoping (`where('seller_id', $user->id)`), unit types (`kg`, `dozen`, `bundle`, `litre`, `piece`, `pack`), harvest date and expiry window calculation, image uploads to `public/storage/products`, safe deletion guardrail, and stock adjustments.
2. Update `routes/web.php` to wire:
   - `Route::get('/products', [SellerProductController::class, 'index'])->name('products.index');`
   - `Route::get('/products/create', [SellerProductController::class, 'create'])->name('products.create');`
   - `Route::post('/products', [SellerProductController::class, 'store'])->name('products.store');`
   - `Route::get('/products/{product}/edit', [SellerProductController::class, 'edit'])->name('products.edit');`
   - `Route::put('/products/{product}', [SellerProductController::class, 'update'])->name('products.update');`
   - `Route::delete('/products/{product}', [SellerProductController::class, 'destroy'])->name('products.destroy');`
   - `Route::get('/inventory', [SellerProductController::class, 'inventory'])->name('products.inventory');`
   - `Route::post('/inventory/{product}/adjust', [SellerProductController::class, 'adjustStock'])->name('products.adjust-stock');`
   Import `App\Http\Controllers\Seller\SellerProductController;` at the top of `routes/web.php`.
3. Implement the 4 Blade views in `resources/views/seller/products/`:
   - `index.blade.php`: Catalog master table, search & filter toolbar, status tabs, automated freshness engine banner, slide-over inspect drawer, and delete modal.
   - `create.blade.php`: Add Product workstation with 6 UoM buttons, harvest date picker, shelf-life window days, media upload dropzone mosaic, live buyer simulation card.
   - `edit.blade.php`: Pre-filled edit workstation for existing models.
   - `inventory.blade.php`: Warehouse stock telemetry table, inline quick-adjustment steppers (+/-), critical stock alert banner, 4 KPI cards, and quick restock / adjustment modal.
   Ensure all views extend `layouts.seller` and include defensive fallback defaults in the top `@php` block.
4. Deploy the automated test suite `tests/Feature/Seller/SellerProductManagementTest.php` from `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m3_controller_1\proposed_SellerProductManagementTest.php`.
5. Run lint checks and tests:
   - `php -l app/Http/Controllers/Seller/SellerProductController.php`
   - `php -l routes/web.php`
   - `php -l resources/views/seller/products/index.blade.php`
   - `php -l resources/views/seller/products/create.blade.php`
   - `php -l resources/views/seller/products/edit.blade.php`
   - `php -l resources/views/seller/products/inventory.blade.php`
   - `php artisan view:clear && php artisan view:cache`
   - `php artisan test --filter=SellerProductManagementTest`
   - `php artisan test --filter=Seller`
   - Full regression pass: `php artisan test` (must pass 100% with 0 failures and 0 regressions).
6. Document results in `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3_2\handoff.md` and notify parent.

## 2026-09-30T06:40:43Z
From: 6f703d77-7d87-49d3-b5fa-6d8efb15a7cc
Message:
Implement Milestone 3:
1. Deploy app/Http/Controllers/Seller/SellerProductController.php from c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m3_controller_1\proposed_SellerProductController.php.
2. Update routes/web.php with all seller product routes under Route::prefix('seller')->name('seller.')->middleware(['auth:seller', 'seller']).
3. Deploy all 4 Blade views in resources/views/seller/products/ (index.blade.php, create.blade.php, edit.blade.php, inventory.blade.php) using the blueprints in c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m3_1\handoff.md.
4. Deploy tests/Feature/Seller/SellerProductManagementTest.php from c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m3_controller_1\proposed_SellerProductManagementTest.php.
5. Run lint and test verification: php -l on all controller/view/route files, php artisan view:clear && php artisan view:cache, php artisan test --filter=SellerProductManagementTest, and php artisan test. Ensure 100% pass with 0 failures and 0 regressions.
6. Write full handoff report to c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3_2\handoff.md and send_message to parent (conversation ID: 6f703d77-7d87-49d3-b5fa-6d8efb15a7cc).
