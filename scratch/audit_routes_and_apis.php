<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;
use App\Models\Product;
use App\Models\Auction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "═══════════════════════════════════════════════════════════════" . PHP_EOL;
echo " BAZAARIO FULL-SYSTEM ROUTE & API FORENSIC AUDIT (LIVE)" . PHP_EOL;
echo "═══════════════════════════════════════════════════════════════" . PHP_EOL;

$admin = User::where('role', 'admin')->first();
$seller = User::where('email', 'seller@gmail.com')->first();
$customer = User::where('email', 'avijitpahari867@gmail.com')->first();
$sampleProduct = Product::where('status', 'active')->first();
$sampleAuction = Auction::where('status', 'live')->first();
$sampleCategory = Category::first();

$testCases = [
    // PUBLIC ROUTES
    ['label' => 'Homepage', 'method' => 'GET', 'uri' => '/', 'user' => null, 'expected' => 200],
    ['label' => 'Products Catalog', 'method' => 'GET', 'uri' => '/products', 'user' => null, 'expected' => 200],
    ['label' => 'Products Filter (Deals)', 'method' => 'GET', 'uri' => '/products?filter=deals', 'user' => null, 'expected' => 200],
    ['label' => 'Products Search', 'method' => 'GET', 'uri' => '/products?search=mango', 'user' => null, 'expected' => 200],
    ['label' => 'Category Listing', 'method' => 'GET', 'uri' => '/category/' . $sampleCategory->slug, 'user' => null, 'expected' => 200],
    ['label' => 'Product Details', 'method' => 'GET', 'uri' => '/products/' . $sampleProduct->slug, 'user' => null, 'expected' => 200],
    ['label' => 'Auctions Terminal', 'method' => 'GET', 'uri' => '/auctions', 'user' => null, 'expected' => 200],
    ['label' => 'Auction Detail View', 'method' => 'GET', 'uri' => '/auctions/' . $sampleAuction->id, 'user' => null, 'expected' => 200],
    ['label' => 'Login Page', 'method' => 'GET', 'uri' => '/login', 'user' => null, 'expected' => 200],
    ['label' => 'Register Page', 'method' => 'GET', 'uri' => '/register', 'user' => null, 'expected' => 200],
    ['label' => 'Privacy Policy', 'method' => 'GET', 'uri' => '/privacy', 'user' => null, 'expected' => 200],
    ['label' => 'Terms of Service', 'method' => 'GET', 'uri' => '/terms', 'user' => null, 'expected' => 200],
    ['label' => 'Return Policy', 'method' => 'GET', 'uri' => '/return-policy', 'user' => null, 'expected' => 200],
    ['label' => 'How It Works', 'method' => 'GET', 'uri' => '/how-it-works', 'user' => null, 'expected' => 200],

    // CUSTOMER ROUTES
    ['label' => 'Customer Profile', 'method' => 'GET', 'uri' => '/account/profile', 'user' => $customer, 'expected' => 200],
    ['label' => 'Customer Orders', 'method' => 'GET', 'uri' => '/account/orders', 'user' => $customer, 'expected' => 200],
    ['label' => 'Customer Addresses', 'method' => 'GET', 'uri' => '/account/addresses', 'user' => $customer, 'expected' => 200],
    ['label' => 'Customer Bids Desk', 'method' => 'GET', 'uri' => '/account/bids', 'user' => $customer, 'expected' => 200],
    ['label' => 'Customer Won Auctions', 'method' => 'GET', 'uri' => '/account/auctions', 'user' => $customer, 'expected' => 200],
    ['label' => 'Customer Cart View', 'method' => 'GET', 'uri' => '/cart', 'user' => $customer, 'expected' => 200],
    ['label' => 'Customer Support Desk', 'method' => 'GET', 'uri' => '/support', 'user' => $customer, 'expected' => 200],
    ['label' => 'Customer Compare View', 'method' => 'GET', 'uri' => '/compare', 'user' => $customer, 'expected' => 200],

    // SELLER ROUTES
    ['label' => 'Seller Dashboard', 'method' => 'GET', 'uri' => '/seller/dashboard', 'user' => $seller, 'expected' => 200],
    ['label' => 'Seller Products Index', 'method' => 'GET', 'uri' => '/seller/products', 'user' => $seller, 'expected' => 200],
    ['label' => 'Seller Add Product Form', 'method' => 'GET', 'uri' => '/seller/products/create', 'user' => $seller, 'expected' => 200],
    ['label' => 'Seller Inventory Telemetry', 'method' => 'GET', 'uri' => '/seller/inventory', 'user' => $seller, 'expected' => 200],
    ['label' => 'Seller Orders Queue', 'method' => 'GET', 'uri' => '/seller/orders', 'user' => $seller, 'expected' => 200],
    ['label' => 'Seller Auctions Manager', 'method' => 'GET', 'uri' => '/seller/auctions', 'user' => $seller, 'expected' => 200],
    ['label' => 'Seller Live Auction Terminal', 'method' => 'GET', 'uri' => '/seller/auctions/live', 'user' => $seller, 'expected' => 200],
    ['label' => 'Seller Auction History', 'method' => 'GET', 'uri' => '/seller/auctions/history', 'user' => $seller, 'expected' => 200],
    ['label' => 'Seller Escrow Payouts Ledger', 'method' => 'GET', 'uri' => '/seller/payouts', 'user' => $seller, 'expected' => 200],
    ['label' => 'Seller Account Settings', 'method' => 'GET', 'uri' => '/seller/settings', 'user' => $seller, 'expected' => 200],
    ['label' => 'Seller Support Center', 'method' => 'GET', 'uri' => '/seller/support', 'user' => $seller, 'expected' => 200],

    // ADMIN ROUTES
    ['label' => 'Admin Dashboard', 'method' => 'GET', 'uri' => '/admin/dashboard', 'user' => $admin, 'expected' => 200],
    ['label' => 'Admin Sellers List', 'method' => 'GET', 'uri' => '/admin/sellers', 'user' => $admin, 'expected' => 200],
    ['label' => 'Admin KYC Approvals Queue', 'method' => 'GET', 'uri' => '/admin/sellers/approvals', 'user' => $admin, 'expected' => 200],
    ['label' => 'Admin Product Catalog', 'method' => 'GET', 'uri' => '/admin/products', 'user' => $admin, 'expected' => 200],
    ['label' => 'Admin Orders Monitor', 'method' => 'GET', 'uri' => '/admin/orders', 'user' => $admin, 'expected' => 200],
    ['label' => 'Admin Live Auctions Desk', 'method' => 'GET', 'uri' => '/admin/auctions', 'user' => $admin, 'expected' => 200],
    ['label' => 'Admin Escrow Settlements', 'method' => 'GET', 'uri' => '/admin/payouts', 'user' => $admin, 'expected' => 200],
    ['label' => 'Admin Dispute Mediation', 'method' => 'GET', 'uri' => '/admin/disputes', 'user' => $admin, 'expected' => 200],
    ['label' => 'Admin Category Taxonomy', 'method' => 'GET', 'uri' => '/admin/categories', 'user' => $admin, 'expected' => 200],
    ['label' => 'Admin Coupons & Vouchers', 'method' => 'GET', 'uri' => '/admin/coupons', 'user' => $admin, 'expected' => 200],
    ['label' => 'Admin Customer Support Desk', 'method' => 'GET', 'uri' => '/admin/support', 'user' => $admin, 'expected' => 200],
    ['label' => 'Admin Platform Settings', 'method' => 'GET', 'uri' => '/admin/settings', 'user' => $admin, 'expected' => 200],
    ['label' => 'Admin Financial Analytics', 'method' => 'GET', 'uri' => '/admin/analytics', 'user' => $admin, 'expected' => 200],
];

