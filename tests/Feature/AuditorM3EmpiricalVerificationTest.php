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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuditorM3EmpiricalVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_controller_stores_comment_column_and_recalculates_aggregates()
    {
        $seller = User::create([
            'name'     => 'Seller Test',
            'email'    => 'seller_test@bazaario.com',
            'password' => Hash::make('Password123!'),
            'role'     => 'seller',
            'status'   => 'active',
        ]);

        $category = Category::create([
            'name'   => 'Organic Farm',
            'slug'   => 'organic-farm',
            'status' => 'active',
        ]);

        $product = Product::create([
            'seller_id'      => $seller->id,
            'category_id'    => $category->id,
            'name'           => 'Fresh Alphonso Mangoes',
            'slug'           => 'fresh-alphonso-mangoes',
            'unit_type'      => 'kg',
            'price'          => 400.00,
            'stock'          => 50,
            'sku'            => 'MNG-ALP-001',
            'average_rating' => 0.00,
            'total_reviews'  => 0,
            'status'         => 'active',
        ]);

        $buyer1 = User::create([
            'name'     => 'Buyer One',
            'email'    => 'buyer1@bazaario.com',
            'password' => Hash::make('Password123!'),
            'role'     => 'user',
            'status'   => 'active',
        ]);

        $buyer2 = User::create([
            'name'     => 'Buyer Two',
            'email'    => 'buyer2@bazaario.com',
            'password' => Hash::make('Password123!'),
            'role'     => 'user',
            'status'   => 'active',
        ]);

        // Buyer 1 posts a 5-star review with comment
        $this->actingAs($buyer1, 'user')->post('/user/reviews', [
            'product_id' => $product->id,
            'rating'     => 5,
            'title'      => 'Delicious fruit',
            'comment'    => 'Sweetest Alphonso mangoes I ever bought!',
        ])->assertRedirect();

        // Verify database contains 'comment' column value
        $this->assertDatabaseHas('reviews', [
            'user_id'    => $buyer1->id,
            'product_id' => $product->id,
            'rating'     => 5,
            'title'      => 'Delicious fruit',
            'comment'    => 'Sweetest Alphonso mangoes I ever bought!',
            'status'     => 'approved',
        ]);

        $product->refresh();
        $this->assertEquals(5.00, (float)$product->average_rating);
        $this->assertEquals(1, $product->total_reviews);

        // Buyer 2 posts a 3-star review with body (backward compatibility check)
        $this->actingAs($buyer2, 'user')->post('/user/reviews', [
            'product_id' => $product->id,
            'rating'     => 3,
            'title'      => 'Average packaging',
            'body'       => 'Fruit was good but packaging was slightly dented.',
        ])->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'user_id'    => $buyer2->id,
            'product_id' => $product->id,
            'rating'     => 3,
            'comment'    => 'Fruit was good but packaging was slightly dented.',
        ]);

        $product->refresh();
        // Average of 5 and 3 is 4.00, count is 2
        $this->assertEquals(4.00, (float)$product->average_rating);
        $this->assertEquals(2, $product->total_reviews);
    }

    public function test_coupon_engine_strictly_enforces_all_db_constraints()
    {
        $buyer = User::create([
            'name'     => 'Coupon Tester',
            'email'    => 'coupon_tester@bazaario.com',
            'password' => Hash::make('Password123!'),
            'role'     => 'user',
            'status'   => 'active',
        ]);

        $seller = User::create([
            'name'     => 'Coupon Seller',
            'email'    => 'coupon_seller@bazaario.com',
            'password' => Hash::make('Password123!'),
            'role'     => 'seller',
            'status'   => 'active',
        ]);

        $category = Category::create([
            'name'   => 'Groceries',
            'slug'   => 'groceries',
            'status' => 'active',
        ]);

        $product = Product::create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'name'        => 'Basmati Rice',
            'slug'        => 'basmati-rice',
            'unit_type'   => 'kg',
            'price'       => 150.00,
            'stock'       => 100,
            'status'      => 'active',
        ]);

        // Cart with 2 kg = ₹300 subtotal
        $cart = Cart::create(['user_id' => $buyer->id]);
        CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 2,
            'unit_price' => 150.00,
        ]);

        // Constraint 1: minimum_order_amount = 500 (cart has 300 -> should fail)
        Coupon::create([
            'code'                 => 'MIN500',
            'discount_type'        => 'fixed',
            'discount_value'       => 50.00,
            'minimum_order_amount' => 500.00,
            'status'               => 'active',
        ]);

        $res1 = $this->actingAs($buyer, 'user')->post('/cart/coupon', ['coupon_code' => 'MIN500']);
        $res1->assertSessionHas('error');
        $this->assertNull(session('coupon'));

        // Constraint 2: usage_limit = 2, used_count = 2 (should fail)
        Coupon::create([
            'code'        => 'EXHAUSTED',
            'discount_type'=> 'fixed',
            'discount_value' => 50.00,
            'usage_limit' => 2,
            'used_count'  => 2,
            'status'      => 'active',
        ]);

        $res2 = $this->actingAs($buyer, 'user')->post('/cart/coupon', ['coupon_code' => 'EXHAUSTED']);
        $res2->assertSessionHas('error');
        $this->assertNull(session('coupon'));

        // Constraint 3: expires_at in past (should fail)
        Coupon::create([
            'code'           => 'EXPIREDCOUPON',
            'discount_type'  => 'fixed',
            'discount_value' => 50.00,
            'expires_at'     => now()->subDay(),
            'status'         => 'active',
        ]);

        $res3 = $this->actingAs($buyer, 'user')->post('/cart/coupon', ['coupon_code' => 'EXPIREDCOUPON']);
        $res3->assertSessionHas('error');
        $this->assertNull(session('coupon'));

        // Constraint 4: maximum_discount_amount cap
        // 50% discount on ₹300 is ₹150, but maximum_discount_amount is ₹100
        $coupon4 = Coupon::create([
            'code'                    => 'MAX100',
            'discount_type'           => 'percentage',
            'discount_value'          => 50.00,
            'maximum_discount_amount' => 100.00,
            'status'                  => 'active',
        ]);

        $res4 = $this->actingAs($buyer, 'user')->post('/cart/coupon', ['coupon_code' => 'MAX100']);
        $res4->assertSessionHas('success');
        $this->assertEquals('MAX100', session('coupon.code'));

        $cartView = $this->actingAs($buyer, 'user')->get('/cart');
        $cartView->assertStatus(200);
        $this->assertEquals(300.00, $cartView->viewData('subtotal'));
        // Discount should be capped at 100, not 150!
        $this->assertEquals(100.00, $cartView->viewData('discount'));
    }

    public function test_show_blade_contains_no_iphone_artifacts_and_renders_genuine_data()
    {
        $seller = User::create([
            'name'     => 'Artisan Weaver',
            'email'    => 'weaver@bazaario.com',
            'password' => Hash::make('Password123!'),
            'role'     => 'seller',
            'status'   => 'active',
        ]);

        SellerProfile::create([
            'user_id'     => $seller->id,
            'shop_name'   => 'Handloom Heritage Stall',
            'shop_slug'   => 'handloom-heritage-stall',
            'seller_type' => 'Farmer',
            'trust_score' => 98.50,
            'city'        => 'Santiniketan',
            'state'       => 'West Bengal',
            'status'      => 'approved',
        ]);

        $category = Category::create([
            'name'   => 'Handicrafts',
            'slug'   => 'handicrafts',
            'status' => 'active',
        ]);

        $product = Product::create([
            'seller_id'      => $seller->id,
            'category_id'    => $category->id,
            'name'           => 'Khadi Cotton Saree Handwoven',
            'slug'           => 'khadi-cotton-saree-handwoven',
            'unit_type'      => 'piece',
            'price'          => 1850.00,
            'stock'          => 8,
            'sku'            => 'KHADI-SR-99',
            'weight'         => 0.65,
            'description'    => 'Pure organic khadi handspun by local artisans.',
            'average_rating' => 4.90,
            'total_reviews'  => 1,
            'status'         => 'active',
        ]);

        $reviewer = User::create([
            'name'     => 'Debashis Roy',
            'email'    => 'debashis@example.com',
            'password' => Hash::make('Password123!'),
            'role'     => 'user',
            'status'   => 'active',
        ]);

        Review::create([
            'user_id'    => $reviewer->id,
            'product_id' => $product->id,
            'rating'     => 5,
            'title'      => 'Splendid organic fabric',
            'comment'    => 'The texture and weave quality are exceptional.',
            'status'     => 'approved',
        ]);

        $response = $this->get('/product/' . $product->slug);
        $response->assertStatus(200);

        $content = $response->getContent();

        // 1. Verify NO Apple/iPhone specs appear
        $this->assertStringNotContainsStringIgnoringCase('iPhone 15', $content);
        $this->assertStringNotContainsStringIgnoringCase('A17 Pro', $content);
        $this->assertStringNotContainsStringIgnoringCase('1,19,999', $content);

        // 2. Verify genuine product attributes appear
        $this->assertStringContainsString('Khadi Cotton Saree Handwoven', $content);
        $this->assertStringContainsString('KHADI-SR-99', $content);
        $this->assertStringContainsString('1,850.00', $content);
        $this->assertStringContainsString('piece', $content);
        $this->assertStringContainsString('Farmer', $content);
        $this->assertStringContainsString('98.5% Trust Score', $content);

        // 3. Verify genuine review appears
        $this->assertStringContainsString('Debashis Roy', $content);
        $this->assertStringContainsString('Splendid organic fabric', $content);
        $this->assertStringContainsString('The texture and weave quality are exceptional.', $content);
    }

    public function test_cart_index_blade_groups_by_seller_and_displays_subtotals()
    {
        $buyer = User::create([
            'name'     => 'Cart Tester',
            'email'    => 'cart_tester@bazaario.com',
            'password' => Hash::make('Password123!'),
            'role'     => 'user',
            'status'   => 'active',
        ]);

        $seller1 = User::create([
            'name'     => 'Merchant Alpha',
            'email'    => 'seller1@bazaario.com',
            'password' => Hash::make('Password123!'),
            'role'     => 'seller',
            'status'   => 'active',
        ]);
        SellerProfile::create([
            'user_id'     => $seller1->id,
            'shop_name'   => 'Alpha Spices Stall',
            'shop_slug'   => 'alpha-spices-stall',
            'seller_type' => 'Dark Store',
            'trust_score' => 96.00,
            'status'      => 'approved',
        ]);

        $seller2 = User::create([
            'name'     => 'Merchant Beta',
            'email'    => 'seller2@bazaario.com',
            'password' => Hash::make('Password123!'),
            'role'     => 'seller',
            'status'   => 'active',
        ]);
        SellerProfile::create([
            'user_id'     => $seller2->id,
            'shop_name'   => 'Beta Dairy Farm',
            'shop_slug'   => 'beta-dairy-farm',
            'seller_type' => 'Farmer',
            'trust_score' => 99.00,
            'status'      => 'approved',
        ]);

        $cat = Category::create(['name' => 'General', 'slug' => 'general', 'status' => 'active']);

        $p1 = Product::create([
            'seller_id'   => $seller1->id,
            'category_id' => $cat->id,
            'name'        => 'Cardamom Pods',
            'slug'        => 'cardamom-pods',
            'unit_type'   => 'bundle',
            'price'       => 250.00,
            'stock'       => 20,
            'status'      => 'active',
        ]);

        $p2 = Product::create([
            'seller_id'   => $seller2->id,
            'category_id' => $cat->id,
            'name'        => 'Pure Cow Ghee',
            'slug'        => 'pure-cow-ghee',
            'unit_type'   => 'litre',
            'price'       => 700.00,
            'stock'       => 15,
            'status'      => 'active',
        ]);

        $cart = Cart::create(['user_id' => $buyer->id]);
        CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $p1->id,
            'quantity'   => 2,
            'unit_price' => 250.00,
        ]);
        CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $p2->id,
            'quantity'   => 1,
            'unit_price' => 700.00,
        ]);

        $response = $this->actingAs($buyer, 'user')->get('/cart');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Check merchant headers and badges rendered
        $this->assertStringContainsString('Alpha Spices Stall', $content);
        $this->assertStringContainsString('Dark Store', $content);
        $this->assertStringContainsString('96.0% Trust', $content);
        $this->assertStringContainsString('Merchant Consignment Subtotal:', $content);
        $this->assertStringContainsString('500.00', $content); // 250 * 2

        $this->assertStringContainsString('Beta Dairy Farm', $content);
        $this->assertStringContainsString('Farmer', $content);
        $this->assertStringContainsString('99.0% Trust', $content);
        $this->assertStringContainsString('700.00', $content); // 700 * 1

        // Check overall total calculation
        // Subtotal = 1200, Shipping = 99 -> Total = 1299
        $this->assertEquals(1200.00, $response->viewData('subtotal'));
        $this->assertEquals(99.00, $response->viewData('shipping'));
        $this->assertEquals(1299.00, $response->viewData('total'));
    }
}
