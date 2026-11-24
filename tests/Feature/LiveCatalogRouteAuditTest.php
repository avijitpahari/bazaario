<?php

namespace Tests\Feature;

use App\Models\Auction;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LiveCatalogRouteAuditTest extends TestCase
{
    use RefreshDatabase;

    protected ?User $admin = null;
    protected ?User $seller = null;
    protected ?User $customer = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->seller = User::where('role', 'seller')->where('status', 'active')->first();
        if (!$this->seller) {
            $this->seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
            SellerProfile::create([
                'user_id' => $this->seller->id,
                'shop_name' => 'Audit Merchant',
                'shop_slug' => 'audit-merchant-' . $this->seller->id,
                'seller_type' => 'Farmer',
                'status' => 'approved',
                'trust_score' => 95.0,
                'commission_rate' => 8.0,
                'bank_account_number' => '1234567890',
                'bank_ifsc' => 'HDFC0001234',
            ]);
        }

        $this->customer = User::where('role', 'user')->first() ?? User::factory()->create(['role' => 'user', 'status' => 'active']);

        $category = Category::firstOrCreate(['slug' => 'organic-produce'], [
            'name' => 'Organic Produce',
            'status' => 'active',
            'image' => 'images/categories/organic_food.jpg'
        ]);

        $product = Product::firstOrCreate(['slug' => 'gi-tagged-ratnagiri-alphonso-mangoes'], [
            'seller_id' => $this->seller->id,
            'category_id' => $category->id,
            'name' => 'GI-Tagged Ratnagiri Alphonso Mangoes',
            'short_description' => 'Naturally tree-ripened Alphonso mangoes.',
            'description' => 'Rich sweet pulp.',
            'sale_type' => 'fixed_price',
            'unit_type' => 'kg',
            'price' => 1450.00,
            'stock' => 50,
            'sku' => 'SKU-AUDIT-01',
            'status' => 'active',
        ]);

        $auctionProduct = Product::firstOrCreate(['slug' => 'rolex-submariner-audit'], [
            'seller_id' => $this->seller->id,
            'category_id' => $category->id,
            'name' => 'Rolex Submariner Date 41mm',
            'short_description' => 'Luxury timepiece.',
            'description' => 'Mint condition.',
            'sale_type' => 'auction',
            'unit_type' => 'piece',
            'price' => 950000.00,
            'stock' => 1,
            'sku' => 'SKU-AUDIT-02',
            'status' => 'active',
        ]);

        Auction::firstOrCreate(['product_id' => $auctionProduct->id], [
            'seller_id' => $this->seller->sellerProfile->id,
            'starting_price' => 950000.00,
            'reserve_price' => 1100000.00,
            'current_price' => 1025000.00,
            'minimum_increment' => 25000.00,
            'starts_at' => now()->subHours(2),
            'ends_at' => now()->addHours(20),
            'status' => 'live',
        ]);
    }

    /**
     * Test 1: Public Core Catalog & Information Routes.
     */
    public function test_public_core_catalog_routes_return_200(): void
    {
        $routes = [
            '/',
            '/products',
            '/products?filter=deals',
            '/products?search=mango',
            '/auctions',
            '/login',
            '/register',
            '/privacy',
            '/terms',
            '/return-policy',
            '/how-it-works',
            '/compare',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }

    /**
     * Test 2: Live Products and Categories resolve with valid views.
     */
    public function test_live_products_and_categories_render(): void
    {
        $products = Product::where('status', 'active')->take(5)->get();
        foreach ($products as $product) {
            $response = $this->get('/products/' . $product->slug);
            $response->assertStatus(200);
            $response->assertSee($product->name);
        }

        $categories = Category::take(5)->get();
        foreach ($categories as $category) {
            $response = $this->get('/category/' . $category->slug);
            $response->assertStatus(200);
        }
    }

    /**
     * Test 3: Live Auctions Detail View renders with countdown telemetry.
     */
    public function test_live_auctions_detail_views_render(): void
    {
        $auctions = Auction::where('status', 'live')->get();
        foreach ($auctions as $auction) {
            $response = $this->get('/auctions/' . $auction->id);
            $response->assertStatus(200);
            $response->assertSee(format_inr($auction->current_price ?: $auction->starting_price));
        }
    }

    /**
     * Test 4: Authenticated Customer Portals.
     */
    public function test_authenticated_customer_portals(): void
    {
        $customerRoutes = [
            '/user/profile',
            '/user/orders',
            '/user/addresses',
            '/user/bids',
            '/user/auctions',
            '/cart',
            '/user/support',
        ];

        foreach ($customerRoutes as $route) {
            $response = $this->actingAs($this->customer, 'user')->get($route);
            $response->assertStatus(200);
        }
    }

    /**
     * Test 5: Authenticated Seller Management Terminal.
     */
    public function test_authenticated_seller_management_terminal(): void
    {
        $sellerRoutes = [
            '/seller/dashboard',
            '/seller/products',
            '/seller/products/create',
            '/seller/inventory',
            '/seller/orders',
            '/seller/auctions',
            '/seller/auctions/live',
            '/seller/auctions/history',
            '/seller/payouts',
            '/seller/account/settings',
            '/seller/support',
        ];

        foreach ($sellerRoutes as $route) {
            $response = $this->actingAs($this->seller, 'seller')->get($route);
            $response->assertStatus(200);
        }
    }

    /**
     * Test 6: Authenticated Admin Operations Platform.
     */
    public function test_authenticated_admin_operations_platform(): void
    {
        $adminRoutes = [
            '/admin/dashboard',
            '/admin/sellers',
            '/admin/sellers/approvals',
            '/admin/products',
            '/admin/orders',
            '/admin/auctions',
            '/admin/payouts',
            '/admin/disputes',
            '/admin/categories',
            '/admin/coupons',
            '/admin/support',
            '/admin/settings',
            '/admin/analytics',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($this->admin, 'admin')->get($route);
            $response->assertStatus(200);
        }
    }

    /**
     * Test 7: Support Chat JSON API Endpoints.
     */
    public function test_support_chat_json_endpoints(): void
    {
        // 1. Init chat
        $initResponse = $this->postJson('/support/chat/init');
        $initResponse->assertStatus(200);
        $initResponse->assertJsonStructure(['success', 'conversation_id']);

        $conversationId = $initResponse->json('conversation_id');

        // 2. Poll chat
        $pollResponse = $this->getJson('/support/chat/poll?conversation_id=' . $conversationId);
        $pollResponse->assertStatus(200);
        $pollResponse->assertJsonStructure(['success', 'messages']);

        // 3. Send inquiry message
        $sendResponse = $this->postJson('/support/chat/send', [
            'conversation_id' => $conversationId,
            'message' => 'Hello, I have an inquiry about my recent auction order.',
        ]);
        $sendResponse->assertStatus(200);
        $sendResponse->assertJsonStructure(['success', 'sent_message']);
    }

    /**
     * Test 8: Language Switcher Endpoint.
     */
    public function test_language_switcher_endpoint(): void
    {
        $response = $this->post('/language/switch', ['locale' => 'hi']);
        $response->assertRedirect();
        $this->assertEquals('hi', session('locale'));

        $responseBn = $this->post('/language/switch', ['locale' => 'bn']);
        $responseBn->assertRedirect();
        $this->assertEquals('bn', session('locale'));

        $responseEn = $this->post('/language/switch', ['locale' => 'en']);
        $responseEn->assertRedirect();
        $this->assertEquals('en', session('locale'));
    }
}
