<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\User\DashboardController;

// index
Route::get('/', function () {
    return view('index'); 
})->name('home');

// Public Marketplace & Product Catalog Routes
Route::get('/products', function () {
    return view('user.products.index');
})->name('products.index');

Route::get('/shop', function () {
    return redirect()->route('products.index');
})->name('shop');

Route::get('/product/{slug?}', function ($slug = 'handcrafted-leather-messenger-bag') {
    return view('user.products.show', compact('slug'));
})->name('products.show');

Route::get('/category/{slug?}', function ($slug = 'all') {
    return view('user.products.category', compact('slug'));
})->name('category.show');

Route::get('/auctions', function () {
    return view('user.account.auctions');
})->name('auctions.index');

Route::get('/cart', function () {
    return view('user.cart.index');
})->name('cart.index');

Route::get('/checkout', function () {
    return view('user.checkout.index');
})->name('checkout.index');

Route::get('/account', function () {
    return view('user.account.profile');
})->name('account.index');

//Authentication Routes (for users and sellers)

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register/send-otp', [AuthController::class, 'sendOtp'])->name('register.send-otp');
Route::post('/register/verify-otp', [AuthController::class, 'verifyOtpAndRegister'])->name('register.verify-otp');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::prefix('user')->name('user.')->group(function () {
    Route::middleware(['auth:user','user'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});


Route::prefix('seller')->name('seller.')->group(function () {
    Route::middleware(['auth:seller','seller'])->group(function () {

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


Route::prefix('admin')->name('admin.')->group(function () {

    // Login
    Route::get('/login', [AdminAuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AdminAuthController::class, 'login'])
        ->name('login.submit');


    // Protected Admin Area
    Route::middleware(['auth:admin', 'admin'])->group(function () {

        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');


        Route::post('/logout', [AdminAuthController::class, 'logout'])
            ->name('logout');
    });

});




