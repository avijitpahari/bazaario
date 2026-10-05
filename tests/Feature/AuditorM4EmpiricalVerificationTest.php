<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponUsage;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * AuditorM4EmpiricalVerificationTest
 *
 * Forensic auditor empirical tests for Milestone 4 (Features 39 to 49):
 * 1. Real stock decrement in products table upon checkout
 * 2. Pessimistic locking & atomic transaction rollback upon out-of-stock
 * 3. Genuine multi-seller sub-order splitting into seller_orders table
 * 4. Real stock restoration upon order cancellation
 * 5. Authorization barriers for order viewing, cancellation, and reordering
 * 6. Order lifecycle cancellation restrictions (cannot cancel completed orders)
 * 7. Genuine 1-click reorder logic adding past items to cart with stock capping
 * 8. Coupon discount deduction and coupon_usage persistence
 * 9. Inline new address creation during checkout
 * 10. Prevention of cross-user address hijacking
 * 11. View rendering of checkout, success, history, and detail tracking
 */
class AuditorM4EmpiricalVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    protected function createBuyer(array $attributes = []): User
    {
        return User::create(array_merge([
            'name'               => 'Auditor Buyer ' . uniqid(),
            'email'              => 'buyer_' . uniqid() . '@bazaario.com',
            'password'           => Hash::make('Password123!'),
            'role'               => 'user',
            'status'             => 'active',
            'preferred_language' => 'en',
            'email_verified_at'  => now(),
        ], $attributes));
    }

    protected function createSeller(string $shopName = 'Merchant Shop', float $commission = 10.00): User
    {
        $seller = User::create([
            'name'               => 'Merchant ' . uniqid(),
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
            'commission_rate' => $commission,
            'trust_score'     => 92.50,
            'city'            => 'Kolkata',
            'state'           => 'West Bengal',
            'country'         => 'India',
        ]);

        return $seller;
    }

    protected function createProduct(User $seller, float $price = 150.00, int $stock = 25): Product
    {
        $category = Category::firstOrCreate(
            ['slug' => 'grocery-spices'],
            ['name' => 'Grocery & Spices', 'status' => 'active']
        );

        return Product::create([
            'seller_id'         => $seller->id,
            'category_id'       => $category->id,
            'name'              => 'Product ' . uniqid(),
            'slug'              => 'prod-' . uniqid(),
            'short_description' => 'Organic fresh marketplace grocery item.',
            'description'       => 'High quality authentic locally sourced product.',
            'price'             => $price,
            'stock'             => $stock,
            'sku'               => 'SKU-' . strtoupper(Str::random(6)),
            'unit_type'         => 'kg',
            'status'            => 'active',
            'average_rating'    => 4.5,
            'total_reviews'     => 2,
        ]);
    }

    protected function createAddress(User $user): Address
    {
        return Address::create([
            'user_id'        => $user->id,
            'type'           => 'home',
            'full_name'      => $user->name,
            'phone'          => '9830098300',
            'address_line_1' => 'Flat 4B, Green Acres',
            'city'           => 'Kolkata',
            'state'          => 'West Bengal',
            'postal_code'    => '700001',
            'country'        => 'India',
            'is_default'     => true,
        ]);
    }

    /**
     * Check 1: Real stock decrement in products table upon checkout
     */
    public function test_checkout_decrements_product_stock_authentically()
    {
        $buyer = $this->createBuyer();
        $seller = $this->createSeller('Bengal Naturals', 12.00);
        $product = $this->createProduct($seller, 200.00, 30);
        $address = $this->createAddress($buyer);

        $cart = Cart::create(['user_id' => $buyer->id]);
        CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 4,
            'unit_price' => 200.00,
        ]);

        $this->assertEquals(30, $product->fresh()->stock);

        $response = $this->actingAs($buyer, 'user')->post('/checkout', [
            'address_id'         => $address->id,
            'delivery_time_slot' => 'Morning: 8 AM - 12 PM',
            'payment_method'     => 'cod',
            'notes'              => 'Ring bell on arrival',
        ]);

        $response->assertRedirect();

        // 1. Stock must be decremented: 30 - 4 = 26
        $this->assertEquals(26, $product->fresh()->stock);

        // 2. Cart must be cleared
        $this->assertEquals(0, $cart->fresh()->items()->count());

        // 3. Parent order must exist with time slot in notes
        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertNotNull($order);
        $this->assertStringContainsString('Morning: 8 AM - 12 PM', $order->notes);
        $this->assertEquals(800.00, $order->subtotal);
        $this->assertEquals(99.00, $order->shipping_amount);
        $this->assertEquals(899.00, $order->total_amount);
        $this->assertEquals('cod', $order->payment_method);
        $this->assertEquals('pending', $order->order_status);

        // 4. Payment record created
        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(899.00, $payment->amount);
        $this->assertEquals('cod', $payment->payment_method);
    }

    /**
     * Check 2: Multi-seller sub-order splitting into seller_orders table
     */
    public function test_multi_seller_cart_splits_into_distinct_seller_orders_with_commission()
    {
        $buyer = $this->createBuyer();
        $sellerA = $this->createSeller('Seller Alpha', 10.00);
        $sellerB = $this->createSeller('Seller Beta', 15.00);

        $prodA = $this->createProduct($sellerA, 300.00, 20);
        $prodB = $this->createProduct($sellerB, 500.00, 20);
        $address = $this->createAddress($buyer);

        $cart = Cart::create(['user_id' => $buyer->id]);
        CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $prodA->id,
            'quantity'   => 2, // 600
            'unit_price' => 300.00,
        ]);
        CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $prodB->id,
            'quantity'   => 1, // 500
            'unit_price' => 500.00,
        ]);

        $this->actingAs($buyer, 'user')->post('/checkout', [
            'address_id'         => $address->id,
            'delivery_time_slot' => 'Evening: 4 PM - 8 PM',
            'payment_method'     => 'cod',
        ]);

        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertNotNull($order);

        // Subtotal = 1100, shipping = 99, total = 1199
        $this->assertEquals(1100.00, $order->subtotal);

        // Exactly 2 seller orders created
        $sellerOrders = SellerOrder::where('order_id', $order->id)->get();
        $this->assertCount(2, $sellerOrders);

        // Seller A suborder verification
        $soA = $sellerOrders->firstWhere('seller_id', $sellerA->id);
        $this->assertNotNull($soA);
        $this->assertEquals(600.00, $soA->subtotal);
        $this->assertEquals(10.00, $soA->commission_rate);
        $this->assertEquals(60.00, $soA->commission_amount);
        $this->assertEquals(540.00, $soA->payout_amount);
        $this->assertNotEmpty($soA->seller_order_number);
        $this->assertNotEmpty($soA->tracking_number);
        $this->assertStringStartsWith('BZ-TRK-', $soA->tracking_number);

        // Seller B suborder verification
        $soB = $sellerOrders->firstWhere('seller_id', $sellerB->id);
        $this->assertNotNull($soB);
        $this->assertEquals(500.00, $soB->subtotal);
        $this->assertEquals(15.00, $soB->commission_rate);
        $this->assertEquals(75.00, $soB->commission_amount);
        $this->assertEquals(425.00, $soB->payout_amount);
        $this->assertNotEmpty($soB->seller_order_number);
        $this->assertNotEmpty($soB->tracking_number);

        // Both products stock decremented
        $this->assertEquals(18, $prodA->fresh()->stock); // 20 - 2
        $this->assertEquals(19, $prodB->fresh()->stock); // 20 - 1
    }

    /**
     * Check 3: Insufficient stock rolls back transaction cleanly
     */
    public function test_insufficient_stock_prevents_checkout_and_rolls_back()
    {
        $buyer = $this->createBuyer();
        $seller = $this->createSeller('Fresh Direct', 10.00);
        $product = $this->createProduct($seller, 100.00, 3);
        $address = $this->createAddress($buyer);

        $cart = Cart::create(['user_id' => $buyer->id]);
        CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 10, // Exceeds stock (3)
            'unit_price' => 100.00,
        ]);

        $response = $this->actingAs($buyer, 'user')->post('/checkout', [
            'address_id'     => $address->id,
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');

        // Stock must NOT be changed
        $this->assertEquals(3, $product->fresh()->stock);

        // No order created
        $this->assertEquals(0, Order::where('user_id', $buyer->id)->count());

        // Cart items remain intact
        $this->assertEquals(1, $cart->fresh()->items()->count());
    }

    /**
     * Check 4: Real stock restoration upon order cancellation
     */
    public function test_order_cancellation_restores_stock_and_marks_orders_cancelled()
    {
        $buyer = $this->createBuyer();
        $seller = $this->createSeller('Organic Farm', 10.00);
        $product = $this->createProduct($seller, 150.00, 20);
        $address = $this->createAddress($buyer);

        // Place order for 5 units
        $cart = Cart::create(['user_id' => $buyer->id]);
        CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 5,
            'unit_price' => 150.00,
        ]);

        $this->actingAs($buyer, 'user')->post('/checkout', [
            'address_id'     => $address->id,
            'payment_method' => 'cod',
        ]);

        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertEquals(15, $product->fresh()->stock);
        $this->assertEquals('pending', $order->order_status);

        // Cancel order via POST /user/orders/{order}/cancel
        $response = $this->actingAs($buyer, 'user')->post("/user/orders/{$order->id}/cancel");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify status transitions
        $this->assertEquals('cancelled', $order->fresh()->order_status);
        $this->assertEquals('cancelled', $order->fresh()->sellerOrders->first()->status);

        // Verify stock restoration: 15 + 5 = 20
        $this->assertEquals(20, $product->fresh()->stock);
    }

    /**
     * Check 5: Cannot cancel completed orders
     */
    public function test_completed_order_cannot_be_cancelled()
    {
        $buyer = $this->createBuyer();
        $seller = $this->createSeller();
        $product = $this->createProduct($seller, 100.00, 10);
        $address = $this->createAddress($buyer);

        $order = Order::create([
            'order_number'            => 'BZ-COMPLETED-001',
            'user_id'                 => $buyer->id,
            'subtotal'                => 100.00,
            'shipping_amount'         => 99.00,
            'total_amount'            => 199.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'paid',
            'order_status'            => 'completed',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Lane 1',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now(),
        ]);

        $sellerOrder = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => 'SO-' . $order->id . '-1',
            'subtotal'            => 100.00,
            'shipping_amount'     => 99.00,
            'commission_rate'     => 10.00,
            'commission_amount'   => 10.00,
            'payout_amount'       => 90.00,
            'status'              => 'delivered',
        ]);

        OrderItem::create([
            'seller_order_id' => $sellerOrder->id,
            'product_id'      => $product->id,
            'product_name'    => $product->name,
            'unit_price'      => 100.00,
            'quantity'        => 1,
            'total_price'     => 100.00,
        ]);

        $response = $this->actingAs($buyer, 'user')->post("/user/orders/{$order->id}/cancel");
        $response->assertSessionHas('error');

        // Status must remain completed
        $this->assertEquals('completed', $order->fresh()->order_status);
        $this->assertEquals(10, $product->fresh()->stock);
    }

    /**
     * Check 6: Unauthorized buyer cannot cancel another customer's order
     */
    public function test_unauthorized_user_cannot_cancel_another_order()
    {
        $buyerA = $this->createBuyer();
        $buyerB = $this->createBuyer();
        $seller = $this->createSeller();
        $product = $this->createProduct($seller);
        $address = $this->createAddress($buyerA);

        $order = Order::create([
            'order_number'            => 'BZ-AUTH-CANCEL',
            'user_id'                 => $buyerA->id,
            'subtotal'                => 100.00,
            'total_amount'            => 199.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'pending',
            'delivery_full_name'      => $buyerA->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Lane 1',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now(),
        ]);

        // Buyer B tries to cancel Buyer A's order
        $response = $this->actingAs($buyerB, 'user')->post("/user/orders/{$order->id}/cancel");
        $response->assertStatus(403);
        $this->assertEquals('pending', $order->fresh()->order_status);
    }

    /**
     * Check 7: Genuine 1-click reorder logic adding past items to cart with stock capping
     */
    public function test_reorder_adds_items_to_cart_and_caps_at_current_stock()
    {
        $buyer = $this->createBuyer();
        $seller = $this->createSeller();
        $product = $this->createProduct($seller, 120.00, 3); // Current stock only 3!

        $order = Order::create([
            'order_number'            => 'BZ-REORDER-TEST',
            'user_id'                 => $buyer->id,
            'subtotal'                => 1200.00,
            'total_amount'            => 1299.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'paid',
            'order_status'            => 'completed',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Lane 1',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now()->subDays(3),
        ]);

        $sellerOrder = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => 'SO-' . $order->id . '-1',
            'subtotal'            => 1200.00,
            'shipping_amount'     => 99.00,
            'commission_rate'     => 10.00,
            'commission_amount'   => 120.00,
            'payout_amount'       => 1080.00,
            'status'              => 'delivered',
        ]);

        OrderItem::create([
            'seller_order_id' => $sellerOrder->id,
            'product_id'      => $product->id,
            'product_name'    => $product->name,
            'unit_price'      => 120.00,
            'quantity'        => 10, // Past order had 10
            'total_price'     => 1200.00,
        ]);

        $response = $this->actingAs($buyer, 'user')->post("/user/orders/{$order->id}/reorder");
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('success');

        // Cart should have item capped at 3 (product stock is 3)
        $cart = Cart::where('user_id', $buyer->id)->first();
        $this->assertNotNull($cart);
        $cartItem = $cart->items()->where('product_id', $product->id)->first();
        $this->assertNotNull($cartItem);
        $this->assertEquals(3, $cartItem->quantity); // Capped at stock
    }

    /**
     * Check 8: Coupon discount application and usage persistence
     */
    public function test_coupon_discount_applied_and_usage_recorded_at_checkout()
    {
        $buyer = $this->createBuyer();
        $seller = $this->createSeller();
        $product = $this->createProduct($seller, 500.00, 10);
        $address = $this->createAddress($buyer);

        $coupon = Coupon::create([
            'code'                 => 'SAVE150AUDIT',
            'discount_type'        => 'fixed',
            'discount_value'       => 150.00,
            'minimum_order_amount' => 300.00,
            'status'               => 'active',
        ]);

        $cart = Cart::create(['user_id' => $buyer->id]);
        CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 2, // 1000
            'unit_price' => 500.00,
        ]);

        // Put coupon in session
        $this->actingAs($buyer, 'user')->withSession([
            'coupon' => [
                'code'           => 'SAVE150AUDIT',
                'discount'       => 150.00,
                'discount_type'  => 'fixed',
                'discount_value' => 150.00,
            ],
        ])->post('/checkout', [
            'address_id'     => $address->id,
            'payment_method' => 'cod',
        ]);

        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(1000.00, $order->subtotal);
        $this->assertEquals(150.00, $order->discount_amount);
        $this->assertEquals(99.00, $order->shipping_amount);
        $this->assertEquals(949.00, $order->total_amount); // 1000 + 99 - 150
        $this->assertEquals($coupon->id, $order->coupon_id);

        // Coupon usage recorded
        $this->assertDatabaseHas('coupon_usages', [
            'order_id'  => $order->id,
            'user_id'   => $buyer->id,
            'coupon_id' => $coupon->id,
        ]);
    }

    /**
     * Check 9: Inline new address creation during checkout
     */
    public function test_checkout_supports_inline_new_address_creation()
    {
        $buyer = $this->createBuyer();
        $seller = $this->createSeller();
        $product = $this->createProduct($seller, 250.00, 10);

        $cart = Cart::create(['user_id' => $buyer->id]);
        CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'unit_price' => 250.00,
        ]);

        $response = $this->actingAs($buyer, 'user')->post('/checkout', [
            'address_id'         => 'new',
            'new_full_name'      => 'Custom Recipient',
            'new_phone'          => '9876598765',
            'new_address_line_1' => 'Plot 88, Tech Park',
            'new_city'           => 'Bengaluru',
            'new_state'          => 'Karnataka',
            'new_postal_code'    => '560001',
            'new_type'           => 'work',
            'payment_method'     => 'cod',
        ]);

        $response->assertRedirect();

        // New address created in addresses table
        $newAddr = Address::where('user_id', $buyer->id)->first();
        $this->assertNotNull($newAddr);
        $this->assertEquals('Custom Recipient', $newAddr->full_name);
        $this->assertEquals('Bengaluru', $newAddr->city);
        $this->assertEquals('work', $newAddr->type);

        // Order delivery details match
        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertEquals('Custom Recipient', $order->delivery_full_name);
        $this->assertEquals('Bengaluru', $order->delivery_city);
    }

    /**
     * Check 10: Prevention of cross-user address hijacking
     */
    public function test_checkout_rejects_address_belonging_to_another_user()
    {
        $buyerA = $this->createBuyer();
        $buyerB = $this->createBuyer();
        $seller = $this->createSeller();
        $product = $this->createProduct($seller);
        $addressA = $this->createAddress($buyerA);

        $cart = Cart::create(['user_id' => $buyerB->id]);
        CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'unit_price' => 100.00,
        ]);

        // Buyer B attempts to use Buyer A's address
        $response = $this->actingAs($buyerB, 'user')->post('/checkout', [
            'address_id'     => $addressA->id,
            'payment_method' => 'cod',
        ]);

        $response->assertSessionHasErrors('address_id');
        $this->assertEquals(0, Order::where('user_id', $buyerB->id)->count());
    }

    /**
     * Check 11: Views render authentic elements without crash
     */
    public function test_m4_blade_views_render_comprehensively()
    {
        $buyer = $this->createBuyer();
        $seller = $this->createSeller('Bengal Traders', 10.00);
        $product = $this->createProduct($seller, 300.00, 15);
        $address = $this->createAddress($buyer);

        $cart = Cart::create(['user_id' => $buyer->id]);
        CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 2,
            'unit_price' => 300.00,
        ]);

        // 1. Checkout view renders time slots and addresses
        $resCheckout = $this->actingAs($buyer, 'user')->get('/checkout');
        $resCheckout->assertStatus(200);
        $resCheckout->assertSee('Morning: 8 AM - 12 PM');
        $resCheckout->assertSee('Afternoon: 12 PM - 4 PM');
        $resCheckout->assertSee('Cash on Delivery');
        $resCheckout->assertSee($address->full_name);

        // Place order
        $this->actingAs($buyer, 'user')->post('/checkout', [
            'address_id'         => $address->id,
            'delivery_time_slot' => 'Morning: 8 AM - 12 PM',
            'payment_method'     => 'cod',
        ]);

        $order = Order::where('user_id', $buyer->id)->first();

        // 2. Checkout success view renders receipt breakdown
        $resSuccess = $this->actingAs($buyer, 'user')->get("/checkout/success/{$order->id}");
        $resSuccess->assertStatus(200);
        $resSuccess->assertSee($order->order_number);
        $resSuccess->assertSee('Morning: 8 AM - 12 PM');
        $resSuccess->assertSee('Bengal Traders');
        $resSuccess->assertSee('Cash on Delivery Instructions');

        // 3. Orders history index view renders
        $resIndex = $this->actingAs($buyer, 'user')->get('/user/orders');
        $resIndex->assertStatus(200);
        $resIndex->assertSee($order->order_number);
        $resIndex->assertSee('Pending');

        // 4. Order show detail view renders telemetry & courier tracking
        $resShow = $this->actingAs($buyer, 'user')->get("/user/orders/{$order->id}");
        $resShow->assertStatus(200);
        $resShow->assertSee($order->order_number);
        $resShow->assertSee('Courier Tracking #:');
        $resShow->assertSee($order->sellerOrders->first()->tracking_number);
        $resShow->assertSee('Order Status Tracking');
    }
}
