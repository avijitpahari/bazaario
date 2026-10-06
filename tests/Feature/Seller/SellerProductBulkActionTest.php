<?php

namespace Tests\Feature\Seller;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SellerProductBulkActionTest extends TestCase
{
    use RefreshDatabase, SellerTestHelperTrait;

    protected User $seller;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = $this->createApprovedSeller();

        $this->category = Category::create([
            'name' => 'Organic Produce',
            'slug' => 'organic-produce',
            'status' => 'active',
        ]);
    }

    protected function createProduct(User $seller, array $overrides = []): Product
    {
        return Product::create(array_merge([
            'seller_id' => $seller->id,
            'category_id' => $this->category->id,
            'name' => 'Organic Apples ' . Str::random(5),
            'slug' => 'organic-apples-' . Str::random(8),
            'description' => 'Crisp local apples',
            'price' => 50.00,
            'stock' => 100,
            'status' => 'active',
            'unit_type' => 'kg',
        ], $overrides));
    }

    public function test_seller_can_bulk_activate_draft_products(): void
    {
        $p1 = $this->createProduct($this->seller, ['status' => 'draft']);
        $p2 = $this->createProduct($this->seller, ['status' => 'draft']);

        $response = $this->actingAs($this->seller, 'seller')
            ->post(route('seller.products.bulk'), [
                'action' => 'activate',
                'product_ids' => [$p1->id, $p2->id],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('active', $p1->fresh()->status);
        $this->assertEquals('active', $p2->fresh()->status);
    }

    public function test_seller_can_bulk_deactivate_products(): void
    {
        $p1 = $this->createProduct($this->seller, ['status' => 'active']);
        $p2 = $this->createProduct($this->seller, ['status' => 'active']);

        $response = $this->actingAs($this->seller, 'seller')
            ->post(route('seller.products.bulk'), [
                'action' => 'deactivate',
                'product_ids' => [$p1->id, $p2->id],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('draft', $p1->fresh()->status);
        $this->assertEquals('draft', $p2->fresh()->status);
    }

    public function test_seller_can_bulk_archive_products(): void
    {
        $p1 = $this->createProduct($this->seller, ['status' => 'active']);
        $p2 = $this->createProduct($this->seller, ['status' => 'active']);

        $response = $this->actingAs($this->seller, 'seller')
            ->post(route('seller.products.bulk'), [
                'action' => 'archive',
                'product_ids' => [$p1->id, $p2->id],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('archived', $p1->fresh()->status);
        $this->assertEquals('archived', $p2->fresh()->status);
    }

    public function test_seller_can_bulk_delete_products_without_active_orders(): void
    {
        $p1 = $this->createProduct($this->seller);
        $p2 = $this->createProduct($this->seller);

        $response = $this->actingAs($this->seller, 'seller')
            ->post(route('seller.products.bulk'), [
                'action' => 'delete',
                'product_ids' => [$p1->id, $p2->id],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('products', ['id' => $p1->id]);
        $this->assertDatabaseMissing('products', ['id' => $p2->id]);
    }

    public function test_seller_cannot_bulk_delete_products_with_active_orders_or_auctions(): void
    {
        $pSafe = $this->createProduct($this->seller);
        $pWithOrder = $this->createProduct($this->seller);

        $buyer = User::factory()->create(['role' => 'user']);

        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'user_id' => $buyer->id,
            'subtotal' => 50.00,
            'total_amount' => 50.00,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'order_status' => 'processing',
            'delivery_full_name' => $buyer->name,
            'delivery_phone' => '9876543210',
            'delivery_address_line_1' => 'Agro Market Hub',
            'delivery_city' => 'Contai',
            'delivery_state' => 'West Bengal',
            'delivery_country' => 'India',
            'delivery_postal_code' => '721401',
        ]);

        $sellerOrder = SellerOrder::create([
            'order_id' => $order->id,
            'seller_id' => $this->seller->id,
            'seller_order_number' => 'SO-' . uniqid(),
            'subtotal' => 50.00,
            'shipping_amount' => 0.00,
            'commission_rate' => 10.00,
            'commission_amount' => 5.00,
            'payout_amount' => 45.00,
            'status' => 'processing',
        ]);

        OrderItem::create([
            'seller_order_id' => $sellerOrder->id,
            'product_id' => $pWithOrder->id,
            'product_name' => $pWithOrder->name,
            'sku' => $pWithOrder->sku,
            'quantity' => 1,
            'unit_price' => 50.00,
            'total_price' => 50.00,
        ]);

        $response = $this->actingAs($this->seller, 'seller')
            ->post(route('seller.products.bulk'), [
                'action' => 'delete',
                'product_ids' => [$pSafe->id, $pWithOrder->id],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('products', ['id' => $pSafe->id]);
        $this->assertDatabaseHas('products', ['id' => $pWithOrder->id]);
    }

    public function test_strict_multi_tenant_isolation_cannot_bulk_mutate_other_seller_products(): void
    {
        $otherSeller = $this->createApprovedSeller();

        $otherProduct = Product::create([
            'seller_id' => $otherSeller->id,
            'category_id' => $this->category->id,
            'name' => 'Other Seller Item',
            'slug' => 'other-seller-item-' . Str::random(6),
            'price' => 99.00,
            'stock' => 50,
            'status' => 'active',
            'unit_type' => 'piece',
        ]);

        $response = $this->actingAs($this->seller, 'seller')
            ->post(route('seller.products.bulk'), [
                'action' => 'deactivate',
                'product_ids' => [$otherProduct->id],
            ]);

        $response->assertRedirect();
        $this->assertEquals('active', $otherProduct->fresh()->status);
    }

    public function test_validation_fails_on_empty_product_ids_or_invalid_action(): void
    {
        $response = $this->actingAs($this->seller, 'seller')
            ->post(route('seller.products.bulk'), [
                'action' => 'invalid_action',
                'product_ids' => [],
            ]);

        $response->assertSessionHasErrors(['action', 'product_ids']);
    }
}
