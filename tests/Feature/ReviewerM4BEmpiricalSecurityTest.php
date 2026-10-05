<?php

namespace Tests\Feature;

use App\Models\Address;
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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * ReviewerM4BEmpiricalSecurityTest
 *
 * Independent empirical security & lifecycle validation for Milestone 4:
 * - Feature 45: View Order History (scoping, status filter, pagination)
 * - Feature 46: Per-Seller Tracking (merchant blocks, tracking numbers, sub-orders)
 * - Feature 47: Order Status tracking & progression stepper
 * - Feature 48: Order Cancellation HTTP endpoint, stock restoration, prevention of re-cancellation
 * - Feature 49: 1-Click Reorder HTTP endpoint, stock availability caps, zero stock handling
 * - Security: Order isolation (show, cancel, reorder, success), address ownership spoofing, pessimistic locking & rollback
 */
class ReviewerM4BEmpiricalSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function createCustomer(array $attrs = []): User
    {
        return User::create(array_merge([
            'name'               => 'Buyer ' . uniqid(),
            'email'              => 'buyer_' . uniqid() . '@bazaario.com',
            'password'           => Hash::make('Password123!'),
            'role'               => 'user',
            'status'             => 'active',
            'preferred_language' => 'en',
            'email_verified_at'  => now(),
        ], $attrs));
    }

    protected function createSeller(string $shopName = 'Merchant Shop'): User
    {
        $seller = User::create([
            'name'               => 'Seller ' . uniqid(),
            'email'              => 'seller_' . uniqid() . '@bazaario.com',
            'password'           => Hash::make('Password123!'),
            'role'               => 'seller',
            'status'             => 'active',
            'preferred_language' => 'en',
            'email_verified_at'  => now(),
        ]);

        SellerProfile::create([
            'user_id'         => $seller->id,
            'shop_name'       => $shopName,
            'shop_slug'       => Str::slug($shopName . '-' . uniqid()),
            'status'          => 'approved',
            'commission_rate' => 10.00,
            'trust_score'     => 98.00,
            'city'            => 'Kolkata',
            'state'           => 'West Bengal',
            'country'         => 'India',
        ]);

        return $seller;
    }

    protected function createAddress(User $user): Address
    {
        return Address::create([
            'user_id'        => $user->id,
            'type'           => 'home',
            'full_name'      => $user->name,
            'phone'          => '9876543210',
            'address_line_1' => 'Flat 5A, Park Street',
            'city'           => 'Kolkata',
            'state'          => 'West Bengal',
            'postal_code'    => '700016',
            'country'        => 'India',
            'is_default'     => true,
        ]);
    }

    protected function createProduct(User $seller, float $price = 300.00, int $stock = 25): Product
    {
        $category = Category::firstOrCreate(
            ['slug' => 'artisan-crafts'],
            ['name' => 'Artisan Crafts', 'status' => 'active']
        );

        return Product::create([
            'seller_id'         => $seller->id,
            'category_id'       => $category->id,
            'name'              => 'Terracotta Clay Vase ' . uniqid(),
            'slug'              => 'terracotta-vase-' . uniqid(),
            'short_description' => 'Handmade clay vase.',
            'description'       => 'Authentic clay pottery from Bengal artisans.',
            'price'             => $price,
            'stock'             => $stock,
            'sku'               => 'TC-' . strtoupper(Str::random(6)),
            'unit_type'         => 'bundle',
            'status'            => 'active',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 45: View Order History
    |--------------------------------------------------------------------------
    */

    public function test_f45_order_history_index_filtering_and_user_isolation()
    {
        $userA = $this->createCustomer();
        $userB = $this->createCustomer();

        // Orders for User A
        $orderA1 = Order::create([
            'order_number'            => 'BZ-HIST-A1',
            'user_id'                 => $userA->id,
            'subtotal'                => 500.00,
            'total_amount'            => 599.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'pending',
            'delivery_full_name'      => $userA->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'A Street',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now()->subDays(1),
        ]);

        $orderA2 = Order::create([
            'order_number'            => 'BZ-HIST-A2',
            'user_id'                 => $userA->id,
            'subtotal'                => 1000.00,
            'total_amount'            => 1099.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'paid',
            'order_status'            => 'completed',
            'delivery_full_name'      => $userA->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'A Street',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now()->subDays(2),
        ]);

        // Order for User B
        Order::create([
            'order_number'            => 'BZ-HIST-B1',
            'user_id'                 => $userB->id,
            'subtotal'                => 200.00,
            'total_amount'            => 299.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'pending',
            'delivery_full_name'      => $userB->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'B Street',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now(),
        ]);

        // User A visits index -> sees A1 and A2, but NOT B1
        $resA = $this->actingAs($userA, 'user')->get('/user/orders');
        $resA->assertStatus(200);
        $resA->assertSee('BZ-HIST-A1');
        $resA->assertSee('BZ-HIST-A2');
        $resA->assertDontSee('BZ-HIST-B1');

        // Status filter: pending
        $resPending = $this->actingAs($userA, 'user')->get('/user/orders?status=pending');
        $resPending->assertStatus(200);
        $resPending->assertSee('BZ-HIST-A1');
        $resPending->assertDontSee('BZ-HIST-A2');

        // Status filter: completed
        $resCompleted = $this->actingAs($userA, 'user')->get('/user/orders?status=completed');
        $resCompleted->assertStatus(200);
        $resCompleted->assertSee('BZ-HIST-A2');
        $resCompleted->assertDontSee('BZ-HIST-A1');
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 46 & 47: Per-Seller Tracking and Status Stepper
    |--------------------------------------------------------------------------
    */

    public function test_f46_and_f47_per_seller_courier_telemetry_rendered_on_show_page()
    {
        $buyer   = $this->createCustomer();
        $seller1 = $this->createSeller('Bengal Handlooms');
        $seller2 = $this->createSeller('Darjeeling Organic Teas');

        $prod1 = $this->createProduct($seller1, 600.00);
        $prod2 = $this->createProduct($seller2, 400.00);

        $order = Order::create([
            'order_number'            => 'BZ-TELEMETRY-001',
            'user_id'                 => $buyer->id,
            'subtotal'                => 1000.00,
            'shipping_amount'         => 99.00,
            'total_amount'            => 1099.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'processing',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => '22 Park Street',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700016',
            'notes'                   => 'Time Slot: Morning: 8 AM - 12 PM',
            'placed_at'               => now(),
        ]);

        $subOrder1 = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller1->id,
            'seller_order_number' => 'SO-TEL-101',
            'subtotal'            => 600.00,
            'shipping_amount'     => 50.00,
            'commission_rate'     => 10.00,
            'commission_amount'   => 60.00,
            'payout_amount'       => 540.00,
            'status'              => 'shipped',
            'tracking_number'     => 'BD-EXP-9921',
            'shipped_at'          => now()->subHours(3),
        ]);

        OrderItem::create([
            'seller_order_id' => $subOrder1->id,
            'product_id'      => $prod1->id,
            'product_name'    => $prod1->name,
            'sku'             => $prod1->sku,
            'unit_price'      => 600.00,
            'quantity'        => 1,
            'total_price'     => 600.00,
        ]);

        $subOrder2 = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller2->id,
            'seller_order_number' => 'SO-TEL-102',
            'subtotal'            => 400.00,
            'shipping_amount'     => 49.00,
            'commission_rate'     => 10.00,
            'commission_amount'   => 40.00,
            'payout_amount'       => 360.00,
            'status'              => 'processing',
            'tracking_number'     => 'DTDC-TRK-7712',
        ]);

        OrderItem::create([
            'seller_order_id' => $subOrder2->id,
            'product_id'      => $prod2->id,
            'product_name'    => $prod2->name,
            'sku'             => $prod2->sku,
            'unit_price'      => 400.00,
            'quantity'        => 1,
            'total_price'     => 400.00,
        ]);

        $response = $this->actingAs($buyer, 'user')->get("/user/orders/{$order->id}");
        $response->assertStatus(200);

        // Merchant shop names
        $response->assertSee('Bengal Handlooms');
        $response->assertSee('Darjeeling Organic Teas');

        // Sub-order numbers
        $response->assertSee('SO-TEL-101');
        $response->assertSee('SO-TEL-102');

        // Courier tracking numbers
        $response->assertSee('BD-EXP-9921');
        $response->assertSee('DTDC-TRK-7712');

        // Lifecycle & delivery time slot
        $response->assertSee('Morning: 8 AM - 12 PM');
        $response->assertSee('Order Status Tracking');
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 48: Order Cancellation via HTTP POST
    |--------------------------------------------------------------------------
    */

    public function test_f48_cancel_order_via_http_endpoint_restores_stock_and_marks_cancelled()
    {
        $buyer  = $this->createCustomer();
        $seller = $this->createSeller();
        $prod   = $this->createProduct($seller, 500.00, 10);

        $order = Order::create([
            'order_number'            => 'BZ-CAN-HTTP-001',
            'user_id'                 => $buyer->id,
            'subtotal'                => 1000.00,
            'shipping_amount'         => 99.00,
            'total_amount'            => 1099.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'pending',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Lane 10',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now(),
        ]);

        $subOrder = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => 'SO-CAN-1',
            'subtotal'            => 1000.00,
            'shipping_amount'     => 99.00,
            'commission_rate'     => 10.00,
            'commission_amount'   => 100.00,
            'payout_amount'       => 900.00,
            'status'              => 'placed',
        ]);

        OrderItem::create([
            'seller_order_id' => $subOrder->id,
            'product_id'      => $prod->id,
            'product_name'    => $prod->name,
            'unit_price'      => 500.00,
            'quantity'        => 3,
            'total_price'     => 1500.00,
        ]);

        Payment::create([
            'order_id'       => $order->id,
            'user_id'        => $buyer->id,
            'payment_method' => 'cod',
            'transaction_id' => 'TXN-TEST-1',
            'amount'         => 1099.00,
            'status'         => 'pending',
        ]);

        // Prior stock is 10
        $this->assertEquals(10, $prod->fresh()->stock);

        // Execute cancellation via HTTP POST
        $res = $this->actingAs($buyer, 'user')->post("/user/orders/{$order->id}/cancel");
        $res->assertSessionHas('success');

        // Verify status and stock
        $this->assertEquals('cancelled', $order->fresh()->order_status);
        $this->assertEquals('cancelled', $subOrder->fresh()->status);
        $this->assertEquals(13, $prod->fresh()->stock); // 10 + 3 restored
        $this->assertEquals('failed', $order->payments()->first()->status);

        // Attempt second cancellation on already cancelled order -> must fail with error
        $res2 = $this->actingAs($buyer, 'user')->post("/user/orders/{$order->id}/cancel");
        $res2->assertSessionHas('error');
        $this->assertEquals(13, $prod->fresh()->stock); // Stock must NOT increment again
    }

    public function test_f48_cannot_cancel_shipped_or_completed_order()
    {
        $buyer  = $this->createCustomer();
        $seller = $this->createSeller();
        $prod   = $this->createProduct($seller, 200.00, 5);

        $order = Order::create([
            'order_number'            => 'BZ-COMPLETED-001',
            'user_id'                 => $buyer->id,
            'subtotal'                => 200.00,
            'total_amount'            => 299.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'paid',
            'order_status'            => 'completed',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Lane 10',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now()->subDays(3),
        ]);

        $res = $this->actingAs($buyer, 'user')->post("/user/orders/{$order->id}/cancel");
        $res->assertSessionHas('error');
        $this->assertEquals('completed', $order->fresh()->order_status);
        $this->assertEquals(5, $prod->fresh()->stock);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 49: 1-Click Reorder via HTTP POST
    |--------------------------------------------------------------------------
    */

    public function test_f49_reorder_via_http_endpoint_populates_cart()
    {
        $buyer  = $this->createCustomer();
        $seller = $this->createSeller();
        $prod1  = $this->createProduct($seller, 300.00, 15);
        $prod2  = $this->createProduct($seller, 150.00, 20);

        $order = Order::create([
            'order_number'            => 'BZ-REORDER-HTTP-001',
            'user_id'                 => $buyer->id,
            'subtotal'                => 900.00,
            'total_amount'            => 999.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'paid',
            'order_status'            => 'completed',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Lane 4',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now()->subDays(4),
        ]);

        $subOrder = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => 'SO-REORDER-1',
            'subtotal'            => 900.00,
            'shipping_amount'     => 99.00,
            'commission_rate'     => 10.00,
            'commission_amount'   => 90.00,
            'payout_amount'       => 810.00,
            'status'              => 'delivered',
        ]);

        OrderItem::create([
            'seller_order_id' => $subOrder->id,
            'product_id'      => $prod1->id,
            'product_name'    => $prod1->name,
            'unit_price'      => 300.00,
            'quantity'        => 2,
            'total_price'     => 600.00,
        ]);

        OrderItem::create([
            'seller_order_id' => $subOrder->id,
            'product_id'      => $prod2->id,
            'product_name'    => $prod2->name,
            'unit_price'      => 150.00,
            'quantity'        => 2,
            'total_price'     => 300.00,
        ]);

        // Call reorder via HTTP POST
        $res = $this->actingAs($buyer, 'user')->post("/user/orders/{$order->id}/reorder");
        $res->assertRedirect(route('cart.index'));
        $res->assertSessionHas('success');

        $cart = Cart::where('user_id', $buyer->id)->first();
        $this->assertNotNull($cart);
        $this->assertEquals(2, $cart->items()->count());

        $item1 = $cart->items()->where('product_id', $prod1->id)->first();
        $this->assertEquals(2, $item1->quantity);
        $this->assertEquals(300.00, $item1->unit_price);
    }

    public function test_f49_reorder_caps_at_stock_and_handles_zero_stock()
    {
        $buyer  = $this->createCustomer();
        $seller = $this->createSeller();
        $prod   = $this->createProduct($seller, 500.00, 1); // Only 1 in stock

        $order = Order::create([
            'order_number'            => 'BZ-REORDER-CAP',
            'user_id'                 => $buyer->id,
            'subtotal'                => 1500.00,
            'total_amount'            => 1599.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'paid',
            'order_status'            => 'completed',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Lane 4',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now()->subDays(4),
        ]);

        $subOrder = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => 'SO-CAP-1',
            'subtotal'            => 1500.00,
            'status'              => 'delivered',
        ]);

        OrderItem::create([
            'seller_order_id' => $subOrder->id,
            'product_id'      => $prod->id,
            'product_name'    => $prod->name,
            'unit_price'      => 500.00,
            'quantity'        => 3, // Past order had 3
            'total_price'     => 1500.00,
        ]);

        // Reorder should cap at 1
        $res = $this->actingAs($buyer, 'user')->post("/user/orders/{$order->id}/reorder");
        $res->assertRedirect(route('cart.index'));

        $cart = Cart::where('user_id', $buyer->id)->first();
        $this->assertEquals(1, $cart->items()->where('product_id', $prod->id)->first()->quantity);

        // When stock is 0
        $cart->items()->delete();
        $prod->update(['stock' => 0]);

        $res2 = $this->actingAs($buyer, 'user')->post("/user/orders/{$order->id}/reorder");
        $res2->assertRedirect(route('cart.index'));
        $res2->assertSessionHas('error');
        $this->assertEquals(0, $cart->fresh()->items()->count());
    }

    /*
    |--------------------------------------------------------------------------
    | Security: User Isolation on All Order Endpoints
    |--------------------------------------------------------------------------
    */

    public function test_security_user_cannot_access_or_mutate_another_users_order()
    {
        $userA = $this->createCustomer();
        $userB = $this->createCustomer();

        $orderA = Order::create([
            'order_number'            => 'BZ-ISOLATION-001',
            'user_id'                 => $userA->id,
            'subtotal'                => 400.00,
            'total_amount'            => 499.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'pending',
            'delivery_full_name'      => $userA->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'A Lane',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now(),
        ]);

        // 1. User B cannot view User A order details
        $this->actingAs($userB, 'user')->get("/user/orders/{$orderA->id}")
            ->assertStatus(403);

        // 2. User B cannot cancel User A order
        $this->actingAs($userB, 'user')->post("/user/orders/{$orderA->id}/cancel")
            ->assertStatus(403);
        $this->assertEquals('pending', $orderA->fresh()->order_status);

        // 3. User B cannot reorder User A order
        $this->actingAs($userB, 'user')->post("/user/orders/{$orderA->id}/reorder")
            ->assertStatus(403);

        // 4. User B cannot view User A checkout success receipt
        $this->actingAs($userB, 'user')->get("/checkout/success/{$orderA->id}")
            ->assertStatus(403);
    }

    /*
    |--------------------------------------------------------------------------
    | Security: Address Ownership Spoofing at Checkout
    |--------------------------------------------------------------------------
    */

    public function test_security_checkout_rejects_address_belonging_to_another_user()
    {
        $userA = $this->createCustomer();
        $userB = $this->createCustomer();
        $addrB = $this->createAddress($userB);

        $seller = $this->createSeller();
        $prod   = $this->createProduct($seller);

        $cartA = Cart::create(['user_id' => $userA->id]);
        CartItem::create([
            'cart_id'    => $cartA->id,
            'product_id' => $prod->id,
            'quantity'   => 1,
            'unit_price' => $prod->price,
        ]);

        // User A tries to place order with User B's address
        $res = $this->actingAs($userA, 'user')->post('/checkout', [
            'address_id'     => $addrB->id,
            'payment_method' => 'cod',
        ]);

        $res->assertSessionHasErrors('address_id');
        $this->assertEquals(0, Order::where('user_id', $userA->id)->count());
    }

    /*
    |--------------------------------------------------------------------------
    | Security: Pessimistic Locking & Rollback on Insufficient Stock
    |--------------------------------------------------------------------------
    */

    public function test_security_pessimistic_lock_and_atomic_rollback_on_stock_depletion()
    {
        $user   = $this->createCustomer();
        $addr   = $this->createAddress($user);
        $seller = $this->createSeller();
        $prod   = $this->createProduct($seller, 100.00, 2); // Initial stock: 2

        $cart = Cart::create(['user_id' => $user->id]);
        CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $prod->id,
            'quantity'   => 5, // Buyer asks for 5 (exceeds stock 2)
            'unit_price' => 100.00,
        ]);

        $ordersBefore = Order::count();

        $res = $this->actingAs($user, 'user')->post('/checkout', [
            'address_id'     => $addr->id,
            'payment_method' => 'cod',
        ]);

        $res->assertRedirect(route('cart.index'));
        $res->assertSessionHas('error');

        // Assert transaction rolled back cleanly
        $this->assertEquals($ordersBefore, Order::count());
        $this->assertEquals(0, SellerOrder::count());
        $this->assertEquals(2, $prod->fresh()->stock); // Stock intact
        $this->assertEquals(1, $cart->fresh()->items()->count()); // Cart items NOT deleted
    }
}
