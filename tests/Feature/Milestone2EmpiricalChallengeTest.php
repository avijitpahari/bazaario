<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class Milestone2EmpiricalChallengeTest extends TestCase
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

    protected function createCategory(array $attributes = []): Category
    {
        return Category::create(array_merge([
            'name'        => 'Textiles ' . uniqid(),
            'slug'        => 'textiles-' . uniqid(),
            'description' => 'Textiles and garments',
            'status'      => 'active',
        ], $attributes));
    }

    protected function createSellerWithCoords(string $city, float $lat, float $lng, string $shopName = null): User
    {
        $seller = User::create([
            'name'               => 'Seller ' . uniqid(),
            'email'              => 'seller_' . uniqid() . '@example.com',
            'password'           => Hash::make('Secret123!'),
            'role'               => 'seller',
            'status'             => 'active',
            'preferred_language' => 'en',
            'email_verified_at'  => now(),
        ]);

        SellerProfile::create([
            'user_id'         => $seller->id,
            'shop_name'       => $shopName ?? ($city . ' Stall ' . uniqid()),
            'shop_slug'       => Str::slug(($shopName ?? $city) . '-' . uniqid()),
            'status'          => 'approved',
            'commission_rate' => 10.00,
            'trust_score'     => 95.00,
            'city'            => $city,
            'state'           => 'West Bengal',
            'country'         => 'India',
            'latitude'        => $lat,
            'longitude'       => $lng,
        ]);

        return $seller;
    }

    protected function createProductForSeller(User $seller, Category $category, array $attributes = []): Product
    {
        return Product::create(array_merge([
            'seller_id'         => $seller->id,
            'category_id'       => $category->id,
            'name'              => 'Product ' . uniqid(),
            'slug'              => 'product-' . uniqid(),
            'description'       => 'High quality authentic product',
            'price'             => 500.00,
            'stock'             => 50,
            'status'            => 'active',
            'average_rating'    => 4.5,
            'total_reviews'     => 10,
        ], $attributes));
    }

    /*
    |--------------------------------------------------------------------------
    | TASK 1: Distance Calculation & Proximity Filtering
    |--------------------------------------------------------------------------
    */

    /**
     * Challenge 1.1: Mathematical precision of distanceTo() against spherical Haversine
     */
    public function test_empirical_distance_to_accuracy_against_known_coordinates()
    {
        // Reference coordinates:
        // Contai: 21.778124, 87.751624
        // Digha: 21.626600, 87.507400
        // Kolkata: 22.572646, 88.363895
        // Bengaluru: 12.971599, 77.594566
        
        $seller = $this->createSellerWithCoords('Contai', 21.778124, 87.751624);
        $profile = $seller->sellerProfile;

        // 1. Distance to self must be 0.0
        $this->assertEquals(0.0, $profile->distanceTo(21.778124, 87.751624));

        // 2. Distance from Contai to Digha: expected ~30.3 km
        $distToDigha = $profile->distanceTo(21.626600, 87.507400);
        $this->assertGreaterThan(28.0, $distToDigha);
        $this->assertLessThan(33.0, $distToDigha);
        $this->assertEquals(30.3, round($distToDigha, 1));

        // 3. Distance from Contai to Kolkata: exact Haversine is 108.5 km
        $distToKolkata = $profile->distanceTo(22.572646, 88.363895);
        $this->assertGreaterThan(105.0, $distToKolkata);
        $this->assertLessThan(110.0, $distToKolkata);
        $this->assertEquals(108.5, round($distToKolkata, 1));

        // 4. Distance from Contai to Bengaluru: expected ~1440 - 1500 km
        $distToBengaluru = $profile->distanceTo(12.971599, 77.594566);
        $this->assertGreaterThan(1400.0, $distToBengaluru);
        $this->assertLessThan(1550.0, $distToBengaluru);

        // 5. Null coordinates safety
        $this->assertEquals(0.0, $profile->distanceTo(null, null));
        $this->assertEquals(0.0, $profile->distanceTo(22.5, null));
        $this->assertEquals(0.0, $profile->distanceTo(null, 88.3));
    }

    /**
     * Challenge 1.2: Nearby Stalls filtering under multiple radii (15km, 50km, 100km, statewide/500km)
     */
    public function test_nearby_stalls_filtering_across_multiple_radii()
    {
        // Origin: Contai (21.778124, 87.751624)
        $originLat = 21.778124;
        $originLng = 87.751624;

        // Stall 1: Contai (~0.0 km)
        $stallContai = $this->createSellerWithCoords('Contai', 21.778124, 87.751624, 'Contai Handicrafts');
        
        // Stall 2: Digha (~30.3 km)
        $stallDigha = $this->createSellerWithCoords('Digha', 21.626600, 87.507400, 'Digha Sea Shells');

        // Stall 3: Kolkata (~108.5 km)
        $stallKolkata = $this->createSellerWithCoords('Kolkata', 22.572646, 88.363895, 'Kolkata Sweet Hub');

        // Stall 4: Bengaluru (~1450 km)
        $stallBengaluru = $this->createSellerWithCoords('Bengaluru', 12.971599, 77.594566, 'Bengaluru Coffee Works');

        $extractNearbyHtml = function ($html) {
            if (preg_match('/<!-- 5\.6 NEARBY STALLS & HYPERLOCAL DISCOVERY -->.*?<section[^>]*id="nearby-stalls"[^>]*>(.*?)<\/section>/s', $html, $m)) {
                return $m[1];
            }
            return $html;
        };

        // ── Radius 15 km ──
        $res15 = $this->get("/?lat={$originLat}&lng={$originLng}&radius=15");
        $res15->assertStatus(200);
        $stalls15 = $res15->viewData('nearbyStalls');
        $this->assertCount(1, $stalls15);
        $this->assertEquals($stallContai->id, $stalls15->first()->id);
        $this->assertEquals(0.0, $stalls15->first()->distance_km);

        $nearbyHtml15 = $extractNearbyHtml($res15->getContent());
        $this->assertStringContainsString('Contai Handicrafts', $nearbyHtml15);
        $this->assertStringNotContainsString('Digha Sea Shells', $nearbyHtml15);
        $this->assertStringNotContainsString('Kolkata Sweet Hub', $nearbyHtml15);
        $this->assertStringNotContainsString('Bengaluru Coffee Works', $nearbyHtml15);

        // ── Radius 50 km ──
        $res50 = $this->get("/?lat={$originLat}&lng={$originLng}&radius=50");
        $res50->assertStatus(200);
        $stalls50 = $res50->viewData('nearbyStalls');
        $this->assertCount(2, $stalls50);
        $stalls50Ids = $stalls50->pluck('id')->all();
        $this->assertContains($stallContai->id, $stalls50Ids);
        $this->assertContains($stallDigha->id, $stalls50Ids);
        $this->assertNotContains($stallKolkata->id, $stalls50Ids);
        $this->assertNotContains($stallBengaluru->id, $stalls50Ids);

        $nearbyHtml50 = $extractNearbyHtml($res50->getContent());
        $this->assertStringContainsString('Contai Handicrafts', $nearbyHtml50);
        $this->assertStringContainsString('Digha Sea Shells', $nearbyHtml50);
        $this->assertStringNotContainsString('Kolkata Sweet Hub', $nearbyHtml50);

        // ── Radius 100 km ──
        $res100 = $this->get("/?lat={$originLat}&lng={$originLng}&radius=100");
        $res100->assertStatus(200);
        $stalls100 = $res100->viewData('nearbyStalls');
        $this->assertCount(2, $stalls100);
        $stalls100Ids = $stalls100->pluck('id')->all();
        $this->assertContains($stallContai->id, $stalls100Ids);
        $this->assertContains($stallDigha->id, $stalls100Ids);
        $this->assertNotContains($stallKolkata->id, $stalls100Ids);

        // ── Radius 500 km (Statewide) ──
        $res500 = $this->get("/?lat={$originLat}&lng={$originLng}&radius=500");
        $res500->assertStatus(200);
        $stalls500 = $res500->viewData('nearbyStalls');
        $this->assertCount(3, $stalls500);
        $stalls500Ids = $stalls500->pluck('id')->all();
        $this->assertContains($stallContai->id, $stalls500Ids);
        $this->assertContains($stallDigha->id, $stalls500Ids);
        $this->assertContains($stallKolkata->id, $stalls500Ids);
        $this->assertNotContains($stallBengaluru->id, $stalls500Ids);
    }

    /**
     * Challenge 1.3: Empirical verification of zero-match radius behavior
     */
    public function test_nearby_stalls_zero_match_behavior_when_no_sellers_within_radius()
    {
        // Only 1 seller exists in Bengaluru (lat: 12.971599, lng: 77.594566)
        $this->createSellerWithCoords('Bengaluru', 12.971599, 77.594566, 'Bengaluru Tech Silk');

        // User queries from Kolkata with radius 15 km
        $res = $this->get('/?lat=22.572646&lng=88.363895&radius=15');
        $res->assertStatus(200);

        // Controller returns empty collection when no stalls match radius
        $nearbyStalls = $res->viewData('nearbyStalls');
        $this->assertTrue($nearbyStalls->isEmpty());
    }

    /**
     * Challenge 1.4: Catalog proximity filter on /products?radius=...
     */
    public function test_catalog_proximity_filtering_on_products_index()
    {
        $category = $this->createCategory(['name' => 'Artisanal Gifts', 'slug' => 'artisanal-gifts']);

        // Seller 1: Contai
        $sContai = $this->createSellerWithCoords('Contai', 21.778124, 87.751624);
        $pContai = $this->createProductForSeller($sContai, $category, ['name' => 'Contai Mat Handwoven']);

        // Seller 2: Digha (~30.3 km)
        $sDigha = $this->createSellerWithCoords('Digha', 21.626600, 87.507400);
        $pDigha = $this->createProductForSeller($sDigha, $category, ['name' => 'Digha Conch Ornament']);

        // Seller 3: Kolkata (~108.5 km)
        $sKolkata = $this->createSellerWithCoords('Kolkata', 22.572646, 88.363895);
        $pKolkata = $this->createProductForSeller($sKolkata, $category, ['name' => 'Kolkata Terracotta']);

        // Query /products centered at Contai with radius=15 km
        $res15 = $this->get('/products?lat=21.778124&lng=87.751624&radius=15');
        $res15->assertStatus(200);
        $prods15 = $res15->viewData('products');
        $p15Ids = collect($prods15->items())->pluck('id')->all();
        $this->assertContains($pContai->id, $p15Ids);
        $this->assertNotContains($pDigha->id, $p15Ids);
        $this->assertNotContains($pKolkata->id, $p15Ids);

        // Query /products centered at Contai with radius=50 km
        $res50 = $this->get('/products?lat=21.778124&lng=87.751624&radius=50');
        $res50->assertStatus(200);
        $prods50 = $res50->viewData('products');
        $p50Ids = collect($prods50->items())->pluck('id')->all();
        $this->assertContains($pContai->id, $p50Ids);
        $this->assertContains($pDigha->id, $p50Ids);
        $this->assertNotContains($pKolkata->id, $p50Ids);
    }

    /**
     * Challenge 1.5: Antipodal and extreme geographic coordinates stress test
     */
    public function test_distance_to_extreme_and_antipodal_coordinates()
    {
        $seller = $this->createSellerWithCoords('Equator', 0.0, 0.0);
        $profile = $seller->sellerProfile;

        // North pole
        $distNorthPole = $profile->distanceTo(90.0, 0.0);
        $this->assertGreaterThan(9900.0, $distNorthPole);
        $this->assertLessThan(10100.0, $distNorthPole);

        // Exact antipodal point: (0, 180) -> half earth circumference ~20015 km
        $distAntipode = $profile->distanceTo(0.0, 180.0);
        $this->assertFalse(is_nan($distAntipode));
        $this->assertGreaterThan(20000.0, $distAntipode);
        $this->assertLessThan(20050.0, $distAntipode);
    }

    /*
    |--------------------------------------------------------------------------
    | TASK 2: Server-Side Pagination & Query Preservation
    |--------------------------------------------------------------------------
    */

    /**
     * Challenge 2.1: Server-side pagination across pages 1, 2, 3, boundary, and out-of-range
     */
    public function test_server_side_pagination_pages_and_boundaries()
    {
        $category = $this->createCategory(['name' => 'Ceramics', 'slug' => 'ceramics']);
        $seller = $this->createSellerWithCoords('Kolkata', 22.572646, 88.363895);

        // Create exactly 25 products (12 per page -> 3 pages: 12, 12, 1)
        for ($i = 1; $i <= 25; $i++) {
            $this->createProductForSeller($seller, $category, [
                'name'  => sprintf('Ceramic Mug #%02d', $i),
                'price' => 100.00 + $i,
            ]);
        }

        // --- Page 1 ---
        $resPage1 = $this->get('/products?page=1');
        $resPage1->assertStatus(200);
        $productsPage1 = $resPage1->viewData('products');
        $this->assertEquals(1, $productsPage1->currentPage());
        $this->assertEquals(25, $productsPage1->total());
        $this->assertEquals(12, $productsPage1->perPage());
        $this->assertCount(12, $productsPage1->items());
        $this->assertTrue($productsPage1->hasMorePages());

        // --- Page 2 ---
        $resPage2 = $this->get('/products?page=2');
        $resPage2->assertStatus(200);
        $productsPage2 = $resPage2->viewData('products');
        $this->assertEquals(2, $productsPage2->currentPage());
        $this->assertCount(12, $productsPage2->items());
        $this->assertTrue($productsPage2->hasMorePages());

        // Ensure Page 1 and Page 2 contain distinct products
        $p1Ids = collect($productsPage1->items())->pluck('id');
        $p2Ids = collect($productsPage2->items())->pluck('id');
        $this->assertEmpty($p1Ids->intersect($p2Ids));

        // --- Page 3 (Last page with remainder) ---
        $resPage3 = $this->get('/products?page=3');
        $resPage3->assertStatus(200);
        $productsPage3 = $resPage3->viewData('products');
        $this->assertEquals(3, $productsPage3->currentPage());
        $this->assertCount(1, $productsPage3->items());
        $this->assertFalse($productsPage3->hasMorePages());

        // --- Page 999 (Out-of-range boundary) ---
        $resPage999 = $this->get('/products?page=999');
        $resPage999->assertStatus(200);
        $productsPage999 = $resPage999->viewData('products');
        $this->assertEquals(999, $productsPage999->currentPage());
        $this->assertCount(0, $productsPage999->items());
        $this->assertFalse($productsPage999->hasMorePages());
        $resPage999->assertSee('No products match your criteria');

        // --- Invalid page parameter (?page=invalid or ?page=-1) ---
        $resInvalid = $this->get('/products?page=invalid');
        $resInvalid->assertStatus(200);
        $this->assertEquals(1, $resInvalid->viewData('products')->currentPage());

        $resNegative = $this->get('/products?page=-5');
        $resNegative->assertStatus(200);
        $this->assertEquals(1, $resNegative->viewData('products')->currentPage());
    }

    /**
     * Challenge 2.2: Preservation of multi-facet query parameters across pagination links
     */
    public function test_query_parameters_preserved_across_pagination_links()
    {
        $catTarget = $this->createCategory(['name' => 'Special Fabrics', 'slug' => 'special-fabrics']);
        $catOther = $this->createCategory(['name' => 'Metalworks', 'slug' => 'metalworks']);
        $seller = $this->createSellerWithCoords('Kolkata', 22.572646, 88.363895);

        // Create 20 matching products:
        for ($i = 1; $i <= 20; $i++) {
            $this->createProductForSeller($seller, $catTarget, [
                'name'  => sprintf('OrganicCotton Shirt #%02d', $i),
                'price' => 250.00,
            ]);
        }

        // Create 10 non-matching products
        for ($i = 1; $i <= 10; $i++) {
            $this->createProductForSeller($seller, $catOther, [
                'name'  => sprintf('Steel Item #%02d', $i),
                'price' => 800.00,
            ]);
        }

        // Request Page 1 with query params: ?q=OrganicCotton&category=special-fabrics&min_price=100&max_price=400
        $url = '/products?' . http_build_query([
            'q'         => 'OrganicCotton',
            'category'  => 'special-fabrics',
            'min_price' => 100,
            'max_price' => 400,
            'page'      => 1,
        ]);

        $response = $this->get($url);
        $response->assertStatus(200);

        $paginator = $response->viewData('products');
        $this->assertEquals(20, $paginator->total());
        $this->assertEquals(12, $paginator->count());
        $this->assertEquals(2, $paginator->lastPage());

        // Inspect the generated pagination links HTML
        $renderedHtml = (string)$paginator->links();

        // 1. Verify next page URL contains page=2
        $this->assertStringContainsString('page=2', $renderedHtml);

        // 2. Verify all query parameters are preserved in the pagination link
        $this->assertStringContainsString('q=OrganicCotton', $renderedHtml);
        $this->assertStringContainsString('category=special-fabrics', $renderedHtml);
        $this->assertStringContainsString('min_price=100', $renderedHtml);
        $this->assertStringContainsString('max_price=400', $renderedHtml);

        // 3. Now simulate following the Page 2 link
        $urlPage2 = '/products?' . http_build_query([
            'q'         => 'OrganicCotton',
            'category'  => 'special-fabrics',
            'min_price' => 100,
            'max_price' => 400,
            'page'      => 2,
        ]);

        $resPage2 = $this->get($urlPage2);
        $resPage2->assertStatus(200);
        $p2Products = $resPage2->viewData('products');

        $this->assertEquals(2, $p2Products->currentPage());
        $this->assertEquals(8, $p2Products->count()); // Remaining 8 of 20
        $this->assertEquals(20, $p2Products->total());

        // Ensure all items on Page 2 still match the query criteria
        foreach ($p2Products->items() as $item) {
            $this->assertStringContainsString('OrganicCotton', $item->name);
            $this->assertEquals($catTarget->id, $item->category_id);
            $this->assertGreaterThanOrEqual(100, $item->price);
            $this->assertLessThanOrEqual(400, $item->price);
        }
    }

    /**
     * Challenge 2.3: Preservation when 'search' parameter is used instead of 'q'
     */
    public function test_query_preservation_with_search_parameter()
    {
        $cat = $this->createCategory(['name' => 'Handicrafts', 'slug' => 'handicrafts']);
        $seller = $this->createSellerWithCoords('Kolkata', 22.572646, 88.363895);

        for ($i = 1; $i <= 15; $i++) {
            $this->createProductForSeller($seller, $cat, [
                'name'  => sprintf('Terracotta Figurine #%02d', $i),
                'price' => 300.00,
            ]);
        }

        $url = '/products?' . http_build_query([
            'search'    => 'Terracotta',
            'sort'      => 'price_low',
            'min_rating'=> 4,
            'page'      => 1,
        ]);

        $response = $this->get($url);
        $response->assertStatus(200);

        $paginator = $response->viewData('products');
        $renderedHtml = (string)$paginator->links();

        $this->assertStringContainsString('search=Terracotta', $renderedHtml);
        $this->assertStringContainsString('sort=price_low', $renderedHtml);
        $this->assertStringContainsString('min_rating=4', $renderedHtml);
    }

    /**
     * Challenge 2.4: Category page pagination (/category/{slug}?page=...)
     */
    public function test_category_page_server_side_pagination_and_query_preservation()
    {
        $cat = $this->createCategory(['name' => 'Pottery Works', 'slug' => 'pottery-works']);
        $seller = $this->createSellerWithCoords('Kolkata', 22.572646, 88.363895);

        for ($i = 1; $i <= 16; $i++) {
            $this->createProductForSeller($seller, $cat, [
                'name'  => sprintf('Clay Pot #%02d', $i),
                'price' => 150.00 + $i,
            ]);
        }

        $url = '/category/pottery-works?' . http_build_query([
            'search' => 'Clay',
            'sort'   => 'price_low',
            'page'   => 1,
        ]);

        $response = $this->get($url);
        $response->assertStatus(200);

        $products = $response->viewData('products');
        $this->assertEquals(16, $products->total());
        $this->assertEquals(12, $products->count());
        $this->assertEquals(2, $products->lastPage());

        $linksHtml = (string)$products->links();
        $this->assertStringContainsString('page=2', $linksHtml);
        $this->assertStringContainsString('search=Clay', $linksHtml);
        $this->assertStringContainsString('sort=price_low', $linksHtml);
    }

    /**
     * Challenge 2.5: Complex URL encoded characters in search query preserved across pagination
     */
    public function test_complex_special_characters_preserved_across_pagination()
    {
        $cat = $this->createCategory();
        $seller = $this->createSellerWithCoords('Kolkata', 22.572646, 88.363895);

        for ($i = 1; $i <= 15; $i++) {
            $this->createProductForSeller($seller, $cat, [
                'name' => sprintf('100%% Pure & Natural Jute #%02d', $i),
            ]);
        }

        $response = $this->get('/products?search=' . urlencode('100% Pure & Natural') . '&page=1');
        $response->assertStatus(200);

        $products = $response->viewData('products');
        $this->assertEquals(15, $products->total());
        $this->assertEquals(2, $products->lastPage());

        $linksHtml = (string)$products->links();
        $this->assertStringContainsString('page=2', $linksHtml);
        // Laravel's paginator should preserve query string
        $this->assertStringContainsString('search=', $linksHtml);
    }
}
