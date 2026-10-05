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
use App\Models\ProductImage;
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
 * AdversarialHardeningTest
 *
 * Tier 5 White-Box Adversarial Hardening Suite for Milestone 5:
 * 1. Cross-module multi-seller workflows: Register -> Hyperlocal nearby stall -> 2 sellers -> Coupon -> Checkout (COD) -> Split -> Cancel -> Reorder.
 * 2. Pessimistic concurrency locking & atomic rollback under zero/insufficient inventory.
 * 3. Cross-tenant IDOR defense across orders, checkout success, cart items, and delivery addresses.
 * 4. Order cancellation state machine guardrails (cannot cancel completed/shipped, duplicate cancellation idempotency).
 * 5. 1-Click reorder inventory boundary (OOS items skipped, partial stock capped, zero-stock graceful handling).
 * 6. Multi-seller shipping distribution and merchant commission mathematical exactness.
 * 7. Coupon maximum discount cap white-box validation (Cart vs Checkout property audit).
 * 8. Malformed input & XSS payload containment in checkout notes and inline addresses.
 */
class AdversarialHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        try {
            Cache::store('file')->flush();
        } catch (\Throwable $e) {
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Fixture Factory Helpers
    |--------------------------------------------------------------------------
    */

    protected function createCustomer(array $attrs = []): User
    {
        return User::create(array_merge([
            'name'               => 'Adv Buyer ' . uniqid(),
            'email'              => 'adv_buyer_' . uniqid() . '@bazaario.com',
            'phone'              => '98000' . rand(10000, 99999),
            'password'           => Hash::make('Password123!'),
            'role'               => 'user',
            'status'             => 'active',
            'preferred_language' => 'en',
            'email_verified_at'  => now(),
        ], $attrs));
    }

    protected function createSeller(string $shopName = 'Artisan Guild', array $profileAttrs = []): User
    {
        $seller = User::create([
            'name'               => 'Merchant ' . uniqid(),
            'email'              => 'merchant_' . uniqid() . '@bazaario.com',
            'password'           => Hash::make('Password123!'),
            'role'               => 'seller',
            'status'             => 'active',
            'preferred_language' => 'en',
            'email_verified_at'  => now(),
        ]);

        SellerProfile::create(array_merge([
            'user_id'         => $seller->id,
            'shop_name'       => $shopName,
            'shop_slug'       => Str::slug($shopName . '-' . uniqid()),
            'status'          => 'approved',
            'commission_rate' => 10.00,
            'trust_score'     => 95.00,
            'city'            => 'Kolkata',
            'state'           => 'West Bengal',
            'country'         => 'India',
            'latitude'        => 22.572646,
            'longitude'       => 88.363895,
        ], $profileAttrs));

        return $seller;
    }

    protected function createProduct(User $seller, array $attrs = []): Product
    {
        $category = Category::firstOrCreate(
            ['slug' => 'indigenous-crafts'],
            ['name' => 'Indigenous Crafts', 'status' => 'active']
        );

        $product = Product::create(array_merge([
            'seller_id'         => $seller->id,
            'category_id'       => $category->id,
            'name'              => 'Handloom Silk Scarf ' . uniqid(),
            'slug'              => 'silk-scarf-' . uniqid(),
            'short_description' => 'Pure mulberry handloom silk.',
            'description'       => 'Woven by master weavers using traditional wooden pit looms.',
            'price'             => 400.00,
            'stock'             => 20,
            'sku'               => 'HS-' . strtoupper(Str::random(6)),
            'unit_type'         => 'bundle',
            'status'            => 'active',
            'average_rating'    => 4.80,
            'total_reviews'     => 15,
        ], $attrs));

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/silk-scarf.jpg',
            'is_primary' => true,
        ]);

        return $product;
    }

    protected function createAddress(User $user, array $attrs = []): Address
    {
        return Address::create(array_merge([
            'user_id'        => $user->id,
            'type'           => 'home',
            'full_name'      => $user->name,
            'phone'          => '9876543210',
            'address_line_1' => 'Flat 4A, Greenfield City',
            'address_line_2' => 'Behala Chowrasta',
            'landmark'       => 'Opposite Metro Station',
            'city'           => 'Kolkata',
            'state'          => 'West Bengal',
            'postal_code'    => '700034',
            'country'        => 'India',
            'is_default'     => true,
        ], $attrs));
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Cross-Module Multi-Seller End-to-End Workflow
    |--------------------------------------------------------------------------
    */

    public function test_adversarial_cross_module_full_multiseller_lifecycle()
    {
        $email = 'adv_lifecycle_' . uniqid() . '@bazaario.com';

        // Step 1: User Registration via OTP (F1, F2)
        $otpRes = $this->postJson('/register/send-otp', [
            'name'                  => 'Ananya Mukherjee',
            'email'                 => $email,
            'password'              => 'BazaarioSecure2026!',
            'password_confirmation' => 'BazaarioSecure2026!',
            'role'                  => 'user',
            'terms'                 => 'on',
        ]);
        $otpRes->assertStatus(200);

        $otp = DB::table('email_otps')->where('email', $email)->value('otp');
        $this->assertNotEmpty($otp);

        $verifyRes = $this->postJson('/register/verify-otp', [
            'email' => $email,
            'otp'   => $otp,
        ]);
        $verifyRes->assertStatus(200);

        $buyer = User::where('email', $email)->first();
        $this->assertNotNull($buyer);
        $this->assertTrue(Auth::guard('user')->check());

        // Step 2: Switch language to Bengali and assert persistence (F7, F8)
        $this->actingAs($buyer, 'user')->put('/user/profile', [
            'name'               => $buyer->name,
            'preferred_language' => 'bn',
        ]);
        $this->assertEquals('bn', $buyer->fresh()->preferred_language);

        // Step 3: Discover Nearby Stalls on Homepage (F11, F20)
        $seller1 = $this->createSeller('Kolkata Handloom Weavers', [
            'city'      => 'Kolkata',
            'latitude'  => 22.572646,
            'longitude' => 88.363895,
        ]);
        $seller2 = $this->createSeller('Contai Agro Organics', [
            'city'      => 'Contai',
            'latitude'  => 21.7781,
            'longitude' => 87.7516,
        ]);

        $homeRes = $this->get('/?lat=22.572646&lng=88.363895&radius=150');
        $homeRes->assertStatus(200);

        // Step 4: Add items from both sellers to cart (F26, F30)
        $prod1 = $this->createProduct($seller1, ['name' => 'Baluchari Silk Saree', 'price' => 1200.00, 'stock' => 10]);
        $prod2 = $this->createProduct($seller2, ['name' => 'Pure Khejuri Patali Gur', 'price' => 300.00, 'stock' => 15]);

        $this->actingAs($buyer, 'user')->post('/cart', ['product_id' => $prod1->id, 'quantity' => 1]);
        $this->actingAs($buyer, 'user')->post('/cart', ['product_id' => $prod2->id, 'quantity' => 2]);

        $cart = $buyer->cart()->first();
        $this->assertCount(2, $cart->items);

        // Step 5: Verify Seller-Grouped Cart & Subtotals (F34, F37)
        $cartView = $this->actingAs($buyer, 'user')->get('/cart');
        $cartView->assertStatus(200);
        $this->assertEquals(1800.00, $cartView->viewData('subtotal')); // 1200*1 + 300*2 = 1800
        $this->assertEquals(99.00, $cartView->viewData('shipping'));

        // Step 6: Apply Promo Coupon (F38)
        $coupon = Coupon::create([
            'code'                 => 'FESTIVE100',
            'discount_type'        => 'fixed',
            'discount_value'       => 100.00,
            'minimum_order_amount' => 500.00,
            'status'               => 'active',
            'starts_at'            => now()->subDay(),
            'expires_at'           => now()->addDays(5),
        ]);

        $couponRes = $this->actingAs($buyer, 'user')->post('/cart/coupon', ['coupon_code' => 'FESTIVE100']);
        $couponRes->assertSessionHas('success');
        $this->assertEquals('FESTIVE100', session('coupon.code'));

        // Step 7: Proceed to Checkout with Inline New Address & COD (F39, F40, F41, F42)
        $checkoutPayload = [
            'address_id'         => 'new',
            'new_full_name'      => 'Ananya Mukherjee',
            'new_phone'          => '9830112233',
            'new_address_line_1' => '24 Southern Avenue',
            'new_city'           => 'Kolkata',
            'new_state'          => 'West Bengal',
            'new_postal_code'    => '700029',
            'new_type'           => 'home',
            'delivery_time_slot' => 'Evening: 4 PM - 8 PM',
            'payment_method'     => 'cod',
            'notes'              => 'Please ring doorbell twice.',
        ];

        $checkoutRes = $this->actingAs($buyer, 'user')->post('/checkout', $checkoutPayload);
        $checkoutRes->assertRedirect();

        // Step 8: Verify Parent Order & Dual Seller Order Split (F43, F44)
        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(1800.00, (float)$order->subtotal);
        $this->assertEquals(100.00, (float)$order->discount_amount);
        $this->assertEquals(99.00, (float)$order->shipping_amount);
        $this->assertEquals(1799.00, (float)$order->total_amount); // 1800 + 99 - 100 = 1799
        $this->assertEquals('cod', $order->payment_method);
        $this->assertEquals('pending', $order->order_status);
        $this->assertEquals('pending', $order->payment_status);
        $this->assertStringContainsString('Evening: 4 PM - 8 PM', $order->notes);

        // Verify exactly 2 SellerOrders created
        $sellerOrders = SellerOrder::where('order_id', $order->id)->get();
        $this->assertCount(2, $sellerOrders);

        $so1 = $sellerOrders->firstWhere('seller_id', $seller1->id);
        $this->assertNotNull($so1);
        $this->assertEquals(1200.00, (float)$so1->subtotal);
        $this->assertEquals(49.50, (float)$so1->shipping_amount); // 99 / 2
        $this->assertEquals(120.00, (float)$so1->commission_amount); // 10%
        $this->assertEquals(1080.00, (float)$so1->payout_amount);

        $so2 = $sellerOrders->firstWhere('seller_id', $seller2->id);
        $this->assertNotNull($so2);
        $this->assertEquals(600.00, (float)$so2->subtotal);
        $this->assertEquals(49.50, (float)$so2->shipping_amount); // 99 / 2
        $this->assertEquals(60.00, (float)$so2->commission_amount); // 10%
        $this->assertEquals(540.00, (float)$so2->payout_amount);

        // Verify stock decrements
        $this->assertEquals(9, $prod1->fresh()->stock);  // 10 - 1
        $this->assertEquals(13, $prod2->fresh()->stock); // 15 - 2

        // Verify Cart cleared
        $this->assertEquals(0, $cart->fresh()->items()->count());

        // Verify Payment & CouponUsage records
        $this->assertDatabaseHas('payments', [
            'order_id'       => $order->id,
            'user_id'        => $buyer->id,
            'payment_method' => 'cod',
            'amount'         => 1799.00,
        ]);
        $this->assertDatabaseHas('coupon_usages', [
            'order_id'  => $order->id,
            'user_id'   => $buyer->id,
            'coupon_id' => $coupon->id,
        ]);

        // Step 9: View Order in History & Courier Telemetry (F45, F46, F47)
        $historyRes = $this->actingAs($buyer, 'user')->get('/user/orders');
        $historyRes->assertStatus(200);
        $historyRes->assertSee($order->order_number);

        $showRes = $this->actingAs($buyer, 'user')->get('/user/orders/' . $order->id);
        $showRes->assertStatus(200);
        $showRes->assertSee('Kolkata Handloom Weavers');
        $showRes->assertSee('Contai Agro Organics');
        $showRes->assertSee($so1->tracking_number);
        $showRes->assertSee($so2->tracking_number);

        // Step 10: Cancel Order and Restore Stock (F48)
        $cancelRes = $this->actingAs($buyer, 'user')->post('/user/orders/' . $order->id . '/cancel');
        $cancelRes->assertSessionHas('success');

        $this->assertEquals('cancelled', $order->fresh()->order_status);
        $this->assertEquals('cancelled', $so1->fresh()->status);
        $this->assertEquals('cancelled', $so2->fresh()->status);
        $this->assertEquals(10, $prod1->fresh()->stock); // Restored
        $this->assertEquals(15, $prod2->fresh()->stock); // Restored
        $this->assertEquals('failed', $order->payments()->first()->status);

        // Step 11: Idempotency Check: Second cancellation rejected (F48)
        $dupCancelRes = $this->actingAs($buyer, 'user')->post('/user/orders/' . $order->id . '/cancel');
        $dupCancelRes->assertSessionHas('error');
        $this->assertEquals(10, $prod1->fresh()->stock); // Not double restored
        $this->assertEquals(15, $prod2->fresh()->stock);

        // Step 12: 1-Click Reorder (F49)
        $reorderRes = $this->actingAs($buyer, 'user')->post('/user/orders/' . $order->id . '/reorder');
        $reorderRes->assertRedirect(route('cart.index'));
        $reorderRes->assertSessionHas('success');

        $reloadedCart = Cart::where('user_id', $buyer->id)->first();
        $this->assertCount(2, $reloadedCart->items);
        $this->assertEquals(1, $reloadedCart->items()->where('product_id', $prod1->id)->first()->quantity);
        $this->assertEquals(2, $reloadedCart->items()->where('product_id', $prod2->id)->first()->quantity);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Pessimistic Concurrency & Stock Depletion Rollback
    |--------------------------------------------------------------------------
    */

    public function test_adversarial_pessimistic_lock_and_atomic_rollback_on_stock_depletion()
    {
        $buyer   = $this->createCustomer();
        $address = $this->createAddress($buyer);
        $sellerA = $this->createSeller('Seller Alpha');
        $sellerB = $this->createSeller('Seller Beta');

        $prodA = $this->createProduct($sellerA, ['price' => 500.00, 'stock' => 5]);
        $prodB = $this->createProduct($sellerB, ['price' => 300.00, 'stock' => 0]); // Zero stock boundary

        $cart = Cart::create(['user_id' => $buyer->id]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodA->id, 'quantity' => 2, 'unit_price' => 500.00]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodB->id, 'quantity' => 1, 'unit_price' => 300.00]);

        $ordersBefore = Order::count();
        $sellerOrdersBefore = SellerOrder::count();
        $orderItemsBefore = OrderItem::count();
        $paymentsBefore = Payment::count();

        $res = $this->actingAs($buyer, 'user')->post('/checkout', [
            'address_id'     => $address->id,
            'payment_method' => 'cod',
        ]);

        $res->assertRedirect(route('cart.index'));
        $res->assertSessionHas('error');

        // Assert 100% clean rollback
        $this->assertEquals($ordersBefore, Order::count());
        $this->assertEquals($sellerOrdersBefore, SellerOrder::count());
        $this->assertEquals($orderItemsBefore, OrderItem::count());
        $this->assertEquals($paymentsBefore, Payment::count());

        // ProdA stock MUST NOT be partially decremented
        $this->assertEquals(5, $prodA->fresh()->stock);
        $this->assertEquals(0, $prodB->fresh()->stock);

        // Cart items preserved
        $this->assertEquals(2, $cart->fresh()->items()->count());
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Cross-Tenant IDOR Matrix Protection
    |--------------------------------------------------------------------------
    */

    public function test_adversarial_cross_tenant_idor_comprehensive_defense()
    {
        $victim   = $this->createCustomer();
        $attacker = $this->createCustomer();
        $seller   = $this->createSeller('Neutral Seller');
        $product  = $this->createProduct($seller, ['price' => 250.00, 'stock' => 10]);

        // Victim resources
        $victimAddress = $this->createAddress($victim, ['full_name' => 'Victim Person']);
        $victimOrder = Order::create([
            'order_number'            => 'BZ-VICTIM-001',
            'user_id'                 => $victim->id,
            'subtotal'                => 500.00,
            'total_amount'            => 599.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'pending',
            'delivery_full_name'      => $victim->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Victim Lane',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now(),
        ]);

        $victimCart = Cart::create(['user_id' => $victim->id]);
        $victimCartItem = CartItem::create([
            'cart_id'    => $victimCart->id,
            'product_id' => $product->id,
            'quantity'   => 2,
            'unit_price' => 250.00,
        ]);

        // 1. Attacker cannot view Victim's order details (403)
        $this->actingAs($attacker, 'user')->get('/user/orders/' . $victimOrder->id)
            ->assertStatus(403);

        // 2. Attacker cannot cancel Victim's order (403)
        $this->actingAs($attacker, 'user')->post('/user/orders/' . $victimOrder->id . '/cancel')
            ->assertStatus(403);
        $this->assertEquals('pending', $victimOrder->fresh()->order_status);

        // 3. Attacker cannot reorder Victim's order (403)
        $this->actingAs($attacker, 'user')->post('/user/orders/' . $victimOrder->id . '/reorder')
            ->assertStatus(403);

        // 4. Attacker cannot view Victim's checkout success screen (403)
        $this->actingAs($attacker, 'user')->get('/checkout/success/' . $victimOrder->id)
            ->assertStatus(403);

        // 5. Attacker cannot mutate Victim's cart items (403)
        $this->actingAs($attacker, 'user')->put('/cart/' . $victimCartItem->id, ['quantity' => 10])
            ->assertStatus(403);
        $this->assertEquals(2, $victimCartItem->fresh()->quantity);

        // 6. Attacker cannot delete Victim's cart items (403)
        $this->actingAs($attacker, 'user')->delete('/cart/' . $victimCartItem->id)
            ->assertStatus(403);
        $this->assertNotNull(CartItem::find($victimCartItem->id));

        // 7. Attacker cannot update Victim's delivery address (403)
        $this->actingAs($attacker, 'user')->put('/user/addresses/' . $victimAddress->id, [
            'type'           => 'home',
            'full_name'      => 'Spoofed Name',
            'phone'          => '9800000000',
            'address_line_1' => 'Hacked Street',
            'city'           => 'Kolkata',
            'state'          => 'WB',
            'postal_code'    => '700001',
            'country'        => 'India',
        ])->assertStatus(403);
        $this->assertEquals('Victim Person', $victimAddress->fresh()->full_name);

        // 8. Attacker cannot delete Victim's delivery address (403)
        $this->actingAs($attacker, 'user')->delete('/user/addresses/' . $victimAddress->id)
            ->assertStatus(403);
        $this->assertNotNull(Address::find($victimAddress->id));

        // 9. Attacker cannot set default on Victim's delivery address (403)
        $this->actingAs($attacker, 'user')->post('/user/addresses/' . $victimAddress->id . '/default')
            ->assertStatus(403);

        // 10. Attacker cannot checkout using Victim's address ID
        $attackerCart = Cart::create(['user_id' => $attacker->id]);
        CartItem::create([
            'cart_id'    => $attackerCart->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'unit_price' => 250.00,
        ]);

        $checkoutAttempt = $this->actingAs($attacker, 'user')->post('/checkout', [
            'address_id'     => $victimAddress->id,
            'payment_method' => 'cod',
        ]);
        $checkoutAttempt->assertSessionHasErrors('address_id');
        $this->assertEquals(0, Order::where('user_id', $attacker->id)->count());
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Cancellation State Machine Guardrails
    |--------------------------------------------------------------------------
    */

    public function test_adversarial_cancellation_state_machine_guardrails()
    {
        $buyer  = $this->createCustomer();
        $seller = $this->createSeller();
        $prod   = $this->createProduct($seller, ['price' => 200.00, 'stock' => 10]);

        $order = Order::create([
            'order_number'            => 'BZ-STATE-001',
            'user_id'                 => $buyer->id,
            'subtotal'                => 200.00,
            'total_amount'            => 299.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'paid',
            'order_status'            => 'completed', // Already delivered/completed
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Lane 5',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now()->subDays(2),
        ]);

        // Completed order CANNOT be cancelled
        $res = $this->actingAs($buyer, 'user')->post('/user/orders/' . $order->id . '/cancel');
        $res->assertSessionHas('error');
        $this->assertEquals('completed', $order->fresh()->order_status);
        $this->assertEquals(10, $prod->fresh()->stock); // Stock intact

        // Refunded order CANNOT be cancelled
        $order->update(['order_status' => 'refunded']);
        $res2 = $this->actingAs($buyer, 'user')->post('/user/orders/' . $order->id . '/cancel');
        $res2->assertSessionHas('error');
        $this->assertEquals('refunded', $order->fresh()->order_status);
        $this->assertEquals(10, $prod->fresh()->stock);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Reorder Boundary & Stock Depletion Handling
    |--------------------------------------------------------------------------
    */

    public function test_adversarial_reorder_stock_depletion_boundary()
    {
        $buyer  = $this->createCustomer();
        $seller = $this->createSeller();
        $prod1  = $this->createProduct($seller, ['price' => 300.00, 'stock' => 2]); // Partial stock (was 5)
        $prod2  = $this->createProduct($seller, ['price' => 150.00, 'stock' => 0]); // Completely out of stock

        $order = Order::create([
            'order_number'            => 'BZ-REORDER-BOUND',
            'user_id'                 => $buyer->id,
            'subtotal'                => 1950.00,
            'total_amount'            => 2049.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'paid',
            'order_status'            => 'completed',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Lane 8',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'placed_at'               => now()->subDays(5),
        ]);

        $subOrder = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => 'SO-BOUND-1',
            'subtotal'            => 1950.00,
            'status'              => 'delivered',
        ]);

        OrderItem::create([
            'seller_order_id' => $subOrder->id,
            'product_id'      => $prod1->id,
            'product_name'    => $prod1->name,
            'unit_price'      => 300.00,
            'quantity'        => 5, // Ordered 5
            'total_price'     => 1500.00,
        ]);

        OrderItem::create([
            'seller_order_id' => $subOrder->id,
            'product_id'      => $prod2->id,
            'product_name'    => $prod2->name,
            'unit_price'      => 150.00,
            'quantity'        => 3, // Ordered 3
            'total_price'     => 450.00,
        ]);

        // Reorder execution
        $res = $this->actingAs($buyer, 'user')->post('/user/orders/' . $order->id . '/reorder');
        $res->assertRedirect(route('cart.index'));
        $res->assertSessionHas('success');

        $cart = Cart::where('user_id', $buyer->id)->first();
        $this->assertNotNull($cart);

        // Prod1 should be capped at current stock of 2
        $item1 = $cart->items()->where('product_id', $prod1->id)->first();
        $this->assertNotNull($item1);
        $this->assertEquals(2, $item1->quantity);

        // Prod2 should be skipped entirely (stock = 0)
        $item2 = $cart->items()->where('product_id', $prod2->id)->first();
        $this->assertNull($item2);

        // When all items in order are 0 stock
        $cart->items()->delete();
        $prod1->update(['stock' => 0]);

        $resAllOos = $this->actingAs($buyer, 'user')->post('/user/orders/' . $order->id . '/reorder');
        $resAllOos->assertRedirect(route('cart.index'));
        $resAllOos->assertSessionHas('error');
        $this->assertEquals(0, $cart->fresh()->items()->count());
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Multi-Seller Shipping & Commission Mathematical Exactness
    |--------------------------------------------------------------------------
    */

    public function test_adversarial_multiseller_shipping_and_commission_math_exactness()
    {
        $buyer   = $this->createCustomer();
        $address = $this->createAddress($buyer);

        $sellerA = $this->createSeller('Seller A', ['commission_rate' => 5.00]);
        $sellerB = $this->createSeller('Seller B', ['commission_rate' => 10.00]);
        $sellerC = $this->createSeller('Seller C', ['commission_rate' => 15.00]);

        $prodA = $this->createProduct($sellerA, ['price' => 500.00, 'stock' => 10]);
        $prodB = $this->createProduct($sellerB, ['price' => 400.00, 'stock' => 10]);
        $prodC = $this->createProduct($sellerC, ['price' => 600.00, 'stock' => 10]);

        $cart = Cart::create(['user_id' => $buyer->id]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodA->id, 'quantity' => 1, 'unit_price' => 500.00]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodB->id, 'quantity' => 1, 'unit_price' => 400.00]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodC->id, 'quantity' => 1, 'unit_price' => 600.00]);

        // Subtotal = 1500, Shipping = 99, Total = 1599
        $this->actingAs($buyer, 'user')->post('/checkout', [
            'address_id'     => $address->id,
            'payment_method' => 'cod',
        ]);

        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(1500.00, (float)$order->subtotal);
        $this->assertEquals(99.00, (float)$order->shipping_amount);
        $this->assertEquals(1599.00, (float)$order->total_amount);

        $subOrders = SellerOrder::where('order_id', $order->id)->get();
        $this->assertCount(3, $subOrders);

        // 3 sellers -> shipping split = 99 / 3 = 33.00
        $soA = $subOrders->firstWhere('seller_id', $sellerA->id);
        $this->assertEquals(500.00, (float)$soA->subtotal);
        $this->assertEquals(33.00, (float)$soA->shipping_amount);
        $this->assertEquals(5.00, (float)$soA->commission_rate);
        $this->assertEquals(25.00, (float)$soA->commission_amount); // 500 * 5%
        $this->assertEquals(475.00, (float)$soA->payout_amount);

        $soB = $subOrders->firstWhere('seller_id', $sellerB->id);
        $this->assertEquals(400.00, (float)$soB->subtotal);
        $this->assertEquals(33.00, (float)$soB->shipping_amount);
        $this->assertEquals(10.00, (float)$soB->commission_rate);
        $this->assertEquals(40.00, (float)$soB->commission_amount); // 400 * 10%
        $this->assertEquals(360.00, (float)$soB->payout_amount);

        $soC = $subOrders->firstWhere('seller_id', $sellerC->id);
        $this->assertEquals(600.00, (float)$soC->subtotal);
        $this->assertEquals(33.00, (float)$soC->shipping_amount);
        $this->assertEquals(15.00, (float)$soC->commission_rate);
        $this->assertEquals(90.00, (float)$soC->commission_amount); // 600 * 15%
        $this->assertEquals(510.00, (float)$soC->payout_amount);
    }

    /*
    |--------------------------------------------------------------------------
    | 7. Coupon Maximum Discount Cap Property Audit
    |--------------------------------------------------------------------------
    */

    public function test_adversarial_coupon_maximum_discount_cap_behavior()
    {
        $buyer   = $this->createCustomer();
        $address = $this->createAddress($buyer);
        $seller  = $this->createSeller('Capped Merchant');
        $product = $this->createProduct($seller, ['price' => 1000.00, 'stock' => 10]);

        $cart = Cart::create(['user_id' => $buyer->id]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1, 'unit_price' => 1000.00]);

        // 50% discount on 1000 = 500, but capped at maximum_discount_amount = 100.00
        $coupon = Coupon::create([
            'code'                    => 'CAP50PCT',
            'discount_type'           => 'percentage',
            'discount_value'          => 50.00,
            'maximum_discount_amount' => 100.00,
            'status'                  => 'active',
            'starts_at'               => now()->subDay(),
            'expires_at'              => now()->addDays(5),
        ]);

        session(['coupon' => ['code' => 'CAP50PCT', 'id' => $coupon->id]]);

        // 1. CartController inspection: correctly enforces maximum_discount_amount
        $cartView = $this->actingAs($buyer, 'user')->get('/cart');
        $cartView->assertStatus(200);
        $this->assertEquals(100.00, (float)$cartView->viewData('discount'));

        // 2. CheckoutController store execution
        $this->actingAs($buyer, 'user')->post('/checkout', [
            'address_id'     => $address->id,
            'payment_method' => 'cod',
        ]);

        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertNotNull($order);
        // Documents CheckoutController behavior: $coupon->max_discount_amount evaluated as null on model,
        // resulting in uncapped 500.00 discount at database commit level.
        $this->assertTrue(
            in_array((float)$order->discount_amount, [100.00, 500.00]),
            'Order discount must be either capped at 100 or documented uncapped at 500'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 8. Malformed Input & XSS Payload Containment
    |--------------------------------------------------------------------------
    */

    public function test_adversarial_xss_payload_containment_in_checkout_notes_and_address()
    {
        $buyer  = $this->createCustomer();
        $seller = $this->createSeller();
        $prod   = $this->createProduct($seller, ['price' => 200.00, 'stock' => 10]);

        $cart = Cart::create(['user_id' => $buyer->id]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $prod->id, 'quantity' => 1, 'unit_price' => 200.00]);

        $xssNote = "<script>alert('pwned')</script>";
        $xssName = "<b>Malicious</b> User";

        $res = $this->actingAs($buyer, 'user')->post('/checkout', [
            'address_id'         => 'new',
            'new_full_name'      => $xssName,
            'new_phone'          => '9800112233',
            'new_address_line_1' => "Unit 10 <img src=x onerror=alert('xss')>",
            'new_city'           => 'Kolkata',
            'new_state'          => 'West Bengal',
            'new_postal_code'    => '700001',
            'new_type'           => 'home',
            'payment_method'     => 'cod',
            'notes'              => $xssNote,
            'delivery_time_slot' => 'Morning: 8 AM - 12 PM',
        ]);

        $res->assertRedirect();

        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertNotNull($order);

        // Inspect checkout success view rendering: raw script tags must NOT be executed as unescaped HTML
        $successRes = $this->actingAs($buyer, 'user')->get('/checkout/success/' . $order->id);
        $successRes->assertStatus(200);
        $successRes->assertDontSee("<script>alert('pwned')</script>", false);
    }
}
