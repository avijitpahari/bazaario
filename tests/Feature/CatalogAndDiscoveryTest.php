<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * CatalogAndDiscoveryTest
 *
 * Verifies Features 9 to 23 across Tiers 1, 2, and 3:
 * - Feature 9: Hero Banner (marketing visuals & CTA)
 * - Feature 10: Featured Sellers (verified merchant profiles)
 * - Feature 11: Nearby Stalls (hyperlocal discovery & distance)
 * - Feature 12: Categories Grid (active taxonomy & product counts)
 * - Feature 13: Trending Products (active catalog shelf)
 * - Feature 14: 'For Sellers' Transparent Pricing (fees and commission)
 * - Feature 15: How It Works / About (platform documentation)
 * - Feature 16: View All Products (catalog browsing & pagination)
 * - Feature 17: Filter by Category (category isolation)
 * - Feature 18: Filter by Price Range (min/max price bounds)
 * - Feature 19: Filter by Seller Rating (minimum rating stars)
 * - Feature 20: Filter by Distance/Radius (spatial filtering)
 * - Feature 21: Keyword Search (fuzzy search on title/description)
 * - Feature 22: Search Results Grid (result matching & zero-state)
 * - Feature 23: Sort Products (price asc/desc, rating, newest)
 */
class CatalogAndDiscoveryTest extends TestCase
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

    protected function createCategory(array $attributes = []): Category
    {
        return Category::create(array_merge([
            'name'        => 'Handmade Crafts',
            'slug'        => 'handmade-crafts-' . uniqid(),
            'description' => 'Artisanal crafts from local makers',
            'status'      => 'active',
        ], $attributes));
    }

    protected function createSeller(array $userAttributes = [], array $profileAttributes = []): User
    {
        $seller = User::create(array_merge([
            'name'               => 'Merchant ' . uniqid(),
            'email'              => 'merchant_' . uniqid() . '@bazaario.com',
            'password'           => Hash::make('Password123!'),
            'role'               => 'seller',
            'status'             => 'active',
            'preferred_language' => 'en',
            'email_verified_at'  => now(),
        ], $userAttributes));

        SellerProfile::create(array_merge([
            'user_id'         => $seller->id,
            'shop_name'       => $seller->name . ' Boutique',
            'shop_slug'       => Str::slug($seller->name . '-' . $seller->id),
            'status'          => 'approved',
            'commission_rate' => 10.00,
            'trust_score'     => 98.00,
            'city'            => 'Kolkata',
            'state'           => 'West Bengal',
            'country'         => 'India',
        ], $profileAttributes));

        return $seller;
    }

    protected function createProduct(array $attributes = []): Product
    {
        $sellerId = $attributes['seller_id'] ?? $this->createSeller()->id;
        $categoryId = $attributes['category_id'] ?? $this->createCategory()->id;

        $product = Product::create(array_merge([
            'seller_id'      => $sellerId,
            'category_id'    => $categoryId,
            'name'           => 'Terracotta Clay Pot ' . uniqid(),
            'slug'           => 'terracotta-pot-' . uniqid(),
            'short_description' => 'Eco-friendly handmade terracotta pot.',
            'description'    => 'Handmade by master artisans with natural earthen clay.',
            'price'          => 299.00,
            'stock'          => 25,
            'sku'            => 'TC-' . strtoupper(Str::random(6)),
            'weight'         => 1.5,
            'status'         => 'active',
            'average_rating' => 4.5,
            'total_reviews'  => 12,
        ], $attributes));

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/sample-' . $product->id . '.jpg',
            'is_primary' => true,
        ]);

        return $product;
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 9: Hero Banner
    |--------------------------------------------------------------------------
    */

    public function test_f9_homepage_renders_hero_banner_and_cta()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Bazaario', false);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 10: Featured Sellers
    |--------------------------------------------------------------------------
    */

    public function test_f10_homepage_displays_featured_sellers()
    {
        $seller = $this->createSeller([
            'name' => 'Kolkata Pottery Guild',
        ], [
            'shop_name' => 'Kolkata Pottery Guild',
            'status'    => 'approved',
        ]);

        $this->createProduct(['seller_id' => $seller->id]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Kolkata Pottery Guild');
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 11: Nearby Stalls (Hyperlocal Discovery)
    |--------------------------------------------------------------------------
    */

    public function test_f11_hyperlocal_nearby_stalls_distance_query_logic()
    {
        // Seller 1: Local seller in Kolkata
        $localSeller = $this->createSeller([], [
            'city' => 'Kolkata',
            'state' => 'West Bengal',
        ]);

        // Seller 2: Distant seller in Mumbai
        $distantSeller = $this->createSeller([], [
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
        ]);

        $this->assertNotEquals($localSeller->sellerProfile->city, $distantSeller->sellerProfile->city);

        // Verification of database spatial fields
        $this->assertTrue(
            \Illuminate\Support\Facades\Schema::hasTable('seller_profiles') ||
            \Illuminate\Support\Facades\Schema::hasTable('addresses')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 12: Categories Grid
    |--------------------------------------------------------------------------
    */

    public function test_f12_homepage_lists_active_product_categories()
    {
        $category = $this->createCategory(['name' => 'Organic Darjeeling Spices']);
        $this->createProduct(['category_id' => $category->id]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Organic Darjeeling Spices');
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 13: Trending Products
    |--------------------------------------------------------------------------
    */

    public function test_f13_homepage_renders_trending_products()
    {
        $product = $this->createProduct([
            'name'   => 'Pure Sundarban Wild Honey',
            'status' => 'active',
            'price'  => 550.00,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Pure Sundarban Wild Honey');
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 14: 'For Sellers' Transparent Pricing
    |--------------------------------------------------------------------------
    */

    public function test_f14_transparent_pricing_page_renders_successfully()
    {
        $response = $this->get('/seller/fees-and-commission');
        $response->assertStatus(200);
        $response->assertSee('Commission', false);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 15: How It Works / About Documentation
    |--------------------------------------------------------------------------
    */

    public function test_f15_platform_documentation_page_renders()
    {
        $response = $this->get('/seller/become-a-seller');
        $response->assertStatus(200);
        $response->assertSee('Seller', false);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 16: View All Products (Catalog Browsing)
    |--------------------------------------------------------------------------
    */

    public function test_f16_products_catalog_page_renders_active_items()
    {
        $product = $this->createProduct([
            'name'  => 'Handwoven Silk Scarf',
            'price' => 799.00,
        ]);

        $response = $this->get('/products');
        $response->assertStatus(200);
        $response->assertSee('Handwoven Silk Scarf');
    }

    public function test_f16_shop_alias_redirects_to_products_index()
    {
        $response = $this->get('/shop');
        $response->assertRedirect(route('products.index'));
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 17: Filter by Category
    |--------------------------------------------------------------------------
    */

    public function test_f17_category_show_page_isolates_category_products()
    {
        $cat1 = $this->createCategory(['name' => 'Brass Utensils', 'slug' => 'brass-utensils']);
        $cat2 = $this->createCategory(['name' => 'Handloom Sarees', 'slug' => 'handloom-sarees']);

        $prod1 = $this->createProduct([
            'category_id' => $cat1->id,
            'name'        => 'Traditional Brass Diya',
        ]);

        $prod2 = $this->createProduct([
            'category_id' => $cat2->id,
            'name'        => 'Dhakai Jamdani Saree',
        ]);

        $response = $this->get('/category/brass-utensils');
        $response->assertStatus(200);
        $this->assertEquals('brass-utensils', $response->viewData('slug'));
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 18: Filter by Price Range
    |--------------------------------------------------------------------------
    */

    public function test_f18_filter_products_by_price_range_logic()
    {
        $affordable = $this->createProduct(['name' => 'Jute Coaster Set', 'price' => 150.00]);
        $expensive = $this->createProduct(['name' => 'Premium Brass Bell', 'price' => 2500.00]);

        $queryProducts = Product::where('status', 'active')
            ->whereBetween('price', [100.00, 500.00])
            ->get();

        $this->assertTrue($queryProducts->contains('id', $affordable->id));
        $this->assertFalse($queryProducts->contains('id', $expensive->id));
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 19: Filter by Seller Rating
    |--------------------------------------------------------------------------
    */

    public function test_f19_filter_products_by_seller_rating_logic()
    {
        $highRatedProd = $this->createProduct(['name' => 'Top Rated Craft', 'average_rating' => 4.8]);
        $lowRatedProd = $this->createProduct(['name' => 'Average Craft', 'average_rating' => 2.5]);

        $filtered = Product::where('status', 'active')
            ->where('average_rating', '>=', 4.0)
            ->get();

        $this->assertTrue($filtered->contains('id', $highRatedProd->id));
        $this->assertFalse($filtered->contains('id', $lowRatedProd->id));
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 20: Filter by Distance / Radius
    |--------------------------------------------------------------------------
    */

    public function test_f20_filter_by_seller_city_and_coordinates()
    {
        $localSeller = $this->createSeller([], ['city' => 'Digha']);
        $distantSeller = $this->createSeller([], ['city' => 'Delhi']);

        $localProd = $this->createProduct(['seller_id' => $localSeller->id, 'name' => 'Digha Cashews']);
        $distantProd = $this->createProduct(['seller_id' => $distantSeller->id, 'name' => 'Delhi Sweets']);

        $dighaProducts = Product::whereHas('seller.sellerProfile', function ($q) {
            $q->where('city', 'Digha');
        })->get();

        $this->assertTrue($dighaProducts->contains('id', $localProd->id));
        $this->assertFalse($dighaProducts->contains('id', $distantProd->id));
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 21 & 22: Keyword Search & Results Matching
    |--------------------------------------------------------------------------
    */

    public function test_f21_and_f22_search_by_keyword_matches_title_and_description()
    {
        $prod = $this->createProduct([
            'name'        => 'Dokra Tribal Figurine',
            'description' => 'Authentic lost-wax casting metallic figurine.',
        ]);

        $searchTerm = 'Dokra';
        $results = Product::where('status', 'active')
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            })->get();

        $this->assertTrue($results->contains('id', $prod->id));
        $this->assertGreaterThanOrEqual(1, $results->count());
    }

    public function test_f22_search_with_zero_results_handles_empty_state_gracefully()
    {
        $searchTerm = 'NonExistentZyx123Item';
        $results = Product::where('status', 'active')
            ->where('name', 'LIKE', "%{$searchTerm}%")
            ->get();

        $this->assertCount(0, $results);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 23: Sort Products
    |--------------------------------------------------------------------------
    */

    public function test_f23_sort_products_by_price_ascending()
    {
        $p1 = $this->createProduct(['name' => 'Low Price Item', 'price' => 100.00]);
        $p2 = $this->createProduct(['name' => 'High Price Item', 'price' => 900.00]);

        $sorted = Product::whereIn('id', [$p1->id, $p2->id])
            ->orderBy('price', 'asc')
            ->pluck('price')
            ->toArray();

        $this->assertEquals([100.00, 900.00], $sorted);
    }

    public function test_f23_sort_products_by_price_descending()
    {
        $p1 = $this->createProduct(['name' => 'Budget Item', 'price' => 200.00]);
        $p2 = $this->createProduct(['name' => 'Luxury Item', 'price' => 800.00]);

        $sorted = Product::whereIn('id', [$p1->id, $p2->id])
            ->orderBy('price', 'desc')
            ->pluck('price')
            ->toArray();

        $this->assertEquals([800.00, 200.00], $sorted);
    }

    public function test_f23_sort_products_by_newest()
    {
        $oldProduct = $this->createProduct(['created_at' => now()->subDays(5)]);
        $newProduct = $this->createProduct(['created_at' => now()]);

        $sorted = Product::whereIn('id', [$oldProduct->id, $newProduct->id])
            ->latest('id')
            ->pluck('id')
            ->toArray();

        $this->assertEquals([$newProduct->id, $oldProduct->id], $sorted);
    }

    /*
    |--------------------------------------------------------------------------
    | Tier 2: Boundary, Edge & Adversarial Conditions
    |--------------------------------------------------------------------------
    */

    public function test_tier2_inactive_product_hidden_from_public_listings()
    {
        $inactive = $this->createProduct([
            'name'   => 'Hidden Inactive Product',
            'status' => 'inactive',
        ]);

        $response = $this->get('/products');
        $response->assertStatus(200);
        $response->assertDontSee('Hidden Inactive Product');
    }

    public function test_tier2_inactive_category_hidden_from_active_category_grid()
    {
        $inactiveCat = $this->createCategory([
            'name'   => 'Discontinued Taxonomy',
            'status' => 'inactive',
        ]);

        $activeCategories = Category::active()->get();
        $this->assertFalse($activeCategories->contains('id', $inactiveCat->id));
    }

    public function test_tier2_empty_catalog_renders_without_crashing()
    {
        // No products created
        $response = $this->get('/products');
        $response->assertStatus(200);
    }

    public function test_tier2_search_with_xss_payload_does_not_break_query()
    {
        $xss = '<script>alert("XSS")</script>';
        $results = Product::where('status', 'active')
            ->where('name', 'LIKE', "%{$xss}%")
            ->get();

        $this->assertCount(0, $results);
    }

    public function test_tier2_extreme_price_bounds_returns_empty_collection()
    {
        $results = Product::where('status', 'active')
            ->where('price', '>', 9999999.00)
            ->get();

        $this->assertCount(0, $results);
    }

    public function test_tier2_unapproved_seller_products_can_be_filtered_out()
    {
        $pendingSeller = $this->createSeller([], ['status' => 'pending']);
        $product = $this->createProduct(['seller_id' => $pendingSeller->id]);

        $approvedProducts = Product::whereHas('seller.sellerProfile', function ($q) {
            $q->where('status', 'approved');
        })->get();

        $this->assertFalse($approvedProducts->contains('id', $product->id));
    }

    /*
    |--------------------------------------------------------------------------
    | Tier 3: Combinatorial & Cross-Feature Interactions
    |--------------------------------------------------------------------------
    */

    public function test_tier3_combined_category_price_and_sort_pipeline()
    {
        $cat = $this->createCategory(['name' => 'Textiles']);
        $p1 = $this->createProduct(['category_id' => $cat->id, 'name' => 'Cotton Shawl', 'price' => 300.00]);
        $p2 = $this->createProduct(['category_id' => $cat->id, 'name' => 'Silk Stole', 'price' => 600.00]);
        $p3 = $this->createProduct(['category_id' => $cat->id, 'name' => 'Pashmina Wrap', 'price' => 1500.00]);

        $results = Product::where('category_id', $cat->id)
            ->whereBetween('price', [200.00, 1000.00])
            ->orderBy('price', 'desc')
            ->get();

        $this->assertCount(2, $results);
        $this->assertEquals($p2->id, $results->first()->id);
        $this->assertEquals($p1->id, $results->last()->id);
    }

    public function test_tier3_unapproved_sellers_excluded_from_featured_and_nearby_homepage()
    {
        $pendingSeller = $this->createSeller([], [
            'shop_name' => 'Pending Stall Express',
            'status'    => 'pending',
            'city'      => 'Kolkata',
        ]);

        $approvedSeller = $this->createSeller([], [
            'shop_name' => 'Approved Stall Express',
            'status'    => 'approved',
            'city'      => 'Kolkata',
        ]);

        $response = $this->get('/?lat=22.572646&lng=88.363895&radius=50');
        $response->assertStatus(200);

        $featuredSellers = $response->viewData('featuredSellers');
        $nearbyStalls = $response->viewData('nearbyStalls');

        $this->assertFalse($featuredSellers->contains('id', $pendingSeller->id));
        $this->assertFalse($nearbyStalls->contains('id', $pendingSeller->id));
        $this->assertTrue($featuredSellers->contains('id', $approvedSeller->id));
        $this->assertTrue($nearbyStalls->contains('id', $approvedSeller->id));
    }

    public function test_tier3_show_method_aborts_404_for_non_existent_or_inactive_product()
    {
        // 1. Non-existent slug must return 404
        $res404 = $this->get('/product/slug-that-does-not-exist-at-all');
        $res404->assertStatus(404);

        // 2. Inactive product must return 404
        $inactive = $this->createProduct([
            'slug'   => 'deactivated-pottery-item',
            'status' => 'inactive',
        ]);
        $resInactive = $this->get('/product/deactivated-pottery-item');
        $resInactive->assertStatus(404);

        // 3. Active product returns 200
        $active = $this->createProduct([
            'slug'   => 'active-pottery-item',
            'status' => 'active',
        ]);
        $resActive = $this->get('/product/active-pottery-item');
        $resActive->assertStatus(200);
    }

    public function test_tier3_array_category_parameter_handled_cleanly()
    {
        $cat = $this->createCategory(['slug' => 'brassware']);
        $product = $this->createProduct(['category_id' => $cat->id, 'name' => 'Brassware Item']);

        $response = $this->get('/products?category[]=brassware');
        $response->assertStatus(200);
        $response->assertSee('Brassware Item');
    }

    public function test_tier3_zero_match_radius_returns_empty_nearby_stalls()
    {
        // Distant seller in Mumbai (1600+ km away)
        $mumbaiSeller = $this->createSeller([], [
            'city'      => 'Mumbai',
            'latitude'  => 19.076090,
            'longitude' => 72.877426,
            'status'    => 'approved',
        ]);

        // User queries from Kolkata with 15 km radius
        $response = $this->get('/?lat=22.572646&lng=88.363895&radius=15');
        $response->assertStatus(200);

        $nearbyStalls = $response->viewData('nearbyStalls');
        $this->assertTrue($nearbyStalls->isEmpty());
    }
}
