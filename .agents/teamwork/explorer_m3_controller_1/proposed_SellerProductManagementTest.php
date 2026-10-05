<?php

namespace Tests\Feature\Seller;

use App\Http\Controllers\Seller\SellerProductController;
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
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SellerProductManagementTest
 *
 * Comprehensive Automated Test Suite for Milestone 3: Product & Inventory Management.
 * Encompasses Tiers 1-4:
 *  - Tier 1: Core Catalog CRUD, Custom UoM Types ('kg', 'dozen', 'bundle', 'litre', 'piece', 'pack'), Image Upload & Stock Telemetry
 *  - Tier 2: Boundary Validation, Input Sanitization & Strict Multi-Tenant Tenancy Isolation
 *  - Tier 3: Agronomic Freshness Engine, Expiry Calculations, Auto-Hide Expired Listings & Safe Deletion Guardrails
 *  - Tier 4: Full Agricultural Product Lifecycle (Field to Catalog to Stock Adjustment to Expiry Transition)
 */
class SellerProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Category $fruitsCategory;
    protected Category $vegetablesCategory;

    protected function setUp(): void
    {
        parent::setUp();

        // Dynamically bind routes to SellerProductController to guarantee self-contained execution
        Route::prefix('seller')->name('seller.')->middleware(['web', 'auth:seller', 'seller'])->group(function () {
            Route::prefix('products')->name('products.')->group(function () {
                Route::get('/', [SellerProductController::class, 'index'])->name('index');
                Route::get('/create', [SellerProductController::class, 'create'])->name('create');
                Route::post('/', [SellerProductController::class, 'store'])->name('store');
                Route::get('/inventory', [SellerProductController::class, 'inventory'])->name('inventory');
                Route::post('/{product}/stock', [SellerProductController::class, 'adjustStock'])->name('adjust-stock');
                Route::get('/{product}', [SellerProductController::class, 'show'])->name('show');
                Route::get('/{product}/edit', [SellerProductController::class, 'edit'])->name('edit');
                Route::put('/{product}', [SellerProductController::class, 'update'])->name('update');
                Route::delete('/{product}', [SellerProductController::class, 'destroy'])->name('destroy');
            });
        });

        $this->fruitsCategory = Category::create([
            'name'   => 'Fresh Fruits',
            'slug'   => 'fresh-fruits',
            'status' => 'active',
        ]);

        $this->vegetablesCategory = Category::create([
            'name'   => 'Organic Vegetables',
            'slug'   => 'organic-vegetables',
            'status' => 'active',
        ]);
    }

    // =========================================================================
    // SECTION 1: TIER 1 - CORE CATALOG, CRUD & UOM MANAGEMENT (HAPPY PATH)
    // =========================================================================

    /**
     * Test 1.1: Approved seller can access product catalog index (HTTP 200).
     */
    public function test_tier1_approved_seller_can_view_product_catalog_index_with_http_200(): void
    {
        $seller = $this->createApprovedSeller();
        $this->createProductRecord($seller, 'Ratnagiri Alphonso Mango', 350.00, 50, 'kg');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('seller.products.index');
        $response->assertViewHas('products');
        $response->assertViewHas('totalCount', 1);
        $response->assertViewHas('activeCount', 1);
    }

    /**
     * Test 1.2: Approved seller can access product creation workstation (HTTP 200).
     */
    public function test_tier1_approved_seller_can_view_product_creation_workstation_with_http_200(): void
    {
        $seller = $this->createApprovedSeller();

        $response = $this->actingAs($seller, 'seller')->get(route('seller.products.create'));

        $response->assertStatus(200);
        $response->assertViewIs('seller.products.create');
        $response->assertViewHas('categories');
        $response->assertViewHas('unitTypes');
    }

    /**
     * Test 1.3: Approved seller can create product with custom unit type 'kg'.
     */
    public function test_tier1_approved_seller_can_create_product_with_custom_unit_type_kg(): void
    {
        $seller = $this->createApprovedSeller();

        $payload = [
            'name'                 => 'Organic Alphonso Mango Lot',
            'category_id'          => $this->fruitsCategory->id,
            'price'                => 350.00,
            'unit_type'            => 'kg',
            'stock'                => 45,
            'low_stock_threshold'  => 10,
            'short_description'    => 'Naturally ripened GI-tagged Ratnagiri mangoes',
            'description'          => 'Harvested in twilight and packed in organic hay crates.',
            'farm_origin'          => 'Green Valley Farm, Sector 4, Ratnagiri',
            'harvest_grade'        => 'Grade A+ Export',
            'status'               => 'active',
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);

        $response->assertRedirect(route('seller.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'seller_id'           => $seller->id,
            'name'                => 'Organic Alphonso Mango Lot',
            'unit_type'           => 'kg',
            'price'               => 350.00,
            'stock'               => 45,
            'low_stock_threshold' => 10,
            'status'              => 'active',
        ]);

        $createdProduct = Product::where('name', 'Organic Alphonso Mango Lot')->first();
        $this->assertNotNull($createdProduct);
        $this->assertStringStartsWith('bz-', strtolower($createdProduct->sku));
        $this->assertStringContainsString('organic-alphonso-mango-lot', $createdProduct->slug);
    }

    /**
     * Test 1.4: Approved seller can create products with all supported unit types (dozen, bundle, litre, piece, pack).
     */
    public function test_tier1_approved_seller_can_create_products_with_various_unit_types(): void
    {
        $seller = $this->createApprovedSeller();
        $unitTypes = ['dozen', 'bundle', 'litre', 'piece', 'pack'];

        foreach ($unitTypes as $index => $unit) {
            $name = "Agricultural Item {$unit} #" . ($index + 1);
            $payload = [
                'name'        => $name,
                'category_id' => $this->fruitsCategory->id,
                'price'       => 150.00 + ($index * 10),
                'unit_type'   => $unit,
                'stock'       => 20 + $index,
                'status'      => 'active',
            ];

            $response = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);
            $response->assertRedirect(route('seller.products.index'));

            $this->assertDatabaseHas('products', [
                'seller_id' => $seller->id,
                'name'      => $name,
                'unit_type' => $unit,
            ]);
        }

        $this->assertEquals(5, Product::where('seller_id', $seller->id)->count());
    }

    /**
     * Test 1.5: Approved seller can view own product details.
     */
    public function test_tier1_approved_seller_can_view_own_product_details(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Wood-Pressed Mustard Oil', 220.00, 30, 'litre');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.products.show', $product));

        $response->assertStatus(200);
        $response->assertViewIs('seller.products.show');
        $response->assertViewHas('product');
    }

    /**
     * Test 1.6: Approved seller can access edit workstation for own product.
     */
    public function test_tier1_approved_seller_can_view_edit_workstation_for_own_product(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Cold-Pressed Coconut Oil', 180.00, 25, 'litre');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.products.edit', $product));

        $response->assertStatus(200);
        $response->assertViewIs('seller.products.edit');
        $response->assertViewHas('product');
        $response->assertViewHas('categories');
    }

    /**
     * Test 1.7: Approved seller can update product attributes and pricing.
     */
    public function test_tier1_approved_seller_can_update_product_attributes_and_pricing(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Raw Wildflower Honey', 450.00, 15, 'litre');

        $updatePayload = [
            'name'                 => 'Pure Wildflower Forest Honey',
            'category_id'          => $this->fruitsCategory->id,
            'price'                => 495.00,
            'unit_type'            => 'litre',
            'stock'                => 28,
            'low_stock_threshold'  => 5,
            'short_description'    => 'Filtered raw forest honey without sugar syrup.',
            'status'               => 'active',
        ];

        $response = $this->actingAs($seller, 'seller')->put(route('seller.products.update', $product), $updatePayload);

        $response->assertRedirect(route('seller.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'id'    => $product->id,
            'name'  => 'Pure Wildflower Forest Honey',
            'price' => 495.00,
            'stock' => 28,
        ]);
    }

    /**
     * Test 1.8: Approved seller can access inventory telemetry table with health KPIs.
     */
    public function test_tier1_approved_seller_can_access_inventory_telemetry_table(): void
    {
        $seller = $this->createApprovedSeller();
        // 1 Healthy item (stock 50 > threshold 10)
        $this->createProductRecord($seller, 'Healthy Stock Product', 100.00, 50, 'piece', 10);
        // 1 Low stock item (stock 5 <= threshold 10)
        $this->createProductRecord($seller, 'Low Stock Product', 200.00, 5, 'piece', 10);
        // 1 Out of stock item (stock 0)
        $this->createProductRecord($seller, 'Out of Stock Product', 300.00, 0, 'piece', 10);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.products.inventory'));

        $response->assertStatus(200);
        $response->assertViewIs('seller.products.inventory');
        $response->assertViewHas('totalTracked', 3);
        $response->assertViewHas('inStockHealthy', 1);
        $response->assertViewHas('lowStockCount', 2); // 5 and 0 are <= 10
        $response->assertViewHas('outOfStockCount', 1);
    }

    /**
     * Test 1.9: Approved seller can adjust stock level via 'add', 'reduce', and 'set' operations.
     */
    public function test_tier1_approved_seller_can_adjust_stock_with_add_reduce_set_actions(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Organic Basmati Rice', 90.00, 50, 'kg');

        // 1. Action: Add 25 kg -> 75 kg
        $respAdd = $this->actingAs($seller, 'seller')->post(route('seller.products.adjust-stock', $product), [
            'action'   => 'add',
            'quantity' => 25,
            'reason'   => 'New morning harvest intake',
        ]);
        $respAdd->assertSessionHas('success');
        $this->assertEquals(75, $product->fresh()->stock);

        // 2. Action: Reduce 30 kg -> 45 kg
        $respReduce = $this->actingAs($seller, 'seller')->post(route('seller.products.adjust-stock', $product), [
            'action'   => 'reduce',
            'quantity' => 30,
            'reason'   => 'Direct farm gate purchase',
        ]);
        $respReduce->assertSessionHas('success');
        $this->assertEquals(45, $product->fresh()->stock);

        // 3. Action: Set exact 120 kg
        $respSet = $this->actingAs($seller, 'seller')->post(route('seller.products.adjust-stock', $product), [
            'action'   => 'set',
            'quantity' => 120,
            'reason'   => 'Physical warehouse inventory recount',
        ]);
        $respSet->assertSessionHas('success');
        $this->assertEquals(120, $product->fresh()->stock);
    }

    /**
     * Test 1.10: Product creation with image upload stores file on public disk and creates primary image record.
     */
    public function test_tier1_product_creation_with_image_upload_attaches_primary_image(): void
    {
        Storage::fake('public');
        $seller = $this->createApprovedSeller();

        $fakeImage = UploadedFile::fake()->image('alphonso_crate.jpg', 800, 600);

        $payload = [
            'name'        => 'Golden Alphonso Crate',
            'category_id' => $this->fruitsCategory->id,
            'price'       => 500.00,
            'unit_type'   => 'dozen',
            'stock'       => 20,
            'status'      => 'active',
            'image'       => $fakeImage,
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);

        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('name', 'Golden Alphonso Crate')->first();
        $this->assertNotNull($product);

        $imageRecord = ProductImage::where('product_id', $product->id)->first();
        $this->assertNotNull($imageRecord);
        $this->assertTrue((bool) $imageRecord->is_primary);

        Storage::disk('public')->assertExists($imageRecord->image_path);
    }

    // =========================================================================
    // SECTION 2: TIER 2 - BOUNDARY VALIDATION & MULTI-TENANT ISOLATION
    // =========================================================================

    /**
     * Test 2.1: Validation rejects product creation with negative price.
     */
    public function test_tier2_rejects_product_creation_with_negative_price(): void
    {
        $seller = $this->createApprovedSeller();

        $payload = [
            'name'        => 'Invalid Price Product',
            'category_id' => $this->fruitsCategory->id,
            'price'       => -25.00,
            'unit_type'   => 'kg',
            'stock'       => 10,
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);

        $response->assertSessionHasErrors(['price']);
        $this->assertDatabaseMissing('products', ['name' => 'Invalid Price Product']);
    }

    /**
     * Test 2.2: Validation rejects product creation with zero price.
     */
    public function test_tier2_rejects_product_creation_with_zero_price(): void
    {
        $seller = $this->createApprovedSeller();

        $payload = [
            'name'        => 'Zero Price Product',
            'category_id' => $this->fruitsCategory->id,
            'price'       => 0.00,
            'unit_type'   => 'kg',
            'stock'       => 10,
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);

        $response->assertSessionHasErrors(['price']);
    }

    /**
     * Test 2.3: Validation rejects product creation with negative stock.
     */
    public function test_tier2_rejects_product_creation_with_negative_stock(): void
    {
        $seller = $this->createApprovedSeller();

        $payload = [
            'name'        => 'Negative Stock Product',
            'category_id' => $this->fruitsCategory->id,
            'price'       => 100.00,
            'unit_type'   => 'kg',
            'stock'       => -5,
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);

        $response->assertSessionHasErrors(['stock']);
    }

    /**
     * Test 2.4: Validation rejects product creation with unsupported unit type.
     */
    public function test_tier2_rejects_product_creation_with_unsupported_unit_type(): void
    {
        $seller = $this->createApprovedSeller();

        $payload = [
            'name'        => 'Invalid UoM Product',
            'category_id' => $this->fruitsCategory->id,
            'price'       => 100.00,
            'unit_type'   => 'ton', // Not in valid list ['kg', 'dozen', 'bundle', 'litre', 'piece', 'pack']
            'stock'       => 10,
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);

        $response->assertSessionHasErrors(['unit_type']);
    }

    /**
     * Test 2.5: Validation rejects product creation when mandatory fields are missing.
     */
    public function test_tier2_rejects_product_creation_missing_required_fields(): void
    {
        $seller = $this->createApprovedSeller();

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), []);

        $response->assertSessionHasErrors(['name', 'category_id', 'price', 'unit_type', 'stock']);
    }

    /**
     * Test 2.6: Strict Tenant Isolation: Seller A cannot view edit workstation of Seller B's product (HTTP 403).
     */
    public function test_tier2_strict_tenant_isolation_seller_a_cannot_view_edit_form_of_seller_b_product(): void
    {
        $sellerA = $this->createApprovedSeller(['email' => 'sellerA@example.com']);
        $sellerB = $this->createApprovedSeller(['email' => 'sellerB@example.com']);

        $productB = $this->createProductRecord($sellerB, 'Seller B Unique Item', 200.00, 10, 'kg');

        $response = $this->actingAs($sellerA, 'seller')->get(route('seller.products.edit', $productB));

        $response->assertStatus(403);
    }

    /**
     * Test 2.7: Strict Tenant Isolation: Seller A cannot update Seller B's product (HTTP 403).
     */
    public function test_tier2_strict_tenant_isolation_seller_a_cannot_update_seller_b_product(): void
    {
        $sellerA = $this->createApprovedSeller(['email' => 'sellerA2@example.com']);
        $sellerB = $this->createApprovedSeller(['email' => 'sellerB2@example.com']);

        $productB = $this->createProductRecord($sellerB, 'Original Untouched Title', 300.00, 20, 'kg');

        $maliciousPayload = [
            'name'        => 'Hijacked Title by Seller A',
            'category_id' => $this->fruitsCategory->id,
            'price'       => 1.00,
            'unit_type'   => 'kg',
            'stock'       => 0,
        ];

        $response = $this->actingAs($sellerA, 'seller')->put(route('seller.products.update', $productB), $maliciousPayload);

        $response->assertStatus(403);
        $this->assertEquals('Original Untouched Title', $productB->fresh()->name);
        $this->assertEquals(300.00, (float) $productB->fresh()->price);
    }

    /**
     * Test 2.8: Strict Tenant Isolation: Seller A cannot delete Seller B's product (HTTP 403).
     */
    public function test_tier2_strict_tenant_isolation_seller_a_cannot_delete_seller_b_product(): void
    {
        $sellerA = $this->createApprovedSeller(['email' => 'sellerA3@example.com']);
        $sellerB = $this->createApprovedSeller(['email' => 'sellerB3@example.com']);

        $productB = $this->createProductRecord($sellerB, 'Protected Seller B Asset', 500.00, 100, 'kg');

        $response = $this->actingAs($sellerA, 'seller')->delete(route('seller.products.destroy', $productB));

        $response->assertStatus(403);
        $this->assertDatabaseHas('products', ['id' => $productB->id]);
    }

    /**
     * Test 2.9: Strict Tenant Isolation: Seller A cannot adjust stock of Seller B's product (HTTP 403).
     */
    public function test_tier2_strict_tenant_isolation_seller_a_cannot_adjust_stock_of_seller_b_product(): void
    {
        $sellerA = $this->createApprovedSeller(['email' => 'sellerA4@example.com']);
        $sellerB = $this->createApprovedSeller(['email' => 'sellerB4@example.com']);

        $productB = $this->createProductRecord($sellerB, 'Seller B Inventory Lot', 150.00, 50, 'kg');

        $response = $this->actingAs($sellerA, 'seller')->post(route('seller.products.adjust-stock', $productB), [
            'action'   => 'set',
            'quantity' => 0,
            'reason'   => 'Unauthorized stock sabotage',
        ]);

        $response->assertStatus(403);
        $this->assertEquals(50, $productB->fresh()->stock);
    }

    /**
     * Test 2.10: Strict Tenant Isolation: Catalog and Inventory views only display authenticated seller's items.
     */
    public function test_tier2_strict_tenant_isolation_catalog_and_inventory_only_displays_own_products(): void
    {
        $sellerA = $this->createApprovedSeller(['email' => 'sellerA5@example.com']);
        $sellerB = $this->createApprovedSeller(['email' => 'sellerB5@example.com']);

        $productA = $this->createProductRecord($sellerA, 'Seller A Distinct Product', 100.00, 10, 'kg');
        $productB = $this->createProductRecord($sellerB, 'Seller B Secret Product', 200.00, 20, 'kg');

        // Check Catalog View for Seller A
        $respCatalog = $this->actingAs($sellerA, 'seller')->get(route('seller.products.index'));
        $respCatalog->assertStatus(200);
        $catalogProducts = $respCatalog->viewData('products');
        $this->assertTrue($catalogProducts->contains('id', $productA->id));
        $this->assertFalse($catalogProducts->contains('id', $productB->id));

        // Check Inventory View for Seller A
        $respInv = $this->actingAs($sellerA, 'seller')->get(route('seller.products.inventory'));
        $respInv->assertStatus(200);
        $invProducts = $respInv->viewData('products');
        $this->assertTrue($invProducts->contains('id', $productA->id));
        $this->assertFalse($invProducts->contains('id', $productB->id));
    }

    /**
     * Test 2.11: Unapproved (pending) seller cannot access product management workstation.
     */
    public function test_tier2_unapproved_pending_seller_is_redirected_to_pending_gate(): void
    {
        $pendingSeller = $this->createPendingSeller();

        $response = $this->actingAs($pendingSeller, 'seller')->get(route('seller.products.index'));

        $response->assertRedirect(route('seller.pending'));
    }

    /**
     * Test 2.12: Unauthenticated guest accessing product management routes is redirected to login.
     */
    public function test_tier2_unauthenticated_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('seller.products.index'));

        $response->assertRedirect(route('login'));
    }

    // =========================================================================
    // SECTION 3: TIER 3 - FRESHNESS ENGINE & SAFE DELETION GUARDRAILS
    // =========================================================================

    /**
     * Test 3.1: Freshness Engine automatically calculates expiry_date from harvest_date + expiry_days.
     */
    public function test_tier3_freshness_engine_calculates_expiry_date_from_harvest_date_and_days(): void
    {
        $seller = $this->createApprovedSeller();

        $harvestDate = '2026-05-15';
        $expiryDays = 5;

        $payload = [
            'name'          => 'Freshly Harvested Guava Lot',
            'category_id'   => $this->fruitsCategory->id,
            'price'         => 80.00,
            'unit_type'     => 'kg',
            'stock'         => 30,
            'harvest_date'  => $harvestDate,
            'expiry_days'   => $expiryDays,
            'is_perishable' => true,
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);
        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('name', 'Freshly Harvested Guava Lot')->first();
        $this->assertNotNull($product);
        $this->assertTrue((bool) $product->is_perishable);
        $this->assertEquals('2026-05-20', $product->expiry_date->toDateString());
    }

    /**
     * Test 3.2: Perishable product past expiry date is accurately evaluated as expired and stale.
     */
    public function test_tier3_perishable_product_past_expiry_is_flagged_as_expired_and_stale(): void
    {
        $seller = $this->createApprovedSeller();

        // 1. Stale product: expired 2 days ago
        $staleProduct = Product::create([
            'seller_id'     => $seller->id,
            'category_id'   => $this->fruitsCategory->id,
            'name'          => 'Expired Organic Berries',
            'slug'          => 'expired-organic-berries-' . uniqid(),
            'unit_type'     => 'kg',
            'price'         => 120.00,
            'stock'         => 15,
            'status'        => 'active',
            'is_perishable' => true,
            'harvest_date'  => Carbon::now()->subDays(7)->toDateString(),
            'expiry_days'   => 5,
            'expiry_date'   => Carbon::now()->subDays(2)->toDateString(),
        ]);

        $this->assertTrue($staleProduct->isExpired());
        $this->assertTrue($staleProduct->isStale());

        // 2. Fresh product: expires in 4 days
        $freshProduct = Product::create([
            'seller_id'     => $seller->id,
            'category_id'   => $this->fruitsCategory->id,
            'name'          => 'Fresh Harvest Berries',
            'slug'          => 'fresh-harvest-berries-' . uniqid(),
            'unit_type'     => 'kg',
            'price'         => 150.00,
            'stock'         => 25,
            'status'        => 'active',
            'is_perishable' => true,
            'harvest_date'  => Carbon::now()->toDateString(),
            'expiry_days'   => 5,
            'expiry_date'   => Carbon::now()->addDays(4)->toDateString(),
        ]);

        $this->assertFalse($freshProduct->isExpired());
        $this->assertFalse($freshProduct->isStale());
    }

    /**
     * Test 3.3: Eloquent scopes scopeFresh() and scopeStale() accurately partition products.
     */
    public function test_tier3_fresh_and_stale_scopes_accurately_filter_catalog_items(): void
    {
        $seller = $this->createApprovedSeller();

        $staleItem = Product::create([
            'seller_id'     => $seller->id,
            'category_id'   => $this->fruitsCategory->id,
            'name'          => 'Stale Item',
            'slug'          => 'stale-item-' . uniqid(),
            'unit_type'     => 'kg',
            'price'         => 100.00,
            'stock'         => 10,
            'is_perishable' => true,
            'expiry_date'   => Carbon::now()->subDay()->toDateString(),
            'status'        => 'active',
        ]);

        $freshItem = Product::create([
            'seller_id'     => $seller->id,
            'category_id'   => $this->fruitsCategory->id,
            'name'          => 'Fresh Item',
            'slug'          => 'fresh-item-' . uniqid(),
            'unit_type'     => 'kg',
            'price'         => 120.00,
            'stock'         => 20,
            'is_perishable' => true,
            'expiry_date'   => Carbon::now()->addDays(3)->toDateString(),
            'status'        => 'active',
        ]);

        $nonPerishable = Product::create([
            'seller_id'     => $seller->id,
            'category_id'   => $this->fruitsCategory->id,
            'name'          => 'Dry Non-Perishable Pulse',
            'slug'          => 'dry-pulse-' . uniqid(),
            'unit_type'     => 'kg',
            'price'         => 90.00,
            'stock'         => 100,
            'is_perishable' => false,
            'status'        => 'active',
        ]);

        $freshIds = Product::fresh()->pluck('id')->toArray();
        $this->assertContains($freshItem->id, $freshIds);
        $this->assertContains($nonPerishable->id, $freshIds);
        $this->assertNotContains($staleItem->id, $freshIds);

        $staleIds = Product::stale()->pluck('id')->toArray();
        $this->assertContains($staleItem->id, $staleIds);
        $this->assertNotContains($freshItem->id, $staleIds);
        $this->assertNotContains($nonPerishable->id, $staleIds);
    }

    /**
     * Test 3.4: Automated Freshness Engine: Expired perishable product with auto_hide_expired=true is hidden from public catalog.
     */
    public function test_tier3_expired_perishable_product_is_auto_hidden_from_public_catalog(): void
    {
        $seller = $this->createApprovedSeller();

        $expiredAutoHideProduct = Product::create([
            'seller_id'         => $seller->id,
            'category_id'       => $this->fruitsCategory->id,
            'name'              => 'Expired Auto Hidden Peaches',
            'slug'              => 'expired-peaches-' . uniqid(),
            'unit_type'         => 'kg',
            'price'             => 140.00,
            'stock'             => 10,
            'status'            => 'active',
            'is_perishable'     => true,
            'auto_hide_expired' => true,
            'expiry_date'       => Carbon::now()->subDay()->toDateString(),
        ]);

        // scopePublicVisible should exclude this item to prevent consumer disputes
        $publicVisibleIds = Product::publicVisible()->pluck('id')->toArray();
        $this->assertNotContains($expiredAutoHideProduct->id, $publicVisibleIds);
    }

    /**
     * Test 3.5: Perishable product with auto_hide_expired=false remains visible publicly even if past expiry.
     */
    public function test_tier3_expired_perishable_with_auto_hide_disabled_remains_visible(): void
    {
        $seller = $this->createApprovedSeller();

        $expiredVisibleProduct = Product::create([
            'seller_id'         => $seller->id,
            'category_id'       => $this->fruitsCategory->id,
            'name'              => 'Discounted Overripe Pulp Mangoes',
            'slug'              => 'pulp-mangoes-' . uniqid(),
            'unit_type'         => 'kg',
            'price'             => 60.00,
            'stock'             => 50,
            'status'            => 'active',
            'is_perishable'     => true,
            'auto_hide_expired' => false, // explicitly allow discount pulping sale
            'expiry_date'       => Carbon::now()->subDay()->toDateString(),
        ]);

        $publicVisibleIds = Product::publicVisible()->pluck('id')->toArray();
        $this->assertContains($expiredVisibleProduct->id, $publicVisibleIds);
    }

    /**
     * Test 3.6: Safe Deletion Guardrail blocks deletion of product associated with active unfulfilled orders.
     */
    public function test_tier3_safe_deletion_guardrail_blocks_deletion_of_product_with_active_unfulfilled_orders(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user']);
        $product = $this->createProductRecord($seller, 'Protected Active Order Crop', 250.00, 20, 'kg');

        // Create active unfulfilled order for this product (status: 'processing')
        $order = Order::create([
            'order_number'            => 'ORD-' . uniqid(),
            'user_id'                 => $buyer->id,
            'subtotal'                => 500.00,
            'total_amount'            => 500.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'processing',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Agro Market Hub',
            'delivery_city'           => 'Contai',
            'delivery_state'          => 'West Bengal',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '721401',
        ]);

        $sellerOrder = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => 'SO-' . uniqid(),
            'subtotal'            => 500.00,
            'shipping_amount'     => 0.00,
            'commission_rate'     => 10.00,
            'commission_amount'   => 50.00,
            'payout_amount'       => 450.00,
            'status'              => 'processing',
        ]);

        OrderItem::create([
            'seller_order_id' => $sellerOrder->id,
            'product_id'      => $product->id,
            'product_name'    => $product->name,
            'sku'             => $product->sku,
            'unit_price'      => 250.00,
            'quantity'        => 2,
            'total_price'     => 500.00,
        ]);

        // Attempt deletion
        $response = $this->actingAs($seller, 'seller')->delete(route('seller.products.destroy', $product));

        // Deletion must be rejected and product must remain in database
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    /**
     * Test 3.7: Safe Deletion Guardrail blocks deletion of product listed in an active or scheduled auction.
     */
    public function test_tier3_safe_deletion_guardrail_blocks_deletion_of_product_in_active_auction(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Wholesale Auction Mango Lot', 5000.00, 200, 'kg');

        // Create live auction lot for this product
        Auction::create([
            'product_id'        => $product->id,
            'seller_id'         => $seller->sellerProfile->id,
            'starting_price'    => 4000.00,
            'reserve_price'     => 4800.00,
            'current_price'     => 4200.00,
            'minimum_increment' => 100.00,
            'starts_at'         => Carbon::now()->subHour(),
            'ends_at'           => Carbon::now()->addHours(5),
            'status'            => 'live',
        ]);

        // Attempt deletion
        $response = $this->actingAs($seller, 'seller')->delete(route('seller.products.destroy', $product));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    /**
     * Test 3.8: Safe Deletion succeeds when orders are completed/delivered and no active auctions exist.
     */
    public function test_tier3_safe_deletion_allows_deletion_when_orders_fulfilled_and_no_active_auctions(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user']);
        $product = $this->createProductRecord($seller, 'Safe to Delete Crop', 100.00, 10, 'kg');

        // Create fulfilled historical order (status: 'delivered')
        $order = Order::create([
            'order_number'            => 'ORD-HIST-' . uniqid(),
            'user_id'                 => $buyer->id,
            'subtotal'                => 100.00,
            'total_amount'            => 100.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'paid',
            'order_status'            => 'completed',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Past Address',
            'delivery_city'           => 'Contai',
            'delivery_state'          => 'West Bengal',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '721401',
        ]);

        $sellerOrder = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => 'SO-HIST-' . uniqid(),
            'subtotal'            => 100.00,
            'shipping_amount'     => 0.00,
            'commission_rate'     => 10.00,
            'commission_amount'   => 10.00,
            'payout_amount'       => 90.00,
            'status'              => 'delivered',
        ]);

        OrderItem::create([
            'seller_order_id' => $sellerOrder->id,
            'product_id'      => $product->id,
            'product_name'    => $product->name,
            'sku'             => $product->sku,
            'unit_price'      => 100.00,
            'quantity'        => 1,
            'total_price'     => 100.00,
        ]);

        // Deletion must succeed
        $response = $this->actingAs($seller, 'seller')->delete(route('seller.products.destroy', $product));

        $response->assertRedirect(route('seller.products.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    // =========================================================================
    // SECTION 4: TIER 4 - FULL AGRICULTURAL PRODUCT LIFECYCLE (END-TO-END)
    // =========================================================================

    /**
     * Test 4.1: Full agricultural product lifecycle:
     * 1. Farmer logs in as approved seller.
     * 2. Creates fresh mango lot in 'kg' with harvest date today and 5-day shelf life.
     * 3. Verifies listing appears in seller catalog.
     * 4. Verifies product is visible on public marketplace.
     * 5. Adjusts stock in warehouse (+25 kg).
     * 6. Advances time by 6 days: product expires.
     * 7. Verifies product is auto-hidden from public marketplace.
     * 8. Verifies product is flagged as stale in seller catalog.
     */
    public function test_tier4_complete_mango_harvest_lifecycle_from_field_to_market_to_expiry(): void
    {
        // Step 1: Login approved farmer
        $farmer = $this->createApprovedSeller([
            'name' => 'Kalyan Farmer',
        ], [
            'shop_name'   => 'Kalyan Organic Orchards',
            'seller_type' => 'Farmer',
        ]);

        $today = Carbon::parse('2026-05-01 08:00:00');
        Carbon::setTestNow($today);

        // Step 2: Create fresh harvest lot in 'kg' with 5-day shelf life
        $createPayload = [
            'name'                 => 'Himsagar Mango Twilight Harvest',
            'category_id'          => $this->fruitsCategory->id,
            'price'                => 220.00,
            'unit_type'            => 'kg',
            'stock'                => 50,
            'low_stock_threshold'  => 10,
            'harvest_date'         => $today->toDateString(),
            'expiry_days'          => 5,
            'is_perishable'        => true,
            'auto_hide_expired'    => true,
            'farm_origin'          => 'Malda Orchard Block B',
            'harvest_grade'        => 'Grade A Table Quality',
            'status'               => 'active',
        ];

        $respCreate = $this->actingAs($farmer, 'seller')->post(route('seller.products.store'), $createPayload);
        $respCreate->assertRedirect(route('seller.products.index'));

        $product = Product::where('name', 'Himsagar Mango Twilight Harvest')->first();
        $this->assertNotNull($product);
        $this->assertEquals('2026-05-06', $product->expiry_date->toDateString());
        $this->assertFalse($product->isExpired());

        // Step 3: Verify present in seller catalog
        $respCatalog = $this->actingAs($farmer, 'seller')->get(route('seller.products.index'));
        $respCatalog->assertStatus(200);
        $this->assertTrue($respCatalog->viewData('products')->contains('id', $product->id));

        // Step 4: Verify visible on public marketplace
        $this->assertTrue(Product::publicVisible()->where('id', $product->id)->exists());

        // Step 5: Warehouse stock adjustment (+25 kg)
        $respAdjust = $this->actingAs($farmer, 'seller')->post(route('seller.products.adjust-stock', $product), [
            'action'   => 'add',
            'quantity' => 25,
            'reason'   => 'Evening second harvest crate arrival',
        ]);
        $respAdjust->assertSessionHas('success');
        $this->assertEquals(75, $product->fresh()->stock);

        // Step 6: Advance time 6 days into future (past the 5-day expiry window)
        $futureDate = Carbon::parse('2026-05-07 10:00:00');
        Carbon::setTestNow($futureDate);

        // Re-evaluate freshness
        $product = $product->fresh();
        $this->assertTrue($product->isExpired());
        $this->assertTrue($product->isStale());

        // Step 7: Verify product is automatically hidden from public marketplace
        $this->assertFalse(Product::publicVisible()->where('id', $product->id)->exists());

        // Step 8: Verify product is flagged under stale tab in seller catalog
        $respStale = $this->actingAs($farmer, 'seller')->get(route('seller.products.index', ['tab' => 'stale']));
        $respStale->assertStatus(200);
        $staleProducts = $respStale->viewData('products');
        $this->assertTrue($staleProducts->contains('id', $product->id));

        // Restore real time
        Carbon::setTestNow();
    }

    // =========================================================================
    // HELPER FIXTURE METHODS
    // =========================================================================

    protected function createApprovedSeller(array $userAttrs = [], array $profileAttrs = []): User
    {
        $seller = User::factory()->create(array_merge([
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ], $userAttrs));

        SellerProfile::create(array_merge([
            'user_id'             => $seller->id,
            'shop_name'           => 'Verified Agro Merchant ' . $seller->id,
            'shop_slug'           => 'verified-agro-' . $seller->id,
            'seller_type'         => 'Farmer',
            'city'                => 'Contai',
            'state'               => 'West Bengal',
            'status'              => 'approved',
            'trust_score'         => 94.00,
            'commission_rate'     => 10.00,
            'address'             => 'Plot 55, Agro Hub',
            'operating_radius_km' => 25,
        ], $profileAttrs));

        return $seller;
    }

    protected function createPendingSeller(array $userAttrs = [], array $profileAttrs = []): User
    {
        $seller = User::factory()->create(array_merge([
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ], $userAttrs));

        SellerProfile::create(array_merge([
            'user_id'             => $seller->id,
            'shop_name'           => 'Pending Application Shop ' . $seller->id,
            'shop_slug'           => 'pending-application-' . $seller->id,
            'seller_type'         => 'Farmer',
            'city'                => 'Contai',
            'state'               => 'West Bengal',
            'status'              => 'pending',
            'trust_score'         => 80.00,
            'commission_rate'     => 10.00,
            'address'             => 'Pending Address',
            'operating_radius_km' => 20,
        ], $profileAttrs));

        return $seller;
    }

    protected function createProductRecord(User $seller, string $name, float $price, int $stock, string $unitType = 'kg', int $threshold = 10, string $status = 'active'): Product
    {
        return Product::create([
            'seller_id'           => $seller->id,
            'category_id'         => $this->fruitsCategory->id,
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
