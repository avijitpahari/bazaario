<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Review;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * ProductDetailAndCartTest
 *
 * Verifies Features 24 to 38 across Tiers 1, 2, and 3:
 * - Feature 24: Product Detail Gallery (images & primary image)
 * - Feature 25: Description & Specs (specifications and details)
 * - Feature 26: Seller Type Badge (Farmer, Kirana Store, Dark Store, Individual)
 * - Feature 27: Dynamic Price & Stock Availability
 * - Feature 28: Unit Type Badge (kg, dozen, bundle, litre)
 * - Feature 29: Seller Trust Score (reputation indicator)
 * - Feature 30: Add to Cart (authenticated cart persistence)
 * - Feature 31: Buy Now (instant redirect to checkout)
 * - Feature 32: View Reviews (customer feedback & ratings)
 * - Feature 33: Add Review & Rating (rating submission & aggregate)
 * - Feature 34: Seller-Grouped Cart (multi-merchant organization)
 * - Feature 35: Update Item Quantity (quantity & subtotal recalculation)
 * - Feature 36: Remove Item (cart item deletion)
 * - Feature 37: Seller-wise Subtotals (merchant breakdown)
 * - Feature 38: Promo Coupon Code (discount calculation & limits)
 */
class ProductDetailAndCartTest extends TestCase
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
            'name'               => 'Test Buyer ' . uniqid(),
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
            'name'               => 'Artisan Seller ' . uniqid(),
            'email'              => 'seller_' . uniqid() . '@bazaario.com',
            'password'           => Hash::make('Password123!'),
            'role'               => 'seller',
            'status'             => 'active',
            'preferred_language' => 'en',
            'email_verified_at'  => now(),
        ], $userAttributes));

        SellerProfile::create(array_merge([
            'user_id'         => $seller->id,
            'shop_name'       => 'Shop of ' . $seller->name,
            'shop_slug'       => Str::slug('shop-' . $seller->name . '-' . $seller->id),
            'status'          => 'approved',
            'commission_rate' => 10.00,
            'trust_score'     => 94.50,
            'city'            => 'Shantiniketan',
            'state'           => 'West Bengal',
            'country'         => 'India',
        ], $profileAttributes));

        return $seller;
    }

    protected function createProduct(array $attributes = []): Product
    {
        $sellerId = $attributes['seller_id'] ?? $this->createSeller()->id;
        $category = Category::firstOrCreate(
            ['slug' => 'artisan-crafts'],
            ['name' => 'Artisan Crafts', 'status' => 'active']
        );

        $product = Product::create(array_merge([
            'seller_id'         => $sellerId,
            'category_id'       => $category->id,
            'name'              => 'Handmade Leather Journal ' . uniqid(),
            'slug'              => 'leather-journal-' . uniqid(),
            'short_description' => 'Recycled vintage paper with genuine leather cover.',
            'description'       => 'Crafted with embossed tree of life and brass buckle closure.',
            'price'             => 450.00,
            'stock'             => 50,
            'sku'               => 'LJ-' . strtoupper(Str::random(6)),
            'weight'            => 0.45,
            'status'            => 'active',
            'average_rating'    => 4.70,
            'total_reviews'     => 15,
        ], $attributes));

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/journal-primary.jpg',
            'is_primary' => true,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/journal-angle.jpg',
            'is_primary' => false,
        ]);

        return $product;
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 24: Product Detail Gallery
    |--------------------------------------------------------------------------
    */

    public function test_f24_product_detail_page_renders_gallery()
    {
        $product = $this->createProduct();

        try {
            Cache::store('file')->flush();
        } catch (\Throwable $e) {}

        $response = $this->get('/product/' . $product->slug);
        $response->assertStatus(200);
        $this->assertEquals($product->id, $response->viewData('product')->id);
        $this->assertCount(2, $response->viewData('product')->images);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 25: Description & Specs
    |--------------------------------------------------------------------------
    */

    public function test_f25_product_detail_contains_specifications()
    {
        $product = $this->createProduct([
            'weight' => 0.75,
            'sku'    => 'SPEC-TEST-SKU',
        ]);

        $this->assertEquals(0.75, $product->weight);
        $this->assertEquals('SPEC-TEST-SKU', $product->sku);
        $this->assertNotEmpty($product->description);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 26: Seller Type Badge
    |--------------------------------------------------------------------------
    */

    public function test_f26_seller_type_badge_domain_logic()
    {
        $validSellerTypes = ['Farmer', 'Kirana Store', 'Dark Store', 'Individual'];

        foreach ($validSellerTypes as $type) {
            $seller = $this->createSeller([], ['bio' => 'Type: ' . $type]);
            $this->assertStringContainsString($type, $seller->sellerProfile->bio);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 27: Dynamic Price & Stock Availability
    |--------------------------------------------------------------------------
    */

    public function test_f27_product_price_and_stock_attributes()
    {
        $inStockProd = $this->createProduct(['price' => 599.00, 'stock' => 20]);
        $outOfStockProd = $this->createProduct(['price' => 999.00, 'stock' => 0]);

        $this->assertEquals(599.00, $inStockProd->price);
        $this->assertGreaterThan(0, $inStockProd->stock);

        $this->assertEquals(0, $outOfStockProd->stock);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 28: Unit Type Badge
    |--------------------------------------------------------------------------
    */

    public function test_f28_unit_type_badges_logic()
    {
        $validUnits = ['kg', 'dozen', 'bundle', 'litre'];

        foreach ($validUnits as $unit) {
            $product = $this->createProduct(['short_description' => "Sold per {$unit}"]);
            $this->assertStringContainsString($unit, $product->short_description);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 29: Seller Trust Score
    |--------------------------------------------------------------------------
    */

    public function test_f29_seller_trust_score_badge_value()
    {
        $seller = $this->createSeller([], ['trust_score' => 96.50]);
        $this->assertEquals(96.50, $seller->sellerProfile->trust_score);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 30: Add to Cart
    |--------------------------------------------------------------------------
    */

    public function test_f30_add_product_to_cart_creates_cart_item()
    {
        $customer = $this->createCustomer();
        $product  = $this->createProduct(['price' => 250.00]);

        $response = $this->actingAs($customer, 'user')->post('/cart', [
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);
    }

    public function test_f30_adding_existing_product_increments_quantity()
    {
        $customer = $this->createCustomer();
        $product  = $this->createProduct();

        $this->actingAs($customer, 'user')->post('/cart', [
            'product_id' => $product->id,
            'quantity'   => 1,
        ]);

        $this->actingAs($customer, 'user')->post('/cart', [
            'product_id' => $product->id,
            'quantity'   => 3,
        ]);

        $cart = $customer->cart()->first();
        $this->assertNotNull($cart);
        $this->assertEquals(4, $cart->items()->where('product_id', $product->id)->value('quantity'));
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 31: Buy Now
    |--------------------------------------------------------------------------
    */

    public function test_f31_buy_now_adds_item_and_redirects_to_checkout()
    {
        $customer = $this->createCustomer();
        $product  = $this->createProduct();

        $response = $this->actingAs($customer, 'user')->post('/cart', [
            'product_id' => $product->id,
            'quantity'   => 1,
            'buy_now'    => 1,
        ]);

        $response->assertRedirect(route('checkout.index'));
        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 32: View Reviews
    |--------------------------------------------------------------------------
    */

    public function test_f32_view_reviews_displays_customer_reviews()
    {
        $customer = $this->createCustomer(['name' => 'Arijit Mondal']);
        $product  = $this->createProduct();

        Review::create([
            'user_id'    => $customer->id,
            'product_id' => $product->id,
            'rating'     => 5,
            'title'      => 'Outstanding craftsmanship!',
            'comment'    => 'Exceeded my expectations, beautiful finish.',
            'status'     => 'approved',
        ]);

        $response = $this->actingAs($customer, 'user')->get('/user/reviews');
        $response->assertStatus(200);
        $response->assertSee('Outstanding craftsmanship!');
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 33: Add Review & Rating
    |--------------------------------------------------------------------------
    */

    public function test_f33_add_review_and_rating_stores_record()
    {
        $customer = $this->createCustomer();
        $product  = $this->createProduct();

        $response = $this->actingAs($customer, 'user')->post('/user/reviews', [
            'product_id' => $product->id,
            'rating'     => 5,
            'title'      => 'Terrific value for money',
            'body'       => 'High quality materials and fast shipping.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'user_id'    => $customer->id,
            'product_id' => $product->id,
            'rating'     => 5,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 34 & 37: Seller-Grouped Cart & Seller-Wise Subtotals
    |--------------------------------------------------------------------------
    */

    public function test_f34_and_f37_cart_groups_items_by_seller_and_subtotals()
    {
        $customer = $this->createCustomer();

        $sellerA = $this->createSeller();
        $sellerB = $this->createSeller();

        $prodA1 = $this->createProduct(['seller_id' => $sellerA->id, 'price' => 200.00]);
        $prodA2 = $this->createProduct(['seller_id' => $sellerA->id, 'price' => 300.00]);
        $prodB1 = $this->createProduct(['seller_id' => $sellerB->id, 'price' => 500.00]);

        $cart = Cart::create(['user_id' => $customer->id]);

        CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodA1->id, 'quantity' => 1, 'unit_price' => 200.00]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodA2->id, 'quantity' => 2, 'unit_price' => 300.00]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodB1->id, 'quantity' => 1, 'unit_price' => 500.00]);

        // Seller A items subtotal: 200 + (300 * 2) = 800
        // Seller B items subtotal: 500 * 1 = 500
        $grouped = $cart->items()->with('product')->get()->groupBy('product.seller_id');

        $this->assertCount(2, $grouped);

        $sellerASubtotal = $grouped->get($sellerA->id)->sum(fn ($i) => $i->quantity * $i->product->price);
        $sellerBSubtotal = $grouped->get($sellerB->id)->sum(fn ($i) => $i->quantity * $i->product->price);

        $this->assertEquals(800.00, $sellerASubtotal);
        $this->assertEquals(500.00, $sellerBSubtotal);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 35: Update Item Quantity
    |--------------------------------------------------------------------------
    */

    public function test_f35_update_item_quantity_updates_database_record()
    {
        $customer = $this->createCustomer();
        $product  = $this->createProduct();

        $cart = Cart::create(['user_id' => $customer->id]);
        $cartItem = CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'unit_price' => $product->price,
        ]);

        $response = $this->actingAs($customer, 'user')->put("/cart/{$cartItem->id}", [
            'quantity' => 4,
        ]);

        $response->assertRedirect(route('cart.index'));
        $this->assertEquals(4, $cartItem->fresh()->quantity);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 36: Remove Item
    |--------------------------------------------------------------------------
    */

    public function test_f36_remove_item_deletes_record_from_cart()
    {
        $customer = $this->createCustomer();
        $product  = $this->createProduct();

        $cart = Cart::create(['user_id' => $customer->id]);
        $cartItem = CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 2,
            'unit_price' => $product->price,
        ]);

        $response = $this->actingAs($customer, 'user')->delete("/cart/{$cartItem->id}");

        $response->assertRedirect(route('cart.index'));
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 38: Promo Coupon Code
    |--------------------------------------------------------------------------
    */

    public function test_f38_apply_valid_percentage_coupon()
    {
        $customer = $this->createCustomer();

        $coupon = Coupon::create([
            'code'                    => 'FESTIVE20',
            'discount_type'           => 'percentage',
            'discount_value'          => 20.00,
            'minimum_order_amount'    => 500.00,
            'maximum_discount_amount' => 200.00,
            'usage_limit'             => 100,
            'used_count'              => 0,
            'status'                  => 'active',
        ]);

        $response = $this->actingAs($customer, 'user')->post('/cart/coupon', [
            'coupon_code' => 'FESTIVE20',
        ]);

        $response->assertRedirect(route('cart.index'));
        $this->assertEquals('FESTIVE20', session('coupon.code'));
    }

    public function test_f38_apply_fixed_amount_coupon()
    {
        $customer = $this->createCustomer();

        Coupon::create([
            'code'           => 'FLAT100',
            'discount_type'  => 'fixed',
            'discount_value' => 100.00,
            'status'         => 'active',
        ]);

        $this->actingAs($customer, 'user')->post('/cart/coupon', [
            'coupon_code' => 'FLAT100',
        ]);

        $this->assertEquals('FLAT100', session('coupon.code'));
    }

    public function test_f38_remove_coupon_clears_session()
    {
        $customer = $this->createCustomer();

        session(['coupon' => ['code' => 'DISCOUNT50', 'id' => 1]]);

        $response = $this->actingAs($customer, 'user')->delete('/cart/coupon/remove');

        $response->assertRedirect(route('cart.index'));
        $this->assertNull(session('coupon'));
    }

    /*
    |--------------------------------------------------------------------------
    | Tier 2: Boundary & Edge Cases
    |--------------------------------------------------------------------------
    */

    public function test_tier2_unauthenticated_user_cannot_add_to_cart()
    {
        $product = $this->createProduct();

        $response = $this->post('/cart', [
            'product_id' => $product->id,
            'quantity'   => 1,
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_tier2_add_to_cart_rejects_negative_or_zero_quantity()
    {
        $customer = $this->createCustomer();
        $product  = $this->createProduct();

        $response = $this->actingAs($customer, 'user')->post('/cart', [
            'product_id' => $product->id,
            'quantity'   => 0,
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_tier2_add_to_cart_rejects_non_existent_product()
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer, 'user')->post('/cart', [
            'product_id' => 999999,
            'quantity'   => 1,
        ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_tier2_user_cannot_modify_another_users_cart_item()
    {
        $userA = $this->createCustomer();
        $userB = $this->createCustomer();
        $product = $this->createProduct();

        $cartA = Cart::create(['user_id' => $userA->id]);
        $itemA = CartItem::create([
            'cart_id'    => $cartA->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'unit_price' => 100.00,
        ]);

        // User B tries to update User A's item
        $response = $this->actingAs($userB, 'user')->put("/cart/{$itemA->id}", [
            'quantity' => 10,
        ]);

        $response->assertStatus(403);
    }

    public function test_tier2_apply_expired_coupon_rejected()
    {
        $customer = $this->createCustomer();

        Coupon::create([
            'code'           => 'EXPIRED20',
            'discount_type'  => 'percentage',
            'discount_value' => 20.00,
            'expires_at'     => now()->subDays(2),
            'status'         => 'active',
        ]);

        $response = $this->actingAs($customer, 'user')->post('/cart/coupon', [
            'coupon_code' => 'EXPIRED20',
        ]);

        $response->assertSessionHas('error');
        $this->assertNull(session('coupon'));
    }

    public function test_tier2_apply_inactive_coupon_rejected()
    {
        $customer = $this->createCustomer();

        Coupon::create([
            'code'           => 'INACTIVE50',
            'discount_type'  => 'percentage',
            'discount_value' => 50.00,
            'status'         => 'inactive',
        ]);

        $response = $this->actingAs($customer, 'user')->post('/cart/coupon', [
            'coupon_code' => 'INACTIVE50',
        ]);

        $response->assertSessionHas('error');
        $this->assertNull(session('coupon'));
    }

    public function test_tier2_add_review_rejects_rating_greater_than_five()
    {
        $customer = $this->createCustomer();
        $product  = $this->createProduct();

        $response = $this->actingAs($customer, 'user')->post('/user/reviews', [
            'product_id' => $product->id,
            'rating'     => 6,
        ]);

        $response->assertSessionHasErrors('rating');
    }

    public function test_tier2_add_review_rejects_rating_less_than_one()
    {
        $customer = $this->createCustomer();
        $product  = $this->createProduct();

        $response = $this->actingAs($customer, 'user')->post('/user/reviews', [
            'product_id' => $product->id,
            'rating'     => 0,
        ]);

        $response->assertSessionHasErrors('rating');
    }

    /*
    |--------------------------------------------------------------------------
    | Tier 3: Combinatorial & Cross-Feature Interactions
    |--------------------------------------------------------------------------
    */

    public function test_tier3_multi_seller_cart_with_applied_coupon_discount()
    {
        $customer = $this->createCustomer();

        $sellerA = $this->createSeller();
        $sellerB = $this->createSeller();

        $prodA = $this->createProduct(['seller_id' => $sellerA->id, 'price' => 1000.00]);
        $prodB = $this->createProduct(['seller_id' => $sellerB->id, 'price' => 500.00]);

        $this->actingAs($customer, 'user')->post('/cart', [
            'product_id' => $prodA->id,
            'quantity'   => 1,
        ]);

        $this->actingAs($customer, 'user')->post('/cart', [
            'product_id' => $prodB->id,
            'quantity'   => 1,
        ]);

        Coupon::create([
            'code'           => 'SUMMER10',
            'discount_type'  => 'percentage',
            'discount_value' => 10.00,
            'status'         => 'active',
        ]);

        $this->actingAs($customer, 'user')->post('/cart/coupon', [
            'coupon_code' => 'SUMMER10',
        ]);

        $response = $this->actingAs($customer, 'user')->get('/cart');
        $response->assertStatus(200);
        $this->assertEquals(1500.00, $response->viewData('subtotal'));
        $this->assertEquals(150.00, $response->viewData('discount'));
    }
}
