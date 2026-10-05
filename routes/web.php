<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\AddressController;
use App\Http\Controllers\User\WishlistController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\SecurityController;
use App\Http\Controllers\User\SettingsController;
use App\Http\Controllers\User\AuctionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Seller\SellerOnboardingController;
use App\Http\Controllers\Seller\SellerDashboardController;
use App\Http\Controllers\Seller\SellerProductController;
use App\Http\Controllers\Seller\SellerOrderController;
use App\Http\Controllers\Seller\SellerPayoutController;
use App\Http\Controllers\Seller\SellerProfileController;
use App\Http\Controllers\Seller\SellerAuctionController;

Route::get('/test', function () {
    return view('test');
});

Route::get('/', [ProductController::class, 'home'])->name('home');

// ── Public Marketplace & Product Catalog ────────────────────────────────────

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::get('/shop', function () {
    return redirect()->route('products.index');
})->name('shop');

Route::get('/auctions', [AuctionController::class, 'index'])->name('auctions.index');
Route::get('/auctions/{auction}', [AuctionController::class, 'show'])->name('auctions.show');
Route::post('/auctions/{auction}/bid', [AuctionController::class, 'placeBid'])->name('auctions.placeBid');
Route::post('/auctions/{auction}/quick-bid', [AuctionController::class, 'quickBid'])->name('auctions.quickBid');

Route::get('/product/{slug?}', [ProductController::class, 'show'])->name('products.show');

Route::get('/category/{slug?}', [ProductController::class, 'category'])->name('category.show');

Route::get('/how-it-works', [ProductController::class, 'howItWorks'])->name('pages.how-it-works');
Route::get('/about', [ProductController::class, 'howItWorks'])->name('about');
Route::get('/privacy', [ProductController::class, 'privacy'])->name('pages.privacy');
Route::get('/terms', [ProductController::class, 'terms'])->name('pages.terms');
Route::get('/return-policy', [ProductController::class, 'returnPolicy'])->name('pages.return-policy');

// ── Cart (accessible to guests too, but actions require auth) ───────────────

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

