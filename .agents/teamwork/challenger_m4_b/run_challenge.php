<?php

require __DIR__ . '/../../../vendor/autoload.php';
$app = require __DIR__ . '/../../../bootstrap/app.php';

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

out("=== STARTING CHALLENGER M4-B EMPIRICAL STRESS TEST SUITE ===");

DB::beginTransaction();

try {
    // -------------------------------------------------------------------------
    // Setup Test Fixtures
    // -------------------------------------------------------------------------
    $buyerA = User::create([
        'name'               => 'Deepak Challenge Buyer A',
        'email'              => 'buyer_a_' . uniqid() . '@example.com',
        'password'           => Hash::make('Secret123!'),
        'role'               => 'user',
        'status'             => 'active',
        'preferred_language' => 'en',
    ]);

    $buyerB = User::create([
        'name'               => 'Sneha Challenge Buyer B',
        'email'              => 'buyer_b_' . uniqid() . '@example.com',
        'password'           => Hash::make('Secret123!'),
        'role'               => 'user',
        'status'             => 'active',
        'preferred_language' => 'en',
    ]);

    $seller1 = User::create([
        'name'     => 'Sundar Farmer Co',
        'email'    => 'sundar_' . uniqid() . '@example.com',
        'password' => Hash::make('Secret123!'),
        'role'     => 'seller',
        'status'   => 'active',
    ]);
    SellerProfile::create([
        'user_id'         => $seller1->id,
        'shop_name'       => 'Sundar Agro Farms',
        'shop_slug'       => 'sundar-agro-' . uniqid(),
        'seller_type'     => 'Farmer',
        'trust_score'     => 98.20,
        'city'            => 'Hooghly',
        'state'           => 'West Bengal',
        'country'         => 'India',
        'status'          => 'approved',
        'commission_rate' => 10.00,
    ]);

    $seller2 = User::create([
        'name'     => 'Gupta Provisions',
        'email'    => 'gupta_' . uniqid() . '@example.com',
        'password' => Hash::make('Secret123!'),
        'role'     => 'seller',
        'status'   => 'active',
    ]);
    SellerProfile::create([
        'user_id'         => $seller2->id,
        'shop_name'       => 'Gupta Kirana Store',
        'shop_slug'       => 'gupta-kirana-' . uniqid(),
        'seller_type'     => 'Kirana Store',
        'trust_score'     => 99.10,
        'city'            => 'Howrah',
        'state'           => 'West Bengal',
        'country'         => 'India',
        'status'          => 'approved',
        'commission_rate' => 10.00,
    ]);

    $category = Category::firstOrCreate(['slug' => 'stress-test-cat'], ['name' => 'Stress Test Cat', 'status' => 'active']);

    $prod1 = Product::create([
        'seller_id'   => $seller1->id,
        'category_id' => $category->id,
        'name'        => 'Gobindobhog Rice 5kg',
        'slug'        => 'gobindobhog-' . uniqid(),
        'price'       => 450.00,
        'stock'       => 10,
        'unit_type'   => 'bag',
        'sku'         => 'RICE-' . Str::random(5),
        'status'      => 'active',
    ]);

    $prod2 = Product::create([
        'seller_id'   => $seller2->id,
        'category_id' => $category->id,
        'name'        => 'Pure Mustard Oil 1L',
        'slug'        => 'mustard-oil-' . uniqid(),
        'price'       => 180.00,
        'stock'       => 15,
        'unit_type'   => 'bottle',
        'sku'         => 'OIL-' . Str::random(5),
        'status'      => 'active',
    ]);

    $orderController = app(\App\Http\Controllers\User\OrderController::class);

    out("Created test fixtures: Buyer A, Buyer B, 2 Sellers, 2 Products (Initial Stocks: prod1=10, prod2=15).");

    // =========================================================================
    // CHALLENGE SCENARIO 1: Order Cancellation & Stock Restoration
    // =========================================================================
    out("\n--- CHALLENGE SCENARIO 1: Order Cancellation & Stock Restoration ---");

    // Sub-case 1.1: Order Placement & Cancellation
    out("1.1 Placing order for 5 units of Gobindobhog Rice (stock decreases 10 -> 5)...");
    $order1 = Order::create([
        'order_number'            => 'BZ-TEST-' . strtoupper(Str::random(6)),
        'user_id'                 => $buyerA->id,
        'order_type'              => 'cart',
        'subtotal'                => 5 * 450.00,
        'shipping_amount'         => 99.00,
        'total_amount'            => (5 * 450.00) + 99.00,
        'payment_method'          => 'cod',
        'payment_status'          => 'pending',
        'order_status'            => 'pending',
        'delivery_full_name'      => $buyerA->name,
        'delivery_phone'          => '9876543210',
        'delivery_address_line_1' => '10 Park St',
        'delivery_city'           => 'Kolkata',
        'delivery_state'          => 'West Bengal',
        'delivery_country'        => 'India',
        'delivery_postal_code'    => '700016',
        'placed_at'               => now(),
    ]);

    $so1 = SellerOrder::create([
        'order_id'            => $order1->id,
        'seller_id'           => $seller1->id,
        'seller_order_number' => 'SO-' . $order1->id . '-1',
        'subtotal'            => 5 * 450.00,
        'shipping_amount'     => 99.00,
        'commission_rate'     => 10.00,
        'commission_amount'   => 225.00,
        'payout_amount'       => 2025.00,
        'status'              => 'placed',
        'tracking_number'     => 'BZ-TRK-TEST1',
    ]);

    OrderItem::create([
        'seller_order_id' => $so1->id,
        'product_id'      => $prod1->id,
        'product_name'    => $prod1->name,
        'unit_price'      => 450.00,
        'quantity'        => 5,
        'total_price'     => 2250.00,
    ]);
    $prod1->decrement('stock', 5);

    $payment1 = Payment::create([
        'order_id'       => $order1->id,
        'user_id'        => $buyerA->id,
        'amount'         => $order1->total_amount,
        'payment_method' => 'cod',
        'status'         => 'pending',
    ]);

    assertCondition($prod1->fresh()->stock === 5, "Product stock decremented from 10 to 5 upon order placement");

    // Authenticate as Buyer A and invoke cancel
    Auth::login($buyerA);
    $reqCancel = Request::create("/user/orders/{$order1->id}/cancel", 'POST');
    session()->start();
    $respCancel = $orderController->cancel($reqCancel, $order1);

    assertCondition($order1->fresh()->order_status === 'cancelled', "Order status updated to 'cancelled'");
    assertCondition($so1->fresh()->status === 'cancelled', "SellerOrder status updated to 'cancelled'");
    assertCondition($prod1->fresh()->stock === 10, "Product stock accurately restored back to 10");
    assertCondition($payment1->fresh()->status === 'failed', "Payment status updated to 'failed'");
    assertCondition(session('success') !== null, "Success flash message set in session upon cancellation");

    // Sub-case 1.2: Idempotent duplicate cancellation rejection
    out("1.2 Attempting duplicate cancellation on already cancelled order...");
    session()->flush();
    $respDuplicateCancel = $orderController->cancel($reqCancel, $order1->fresh());
    assertCondition($order1->fresh()->order_status === 'cancelled', "Order remains 'cancelled'");
    assertCondition($prod1->fresh()->stock === 10, "Product stock remains 10 (not double restored)");
    assertCondition(session('error') !== null, "Error flash message set rejecting duplicate cancellation");

    // Sub-case 1.3: Rejection on Delivered / Completed order
    out("1.3 Attempting to cancel a completed (delivered) order...");
    $orderDelivered = Order::create([
        'order_number'            => 'BZ-DELIV-' . strtoupper(Str::random(6)),
        'user_id'                 => $buyerA->id,
        'order_type'              => 'cart',
        'subtotal'                => 450.00,
        'shipping_amount'         => 99.00,
        'total_amount'            => 549.00,
        'payment_method'          => 'cod',
        'payment_status'          => 'paid',
        'order_status'            => 'completed',
        'delivery_full_name'      => $buyerA->name,
        'delivery_phone'          => '9876543210',
        'delivery_address_line_1' => '10 Park St',
        'delivery_city'           => 'Kolkata',
        'delivery_state'          => 'West Bengal',
        'delivery_country'        => 'India',
        'delivery_postal_code'    => '700016',
        'placed_at'               => now()->subDays(3),
    ]);
    session()->flush();
    $respDelivCancel = $orderController->cancel($reqCancel, $orderDelivered);
    assertCondition($orderDelivered->fresh()->order_status === 'completed', "Delivered order status remains 'completed'");
    assertCondition(session('error') !== null, "Completed/delivered order cancellation rejected with error toast");

    // Sub-case 1.4: Authorization violation (Buyer B cancelling Buyer A's order)
    out("1.4 Attempting cross-user cancellation (Buyer B cancelling Buyer A's order)...");
    Auth::login($buyerB);
    $unauthBlocked = false;
    try {
        $orderController->cancel($reqCancel, $order1);
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        if ($e->getStatusCode() === 403) {
            $unauthBlocked = true;
        }
    }
    assertCondition($unauthBlocked, "403 Forbidden thrown when Buyer B attempts to cancel Buyer A's order");

    // =========================================================================
    // CHALLENGE SCENARIO 2: 1-Click Reorder
    // =========================================================================
    out("\n--- CHALLENGE SCENARIO 2: 1-Click Reorder ---");

    Auth::login($buyerA);

    // Create past order with prod1 (qty 2) and prod2 (qty 3)
    $pastOrder = Order::create([
        'order_number'            => 'BZ-PAST-' . strtoupper(Str::random(6)),
        'user_id'                 => $buyerA->id,
        'order_type'              => 'cart',
        'subtotal'                => (2 * 450.00) + (3 * 180.00),
        'shipping_amount'         => 99.00,
        'total_amount'            => (2 * 450.00) + (3 * 180.00) + 99.00,
        'payment_method'          => 'cod',
        'payment_status'          => 'paid',
        'order_status'            => 'completed',
        'delivery_full_name'      => $buyerA->name,
        'delivery_phone'          => '9876543210',
        'delivery_address_line_1' => '10 Park St',
        'delivery_city'           => 'Kolkata',
        'delivery_state'          => 'West Bengal',
        'delivery_country'        => 'India',
        'delivery_postal_code'    => '700016',
        'placed_at'               => now()->subDays(7),
    ]);

    $pastSO1 = SellerOrder::create([
        'order_id'            => $pastOrder->id,
        'seller_id'           => $seller1->id,
        'seller_order_number' => 'SO-' . $pastOrder->id . '-1',
        'subtotal'            => 900.00,
        'shipping_amount'     => 49.50,
        'commission_rate'     => 10.00,
        'commission_amount'   => 90.00,
        'payout_amount'       => 810.00,
        'status'              => 'delivered',
        'tracking_number'     => 'BZ-TRK-PAST1',
    ]);
    OrderItem::create([
        'seller_order_id' => $pastSO1->id,
        'product_id'      => $prod1->id,
        'product_name'    => $prod1->name,
        'unit_price'      => 450.00,
        'quantity'        => 2,
        'total_price'     => 900.00,
    ]);

    $pastSO2 = SellerOrder::create([
        'order_id'            => $pastOrder->id,
        'seller_id'           => $seller2->id,
        'seller_order_number' => 'SO-' . $pastOrder->id . '-2',
        'subtotal'            => 540.00,
        'shipping_amount'     => 49.50,
        'commission_rate'     => 10.00,
        'commission_amount'   => 54.00,
        'payout_amount'       => 486.00,
        'status'              => 'delivered',
        'tracking_number'     => 'BZ-TRK-PAST2',
    ]);
    OrderItem::create([
        'seller_order_id' => $pastSO2->id,
        'product_id'      => $prod2->id,
        'product_name'    => $prod2->name,
        'unit_price'      => 180.00,
        'quantity'        => 3,
        'total_price'     => 540.00,
    ]);

    // Sub-case 2.1: Reorder completed order
    out("2.1 Invoking 1-Click Reorder on completed past order...");
    $cartA = Cart::firstOrCreate(['user_id' => $buyerA->id]);
    $cartA->items()->delete(); // Clear cart initially

    $reqReorder = Request::create("/user/orders/{$pastOrder->id}/reorder", 'POST');
    session()->flush();
    $respReorder = $orderController->reorder($reqReorder, $pastOrder);

    assertCondition($respReorder->isRedirect(route('cart.index')), "Reorder redirects to /cart");
    $cartItems = $cartA->fresh()->items;
    assertCondition($cartItems->count() === 2, "Cart populated with 2 items from past order");

    $item1 = $cartItems->where('product_id', $prod1->id)->first();
    assertCondition($item1 && $item1->quantity === 2 && (float)$item1->unit_price === 450.00, "Prod1 re-added with quantity 2 and price ₹450.00");

    $item2 = $cartItems->where('product_id', $prod2->id)->first();
    assertCondition($item2 && $item2->quantity === 3 && (float)$item2->unit_price === 180.00, "Prod2 re-added with quantity 3 and price ₹180.00");
    assertCondition(session('success') !== null, "Success flash notification displayed");

    // Sub-case 2.2: Reorder when item is Out of Stock (OOS)
    out("2.2 Reorder when items are out of stock...");
    $prodOOS = Product::create([
        'seller_id'   => $seller1->id,
        'category_id' => $category->id,
        'name'        => 'Rare Kesar Saffron 1g',
        'slug'        => 'saffron-' . uniqid(),
        'price'       => 800.00,
        'stock'       => 0, // OUT OF STOCK
        'unit_type'   => 'gram',
        'sku'         => 'SAF-' . Str::random(5),
        'status'      => 'active',
    ]);

    $orderOOS = Order::create([
        'order_number'            => 'BZ-OOS-' . strtoupper(Str::random(6)),
        'user_id'                 => $buyerA->id,
        'order_type'              => 'cart',
        'subtotal'                => 800.00,
        'shipping_amount'         => 99.00,
        'total_amount'            => 899.00,
        'payment_method'          => 'cod',
        'payment_status'          => 'paid',
        'order_status'            => 'completed',
        'delivery_full_name'      => $buyerA->name,
        'delivery_phone'          => '9876543210',
        'delivery_address_line_1' => '10 Park St',
        'delivery_city'           => 'Kolkata',
        'delivery_state'          => 'West Bengal',
        'delivery_country'        => 'India',
        'delivery_postal_code'    => '700016',
        'placed_at'               => now()->subDays(10),
    ]);
    $soOOS = SellerOrder::create([
        'order_id'            => $orderOOS->id,
        'seller_id'           => $seller1->id,
        'seller_order_number' => 'SO-' . $orderOOS->id . '-1',
        'subtotal'            => 800.00,
        'shipping_amount'     => 99.00,
        'commission_rate'     => 10.00,
        'commission_amount'   => 80.00,
        'payout_amount'       => 720.00,
        'status'              => 'delivered',
        'tracking_number'     => 'BZ-TRK-OOS',
    ]);
    OrderItem::create([
        'seller_order_id' => $soOOS->id,
        'product_id'      => $prodOOS->id,
        'product_name'    => $prodOOS->name,
        'unit_price'      => 800.00,
        'quantity'        => 2,
        'total_price'     => 1600.00,
    ]);

    $cartA->items()->delete();
    session()->flush();
    $respOOS = $orderController->reorder($reqReorder, $orderOOS);
    assertCondition($respOOS->isRedirect(route('cart.index')), "OOS reorder gracefully redirects to /cart");
    assertCondition($cartA->fresh()->items()->count() === 0, "Cart remains empty when item is out of stock");
    assertCondition(str_contains(session('error') ?? '', 'None of the items from this order are currently available in stock'), "Graceful error message displayed for OOS items");

    // Sub-case 2.3: Reorder quantity capping
    out("2.3 Reorder capping quantity at available stock...");
    $prodCapped = Product::create([
        'seller_id'   => $seller1->id,
        'category_id' => $category->id,
        'name'        => 'Darjeeling White Tea',
        'slug'        => 'tea-cap-' . uniqid(),
        'price'       => 500.00,
        'stock'       => 3, // ONLY 3 AVAILABLE
        'unit_type'   => 'box',
        'sku'         => 'TEA-' . Str::random(5),
        'status'      => 'active',
    ]);

    $orderCapped = Order::create([
        'order_number'            => 'BZ-CAP-' . strtoupper(Str::random(6)),
        'user_id'                 => $buyerA->id,
        'order_type'              => 'cart',
        'subtotal'                => 4000.00,
        'shipping_amount'         => 99.00,
        'total_amount'            => 4099.00,
        'payment_method'          => 'cod',
        'payment_status'          => 'paid',
        'order_status'            => 'completed',
        'delivery_full_name'      => $buyerA->name,
        'delivery_phone'          => '9876543210',
        'delivery_address_line_1' => '10 Park St',
        'delivery_city'           => 'Kolkata',
        'delivery_state'          => 'West Bengal',
        'delivery_country'        => 'India',
        'delivery_postal_code'    => '700016',
        'placed_at'               => now()->subDays(5),
    ]);
    $soCapped = SellerOrder::create([
        'order_id'            => $orderCapped->id,
        'seller_id'           => $seller1->id,
        'seller_order_number' => 'SO-' . $orderCapped->id . '-1',
        'subtotal'            => 4000.00,
        'shipping_amount'     => 99.00,
        'commission_rate'     => 10.00,
        'commission_amount'   => 400.00,
        'payout_amount'       => 3600.00,
        'status'              => 'delivered',
        'tracking_number'     => 'BZ-TRK-CAP',
    ]);
    OrderItem::create([
        'seller_order_id' => $soCapped->id,
        'product_id'      => $prodCapped->id,
        'product_name'    => $prodCapped->name,
        'unit_price'      => 500.00,
        'quantity'        => 8, // ORDER WAS FOR 8
        'total_price'     => 4000.00,
    ]);

    $cartA->items()->delete();
    $orderController->reorder($reqReorder, $orderCapped);
    $cappedItem = $cartA->fresh()->items()->where('product_id', $prodCapped->id)->first();
    assertCondition($cappedItem && $cappedItem->quantity === 3, "Reorder quantity strictly capped at available stock of 3 (ordered 8)");

    // Sub-case 2.4: Cross-user reorder authorization check
    out("2.4 Cross-user reorder authorization check...");
    Auth::login($buyerB);
    $unauthReorderBlocked = false;
    try {
        $orderController->reorder($reqReorder, $pastOrder);
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        if ($e->getStatusCode() === 403) {
            $unauthReorderBlocked = true;
        }
    }
    assertCondition($unauthReorderBlocked, "403 Forbidden thrown when Buyer B attempts to reorder Buyer A's order");

    // =========================================================================
    // CHALLENGE SCENARIO 3: Tracking & Order Status Telemetry
    // =========================================================================
    out("\n--- CHALLENGE SCENARIO 3: Tracking & Order Status Telemetry ---");

    Auth::login($buyerA);

    // Sub-case 3.1: Order Detail View rendering tracking telemetry
    out("3.1 Inspecting Order Detail show view for multi-seller telemetry...");
    $viewShow = $orderController->show($pastOrder);
    $renderedShow = $viewShow->render();

    assertCondition(str_contains($renderedShow, 'Sundar Agro Farms'), "View renders Seller 1 shop name: Sundar Agro Farms");
    assertCondition(str_contains($renderedShow, 'BZ-TRK-PAST1'), "View renders Seller 1 courier tracking number: BZ-TRK-PAST1");
    assertCondition(str_contains($renderedShow, 'Gupta Kirana Store'), "View renders Seller 2 shop name: Gupta Kirana Store");
    assertCondition(str_contains($renderedShow, 'BZ-TRK-PAST2'), "View renders Seller 2 courier tracking number: BZ-TRK-PAST2");
    assertCondition(str_contains($renderedShow, 'Per-Seller Shipment Tracking'), "View renders 'Per-Seller Shipment Tracking' header");

    // Sub-case 3.2: Lifecycle progression tracker
    out("3.2 Progression stepper rendering for completed vs cancelled...");
    assertCondition(str_contains($renderedShow, 'Delivered'), "Completed order shows 'Delivered' lifecycle step");

    $viewCancelled = $orderController->show($order1->fresh());
    $renderedCancelled = $viewCancelled->render();
    assertCondition(str_contains($renderedCancelled, 'Order Cancelled'), "Cancelled order shows 'Order Cancelled' banner");
    assertCondition(str_contains($renderedCancelled, 'all product inventory has been restored'), "Cancelled banner explains inventory restoration");

    // Sub-case 3.3: Order history status filtering
    out("3.3 Order history status filtering tabs...");
    $reqHistoryAll = Request::create('/user/orders', 'GET', ['status' => 'all']);
    $viewHistoryAll = $orderController->index($reqHistoryAll);
    $dataAll = $viewHistoryAll->getData();
    assertCondition($dataAll['statusCounts']['all'] >= 3, "History view tracks all user orders count");
    assertCondition($dataAll['statusCounts']['cancelled'] >= 1, "History view tracks cancelled count");
    assertCondition($dataAll['statusCounts']['completed'] >= 1, "History view tracks completed count");

    $reqHistoryCancelled = Request::create('/user/orders', 'GET', ['status' => 'cancelled']);
    $viewHistoryCancelled = $orderController->index($reqHistoryCancelled);
    $dataCancelled = $viewHistoryCancelled->getData();
    foreach ($dataCancelled['orders'] as $ord) {
        assertCondition($ord->order_status === 'cancelled', "Filtered order status is strictly 'cancelled'");
    }

    out("\n=======================================================");
    out("🎉 ALL 18 EMPIRICAL ADVERSARIAL CHALLENGES PASSED! 🎉");
    out("=======================================================");

} catch (\Throwable $e) {
    out("💥 EXCEPTION: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
    throw $e;
} finally {
    DB::rollBack();
    out("Database transaction cleanly rolled back (no test residue).");
    file_put_contents(__DIR__ . '/challenge_run.log', implode(PHP_EOL, $logLines) . PHP_EOL);
}
