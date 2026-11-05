<?php

namespace Tests\Feature;

use App\Models\Auction;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\GeminiApiService;
use App\Services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiAiIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function createCustomer(string $name = 'John Buyer', string $email = 'john@example.com'): User
    {
        return User::factory()->create([
            'name'     => $name,
            'email'    => $email,
            'role'     => 'user',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ]);
    }

    protected function createSeller(string $name = 'Farmer Ramesh'): User
    {
        $seller = User::factory()->create([
            'name'     => $name,
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ]);

        SellerProfile::create([
            'user_id'             => $seller->id,
            'shop_name'           => $name . ' Farm',
            'shop_slug'           => 'farm-' . $seller->id,
            'seller_type'         => 'Farmer',
            'city'                => 'Kolkata',
            'state'               => 'West Bengal',
            'country'             => 'India',
            'status'              => 'approved',
            'trust_score'         => 95.00,
            'commission_rate'     => 8.50,
            'address'             => 'Kolkata Green Road',
            'postal_code'         => '700001',
            'operating_radius_km' => 30,
        ]);

        return $seller->fresh(['sellerProfile']);
    }

    protected function createProduct(User $seller, string $name, float $price, string $status = 'active', ?Category $category = null): Product
    {
        if (!$category) {
            $category = Category::firstOrCreate(
                ['slug' => 'fresh-produce'],
                ['name' => 'Fresh Produce', 'status' => 'active']
            );
        }

        return Product::create([
            'seller_id'      => $seller->id,
            'category_id'    => $category->id,
            'name'           => $name,
            'slug'           => str()->slug($name) . '-' . rand(100, 999),
            'price'          => $price,
            'stock'          => 25,
            'status'         => $status,
            'unit_type'      => 'kg',
            'average_rating' => 4.80,
            'total_reviews'  => 12,
            'is_perishable'  => true,
        ]);
    }

    /**
     * Clear all configured Gemini API keys to test fallback mode.
     */
    protected function clearGeminiApiKey(): void
    {
        SiteSetting::set('gemini_api_key', null);
        Config::set('services.gemini.api_key', null);
        putenv('GEMINI_API_KEY=');
    }

    /**
     * Test 1: Missing API key returns structured recommendations with zero 500 errors.
     */
    public function test_missing_api_key_returns_structured_recommendations_with_zero_500_errors(): void
    {
        $this->clearGeminiApiKey();

        $seller = $this->createSeller();
        $this->createProduct($seller, 'Alphonso Mangoes', 450.00);
        $this->createProduct($seller, 'Organic Darjeeling Tea', 320.00);

        $service = app(GeminiApiService::class);
        $result = $service->getPersonalizedRecommendations(null, 4);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('local_fallback', $result['source']);
        $this->assertNotEmpty($result['items']);

        $firstItem = $result['items'][0];
        $this->assertArrayHasKey('id', $firstItem);
        $this->assertArrayHasKey('name', $firstItem);
        $this->assertArrayHasKey('price', $firstItem);
        $this->assertArrayHasKey('reason', $firstItem);
        $this->assertNotEmpty($firstItem['reason']);
    }

    /**
     * Test 2: Missing API key returns structured comparison with zero 500 errors.
     */
    public function test_missing_api_key_returns_structured_comparison_with_zero_500_errors(): void
    {
        $this->clearGeminiApiKey();

        $seller = $this->createSeller();
        $p1 = $this->createProduct($seller, 'Economy Wheat Flour', 60.00);
        $p2 = $this->createProduct($seller, 'Premium Sharbati Atta', 110.00);

        $service = app(GeminiApiService::class);
        $result = $service->compareProducts([$p1->id, $p2->id]);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('local_fallback', $result['source']);
        $this->assertCount(2, $result['products']);
        $this->assertArrayHasKey('attributes', $result);
        $this->assertArrayHasKey('pros_cons', $result);
        $this->assertNotEmpty($result['pros_cons']);
        $this->assertNotEmpty($result['recommendation']);
        $this->assertNotEmpty($result['verdict']);
    }

    /**
     * Test 3: Missing API key returns conversational chatbot response with zero 500 errors.
     */
    public function test_missing_api_key_returns_conversational_chatbot_response_with_zero_500_errors(): void
    {
        $this->clearGeminiApiKey();

        $service = app(GeminiApiService::class);
        $result = $service->chat('Hello, what can you help me with?');

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEquals('local_fallback', $result['source']);
        $this->assertNotEmpty($result['reply']);
        $this->assertStringContainsString('Bazaario AI', $result['reply']);
    }

    /**
     * Test 4: Chatbot order lookup queries authenticated user's own orders accurately.
     */
    public function test_chatbot_order_lookup_queries_authenticated_user_orders_accurately(): void
    {
        $this->clearGeminiApiKey();

        $customer = $this->createCustomer('Alice Buyer', 'alice@test.com');
        $seller = $this->createSeller('Ramesh Farmer');
        $product = $this->createProduct($seller, 'Sundarban Pure Honey', 650.00);

        $order = Order::create([
            'order_number'            => 'ORD-ALICE-1001',
            'user_id'                 => $customer->id,
            'subtotal'                => 650.00,
            'total_amount'            => 699.00,
            'payment_method'          => 'upi',
            'payment_status'          => 'paid',
            'order_status'            => 'processing',
            'delivery_full_name'      => 'Alice Buyer',
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Flat 4B, Salt Lake',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'West Bengal',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700064',
            'placed_at'               => now(),
        ]);

        $sellerOrder = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => 'SO-ALICE-1001',
            'subtotal'            => 650.00,
            'status'              => 'processing',
            'courier_name'        => 'Bengal Agro Logistics #9',
            'tracking_number'     => 'TRK-ALICE-999',
            'delivery_slot'       => 'Morning 8-11 AM',
        ]);

        OrderItem::create([
            'seller_order_id' => $sellerOrder->id,
            'product_id'      => $product->id,
            'product_name'    => $product->name,
            'unit_price'      => 650.00,
            'quantity'        => 1,
            'total_price'     => 650.00,
        ]);

        $service = app(GeminiApiService::class);
        $result = $service->chat('Where is my order ORD-ALICE-1001?', [], $customer);

        $this->assertTrue($result['success']);
        $this->assertContains('lookupUserOrders', $result['tools_used']);
        $this->assertStringContainsString('ORD-ALICE-1001', $result['reply']);
        $this->assertStringContainsString('TRK-ALICE-999', $result['reply']);
        $this->assertStringContainsString('Bengal Agro Logistics', $result['reply']);
    }

    /**
     * Test 5: Chatbot order lookup rejects or blocks access to other users' orders (tenant isolation).
     */
    public function test_chatbot_order_lookup_enforces_strict_tenant_isolation(): void
    {
        $this->clearGeminiApiKey();

        $victim = $this->createCustomer('Victim User', 'victim@test.com');
        $attacker = $this->createCustomer('Attacker User', 'attacker@test.com');

        $orderVictim = Order::create([
            'order_number'            => 'ORD-VICTIM-CONFIDENTIAL-777',
            'user_id'                 => $victim->id,
            'subtotal'                => 1500.00,
            'total_amount'            => 1550.00,
            'payment_method'          => 'upi',
            'payment_status'          => 'paid',
            'order_status'            => 'processing',
            'delivery_full_name'      => 'Victim User',
            'delivery_phone'          => '9998887776',
            'delivery_address_line_1' => 'Confidential Address 99',
            'delivery_city'           => 'Siliguri',
            'delivery_state'          => 'West Bengal',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '734001',
            'placed_at'               => now(),
        ]);

        $service = app(GeminiApiService::class);

        // Scenario A: Attacker tries to query Victim's order
        $resultAttacker = $service->chat('Track order ORD-VICTIM-CONFIDENTIAL-777', [], $attacker);
        $this->assertTrue($resultAttacker['success']);
        $this->assertStringNotContainsString('Confidential Address', $resultAttacker['reply']);
        $this->assertStringNotContainsString('Shipped', $resultAttacker['reply']);
        $this->assertStringContainsString('not found in your account', $resultAttacker['reply']);

        // Scenario B: Unauthenticated guest tries to query order
        $resultGuest = $service->chat('Where is order ORD-VICTIM-CONFIDENTIAL-777?', [], null);
        $this->assertTrue($resultGuest['success']);
        $this->assertStringNotContainsString('Confidential Address', $resultGuest['reply']);
        $this->assertStringContainsString('log in', $resultGuest['reply']);
    }

    /**
     * Test 6: Chatbot product search finds active products.
     */
    public function test_chatbot_product_search_finds_active_products(): void
    {
        $this->clearGeminiApiKey();

        $seller = $this->createSeller();
        $activeItem = $this->createProduct($seller, 'Kolkata Sweet Sondesh Sweets', 280.00, 'active');
        $inactiveItem = $this->createProduct($seller, 'Hidden Unapproved Sondesh', 200.00, 'draft');

        $service = app(GeminiApiService::class);
        $result = $service->chat('Do you have Sweet Sondesh?', [], null);

        $this->assertTrue($result['success']);
        $this->assertContains('lookupProducts', $result['tools_used']);
        $this->assertStringContainsString('Kolkata Sweet Sondesh Sweets', $result['reply']);
        $this->assertStringNotContainsString('Hidden Unapproved Sondesh', $result['reply']);
    }

    /**
     * Test 7: Chatbot policy query explains return and escrow policies accurately.
     */
    public function test_chatbot_policy_query_explains_return_and_escrow_policies(): void
    {
        $this->clearGeminiApiKey();

        $service = app(GeminiApiService::class);
        $result = $service->chat('What is your return policy for fresh fruits and handicrafts?');

        $this->assertTrue($result['success']);
        $this->assertContains('lookupStorePolicies', $result['tools_used']);
        $this->assertStringContainsString('48 hours', $result['reply']);
        $this->assertStringContainsString('7 days', $result['reply']);
        $this->assertStringContainsString('Escrow', $result['reply']);
        $this->assertStringContainsString('24 to 48 hours', $result['reply']);
    }

    /**
     * Test 8: Simulated valid API response with Http::fake() parses live structured response.
     */
    public function test_simulated_valid_api_response_with_http_fake_parses_live_structured_response(): void
    {
        Config::set('services.gemini.api_key', 'test-valid-gemini-key');

        $seller = $this->createSeller();
        $p1 = $this->createProduct($seller, 'Product Alpha', 100.00);
        $p2 = $this->createProduct($seller, 'Product Beta', 200.00);

        // Mock Gemini generateContent endpoint
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        [
                                            'id'     => $p1->id,
                                            'reason' => 'AI detected high category relevance for cooking',
                                        ],
                                        [
                                            'id'     => $p2->id,
                                            'reason' => 'Trending community favorite in your district',
                                        ],
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = app(GeminiApiService::class);
        $result = $service->getPersonalizedRecommendations(null, 2);

        $this->assertEquals('success', $result['status']);
        $this->assertEquals('gemini', $result['source']);
        $this->assertCount(2, $result['items']);
        $this->assertEquals('AI detected high category relevance for cooking', $result['items'][0]['reason']);
    }

    /**
     * Test 9: HTTP endpoints /ai/chat, /ai/recommendations, /ai/compare respond with status 200.
     */
    public function test_http_endpoints_respond_with_status_200(): void
    {
        $this->clearGeminiApiKey();

        $seller = $this->createSeller();
        $p1 = $this->createProduct($seller, 'Item A', 150.00);
        $p2 = $this->createProduct($seller, 'Item B', 250.00);
        $customer = $this->createCustomer();

        // 1. POST /ai/chat
        $chatRes = $this->postJson('/ai/chat', [
            'prompt' => 'Show me fresh fruits',
        ]);
        $chatRes->assertStatus(200);
        $chatRes->assertJsonStructure(['success', 'reply', 'source']);
        $this->assertTrue($chatRes->json('success'));

        // 2. GET /ai/recommendations
        $recRes = $this->getJson('/ai/recommendations');
        $recRes->assertStatus(200);
        $recRes->assertJsonStructure(['status', 'source', 'items']);
        $this->assertEquals('success', $recRes->json('status'));

        // 3. POST /ai/compare
        $cmpRes = $this->postJson('/ai/compare', [
            'product_ids' => [$p1->id, $p2->id],
        ]);
        $cmpRes->assertStatus(200);
        $cmpRes->assertJsonStructure(['status', 'products', 'attributes', 'pros_cons', 'recommendation', 'verdict']);
        $this->assertEquals('success', $cmpRes->json('status'));

        // 4. GET /user/recommendations (Web view)
        $userRecView = $this->actingAs($customer, 'user')->get('/user/recommendations');
        $userRecView->assertStatus(200);
        $userRecView->assertSee('AI Recommendations');

        // 5. GET /user/compare (Web view)
        $userCmpView = $this->actingAs($customer, 'user')->get('/user/compare?products=' . $p1->id . ',' . $p2->id);
        $userCmpView->assertStatus(200);
        $userCmpView->assertSee('AI Product Comparison');
    }

    /**
     * Test 10: GeminiService backward compatibility delegates to GeminiApiService.
     */
    public function test_gemini_service_delegates_to_gemini_api_service_cleanly(): void
    {
        $this->clearGeminiApiKey();

        $legacyService = new GeminiService();
        $result = $legacyService->chat('Hello from legacy caller');

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('reply', $result);
        $this->assertArrayHasKey('source', $result);
    }
}
