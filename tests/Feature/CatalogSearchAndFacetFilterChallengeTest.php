<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * CatalogSearchAndFacetFilterChallengeTest
 *
 * Empirical Challenge Suite for Milestone 2:
 * 1. Keyword search (partial match, special characters, SQL meta-characters, zero matches with empty state validation).
 * 2. Combined multi-facet filters & sorting (category, min/max price, rating, proximity, sorting price_low/price_high/rating/newest).
 * 3. Dynamic category views (/category/{slug}) verifying dynamic DB product rendering, absence of static mockups, empty states.
 */
class CatalogSearchAndFacetFilterChallengeTest extends TestCase
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
    | Data Factories & Helpers
    |--------------------------------------------------------------------------
    */

    protected function makeCategory(array $overrides = []): Category
    {
        return Category::create(array_merge([
            'name'        => 'Handmade Crafts ' . uniqid(),
            'slug'        => 'handmade-crafts-' . uniqid(),
            'description' => 'Authentic handmade crafts from certified artisans.',
            'status'      => 'active',
        ], $overrides));
    }

    protected function makeSeller(array $userOverrides = [], array $profileOverrides = []): User
    {
        $seller = User::create(array_merge([
            'name'               => 'Artisan Merchant ' . uniqid(),
            'email'              => 'artisan_' . uniqid() . '@bazaario.com',
            'password'           => Hash::make('Secret123!'),
            'role'               => 'seller',
            'status'             => 'active',
            'preferred_language' => 'en',
            'email_verified_at'  => now(),
        ], $userOverrides));

        SellerProfile::create(array_merge([
            'user_id'         => $seller->id,
            'shop_name'       => $seller->name . ' Studio',
            'shop_slug'       => Str::slug($seller->name . '-' . $seller->id),
            'status'          => 'approved',
            'commission_rate' => 10.00,
            'trust_score'     => 95.00,
            'city'            => 'Kolkata',
            'state'           => 'West Bengal',
            'country'         => 'India',
            'latitude'        => 22.572646,
            'longitude'       => 88.363895,
        ], $profileOverrides));

        return $seller;
    }

    protected function makeProduct(array $overrides = []): Product
    {
        $sellerId = $overrides['seller_id'] ?? $this->makeSeller()->id;
        $categoryId = $overrides['category_id'] ?? $this->makeCategory()->id;

        $product = Product::create(array_merge([
            'seller_id'         => $sellerId,
            'category_id'       => $categoryId,
            'name'              => 'Artisan Pot ' . uniqid(),
            'slug'              => 'artisan-pot-' . uniqid(),
            'short_description' => 'Handcrafted earthenware piece.',
            'description'       => 'Carefully moulded and kiln-fired by heritage craftspeople.',
            'price'             => 499.00,
            'stock'             => 20,
            'sku'               => 'SKU-' . strtoupper(Str::random(8)),
            'weight'            => 1.20,
            'status'            => 'active',
            'average_rating'    => 4.50,
            'total_reviews'     => 10,
        ], $overrides));

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/item-' . $product->id . '.jpg',
            'is_primary' => true,
        ]);

        return $product;
    }

    /*
    |--------------------------------------------------------------------------
    | TASK 1: KEYWORD SEARCH EMPIRICAL CHALLENGES
    |--------------------------------------------------------------------------
    */

    public function test_challenge_search_partial_match_on_title_and_case_insensitivity()
    {
        $target = $this->makeProduct([
            'name' => 'Royal Dokra Terracotta Table Lamp',
        ]);
        $other = $this->makeProduct([
            'name' => 'Kashmiri Handloom Wool Shawl',
        ]);

        // Partial match with exact case
        $resp1 = $this->get('/products?search=Dokra');
        $resp1->assertStatus(200);
        $resp1->assertSee('Royal Dokra Terracotta Table Lamp');
        $resp1->assertDontSee('Kashmiri Handloom Wool Shawl');

        // Middle substring match
        $resp2 = $this->get('/products?search=cotta');
        $resp2->assertStatus(200);
        $resp2->assertSee('Royal Dokra Terracotta Table Lamp');
        $resp2->assertDontSee('Kashmiri Handloom Wool Shawl');

        // Case insensitivity: lowercase
        $resp3 = $this->get('/products?search=dokra');
        $resp3->assertStatus(200);
        $resp3->assertSee('Royal Dokra Terracotta Table Lamp');

        // Case insensitivity: UPPERCASE
        $resp4 = $this->get('/products?search=TABLE LAMP');
        $resp4->assertStatus(200);
        $resp4->assertSee('Royal Dokra Terracotta Table Lamp');
    }

    public function test_challenge_search_matches_description_and_short_description()
    {
        $prod1 = $this->makeProduct([
            'name'              => 'Earthen Cooking Handi',
            'short_description' => 'Pure unglazed porous clay pot.',
            'description'       => 'Retains natural micro-nutrients when heated.',
        ]);

        $prod2 = $this->makeProduct([
            'name'              => 'Bamboo Desk Organizer',
            'short_description' => 'Eco desk accessory.',
            'description'       => 'Handcrafted using seasoned golden bamboo strips.',
        ]);

        // Search term in short_description only
        $respDesc1 = $this->get('/products?search=unglazed');
        $respDesc1->assertStatus(200);
        $respDesc1->assertSee('Earthen Cooking Handi');
        $respDesc1->assertDontSee('Bamboo Desk Organizer');

        // Search term in full description only
        $respDesc2 = $this->get('/products?search=micro-nutrients');
        $respDesc2->assertStatus(200);
        $respDesc2->assertSee('Earthen Cooking Handi');
        $respDesc2->assertDontSee('Bamboo Desk Organizer');
    }

    public function test_challenge_search_matches_category_name_and_seller_shop_name()
    {
        $category = $this->makeCategory(['name' => 'Varanasi Weaves']);
        $seller = $this->makeSeller([], ['shop_name' => 'Gangetic Silk Emporium']);

        $product = $this->makeProduct([
            'category_id' => $category->id,
            'seller_id'   => $seller->id,
            'name'        => 'Crimson Zari Brocade',
        ]);

        // Search matching category name
        $respCat = $this->get('/products?search=Varanasi');
        $respCat->assertStatus(200);
        $respCat->assertSee('Crimson Zari Brocade');

        // Search matching seller shop name
        $respSeller = $this->get('/products?search=Gangetic');
        $respSeller->assertStatus(200);
        $respSeller->assertSee('Crimson Zari Brocade');
    }

    public function test_challenge_search_with_special_characters_and_punctuation()
    {
        $p1 = $this->makeProduct(['name' => 'Art & Soul Copper Pitcher (1.5L)']);
        $p2 = $this->makeProduct(['name' => '100% Cotton Hand-Stitched Quilt']);

        // Search with ampersand
        $respAmp = $this->get('/products?search=' . urlencode('Art & Soul'));
        $respAmp->assertStatus(200);
        $respAmp->assertSee('Art & Soul Copper Pitcher (1.5L)');

        // Search with parentheses
        $respParen = $this->get('/products?search=' . urlencode('(1.5L)'));
        $respParen->assertStatus(200);
        $respParen->assertSee('Art & Soul Copper Pitcher (1.5L)');

        // Search with hyphen
        $respHyphen = $this->get('/products?search=' . urlencode('Hand-Stitched'));
        $respHyphen->assertStatus(200);
        $respHyphen->assertSee('100% Cotton Hand-Stitched Quilt');

        // Search with percent symbol (SQL wildcard)
        $respPercent = $this->get('/products?search=' . urlencode('100%'));
        $respPercent->assertStatus(200);
        $respPercent->assertSee('100% Cotton Hand-Stitched Quilt');
    }

    public function test_challenge_search_with_sql_metacharacters_and_injection_payloads()
    {
        $this->makeProduct(['name' => 'Genuine Leather Satchel']);

        $maliciousPayloads = [
            "' OR '1'='1",
            "' OR 1=1 --",
            "\" OR \"1\"=\"1",
            "1; DROP TABLE products; --",
            "' UNION SELECT id, name, email, password, role FROM users --",
            "admin' --",
            "\\'; EXEC xp_cmdshell('dir'); --",
            "<script>alert('XSS')</script>",
            "'; WAITFOR DELAY '0:0:5'--",
            "1' AND SLEEP(5)--",
        ];

        foreach ($maliciousPayloads as $payload) {
            $response = $this->get('/products?search=' . urlencode($payload));
            // Must return 200 without throwing database exceptions or syntax errors
            $response->assertStatus(200);
            // Must not cause 500 error or expose table dumps
            $this->assertLessThan(500, $response->getStatusCode());
        }

        // Verify products table was not dropped or compromised
        $this->assertDatabaseHas('products', ['name' => 'Genuine Leather Satchel']);
    }

    public function test_challenge_search_zero_matches_displays_helpful_empty_state()
    {
        $this->makeProduct(['name' => 'Brass Pooja Thali']);

        $bogusSearch = 'NonExistentProductZyx987Token';
        $response = $this->get('/products?search=' . $bogusSearch);

        $response->assertStatus(200);
        $response->assertDontSee('Brass Pooja Thali');

        // Check empty state UX features in user/products/index.blade.php
        $response->assertSee('No products match your criteria');
        $response->assertSee('Clear All Filters');
        $response->assertSee($bogusSearch);
        $this->assertEquals(0, $response->viewData('products')->total());
        $response->assertSee('matching results for');
    }

    public function test_challenge_search_whitespace_only_returns_full_catalog()
    {
        $p1 = $this->makeProduct(['name' => 'First Active Product']);
        $p2 = $this->makeProduct(['name' => 'Second Active Product']);

        $response = $this->get('/products?search=' . urlencode('     '));
        $response->assertStatus(200);
        $response->assertSee('First Active Product');
        $response->assertSee('Second Active Product');
        $this->assertEquals(2, $response->viewData('products')->total());
    }

    /*
    |--------------------------------------------------------------------------
    | TASK 2: COMBINED MULTI-FACET FILTERS & SORTING EMPIRICAL CHALLENGES
    |--------------------------------------------------------------------------
    */

    public function test_challenge_combined_multi_facet_filtering_accuracy()
    {
        $catTextiles = $this->makeCategory(['name' => 'Textiles', 'slug' => 'textiles']);
        $catPottery = $this->makeCategory(['name' => 'Pottery', 'slug' => 'pottery']);

        // Product A: Matches ALL criteria (Cat: textiles, Price: 400, Rating: 4.8)
        $prodA = $this->makeProduct([
            'category_id'    => $catTextiles->id,
            'name'           => 'Indigo Handblock Cotton Scarf',
            'price'          => 400.00,
            'average_rating' => 4.80,
        ]);

        // Product B: In Textiles, but price too low (100 < min_price 200)
        $prodB = $this->makeProduct([
            'category_id'    => $catTextiles->id,
            'name'           => 'Mini Cotton Napkin',
            'price'          => 100.00,
            'average_rating' => 4.90,
        ]);

        // Product C: In Textiles, price in range, but rating too low (3.2 < min_rating 4.0)
        $prodC = $this->makeProduct([
            'category_id'    => $catTextiles->id,
            'name'           => 'Rough Loom Fabric',
            'price'          => 350.00,
            'average_rating' => 3.20,
        ]);

        // Product D: In Textiles, rating high, but price too high (1200 > max_price 800)
        $prodD = $this->makeProduct([
            'category_id'    => $catTextiles->id,
            'name'           => 'Royal Embroidered Pashmina',
            'price'          => 1200.00,
            'average_rating' => 4.90,
        ]);

        // Product E: Wrong Category (Pottery), but matches price and rating
        $prodE = $this->makeProduct([
            'category_id'    => $catPottery->id,
            'name'           => 'Clay Water Bottle',
            'price'          => 450.00,
            'average_rating' => 4.80,
        ]);

        // Request with combined facets
        $url = '/products?category=textiles&min_price=200&max_price=800&min_rating=4';
        $response = $this->get($url);

        $response->assertStatus(200);

        $products = $response->viewData('products');
        $this->assertEquals(1, $products->total(), 'Only exactly 1 product should match all 4 facets');
        $this->assertEquals($prodA->id, $products->first()->id);

        $response->assertSee('Indigo Handblock Cotton Scarf');
        $response->assertDontSee('Mini Cotton Napkin');
        $response->assertDontSee('Rough Loom Fabric');
        $response->assertDontSee('Royal Embroidered Pashmina');
        $response->assertDontSee('Clay Water Bottle');
    }

    public function test_challenge_sorting_price_low_to_high()
    {
        $cat = $this->makeCategory(['slug' => 'crafts']);
        $p1 = $this->makeProduct(['category_id' => $cat->id, 'name' => 'Budget Coaster', 'price' => 99.00]);
        $p2 = $this->makeProduct(['category_id' => $cat->id, 'name' => 'Mid Iron Lamp', 'price' => 499.00]);
        $p3 = $this->makeProduct(['category_id' => $cat->id, 'name' => 'Luxury Brass Chandelier', 'price' => 2999.00]);

        $response = $this->get('/products?category=crafts&sort=price_low');
        $response->assertStatus(200);

        $items = $response->viewData('products');
        $this->assertCount(3, $items);
        $this->assertEquals($p1->id, $items[0]->id, 'First item must be lowest price');
        $this->assertEquals($p2->id, $items[1]->id, 'Second item must be mid price');
        $this->assertEquals($p3->id, $items[2]->id, 'Third item must be highest price');
    }

    public function test_challenge_sorting_price_high_to_low()
    {
        $cat = $this->makeCategory(['slug' => 'crafts']);
        $p1 = $this->makeProduct(['category_id' => $cat->id, 'name' => 'Budget Coaster', 'price' => 99.00]);
        $p2 = $this->makeProduct(['category_id' => $cat->id, 'name' => 'Mid Iron Lamp', 'price' => 499.00]);
        $p3 = $this->makeProduct(['category_id' => $cat->id, 'name' => 'Luxury Brass Chandelier', 'price' => 2999.00]);

        $response = $this->get('/products?category=crafts&sort=price_high');
        $response->assertStatus(200);

        $items = $response->viewData('products');
        $this->assertCount(3, $items);
        $this->assertEquals($p3->id, $items[0]->id, 'First item must be highest price');
        $this->assertEquals($p2->id, $items[1]->id, 'Second item must be mid price');
        $this->assertEquals($p1->id, $items[2]->id, 'Third item must be lowest price');
    }

    public function test_challenge_sorting_by_rating_highest()
    {
        $pLow = $this->makeProduct(['name' => 'Fair Item', 'average_rating' => 3.40]);
        $pMid = $this->makeProduct(['name' => 'Good Item', 'average_rating' => 4.20]);
        $pHigh = $this->makeProduct(['name' => 'Excellent Item', 'average_rating' => 4.95]);

        $response = $this->get('/products?sort=rating');
        $response->assertStatus(200);

        $items = $response->viewData('products');
        $this->assertEquals($pHigh->id, $items[0]->id, 'Highest rated item must come first');
        $this->assertEquals($pMid->id, $items[1]->id, 'Mid rated item must come second');
        $this->assertEquals($pLow->id, $items[2]->id, 'Lowest rated item must come third');
    }

    public function test_challenge_sorting_by_newest()
    {
        $pOld = $this->makeProduct(['name' => 'Old Product', 'created_at' => now()->subDays(10)]);
        $pNew = $this->makeProduct(['name' => 'Brand New Product', 'created_at' => now()]);

        $response = $this->get('/products?sort=newest');
        $response->assertStatus(200);

        $items = $response->viewData('products');
        $this->assertEquals($pNew->id, $items[0]->id, 'Newest item must come first');
        $this->assertEquals($pOld->id, $items[1]->id, 'Oldest item must come second');
    }

    public function test_challenge_combined_search_filter_and_sort_pipeline()
    {
        $cat = $this->makeCategory(['slug' => 'wooden-toys']);

        $p1 = $this->makeProduct([
            'category_id'    => $cat->id,
            'name'           => 'Wooden Toy Train Set',
            'price'          => 350.00,
            'average_rating' => 4.60,
        ]);
        $p2 = $this->makeProduct([
            'category_id'    => $cat->id,
            'name'           => 'Wooden Rocking Horse Toy',
            'price'          => 850.00,
            'average_rating' => 4.90,
        ]);
        $p3 = $this->makeProduct([
            'category_id'    => $cat->id,
            'name'           => 'Wooden Toy Building Blocks',
            'price'          => 600.00,
            'average_rating' => 4.70,
        ]);

        // Search "Toy", category "wooden-toys", price range 300 - 900, rating >= 4.5, sorted by price_high
        $url = '/products?search=Toy&category=wooden-toys&min_price=300&max_price=900&min_rating=4&sort=price_high';
        $response = $this->get($url);

        $response->assertStatus(200);
        $items = $response->viewData('products');
        $this->assertEquals(3, $items->total());

        // Order check: 850 (Horse) -> 600 (Blocks) -> 350 (Train)
        $this->assertEquals($p2->id, $items[0]->id);
        $this->assertEquals($p3->id, $items[1]->id);
        $this->assertEquals($p1->id, $items[2]->id);
    }

    public function test_challenge_conflicting_price_boundaries_handles_zero_state()
    {
        $this->makeProduct(['price' => 500.00]);

        // min_price 1000 > max_price 200 (impossible range)
        $response = $this->get('/products?min_price=1000&max_price=200');

        $response->assertStatus(200);
        $this->assertEquals(0, $response->viewData('products')->total());
        $response->assertSee('No products match your criteria');
    }

    public function test_challenge_invalid_filter_types_and_extremes_do_not_crash()
    {
        $this->makeProduct(['name' => 'Normal Safe Product', 'price' => 200.00]);

        $extremeUrls = [
            '/products?min_price=-500&max_price=999999999999999',
            '/products?min_price=invalid_text&max_price=another_string',
            '/products?min_rating=not_a_number',
            '/products?min_rating=999999',
            '/products?radius=-100',
            '/products?radius=99999999',
            '/products?sort=unsupported_random_sort_key',
            '/products?category=phantom-category-slug-that-does-not-exist',
        ];

        foreach ($extremeUrls as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
            $this->assertLessThan(500, $response->getStatusCode());
        }
    }

    /**
     * Adversarial Challenge: Query Parameter Array Type Handling
     *
     * Validates that passing array query parameters (e.g. category[]=textiles)
     * is safely handled and does not cause a fatal 500 ViewException.
     */
    public function test_challenge_adversarial_array_parameter_behavior_documented()
    {
        $this->makeProduct(['name' => 'Array Test Product', 'price' => 250.00]);

        $response = $this->get('/products?category[]=textiles');
        $this->assertEquals(200, $response->getStatusCode(), 'Confirms 200 OK and no 500 ViewException on array query parameter');
    }

    public function test_challenge_array_query_parameters_do_not_cause_fatal_500()
    {
        $this->makeProduct(['name' => 'Array Test Product 2', 'price' => 250.00]);

        $response = $this->get('/products?category[]=textiles');
        $response->assertStatus(200);
    }

    public function test_challenge_pagination_preserves_multi_facet_query_strings()
    {
        $cat = $this->makeCategory(['slug' => 'stationery']);

        // Create 15 products to trigger pagination (12 per page)
        for ($i = 1; $i <= 15; $i++) {
            $this->makeProduct([
                'category_id' => $cat->id,
                'name'        => sprintf('Handmade Journal Volume %02d', $i),
                'price'       => 100.00 + $i * 10,
            ]);
        }

        $response = $this->get('/products?category=stationery&sort=price_low&page=1');
        $response->assertStatus(200);

        $products = $response->viewData('products');
        $this->assertEquals(15, $products->total());
        $this->assertCount(12, $products);
        $this->assertTrue($products->hasMorePages());

        // Page 2 request
        $respPage2 = $this->get('/products?category=stationery&sort=price_low&page=2');
        $respPage2->assertStatus(200);
        $p2Items = $respPage2->viewData('products');
        $this->assertCount(3, $p2Items);
        // Verify sorting carried through across pages
        $this->assertGreaterThan($products->last()->price, $p2Items->first()->price);
    }

    /*
    |--------------------------------------------------------------------------
    | TASK 3: CATEGORY DYNAMIC VIEW (/category/{slug}) CHALLENGES
    |--------------------------------------------------------------------------
    */

    public function test_challenge_category_dynamic_view_renders_database_products_and_isolates()
    {
        $catCeramics = $this->makeCategory([
            'name'        => 'Glazed Ceramics',
            'slug'        => 'glazed-ceramics',
            'description' => 'Fine glazed ceramics produced by studio potters.',
        ]);

        $catJewelry = $this->makeCategory([
            'name'        => 'Tribal Silver Jewelry',
            'slug'        => 'tribal-silver-jewelry',
            'description' => 'Intricate tribal silver ornaments.',
        ]);

        $prodCeramic = $this->makeProduct([
            'category_id' => $catCeramics->id,
            'name'        => 'Glazed Ceramic Teapot 800ml',
            'price'       => 850.00,
        ]);

        $prodJewelry = $this->makeProduct([
            'category_id' => $catJewelry->id,
            'name'        => 'Handcrafted Hasli Choker',
            'price'       => 4500.00,
        ]);

        // 1. Visit Ceramics Category
        $respCeramics = $this->get('/category/glazed-ceramics');
        $respCeramics->assertStatus(200);
        $respCeramics->assertSee('Glazed Ceramics');
        $respCeramics->assertSee('Glazed Ceramic Teapot 800ml');
        $respCeramics->assertSee('₹850.00');
        $respCeramics->assertDontSee('Handcrafted Hasli Choker');

        // 2. Visit Jewelry Category
        $respJewelry = $this->get('/category/tribal-silver-jewelry');
        $respJewelry->assertStatus(200);
        $respJewelry->assertSee('Tribal Silver Jewelry');
        $respJewelry->assertSee('Handcrafted Hasli Choker');
        $respJewelry->assertSee('₹4,500.00');
        $respJewelry->assertDontSee('Glazed Ceramic Teapot 800ml');
    }

    public function test_challenge_category_all_slug_renders_all_products()
    {
        $p1 = $this->makeProduct(['name' => 'Universal Item Alpha']);
        $p2 = $this->makeProduct(['name' => 'Universal Item Beta']);

        $response = $this->get('/category/all');
        $response->assertStatus(200);
        $response->assertSee('Universal Item Alpha');
        $response->assertSee('Universal Item Beta');
        $this->assertEquals(2, $response->viewData('products')->total());
    }

    public function test_challenge_category_search_and_sorting_features()
    {
        $cat = $this->makeCategory(['slug' => 'spices']);

        $p1 = $this->makeProduct(['category_id' => $cat->id, 'name' => 'Organic Black Pepper', 'price' => 180.00]);
        $p2 = $this->makeProduct(['category_id' => $cat->id, 'name' => 'Organic Green Cardamom', 'price' => 950.00]);
        $p3 = $this->makeProduct(['category_id' => $cat->id, 'name' => 'Organic Clove Whole', 'price' => 450.00]);

        // Search inside category
        $respSearch = $this->get('/category/spices?search=Cardamom');
        $respSearch->assertStatus(200);
        $respSearch->assertSee('Organic Green Cardamom');
        $respSearch->assertDontSee('Organic Black Pepper');
        $respSearch->assertDontSee('Organic Clove Whole');

        // Sort inside category: price_low
        $respSort = $this->get('/category/spices?sort=price_low');
        $respSort->assertStatus(200);
        $items = $respSort->viewData('products');
        $this->assertEquals($p1->id, $items[0]->id, 'Cheapest spice must come first');
        $this->assertEquals($p3->id, $items[1]->id, 'Mid spice must come second');
        $this->assertEquals($p2->id, $items[2]->id, 'Expensive spice must come last');
    }

    public function test_challenge_category_empty_renders_helpful_empty_state()
    {
        $emptyCat = $this->makeCategory([
            'name' => 'Empty Pristine Taxonomy',
            'slug' => 'empty-pristine-taxonomy',
        ]);

        $response = $this->get('/category/empty-pristine-taxonomy');
        $response->assertStatus(200);
        $response->assertSee('No products found in this category');
        $response->assertSee('Browse All Products');
    }

    public function test_challenge_category_non_existent_slug_handled_gracefully()
    {
        $response = $this->get('/category/totally-unknown-slug-xyz');
        $response->assertStatus(200);
        $response->assertSee('No products found in this category');
        $this->assertEquals(0, $response->viewData('products')->total());
    }

    public function test_challenge_inactive_products_excluded_from_category_view()
    {
        $cat = $this->makeCategory(['slug' => 'antiques']);

        $activeProd = $this->makeProduct([
            'category_id' => $cat->id,
            'name'        => 'Public Restored Antique Clock',
            'status'      => 'active',
        ]);

        $inactiveProd = $this->makeProduct([
            'category_id' => $cat->id,
            'name'        => 'Withdrawn Unlisted Antique Watch',
            'status'      => 'inactive',
        ]);

        $response = $this->get('/category/antiques');
        $response->assertStatus(200);
        $response->assertSee('Public Restored Antique Clock');
        $response->assertDontSee('Withdrawn Unlisted Antique Watch');
    }
}
