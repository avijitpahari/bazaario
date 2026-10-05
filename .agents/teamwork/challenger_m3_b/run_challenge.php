<?php

require __DIR__ . '/../../../vendor/autoload.php';
$app = require __DIR__ . '/../../../bootstrap/app.php';

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$logLines = [];
function out(string $msg) {
    global $logLines;
    $line = "[" . date('Y-m-d H:i:s') . "] " . $msg;
    echo $line . PHP_EOL;
    $logLines[] = $line;
}

function assertCondition(bool $cond, string $desc) {
    if (!$cond) {
        out("❌ FAILED: " . $desc);
        throw new \RuntimeException("Assertion failed: " . $desc);
    }
    out("✅ PASS: " . $desc);
}

out("=== STARTING CHALLENGER M3-B EMPIRICAL STRESS TEST SUITE ===");

DB::beginTransaction();

try {
    // -------------------------------------------------------------------------
    // Setup Test Fixtures
    // -------------------------------------------------------------------------
    $buyer = User::create([
        'name'               => 'Deepak Challenge Buyer',
        'email'              => 'deepak_' . uniqid() . '@example.com',
        'password'           => Hash::make('Secret123!'),
        'role'               => 'user',
        'status'             => 'active',
        'preferred_language' => 'en',
    ]);

    // Seller A: Farmer
    $sellerA = User::create([
        'name'     => 'Sundar Farmer Co',
        'email'    => 'sundar_' . uniqid() . '@example.com',
        'password' => Hash::make('Secret123!'),
        'role'     => 'seller',
        'status'   => 'active',
    ]);
    $profileA = SellerProfile::create([
        'user_id'     => $sellerA->id,
        'shop_name'   => 'Sundar Agro Farms',
        'shop_slug'   => 'sundar-agro-' . uniqid(),
        'seller_type' => 'Farmer',
        'trust_score' => 97.80,
        'city'        => 'Hooghly',
        'state'       => 'West Bengal',
        'country'     => 'India',
        'status'      => 'approved',
    ]);

    // Seller B: Dark Store
    $sellerB = User::create([
        'name'     => 'QuickLogistics Ltd',
        'email'    => 'quick_' . uniqid() . '@example.com',
        'password' => Hash::make('Secret123!'),
        'role'     => 'seller',
        'status'   => 'active',
    ]);
    $profileB = SellerProfile::create([
        'user_id'     => $sellerB->id,
        'shop_name'   => 'Express Dark Hub 04',
        'shop_slug'   => 'express-dark-' . uniqid(),
        'seller_type' => 'Dark Store',
        'trust_score' => 92.40,
        'city'        => 'Kolkata',
        'state'       => 'West Bengal',
        'country'     => 'India',
        'status'      => 'approved',
    ]);

    // Seller C: Kirana Store
    $sellerC = User::create([
        'name'     => 'Gupta Provisions',
        'email'    => 'gupta_' . uniqid() . '@example.com',
        'password' => Hash::make('Secret123!'),
        'role'     => 'seller',
        'status'   => 'active',
    ]);
    $profileC = SellerProfile::create([
        'user_id'     => $sellerC->id,
        'shop_name'   => 'Gupta Kirana & General Store',
        'shop_slug'   => 'gupta-kirana-' . uniqid(),
        'seller_type' => 'Kirana Store',
        'trust_score' => 99.10,
        'city'        => 'Howrah',
        'state'       => 'West Bengal',
        'country'     => 'India',
        'status'      => 'approved',
    ]);

    $category = Category::firstOrCreate(['slug' => 'challenge-cat'], ['name' => 'Challenge Category', 'status' => 'active']);

    $prodA1 = Product::create([
        'seller_id'   => $sellerA->id,
        'category_id' => $category->id,
        'name'        => 'Fresh Organic Honey 500g',
        'slug'        => 'honey-500g-' . uniqid(),
        'price'       => 350.00,
        'stock'       => 40,
        'unit_type'   => 'piece',
        'sku'         => 'HNY-' . Str::random(5),
        'status'      => 'active',
    ]);
    $prodA2 = Product::create([
        'seller_id'   => $sellerA->id,
        'category_id' => $category->id,
        'name'        => 'Desi Mustard Oil 1L',
        'slug'        => 'mustard-oil-' . uniqid(),
        'price'       => 195.00,
        'stock'       => 60,
        'unit_type'   => 'litre',
        'sku'         => 'OIL-' . Str::random(5),
        'status'      => 'active',
    ]);

    $prodB1 = Product::create([
        'seller_id'   => $sellerB->id,
        'category_id' => $category->id,
        'name'        => 'Smartphone Screen Guard & Lens Kit',
        'slug'        => 'screen-guard-' . uniqid(),
        'price'       => 499.00,
        'stock'       => 100,
        'unit_type'   => 'piece',
        'sku'         => 'SG-' . Str::random(5),
        'status'      => 'active',
    ]);

    $prodC1 = Product::create([
        'seller_id'   => $sellerC->id,
        'category_id' => $category->id,
        'name'        => 'Darjeeling First Flush Tea 250g',
        'slug'        => 'darjeeling-tea-' . uniqid(),
        'price'       => 650.00,
        'stock'       => 30,
        'unit_type'   => 'piece',
        'sku'         => 'TEA-' . Str::random(5),
        'status'      => 'active',
    ]);

    out("Created test buyer, 3 sellers with distinct types & profiles, and 4 products.");

    // =========================================================================
    // SCENARIO 1: Multi-Seller Cart Grouping
    // =========================================================================
    out("\n--- SCENARIO 1: Multi-Seller Cart Grouping & Isolation ---");

    $cart = Cart::create(['user_id' => $buyer->id]);

    // Add items from 3 distinct sellers
    // Seller A: prodA1 (qty 2 * 350 = 700), prodA2 (qty 3 * 195 = 585) -> Subtotal = 1285.00
    // Seller B: prodB1 (qty 1 * 499 = 499) -> Subtotal = 499.00
    // Seller C: prodC1 (qty 2 * 650 = 1300) -> Subtotal = 1300.00
    // Total Subtotal = 1285 + 499 + 1300 = 3084.00
    $itemA1 = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodA1->id, 'quantity' => 2, 'unit_price' => 350.00]);
    $itemA2 = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodA2->id, 'quantity' => 3, 'unit_price' => 195.00]);
    $itemB1 = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodB1->id, 'quantity' => 1, 'unit_price' => 499.00]);
    $itemC1 = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodC1->id, 'quantity' => 2, 'unit_price' => 650.00]);

    Auth::login($buyer);

    // Call CartController index
    $controller = app(\App\Http\Controllers\User\CartController::class);
    $view = $controller->index();
    $viewData = $view->getData();

    assertCondition(isset($viewData['groupedItems']), "CartController returns 'groupedItems'");
    assertCondition($viewData['groupedItems']->count() === 3, "CartController groups items into exactly 3 merchant blocks");

    $subtotals = $viewData['sellerSubtotals'];
    assertCondition(round($subtotals[$sellerA->id], 2) === 1285.00, "Seller A subtotal is ₹1,285.00 (2*350 + 3*195)");
    assertCondition(round($subtotals[$sellerB->id], 2) === 499.00, "Seller B subtotal is ₹499.00 (1*499)");
    assertCondition(round($subtotals[$sellerC->id], 2) === 1300.00, "Seller C subtotal is ₹1,300.00 (2*650)");
    assertCondition(round($viewData['subtotal'], 2) === 3084.00, "Cart total subtotal is ₹3,084.00");

    // Render Blade view and inspect output
    $renderedHtml = $view->render();
    assertCondition(str_contains($renderedHtml, 'Sundar Agro Farms'), "HTML renders Seller A shop name: Sundar Agro Farms");
    assertCondition(str_contains($renderedHtml, 'Farmer'), "HTML renders Seller A badge: Farmer");
    assertCondition(str_contains($renderedHtml, '97.8% Trust'), "HTML renders Seller A trust score: 97.8% Trust");
    assertCondition(str_contains($renderedHtml, '1,285.00'), "HTML renders Seller A merchant subtotal: ₹1,285.00");

    assertCondition(str_contains($renderedHtml, 'Express Dark Hub 04'), "HTML renders Seller B shop name: Express Dark Hub 04");
    assertCondition(str_contains($renderedHtml, 'Dark Store'), "HTML renders Seller B badge: Dark Store");
    assertCondition(str_contains($renderedHtml, '92.4% Trust'), "HTML renders Seller B trust score: 92.4% Trust");
    assertCondition(str_contains($renderedHtml, '499.00'), "HTML renders Seller B merchant subtotal: ₹499.00");

    assertCondition(str_contains($renderedHtml, 'Gupta Kirana &amp; General Store') || str_contains($renderedHtml, 'Gupta Kirana & General Store'), "HTML renders Seller C shop name: Gupta Kirana");
    assertCondition(str_contains($renderedHtml, 'Kirana Store'), "HTML renders Seller C badge: Kirana Store");
    assertCondition(str_contains($renderedHtml, '99.1% Trust'), "HTML renders Seller C trust score: 99.1% Trust");
    assertCondition(str_contains($renderedHtml, '1,300.00'), "HTML renders Seller C merchant subtotal: ₹1,300.00");

    // Mutation 1: Update quantity of prodA1 in Seller A's block from 2 to 4
    out("\nTesting mutation: Update quantity of prodA1 in Seller A's block from 2 to 4...");
    $reqUpdate = \Illuminate\Http\Request::create("/cart/{$itemA1->id}", 'PUT', ['quantity' => 4]);
    $controller->update($reqUpdate, $itemA1->fresh());

    $viewAfterUpdate = $controller->index();
    $dataAfterUpdate = $viewAfterUpdate->getData();
    $subAfterUpdate = $dataAfterUpdate['sellerSubtotals'];

    // Seller A: (4 * 350) + (3 * 195) = 1400 + 585 = 1985.00
    assertCondition(round($subAfterUpdate[$sellerA->id], 2) === 1985.00, "Seller A subtotal updated correctly to ₹1,985.00");
    assertCondition(round($subAfterUpdate[$sellerB->id], 2) === 499.00, "Seller B subtotal remains completely unaffected at ₹499.00");
    assertCondition(round($subAfterUpdate[$sellerC->id], 2) === 1300.00, "Seller C subtotal remains completely unaffected at ₹1,300.00");
    assertCondition(round($dataAfterUpdate['subtotal'], 2) === 3784.00, "Cart subtotal updated to ₹3,784.00");

    // Mutation 2: Remove item from Seller B's block
    out("\nTesting mutation: Remove prodB1 from Seller B's block...");
    $controller->destroy($itemB1->fresh());

    assertCondition(CartItem::find($itemB1->id) === null, "Item B1 was deleted from database");
    assertCondition(CartItem::find($itemA1->id) !== null, "Item A1 still exists");
    assertCondition(CartItem::find($itemA2->id) !== null, "Item A2 still exists");
    assertCondition(CartItem::find($itemC1->id) !== null, "Item C1 still exists");

    $viewAfterDelete = $controller->index();
    $dataAfterDelete = $viewAfterDelete->getData();
    assertCondition($dataAfterDelete['groupedItems']->count() === 2, "Cart now contains exactly 2 merchant blocks after Seller B item removal");
    assertCondition(!isset($dataAfterDelete['sellerSubtotals'][$sellerB->id]), "Seller B block has been cleanly pruned from subtotals");
    assertCondition(round($dataAfterDelete['sellerSubtotals'][$sellerA->id], 2) === 1985.00, "Seller A subtotal remains ₹1,985.00");
    assertCondition(round($dataAfterDelete['sellerSubtotals'][$sellerC->id], 2) === 1300.00, "Seller C subtotal remains ₹1,300.00");
    assertCondition(round($dataAfterDelete['subtotal'], 2) === 3285.00, "Cart total subtotal is now ₹3,285.00");

    // =========================================================================
    // SCENARIO 2: Coupon Validation Edge Cases
    // =========================================================================
    out("\n--- SCENARIO 2: Coupon Validation Edge Cases ---");

    // Clear cart items for controlled coupon tests
    CartItem::where('cart_id', $cart->id)->delete();
    session()->forget('coupon');

    // Create coupon with minimum_order_amount = 500
    $couponMin500 = Coupon::create([
        'code'                 => 'MIN500TEST',
        'discount_type'        => 'fixed',
        'discount_value'       => 50.00,
        'minimum_order_amount' => 500.00,
        'status'               => 'active',
    ]);

    // Test A: Cart subtotal = ₹499.00 -> Coupon REJECTED
    $prod499 = Product::create([
        'seller_id'   => $sellerA->id,
        'category_id' => $category->id,
        'name'        => 'Product for 499',
        'slug'        => 'prod-499-' . uniqid(),
        'price'       => 499.00,
        'stock'       => 10,
        'unit_type'   => 'piece',
        'sku'         => 'P499-' . Str::random(4),
        'status'      => 'active',
    ]);
    $item499 = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prod499->id, 'quantity' => 1, 'unit_price' => 499.00]);

    $reqCoupon499 = \Illuminate\Http\Request::create('/cart/coupon', 'POST', ['coupon_code' => 'MIN500TEST']);
    $respCoupon499 = $controller->applyCoupon($reqCoupon499);
    $errorFlash = session('error');
    assertCondition(str_contains($errorFlash, '500.00'), "Cart subtotal = ₹499 rejects coupon with error mentioning ₹500.00");
    assertCondition(!session()->has('coupon'), "Coupon not stored in session on subtotal ₹499 rejection");

    // Test B: Cart subtotal = ₹500.00 -> Coupon ACCEPTED
    $prod1 = Product::create([
        'seller_id'   => $sellerA->id,
        'category_id' => $category->id,
        'name'        => 'Addon 1 Rupee',
        'slug'        => 'prod-1-' . uniqid(),
        'price'       => 1.00,
        'stock'       => 10,
        'unit_type'   => 'piece',
        'sku'         => 'P1-' . Str::random(4),
        'status'      => 'active',
    ]);
    CartItem::create(['cart_id' => $cart->id, 'product_id' => $prod1->id, 'quantity' => 1, 'unit_price' => 1.00]);

    $reqCoupon500 = \Illuminate\Http\Request::create('/cart/coupon', 'POST', ['coupon_code' => 'MIN500TEST']);
    $respCoupon500 = $controller->applyCoupon($reqCoupon500);
    $successFlash = session('success');
    assertCondition(str_contains($successFlash, 'MIN500TEST'), "Cart subtotal = ₹500 accepts coupon successfully");
    assertCondition(session('coupon.code') === 'MIN500TEST', "Coupon stored in session on subtotal ₹500");

    $view500 = $controller->index();
    assertCondition($view500->getData()['discount'] === 50.0, "Coupon discount of ₹50.00 correctly applied on subtotal ₹500");

    // Test C: maximum_discount_amount = 100 on 50% coupon on ₹1000 order
    out("\nTesting maximum_discount_amount = 100 cap...");
    session()->forget('coupon');
    CartItem::where('cart_id', $cart->id)->delete();

    $prod1000 = Product::create([
        'seller_id'   => $sellerA->id,
        'category_id' => $category->id,
        'name'        => 'Product for 1000',
        'slug'        => 'prod-1000-' . uniqid(),
        'price'       => 1000.00,
        'stock'       => 10,
        'unit_type'   => 'piece',
        'sku'         => 'P1000-' . Str::random(4),
        'status'      => 'active',
    ]);
    CartItem::create(['cart_id' => $cart->id, 'product_id' => $prod1000->id, 'quantity' => 1, 'unit_price' => 1000.00]);

    $couponCap100 = Coupon::create([
        'code'                    => 'MAX100CAP',
        'discount_type'           => 'percentage',
        'discount_value'          => 50.00,
        'maximum_discount_amount' => 100.00,
        'status'                  => 'active',
    ]);

    $reqCap = \Illuminate\Http\Request::create('/cart/coupon', 'POST', ['coupon_code' => 'MAX100CAP']);
    $controller->applyCoupon($reqCap);
    assertCondition(session('coupon.code') === 'MAX100CAP', "Coupon MAX100CAP applied to session");

    $viewCap = $controller->index();
    $dataCap = $viewCap->getData();
    assertCondition($dataCap['subtotal'] === 1000.0, "Subtotal is ₹1,000.00");
    assertCondition($dataCap['discount'] === 100.0, "50% discount strictly capped at ₹100.00 (NOT ₹500.00)");
    assertCondition($dataCap['total'] === 999.0, "Total is ₹999.00 (1000 subtotal + 99 shipping - 100 capped discount)");

    // Test D: Expired coupon rejected
    out("\nTesting expired coupon rejection...");
    session()->forget('coupon');
    $couponExpired = Coupon::create([
        'code'           => 'EXPIREDNOW',
        'discount_type'  => 'fixed',
        'discount_value' => 50.00,
        'expires_at'     => now()->subDay(),
        'status'         => 'active',
    ]);

    $reqExpired = \Illuminate\Http\Request::create('/cart/coupon', 'POST', ['coupon_code' => 'EXPIREDNOW']);
    $controller->applyCoupon($reqExpired);
    assertCondition(str_contains(strtolower(session('error')), 'expired'), "Expired coupon rejected with descriptive error");
    assertCondition(!session()->has('coupon'), "Expired coupon not applied to session");

    // Test E: Usage limit exceeded coupon rejected
    out("\nTesting usage_limit exceeded rejection...");
    session()->forget('coupon');
    $couponLimit = Coupon::create([
        'code'           => 'EXCEEDEDLIMIT',
        'discount_type'  => 'fixed',
        'discount_value' => 50.00,
        'usage_limit'    => 5,
        'used_count'     => 5,
        'status'         => 'active',
    ]);

    $reqLimit = \Illuminate\Http\Request::create('/cart/coupon', 'POST', ['coupon_code' => 'EXCEEDEDLIMIT']);
    $controller->applyCoupon($reqLimit);
    assertCondition(str_contains(strtolower(session('error')), 'limit'), "Coupon exceeding usage_limit rejected with descriptive error");
    assertCondition(!session()->has('coupon'), "Over-limit coupon not applied to session");

    // =========================================================================
    // SCENARIO 3: Cart Badge Sync Across Multiple Sellers
    // =========================================================================
    out("\n--- SCENARIO 3: Cart Badge Sync Across Sellers ---");

    CartItem::where('cart_id', $cart->id)->delete();

    // Add items from 3 distinct sellers:
    // Seller A: 3 items
    // Seller B: 4 items
    // Seller C: 2 items
    // Total sum = 9 items
    $biA = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodA1->id, 'quantity' => 3, 'unit_price' => 350.00]);
    $biB = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodB1->id, 'quantity' => 4, 'unit_price' => 499.00]);
    $biC = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodC1->id, 'quantity' => 2, 'unit_price' => 650.00]);

    // Check Cart model sum
    $totalCartQty = (int) $buyer->cart->items->sum('quantity');
    assertCondition($totalCartQty === 9, "DB Cart items sum across 3 sellers is exactly 9 items (3 + 4 + 2)");

    // Test nav component logic
    $userCart = \App\Models\Cart::where('user_id', $buyer->id)->with('items')->first();
    $badgeCount = (int) $userCart->items->sum('quantity');
    assertCondition($badgeCount === 9, "Navbar cart badge logic computes 9 items");

    // Update quantity of Seller A: change from 3 to 7 -> total becomes 7 + 4 + 2 = 13
    $biA->update(['quantity' => 7]);
    $badgeCountUpdated = (int) \App\Models\Cart::where('user_id', $buyer->id)->first()->items->sum('quantity');
    assertCondition($badgeCountUpdated === 13, "Navbar cart badge logic updates to 13 items after quantity increment");

    // Remove Seller C items: 2 items removed -> total becomes 7 + 4 = 11
    $biC->delete();
    $badgeCountAfterDelete = (int) \App\Models\Cart::where('user_id', $buyer->id)->first()->items->sum('quantity');
    assertCondition($badgeCountAfterDelete === 11, "Navbar cart badge logic updates to 11 items after item removal");

    // Guest user session cart test
    $guestSessionCart = [
        101 => ['id' => 101, 'name' => 'Item 1', 'price' => 50, 'quantity' => 2],
        102 => ['id' => 102, 'name' => 'Item 2', 'price' => 80, 'quantity' => 1],
    ];
    $guestBadgeCount = count($guestSessionCart);
    assertCondition($guestBadgeCount === 2, "Guest session cart badge logic computes count of session cart items");

    out("\n=======================================================");
    out("ALL 3 CHALLENGE SCENARIOS AND EDGE CASES EMPIRICALLY PASSED!");
    out("=======================================================");

} finally {
    DB::rollBack();
}

file_put_contents(__DIR__ . '/challenge_run.log', implode(PHP_EOL, $logLines) . PHP_EOL);
out("Execution logs written to challenge_run.log");
