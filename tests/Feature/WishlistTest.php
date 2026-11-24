<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    protected User $userA;
    protected User $userB;
    protected User $seller;
    protected Category $category;
    protected Product $product1;
    protected Product $product2;
    protected Product $outOfStockProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA = User::create([
            'name' => 'John Doe',
            'email' => 'john@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->userB = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->seller = User::create([
            'name' => 'Master Artisan',
            'email' => 'artisan@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'seller',
            'status' => 'active',
        ]);

        SellerProfile::create([
            'user_id' => $this->seller->id,
            'shop_name' => 'Artisan Heritage Store',
            'shop_slug' => 'artisan-heritage',
            'seller_type' => 'artisan',
            'status' => 'approved',
            'commission_rate' => 8.5,
        ]);

        $this->category = Category::create([
            'name' => 'Handcrafted Pottery',
            'slug' => 'handcrafted-pottery',
            'status' => 'active',
        ]);

        $this->product1 = Product::create([
            'seller_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'name' => 'Terracotta Water Pitcher',
            'slug' => 'terracotta-water-pitcher',
            'price' => 750.00,
            'stock' => 15,
            'sku' => 'POT-001',
            'sale_type' => 'fixed_price',
            'status' => 'active',
        ]);

        $this->product2 = Product::create([
            'seller_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'name' => 'Glazed Ceramic Dinner Plate',
            'slug' => 'glazed-ceramic-plate',
            'price' => 1200.00,
            'stock' => 5,
            'sku' => 'POT-002',
            'sale_type' => 'fixed_price',
            'status' => 'active',
        ]);

        $this->outOfStockProduct = Product::create([
            'seller_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'name' => 'Rare Antique Brass Lamp',
            'slug' => 'rare-antique-brass-lamp',
            'price' => 3500.00,
            'stock' => 0,
            'sku' => 'POT-003',
            'sale_type' => 'fixed_price',
            'status' => 'active',
        ]);
    }

    public function test_guest_cannot_access_user_wishlist_page(): void
    {
        $response = $this->get(route('user.wishlist.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_wishlist_hub(): void
    {
        $wishlist = Wishlist::create(['user_id' => $this->userA->id]);
        WishlistItem::create(['wishlist_id' => $wishlist->id, 'product_id' => $this->product1->id]);

        $response = $this->actingAs($this->userA, 'user')->get(route('user.wishlist.index'));

        $response->assertStatus(200);
        $response->assertSee('My Saved Finds');
        $response->assertSee('Terracotta Water Pitcher');
        $response->assertSee('₹750.00');
        $response->assertSee('In Stock');
    }

    public function test_authenticated_user_can_toggle_product_in_wishlist_via_ajax(): void
    {
        // 1. Add to wishlist
        $response = $this->actingAs($this->userA, 'user')->postJson(route('wishlist.toggle'), [
            'product_id' => $this->product1->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'in_wishlist' => true,
            'count' => 1,
            'product_id' => $this->product1->id,
        ]);

        $this->assertDatabaseHas('wishlist_items', [
            'product_id' => $this->product1->id,
        ]);

        // 2. Remove from wishlist by toggling again
        $toggleOffResponse = $this->actingAs($this->userA, 'user')->postJson(route('wishlist.toggle'), [
            'product_id' => $this->product1->id,
        ]);

        $toggleOffResponse->assertStatus(200);
        $toggleOffResponse->assertJson([
            'success' => true,
            'in_wishlist' => false,
            'count' => 0,
            'product_id' => $this->product1->id,
        ]);

        $this->assertDatabaseMissing('wishlist_items', [
            'product_id' => $this->product1->id,
        ]);
    }

    public function test_authenticated_user_can_delete_wishlist_item(): void
    {
        $wishlist = Wishlist::create(['user_id' => $this->userA->id]);
        $item = WishlistItem::create(['wishlist_id' => $wishlist->id, 'product_id' => $this->product1->id]);

        $response = $this->actingAs($this->userA, 'user')->deleteJson(route('user.wishlist.destroy', $item));

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'count' => 0]);
        $this->assertDatabaseMissing('wishlist_items', ['id' => $item->id]);
    }

    public function test_user_cannot_delete_another_users_wishlist_item(): void
    {
        $wishlistA = Wishlist::create(['user_id' => $this->userA->id]);
        $itemA = WishlistItem::create(['wishlist_id' => $wishlistA->id, 'product_id' => $this->product1->id]);

        $response = $this->actingAs($this->userB, 'user')->delete(route('user.wishlist.destroy', $itemA));
        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_move_wishlist_item_to_cart(): void
    {
        $wishlist = Wishlist::create(['user_id' => $this->userA->id]);
        $item = WishlistItem::create(['wishlist_id' => $wishlist->id, 'product_id' => $this->product1->id]);

        $response = $this->actingAs($this->userA, 'user')->postJson(route('user.wishlist.move-to-cart', $item));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'cart_count' => 1,
            'wishlist_count' => 0,
        ]);

        // Verifies item moved to cart
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product1->id,
            'quantity' => 1,
        ]);

        // Verifies item removed from wishlist
        $this->assertDatabaseMissing('wishlist_items', [
            'id' => $item->id,
        ]);
    }

    public function test_cannot_move_out_of_stock_item_to_cart(): void
    {
        $wishlist = Wishlist::create(['user_id' => $this->userA->id]);
        $item = WishlistItem::create(['wishlist_id' => $wishlist->id, 'product_id' => $this->outOfStockProduct->id]);

        $response = $this->actingAs($this->userA, 'user')->postJson(route('user.wishlist.move-to-cart', $item));

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $this->assertDatabaseHas('wishlist_items', ['id' => $item->id]);
    }

    public function test_authenticated_user_can_move_all_in_stock_items_to_cart(): void
    {
        $wishlist = Wishlist::create(['user_id' => $this->userA->id]);
        WishlistItem::create(['wishlist_id' => $wishlist->id, 'product_id' => $this->product1->id]);
        WishlistItem::create(['wishlist_id' => $wishlist->id, 'product_id' => $this->product2->id]);
        WishlistItem::create(['wishlist_id' => $wishlist->id, 'product_id' => $this->outOfStockProduct->id]);

        $response = $this->actingAs($this->userA, 'user')->post(route('user.wishlist.move-all-to-cart'));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Products 1 and 2 moved to cart
        $this->assertDatabaseHas('cart_items', ['product_id' => $this->product1->id]);
        $this->assertDatabaseHas('cart_items', ['product_id' => $this->product2->id]);

        // Out of stock product remains saved
        $this->assertDatabaseHas('wishlist_items', ['product_id' => $this->outOfStockProduct->id]);
        $this->assertDatabaseMissing('wishlist_items', ['product_id' => $this->product1->id]);
        $this->assertDatabaseMissing('wishlist_items', ['product_id' => $this->product2->id]);
    }

    public function test_authenticated_user_can_clear_wishlist(): void
    {
        $wishlist = Wishlist::create(['user_id' => $this->userA->id]);
        WishlistItem::create(['wishlist_id' => $wishlist->id, 'product_id' => $this->product1->id]);
        WishlistItem::create(['wishlist_id' => $wishlist->id, 'product_id' => $this->product2->id]);

        $response = $this->actingAs($this->userA, 'user')->post(route('user.wishlist.clear'));

        $response->assertRedirect(route('user.wishlist.index'));
        $this->assertEquals(0, $wishlist->items()->count());
    }

    public function test_authenticated_user_can_save_personal_note_on_wishlist_item(): void
    {
        $wishlist = Wishlist::create(['user_id' => $this->userA->id]);
        $item = WishlistItem::create(['wishlist_id' => $wishlist->id, 'product_id' => $this->product1->id]);

        $response = $this->actingAs($this->userA, 'user')->postJson(route('user.wishlist.note', $item), [
            'note' => 'Gift for Mom on Diwali',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'note' => 'Gift for Mom on Diwali',
        ]);

        $this->assertEquals('Gift for Mom on Diwali', $item->fresh()->note);
    }

    public function test_authenticated_user_can_generate_shareable_wishlist_link(): void
    {
        $response = $this->actingAs($this->userA, 'user')->postJson(route('user.wishlist.share'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertNotEmpty($response->json('sharing_token'));
        $this->assertNotEmpty($response->json('share_url'));

        $wishlist = $this->userA->wishlist()->first();
        $this->assertTrue($wishlist->is_public);
        $this->assertEquals($response->json('sharing_token'), $wishlist->sharing_token);
    }

    public function test_public_user_can_view_shared_wishlist(): void
    {
        $wishlist = Wishlist::create([
            'user_id' => $this->userA->id,
            'sharing_token' => 'WSH-TEST-TOKEN123',
            'is_public' => true,
        ]);
        WishlistItem::create(['wishlist_id' => $wishlist->id, 'product_id' => $this->product1->id]);

        // Access as unauthenticated guest
        $response = $this->get(route('wishlist.public', 'WSH-TEST-TOKEN123'));

        $response->assertStatus(200);
        $response->assertSee("John Doe's Saved Finds");
        $response->assertSee('Terracotta Water Pitcher');
        $response->assertSee('₹750.00');
    }

    public function test_guest_can_toggle_wishlist_in_session(): void
    {
        $response = $this->postJson(route('wishlist.toggle'), [
            'product_id' => $this->product1->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'in_wishlist' => true,
            'count' => 1,
        ]);

        $this->assertEquals([$this->product1->id], session('wishlist'));
    }

    public function test_guest_session_wishlist_merges_into_database_upon_login(): void
    {
        // Session has 2 products
        session(['wishlist' => [$this->product1->id, $this->product2->id]]);

        $response = $this->post(route('login'), [
            'email' => 'john@bazaario.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();

        $wishlist = $this->userA->wishlist()->first();
        $this->assertNotNull($wishlist);
        $this->assertEquals(2, $wishlist->items()->count());
        $this->assertDatabaseHas('wishlist_items', ['wishlist_id' => $wishlist->id, 'product_id' => $this->product1->id]);
        $this->assertDatabaseHas('wishlist_items', ['wishlist_id' => $wishlist->id, 'product_id' => $this->product2->id]);

        // Session wishlist is cleared after merging
        $this->assertNull(session('wishlist'));
    }

    public function test_wishlist_filters_by_search_and_category(): void
    {
        $otherCat = Category::create(['name' => 'Organic Spices', 'slug' => 'organic-spices', 'status' => 'active']);
        $spiceProd = Product::create([
            'seller_id' => $this->seller->id,
            'category_id' => $otherCat->id,
            'name' => 'Kashmiri Saffron Grade A',
            'slug' => 'kashmiri-saffron',
            'price' => 2500.00,
            'stock' => 10,
            'sale_type' => 'fixed_price',
            'status' => 'active',
        ]);

        $wishlist = Wishlist::create(['user_id' => $this->userA->id]);
        WishlistItem::create(['wishlist_id' => $wishlist->id, 'product_id' => $this->product1->id]);
        WishlistItem::create(['wishlist_id' => $wishlist->id, 'product_id' => $spiceProd->id]);

        // Filter by search keyword "Pitcher"
        $searchRes = $this->actingAs($this->userA, 'user')->get(route('user.wishlist.index', ['q' => 'Pitcher']));
        $searchRes->assertStatus(200);
        $searchRes->assertSee('Terracotta Water Pitcher');
        $searchRes->assertDontSee('Kashmiri Saffron');

        // Filter by category
        $catRes = $this->actingAs($this->userA, 'user')->get(route('user.wishlist.index', ['category_id' => $otherCat->id]));
        $catRes->assertStatus(200);
        $catRes->assertSee('Kashmiri Saffron');
        $catRes->assertDontSee('Terracotta Water Pitcher');
    }

    public function test_catalog_shows_red_indication_and_alignment_for_wishlisted_products(): void
    {
        $wishlist = Wishlist::create(['user_id' => $this->userA->id]);
        WishlistItem::create(['wishlist_id' => $wishlist->id, 'product_id' => $this->product1->id]);

        $response = $this->actingAs($this->userA, 'user')->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('w-8 h-8 rounded-full flex items-center justify-center');
        $response->assertSee('text-red-500 ring-1 ring-red-200');
        $response->assertSee("font-variation-settings: 'FILL' 1;");
    }
}
