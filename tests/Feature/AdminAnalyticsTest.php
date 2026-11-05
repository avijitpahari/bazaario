<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected User $sellerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Admin Test',
            'email' => 'admintest@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->regularUser = User::create([
            'name' => 'Buyer Test',
            'email' => 'buyertest@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->sellerUser = User::create([
            'name' => 'Seller Test',
            'email' => 'sellertest@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'seller',
            'status' => 'active',
        ]);
    }

    public function test_guest_cannot_access_admin_analytics(): void
    {
        $response = $this->get(route('admin.analytics.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_regular_user_cannot_access_admin_analytics(): void
    {
        $response = $this->actingAs($this->regularUser, 'admin')->get(route('admin.analytics.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_seller_cannot_access_admin_analytics(): void
    {
        $response = $this->actingAs($this->sellerUser, 'admin')->get(route('admin.analytics.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_access_analytics_under_empty_state(): void
    {
        $response = $this->actingAs($this->adminUser, 'admin')->get(route('admin.analytics.index'));

        $response->assertStatus(200);
        $response->assertSee('Platform Analytics & Intelligence');
        $response->assertSee('PLATFORM GMV');
        $response->assertSee('NET COMMISSION');
        $response->assertSee('TOTAL ORDERS');
        $response->assertSee('AVG ORDER VALUE');
    }

    public function test_admin_analytics_calculates_gmv_commission_and_timeframe_filters(): void
    {
        // Setup category, product, seller profile, and orders
        $category = Category::create([
            'name' => 'Spices & Condiments',
            'slug' => 'spices-condiments',
            'commission_rate' => 10.0,
        ]);

        $sellerProfile = SellerProfile::create([
            'user_id' => $this->sellerUser->id,
            'shop_name' => 'Bengal Organic Spices',
            'shop_slug' => 'bengal-organic-spices',
            'seller_type' => 'farmer',
            'status' => 'approved',
            'commission_rate' => 8.5,
        ]);

        $product = Product::create([
            'seller_id' => $this->sellerUser->id,
            'category_id' => $category->id,
            'name' => 'Sundarbans Pure Honey',
            'slug' => 'sundarbans-pure-honey',
            'price' => 500.0,
            'stock_quantity' => 40,
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_number' => 'BZ-TEST-001',
            'user_id' => $this->regularUser->id,
            'order_type' => 'cart',
            'order_status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => 1000.0,
            'total_amount' => 1000.0,
            'delivery_full_name' => 'Shopper Person',
            'delivery_phone' => '9876543210',
            'delivery_address_line_1' => 'Park Street 10',
            'delivery_postal_code' => '700016',
            'delivery_state' => 'West Bengal',
            'delivery_city' => 'Kolkata',
            'delivery_country' => 'India',
        ]);

        $sellerOrder = SellerOrder::create([
            'order_id' => $order->id,
            'seller_id' => $this->sellerUser->id,
            'seller_order_number' => 'SO-TEST-001',
            'subtotal' => 1000.0,
            'commission_rate' => 8.5,
            'commission_amount' => 85.0,
            'payout_amount' => 915.0,
            'status' => 'delivered',
            'created_at' => now()->subDays(2),
        ]);

        OrderItem::create([
            'seller_order_id' => $sellerOrder->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 500.0,
            'quantity' => 2,
            'total_price' => 1000.0,
        ]);

        // Test default 30d range
        $response = $this->actingAs($this->adminUser, 'admin')->get(route('admin.analytics.index', ['range' => '30d']));
        $response->assertStatus(200);
        $response->assertSee('₹1,000.00'); // GMV
        $response->assertSee('₹85.00');    // Commission
        $response->assertSee('Bengal Organic Spices');
        $response->assertSee('Spices & Condiments');

        // Test 7d range
        $response7d = $this->actingAs($this->adminUser, 'admin')->get(route('admin.analytics.index', ['range' => '7d']));
        $response7d->assertStatus(200);

        // Test 90d range
        $response90d = $this->actingAs($this->adminUser, 'admin')->get(route('admin.analytics.index', ['range' => '90d']));
        $response90d->assertStatus(200);

        // Test 1y range
        $response1y = $this->actingAs($this->adminUser, 'admin')->get(route('admin.analytics.index', ['range' => '1y']));
        $response1y->assertStatus(200);

        // Test all-time range
        $responseAll = $this->actingAs($this->adminUser, 'admin')->get(route('admin.analytics.index', ['range' => 'all']));
        $responseAll->assertStatus(200);
    }

    public function test_admin_can_export_analytics_csv(): void
    {
        $response = $this->actingAs($this->adminUser, 'admin')->get(route('admin.analytics.export', ['range' => '30d']));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('bazaario-platform-analytics-30d', (string) $response->headers->get('Content-Disposition'));
    }
}