Route::middleware(['auth:user', 'user'])->group(function () {
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
    Route::delete('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
});

// ── Checkout ────────────────────────────────────────────────────────────────

Route::middleware(['auth:user', 'user'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
});

// ── Account Page (alias → dashboard) ───────────────────────────────────────

Route::get('/account', function () {
    if (Auth::guard('user')->check()) {
        return redirect()->route('user.dashboard');
    }
    return redirect()->route('login');
})->name('account.index');

// ── Localization ────────────────────────────────────────────────────────────
Route::post('/language', [LanguageController::class, 'switch'])->name('language.switch');

// ── Authentication ──────────────────────────────────────────────────────────

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register/send-otp', [AuthController::class, 'sendOtp'])->name('register.send-otp');
Route::post('/register/resend-otp', [AuthController::class, 'resendOtp'])->name('register.resend-otp');
Route::post('/register/verify-otp', [AuthController::class, 'verifyOtpAndRegister'])->name('register.verify-otp');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Password Reset ──────────────────────────────────────────────────────────
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// ── User Panel (auth:user required) ────────────────────────────────────────

Route::prefix('user')->name('user.')->middleware(['auth:user', 'user'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');

    // Addresses
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.default');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{wishlistItem}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // Reviews
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Security
    Route::get('/security', [SecurityController::class, 'index'])->name('security');
    Route::post('/security/password', [SecurityController::class, 'updatePassword'])->name('security.password');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Dynamic Account Views
    Route::get('/coupons', function () {
        $coupons = \App\Models\Coupon::where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderBy('minimum_order_amount', 'asc')
            ->get();
        return view('user.account.coupons', ['user' => Auth::user(), 'coupons' => $coupons]);
    })->name('coupons');

    Route::get('/auctions', [AuctionController::class, 'index'])->name('auctions');
    Route::get('/bids', [AuctionController::class, 'bids'])->name('bids');

    Route::get('/returns', function () {
        $returns = \App\Models\OrderReturn::where('user_id', Auth::id())
            ->with(['order', 'orderItem'])
            ->latest('requested_at')
            ->get();
        return view('user.account.returns', ['user' => Auth::user(), 'returns' => $returns]);
    })->name('returns');

    Route::get('/invoices', fn () => view('user.account.invoices', ['user' => Auth::user()]))->name('invoices');

    Route::get('/recommendations', function () {
        $recommendedProducts = \App\Models\Product::with(['category', 'primaryImage', 'images', 'seller.sellerProfile'])
            ->where('status', 'active')
            ->latest()
            ->take(8)
            ->get();
        return view('user.account.recommendations', ['user' => Auth::user(), 'recommendedProducts' => $recommendedProducts]);
    })->name('recommendations');

    Route::get('/compare', fn () => view('user.account.compare', ['user' => Auth::user()]))->name('compare');
});

// ── Public Seller Documentation Pages ────────────────────────────────────────

Route::get('/seller/become-a-seller', [ProductController::class, 'becomeASeller'])->name('docs.become-a-seller');
Route::get('/seller/fees-and-commission', [ProductController::class, 'feesAndCommission'])->name('docs.fees-and-commission');

// ── Seller Panel ────────────────────────────────────────────────────────────

Route::prefix('seller')->name('seller.')->middleware(['auth:seller', 'seller'])->group(function () {

    // Unapproved-accessible: onboarding wizard & pending waiting gate
    Route::get('/onboarding', [SellerOnboardingController::class, 'showWizard'])->name('onboarding');
    Route::post('/onboarding', [SellerOnboardingController::class, 'submitWizard'])->name('onboarding.submit');
    Route::get('/pending', [SellerOnboardingController::class, 'pending'])->name('pending');

    // Approval-required routes (SellerMiddleware redirects unapproved sellers to seller.pending)
    Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');

    // Milestone 3: Product & Inventory Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [SellerProductController::class, 'index'])->name('index');
        Route::get('/create', [SellerProductController::class, 'create'])->name('create');
        Route::post('/', [SellerProductController::class, 'store'])->name('store');
        Route::get('/inventory', [SellerProductController::class, 'inventory'])->name('inventory');
        Route::post('/{product}/stock', [SellerProductController::class, 'adjustStock'])->name('adjust-stock');
        Route::post('/inventory/{product}/adjust', [SellerProductController::class, 'adjustStock']);
        Route::get('/{product}', [SellerProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [SellerProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [SellerProductController::class, 'update'])->name('update');
        Route::post('/bulk', [SellerProductController::class, 'bulkAction'])->name('bulk');
        Route::delete('/{product}', [SellerProductController::class, 'destroy'])->name('destroy');
    });

    // Top-level aliases for inventory
    Route::get('/inventory', [SellerProductController::class, 'inventory']);
    Route::post('/inventory/{product}/adjust', [SellerProductController::class, 'adjustStock']);

    // Milestone 4: Order Fulfillment Desk & Transparent Payout Ledger
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [SellerOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [SellerOrderController::class, 'show'])->name('show');
        Route::patch('/{order}/status', [SellerOrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{order}/fulfill', [SellerOrderController::class, 'fulfill'])->name('fulfill');
        Route::post('/{order}/handover', [SellerOrderController::class, 'handover'])->name('handover');
    });

    Route::prefix('payouts')->name('payouts.')->group(function () {
        Route::get('/', [SellerPayoutController::class, 'index'])->name('index');
        Route::get('/{payout}', [SellerPayoutController::class, 'show'])->name('show');
    });

    Route::prefix('auctions')->name('auctions.')->group(function () {
        Route::get('/', [SellerAuctionController::class, 'index'])->name('index');
        Route::get('/create', [SellerAuctionController::class, 'create'])->name('create');
        Route::post('/', [SellerAuctionController::class, 'store'])->name('store');
        Route::get('/history', [SellerAuctionController::class, 'history'])->name('history');
        Route::get('/live', [SellerAuctionController::class, 'liveTerminal'])->name('live');
        Route::get('/{auction}', [SellerAuctionController::class, 'show'])->name('show');
        Route::post('/{auction}/cancel', [SellerAuctionController::class, 'cancel'])->name('cancel');
    });

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/profile', [SellerProfileController::class, 'profile'])->name('profile');
        Route::put('/profile', [SellerProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile', [SellerProfileController::class, 'updateProfile']);
        Route::get('/location', [SellerProfileController::class, 'location'])->name('location');
        Route::put('/location', [SellerProfileController::class, 'updateLocation'])->name('location.update');
        Route::post('/location', [SellerProfileController::class, 'updateLocation']);
        Route::get('/security', [SellerProfileController::class, 'security'])->name('security');
        Route::put('/security', [SellerProfileController::class, 'updatePassword'])->name('security.update');
        Route::post('/security', [SellerProfileController::class, 'updatePassword']);
        Route::get('/notifications', [SellerProfileController::class, 'notifications'])->name('notifications');
        Route::get('/settings', [SellerProfileController::class, 'settings'])->name('settings');
        Route::put('/settings', [SellerProfileController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings', [SellerProfileController::class, 'updateSettings']);
    });
});

// ── Admin Panel ─────────────────────────────────────────────────────────────

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');

    Route::middleware(['auth:admin', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
        
        // Sellers & Approvals
        Route::get('/sellers', [AdminDashboardController::class, 'sellers'])->name('sellers.index');
        Route::get('/sellers/approvals', [AdminDashboardController::class, 'sellerApprovals'])->name('sellers.approvals');
        Route::get('/sellers/{id}', [AdminDashboardController::class, 'sellerDetail'])->name('sellers.show');
        Route::post('/sellers/{id}/approve', [AdminDashboardController::class, 'approveSeller'])->name('sellers.approve');
        Route::post('/sellers/{id}/reject', [AdminDashboardController::class, 'rejectSeller'])->name('sellers.reject');
        Route::post('/sellers/{id}/toggle-status', [AdminDashboardController::class, 'toggleSellerStatus'])->name('sellers.toggle-status');
        Route::post('/sellers/{id}/commission', [AdminDashboardController::class, 'updateSellerCommission'])->name('sellers.commission');

        // Products & Inventory
        Route::get('/products', [AdminDashboardController::class, 'products'])->name('products.index');
        Route::post('/products/{id}/toggle-status', [AdminDashboardController::class, 'toggleProductStatus'])->name('products.toggle-status');
        Route::post('/products/{id}/update-stock', [AdminDashboardController::class, 'updateProductStock'])->name('products.update-stock');
        Route::delete('/products/{id}', [AdminDashboardController::class, 'deleteProduct'])->name('products.destroy');

        // Categories Taxonomy
        Route::get('/categories', [AdminDashboardController::class, 'categories'])->name('categories.index');
        Route::post('/categories', [AdminDashboardController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{id}', [AdminDashboardController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{id}', [AdminDashboardController::class, 'deleteCategory'])->name('categories.destroy');

        // Orders & Fulfillment
        Route::get('/orders', [AdminDashboardController::class, 'orders'])->name('orders.index');
        Route::get('/orders/{id}', [AdminDashboardController::class, 'orderDetail'])->name('orders.show');
        Route::post('/orders/{id}/status', [AdminDashboardController::class, 'updateOrderStatus'])->name('orders.update-status');

        // Live Auctions
        Route::get('/auctions', [AdminDashboardController::class, 'auctions'])->name('auctions.index');
        Route::get('/auctions/{id}', [AdminDashboardController::class, 'auctionDetail'])->name('auctions.show');
        Route::post('/auctions/{id}/end', [AdminDashboardController::class, 'endAuction'])->name('auctions.end');
        Route::post('/auctions/{id}/cancel', [AdminDashboardController::class, 'cancelAuction'])->name('auctions.cancel');

        // Payouts & Escrow
        Route::get('/payouts', [AdminDashboardController::class, 'payouts'])->name('payouts.index');
        Route::post('/payouts/{id}/release', [AdminDashboardController::class, 'releasePayout'])->name('payouts.release');
        Route::post('/payouts/batch-release', [AdminDashboardController::class, 'batchReleasePayouts'])->name('payouts.batch-release');

        // Disputes & Return Arbitration
        Route::get('/disputes', [AdminDashboardController::class, 'disputes'])->name('disputes.index');
        Route::post('/disputes/{id}/arbitrate', [AdminDashboardController::class, 'arbitrateDispute'])->name('disputes.arbitrate');

        // Customers
        Route::get('/customers', [AdminDashboardController::class, 'customers'])->name('customers.index');
        Route::get('/customers/{id}', [AdminDashboardController::class, 'customerDetail'])->name('customers.show');
        Route::post('/customers/{id}/toggle-status', [AdminDashboardController::class, 'toggleCustomerStatus'])->name('customers.toggle-status');

        // Coupons
        Route::get('/coupons', [AdminDashboardController::class, 'coupons'])->name('coupons.index');
        Route::post('/coupons', [AdminDashboardController::class, 'storeCoupon'])->name('coupons.store');
        Route::post('/coupons/{id}/toggle-status', [AdminDashboardController::class, 'toggleCouponStatus'])->name('coupons.toggle-status');
        Route::delete('/coupons/{id}', [AdminDashboardController::class, 'deleteCoupon'])->name('coupons.destroy');

        // AI Engine Configuration
        Route::get('/settings/ai', [AdminDashboardController::class, 'aiSettings'])->name('settings.ai');
        Route::post('/settings/ai', [AdminDashboardController::class, 'updateAiSettings'])->name('settings.ai.update');

        // ── Admin Impersonation ─────────────────────────────────────────────
        Route::post('/impersonate/{userId}', [\App\Http\Controllers\Admin\ImpersonationController::class, 'impersonate'])->name('impersonate');
        Route::post('/impersonate-stop', [\App\Http\Controllers\Admin\ImpersonationController::class, 'stop'])->name('impersonate.stop');

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});
