<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminAuthController;
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

Route::get('/test', function () {
    return view('test');
});

Route::get('/', function () {
    $dbCategories = Cache::store('file')->remember('home_categories', 600, function () {
        return \App\Models\Category::where('status', 'active')->withCount('products')->get();
    });



    $trendingProducts = Cache::store('file')->remember('home_trending_products', 300, function () {
        return \App\Models\Product::with(['category', 'seller.sellerProfile', 'primaryImage', 'images'])
            ->where('status', 'active')
            ->latest()
            ->take(10)
            ->get();
    });

    $featuredAuction = Cache::store('file')->remember('home_featured_auction', 120, function () {
        return \App\Models\Auction::with(['product.primaryImage', 'product.images', 'bids'])
            ->whereIn('status', ['live', 'active'])
            ->latest()
            ->first();
    });

    $reviews = Cache::store('file')->remember('home_reviews', 600, function () {
        return \App\Models\Review::with(['user', 'product'])
            ->latest()
            ->take(3)
            ->get();
    });

    $featuredSellers = Cache::store('file')->remember('home_featured_sellers', 300, function () {
        return \App\Models\User::where('role', 'seller')
            ->with(['sellerProfile'])
            ->withCount('products')
            ->latest()
            ->take(4)
            ->get();
    });

    $stats = [
        'total_products' => \App\Models\Product::where('status', 'active')->count(),
        'total_sellers' => max(4, \App\Models\User::where('role', 'seller')->count()),
        'total_categories' => \App\Models\Category::where('status', 'active')->count(),
        'active_auctions' => \App\Models\Auction::whereIn('status', ['live', 'active'])->count(),
    ];

    return view('index', compact('dbCategories', 'trendingProducts', 'featuredAuction', 'reviews', 'stats', 'featuredSellers'));
})->name('home');

use Illuminate\Support\Facades\Cache;

// ── Public Marketplace & Product Catalog ────────────────────────────────────

Route::get('/products', function () {
    $dbProducts = Cache::store('file')->remember('marketplace_products_active', 300, function () {
        return \App\Models\Product::with(['category', 'seller.sellerProfile', 'images', 'primaryImage'])
            ->where('status', 'active')
            ->latest()
            ->take(60)
            ->get();
    });
    
    $dbCategories = Cache::store('file')->remember('marketplace_categories_active', 600, function () {
        return \App\Models\Category::where('status', 'active')->get();
    });
    
    return view('user.products.index', compact('dbProducts', 'dbCategories'));
})->name('products.index');

Route::get('/shop', function () {
    return redirect()->route('products.index');
})->name('shop');

Route::get('/product/{slug?}', function ($slug = 'handcrafted-leather-messenger-bag') {
    $product = Cache::store('file')->remember("product_detail_{$slug}", 300, function () use ($slug) {
        return \App\Models\Product::with(['category', 'seller.sellerProfile', 'images', 'reviews.user'])
            ->where('slug', $slug)
            ->first() ?? \App\Models\Product::with(['category', 'seller.sellerProfile', 'images', 'reviews.user'])->first();
    });
    
    $relatedProducts = Cache::store('file')->remember("product_related_{$slug}", 300, function () use ($product) {
        return \App\Models\Product::with(['category', 'primaryImage', 'images'])
            ->where('id', '!=', $product ? $product->id : 0)
            ->where('status', 'active')
            ->take(4)
            ->get();
    });

    return view('user.products.show', compact('product', 'slug', 'relatedProducts'));
})->name('products.show');

Route::get('/category/{slug?}', function ($slug = 'all') {
    $categories = Cache::store('file')->remember('marketplace_categories_active', 600, function () {
        return \App\Models\Category::where('status', 'active')->get();
    });

    $category = null;
    if ($slug !== 'all') {
        $category = $categories->firstWhere('slug', $slug) 
            ?? \App\Models\Category::where('slug', $slug)->first();
    }
    
    $products = Cache::store('file')->remember("category_products_{$slug}", 300, function () use ($category) {
        $productsQuery = \App\Models\Product::with(['category', 'seller.sellerProfile', 'images', 'primaryImage'])
            ->where('status', 'active');
            
        if ($category) {
            $productsQuery->where('category_id', $category->id);
        }
        
        return $productsQuery->latest()->take(60)->get();
    });
    
    return view('user.products.category', compact('slug', 'category', 'categories', 'products'));
})->name('category.show');


Route::get('/auctions', function () {
    return view('user.account.auctions');
})->name('auctions.index');

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

// ── Authentication ──────────────────────────────────────────────────────────

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register/send-otp', [AuthController::class, 'sendOtp'])->name('register.send-otp');
Route::post('/register/resend-otp', [AuthController::class, 'resendOtp'])->name('register.resend-otp');
Route::post('/register/verify-otp', [AuthController::class, 'verifyOtpAndRegister'])->name('register.verify-otp');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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

    // Static-data pages (no complex controller needed yet)
    Route::get('/coupons', fn () => view('user.account.coupons', ['user' => Auth::user()]))->name('coupons');
    Route::get('/auctions', fn () => view('user.account.auctions', ['user' => Auth::user()]))->name('auctions');
    Route::get('/bids', fn () => view('user.account.bids', ['user' => Auth::user()]))->name('bids');
    Route::get('/returns', fn () => view('user.account.returns', ['user' => Auth::user()]))->name('returns');
    Route::get('/invoices', fn () => view('user.account.invoices', ['user' => Auth::user()]))->name('invoices');
    Route::get('/recommendations', fn () => view('user.account.recommendations', ['user' => Auth::user()]))->name('recommendations');
    Route::get('/compare', fn () => view('user.account.compare', ['user' => Auth::user()]))->name('compare');
});

// ── Seller Panel ────────────────────────────────────────────────────────────

Route::prefix('seller')->name('seller.')->group(function () {
    Route::middleware(['auth:seller', 'seller'])->group(function () {

        Route::get('/pending', function () {
            $user = Auth::guard('seller')->user();
            if ($user && $user->role === 'seller' && $user->sellerProfile && $user->sellerProfile->status === 'approved') {
                return redirect()->route('seller.dashboard');
            }
            return view('seller.pending', compact('user'));
        })->name('pending');

        Route::get('/dashboard', function () {
            $user = Auth::guard('seller')->user();
            return view('seller.dashboard', compact('user'));
        })->name('dashboard');
    });
});

// ── Admin Panel ─────────────────────────────────────────────────────────────

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');

    Route::middleware(['auth:admin', 'admin'])->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});
