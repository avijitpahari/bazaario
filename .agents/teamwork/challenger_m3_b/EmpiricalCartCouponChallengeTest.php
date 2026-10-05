<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * EmpiricalCartCouponChallengeTest
 *
 * Challenger M3-B Adversarial Stress Harness for Features 34-38:
 * - Challenge 1: Multi-Seller Cart Grouping, subtotal isolation, quantity updates, single item removals.
 * - Challenge 2: Coupon Validation edge cases (min order ₹499 vs ₹500, max discount ₹100 cap on ₹1000, expired, usage limit, starts_at, subtotal degradation).
 * - Challenge 3: Navbar Cart Badge Sync across multiple sellers and session/DB states.
 */
class EmpiricalCartCouponChallengeTest extends TestCase
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

    protected function createSellerWithProfile(array $sellerData = [], array $profileData = []): User
    {
        $seller = User::create(array_merge([
            'name'               => 'Seller ' . uniqid(),
            'email'              => 'seller_' . uniqid() . '@bazaario.com',
            'password'           => Hash::make('Password123!'),
            'role'               => 'seller',
            'status'             => 'active',
            'preferred_language' => 'en',
            'email_verified_at'  => now(),
        ], $sellerData));

        SellerProfile::create(array_merge([
            'user_id'         => $seller->id,
            'shop_name'       => 'Shop ' . $seller->name,
            'shop_slug'       => Str::slug('shop-' . $seller->name . '-' . $seller->id),
            'seller_type'     => 'Kirana Store',
            'status'          => 'approved',
            'commission_rate' => 10.00,
            'trust_score'     => 95.00,
            'city'            => 'Kolkata',
            'state'           => 'West Bengal',
            'country'         => 'India',
        ], $profileData));

        return $seller;
    }

    protected function createProductForSeller(int $sellerId, array $attributes = []): Product
    {
        $category = Category::firstOrCreate(
            ['slug' => 'test-category'],
            ['name' => 'Test Category', 'status' => 'active']
        );

        $product = Product::create(array_merge([
            'seller_id'         => $sellerId,
            'category_id'       => $category->id,
            'name'              => 'Product ' . uniqid(),
            'slug'              => 'product-' . uniqid(),
            'short_description' => 'Test short description',
            'description'       => 'Test full description with high detail',
            'price'             => 250.00,
            'stock'             => 50,
            'unit_type'         => 'piece',
            'sku'               => 'SKU-' . strtoupper(Str::random(6)),
            'weight'            => 0.5,
            'status'            => 'active',
            'average_rating'    => 4.50,
            'total_reviews'     => 10,
        ], $attributes));

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/test-image.jpg',
            'is_primary' => true,
        ]);

        return $product;
    }

    // =========================================================================
    // SCENARIO 1: Multi-Seller Cart Grouping, Badges, Isolation & Mutations
    // =========================================================================

    public function test_scenario_1_multi_seller_cart_renders_3_merchant_blocks_with_badges_and_subtotals()
    {
        $customer = $this->createCustomer();

        // 3 distinct sellers with distinct types, names, and trust scores
        $sellerA = $this->createSellerWithProfile(
            ['name' => 'Ramesh Crafts'],
            ['shop_name' => 'Ramesh Handlooms', 'seller_type' => 'Farmer', 'trust_score' => 98.2, 'city' => 'Santiniketan']
        );
        $sellerB = $this->createSellerWithProfile(
            ['name' => 'Metro Quick'],
            ['shop_name' => 'Metro Dark Store', 'seller_type' => 'Dark Store', 'trust_score' => 91.5, 'city' => 'Kolkata']
        );
        $sellerC = $this->createSellerWithProfile(
            ['name' => 'Pooja Grocery'],
            ['shop_name' => 'Pooja Daily Needs', 'seller_type' => 'Kirana Store', 'trust_score' => 96.0, 'city' => 'Durgapur']
        );

        // Products for each seller
        $prodA1 = $this->createProductForSeller($sellerA->id, ['name' => 'Organic Cotton Scarf', 'price' => 250.00]);
        $prodA2 = $this->createProductForSeller($sellerA->id, ['name' => 'Handwoven Mat', 'price' => 150.00]);
        $prodB1 = $this->createProductForSeller($sellerB->id, ['name' => 'Instant Brew Coffee', 'price' => 500.00]);
        $prodC1 = $this->createProductForSeller($sellerC->id, ['name' => 'Basmati Rice 5kg', 'price' => 800.00]);

        $cart = Cart::create(['user_id' => $customer->id]);

        // Seller A: (250 * 2) + (150 * 1) = 650.00
        $itemA1 = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodA1->id, 'quantity' => 2, 'unit_price' => 250.00]);
        $itemA2 = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodA2->id, 'quantity' => 1, 'unit_price' => 150.00]);
        // Seller B: 500 * 3 = 1500.00
        $itemB1 = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodB1->id, 'quantity' => 3, 'unit_price' => 500.00]);
        // Seller C: 800 * 1 = 800.00
        $itemC1 = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodC1->id, 'quantity' => 1, 'unit_price' => 800.00]);

        $response = $this->actingAs($customer, 'user')->get('/cart');
        $response->assertStatus(200);

        // Verify view data
        $grouped = $response->viewData('groupedItems');
        $this->assertCount(3, $grouped, 'Cart must group items into exactly 3 merchant blocks.');
        $this->assertTrue($grouped->has($sellerA->id), 'Grouped items must contain Seller A.');
        $this->assertTrue($grouped->has($sellerB->id), 'Grouped items must contain Seller B.');
        $this->assertTrue($grouped->has($sellerC->id), 'Grouped items must contain Seller C.');

        $sellerSubtotals = $response->viewData('sellerSubtotals');
        $this->assertEquals(650.00, $sellerSubtotals[$sellerA->id], 'Seller A subtotal mismatch');
        $this->assertEquals(1500.00, $sellerSubtotals[$sellerB->id], 'Seller B subtotal mismatch');
        $this->assertEquals(800.00, $sellerSubtotals[$sellerC->id], 'Seller C subtotal mismatch');

        $overallSubtotal = $response->viewData('subtotal');
        $this->assertEquals(2950.00, $overallSubtotal, 'Cart overall subtotal mismatch');

        // Verify HTML displays merchant details
        // Seller A
        $response->assertSee('Ramesh Handlooms');
        $response->assertSee('Farmer');
        $response->assertSee('98.2% Trust');
        $response->assertSee('650.00');

        // Seller B
        $response->assertSee('Metro Dark Store');
        $response->assertSee('Dark Store');
        $response->assertSee('91.5% Trust');
        $response->assertSee('1,500.00');

        // Seller C
        $response->assertSee('Pooja Daily Needs');
        $response->assertSee('Kirana Store');
        $response->assertSee('96.0% Trust');
        $response->assertSee('800.00');
    }

    public function test_scenario_1_update_item_quantity_updates_target_seller_subtotal_with_isolation()
    {
        $customer = $this->createCustomer();

        $sellerA = $this->createSellerWithProfile([], ['shop_name' => 'Seller Alpha Store']);
        $sellerB = $this->createSellerWithProfile([], ['shop_name' => 'Seller Beta Store']);
        $sellerC = $this->createSellerWithProfile([], ['shop_name' => 'Seller Gamma Store']);

        $prodA1 = $this->createProductForSeller($sellerA->id, ['price' => 200.00]);
        $prodB1 = $this->createProductForSeller($sellerB->id, ['price' => 300.00]);
        $prodC1 = $this->createProductForSeller($sellerC->id, ['price' => 400.00]);

        $cart = Cart::create(['user_id' => $customer->id]);

        $itemA = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodA1->id, 'quantity' => 1, 'unit_price' => 200.00]);
        $itemB = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodB1->id, 'quantity' => 2, 'unit_price' => 300.00]); // 600.00
        $itemC = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodC1->id, 'quantity' => 1, 'unit_price' => 400.00]); // 400.00

        // Mutation: Update Seller A item quantity from 1 to 5
        $putResponse = $this->actingAs($customer, 'user')->put("/cart/{$itemA->id}", [
            'quantity' => 5,
        ]);
        $putResponse->assertRedirect(route('cart.index'));

        // Query fresh cart page
        $cartPage = $this->actingAs($customer, 'user')->get('/cart');
        $cartPage->assertStatus(200);

        $subtotals = $cartPage->viewData('sellerSubtotals');
        // Seller A: 5 * 200 = 1000.00
        $this->assertEquals(1000.00, $subtotals[$sellerA->id], 'Seller A subtotal should be updated to 1000.00');
        // Seller B: 2 * 300 = 600.00 (MUST REMAIN UNAFFECTED)
        $this->assertEquals(600.00, $subtotals[$sellerB->id], 'Seller B subtotal must be isolated and unaffected at 600.00');
        // Seller C: 1 * 400 = 400.00 (MUST REMAIN UNAFFECTED)
        $this->assertEquals(400.00, $subtotals[$sellerC->id], 'Seller C subtotal must be isolated and unaffected at 400.00');

        $this->assertEquals(2000.00, $cartPage->viewData('subtotal'), 'Overall cart subtotal should be 2000.00');
    }

    public function test_scenario_1_remove_item_from_seller_b_only_deletes_that_item()
    {
        $customer = $this->createCustomer();

        $sellerA = $this->createSellerWithProfile([], ['shop_name' => 'Seller Alpha Store']);
        $sellerB = $this->createSellerWithProfile([], ['shop_name' => 'Seller Beta Store']);
        $sellerC = $this->createSellerWithProfile([], ['shop_name' => 'Seller Gamma Store']);

        $prodA1 = $this->createProductForSeller($sellerA->id, ['price' => 200.00]);
        $prodB1 = $this->createProductForSeller($sellerB->id, ['price' => 300.00]);
        $prodC1 = $this->createProductForSeller($sellerC->id, ['price' => 400.00]);

        $cart = Cart::create(['user_id' => $customer->id]);

        $itemA = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodA1->id, 'quantity' => 2, 'unit_price' => 200.00]);
        $itemB = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodB1->id, 'quantity' => 1, 'unit_price' => 300.00]);
        $itemC = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodC1->id, 'quantity' => 3, 'unit_price' => 400.00]);

        // Mutation: Delete Seller B item
        $deleteResponse = $this->actingAs($customer, 'user')->delete("/cart/{$itemB->id}");
        $deleteResponse->assertRedirect(route('cart.index'));

        // Assert DB status
        $this->assertDatabaseMissing('cart_items', ['id' => $itemB->id]);
        $this->assertDatabaseHas('cart_items', ['id' => $itemA->id, 'quantity' => 2]);
        $this->assertDatabaseHas('cart_items', ['id' => $itemC->id, 'quantity' => 3]);

        // Assert fresh Cart page
        $cartPage = $this->actingAs($customer, 'user')->get('/cart');
        $cartPage->assertStatus(200);

        $grouped = $cartPage->viewData('groupedItems');
        $this->assertCount(2, $grouped, 'Cart should now only contain 2 seller blocks.');
        $this->assertFalse($grouped->has($sellerB->id), 'Seller B block should be removed.');
        $this->assertTrue($grouped->has($sellerA->id), 'Seller A block must remain intact.');
        $this->assertTrue($grouped->has($sellerC->id), 'Seller C block must remain intact.');

        $subtotals = $cartPage->viewData('sellerSubtotals');
        $this->assertEquals(400.00, $subtotals[$sellerA->id]);
        $this->assertEquals(1200.00, $subtotals[$sellerC->id]);
        $this->assertEquals(1600.00, $cartPage->viewData('subtotal'));
    }

    // =========================================================================
    // SCENARIO 2: Coupon Validation Edge Cases
    // =========================================================================

    public function test_scenario_2_coupon_minimum_order_amount_rejected_at_499_and_accepted_at_500()
    {
        $customer = $this->createCustomer();
        $seller = $this->createSellerWithProfile();

        $prod499 = $this->createProductForSeller($seller->id, ['price' => 499.00]);
        $prod1 = $this->createProductForSeller($seller->id, ['price' => 1.00]);

        $coupon = Coupon::create([
            'code'                 => 'MIN500CODE',
            'discount_type'        => 'fixed',
            'discount_value'       => 50.00,
            'minimum_order_amount' => 500.00,
            'status'               => 'active',
        ]);

        $cart = Cart::create(['user_id' => $customer->id]);
        $item = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prod499->id, 'quantity' => 1, 'unit_price' => 499.00]);

        // Case A: Cart Subtotal = ₹499 -> MUST BE REJECTED
        $res499 = $this->actingAs($customer, 'user')->post('/cart/coupon', [
            'coupon_code' => 'MIN500CODE',
        ]);
        $res499->assertRedirect(route('cart.index'));
        $res499->assertSessionHas('error');
        $errorMsg = session('error');
        $this->assertStringContainsString('500.00', $errorMsg, 'Error message must state required minimum order amount.');
        $this->assertNull(session('coupon'), 'Coupon must not be stored in session on rejection.');

        // Case B: Add ₹1.00 product so Cart Subtotal = ₹500.00 -> MUST BE ACCEPTED
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $prod1->id, 'quantity' => 1, 'unit_price' => 1.00]);

        $res500 = $this->actingAs($customer, 'user')->post('/cart/coupon', [
            'coupon_code' => 'MIN500CODE',
        ]);
        $res500->assertRedirect(route('cart.index'));
        $res500->assertSessionHas('success');
        $this->assertEquals('MIN500CODE', session('coupon.code'), 'Coupon must be stored in session on success.');

        // Verify Cart view shows applied discount
        $cartPage = $this->actingAs($customer, 'user')->get('/cart');
        $cartPage->assertStatus(200);
        $this->assertEquals(500.00, $cartPage->viewData('subtotal'));
        $this->assertEquals(50.00, $cartPage->viewData('discount'));
        $this->assertEquals(549.00, $cartPage->viewData('total'), '500 subtotal + 99 shipping - 50 discount = 549');
    }

    public function test_scenario_2_maximum_discount_amount_capped_at_100_for_50_percent_on_1000()
    {
        $customer = $this->createCustomer();
        $seller = $this->createSellerWithProfile();

        $prod1000 = $this->createProductForSeller($seller->id, ['price' => 1000.00]);

        // 50% discount capped at ₹100
        $coupon = Coupon::create([
            'code'                    => 'CAP100',
            'discount_type'           => 'percentage',
            'discount_value'          => 50.00,
            'maximum_discount_amount' => 100.00,
            'status'                  => 'active',
        ]);

        $cart = Cart::create(['user_id' => $customer->id]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $prod1000->id, 'quantity' => 1, 'unit_price' => 1000.00]);

        $applyRes = $this->actingAs($customer, 'user')->post('/cart/coupon', [
            'coupon_code' => 'CAP100',
        ]);
        $applyRes->assertRedirect(route('cart.index'));
        $this->assertEquals('CAP100', session('coupon.code'));

        $cartPage = $this->actingAs($customer, 'user')->get('/cart');
        $cartPage->assertStatus(200);

        // 50% of 1000 would be 500, but maximum cap is 100
        $this->assertEquals(1000.00, $cartPage->viewData('subtotal'));
        $this->assertEquals(100.00, $cartPage->viewData('discount'), 'Discount must be strictly capped at 100.00, not 500.00');
        // Total: 1000 + 99 (shipping) - 100 (capped discount) = 999
        $this->assertEquals(999.00, $cartPage->viewData('total'));
        $cartPage->assertSee('-₹100.00');
    }

    public function test_scenario_2_expired_coupon_rejected()
    {
        $customer = $this->createCustomer();
        $seller = $this->createSellerWithProfile();
        $prod = $this->createProductForSeller($seller->id, ['price' => 300.00]);

        $cart = Cart::create(['user_id' => $customer->id]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $prod->id, 'quantity' => 1, 'unit_price' => 300.00]);

        Coupon::create([
            'code'           => 'EXPIREDNOW',
            'discount_type'  => 'fixed',
            'discount_value' => 50.00,
            'expires_at'     => now()->subMinutes(10),
            'status'         => 'active',
        ]);

        $res = $this->actingAs($customer, 'user')->post('/cart/coupon', [
            'coupon_code' => 'EXPIREDNOW',
        ]);
        $res->assertRedirect(route('cart.index'));
        $res->assertSessionHas('error');
        $this->assertStringContainsString('expired', strtolower(session('error')));
        $this->assertNull(session('coupon'));
    }

    public function test_scenario_2_coupon_exceeding_usage_limit_rejected()
    {
        $customer = $this->createCustomer();
        $seller = $this->createSellerWithProfile();
        $prod = $this->createProductForSeller($seller->id, ['price' => 300.00]);

        $cart = Cart::create(['user_id' => $customer->id]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $prod->id, 'quantity' => 1, 'unit_price' => 300.00]);

        Coupon::create([
            'code'           => 'MAXEDOUT',
            'discount_type'  => 'fixed',
            'discount_value' => 50.00,
            'usage_limit'    => 5,
            'used_count'     => 5,
            'status'         => 'active',
        ]);

        $res = $this->actingAs($customer, 'user')->post('/cart/coupon', [
            'coupon_code' => 'MAXEDOUT',
        ]);
        $res->assertRedirect(route('cart.index'));
        $res->assertSessionHas('error');
        $this->assertStringContainsString('limit', strtolower(session('error')));
        $this->assertNull(session('coupon'));
    }

    public function test_scenario_2_coupon_with_future_starts_at_rejected()
    {
        $customer = $this->createCustomer();
        $seller = $this->createSellerWithProfile();
        $prod = $this->createProductForSeller($seller->id, ['price' => 300.00]);

        $cart = Cart::create(['user_id' => $customer->id]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $prod->id, 'quantity' => 1, 'unit_price' => 300.00]);

        Coupon::create([
            'code'           => 'FUTURECODE',
            'discount_type'  => 'fixed',
            'discount_value' => 50.00,
            'starts_at'      => now()->addDays(3),
            'status'         => 'active',
        ]);

        $res = $this->actingAs($customer, 'user')->post('/cart/coupon', [
            'coupon_code' => 'FUTURECODE',
        ]);
        $res->assertRedirect(route('cart.index'));
        $res->assertSessionHas('error');
        $this->assertStringContainsString('not active yet', strtolower(session('error')));
        $this->assertNull(session('coupon'));
    }

    public function test_scenario_2_coupon_auto_discount_zero_when_cart_subtotal_degrades_below_minimum()
    {
        $customer = $this->createCustomer();
        $seller = $this->createSellerWithProfile();

        $prod = $this->createProductForSeller($seller->id, ['price' => 300.00]);

        $coupon = Coupon::create([
            'code'                 => 'DEGRADECHECK',
            'discount_type'        => 'fixed',
            'discount_value'       => 50.00,
            'minimum_order_amount' => 500.00,
            'status'               => 'active',
        ]);

        $cart = Cart::create(['user_id' => $customer->id]);
        $item = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prod->id, 'quantity' => 2, 'unit_price' => 300.00]); // 600.00

        // Subtotal = 600, apply coupon
        $this->actingAs($customer, 'user')->post('/cart/coupon', ['coupon_code' => 'DEGRADECHECK']);
        $this->assertEquals('DEGRADECHECK', session('coupon.code'));

        $cartPage1 = $this->actingAs($customer, 'user')->get('/cart');
        $this->assertEquals(50.00, $cartPage1->viewData('discount'));

        // Customer reduces quantity to 1 -> Subtotal becomes 300.00 (< 500.00 minimum)
        $this->actingAs($customer, 'user')->put("/cart/{$item->id}", ['quantity' => 1]);

        $cartPage2 = $this->actingAs($customer, 'user')->get('/cart');
        $this->assertEquals(300.00, $cartPage2->viewData('subtotal'));
        $this->assertEquals(0.00, $cartPage2->viewData('discount'), 'Discount must gracefully drop to 0 if cart subtotal degrades below minimum_order_amount');
    }

    // =========================================================================
    // SCENARIO 3: Navbar Cart Badge Sync
    // =========================================================================

    public function test_scenario_3_navbar_cart_badge_reflects_total_items_across_sellers_and_dynamic_mutations()
    {
        $customer = $this->createCustomer();

        $sellerA = $this->createSellerWithProfile();
        $sellerB = $this->createSellerWithProfile();
        $sellerC = $this->createSellerWithProfile();

        $prodA = $this->createProductForSeller($sellerA->id, ['price' => 100.00]);
        $prodB = $this->createProductForSeller($sellerB->id, ['price' => 200.00]);
        $prodC = $this->createProductForSeller($sellerC->id, ['price' => 300.00]);

        $cart = Cart::create(['user_id' => $customer->id]);

        // Seller A: 2, Seller B: 3, Seller C: 1 -> Total: 6
        $itemA = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodA->id, 'quantity' => 2, 'unit_price' => 100.00]);
        $itemB = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodB->id, 'quantity' => 3, 'unit_price' => 200.00]);
        $itemC = CartItem::create(['cart_id' => $cart->id, 'product_id' => $prodC->id, 'quantity' => 1, 'unit_price' => 300.00]);

        // Test authenticated page render (e.g. /cart or /products)
        $response1 = $this->actingAs($customer, 'user')->get('/products');
        $response1->assertStatus(200);

        // Verify nav-user renders badge with count 6
        $content1 = $response1->getContent();
        $this->assertStringContainsString('6', $content1);
        $this->assertStringContainsString('Your Cart (6)', $content1);

        // Mutation: Increase Seller A from 2 to 5 -> Total: 5 + 3 + 1 = 9
        $this->actingAs($customer, 'user')->put("/cart/{$itemA->id}", ['quantity' => 5]);

        $response2 = $this->actingAs($customer, 'user')->get('/products');
        $response2->assertStatus(200);
        $content2 = $response2->getContent();
        $this->assertStringContainsString('Your Cart (9)', $content2);

        // Mutation: Delete Seller B item (3 items) -> Total: 5 + 1 = 6
        $this->actingAs($customer, 'user')->delete("/cart/{$itemB->id}");

        $response3 = $this->actingAs($customer, 'user')->get('/products');
        $response3->assertStatus(200);
        $content3 = $response3->getContent();
        $this->assertStringContainsString('Your Cart (6)', $content3);
    }

    public function test_scenario_3_guest_session_cart_badge_reflects_session_count()
    {
        // Unauthenticated guest user with session cart
        $sessionCart = [
            101 => ['id' => 101, 'name' => 'Guest Item 1', 'price' => 150, 'quantity' => 1],
            102 => ['id' => 102, 'name' => 'Guest Item 2', 'price' => 250, 'quantity' => 2],
        ];

        $response = $this->withSession(['cart' => $sessionCart])->get('/products');
        $response->assertStatus(200);

        // Guest navbar should render cartCount = 2 (items count)
        $response->assertSee('2');
    }
}
