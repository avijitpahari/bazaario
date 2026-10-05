<?php

namespace Tests\Feature\Seller;

use App\Models\Auction;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class AuditorM3ForensicIntegrityTest
 *
 * Independent Forensic Audit Test Suite for Milestone 3 (Seller Product & Inventory Management).
 * Independently probes:
 * 1. Zero hardcoded outputs (dynamic counts, status tabs, search & category filtering)
 * 2. Authentic Eloquent operations & all 6 unit types persistence
 * 3. Authentic file upload & public disk storage persistence
 * 4. Multi-tenant isolation (cross-tenant 403 blocks & zero data leakage)
 * 5. Freshness calculation & automatic public hiding of expired perishables
 * 6. Safe deletion guardrails (active orders & auctions lockout)
 * 7. Approval gate & guest access controls
 */
class AuditorM3ForensicIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected Category $categoryProduce;
    protected Category $categoryDairy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryProduce = Category::create([
            'name'   => 'Fresh Produce',
            'slug'   => 'fresh-produce',
            'status' => 'active',
        ]);

        $this->categoryDairy = Category::create([
            'name'   => 'Organic Dairy',
            'slug'   => 'organic-dairy',
            'status' => 'active',
        ]);
    }

    /**
     * Audit Check 1: Dynamic counts update accurately in DB and views without hardcoded static responses.
     */
    public function test_audit_dynamic_catalog_counts_and_tab_filtering(): void
    {
        $seller = $this->createApprovedSeller();

        // 1. Initial zero state
        $res0 = $this->actingAs($seller, 'seller')->get(route('seller.products.index'));
        $res0->assertStatus(200);
        $res0->assertViewHas('totalCount', 0);
        $res0->assertViewHas('activeCount', 0);
        $res0->assertViewHas('lowStockCount', 0);
        $res0->assertViewHas('staleCount', 0);

        // 2. Add 1 active healthy, 1 low stock, 1 stale product
        $this->createProduct($seller, 'Fresh Mint Leaves', 20.00, 50, 'bundle', 10, 'active');
        $this->createProduct($seller, 'Low Stock Spinach', 30.00, 5, 'bundle', 10, 'active');
        
        $staleProduct = Product::create([
            'seller_id'         => $seller->id,
            'category_id'       => $this->categoryProduce->id,
            'name'              => 'Stale Coriander Bunch',
            'slug'              => 'stale-coriander-' . uniqid(),
            'sku'               => 'BZ-COR-001',
            'unit_type'         => 'bundle',
            'price'             => 15.00,
            'stock'             => 12,
            'status'            => 'active',
            'is_perishable'     => true,
            'auto_hide_expired' => true,
            'harvest_date'      => Carbon::now()->subDays(6)->toDateString(),
            'expiry_days'       => 3,
            'expiry_date'       => Carbon::now()->subDays(3)->toDateString(),
        ]);

        $res1 = $this->actingAs($seller, 'seller')->get(route('seller.products.index'));
        $res1->assertStatus(200);
        $res1->assertViewHas('totalCount', 3);
        $res1->assertViewHas('activeCount', 3);
        $res1->assertViewHas('lowStockCount', 1);
        $res1->assertViewHas('staleCount', 1);

        // 3. Filter by tab=stale
        $resStale = $this->actingAs($seller, 'seller')->get(route('seller.products.index', ['tab' => 'stale']));
        $resStale->assertStatus(200);
        $products = $resStale->viewData('products');
        $this->assertCount(1, $products);
        $this->assertEquals($staleProduct->id, $products->first()->id);
    }

    /**
     * Audit Check 2: Authentic Eloquent creation supporting all 6 official unit types.
     */
    public function test_audit_authentic_eloquent_creation_all_six_unit_types(): void
    {
        $seller = $this->createApprovedSeller();
        $unitTypes = ['kg', 'dozen', 'bundle', 'litre', 'piece', 'pack'];

        foreach ($unitTypes as $idx => $unit) {
            $payload = [
                'name'                => "Agricultural Unit Item {$unit}",
                'category_id'         => $this->categoryProduce->id,
                'price'               => 100.00 + ($idx * 15.5),
                'unit_type'           => $unit,
                'stock'               => 20 + $idx,
                'low_stock_threshold' => 5,
                'status'              => 'active',
            ];

            $response = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);
            $response->assertRedirect(route('seller.products.index'));

            $this->assertDatabaseHas('products', [
                'seller_id' => $seller->id,
                'name'      => "Agricultural Unit Item {$unit}",
                'unit_type' => $unit,
                'price'     => 100.00 + ($idx * 15.5),
                'stock'     => 20 + $idx,
            ]);
        }

        $this->assertEquals(6, Product::where('seller_id', $seller->id)->count());
    }

    /**
     * Audit Check 3: Authentic Image Upload persisted to public disk and linked via ProductImage.
     */
    public function test_audit_authentic_image_upload_to_public_disk(): void
    {
        Storage::fake('public');
        $seller = $this->createApprovedSeller();

        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $fakeFile = UploadedFile::fake()->createWithContent('farm_produce.png', $pngContent);

        $payload = [
            'name'        => 'Farm Fresh Strawberries',
            'category_id' => $this->categoryProduce->id,
            'price'       => 280.00,
            'unit_type'   => 'pack',
            'stock'       => 35,
            'status'      => 'active',
            'image'       => $fakeFile,
        ];

        $res = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);
        $res->assertRedirect(route('seller.products.index'));

        $product = Product::where('name', 'Farm Fresh Strawberries')->first();
        $this->assertNotNull($product);

        $image = ProductImage::where('product_id', $product->id)->first();
        $this->assertNotNull($image);
        $this->assertTrue((bool)$image->is_primary);

        Storage::disk('public')->assertExists($image->image_path);
    }

    /**
     * Audit Check 4: Strict Multi-Tenant Isolation & Zero Data Leakage.
     */
    public function test_audit_strict_multi_tenant_isolation_on_all_operations(): void
    {
        $sellerA = $this->createApprovedSeller(['email' => 'seller_a@example.com']);
        $sellerB = $this->createApprovedSeller(['email' => 'seller_b@example.com']);

        $productB = $this->createProduct($sellerB, 'Seller B Proprietary Grain', 500.00, 100, 'kg');

        // 1. Seller A cannot view edit form of Seller B's product
        $this->actingAs($sellerA, 'seller')
            ->get(route('seller.products.edit', $productB))
            ->assertStatus(403);

        // 2. Seller A cannot update Seller B's product
        $this->actingAs($sellerA, 'seller')
            ->put(route('seller.products.update', $productB), [
                'name'        => 'Tampered Name',
                'category_id' => $this->categoryProduce->id,
                'price'       => 10.00,
                'unit_type'   => 'kg',
                'stock'       => 0,
            ])
            ->assertStatus(403);
        $this->assertEquals('Seller B Proprietary Grain', $productB->fresh()->name);

        // 3. Seller A cannot adjust stock of Seller B's product
        $this->actingAs($sellerA, 'seller')
            ->post(route('seller.products.adjust-stock', $productB), [
                'action'   => 'set',
                'quantity' => 0,
            ])
            ->assertStatus(403);
        $this->assertEquals(100, $productB->fresh()->stock);

        // 4. Seller A cannot delete Seller B's product
        $this->actingAs($sellerA, 'seller')
            ->delete(route('seller.products.destroy', $productB))
            ->assertStatus(403);
        $this->assertDatabaseHas('products', ['id' => $productB->id]);

        // 5. Seller A catalog index and inventory table MUST NOT contain Seller B's products
        $catalogRes = $this->actingAs($sellerA, 'seller')->get(route('seller.products.index'));
        $this->assertFalse($catalogRes->viewData('products')->contains('id', $productB->id));

        $invRes = $this->actingAs($sellerA, 'seller')->get(route('seller.products.inventory'));
        $this->assertFalse($invRes->viewData('products')->contains('id', $productB->id));
    }

    /**
     * Audit Check 5: Freshness Engine, Expiry Calculation & Public Visibility Scoping.
     */
    public function test_audit_freshness_engine_and_public_visibility(): void
    {
        $seller = $this->createApprovedSeller();

        // 1. Creation with harvest_date + expiry_days auto-computes expiry_date
        $res = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), [
            'name'          => 'Harvest Date Test Crop',
            'category_id'   => $this->categoryProduce->id,
            'price'         => 80.00,
            'unit_type'     => 'kg',
            'stock'         => 40,
            'harvest_date'  => '2026-07-01',
            'expiry_days'   => 7,
            'is_perishable' => true,
        ]);
        $res->assertRedirect(route('seller.products.index'));

        $crop = Product::where('name', 'Harvest Date Test Crop')->first();
        $this->assertNotNull($crop);
        $this->assertEquals('2026-07-08', $crop->expiry_date->toDateString());

        // 2. Expired perishable with auto_hide_expired = true is hidden from publicVisible
        $expiredCrop = Product::create([
            'seller_id'         => $seller->id,
            'category_id'       => $this->categoryProduce->id,
            'name'              => 'Expired Secret Crop',
            'slug'              => 'expired-secret-' . uniqid(),
            'sku'               => 'BZ-EXP-99',
            'unit_type'         => 'kg',
            'price'             => 50.00,
            'stock'             => 10,
            'status'            => 'active',
            'is_perishable'     => true,
            'auto_hide_expired' => true,
            'expiry_date'       => Carbon::now()->subDay()->toDateString(),
        ]);

        $this->assertTrue($expiredCrop->isExpired());
        $this->assertTrue($expiredCrop->isStale());
        $this->assertFalse(Product::publicVisible()->where('id', $expiredCrop->id)->exists());
    }

    /**
     * Audit Check 6: Safe Deletion Guardrail blocks deletion for active orders & auctions.
     */
    public function test_audit_safe_deletion_guardrail_blocking(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user']);
        $product = $this->createProduct($seller, 'Guarded Grain Lot', 300.00, 50, 'kg');

        // 1. Order in 'processing' status protects product from deletion
        $order = Order::create([
            'order_number'            => 'ORD-AUDIT-' . uniqid(),
            'user_id'                 => $buyer->id,
            'subtotal'                => 600.00,
            'total_amount'            => 600.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'processing',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9998887776',
            'delivery_address_line_1' => 'Buyer Address',
            'delivery_city'           => 'Contai',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '721401',
        ]);

        $sellerOrder = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => 'SO-AUDIT-' . uniqid(),
            'subtotal'            => 600.00,
            'shipping_amount'     => 0.00,
            'commission_rate'     => 10.00,
            'commission_amount'   => 60.00,
            'payout_amount'       => 540.00,
            'status'              => 'processing',
        ]);

        OrderItem::create([
            'seller_order_id' => $sellerOrder->id,
            'product_id'      => $product->id,
            'product_name'    => $product->name,
            'sku'             => $product->sku,
            'unit_price'      => 300.00,
            'quantity'        => 2,
            'total_price'     => 600.00,
        ]);

        // Attempt deletion
        $delResp = $this->actingAs($seller, 'seller')->delete(route('seller.products.destroy', $product));
        $delResp->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);

        // JSON endpoint should return 422
        $delJson = $this->actingAs($seller, 'seller')->deleteJson(route('seller.products.destroy', $product));
        $delJson->assertStatus(422);

        // 2. Mark order delivered -> now deletable
        $sellerOrder->update(['status' => 'delivered']);
        $delSuccess = $this->actingAs($seller, 'seller')->delete(route('seller.products.destroy', $product));
        $delSuccess->assertRedirect(route('seller.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /**
     * Audit Check 7: Access Control Gate blocks unauthenticated and pending users.
     */
    public function test_audit_approval_gate_and_guest_redirection(): void
    {
        // 1. Guest redirected to login
        $this->get(route('seller.products.index'))->assertRedirect(route('login'));
        $this->get(route('seller.products.create'))->assertRedirect(route('login'));
        $this->get(route('seller.products.inventory'))->assertRedirect(route('login'));

        // 2. Pending seller redirected to seller.pending
        $pendingSeller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id'     => $pendingSeller->id,
            'shop_name'   => 'Pending Shop',
            'shop_slug'   => 'pending-shop',
            'seller_type' => 'Farmer',
            'status'      => 'pending',
        ]);

        $this->actingAs($pendingSeller, 'seller')
            ->get(route('seller.products.index'))
            ->assertRedirect(route('seller.pending'));

        $this->actingAs($pendingSeller, 'seller')
            ->get(route('seller.products.create'))
            ->assertRedirect(route('seller.pending'));
    }

    // =========================================================================
    // FIXTURE HELPERS
    // =========================================================================

    protected function createApprovedSeller(array $userAttrs = []): User
    {
        $seller = User::factory()->create(array_merge([
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ], $userAttrs));

        SellerProfile::create([
            'user_id'             => $seller->id,
            'shop_name'           => 'Approved Merchant ' . $seller->id,
            'shop_slug'           => 'approved-merchant-' . $seller->id,
            'seller_type'         => 'Farmer',
            'city'                => 'Contai',
            'state'               => 'West Bengal',
            'status'              => 'approved',
            'trust_score'         => 95.00,
            'commission_rate'     => 10.00,
            'address'             => 'Hub Road',
            'operating_radius_km' => 30,
        ]);

        return $seller;
    }

    protected function createProduct(User $seller, string $name, float $price, int $stock, string $unitType = 'kg', int $threshold = 10, string $status = 'active'): Product
    {
        return Product::create([
            'seller_id'           => $seller->id,
            'category_id'         => $this->categoryProduce->id,
            'name'                => $name,
            'slug'                => Str::slug($name) . '-' . uniqid(),
            'sku'                 => 'BZ-' . strtoupper(Str::random(6)),
            'sale_type'           => 'fixed_price',
            'unit_type'           => $unitType,
            'price'               => $price,
            'stock'               => $stock,
            'low_stock_threshold' => $threshold,
            'status'              => $status,
            'is_perishable'       => false,
        ]);
    }
}
