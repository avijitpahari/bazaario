<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * EmpiricalOrderLifecycleChallengeTest
 *
 * Challenger M4-B Adversarial Empirical Stress Test Suite for Features 45 to 49:
 * - Feature 45: View Order History (listing & status filtering)
 * - Feature 46: Track Order Per-Seller (courier tracking numbers & telemetry)
 * - Feature 47: View Order Status (lifecycle stages: pending -> processing -> shipped -> delivered -> cancelled)
 * - Feature 48: Cancel Order & Stock Restoration (atomic rollback, idempotency, terminal guards, RBAC)
 * - Feature 49: 1-Click Reorder (re-populating cart, OOS handling, capping, cart merge, RBAC)
 */
class EmpiricalOrderLifecycleChallengeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        try {
            Cache::store('file')->flush();
        } catch (\Throwable $e) {}
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    protected function createCustomer(array $attributes = []): User
    {
        return User::create(array_merge([
            'name'               => 'Challenger Buyer ' . uniqid(),
            'email'              => 'buyer_' . uniqid() . '@bazaario.com',
            'password'           => Hash::make('Password123!'),
            'role'               => 'user',
            'status'             => 'active',
            'preferred_language' => 'en',
            'email_verified_at'  => now(),
        ], $attributes));
    }

    protected function createSeller(array $userAttributes = [], array $profileAttributes = []): User
    {
        $seller = User::create(array_merge([
            'name'               => 'Merchant ' . uniqid(),
            'email'              => 'seller_' . uniqid() . '@bazaario.com',
            'password'           => Hash::make('Password123!'),
            'role'               => 'seller',
            'status'             => 'active',
            'preferred_language' => 'en',
            'email_verified_at'  => now(),
        ], $userAttributes));

        SellerProfile::create(array_merge([
            'user_id'         => $seller->id,
            'shop_name'       => $seller->name . ' Store',
            'shop_slug'       => Str::slug('shop-' . $seller->name . '-' . $seller->id),
            'seller_type'     => 'Kirana Store',
            'status'          => 'approved',
            'commission_rate' => 10.00,
            'trust_score'     => 95.00,
            'city'            => 'Kolkata',
            'state'           => 'West Bengal',
            'country'         => 'India',
        ], $profileAttributes));

        return $seller;
    }

    protected function createProduct(array $attributes = []): Product
    {
        $category = Category::firstOrCreate(
            ['slug' => 'challenge-cat'],
            ['name' => 'Challenge Category', 'status' => 'active']
        );

        $sellerId = $attributes['seller_id'] ?? $this->createSeller()->id;

        return Product::create(array_merge([
            'seller_id'   => $sellerId,
            'category_id' => $category->id,
            'name'        => 'Test Product ' . uniqid(),
            'slug'        => 'test-product-' . uniqid(),
            'price'       => 100.00,
            'stock'       => 10,
            'unit_type'   => 'piece',
            'sku'         => 'SKU-' . strtoupper(Str::random(6)),
            'status'      => 'active',
        ], $attributes));
    }

    protected function createOrderWithSeller(User $customer, User $seller, array $productsWithQty, string $orderStatus = 'pending'): Order
    {
        $subtotal = 0;
        foreach ($productsWithQty as $entry) {
            $subtotal += $entry['product']->price * $entry['qty'];
        }

        $order = Order::create([
            'order_number'            => 'BZ-' . date('Y') . '-' . strtoupper(Str::random(8)),
            'user_id'                 => $customer->id,
            'order_type'              => 'cart',
            'subtotal'                => $subtotal,
            'discount_amount'         => 0.00,
            'shipping_amount'         => 99.00,
            'total_amount'            => $subtotal + 99.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => $orderStatus,
            'delivery_full_name'      => $customer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => '123 Challenger St',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'West Bengal',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now(),
        ]);

        $sellerStatus = match($orderStatus) {
            'pending'    => 'placed',
            'processing' => 'processing',
            'completed'  => 'delivered',
            'cancelled'  => 'cancelled',
            'refunded'   => 'returned',
            default      => 'placed',
        };

        $sellerOrder = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => 'SO-' . $order->id . '-1-' . strtoupper(Str::random(4)),
            'subtotal'            => $subtotal,
            'shipping_amount'     => 99.00,
            'commission_rate'     => 10.00,
            'commission_amount'   => round($subtotal * 0.10, 2),
            'payout_amount'       => round($subtotal * 0.90, 2),
            'status'              => $sellerStatus,
            'tracking_number'     => 'BZ-TRK-' . strtoupper(Str::random(8)),
        ]);

        foreach ($productsWithQty as $entry) {
            $prod = $entry['product'];
            $qty  = $entry['qty'];
            OrderItem::create([
                'seller_order_id' => $sellerOrder->id,
                'product_id'      => $prod->id,
                'product_name'    => $prod->name,
                'sku'             => $prod->sku,
                'unit_price'      => $prod->price,
                'quantity'        => $qty,
                'total_price'     => $prod->price * $qty,
            ]);
            // Simulate decrement during placement
            $prod->decrement('stock', $qty);
        }

        Payment::create([
            'order_id'       => $order->id,
            'user_id'        => $customer->id,
            'amount'         => $order->total_amount,
            'payment_method' => 'cod',
            'status'         => 'pending',
        ]);

        return $order;
    }

    /*
    |--------------------------------------------------------------------------
    | Scenario 1: Order Cancellation & Stock Restoration (Feature 48)
    |--------------------------------------------------------------------------
    */

    public function test_scenario_1_standard_cancellation_and_exact_stock_restoration()
    {
        $customer = $this->createCustomer();
        $seller   = $this->createSeller();
        $product  = $this->createProduct(['seller_id' => $seller->id, 'stock' => 10, 'price' => 150.00]);

        // Place order for 5 units (stock decrements 10 -> 5)
        $order = $this->createOrderWithSeller($customer, $seller, [
            ['product' => $product, 'qty' => 5],
        ], 'pending');

        $this->assertEquals(5, $product->fresh()->stock, "Product stock decremented from 10 to 5 upon order placement");

        // Cancel the order via HTTP endpoint
        $response = $this->actingAs($customer, 'user')
            ->from("/user/orders/{$order->id}")
            ->post("/user/orders/{$order->id}/cancel");

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Verify parent order updated to 'cancelled'
        $this->assertEquals('cancelled', $order->fresh()->order_status);

        // Verify all seller sub-orders updated to 'cancelled'
        $sellerOrders = $order->fresh()->sellerOrders;
        $this->assertNotEmpty($sellerOrders);
        foreach ($sellerOrders as $so) {
            $this->assertEquals('cancelled', $so->status);
        }

        // Verify stock accurately restored back to 10
        $this->assertEquals(10, $product->fresh()->stock, "Product stock accurately restored back to 10");

        // Verify payment record updated to 'failed'
        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertEquals('failed', $payment->status);
    }

    public function test_scenario_1_multi_seller_multi_item_cancellation_restores_all_inventory()
    {
        $customer = $this->createCustomer();
        $seller1  = $this->createSeller();
        $seller2  = $this->createSeller();

        $prod1A = $this->createProduct(['seller_id' => $seller1->id, 'stock' => 20, 'price' => 100.00]);
        $prod1B = $this->createProduct(['seller_id' => $seller1->id, 'stock' => 15, 'price' => 200.00]);
        $prod2A = $this->createProduct(['seller_id' => $seller2->id, 'stock' => 8,  'price' => 350.00]);

        // Create parent order
        $order = Order::create([
            'order_number'            => 'BZ-MULTI-' . strtoupper(Str::random(6)),
            'user_id'                 => $customer->id,
            'order_type'              => 'cart',
            'subtotal'                => (4 * 100) + (5 * 200) + (3 * 350), // 400 + 1000 + 1050 = 2450
            'discount_amount'         => 0.00,
            'shipping_amount'         => 99.00,
            'total_amount'            => 2549.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'pending',
            'delivery_full_name'      => $customer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Market Square',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now(),
        ]);

        // Seller 1 SubOrder: 4 units of 1A (stock -> 16), 5 units of 1B (stock -> 10)
        $so1 = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller1->id,
            'seller_order_number' => 'SO-' . $order->id . '-1',
            'subtotal'            => 1400.00,
            'shipping_amount'     => 49.50,
            'commission_rate'     => 10.00,
            'commission_amount'   => 140.00,
            'payout_amount'       => 1260.00,
            'status'              => 'placed',
            'tracking_number'     => 'BZ-TRK-SO1',
        ]);
        OrderItem::create([
            'seller_order_id' => $so1->id,
            'product_id'      => $prod1A->id,
            'product_name'    => $prod1A->name,
            'unit_price'      => 100.00,
            'quantity'        => 4,
            'total_price'     => 400.00,
        ]);
        $prod1A->decrement('stock', 4);

        OrderItem::create([
            'seller_order_id' => $so1->id,
            'product_id'      => $prod1B->id,
            'product_name'    => $prod1B->name,
            'unit_price'      => 200.00,
            'quantity'        => 5,
            'total_price'     => 1000.00,
        ]);
        $prod1B->decrement('stock', 5);

        // Seller 2 SubOrder: 3 units of 2A (stock -> 5)
        $so2 = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller2->id,
            'seller_order_number' => 'SO-' . $order->id . '-2',
            'subtotal'            => 1050.00,
            'shipping_amount'     => 49.50,
            'commission_rate'     => 10.00,
            'commission_amount'   => 105.00,
            'payout_amount'       => 945.00,
            'status'              => 'placed',
            'tracking_number'     => 'BZ-TRK-SO2',
        ]);
        OrderItem::create([
            'seller_order_id' => $so2->id,
            'product_id'      => $prod2A->id,
            'product_name'    => $prod2A->name,
            'unit_price'      => 350.00,
            'quantity'        => 3,
            'total_price'     => 1050.00,
        ]);
        $prod2A->decrement('stock', 3);

        $this->assertEquals(16, $prod1A->fresh()->stock);
        $this->assertEquals(10, $prod1B->fresh()->stock);
        $this->assertEquals(5,  $prod2A->fresh()->stock);

        // Trigger cancellation
        $response = $this->actingAs($customer, 'user')
            ->from("/user/orders/{$order->id}")
            ->post("/user/orders/{$order->id}/cancel");

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Verify all stocks restored across sellers
        $this->assertEquals(20, $prod1A->fresh()->stock, "Product 1A restored to initial 20");
        $this->assertEquals(15, $prod1B->fresh()->stock, "Product 1B restored to initial 15");
        $this->assertEquals(8,  $prod2A->fresh()->stock, "Product 2A restored to initial 8");

        // Verify suborders cancelled
        $this->assertEquals('cancelled', $so1->fresh()->status);
        $this->assertEquals('cancelled', $so2->fresh()->status);
        $this->assertEquals('cancelled', $order->fresh()->order_status);
    }

    public function test_scenario_1_cancellation_allowed_in_processing_status()
    {
        $customer = $this->createCustomer();
        $seller   = $this->createSeller();
        $product  = $this->createProduct(['seller_id' => $seller->id, 'stock' => 10]);

        $order = $this->createOrderWithSeller($customer, $seller, [
            ['product' => $product, 'qty' => 3],
        ], 'processing');

        $this->assertEquals(7, $product->fresh()->stock);

        $response = $this->actingAs($customer, 'user')
            ->from("/user/orders/{$order->id}")
            ->post("/user/orders/{$order->id}/cancel");

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertEquals('cancelled', $order->fresh()->order_status);
        $this->assertEquals(10, $product->fresh()->stock);
    }

    public function test_scenario_1_cancellation_rejected_for_completed_delivered_order()
    {
        $customer = $this->createCustomer();
        $seller   = $this->createSeller();
        $product  = $this->createProduct(['seller_id' => $seller->id, 'stock' => 10]);

        // 'completed' order represents Delivered shipment
        $order = $this->createOrderWithSeller($customer, $seller, [
            ['product' => $product, 'qty' => 2],
        ], 'completed');

        $this->assertEquals(8, $product->fresh()->stock);

        $response = $this->actingAs($customer, 'user')
            ->from("/user/orders/{$order->id}")
            ->post("/user/orders/{$order->id}/cancel");

        $response->assertStatus(302);
        $response->assertSessionHas('error');

        $this->assertEquals('completed', $order->fresh()->order_status);
        $this->assertEquals(8, $product->fresh()->stock, "Stock is not restored for delivered order");
    }

    public function test_scenario_1_cancellation_rejected_for_refunded_order()
    {
        $customer = $this->createCustomer();
        $seller   = $this->createSeller();
        $product  = $this->createProduct(['seller_id' => $seller->id, 'stock' => 10]);

        $order = $this->createOrderWithSeller($customer, $seller, [
            ['product' => $product, 'qty' => 2],
        ], 'refunded');

        $this->assertEquals(8, $product->fresh()->stock);

        $response = $this->actingAs($customer, 'user')
            ->from("/user/orders/{$order->id}")
            ->post("/user/orders/{$order->id}/cancel");

        $response->assertStatus(302);
        $response->assertSessionHas('error');

        $this->assertEquals('refunded', $order->fresh()->order_status);
        $this->assertEquals(8, $product->fresh()->stock);
    }

    public function test_scenario_1_cancellation_idempotency_already_cancelled_order_rejected()
    {
        $customer = $this->createCustomer();
        $seller   = $this->createSeller();
        $product  = $this->createProduct(['seller_id' => $seller->id, 'stock' => 10]);

        $order = $this->createOrderWithSeller($customer, $seller, [
            ['product' => $product, 'qty' => 5],
        ], 'pending');

        // 1st cancel succeeds
        $this->actingAs($customer, 'user')->post("/user/orders/{$order->id}/cancel");
        $this->assertEquals(10, $product->fresh()->stock);
        $this->assertEquals('cancelled', $order->fresh()->order_status);

        // 2nd cancel attempt must be rejected and must NOT double restore stock to 15!
        $response = $this->actingAs($customer, 'user')
            ->from("/user/orders/{$order->id}")
            ->post("/user/orders/{$order->id}/cancel");

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertEquals(10, $product->fresh()->stock, "Stock was not double-incremented on duplicate cancellation");
    }

    public function test_scenario_1_unauthorized_user_cannot_cancel_another_users_order()
    {
        $customerA = $this->createCustomer();
        $customerB = $this->createCustomer();
        $seller    = $this->createSeller();
        $product   = $this->createProduct(['seller_id' => $seller->id, 'stock' => 10]);

        $orderA = $this->createOrderWithSeller($customerA, $seller, [
            ['product' => $product, 'qty' => 3],
        ], 'pending');

        $this->assertEquals(7, $product->fresh()->stock);

        // Customer B tries to cancel Customer A's order
        $response = $this->actingAs($customerB, 'user')->post("/user/orders/{$orderA->id}/cancel");

        $response->assertStatus(403);
        $this->assertEquals('pending', $orderA->fresh()->order_status);
        $this->assertEquals(7, $product->fresh()->stock);
    }

    public function test_scenario_1_guest_cannot_cancel_order()
    {
        $customer = $this->createCustomer();
        $seller   = $this->createSeller();
        $product  = $this->createProduct(['seller_id' => $seller->id]);

        $order = $this->createOrderWithSeller($customer, $seller, [
            ['product' => $product, 'qty' => 1],
        ], 'pending');

        $response = $this->post("/user/orders/{$order->id}/cancel");
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /*
    |--------------------------------------------------------------------------
    | Scenario 2: 1-Click Reorder (Feature 49)
    |--------------------------------------------------------------------------
    */

    public function test_scenario_2_reorder_completed_order_adds_items_to_cart_and_redirects()
    {
        $customer = $this->createCustomer();
        $seller   = $this->createSeller();
        $product1 = $this->createProduct(['seller_id' => $seller->id, 'stock' => 20, 'price' => 120.00]);
        $product2 = $this->createProduct(['seller_id' => $seller->id, 'stock' => 15, 'price' => 250.00]);

        $order = $this->createOrderWithSeller($customer, $seller, [
            ['product' => $product1, 'qty' => 2],
            ['product' => $product2, 'qty' => 3],
        ], 'completed');

        // Customer's cart is initially empty
        $cart = Cart::firstOrCreate(['user_id' => $customer->id]);
        $this->assertEquals(0, $cart->items()->count());

        $response = $this->actingAs($customer, 'user')->post("/user/orders/{$order->id}/reorder");

        $response->assertStatus(302);
        $response->assertRedirect('/cart');
        $response->assertSessionHas('success');

        // Verify cart now populated
        $cartItems = $cart->fresh()->items;
        $this->assertEquals(2, $cartItems->count());

        $item1 = $cartItems->where('product_id', $product1->id)->first();
        $this->assertNotNull($item1);
        $this->assertEquals(2, $item1->quantity);
        $this->assertEquals(120.00, (float)$item1->unit_price);

        $item2 = $cartItems->where('product_id', $product2->id)->first();
        $this->assertNotNull($item2);
        $this->assertEquals(3, $item2->quantity);
        $this->assertEquals(250.00, (float)$item2->unit_price);
    }

    public function test_scenario_2_reorder_graceful_handling_when_item_out_of_stock()
    {
        $customer = $this->createCustomer();
        $seller   = $this->createSeller();
        $productOOS = $this->createProduct(['seller_id' => $seller->id, 'stock' => 0]);

        $order = $this->createOrderWithSeller($customer, $seller, [
            ['product' => $productOOS, 'qty' => 2],
        ], 'completed');

        $cart = Cart::firstOrCreate(['user_id' => $customer->id]);
        $this->assertEquals(0, $cart->items()->count());

        $response = $this->actingAs($customer, 'user')->post("/user/orders/{$order->id}/reorder");

        $response->assertStatus(302);
        $response->assertRedirect('/cart');
        $response->assertSessionHas('error');

        $errorMsg = session('error');
        $this->assertStringContainsString('None of the items from this order are currently available in stock', $errorMsg);
        $this->assertEquals(0, $cart->fresh()->items()->count(), "Cart remains empty when items are out of stock");
    }

    public function test_scenario_2_reorder_caps_quantity_when_stock_is_insufficient()
    {
        $customer = $this->createCustomer();
        $seller   = $this->createSeller();
        $product  = $this->createProduct(['seller_id' => $seller->id, 'stock' => 15]);

        // Original order was for 8 units
        $order = $this->createOrderWithSeller($customer, $seller, [
            ['product' => $product, 'qty' => 8],
        ], 'completed');

        // Current product stock reduced down to 3
        $product->update(['stock' => 3]);

        $cart = Cart::firstOrCreate(['user_id' => $customer->id]);

        $response = $this->actingAs($customer, 'user')->post("/user/orders/{$order->id}/reorder");

        $response->assertStatus(302);
        $response->assertRedirect('/cart');
        $response->assertSessionHas('success');

        // Quantity in cart must be capped at current available stock: 3
        $cartItem = $cart->fresh()->items()->where('product_id', $product->id)->first();
        $this->assertNotNull($cartItem);
        $this->assertEquals(3, $cartItem->quantity, "Cart quantity capped at available stock of 3");
    }

    public function test_scenario_2_reorder_merges_with_existing_cart_items_up_to_stock_limit()
    {
        $customer = $this->createCustomer();
        $seller   = $this->createSeller();
        // Product with 15 initial stock
        $product  = $this->createProduct(['seller_id' => $seller->id, 'stock' => 15]);

        // Place order for 3 units (remaining stock becomes 12)
        $order = $this->createOrderWithSeller($customer, $seller, [
            ['product' => $product, 'qty' => 3],
        ], 'completed');

        // Customer already has 2 in cart (stock remaining is 12)
        $cart = Cart::firstOrCreate(['user_id' => $customer->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity'   => 2,
            'unit_price' => $product->price,
        ]);

        $response = $this->actingAs($customer, 'user')->post("/user/orders/{$order->id}/reorder");

        $response->assertStatus(302);
        $response->assertRedirect('/cart');

        // 2 (existing) + 3 (from order) = 5 (within available stock 12)
        $cartItem = $cart->fresh()->items()->where('product_id', $product->id)->first();
        $this->assertEquals(5, $cartItem->quantity);

        // Now lower stock to 6, reorder again (5 + 3 = 8, capped at stock 6)
        $product->update(['stock' => 6]);
        $this->actingAs($customer, 'user')->post("/user/orders/{$order->id}/reorder");
        $cartItem = $cart->fresh()->items()->where('product_id', $product->id)->first();
        $this->assertEquals(6, $cartItem->quantity, "Cart item merged and capped at max stock 6");
    }

    public function test_scenario_2_unauthorized_user_cannot_reorder_another_users_order()
    {
        $customerA = $this->createCustomer();
        $customerB = $this->createCustomer();
        $seller    = $this->createSeller();
        $product   = $this->createProduct(['seller_id' => $seller->id]);

        $orderA = $this->createOrderWithSeller($customerA, $seller, [
            ['product' => $product, 'qty' => 2],
        ], 'completed');

        // Customer B tries to reorder Customer A's order
        $response = $this->actingAs($customerB, 'user')->post("/user/orders/{$orderA->id}/reorder");

        $response->assertStatus(403);
    }

    public function test_scenario_2_guest_cannot_reorder()
    {
        $customer = $this->createCustomer();
        $seller   = $this->createSeller();
        $product  = $this->createProduct(['seller_id' => $seller->id]);

        $order = $this->createOrderWithSeller($customer, $seller, [
            ['product' => $product, 'qty' => 1],
        ], 'completed');

        $response = $this->post("/user/orders/{$order->id}/reorder");
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /*
    |--------------------------------------------------------------------------
    | Scenario 3: Tracking & Order Status Telemetry (Features 45, 46, 47)
    |--------------------------------------------------------------------------
    */

    public function test_scenario_3_order_show_displays_per_seller_tracking_telemetry()
    {
        $customer = $this->createCustomer();
        $seller1  = $this->createSeller([], ['shop_name' => 'Himalayan Organic Honey']);
        $seller2  = $this->createSeller([], ['shop_name' => 'Bishnupur Silk Crafts']);

        $prod1 = $this->createProduct(['seller_id' => $seller1->id]);
        $prod2 = $this->createProduct(['seller_id' => $seller2->id]);

        $order = Order::create([
            'order_number'            => 'BZ-TELEMETRY-999',
            'user_id'                 => $customer->id,
            'order_type'              => 'cart',
            'subtotal'                => 800.00,
            'discount_amount'         => 0.00,
            'shipping_amount'         => 99.00,
            'total_amount'            => 899.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'processing',
            'delivery_full_name'      => $customer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Station Road',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now(),
        ]);

        $so1 = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller1->id,
            'seller_order_number' => 'SO-' . $order->id . '-1',
            'subtotal'            => 400.00,
            'shipping_amount'     => 49.50,
            'commission_rate'     => 10.00,
            'commission_amount'   => 40.00,
            'payout_amount'       => 360.00,
            'status'              => 'shipped',
            'tracking_number'     => 'BLUEDART-HIM-8812',
            'shipped_at'          => now()->subHours(4),
        ]);
        OrderItem::create([
            'seller_order_id' => $so1->id,
            'product_id'      => $prod1->id,
            'product_name'    => 'Raw Forest Honey 500g',
            'unit_price'      => 400.00,
            'quantity'        => 1,
            'total_price'     => 400.00,
        ]);

        $so2 = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller2->id,
            'seller_order_number' => 'SO-' . $order->id . '-2',
            'subtotal'            => 400.00,
            'shipping_amount'     => 49.50,
            'commission_rate'     => 10.00,
            'commission_amount'   => 40.00,
            'payout_amount'       => 360.00,
            'status'              => 'processing',
            'tracking_number'     => 'DELHIVERY-SLK-4491',
        ]);
        OrderItem::create([
            'seller_order_id' => $so2->id,
            'product_id'      => $prod2->id,
            'product_name'    => 'Baluchari Silk Scarf',
            'unit_price'      => 400.00,
            'quantity'        => 1,
            'total_price'     => 400.00,
        ]);

        $response = $this->actingAs($customer, 'user')->get("/user/orders/{$order->id}");

        $response->assertStatus(200);
        $response->assertSee('BZ-TELEMETRY-999');
        $response->assertSee('Himalayan Organic Honey');
        $response->assertSee('BLUEDART-HIM-8812');
        $response->assertSee('Bishnupur Silk Crafts');
        $response->assertSee('DELHIVERY-SLK-4491');
        $response->assertSee('Per-Seller Shipment Tracking');
    }

    public function test_scenario_3_order_lifecycle_progression_steps_rendered()
    {
        $customer = $this->createCustomer();
        $seller   = $this->createSeller();
        $product  = $this->createProduct(['seller_id' => $seller->id]);

        // 1. Pending
        $orderPending = $this->createOrderWithSeller($customer, $seller, [
            ['product' => $product, 'qty' => 1],
        ], 'pending');

        $resPending = $this->actingAs($customer, 'user')->get("/user/orders/{$orderPending->id}");
        $resPending->assertStatus(200);
        $resPending->assertSee('Order Status Tracking');
        $resPending->assertSee('Order Placed');

        // 2. Completed / Delivered
        $orderCompleted = $this->createOrderWithSeller($customer, $seller, [
            ['product' => $product, 'qty' => 1],
        ], 'completed');

        $resCompleted = $this->actingAs($customer, 'user')->get("/user/orders/{$orderCompleted->id}");
        $resCompleted->assertStatus(200);
        $resCompleted->assertSee('Delivered');

        // 3. Cancelled
        $orderCancelled = $this->createOrderWithSeller($customer, $seller, [
            ['product' => $product, 'qty' => 1],
        ], 'cancelled');

        $resCancelled = $this->actingAs($customer, 'user')->get("/user/orders/{$orderCancelled->id}");
        $resCancelled->assertStatus(200);
        $resCancelled->assertSee('Order Cancelled');
        $resCancelled->assertSee('all product inventory has been restored');
    }

    public function test_scenario_3_order_history_filtering_by_status()
    {
        $customer = $this->createCustomer();
        $seller   = $this->createSeller();
        $product  = $this->createProduct(['seller_id' => $seller->id]);

        $orderPending = $this->createOrderWithSeller($customer, $seller, [['product' => $product, 'qty' => 1]], 'pending');
        $orderPending->update(['order_number' => 'BZ-HIST-PENDING']);

        $orderCompleted = $this->createOrderWithSeller($customer, $seller, [['product' => $product, 'qty' => 1]], 'completed');
        $orderCompleted->update(['order_number' => 'BZ-HIST-COMPLETED']);

        $orderCancelled = $this->createOrderWithSeller($customer, $seller, [['product' => $product, 'qty' => 1]], 'cancelled');
        $orderCancelled->update(['order_number' => 'BZ-HIST-CANCELLED']);

        // All tab
        $resAll = $this->actingAs($customer, 'user')->get('/user/orders');
        $resAll->assertStatus(200);
        $resAll->assertSee('BZ-HIST-PENDING');
        $resAll->assertSee('BZ-HIST-COMPLETED');
        $resAll->assertSee('BZ-HIST-CANCELLED');

        // Filter: pending
        $resPending = $this->actingAs($customer, 'user')->get('/user/orders?status=pending');
        $resPending->assertStatus(200);
        $resPending->assertSee('BZ-HIST-PENDING');
        $resPending->assertDontSee('BZ-HIST-COMPLETED');
        $resPending->assertDontSee('BZ-HIST-CANCELLED');

        // Filter: completed
        $resCompleted = $this->actingAs($customer, 'user')->get('/user/orders?status=completed');
        $resCompleted->assertStatus(200);
        $resCompleted->assertSee('BZ-HIST-COMPLETED');
        $resCompleted->assertDontSee('BZ-HIST-PENDING');
        $resCompleted->assertDontSee('BZ-HIST-CANCELLED');

        // Filter: cancelled
        $resCancelled = $this->actingAs($customer, 'user')->get('/user/orders?status=cancelled');
        $resCancelled->assertStatus(200);
        $resCancelled->assertSee('BZ-HIST-CANCELLED');
        $resCancelled->assertDontSee('BZ-HIST-PENDING');
        $resCancelled->assertDontSee('BZ-HIST-COMPLETED');
    }

    public function test_scenario_3_customer_cannot_view_another_customers_order_detail()
    {
        $customerA = $this->createCustomer();
        $customerB = $this->createCustomer();
        $seller    = $this->createSeller();
        $product   = $this->createProduct(['seller_id' => $seller->id]);

        $orderA = $this->createOrderWithSeller($customerA, $seller, [
            ['product' => $product, 'qty' => 1],
        ], 'pending');

        $response = $this->actingAs($customerB, 'user')->get("/user/orders/{$orderA->id}");
        $response->assertStatus(403);
    }
}
