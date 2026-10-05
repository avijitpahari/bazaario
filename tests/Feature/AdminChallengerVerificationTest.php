<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\SellerOrder;
use App\Models\OrderItem;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Payout;
use App\Models\OrderReturn;
use App\Models\Coupon;
use App\Models\SiteSetting;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

class AdminChallengerVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected User $sellerUser;
    protected User $suspendedAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@bazaario.com',
            'password' => Hash::make('AdminPass123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->regularUser = User::create([
            'name' => 'Regular Shopper',
            'email' => 'shopper@bazaario.com',
            'password' => Hash::make('ShopperPass123!'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->sellerUser = User::create([
            'name' => 'Artisan Merchant',
            'email' => 'artisan@bazaario.com',
            'password' => Hash::make('ArtisanPass123!'),
            'role' => 'seller',
            'status' => 'active',
        ]);

        $this->suspendedAdmin = User::create([
            'name' => 'Suspended Administrator',
            'email' => 'suspended_admin@bazaario.com',
            'password' => Hash::make('AdminPass123!'),
            'role' => 'admin',
            'status' => 'suspended',
        ]);
    }

    /**
     * Helper to create a dedicated seller user with profile.
     */
    protected function createMerchantUser(string $name, string $email, string $shopName, string $status = 'pending'): array
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('MerchantPass123!'),
            'role' => 'seller',
            'status' => 'active',
        ]);

        $profile = SellerProfile::create([
            'user_id' => $user->id,
            'shop_name' => $shopName,
            'shop_slug' => Str::slug($shopName),
            'status' => $status,
        ]);

        return [$user, $profile];
    }

    /**
     * Provide list of all 38 protected admin routes and their HTTP methods.
     */
    protected function getProtectedAdminRoutes(): array
    {
        return [
            ['GET', route('admin.dashboard')],
            ['GET', route('admin.sellers.index')],
            ['GET', route('admin.sellers.approvals')],
            ['GET', route('admin.sellers.show', ['id' => 1])],
            ['POST', route('admin.sellers.approve', ['id' => 1])],
            ['POST', route('admin.sellers.reject', ['id' => 1])],
            ['POST', route('admin.sellers.toggle-status', ['id' => 1])],
            ['POST', route('admin.sellers.commission', ['id' => 1])],
            ['GET', route('admin.products.index')],
            ['POST', route('admin.products.toggle-status', ['id' => 1])],
            ['POST', route('admin.products.update-stock', ['id' => 1])],
            ['DELETE', route('admin.products.destroy', ['id' => 1])],
            ['GET', route('admin.categories.index')],
            ['POST', route('admin.categories.store')],
            ['PUT', route('admin.categories.update', ['id' => 1])],
            ['DELETE', route('admin.categories.destroy', ['id' => 1])],
            ['GET', route('admin.orders.index')],
            ['GET', route('admin.orders.show', ['id' => 'BZ-1001'])],
            ['POST', route('admin.orders.update-status', ['id' => 1])],
            ['GET', route('admin.auctions.index')],
            ['GET', route('admin.auctions.show', ['id' => 1])],
            ['POST', route('admin.auctions.end', ['id' => 1])],
            ['POST', route('admin.auctions.cancel', ['id' => 1])],
            ['GET', route('admin.payouts.index')],
            ['POST', route('admin.payouts.release', ['id' => 1])],
            ['POST', route('admin.payouts.batch-release')],
            ['GET', route('admin.disputes.index')],
            ['POST', route('admin.disputes.arbitrate', ['id' => 1])],
            ['GET', route('admin.customers.index')],
            ['GET', route('admin.customers.show', ['id' => 1])],
            ['POST', route('admin.customers.toggle-status', ['id' => 1])],
            ['GET', route('admin.coupons.index')],
            ['POST', route('admin.coupons.store')],
            ['POST', route('admin.coupons.toggle-status', ['id' => 1])],
            ['DELETE', route('admin.coupons.destroy', ['id' => 1])],
            ['GET', route('admin.settings.ai')],
            ['POST', route('admin.settings.ai.update')],
            ['POST', route('admin.logout')],
        ];
    }

    /**
     * Helper to make HTTP request by method string.
     */
    protected function requestRoute(string $method, string $url, array $data = [])
    {
        return match (strtoupper($method)) {
            'GET' => $this->get($url),
            'POST' => $this->post($url, $data),
            'PUT' => $this->put($url, $data),
            'DELETE' => $this->delete($url, $data),
            default => throw new \InvalidArgumentException("Unsupported method: {$method}"),
        };
    }

    // =========================================================================
    // 1. ROUTE AUTHORIZATION CHALLENGE (ALL 40 ADMIN ROUTES)
    // =========================================================================

    public function test_all_38_protected_admin_routes_reject_unauthenticated_sessions(): void
    {
        $routes = $this->getProtectedAdminRoutes();
        $this->assertCount(38, $routes, "Expected exactly 38 protected admin routes.");

        foreach ($routes as [$method, $url]) {
            $response = $this->requestRoute($method, $url);
            $this->assertTrue(
                $response->isRedirect(route('admin.login')),
                "Route {$method} {$url} did not redirect unauthenticated request to admin.login. Status: " . $response->getStatusCode()
            );
        }

        // Test the remaining 2 login routes for guest handling
        $loginPageResponse = $this->get(route('admin.login'));
        $loginPageResponse->assertStatus(200);

        $loginSubmitResponse = $this->post(route('admin.login.submit'), [
            'email' => 'unknown@bazaario.com',
            'password' => 'WrongPassword',
        ]);
        $loginSubmitResponse->assertSessionHasErrors('email');
    }

    public function test_all_38_protected_admin_routes_reject_regular_buyer_sessions(): void
    {
        $this->actingAs($this->regularUser, 'admin');

        $routes = $this->getProtectedAdminRoutes();
        foreach ($routes as [$method, $url]) {
            $response = $this->requestRoute($method, $url);
            $this->assertTrue(
                $response->isRedirect(route('admin.login')),
                "Route {$method} {$url} did not reject regular buyer session with redirect to admin.login."
            );
        }
    }

    public function test_all_38_protected_admin_routes_reject_seller_merchant_sessions(): void
    {
        $this->actingAs($this->sellerUser, 'admin');

        $routes = $this->getProtectedAdminRoutes();
        foreach ($routes as [$method, $url]) {
            $response = $this->requestRoute($method, $url);
            $this->assertTrue(
                $response->isRedirect(route('admin.login')),
                "Route {$method} {$url} did not reject seller session with redirect to admin.login."
            );
        }
    }

    public function test_all_38_protected_admin_routes_reject_suspended_admin_sessions(): void
    {
        $this->actingAs($this->suspendedAdmin, 'admin');

        $routes = $this->getProtectedAdminRoutes();
        foreach ($routes as [$method, $url]) {
            $response = $this->requestRoute($method, $url);
            $this->assertTrue(
                $response->isRedirect(route('admin.login')),
                "Route {$method} {$url} did not reject suspended admin session with redirect to admin.login."
            );
        }
    }

    // =========================================================================
    // 2. TEMPLATE INTEGRITY & RENDERING (EMPTY AND POPULATED STATES)
    // =========================================================================

    public function test_all_16_admin_views_render_cleanly_under_empty_database_state(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $screens = [
            'dashboard'     => ['GET', route('admin.dashboard')],
            'sellers'       => ['GET', route('admin.sellers.index')],
            'approvals'     => ['GET', route('admin.sellers.approvals')],
            'seller_show'   => ['GET', route('admin.sellers.show', 999)],
            'products'      => ['GET', route('admin.products.index')],
            'categories'    => ['GET', route('admin.categories.index')],
            'orders'        => ['GET', route('admin.orders.index')],
            'order_show'    => ['GET', route('admin.orders.show', 'NON-EXISTENT')],
            'auctions'      => ['GET', route('admin.auctions.index')],
            'auction_show'  => ['GET', route('admin.auctions.show', 999)],
            'payouts'       => ['GET', route('admin.payouts.index')],
            'disputes'      => ['GET', route('admin.disputes.index')],
            'customers'     => ['GET', route('admin.customers.index')],
            'customer_show' => ['GET', route('admin.customers.show', 999)],
            'coupons'       => ['GET', route('admin.coupons.index')],
            'ai_settings'   => ['GET', route('admin.settings.ai')],
        ];

        $this->assertCount(16, $screens, "Must test exactly 16 admin screens.");

        foreach ($screens as $name => [$method, $url]) {
            $response = $this->requestRoute($method, $url);
            $status = $response->getStatusCode();
            $this->assertTrue(
                in_array($status, [200, 302]),
                "Screen {$name} ({$url}) returned unexpected HTTP {$status}."
            );
            $this->assertNotEquals(500, $status, "Screen {$name} ({$url}) triggered a 500 error on empty DB!");

            if ($status === 200) {
                $html = $response->getContent();
                $this->assertNoUnparsedBladeSyntax($html, $name);
                $this->assertNoBrokenAttributeLinks($html, $name);
            }
        }
    }

    public function test_all_16_admin_views_render_200_with_populated_records_and_zero_syntax_errors(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        // Seed comprehensive domain records
        $category = Category::create([
            'name' => 'Handcrafted Pottery',
            'slug' => 'handcrafted-pottery',
            'description' => 'Traditional clay and terracotta wares.',
            'status' => 'active',
        ]);

        $subCategory = Category::create([
            'parent_id' => $category->id,
            'name' => 'Terracotta Planters',
            'slug' => 'terracotta-planters',
            'description' => 'Garden planters.',
            'status' => 'active',
        ]);

        $sellerProfile = SellerProfile::create([
            'user_id' => $this->sellerUser->id,
            'shop_name' => 'Terracotta Crafts Studio',
            'shop_slug' => 'terracotta-crafts-studio',
            'status' => 'approved',
            'bank_account_number' => '501004928192',
            'bank_ifsc' => 'HDFC0001234',
            'bank_name' => 'HDFC Bank',
            'commission_rate' => 10.00,
            'trust_score' => 92.50,
            'gstin' => '19AAACG1234A1Z5',
            'pan_number' => 'AAACG1234A',
            'city' => 'Bishnupur',
            'state' => 'West Bengal',
            'country' => 'India',
        ]);

        $product = Product::create([
            'seller_id' => $this->sellerUser->id,
            'category_id' => $category->id,
            'name' => 'Bishnupuri Terracotta Vase',
            'slug' => 'bishnupuri-terracotta-vase',
            'price' => 1850.00,
            'stock' => 20,
            'sale_type' => 'fixed_price',
            'status' => 'active',
        ]);

        $auctionProduct = Product::create([
            'seller_id' => $this->sellerUser->id,
            'category_id' => $category->id,
            'name' => 'Antique Terracotta Sculpture',
            'slug' => 'antique-terracotta-sculpture',
            'price' => 5000.00,
            'stock' => 1,
            'sale_type' => 'auction',
            'status' => 'active',
        ]);

        $order = Order::create([
            'user_id' => $this->regularUser->id,
            'order_number' => 'BZ-1099',
            'subtotal' => 1850.00,
            'total_amount' => 1850.00,
            'order_status' => 'processing',
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'delivery_full_name' => 'Regular Shopper',
            'delivery_phone' => '9830012345',
            'delivery_address_line_1' => '10 Pottery Lane',
            'delivery_city' => 'Kolkata',
            'delivery_state' => 'West Bengal',
            'delivery_country' => 'India',
            'delivery_postal_code' => '700029',
        ]);

        $sellerOrder = SellerOrder::create([
            'order_id' => $order->id,
            'seller_id' => $this->sellerUser->id,
            'seller_order_number' => 'BZ-1099-SO1',
            'subtotal' => 1850.00,
            'commission_rate' => 10.00,
            'commission_amount' => 185.00,
            'payout_amount' => 1665.00,
            'status' => 'shipped',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'seller_order_id' => $sellerOrder->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 1850.00,
            'total_price' => 1850.00,
        ]);

        $auction = Auction::create([
            'product_id' => $auctionProduct->id,
            'seller_id' => $sellerProfile->id,
            'starting_price' => 2000.00,
            'reserve_price' => 4500.00,
            'current_price' => 3500.00,
            'minimum_increment' => 200.00,
            'starts_at' => now()->subHours(12),
            'ends_at' => now()->addHours(24),
            'status' => 'live',
        ]);

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id' => $this->regularUser->id,
            'amount' => 3500.00,
        ]);

        Payout::create([
            'seller_id' => $this->sellerUser->id,
            'seller_order_id' => $sellerOrder->id,
            'gross_amount' => 1850.00,
            'commission_amount' => 185.00,
            'net_amount' => 1665.00,
            'status' => 'pending',
        ]);

        OrderReturn::create([
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'user_id' => $this->regularUser->id,
            'reason' => 'Damaged during transit',
            'description' => 'Vase neck has hairline crack.',
            'status' => 'requested',
            'refund_amount' => 1850.00,
        ]);

        Coupon::create([
            'code' => 'POTTERY20',
            'discount_type' => 'percentage',
            'discount_value' => 20.00,
            'starts_at' => now(),
            'status' => 'active',
        ]);

        SiteSetting::set('gemini_model', 'gemini-1.5-flash');

        // All 16 administrative view endpoints
        $screens = [
            'dashboard'     => route('admin.dashboard'),
            'sellers'       => route('admin.sellers.index'),
            'approvals'     => route('admin.sellers.approvals'),
            'seller_show'   => route('admin.sellers.show', $sellerProfile->id),
            'products'      => route('admin.products.index'),
            'categories'    => route('admin.categories.index'),
            'orders'        => route('admin.orders.index'),
            'order_show'    => route('admin.orders.show', $order->id),
            'auctions'      => route('admin.auctions.index'),
            'auction_show'  => route('admin.auctions.show', $auction->id),
            'payouts'       => route('admin.payouts.index'),
            'disputes'      => route('admin.disputes.index'),
            'customers'     => route('admin.customers.index'),
            'customer_show' => route('admin.customers.show', $this->regularUser->id),
            'coupons'       => route('admin.coupons.index'),
            'ai_settings'   => route('admin.settings.ai'),
        ];

        $this->assertCount(16, $screens, "Must test exactly 16 populated admin views.");

        foreach ($screens as $name => $url) {
            $response = $this->get($url);
            $this->assertEquals(200, $response->getStatusCode(), "Screen {$name} failed to render HTTP 200.");

            $html = $response->getContent();
            $this->assertGreaterThan(1000, strlen($html), "Screen {$name} rendered empty or truncated output.");
            $this->assertNoUnparsedBladeSyntax($html, $name);
            $this->assertNoBrokenAttributeLinks($html, $name);
        }
    }

    // =========================================================================
    // 3. ACTION FORMS: CSRF ENFORCEMENT & INPUT HANDLING
    // =========================================================================

    public function test_seller_rejection_action_form_csrf_and_payload_handling(): void
    {
        $seller = SellerProfile::create([
            'user_id' => $this->sellerUser->id,
            'shop_name' => 'Reject Test Shop',
            'shop_slug' => 'reject-test-shop',
            'status' => 'pending',
            'gstin' => '19AAACG0000A1Z0',
        ]);

        $this->actingAs($this->adminUser, 'admin');

        // 1. Verify view contains CSRF token and rejection action form
        $viewResponse = $this->get(route('admin.sellers.approvals'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('action="' . route('admin.sellers.reject', $seller->id) . '"', false);
        $viewResponse->assertSee('name="reason"', false);
        $viewResponse->assertSee('name="_token"', false);

        // 2. Verify CSRF middleware rejects request without token
        $csrfMiddleware = new class($this->app, $this->app['encrypter']) extends ValidateCsrfToken {
            protected function runningUnitTests(): bool {
                return false;
            }
        };

        $session = app('session.store');
        $session->start();

        $invalidCsrfRequest = \Illuminate\Http\Request::create(
            route('admin.sellers.reject', $seller->id),
            'POST',
            ['reason' => 'Invalid CSRF test']
        );
        $invalidCsrfRequest->setLaravelSession($session);

        $intercepted = false;
        try {
            $csrfMiddleware->handle($invalidCsrfRequest, fn() => response('OK'));
        } catch (TokenMismatchException $e) {
            $intercepted = true;
        }
        $this->assertTrue($intercepted, 'CSRF verification must block rejection requests without matching CSRF token.');

        // 3. Verify valid POST with custom reason executes and updates DB
        $customReason = 'GSTIN certificate mismatched with submitted PAN card.';
        $response = $this->post(route('admin.sellers.reject', $seller->id), [
            'reason' => $customReason,
        ]);
        $response->assertSessionHas('success');

        $seller->refresh();
        $this->assertEquals('rejected', $seller->status);
        $this->assertEquals($customReason, $seller->rejection_reason);
        $this->assertEquals('suspended', $this->sellerUser->fresh()->status);

        // 4. Test XSS sanitization in rejection reason
        [$xssUser, $seller2] = $this->createMerchantUser('XSS Seller', 'xss_seller@bazaario.com', 'Reject XSS Shop', 'pending');
        $this->post(route('admin.sellers.reject', $seller2->id), [
            'reason' => '<script>alert("hacked")</script>Document unreadable.',
        ]);
        $seller2->refresh();
        $this->assertEquals('Document unreadable.', $seller2->rejection_reason);
        $this->assertStringNotContainsString('<script>', $seller2->rejection_reason);
    }

    public function test_category_update_action_form_csrf_and_payload_handling(): void
    {
        $category = Category::create([
            'name' => 'Original Category Name',
            'slug' => 'original-category-name',
            'description' => 'Original description',
            'status' => 'active',
        ]);

        $this->actingAs($this->adminUser, 'admin');

        // 1. Verify view contains CSRF token and PUT method for edit form
        $viewResponse = $this->get(route('admin.categories.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('id="editCategoryForm"', false);
        $viewResponse->assertSee('name="_method"', false);
        $viewResponse->assertSee('value="PUT"', false);
        $viewResponse->assertSee('name="_token"', false);

        // 2. Verify CSRF rejection on missing token
        $csrfMiddleware = new class($this->app, $this->app['encrypter']) extends ValidateCsrfToken {
            protected function runningUnitTests(): bool {
                return false;
            }
        };

        $session = app('session.store');
        $session->start();

        $invalidCsrfRequest = \Illuminate\Http\Request::create(
            route('admin.categories.update', $category->id),
            'PUT',
            ['name' => 'Tampered Category']
        );
        $invalidCsrfRequest->setLaravelSession($session);

        $intercepted = false;
        try {
            $csrfMiddleware->handle($invalidCsrfRequest, fn() => response('OK'));
        } catch (TokenMismatchException $e) {
            $intercepted = true;
        }
        $this->assertTrue($intercepted, 'CSRF verification must block category update requests without matching CSRF token.');

        // 3. Test valid update with custom inputs
        $updateResponse = $this->put(route('admin.categories.update', $category->id), [
            'name' => 'Handmade Leather Goods',
            'slug' => 'handmade-leather-goods',
            'description' => 'Artisanal genuine leather wallets and belts.',
            'is_active' => '1',
        ]);
        $updateResponse->assertSessionHas('success');

        $category->refresh();
        $this->assertEquals('Handmade Leather Goods', $category->name);
        $this->assertEquals('handmade-leather-goods', $category->slug);
        $this->assertEquals('Artisanal genuine leather wallets and belts.', $category->description);

        // 4. Test validation error handling: empty name
        $emptyNameResponse = $this->put(route('admin.categories.update', $category->id), [
            'name' => '',
        ]);
        $emptyNameResponse->assertSessionHasErrors('name');

        // 5. Test validation error handling: duplicate name of another category
        $otherCat = Category::create([
            'name' => 'Duplicate Test Target',
            'slug' => 'duplicate-test-target',
            'status' => 'active',
        ]);
        $duplicateNameResponse = $this->put(route('admin.categories.update', $category->id), [
            'name' => 'Duplicate Test Target',
        ]);
        $duplicateNameResponse->assertSessionHasErrors('name');
    }

    public function test_product_inline_stock_and_price_action_form_csrf_and_payload_handling(): void
    {
        $category = Category::create([
            'name' => 'Test Hardware',
            'slug' => 'test-hardware',
            'status' => 'active',
        ]);

        $product = Product::create([
            'seller_id' => $this->sellerUser->id,
            'category_id' => $category->id,
            'name' => 'Brass Hardware Fitting',
            'slug' => 'brass-hardware-fitting',
            'price' => 350.00,
            'stock' => 10,
            'sale_type' => 'fixed_price',
            'status' => 'active',
        ]);

        $this->actingAs($this->adminUser, 'admin');

        // 1. Verify view contains quick stock form with CSRF and input fields
        $viewResponse = $this->get(route('admin.products.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('id="quickStockModal"', false);
        $viewResponse->assertSee('id="quickStockForm"', false);
        $viewResponse->assertSee('name="stock"', false);
        $viewResponse->assertSee('name="price"', false);
        $viewResponse->assertSee('name="_token"', false);

        // 2. Verify CSRF rejection on missing token
        $csrfMiddleware = new class($this->app, $this->app['encrypter']) extends ValidateCsrfToken {
            protected function runningUnitTests(): bool {
                return false;
            }
        };

        $session = app('session.store');
        $session->start();

        $invalidCsrfRequest = \Illuminate\Http\Request::create(
            route('admin.products.update-stock', $product->id),
            'POST',
            ['stock' => 50, 'price' => 450.00]
        );
        $invalidCsrfRequest->setLaravelSession($session);

        $intercepted = false;
        try {
            $csrfMiddleware->handle($invalidCsrfRequest, fn() => response('OK'));
        } catch (TokenMismatchException $e) {
            $intercepted = true;
        }
        $this->assertTrue($intercepted, 'CSRF verification must block product stock update requests without matching CSRF token.');

        // 3. Test valid update with custom stock and price
        $validResponse = $this->post(route('admin.products.update-stock', $product->id), [
            'stock' => 88,
            'price' => 499.50,
        ]);
        $validResponse->assertSessionHas('success');

        $product->refresh();
        $this->assertEquals(88, $product->stock);
        $this->assertEquals(499.50, (float)$product->price);

        // 4. Test validation errors: negative stock
        $negStockResponse = $this->post(route('admin.products.update-stock', $product->id), [
            'stock' => -5,
            'price' => 499.50,
        ]);
        $negStockResponse->assertSessionHasErrors('stock');

        // 5. Test validation errors: negative price
        $negPriceResponse = $this->post(route('admin.products.update-stock', $product->id), [
            'stock' => 10,
            'price' => -20.00,
        ]);
        $negPriceResponse->assertSessionHasErrors('price');

        // 6. Test validation errors: non-numeric price
        $nanPriceResponse = $this->post(route('admin.products.update-stock', $product->id), [
            'stock' => 10,
            'price' => 'not-a-number',
        ]);
        $nanPriceResponse->assertSessionHasErrors('price');
    }

    // =========================================================================
    // 4. DYNAMIC SIDEBAR DATABASE COUNTS VERIFICATION
    // =========================================================================

    public function test_sidebar_dynamic_counts_match_live_database_records(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        // Stage 1: Zero pending KYC and zero active orders
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $html = $response->getContent();

        // KYC approval badge should NOT appear when pending count is 0
        $this->assertStringNotContainsString('bg-primary-container text-on-primary-container font-mono text-[10px] font-bold', $html);

        // Stage 2: Create 3 pending KYC applications with distinct merchant users and 5 active orders
        [$u1, $pendingSeller1] = $this->createMerchantUser('Seller 1', 'seller1@bazaario.com', 'Pending Shop Alpha', 'pending');
        [$u2, $pendingSeller2] = $this->createMerchantUser('Seller 2', 'seller2@bazaario.com', 'Pending Shop Beta', 'pending');
        [$u3, $pendingSeller3] = $this->createMerchantUser('Seller 3', 'seller3@bazaario.com', 'Pending Shop Gamma', 'pending');

        $order1 = $this->createTestOrder('BZ-LIVE-1', 'processing');
        $order2 = $this->createTestOrder('BZ-LIVE-2', 'processing');
        $order3 = $this->createTestOrder('BZ-LIVE-3', 'pending');
        $order4 = $this->createTestOrder('BZ-LIVE-4', 'processing');
        $order5 = $this->createTestOrder('BZ-LIVE-5', 'pending');

        $this->assertEquals(3, SellerProfile::where('status', 'pending')->count());
        $this->assertEquals(5, Order::whereIn('order_status', ['pending', 'processing'])->count());

        $response = $this->get(route('admin.dashboard'));
        $html = $response->getContent();

        // Check pending KYC count badge dynamically renders 3
        $this->assertMatchesRegularExpression(
            '/<span[^>]*class="[^"]*bg-primary-container[^"]*"[^>]*>\s*3\s*<\/span>/',
            $html,
            "Sidebar must dynamically render KYC badge count of 3."
        );

        // Check active orders count badge dynamically renders 5
        $this->assertMatchesRegularExpression(
            '/<span[^>]*class="[^"]*bg-\[#334155\][^"]*"[^>]*>\s*5\s*<\/span>/',
            $html,
            "Sidebar must dynamically render live orders count of 5."
        );

        // Stage 3: Approve 1 seller application -> KYC count reduces to 2
        $this->post(route('admin.sellers.approve', $pendingSeller1->id));
        $this->assertEquals(2, SellerProfile::where('status', 'pending')->count());

        $response = $this->get(route('admin.dashboard'));
        $html = $response->getContent();
        $this->assertMatchesRegularExpression(
            '/<span[^>]*class="[^"]*bg-primary-container[^"]*"[^>]*>\s*2\s*<\/span>/',
            $html,
            "Sidebar KYC badge must update to 2 after approving 1 seller."
        );

        // Stage 4: Reject 1 seller application -> KYC count reduces to 1
        $this->post(route('admin.sellers.reject', $pendingSeller2->id), ['reason' => 'Audit fail']);
        $this->assertEquals(1, SellerProfile::where('status', 'pending')->count());

        $response = $this->get(route('admin.dashboard'));
        $html = $response->getContent();
        $this->assertMatchesRegularExpression(
            '/<span[^>]*class="[^"]*bg-primary-container[^"]*"[^>]*>\s*1\s*<\/span>/',
            $html,
            "Sidebar KYC badge must update to 1 after rejecting 1 seller."
        );

        // Stage 5: Update 3 orders to completed/cancelled -> active orders count drops to 2
        $this->post(route('admin.orders.update-status', $order1->id), ['order_status' => 'completed']);
        $this->post(route('admin.orders.update-status', $order2->id), ['order_status' => 'completed']);
        $this->post(route('admin.orders.update-status', $order3->id), ['order_status' => 'cancelled']);
        $this->assertEquals(2, Order::whereIn('order_status', ['pending', 'processing'])->count());

        $response = $this->get(route('admin.dashboard'));
        $html = $response->getContent();
        $this->assertMatchesRegularExpression(
            '/<span[^>]*class="[^"]*bg-\[#334155\][^"]*"[^>]*>\s*2\s*<\/span>/',
            $html,
            "Sidebar live orders badge must update to 2 after completing/cancelling orders."
        );

        // Stage 6: Complete remaining 2 active orders -> active orders count drops to 0
        $this->post(route('admin.orders.update-status', $order4->id), ['order_status' => 'completed']);
        $this->post(route('admin.orders.update-status', $order5->id), ['order_status' => 'completed']);
        $this->assertEquals(0, Order::whereIn('order_status', ['pending', 'processing'])->count());

        $response = $this->get(route('admin.dashboard'));
        $html = $response->getContent();
        // Live orders badge should NOT be rendered when count is 0
        $this->assertStringNotContainsString('bg-[#334155] text-surface-container-lowest font-mono text-[10px]', $html);
    }

    // =========================================================================
    // 5. ADVERSARIAL EDGE CASES: JSON AUTH HEADERS & NON-EXISTENT ENTITIES
    // =========================================================================

    public function test_json_requests_receive_json_error_responses_from_admin_guard(): void
    {
        // Unauthenticated JSON request receives 401 JSON
        $response = $this->getJson(route('admin.dashboard'));
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);

        // Non-admin buyer JSON request receives 403 JSON
        $this->actingAs($this->regularUser, 'admin');
        $response = $this->getJson(route('admin.dashboard'));
        $response->assertStatus(403);
        $response->assertJson(['message' => 'Unauthorized access. Administrator privileges required.']);
    }

    public function test_mutations_on_non_existent_resources_redirect_with_flash_error_without_500(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        // Non-existent seller rejection
        $response = $this->post(route('admin.sellers.reject', 99999), ['reason' => 'Non-existent seller']);
        $response->assertSessionHas('error');
        $this->assertNotEquals(500, $response->getStatusCode());

        // Non-existent product stock update
        $response = $this->post(route('admin.products.update-stock', 99999), ['stock' => 10, 'price' => 100]);
        $response->assertSessionHas('error');
        $this->assertNotEquals(500, $response->getStatusCode());

        // Non-existent category update
        $response = $this->put(route('admin.categories.update', 99999), ['name' => 'Ghost Cat']);
        $response->assertSessionHas('error');
        $this->assertNotEquals(500, $response->getStatusCode());
    }

    // =========================================================================
    // HELPER ASSERTIONS
    // =========================================================================

    protected function assertNoUnparsedBladeSyntax(string $html, string $screenName): void
    {
        $bladeDirectives = ['@if', '@foreach', '@forelse', '@endforeach', '@endforelse', '@endif', '@section', '@endsection', '@extends', '@include'];
        foreach ($bladeDirectives as $directive) {
            $this->assertStringNotContainsString(
                $directive,
                $html,
                "Screen {$screenName} rendered unparsed blade directive '{$directive}' in HTML!"
            );
        }

        // Check for unparsed raw blade echo blocks
        $this->assertDoesNotMatchRegularExpression(
            '/\{\{[^}]+\}\}/',
            $html,
            "Screen {$screenName} rendered unparsed '{{ ... }}' Blade echo expressions in HTML!"
        );
    }

    protected function assertNoBrokenAttributeLinks(string $html, string $screenName): void
    {
        // Assert no href="" or src="" (empty attributes indicating missing variables)
        $this->assertDoesNotMatchRegularExpression(
            '/\b(href|src)=["\']\s*["\']/',
            $html,
            "Screen {$screenName} contains empty href or src attribute!"
        );

        // Assert no href="undefined" or src="undefined"
        $this->assertDoesNotMatchRegularExpression(
            '/\b(href|src)=["\']undefined["\']/',
            $html,
            "Screen {$screenName} contains literal 'undefined' in href or src attribute!"
        );
    }

    protected function createTestOrder(string $orderNumber, string $status): Order
    {
        return Order::create([
            'user_id' => $this->regularUser->id,
            'order_number' => $orderNumber,
            'subtotal' => 1000.00,
            'total_amount' => 1000.00,
            'order_status' => $status,
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'delivery_full_name' => 'Regular Shopper',
            'delivery_phone' => '9830012345',
            'delivery_address_line_1' => '10 Pottery Lane',
            'delivery_city' => 'Kolkata',
            'delivery_state' => 'West Bengal',
            'delivery_country' => 'India',
            'delivery_postal_code' => '700029',
        ]);
    }
}
