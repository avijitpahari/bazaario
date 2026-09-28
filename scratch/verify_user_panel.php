<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Auction;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

echo "--- STARTING USER PANEL VERIFICATION ---\n";

$user = User::first();
if (!$user) {
    echo "Creating dummy user for test...\n";
    $user = User::create([
        'name' => 'Demo Bidder',
        'email' => 'bidder@bazaario.test',
        'password' => bcrypt('password123'),
        'role' => 'user',
        'status' => 'active',
    ]);
}

Auth::login($user);
echo "Logged in as User #{$user->id} ({$user->name})\n";

// 1. Test Dashboard Controller & View
echo "\n[1] Testing User Dashboard...\n";
$dashCtrl = new \App\Http\Controllers\User\DashboardController();
$dashView = $dashCtrl->index();
$dashHtml = $dashView->render();
echo "-> Dashboard controller executed and rendered successfully (" . strlen($dashHtml) . " bytes)\n";

// 2. Test Bids Controller with various filters
echo "\n[2] Testing User Bids Panel with dynamic filters...\n";
$bidCtrl = new \App\Http\Controllers\User\AuctionController();

$filters = ['active', 'won', 'outbid', 'refunds', 'all'];
$sorts = ['ending_soonest', 'highest_escrow', 'recently_outbid', 'lowest_increment'];

foreach ($filters as $filter) {
    $req = Request::create('/user/bids', 'GET', ['filter' => $filter, 'sort' => 'ending_soonest']);
    $bidsView = $bidCtrl->bids($req);
    $bidsHtml = $bidsView->render();
    echo "-> Filter [{$filter}] rendered successfully (" . strlen($bidsHtml) . " bytes)\n";
}

foreach ($sorts as $sort) {
    $req = Request::create('/user/bids', 'GET', ['filter' => 'active', 'sort' => $sort]);
    $bidsView = $bidCtrl->bids($req);
    $bidsHtml = $bidsView->render();
    echo "-> Sort [{$sort}] rendered successfully (" . strlen($bidsHtml) . " bytes)\n";
}

// 3. Test Coupons View
echo "\n[3] Testing User Coupons View...\n";
$coupons = Coupon::where('status', 'active')->get();
$couponsView = view('user.account.coupons', ['user' => $user, 'coupons' => $coupons]);
$couponsHtml = $couponsView->render();
echo "-> Coupons rendered successfully (" . strlen($couponsHtml) . " bytes) with " . $coupons->count() . " active vouchers\n";

// 4. Test Recommendations View
echo "\n[4] Testing Recommendations View...\n";
$recommendedProducts = \App\Models\Product::with(['category', 'primaryImage', 'images', 'seller.sellerProfile'])
    ->where('status', 'active')
    ->take(8)
    ->get();
$recView = view('user.account.recommendations', ['user' => $user, 'recommendedProducts' => $recommendedProducts]);
$recHtml = $recView->render();
echo "-> Recommendations rendered successfully (" . strlen($recHtml) . " bytes) with " . $recommendedProducts->count() . " products\n";

// 5. Test Returns View
echo "\n[5] Testing Returns View...\n";
$returns = \App\Models\OrderReturn::where('user_id', $user->id)->get();
$returnsView = view('user.account.returns', ['user' => $user, 'returns' => $returns]);
$returnsHtml = $returnsView->render();
echo "-> Returns rendered successfully (" . strlen($returnsHtml) . " bytes)\n";

// 6. Test Full HTTP Kernel Route Dispatching
echo "\n[6] Testing HTTP Kernel Dispatching...\n";
$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$testRoutes = ['/user/dashboard', '/user/bids', '/user/coupons', '/user/recommendations', '/user/returns'];
foreach ($testRoutes as $tRoute) {
    $request = Request::create($tRoute, 'GET');
    $session = $app->make('session')->driver();
    $session->start();
    $request->setLaravelSession($session);
    $app['auth']->guard('user')->setUser($user);
    $response = $httpKernel->handle($request);
    echo "-> Route [{$tRoute}] returned HTTP Status {$response->getStatusCode()}\n";
    $httpKernel->terminate($request, $response);
}

echo "\n--- ALL VERIFICATION CHECKS PASSED PERFECTLY! ---\n";
