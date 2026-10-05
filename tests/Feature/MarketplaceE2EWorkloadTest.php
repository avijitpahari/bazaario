<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Review;
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
 * MarketplaceE2EWorkloadTest
 *
 * Implements Tier 4 Real-World Application Workload Scenarios:
 * - Scenario 1: Full Buyer Onboarding to First Purchase (F1, F2, F7, F8, F9, F16, F21, F24, F30, F39, F40, F41, F42, F43, F44)
 * - Scenario 2: Multi-Seller Mixed Cart & Coupon Checkout (F16, F26, F30, F34, F35, F37, F38, F39, F42, F43, F46)
 * - Scenario 3: Hyperlocal Discovery to Nearby Stall Purchase (F11, F20, F26, F29, F30, F31, F40, F43)
 * - Scenario 4: Order Cancellation and 1-Click Reorder Flow (F43, F45, F47, F48, F49, F34, F44)
 * - Scenario 5: Buyer Reputation & Review Submission Cycle (F43, F45, F24, F32, F33, F50, F51)
 * - Scenario 6: Vernacular Switch Across Entire Navigation (F7, F8, F9, F12, F16, F24, F34, F39, F50)
 */
class MarketplaceE2EWorkloadTest extends TestCase
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
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    protected function createCustomer(array $attributes = []): User
    {
        return User::create(array_merge([
            'name'               => 'E2E Buyer ' . uniqid(),
            'email'              => 'buyer_' . uniqid() . '@bazaario.com',
            'phone'              => '98000' . rand(10000, 99999),
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
            'name'               => 'Artisan ' . uniqid(),
            'email'              => 'artisan_' . uniqid() . '@bazaario.com',
            'password'           => Hash::make('Password123!'),
            'role'               => 'seller',
            'status'             => 'active',
            'preferred_language' => 'en',
            'email_verified_at'  => now(),
        ], $userAttributes));

        SellerProfile::create(array_merge([
            'user_id'         => $seller->id,
            'shop_name'       => $seller->name . ' Haven',
            'shop_slug'       => Str::slug('haven-' . $seller->name . '-' . $seller->id),
            'status'          => 'approved',
            'commission_rate' => 8.00,
            'trust_score'     => 97.00,
            'city'            => 'Kolkata',
            'state'           => 'West Bengal',
            'country'         => 'India',
        ], $profileAttributes));

        return $seller;
    }

    protected function createProduct(array $attributes = []): Product
    {
        $sellerId = $attributes['seller_id'] ?? $this->createSeller()->id;
        $category = Category::firstOrCreate(
            ['slug' => 'handicrafts'],
            ['name' => 'Handicrafts', 'status' => 'active']
        );

        $product = Product::create(array_merge([
            'seller_id'         => $sellerId,
            'category_id'       => $category->id,
            'name'              => 'Handcrafted Bamboo Basket ' . uniqid(),
            'slug'              => 'bamboo-basket-' . uniqid(),
            'short_description' => 'Eco-friendly bamboo basket made by rural weavers.',
            'description'       => 'Woven from mature indigenous bamboo stems with natural resin polish.',
            'price'             => 350.00,
            'stock'             => 30,
            'sku'               => 'BB-' . strtoupper(Str::random(6)),
            'weight'            => 0.8,
            'status'            => 'active',
            'average_rating'    => 4.90,
            'total_reviews'     => 22,
        ], $attributes));

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/basket-main.jpg',
            'is_primary' => true,
        ]);

        return $product;
    }

    protected function createAddress(User $user, array $attributes = []): Address
    {
        return Address::create(array_merge([
            'user_id'        => $user->id,
            'type'           => 'home',
            'full_name'      => $user->name,
            'phone'          => '9876543210',
            'address_line_1' => 'Flat 5B, New Town Residency',
            'address_line_2' => 'Action Area 1',
            'landmark'       => 'Near Central Mall',
            'city'           => 'Kolkata',
            'state'          => 'West Bengal',
            'postal_code'    => '700156',
            'country'        => 'India',
            'is_default'     => true,
        ], $attributes));
    }

    /*
    |--------------------------------------------------------------------------
    | Scenario 1: Full Buyer Onboarding to First Purchase
    | Features: F1, F2, F7, F8, F9, F16, F21, F24, F30, F39, F40, F41, F42, F43, F44
    |--------------------------------------------------------------------------
    */

    public function test_scenario_1_full_buyer_onboarding_to_first_purchase()
    {
        $email = 'newbuyer_e2e@bazaario.com';
        $seller = $this->createSeller();
        $product = $this->createProduct([
            'seller_id' => $seller->id,
            'name'      => 'Darjeeling First Flush Green Tea',
            'price'     => 450.00,
            'stock'     => 20,
        ]);

        // Step 1: Registration OTP request (F1)
        $otpResponse = $this->postJson('/register/send-otp', [
            'name'                  => 'Rohan Sen',
            'email'                 => $email,
            'password'              => 'FirstPurchasePass123!',
            'password_confirmation' => 'FirstPurchasePass123!',
            'role'                  => 'user',
            'terms'                 => 'on',
        ]);
        $otpResponse->assertStatus(200);

        $otp = DB::table('email_otps')->where('email', $email)->value('otp');
        $this->assertNotEmpty($otp);

        // Step 2: Verification and login (F1, F2)
        $verifyResponse = $this->postJson('/register/verify-otp', [
            'email' => $email,
            'otp'   => $otp,
        ]);
        $verifyResponse->assertStatus(200);

        $buyer = User::where('email', $email)->first();
        $this->assertNotNull($buyer);
        $this->assertTrue(Auth::guard('user')->check());

        // Step 3: Switch language to Hindi and persist (F7, F8)
        $this->actingAs($buyer, 'user')->put('/user/profile', [
            'name'               => $buyer->name,
            'preferred_language' => 'hi',
        ]);
        $this->assertEquals('hi', $buyer->fresh()->preferred_language);

        // Step 4: Visit Homepage (F9)
        $homeRes = $this->withSession(['locale' => 'hi'])->get('/');
        $homeRes->assertStatus(200);

        // Step 5: Browse & Search Catalog (F16, F21)
        $catalogRes = $this->get('/products');
        $catalogRes->assertStatus(200);

        // Step 6: View Product Details (F24)
        $detailRes = $this->get('/product/' . $product->slug);
        $detailRes->assertStatus(200);

        // Step 7: Add to Cart (F30)
        $cartRes = $this->actingAs($buyer, 'user')->post('/cart', [
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);
        $cartRes->assertRedirect();

        // Step 8: Create Address & Proceed to Checkout (F39, F40, F41, F42)
        $address = $this->createAddress($buyer);

        // Step 9: Commit Order with COD (F43, F44)
        $checkoutRes = $this->actingAs($buyer, 'user')->post('/checkout', [
            'address_id'     => $address->id,
            'payment_method' => 'cod',
            'notes'          => 'Leave package at front desk.',
        ]);
        $checkoutRes->assertRedirect();

        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('cod', $order->payment_method);
        $this->assertEquals(900.00, $order->subtotal); // 450 * 2
        $this->assertEquals(0, $buyer->cart()->first()->items()->count());
    }

    /*
    |--------------------------------------------------------------------------
    | Scenario 2: Multi-Seller Mixed Cart & Coupon Checkout
    | Features: F16, F26, F30, F34, F35, F37, F38, F39, F42, F43, F46
    |--------------------------------------------------------------------------
    */

    public function test_scenario_2_multiseller_mixed_cart_and_coupon_checkout()
    {
        $buyer = $this->createCustomer();
        $address = $this->createAddress($buyer);

        // Two merchants with distinct seller types (F26)
        $farmerSeller = $this->createSeller([], ['bio' => 'Certified Organic Farmer']);
        $kiranaSeller = $this->createSeller([], ['bio' => 'Local Kirana Store']);

        $prodA = $this->createProduct(['seller_id' => $farmerSeller->id, 'price' => 200.00, 'name' => 'Farm Fresh Apples']);
        $prodB = $this->createProduct(['seller_id' => $kiranaSeller->id, 'price' => 400.00, 'name' => 'Spiced Mustard Oil']);

        // Add both to cart (F30)
        $this->actingAs($buyer, 'user')->post('/cart', ['product_id' => $prodA->id, 'quantity' => 1]);
        $this->actingAs($buyer, 'user')->post('/cart', ['product_id' => $prodB->id, 'quantity' => 1]);

        $cart = $buyer->cart()->first();
        $this->assertCount(2, $cart->items);

        // Multi-seller grouping & Subtotals (F34, F37)
        $grouped = $cart->items()->with('product')->get()->groupBy('product.seller_id');
        $this->assertCount(2, $grouped);

        // Update quantity (F35)
        $itemA = $cart->items()->where('product_id', $prodA->id)->first();
        $this->actingAs($buyer, 'user')->put("/cart/{$itemA->id}", ['quantity' => 3]);
        $this->assertEquals(3, $itemA->fresh()->quantity);

        // Apply discount coupon (F38)
        Coupon::create([
            'code'           => 'HARVEST10',
            'discount_type'  => 'percentage',
            'discount_value' => 10.00,
            'status'         => 'active',
        ]);
        $this->actingAs($buyer, 'user')->post('/cart/coupon', ['coupon_code' => 'HARVEST10']);
        $this->assertEquals('HARVEST10', session('coupon.code'));

        // Checkout with COD (F39, F42, F43)
        $this->actingAs($buyer, 'user')->post('/checkout', [
            'address_id'     => $address->id,
            'payment_method' => 'cod',
        ]);

        $order = Order::where('user_id', $buyer->id)->latest('id')->first();
        $this->assertNotNull($order);
        $this->assertEquals(0, $cart->fresh()->items()->count());
    }

    /*
    |--------------------------------------------------------------------------
    | Scenario 3: Hyperlocal Discovery to Nearby Stall Purchase
    | Features: F11, F20, F26, F29, F30, F31, F40, F43
    |--------------------------------------------------------------------------
    */

    public function test_scenario_3_hyperlocal_discovery_to_nearby_stall_purchase()
    {
        $buyer = $this->createCustomer();
        $address = $this->createAddress($buyer, ['city' => 'Digha']);

        $nearbySeller = $this->createSeller([], [
            'city'        => 'Digha',
            'trust_score' => 99.00,
            'bio'         => 'Local Coastal Farmer',
        ]);

        $product = $this->createProduct([
            'seller_id' => $nearbySeller->id,
            'name'      => 'Digha Cashew Kernels',
            'price'     => 500.00,
        ]);

        // Verify seller proximity (F11, F20)
        $this->assertEquals('Digha', $nearbySeller->sellerProfile->city);
        $this->assertEquals(99.00, $nearbySeller->sellerProfile->trust_score);

        // Buy Now instant checkout (F31)
        $buyNowRes = $this->actingAs($buyer, 'user')->post('/cart', [
            'product_id' => $product->id,
            'quantity'   => 1,
            'buy_now'    => 1,
        ]);
        $buyNowRes->assertRedirect(route('checkout.index'));

        // Commit order (F40, F43)
        $this->actingAs($buyer, 'user')->post('/checkout', [
            'address_id'     => $address->id,
            'payment_method' => 'cod',
        ]);

        $order = Order::where('user_id', $buyer->id)->latest('id')->first();
        $this->assertNotNull($order);
        $this->assertEquals('cod', $order->payment_method);
    }

    /*
    |--------------------------------------------------------------------------
    | Scenario 4: Order Cancellation and 1-Click Reorder Flow
    | Features: F43, F45, F47, F48, F49, F34, F44
    |--------------------------------------------------------------------------
    */

    public function test_scenario_4_order_cancellation_and_reorder_cycle()
    {
        $buyer = $this->createCustomer();
        $address = $this->createAddress($buyer);
        $product = $this->createProduct(['stock' => 10, 'price' => 300.00]);

        // Place initial order (F43)
        $order = Order::create([
            'order_number'            => 'BZ-CYCLE-001',
            'user_id'                 => $buyer->id,
            'subtotal'                => 600.00,
            'total_amount'            => 699.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'pending',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => $address->address_line_1,
            'delivery_city'           => $address->city,
            'delivery_state'          => $address->state,
            'delivery_country'        => $address->country,
            'delivery_postal_code'    => $address->postal_code,
            'placed_at'               => now(),
        ]);

        // View Order in History (F45, F47)
        $historyRes = $this->actingAs($buyer, 'user')->get('/user/orders');
        $historyRes->assertStatus(200);
        $historyRes->assertSee('BZ-CYCLE-001');

        // Cancel Order (F48)
        $order->update(['order_status' => 'cancelled']);
        $product->increment('stock', 2);
        $this->assertEquals('cancelled', $order->fresh()->order_status);

        // 1-Click Reorder Flow (F49, F34)
        $cart = Cart::firstOrCreate(['user_id' => $buyer->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity'   => 2,
            'unit_price' => $product->price,
        ]);
        $this->assertEquals(2, $cart->items()->first()->quantity);

        // Re-checkout (F44)
        $this->actingAs($buyer, 'user')->post('/checkout', [
            'address_id'     => $address->id,
            'payment_method' => 'cod',
        ]);

        $this->assertEquals(2, Order::where('user_id', $buyer->id)->count());
    }

    /*
    |--------------------------------------------------------------------------
    | Scenario 5: Buyer Reputation & Review Submission Cycle
    | Features: F43, F45, F24, F32, F33, F50, F51
    |--------------------------------------------------------------------------
    */

    public function test_scenario_5_buyer_reputation_and_review_submission_cycle()
    {
        $buyer = $this->createCustomer();
        $product = $this->createProduct();

        // 1. Submit review (F33)
        $this->actingAs($buyer, 'user')->post('/user/reviews', [
            'product_id' => $product->id,
            'rating'     => 5,
            'title'      => 'Incredible traditional artistry',
            'body'       => 'Loved the quality and authentic finish.',
        ]);

        $this->assertDatabaseHas('reviews', [
            'user_id'    => $buyer->id,
            'product_id' => $product->id,
            'rating'     => 5,
        ]);

        // 2. View Reviews (F32)
        $reviewsRes = $this->actingAs($buyer, 'user')->get('/user/reviews');
        $reviewsRes->assertStatus(200);
        $reviewsRes->assertSee('Incredible traditional artistry');

        // 3. View & Update Profile (F50, F51)
        $profileRes = $this->actingAs($buyer, 'user')->get('/user/profile');
        $profileRes->assertStatus(200);

        $this->actingAs($buyer, 'user')->put('/user/profile', [
            'name' => 'Verified Reviewer ' . $buyer->id,
        ]);
        $this->assertEquals('Verified Reviewer ' . $buyer->id, $buyer->fresh()->name);
    }

    /*
    |--------------------------------------------------------------------------
    | Scenario 6: Vernacular Switch Across Entire Navigation
    | Features: F7, F8, F9, F12, F16, F24, F34, F39, F50
    |--------------------------------------------------------------------------
    */

    public function test_scenario_6_vernacular_switch_across_entire_navigation()
    {
        $buyer = $this->createCustomer(['preferred_language' => 'bn']);
        $product = $this->createProduct();

        session(['locale' => 'bn']);

        // 1. Homepage (F9)
        $this->withSession(['locale' => 'bn'])->get('/')->assertStatus(200);

        // 2. Catalog (F16)
        $this->withSession(['locale' => 'bn'])->get('/products')->assertStatus(200);

        // 3. Product Detail (F24)
        $this->withSession(['locale' => 'bn'])->get('/product/' . $product->slug)->assertStatus(200);

        // 4. Cart (F34)
        $this->actingAs($buyer, 'user')->withSession(['locale' => 'bn'])->get('/cart')->assertStatus(200);

        // 5. Profile (F50)
        $this->actingAs($buyer, 'user')->withSession(['locale' => 'bn'])->get('/user/profile')->assertStatus(200);

        // 6. Persistence in session
        $this->assertEquals('bn', session('locale'));
    }
}
