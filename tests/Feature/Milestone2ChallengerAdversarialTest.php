<?php

namespace Tests\Feature;

use App\Models\Auction;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Milestone2ChallengerAdversarialTest extends TestCase
{
    use RefreshDatabase;

    protected function createSeller(string $shopName = 'Contai Agro Hub'): User
    {
        $seller = User::factory()->create([
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ]);

        SellerProfile::create([
            'user_id'             => $seller->id,
            'shop_name'           => $shopName,
            'shop_slug'           => \Illuminate\Support\Str::slug($shopName . '-' . $seller->id),
            'seller_type'         => 'Farmer',
            'city'                => 'Contai',
            'state'               => 'West Bengal',
            'country'             => 'India',
            'status'              => 'approved',
            'trust_score'         => 96.00,
            'commission_rate'     => 8.50,
            'address'             => 'Village Agro Center, Contai',
            'postal_code'         => '721401',
            'operating_radius_km' => 30,
            'latitude'            => 21.778124,
            'longitude'           => 87.751624,
            'bank_account_number' => '1234567890123456',
            'bank_ifsc'           => 'SBIN0000057',
        ]);

        return $seller->fresh(['sellerProfile']);
    }

    protected function createProduct(User $seller, Category $category, array $attributes = []): Product
    {
        return Product::create(array_merge([
            'seller_id'      => $seller->id,
            'category_id'    => $category->id,
            'name'           => 'Product ' . uniqid(),
            'slug'           => 'product-' . uniqid(),
            'description'    => 'Organic product from local farm',
            'price'          => 120.00,
            'stock'          => 40,
            'unit_type'      => 'kg',
            'status'         => 'active',
            'average_rating' => 4.8,
            'total_reviews'  => 12,
        ], $attributes));
    }

    /*
    |--------------------------------------------------------------------------
    | 1. LEGAL ROUTES EMPIRICAL & ADVERSARIAL CHALLENGE
    |--------------------------------------------------------------------------
    */

    /**
     * Challenge 1.1: GET /privacy returns HTTP 200 with valid, substantive policy content.
     */
    public function test_privacy_page_returns_200_with_substantive_content(): void
    {
        $response = $this->get('/privacy');
        $response->assertStatus(200);

        // Core identity & content assertions
        $response->assertSee('Privacy Policy');
        $response->assertSee('Information We Collect');
        $response->assertSee('Digital Personal Data Protection Act');
        $response->assertSee('Contai');
        $response->assertSee('Bazaario');

        // Named route verification
        $this->assertEquals(url('/privacy'), route('pages.privacy'));
    }

    /**
     * Challenge 1.2: GET /terms returns HTTP 200 with valid, substantive terms content.
     */
    public function test_terms_page_returns_200_with_substantive_content(): void
    {
        $response = $this->get('/terms');
        $response->assertStatus(200);

        // Core identity & content assertions
        $response->assertSee('Terms of Service');
        $response->assertSee('Acceptance of Terms');
        $response->assertSee('Marketplace &amp; Escrow Framework', false);
        $response->assertSee('Governing Law: Jurisdiction of Contai, West Bengal, India');

        // Named route verification
        $this->assertEquals(url('/terms'), route('pages.terms'));
    }

    /**
     * Challenge 1.3: GET /return-policy returns HTTP 200 with valid, substantive return policy content.
     */
    public function test_return_policy_page_returns_200_with_substantive_content(): void
    {
        $response = $this->get('/return-policy');
        $response->assertStatus(200);

        // Core identity & content assertions
        $response->assertSee('Return &amp; Refund Policy', false);
        $response->assertSee('Escrow-Backed Buyer Protection');
        $response->assertSee('Perishable &amp; Farm Goods', false);
        $response->assertSee('Non-Returnable Items');

        // Named route verification
        $this->assertEquals(url('/return-policy'), route('pages.return-policy'));
    }

    /**
     * Challenge 1.4: Legal routes accessible across guest, customer, and seller roles without session corruption.
     */
    public function test_legal_routes_accessible_across_all_auth_contexts(): void
    {
        $customer = User::factory()->create(['role' => 'user']);
        $seller = $this->createSeller('Agro Merchant');

        $routes = ['/privacy', '/terms', '/return-policy'];

        foreach ($routes as $route) {
            // Guest access
            $guestRes = $this->get($route);
            $guestRes->assertStatus(200);

            // Customer auth access
            $custRes = $this->actingAs($customer, 'user')->get($route);
            $custRes->assertStatus(200);

            // Seller auth access
            $sellerRes = $this->actingAs($seller, 'seller')->get($route);
            $sellerRes->assertStatus(200);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 2. SELLER AUCTION HISTORY ISOLATION & FILTERING
    |--------------------------------------------------------------------------
    */

    /**
     * Challenge 2.1: Unauthenticated access to /seller/auctions/history redirects to login.
     */
    public function test_seller_auction_history_requires_seller_auth(): void
    {
        $response = $this->get('/seller/auctions/history');
        $response->assertRedirect(route('login'));
    }

    /**
     * Challenge 2.2: GET /seller/auctions/history returns HTTP 200 and STRICTLY lists ended auctions only.
     */
    public function test_seller_auction_history_strictly_isolates_ended_auctions(): void
    {
        $sellerA = $this->createSeller('Seller Alpha');
        $sellerB = $this->createSeller('Seller Beta');

        $category = Category::create([
            'name'   => 'Fisheries',
            'slug'   => 'fisheries',
            'status' => 'active',
        ]);

        $prodA1 = $this->createProduct($sellerA, $category, ['name' => 'Ended Fish Lot Alpha']);
        $prodA2 = $this->createProduct($sellerA, $category, ['name' => 'Live Fish Lot Alpha']);
        $prodA3 = $this->createProduct($sellerA, $category, ['name' => 'Scheduled Fish Lot Alpha']);
        $prodA4 = $this->createProduct($sellerA, $category, ['name' => 'Cancelled Fish Lot Alpha']);

        $prodB = $this->createProduct($sellerB, $category, ['name' => 'Ended Fish Lot Beta']);

        // Seller A: Ended auction (Must be present)
        $endedAuctionA = Auction::create([
            'seller_id'         => $sellerA->sellerProfile->id,
            'product_id'        => $prodA1->id,
            'starting_price'    => 500.00,
            'current_price'     => 850.00,
            'minimum_increment' => 50.00,
            'status'            => 'ended',
            'starts_at'         => Carbon::now()->subDays(5),
            'ends_at'           => Carbon::now()->subDays(2),
        ]);

        // Seller A: Live auction (Must NOT be present)
        $liveAuctionA = Auction::create([
            'seller_id'         => $sellerA->sellerProfile->id,
            'product_id'        => $prodA2->id,
            'starting_price'    => 600.00,
            'current_price'     => 700.00,
            'minimum_increment' => 50.00,
            'status'            => 'live',
            'starts_at'         => Carbon::now()->subHour(),
            'ends_at'           => Carbon::now()->addHours(2),
        ]);

        // Seller A: Scheduled auction (Must NOT be present)
        $scheduledAuctionA = Auction::create([
            'seller_id'         => $sellerA->sellerProfile->id,
            'product_id'        => $prodA3->id,
            'starting_price'    => 400.00,
            'current_price'     => 400.00,
            'minimum_increment' => 50.00,
            'status'            => 'scheduled',
            'starts_at'         => Carbon::now()->addDay(),
            'ends_at'           => Carbon::now()->addDays(3),
        ]);

        // Seller A: Cancelled auction (Must NOT be present)
        $cancelledAuctionA = Auction::create([
            'seller_id'         => $sellerA->sellerProfile->id,
            'product_id'        => $prodA4->id,
            'starting_price'    => 300.00,
            'current_price'     => 300.00,
            'minimum_increment' => 50.00,
            'status'            => 'cancelled',
            'starts_at'         => Carbon::now()->subDays(4),
            'ends_at'           => Carbon::now()->subDays(3),
        ]);

        // Seller B: Ended auction (Must NOT be present - Tenant isolation)
        $endedAuctionB = Auction::create([
            'seller_id'         => $sellerB->sellerProfile->id,
            'product_id'        => $prodB->id,
            'starting_price'    => 1000.00,
            'current_price'     => 1200.00,
            'minimum_increment' => 100.00,
            'status'            => 'ended',
            'starts_at'         => Carbon::now()->subDays(6),
            'ends_at'           => Carbon::now()->subDays(3),
        ]);

        // Execute request as Seller A
        $response = $this->actingAs($sellerA, 'seller')->get(route('seller.auctions.history'));
        $response->assertStatus(200);

        // Verify view data contains only Seller A's ended auction
        $viewAuctions = $response->viewData('auctions');
        $auctionIds = collect($viewAuctions->items())->pluck('id')->all();

        $this->assertContains($endedAuctionA->id, $auctionIds, 'Ended auction for Seller A must be present in history');
        $this->assertNotContains($liveAuctionA->id, $auctionIds, 'Live auction must NOT appear in auction history');
        $this->assertNotContains($scheduledAuctionA->id, $auctionIds, 'Scheduled auction must NOT appear in auction history');
        $this->assertNotContains($cancelledAuctionA->id, $auctionIds, 'Cancelled auction must NOT appear in auction history');
        $this->assertNotContains($endedAuctionB->id, $auctionIds, 'Another seller\'s ended auction must NOT appear');

        $this->assertCount(1, $auctionIds);

        // Verify HTML contents
        $response->assertSee('Ended Fish Lot Alpha');
        $response->assertDontSee('Live Fish Lot Alpha');
        $response->assertDontSee('Scheduled Fish Lot Alpha');
        $response->assertDontSee('Ended Fish Lot Beta');
    }

    /**
     * Challenge 2.3: Adversarial query parameter tampering on /seller/auctions/history (?status=live).
     */
    public function test_seller_auction_history_resists_query_param_tampering(): void
    {
        $seller = $this->createSeller('Agro Tamper Test');
        $category = Category::create(['name' => 'Spices', 'slug' => 'spices', 'status' => 'active']);

        $prod1 = $this->createProduct($seller, $category, ['name' => 'Turmeric Ended']);
        $prod2 = $this->createProduct($seller, $category, ['name' => 'Turmeric Live']);

        $endedAuction = Auction::create([
            'seller_id'         => $seller->sellerProfile->id,
            'product_id'        => $prod1->id,
            'starting_price'    => 100.00,
            'current_price'     => 150.00,
            'minimum_increment' => 20.00,
            'status'            => 'ended',
            'starts_at'         => Carbon::now()->subDays(3),
            'ends_at'           => Carbon::now()->subDay(),
        ]);

        $liveAuction = Auction::create([
            'seller_id'         => $seller->sellerProfile->id,
            'product_id'        => $prod2->id,
            'starting_price'    => 200.00,
            'current_price'     => 220.00,
            'minimum_increment' => 20.00,
            'status'            => 'live',
            'starts_at'         => Carbon::now()->subHour(),
            'ends_at'           => Carbon::now()->addHour(),
        ]);

        // Attempt parameter tampering by requesting history with ?status=live
        $response = $this->actingAs($seller, 'seller')->get('/seller/auctions/history?status=live');
        $response->assertStatus(200);

        $viewAuctions = $response->viewData('auctions');
        $auctionIds = collect($viewAuctions->items())->pluck('id')->all();

        // History endpoint must enforce ended auctions regardless of query param override
        $this->assertContains($endedAuction->id, $auctionIds);
        $this->assertNotContains($liveAuction->id, $auctionIds);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. SELLER NOTIFICATIONS & SETTINGS ROUTES
    |--------------------------------------------------------------------------
    */

    /**
     * Challenge 3.1: GET /seller/account/notifications returns HTTP 200 with notification feed.
     */
    public function test_seller_notifications_returns_200(): void
    {
        $seller = $this->createSeller('Notify Merchant');

        // Unauthenticated check
        $guestRes = $this->get(route('seller.account.notifications'));
        $guestRes->assertRedirect(route('login'));

        // Authenticated check
        $response = $this->actingAs($seller, 'seller')->get(route('seller.account.notifications'));
        $response->assertStatus(200);
        $response->assertSee('Store Alerts &amp; Notifications', false);
        $response->assertSee('New Multi-Seller Consignment Order Assigned');
        $response->assertSee('Escrow Payout Settled to Bank Account');
    }

    /**
     * Challenge 3.2: GET /seller/account/settings returns HTTP 200 and DOES NOT redirect to profile.
     */
    public function test_seller_settings_returns_200_and_does_not_redirect_to_profile(): void
    {
        $seller = $this->createSeller('Settings Merchant');

        // Unauthenticated check
        $guestRes = $this->get(route('seller.account.settings'));
        $guestRes->assertRedirect(route('login'));

        // Authenticated check
        $response = $this->actingAs($seller, 'seller')->get(route('seller.account.settings'));
        
        // STRICT CHECK: Must be HTTP 200, NOT a 302 redirect to profile!
        $response->assertStatus(200);
        $this->assertFalse($response->isRedirection(), 'Settings route must render view directly, not redirect');
        $response->assertSee('Store Settings &amp; Preferences', false);
        $response->assertSee('Configure your operational defaults');
        $response->assertSee('action="' . route('seller.account.settings.update') . '"', false);
    }

    /**
     * Challenge 3.3: PUT /seller/account/settings.update processes updates and returns with flash feedback.
     */
    public function test_seller_settings_update_processes_safely(): void
    {
        $seller = $this->createSeller('Settings Update Merchant');

        $response = $this->actingAs($seller, 'seller')->put(route('seller.account.settings.update'), [
            'auto_hide_perishable' => 1,
            'default_low_stock_threshold' => 12,
            'default_unit_type' => 'kg',
            'sms_alerts' => 1,
            'email_digest' => 1,
        ]);

        $response->assertRedirect(route('seller.account.settings'));
        $response->assertSessionHas('success');
    }

    /*
    |--------------------------------------------------------------------------
    | 4. CATEGORY ROUTING & SLUG FALLBACKS
    |--------------------------------------------------------------------------
    */

    /**
     * Challenge 4.1: GET /category and GET /category/all resolve cleanly with HTTP 200 without 404 or 500.
     */
    public function test_category_and_category_all_resolve_cleanly(): void
    {
        $seller = $this->createSeller('Agro Farmer');

        $cat1 = Category::create([
            'name'   => 'Fresh Dairy',
            'slug'   => 'fresh-dairy',
            'status' => 'active',
        ]);

        $cat2 = Category::create([
            'name'   => 'Poultry',
            'slug'   => 'poultry',
            'status' => 'active',
        ]);

        $prod1 = $this->createProduct($seller, $cat1, ['name' => 'Organic Cow Milk']);
        $prod2 = $this->createProduct($seller, $cat2, ['name' => 'Free Range Country Eggs']);

        // 1. GET /category without slug
        $resNull = $this->get('/category');
        $resNull->assertStatus(200);
        $resNull->assertSee('Organic Cow Milk');
        $resNull->assertSee('Free Range Country Eggs');

        // 2. GET /category/all
        $resAll = $this->get('/category/all');
        $resAll->assertStatus(200);
        $resAll->assertSee('Organic Cow Milk');
        $resAll->assertSee('Free Range Country Eggs');

        // 3. GET /category/{slug} for specific category
        $resSpecific = $this->get('/category/fresh-dairy');
        $resSpecific->assertStatus(200);
        $resSpecific->assertSee('Organic Cow Milk');
        $resSpecific->assertDontSee('Free Range Country Eggs');

        // 4. Non-existent slug returns 200 gracefully without crashing
        $resNonExistent = $this->get('/category/non-existent-random-category');
        $resNonExistent->assertStatus(200);

        // 5. Query parameters (search and sort) on /category/all
        $resSearch = $this->get('/category/all?search=Cow&sort=price_low');
        $resSearch->assertStatus(200);
        $resSearch->assertSee('Organic Cow Milk');
        $resSearch->assertDontSee('Free Range Country Eggs');
    }

    /*
    |--------------------------------------------------------------------------
    | 5. FOOTER ROUTE BINDINGS & LINK INTEGRITY
    |--------------------------------------------------------------------------
    */

    /**
     * Challenge 5.1: Rendered HTML footer contains valid routes for privacy, terms, return-policy, and deals.
     */
    public function test_footer_contains_valid_routes_and_no_dead_links(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // Legal page route links
        $response->assertSee('href="' . route('pages.privacy') . '"', false);
        $response->assertSee('href="' . route('pages.terms') . '"', false);
        $response->assertSee('href="' . route('pages.return-policy') . '"', false);

        // Deals route link with filter=deals (NOT filter=escrow)
        $response->assertSee('href="' . route('products.index', ['filter' => 'deals']) . '"', false);
        $response->assertDontSee('filter=escrow');

        // Social media links must not be dead href="#"
        $response->assertDontSee('href="#"');

        // Verify same footer integrity on other key pages
        $pagesToAudit = [
            '/products',
            '/privacy',
            '/terms',
            '/return-policy',
            '/how-it-works',
        ];

        foreach ($pagesToAudit as $pageUrl) {
            $pageRes = $this->get($pageUrl);
            $pageRes->assertStatus(200);
            $pageRes->assertSee(route('pages.privacy'));
            $pageRes->assertSee(route('pages.terms'));
            $pageRes->assertSee(route('pages.return-policy'));
            $pageRes->assertSee(route('products.index', ['filter' => 'deals']));
        }
    }
}
