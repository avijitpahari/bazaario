<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payout;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SellerAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyerUser;
    protected User $sellerA;
    protected User $sellerB;
    protected SellerProfile $profileA;
    protected SellerProfile $profileB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyerUser = User::create([
            'name' => 'Buyer Test',
            'email' => 'buyer@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->sellerA = User::create([
            'name' => 'Merchant Alpha',
            'email' => 'alpha@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'seller',
            'status' => 'active',
        ]);

        $this->profileA = SellerProfile::create([
            'user_id' => $this->sellerA->id,
            'shop_name' => 'Alpha Handlooms',
            'shop_slug' => 'alpha-handlooms',
            'seller_type' => 'artisan',
            'status' => 'approved',
            'commission_rate' => 8.5,
        ]);

        $this->sellerB = User::create([
            'name' => 'Merchant Beta',
            'email' => 'beta@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'seller',
            'status' => 'active',
        ]);

        $this->profileB = SellerProfile::create([
            'user_id' => $this->sellerB->id,
            'shop_name' => 'Beta Pottery',
            'shop_slug' => 'beta-pottery',
            'seller_type' => 'artisan',
            'status' => 'approved',
            'commission_rate' => 8.5,
        ]);
    }

    public function test_guest_cannot_access_seller_analytics(): void
    {
        $response = $this->get(route('seller.analytics.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_customer_cannot_access_seller_analytics(): void
    {
        $response = $this->actingAs($this->buyerUser, 'seller')->get(route('seller.analytics.index'));
        $response->assertRedirect(route('products.index'));
    }

    public function test_unapproved_seller_is_redirected_to_pending(): void
    {
        $unapprovedSeller = User::create([
            'name' => 'Pending Merchant',
            'email' => 'pending@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'seller',
            'status' => 'active',
        ]);

        SellerProfile::create([
            'user_id' => $unapprovedSeller->id,
            'shop_name' => 'Pending Store',
            'shop_slug' => 'pending-store',
            'seller_type' => 'artisan',
            'status' => 'pending',
            'commission_rate' => 8.5,
        ]);

        $response = $this->actingAs($unapprovedSeller, 'seller')->get(route('seller.analytics.index'));
        $response->assertRedirect(route('seller.pending'));
    }

    public function test_approved_seller_can_access_analytics_under_empty_state(): void
    {
        $response = $this->actingAs($this->sellerA, 'seller')->get(route('seller.analytics.index'));

        $response->assertStatus(200);
        $response->assertSee('Store Analytics');
        $response->assertSee('GROSS SALES');
        $response->assertSee('NET EARNINGS');
        $response->assertSee('MARKETPLACE FEE');
        $response->assertSee('FULFILLMENT');
        $response->assertSee('UNIT ECONOMICS');
    }

    public function test_strict_multi_tenant_isolation_between_sellers(): void
    {
        $category = Category::create([
            'name' => 'Handmade Crafts',
            'slug' => 'handmade-crafts',
            'commission_rate' => 8.5,
        ]);

        $productA = Product::create([
            'seller_id' => $this->sellerA->id,
            'category_id' => $category->id,
            'name' => 'Silk Jamdani Scarf',
            'slug' => 'silk-jamdani-scarf',
            'price' => 3000.0,
            'stock_quantity' => 15,
            'is_active' => true,
        ]);

        $productB = Product::create([
            'seller_id' => $this->sellerB->id,
            'category_id' => $category->id,
            'name' => 'Terracotta Tea Pot',
            'slug' => 'terracotta-tea-pot',
            'price' => 800.0,
            'stock_quantity' => 25,
            'is_active' => true,
        ]);

        $orderA = Order::create([
            'order_number' => 'BZ-ORD-A',
            'user_id' => $this->buyerUser->id,
            'order_type' => 'cart',
            'order_status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => 3000.0,
            'total_amount' => 3000.0,
            'delivery_full_name' => 'Buyer Alpha',
            'delivery_phone' => '9876543210',
            'delivery_address_line_1' => 'Street A',
            'delivery_postal_code' => '700001',
            'delivery_state' => 'West Bengal',
            'delivery_city' => 'Kolkata',
            'delivery_country' => 'India',
        ]);

        $soA = SellerOrder::create([
            'order_id' => $orderA->id,
            'seller_id' => $this->sellerA->id,
            'seller_order_number' => 'SOA-001',
            'subtotal' => 3000.0,
            'commission_rate' => 8.5,
            'commission_amount' => 255.0,
            'payout_amount' => 2745.0,
            'status' => 'delivered',
            'created_at' => now()->subDay(),
        ]);

        OrderItem::create([
            'seller_order_id' => $soA->id,
            'product_id' => $productA->id,
            'product_name' => $productA->name,
            'unit_price' => 3000.0,
            'quantity' => 1,
            'total_price' => 3000.0,
        ]);

        $orderB = Order::create([
            'order_number' => 'BZ-ORD-B',
            'user_id' => $this->buyerUser->id,
            'order_type' => 'cart',
            'order_status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => 800.0,
            'total_amount' => 800.0,
            'delivery_full_name' => 'Buyer Beta',
            'delivery_phone' => '9876543211',
            'delivery_address_line_1' => 'Street B',
            'delivery_postal_code' => '700002',
            'delivery_state' => 'West Bengal',
            'delivery_city' => 'Howrah',
            'delivery_country' => 'India',
        ]);

        $soB = SellerOrder::create([
            'order_id' => $orderB->id,
            'seller_id' => $this->sellerB->id,
            'seller_order_number' => 'SOB-001',
            'subtotal' => 800.0,
            'commission_rate' => 8.5,
            'commission_amount' => 68.0,
            'payout_amount' => 732.0,
            'status' => 'delivered',
            'created_at' => now()->subDay(),
        ]);

        OrderItem::create([
            'seller_order_id' => $soB->id,
            'product_id' => $productB->id,
            'product_name' => $productB->name,
            'unit_price' => 800.0,
            'quantity' => 1,
            'total_price' => 800.0,
        ]);

        // Seller A checks analytics: sees ₹3,000.00, sees Silk Jamdani Scarf, does NOT see Terracotta Tea Pot or ₹800.00
        $responseA = $this->actingAs($this->sellerA, 'seller')->get(route('seller.analytics.index'));
        $responseA->assertStatus(200);
        $responseA->assertSee('₹3,000.00');
        $responseA->assertSee('₹2,745.00'); // net earnings
        $responseA->assertSee('Silk Jamdani Scarf');
        $responseA->assertDontSee('Terracotta Tea Pot');

        // Seller B checks analytics: sees ₹800.00, sees Terracotta Tea Pot, does NOT see Silk Jamdani Scarf or ₹3,000.00
        $responseB = $this->actingAs($this->sellerB, 'seller')->get(route('seller.analytics.index'));
        $responseB->assertStatus(200);
        $responseB->assertSee('₹800.00');
        $responseB->assertSee('₹732.00'); // net earnings
        $responseB->assertSee('Terracotta Tea Pot');
        $responseB->assertDontSee('Silk Jamdani Scarf');
    }

    public function test_seller_can_export_analytics_csv(): void
    {
        $response = $this->actingAs($this->sellerA, 'seller')->get(route('seller.analytics.export', ['range' => '30d']));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('bazaario-seller-analytics-30d', (string) $response->headers->get('Content-Disposition'));
    }
}
