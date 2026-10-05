<?php

namespace Tests\Feature\Seller;

use App\Http\Controllers\Seller\SellerProductController;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SellerProductInventoryEmpiricalTest
 *
 * Empirical Challenge Test Suite for Milestone 3:
 * - High-volume stock adjustments (50 rapid sequential adjustments with exact arithmetic assertions)
 * - Negative stock deduction attempts (rejection of negative inputs, safe clamping to zero)
 * - Exact low-stock boundary alerts (threshold = 10; 11 normal, 10 low, 4 critical)
 * - Custom unit types across all 6 valid unit types ('kg', 'dozen', 'bundle', 'litre', 'piece', 'pack')
 * - Tenancy isolation and security guardrails on stock telemetry
 */
class SellerProductInventoryEmpiricalTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure routes are bound for the controller under test
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

        $this->category = Category::create([
            'name'   => 'Empirical Agro Test Category',
            'slug'   => 'empirical-agro-test-category',
            'status' => 'active',
        ]);
    }

    // =========================================================================
    // SECTION 1: HIGH VOLUME STOCK ADJUSTMENTS (50 RAPID SEQUENTIAL ADJUSTMENTS)
    // =========================================================================

    /**
     * Test 1.1: 50 rapid sequential stock adjustments execute deterministically without drift.
     * Evaluates a pseudo-random mixed sequence of add, reduce, and set actions.
     */
    public function test_50_rapid_sequential_stock_adjustments_maintain_exact_arithmetic_integrity(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'High Volume Test Mangoes', 300.00, 100, 'kg', 10);

        $expectedStock = 100;
        $operations = [
            ['action' => 'add', 'quantity' => 15],
            ['action' => 'reduce', 'quantity' => 10],
            ['action' => 'add', 'quantity' => 25],
            ['action' => 'reduce', 'quantity' => 30],
            ['action' => 'set', 'quantity' => 120],
            ['action' => 'reduce', 'quantity' => 20],
            ['action' => 'add', 'quantity' => 5],
            ['action' => 'reduce', 'quantity' => 15],
            ['action' => 'add', 'quantity' => 50],
            ['action' => 'reduce', 'quantity' => 45],
            ['action' => 'set', 'quantity' => 80],
            ['action' => 'add', 'quantity' => 10],
            ['action' => 'add', 'quantity' => 12],
            ['action' => 'reduce', 'quantity' => 22],
            ['action' => 'add', 'quantity' => 30],
            ['action' => 'reduce', 'quantity' => 10],
            ['action' => 'set', 'quantity' => 95],
            ['action' => 'reduce', 'quantity' => 5],
            ['action' => 'add', 'quantity' => 15],
            ['action' => 'reduce', 'quantity' => 20],
            ['action' => 'set', 'quantity' => 50],
            ['action' => 'add', 'quantity' => 25],
            ['action' => 'reduce', 'quantity' => 10],
            ['action' => 'add', 'quantity' => 10],
            ['action' => 'reduce', 'quantity' => 35],
            ['action' => 'set', 'quantity' => 150],
            ['action' => 'reduce', 'quantity' => 50],
            ['action' => 'add', 'quantity' => 10],
            ['action' => 'reduce', 'quantity' => 20],
            ['action' => 'add', 'quantity' => 5],
            ['action' => 'set', 'quantity' => 60],
            ['action' => 'add', 'quantity' => 40],
            ['action' => 'reduce', 'quantity' => 15],
            ['action' => 'reduce', 'quantity' => 25],
            ['action' => 'add', 'quantity' => 30],
            ['action' => 'set', 'quantity' => 70],
            ['action' => 'reduce', 'quantity' => 10],
            ['action' => 'add', 'quantity' => 20],
            ['action' => 'reduce', 'quantity' => 15],
            ['action' => 'add', 'quantity' => 5],
            ['action' => 'set', 'quantity' => 110],
            ['action' => 'reduce', 'quantity' => 40],
            ['action' => 'add', 'quantity' => 15],
            ['action' => 'reduce', 'quantity' => 25],
            ['action' => 'add', 'quantity' => 10],
            ['action' => 'set', 'quantity' => 85],
            ['action' => 'reduce', 'quantity' => 35],
            ['action' => 'add', 'quantity' => 50],
            ['action' => 'reduce', 'quantity' => 20],
            ['action' => 'set', 'quantity' => 100],
        ];

        $this->assertCount(50, $operations);

        foreach ($operations as $index => $op) {
            $oldStockBeforeOp = $expectedStock;

            if ($op['action'] === 'add') {
                $expectedStock += $op['quantity'];
            } elseif ($op['action'] === 'reduce') {
                $expectedStock = max(0, $expectedStock - $op['quantity']);
            } elseif ($op['action'] === 'set') {
                $expectedStock = max(0, $op['quantity']);
            }

            $response = $this->actingAs($seller, 'seller')
                ->postJson(route('seller.products.adjust-stock', $product), [
                    'action'   => $op['action'],
                    'quantity' => $op['quantity'],
                    'reason'   => "Batch sequence adjustment step #{$index}",
                ]);

            $response->assertStatus(200);
            $response->assertJson([
                'success'    => true,
                'old_stock'  => $oldStockBeforeOp,
                'new_stock'  => $expectedStock,
                'product_id' => $product->id,
            ]);

            // Database verification at step
            $currentDbStock = (int) Product::find($product->id)->stock;
            $this->assertEquals(
                $expectedStock,
                $currentDbStock,
                "Stock divergence at step #{$index} ({$op['action']} {$op['quantity']}). Expected {$expectedStock}, got {$currentDbStock}"
            );
        }

        $this->assertEquals(100, $product->fresh()->stock);
    }

    /**
     * Test 1.2: 50 rapid alternating +1 / -1 sequential adjustments stress test.
     */
    public function test_50_rapid_alternating_increment_and_decrement_cycles_preserve_zero_drift(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Alternating Stress Batch', 150.00, 50, 'pack', 10);

        for ($cycle = 1; $cycle <= 25; $cycle++) {
            // +1
            $resAdd = $this->actingAs($seller, 'seller')
                ->postJson(route('seller.products.adjust-stock', $product), [
                    'action'   => 'add',
                    'quantity' => 1,
                ]);
            $resAdd->assertStatus(200);
            $resAdd->assertJson(['success' => true, 'new_stock' => 51]);

            // -1
            $resSub = $this->actingAs($seller, 'seller')
                ->postJson(route('seller.products.adjust-stock', $product), [
                    'action'   => 'reduce',
                    'quantity' => 1,
                ]);
            $resSub->assertStatus(200);
            $resSub->assertJson(['success' => true, 'new_stock' => 50]);
        }

        $this->assertEquals(50, $product->fresh()->stock);
    }

    /**
     * Test 1.3: Web form redirection adjustments with audit logging and session feedback.
     */
    public function test_web_form_stock_adjustments_flash_success_and_persist_audit(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Field Harvest Lot', 90.00, 30, 'dozen', 10);

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.products.adjust-stock', $product), [
                'action'   => 'add',
                'quantity' => 20,
                'reason'   => 'New Morning Harvest Intake',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', "Stock for 'Field Harvest Lot' updated to 50 dozen.");
        $this->assertEquals(50, $product->fresh()->stock);
    }

    // =========================================================================
    // SECTION 2: NEGATIVE STOCK REJECTION & SAFE CLAMPING TO ZERO
    // =========================================================================

    /**
     * Test 2.1: Rejection of negative quantity in 'add' action.
     */
    public function test_rejects_negative_quantity_in_add_action(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Negative Add Test Product', 200.00, 40, 'kg');

        $response = $this->actingAs($seller, 'seller')
            ->postJson(route('seller.products.adjust-stock', $product), [
                'action'   => 'add',
                'quantity' => -10,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['quantity']);
        $this->assertEquals(40, $product->fresh()->stock);
    }

    /**
     * Test 2.2: Rejection of negative quantity in 'reduce' action.
     */
    public function test_rejects_negative_quantity_in_reduce_action(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Negative Reduce Test Product', 200.00, 40, 'kg');

        $response = $this->actingAs($seller, 'seller')
            ->postJson(route('seller.products.adjust-stock', $product), [
                'action'   => 'reduce',
                'quantity' => -25,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['quantity']);
        $this->assertEquals(40, $product->fresh()->stock);
    }

    /**
     * Test 2.3: Rejection of negative quantity in 'set' action.
     */
    public function test_rejects_negative_quantity_in_set_action(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Negative Set Test Product', 200.00, 40, 'kg');

        $response = $this->actingAs($seller, 'seller')
            ->postJson(route('seller.products.adjust-stock', $product), [
                'action'   => 'set',
                'quantity' => -5,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['quantity']);
        $this->assertEquals(40, $product->fresh()->stock);
    }

    /**
     * Test 2.4: Excessive reduction below zero is safely clamped to 0 (never negative).
     */
    public function test_safe_clamping_to_zero_when_reduction_exceeds_available_stock(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Clamping Underflow Test Item', 100.00, 15, 'kg');

        // Request deduction of 40 from 15 (arithmetic 15 - 40 = -25)
        $response = $this->actingAs($seller, 'seller')
            ->postJson(route('seller.products.adjust-stock', $product), [
                'action'   => 'reduce',
                'quantity' => 40,
                'reason'   => 'Severe transit loss batch',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'    => true,
            'old_stock'  => 15,
            'new_stock'  => 0,
            'product_id' => $product->id,
        ]);

        $freshProduct = $product->fresh();
        $this->assertEquals(0, $freshProduct->stock);
        $this->assertGreaterThanOrEqual(0, $freshProduct->stock);
    }

    /**
     * Test 2.5: Reducing already depleted (0 stock) item remains safely clamped at 0.
     */
    public function test_safe_clamping_when_reducing_already_zero_stock_product(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Depleted Item', 80.00, 0, 'piece');

        $response = $this->actingAs($seller, 'seller')
            ->postJson(route('seller.products.adjust-stock', $product), [
                'action'   => 'reduce',
                'quantity' => 50,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'    => true,
            'old_stock'  => 0,
            'new_stock'  => 0,
            'product_id' => $product->id,
        ]);

        $this->assertEquals(0, $product->fresh()->stock);
    }

    /**
     * Test 2.6: Product creation workstation rejects negative stock.
     */
    public function test_product_creation_rejects_negative_stock_with_validation_error(): void
    {
        $seller = $this->createApprovedSeller();

        $payload = [
            'name'        => 'Negative Stock Product',
            'category_id' => $this->category->id,
            'price'       => 150.00,
            'unit_type'   => 'kg',
            'stock'       => -20,
        ];

        $response = $this->actingAs($seller, 'seller')
            ->postJson(route('seller.products.store'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['stock']);
        $this->assertDatabaseMissing('products', ['name' => 'Negative Stock Product']);
    }

    /**
     * Test 2.7: Product update workstation rejects negative stock.
     */
    public function test_product_update_rejects_negative_stock_with_validation_error(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Existing Valid Item', 100.00, 25, 'kg');

        $payload = [
            'name'        => 'Existing Valid Item Updated',
            'category_id' => $this->category->id,
            'price'       => 120.00,
            'unit_type'   => 'kg',
            'stock'       => -1,
        ];

        $response = $this->actingAs($seller, 'seller')
            ->putJson(route('seller.products.update', $product), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['stock']);
        $this->assertEquals(25, $product->fresh()->stock);
    }

    /**
     * Test 2.8: Boundary zero stock operations execute cleanly (identity & zero setter).
     */
    public function test_boundary_zero_quantity_and_zero_set_operations(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Boundary Zero Tester', 100.00, 30, 'kg');

        // Add 0 -> stock unchanged
        $resAddZero = $this->actingAs($seller, 'seller')
            ->postJson(route('seller.products.adjust-stock', $product), [
                'action'   => 'add',
                'quantity' => 0,
            ]);
        $resAddZero->assertStatus(200);
        $resAddZero->assertJson(['new_stock' => 30]);

        // Reduce 0 -> stock unchanged
        $resReduceZero = $this->actingAs($seller, 'seller')
            ->postJson(route('seller.products.adjust-stock', $product), [
                'action'   => 'reduce',
                'quantity' => 0,
            ]);
        $resReduceZero->assertStatus(200);
        $resReduceZero->assertJson(['new_stock' => 30]);

        // Set to 0 -> stock becomes 0
        $resSetZero = $this->actingAs($seller, 'seller')
            ->postJson(route('seller.products.adjust-stock', $product), [
                'action'   => 'set',
                'quantity' => 0,
            ]);
        $resSetZero->assertStatus(200);
        $resSetZero->assertJson(['new_stock' => 0]);
        $this->assertEquals(0, $product->fresh()->stock);
    }

    // =========================================================================
    // SECTION 3: EXACT THRESHOLD BOUNDARIES (11 NORMAL, 10 LOW, 4 CRITICAL)
    // =========================================================================

    /**
     * Test 3.1: Exact threshold boundary evaluation:
     * - Threshold = 10
     * - Stock 11 is normal (NOT low stock, NOT critical)
     * - Stock 10 is low stock (isLowStock() = true, NOT critical)
     * - Stock 5 is low stock (isLowStock() = true, NOT critical: 5 < 5 is false)
     * - Stock 4 is critical (isLowStock() = true, stock < 5 = true)
     * - Stock 0 is depleted & critical (isLowStock() = true, stock < 5 = true, stock <= 0 = true)
     */
    public function test_exact_threshold_boundary_evaluation_at_11_normal_10_low_4_critical(): void
    {
        $seller = $this->createApprovedSeller();

        $prod11 = $this->createProductRecord($seller, 'Boundary Stock 11 Item', 100.00, 11, 'kg', 10);
        $prod10 = $this->createProductRecord($seller, 'Boundary Stock 10 Item', 100.00, 10, 'kg', 10);
        $prod5  = $this->createProductRecord($seller, 'Boundary Stock 5 Item', 100.00, 5, 'kg', 10);
        $prod4  = $this->createProductRecord($seller, 'Boundary Stock 4 Item', 100.00, 4, 'kg', 10);
        $prod0  = $this->createProductRecord($seller, 'Boundary Stock 0 Item', 100.00, 0, 'kg', 10);

        // 1. Model method evaluations
        // 11 units: normal
        $this->assertFalse($prod11->isLowStock(), 'Stock 11 must NOT be low stock when threshold is 10');
        $this->assertFalse($prod11->stock < 5, 'Stock 11 must NOT be critical');

        // 10 units: low stock boundary
        $this->assertTrue($prod10->isLowStock(), 'Stock 10 MUST be low stock when threshold is 10');
        $this->assertFalse($prod10->stock < 5, 'Stock 10 must NOT be critical (<5)');

        // 5 units: low stock, but not critical
        $this->assertTrue($prod5->isLowStock(), 'Stock 5 MUST be low stock when threshold is 10');
        $this->assertFalse($prod5->stock < 5, 'Stock 5 must NOT be critical (<5 is false at 5)');

        // 4 units: critical low stock boundary
        $this->assertTrue($prod4->isLowStock(), 'Stock 4 MUST be low stock when threshold is 10');
        $this->assertTrue($prod4->stock < 5, 'Stock 4 MUST be critical (<5)');

        // 0 units: out of stock and critical
        $this->assertTrue($prod0->isLowStock(), 'Stock 0 MUST be low stock');
        $this->assertTrue($prod0->stock < 5, 'Stock 0 MUST be critical (<5)');
        $this->assertTrue($prod0->stock <= 0, 'Stock 0 MUST be out of stock');

        // 2. Query scope evaluation: scopeLowStock
        $lowStockIds = Product::where('seller_id', $seller->id)
            ->lowStock()
            ->pluck('id')
            ->all();

        $this->assertNotContains($prod11->id, $lowStockIds, 'Scope lowStock must exclude stock 11');
        $this->assertContains($prod10->id, $lowStockIds, 'Scope lowStock must include stock 10');
        $this->assertContains($prod5->id, $lowStockIds, 'Scope lowStock must include stock 5');
        $this->assertContains($prod4->id, $lowStockIds, 'Scope lowStock must include stock 4');
        $this->assertContains($prod0->id, $lowStockIds, 'Scope lowStock must include stock 0');

        // 3. Operational Dashboard KPI evaluation
        $dashResponse = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $dashResponse->assertStatus(200);

        // Low stock count = 4 (10, 5, 4, 0), critical stock count = 2 (4, 0)
        $dashResponse->assertSee('4');
        $dashResponse->assertSee('2 critical (&lt;5 units)', false);
        $dashResponse->assertViewHas('lowStockCount', 4);
        $dashResponse->assertViewHas('criticalStockCount', 2);

        // 4. Warehouse Inventory Telemetry evaluation
        $invResponse = $this->actingAs($seller, 'seller')->get(route('seller.products.inventory'));
        $invResponse->assertStatus(200);

        // Check view variables
        $invResponse->assertViewHas('totalTracked', 5);
        $invResponse->assertViewHas('inStockHealthy', 1); // Only prod11
        $invResponse->assertViewHas('lowStockCount', 4);   // prod10, prod5, prod4, prod0
        $invResponse->assertViewHas('outOfStockCount', 1); // prod0
    }

    /**
     * Test 3.2: Custom threshold boundary precision (e.g. threshold = 25 and threshold = 2).
     */
    public function test_custom_threshold_boundaries_behave_with_exact_precision(): void
    {
        $seller = $this->createApprovedSeller();

        // Custom threshold 25
        $p26 = $this->createProductRecord($seller, 'High Threshold 26', 50.00, 26, 'pack', 25);
        $p25 = $this->createProductRecord($seller, 'High Threshold 25', 50.00, 25, 'pack', 25);
        $p24 = $this->createProductRecord($seller, 'High Threshold 24', 50.00, 24, 'pack', 25);

        $this->assertFalse($p26->isLowStock(), 'Stock 26 must not be low stock when threshold is 25');
        $this->assertTrue($p25->isLowStock(), 'Stock 25 must be low stock when threshold is 25');
        $this->assertTrue($p24->isLowStock(), 'Stock 24 must be low stock when threshold is 25');

        // Custom threshold 2
        $p3 = $this->createProductRecord($seller, 'Tight Threshold 3', 800.00, 3, 'piece', 2);
        $p2 = $this->createProductRecord($seller, 'Tight Threshold 2', 800.00, 2, 'piece', 2);

        $this->assertFalse($p3->isLowStock(), 'Stock 3 must not be low stock when threshold is 2');
        $this->assertTrue($p2->isLowStock(), 'Stock 2 must be low stock when threshold is 2');
    }

    /**
     * Test 3.3: Null low_stock_threshold defaults to 10 in model logic and workstation creation.
     */
    public function test_null_low_stock_threshold_defaults_to_10_in_both_php_and_sql(): void
    {
        $seller = $this->createApprovedSeller();

        // 1. In-memory model fallback check when low_stock_threshold is null
        $model11 = new Product(['stock' => 11, 'low_stock_threshold' => null]);
        $model10 = new Product(['stock' => 10, 'low_stock_threshold' => null]);
        $this->assertFalse($model11->isLowStock(), 'Model with null threshold must default to 10 and treat 11 as not low');
        $this->assertTrue($model10->isLowStock(), 'Model with null threshold must default to 10 and treat 10 as low stock');

        // 2. Workstation store endpoint automatically defaults omitted low_stock_threshold to 10
        $payload = [
            'name'        => 'Default Threshold Crop',
            'category_id' => $this->category->id,
            'price'       => 150.00,
            'unit_type'   => 'kg',
            'stock'       => 10,
            // low_stock_threshold omitted
        ];

        $resp = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);
        $resp->assertRedirect(route('seller.products.index'));

        $created = Product::where('name', 'Default Threshold Crop')->first();
        $this->assertNotNull($created);
        $this->assertEquals(10, $created->low_stock_threshold);
        $this->assertTrue($created->isLowStock());

        // 3. Query scope includes it in low stock
        $this->assertTrue(Product::where('id', $created->id)->lowStock()->exists());
    }

    // =========================================================================
    // SECTION 4: UNIT TYPES ACROSS ALL 6 TYPES ('kg', 'dozen', 'bundle', 'litre', 'piece', 'pack')
    // =========================================================================

    /**
     * Test 4.1: Product creation workstation successfully accepts and persists all 6 valid unit types.
     */
    public function test_all_6_unit_types_are_accepted_and_persisted_cleanly(): void
    {
        $seller = $this->createApprovedSeller();

        $unitsToTest = [
            'kg'     => ['name' => 'Organic Ratnagiri Alphonso Mango', 'price' => 350.00, 'stock' => 50],
            'dozen'  => ['name' => 'Pasture-Raised Country Eggs',      'price' => 110.00, 'stock' => 40],
            'bundle' => ['name' => 'Farm Fresh Palak Spinach',         'price' => 20.00,  'stock' => 80],
            'litre'  => ['name' => 'Raw A2 Gir Cow Desi Milk',         'price' => 75.00,  'stock' => 30],
            'piece'  => ['name' => 'Tree Ripened Sweet Jackfruit',     'price' => 180.00, 'stock' => 15],
            'pack'   => ['name' => 'Hydroponic Microgreens Pack',      'price' => 95.00,  'stock' => 25],
        ];

        foreach ($unitsToTest as $unitType => $data) {
            $payload = [
                'name'                => $data['name'],
                'category_id'         => $this->category->id,
                'price'               => $data['price'],
                'unit_type'           => $unitType,
                'stock'               => $data['stock'],
                'low_stock_threshold' => 10,
                'status'              => 'active',
            ];

            $response = $this->actingAs($seller, 'seller')
                ->post(route('seller.products.store'), $payload);

            $response->assertRedirect(route('seller.products.index'));

            $this->assertDatabaseHas('products', [
                'seller_id' => $seller->id,
                'name'      => $data['name'],
                'unit_type' => $unitType,
                'stock'     => $data['stock'],
            ]);
        }

        $this->assertEquals(6, Product::where('seller_id', $seller->id)->count());
    }

    /**
     * Test 4.2: Inventory telemetry table renders formatted unit labels for all 6 unit types.
     */
    public function test_inventory_telemetry_table_displays_all_6_unit_types(): void
    {
        $seller = $this->createApprovedSeller();

        $this->createProductRecord($seller, 'Wheat Flour', 45.00, 60, 'kg');
        $this->createProductRecord($seller, 'Farm Fresh Eggs', 90.00, 20, 'dozen');
        $this->createProductRecord($seller, 'Methi Greens', 15.00, 45, 'bundle');
        $this->createProductRecord($seller, 'Mustard Cooking Oil', 160.00, 35, 'litre');
        $this->createProductRecord($seller, 'Green Coconuts', 50.00, 30, 'piece');
        $this->createProductRecord($seller, 'Mushroom Crates', 70.00, 25, 'pack');

        $response = $this->actingAs($seller, 'seller')->get(route('seller.products.inventory'));

        $response->assertStatus(200);

        // Assert all 6 formatted unit strings appear in table
        $response->assertSee('Kg (kg)');
        $response->assertSee('Dozen (dozen)');
        $response->assertSee('Bundle (bundle)');
        $response->assertSee('Litre (litre)');
        $response->assertSee('Piece (piece)');
        $response->assertSee('Pack (pack)');
    }

    /**
     * Test 4.3: Stock adjustment echoes exact unit type in response messages for each unit.
     */
    public function test_stock_adjustment_echoes_exact_unit_type_in_feedback_messages(): void
    {
        $seller = $this->createApprovedSeller();

        $units = ['kg', 'dozen', 'bundle', 'litre', 'piece', 'pack'];

        foreach ($units as $unit) {
            $product = $this->createProductRecord($seller, "Item of {$unit}", 100.00, 20, $unit);

            $response = $this->actingAs($seller, 'seller')
                ->postJson(route('seller.products.adjust-stock', $product), [
                    'action'   => 'add',
                    'quantity' => 10,
                ]);

            $response->assertStatus(200);
            $response->assertJson([
                'success' => true,
                'message' => "Stock for '{$product->name}' updated to 30 {$unit}.",
            ]);
        }
    }

    /**
     * Test 4.4: Strict rejection of unsupported unit types in product creation and update.
     */
    public function test_strict_rejection_of_unsupported_unit_types(): void
    {
        $seller = $this->createApprovedSeller();

        $invalidUnits = ['gallon', 'meter', 'gram', 'quintal', 'box', 'ton', 'lbs', 'bottle'];

        foreach ($invalidUnits as $invalidUnit) {
            $payload = [
                'name'        => "Invalid Unit {$invalidUnit} Test",
                'category_id' => $this->category->id,
                'price'       => 100.00,
                'unit_type'   => $invalidUnit,
                'stock'       => 50,
            ];

            $response = $this->actingAs($seller, 'seller')
                ->postJson(route('seller.products.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['unit_type']);
        }
    }

    /**
     * Test 4.5: Unit type validation rejects uppercase and non-whitelisted string injections.
     */
    public function test_unit_type_strictly_requires_lowercase_canonical_tokens(): void
    {
        $seller = $this->createApprovedSeller();

        $malformedUnits = [
            'KG',
            'Dozen',
            'LITRE',
            'invalid_piece',
            'kg; DROP TABLE products;--',
            '<script>alert(1)</script>',
        ];

        foreach ($malformedUnits as $malformed) {
            $payload = [
                'name'        => 'Malformed Unit Product',
                'category_id' => $this->category->id,
                'price'       => 100.00,
                'unit_type'   => $malformed,
                'stock'       => 10,
            ];

            $response = $this->actingAs($seller, 'seller')
                ->postJson(route('seller.products.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['unit_type']);
        }
    }

    // =========================================================================
    // SECTION 5: TENANCY ISOLATION & ACCESS CONTROL GUARDRAILS
    // =========================================================================

    /**
     * Test 5.1: Cross-tenant isolation - Seller A cannot adjust stock of Seller B product (HTTP 403).
     */
    public function test_seller_a_cannot_adjust_stock_of_seller_b_product(): void
    {
        $sellerA = $this->createApprovedSeller(['email' => 'seller_a@bazaario.com']);
        $sellerB = $this->createApprovedSeller(['email' => 'seller_b@bazaario.com']);

        $productB = $this->createProductRecord($sellerB, 'Seller B High Value Crops', 500.00, 50, 'kg');

        $response = $this->actingAs($sellerA, 'seller')
            ->postJson(route('seller.products.adjust-stock', $productB), [
                'action'   => 'set',
                'quantity' => 0,
            ]);

        $response->assertStatus(403);
        $this->assertEquals(50, $productB->fresh()->stock);
    }

    /**
     * Test 5.2: Unapproved pending seller cannot access inventory page or mutate stock.
     */
    public function test_unapproved_seller_is_gated_from_inventory_and_stock_adjustments(): void
    {
        $pendingSeller = $this->createPendingSeller();
        $product = $this->createProductRecord($pendingSeller, 'Pending Crop', 100.00, 20, 'kg');

        // Inventory table access is gated
        $respInv = $this->actingAs($pendingSeller, 'seller')
            ->get(route('seller.products.inventory'));
        $respInv->assertRedirect(route('seller.pending'));

        // Stock mutation is gated
        $respAdj = $this->actingAs($pendingSeller, 'seller')
            ->postJson(route('seller.products.adjust-stock', $product), [
                'action'   => 'add',
                'quantity' => 10,
            ]);
        // SellerMiddleware or Controller blocks with 403 or redirects to pending
        $this->assertTrue(in_array($respAdj->status(), [302, 403]));
    }

    /**
     * Test 5.3: Unauthenticated guest cannot adjust stock (HTTP 401 or redirect to login).
     */
    public function test_unauthenticated_guest_cannot_adjust_stock(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Guest Target Crop', 100.00, 20, 'kg');

        $response = $this->postJson(route('seller.products.adjust-stock', $product), [
            'action'   => 'add',
            'quantity' => 10,
        ]);

        $this->assertTrue(in_array($response->status(), [401, 302]));
        $this->assertEquals(20, $product->fresh()->stock);
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
            'shop_name'           => 'Empirical Test Agro Merchant ' . $seller->id,
            'shop_slug'           => 'empirical-agro-' . $seller->id,
            'seller_type'         => 'Farmer',
            'city'                => 'Contai',
            'state'               => 'West Bengal',
            'status'              => 'approved',
            'trust_score'         => 94.00,
            'commission_rate'     => 10.00,
            'address'             => 'Plot 101, Empirical Agro Hub',
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
            'shop_name'           => 'Pending Verification Shop ' . $seller->id,
            'shop_slug'           => 'pending-verification-' . $seller->id,
            'seller_type'         => 'Farmer',
            'city'                => 'Contai',
            'state'               => 'West Bengal',
            'status'              => 'pending',
            'trust_score'         => 80.00,
            'commission_rate'     => 10.00,
            'address'             => 'Pending Address Lane',
            'operating_radius_km' => 20,
        ], $profileAttrs));

        return $seller;
    }

    protected function createProductRecord(
        User $seller,
        string $name,
        float $price,
        int $stock,
        string $unitType = 'kg',
        int $threshold = 10,
        string $status = 'active'
    ): Product {
        return Product::create([
            'seller_id'           => $seller->id,
            'category_id'         => $this->category->id,
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