$passCount = 0;
$failCount = 0;
$issues = [];

foreach ($testCases as $tc) {
    if ($tc['user']) {
        Auth::login($tc['user']);
    } else {
        Auth::logout();
    }

    $request = Request::create($tc['uri'], $tc['method']);
    
    // Set session
    $session = $app->make('session')->driver();
    $session->start();
    $request->setLaravelSession($session);

    try {
        $response = $httpKernel->handle($request);
        $status = $response->getStatusCode();
        $httpKernel->terminate($request, $response);

        if ($status === $tc['expected'] || ($tc['expected'] === 200 && ($status === 302 && str_contains($tc['uri'], 'checkout')))) {
            echo sprintf("  [PASS %d] %-35s -> %s\n", $status, $tc['label'], $tc['uri']);
            $passCount++;
        } else {
            echo sprintf("  [FAIL %d (Expected %d)] %-35s -> %s\n", $status, $tc['expected'], $tc['label'], $tc['uri']);
            $failCount++;
            $issues[] = [
                'label' => $tc['label'],
                'uri' => $tc['uri'],
                'status' => $status,
                'expected' => $tc['expected'],
                'content' => substr(strip_tags($response->getContent()), 0, 180)
            ];
        }
    } catch (\Throwable $e) {
        echo sprintf("  [EXCEPTION] %-35s -> %s (%s)\n", $tc['label'], $tc['uri'], $e->getMessage());
        $failCount++;
        $issues[] = [
            'label' => $tc['label'],
            'uri' => $tc['uri'],
            'exception' => $e->getMessage(),
        ];
    }
}

echo PHP_EOL . "═══════════════════════════════════════════════════════════════" . PHP_EOL;
echo " AUDIT SUMMARY: {$passCount} Passed, {$failCount} Failed" . PHP_EOL;
echo "═══════════════════════════════════════════════════════════════" . PHP_EOL;

if (!empty($issues)) {
    echo "ISSUES FOUND:" . PHP_EOL;
    print_r($issues);
}
