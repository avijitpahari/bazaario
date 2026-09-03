<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Bazaario — Full Multi-Vendor Marketplace Routes
|--------------------------------------------------------------------------
| Complete page inventory mapping with named routes.
*/

// ── 1. Public / Buyer-Facing Pages ───────────────────────────────────────
Route::view('/', 'home.index')->name('home');
Route::view('/products', 'products.index')->name('products.index');
Route::view('/category/{slug}', 'products.index')->name('category.show');
Route::view('/product/{slug}', 'products.show')->name('products.show');
Route::view('/stall/{slug}', 'stall.show')->name('stall.show');
Route::view('/cart', 'cart.index')->name('cart.index');
Route::view('/checkout', 'checkout.index')->name('checkout.index');
Route::view('/order/{id}/confirmation', 'orders.confirmation')->name('orders.confirmation');

// ── 2. Auth Pages ────────────────────────────────────────────────────────
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');

// ── 3. Customer Profile & Order History ──────────────────────────────────
Route::view('/account', 'account.index')->name('account.index');
Route::view('/account/orders', 'account.index')->name('account.orders');

// ── 4. Seller-Facing Pages ───────────────────────────────────────────────
Route::view('/seller/register', 'seller.onboarding.index')->name('seller.register');
Route::view('/seller/onboarding', 'seller.onboarding.index')->name('seller.onboarding.index');
Route::view('/seller/dashboard', 'seller.dashboard.index')->name('seller.dashboard');
Route::view('/seller/products', 'seller.products.index')->name('seller.products.index');
Route::view('/seller/products/create', 'seller.products.create')->name('seller.products.create');
Route::view('/seller/products/{id}/edit', 'seller.products.create')->name('seller.products.edit');
Route::view('/seller/orders', 'seller.orders.index')->name('seller.orders.index');
Route::view('/seller/payouts', 'seller.payouts.index')->name('seller.payouts.index');
Route::view('/seller/settings', 'seller.payouts.index')->name('seller.settings');

// ── 5. Admin-Facing Pages ────────────────────────────────────────────────
Route::view('/admin/dashboard', 'admin.dashboard.index')->name('admin.dashboard');
Route::view('/admin/sellers/pending', 'admin.sellers.pending')->name('admin.sellers.pending');
Route::view('/admin/sellers', 'admin.sellers.index')->name('admin.sellers.index');
Route::view('/admin/categories', 'admin.categories.index')->name('admin.categories.index');
Route::view('/admin/orders', 'admin.orders.index')->name('admin.orders.index');
Route::view('/admin/settings/branding', 'admin.settings.branding')->name('admin.settings.branding');
Route::view('/admin/settings/platform', 'admin.settings.platform')->name('admin.settings.platform');

// ── 6. Shared / System Pages & Error Previews ───────────────────────────
Route::view('/maintenance', 'pages.maintenance')->name('maintenance');
Route::view('/404', 'errors.404')->name('errors.404');
Route::view('/403', 'errors.403')->name('errors.403');
Route::view('/500', 'errors.500')->name('errors.500');
