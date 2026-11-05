<?php

namespace Tests\Feature\Seller;

use App\Http\Controllers\Seller\SellerProductController;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SellerOrganicCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;
    protected SellerProfile $profile;
    protected Category $organicCategory;
    protected Category $electronicsCategory;

    protected function setUp(): void
    {
        parent::setUp();

        Route::prefix('seller')->name('seller.')->middleware(['web', 'auth:seller', 'seller'])->group(function () {
            Route::prefix('products')->name('products.')->group(function () {
                Route::get('/', [SellerProductController::class, 'index'])->name('index');
                Route::get('/create', [SellerProductController::class, 'create'])->name('create');
                Route::post('/', [SellerProductController::class, 'store'])->name('store');
                Route::get('/{product}/edit', [SellerProductController::class, 'edit'])->name('edit');
                Route::put('/{product}', [SellerProductController::class, 'update'])->name('update');
            });
        });

        $this->seller = User::factory()->create([
            'role'   => 'seller',
            'status' => 'active',
        ]);

        $this->profile = SellerProfile::create([
            'user_id'     => $this->seller->id,
            'seller_type' => 'Farmer',
            'shop_name'   => 'Blackburn Organic Farm',
            'shop_slug'   => 'blackburn-organic-farm',
            'status'      => 'approved',
            'city'        => 'Contai',
            'latitude'    => 21.7781,
            'longitude'   => 87.7516,
        ]);

        $this->organicCategory = Category::create([
            'name'        => 'Organic Food',
            'slug'        => 'organic-food',
            'description' => 'Naturally grown fresh farm produce and organic harvests.',
            'status'      => 'active',
        ]);

        $this->electronicsCategory = Category::create([
            'name'        => 'Electronics & Gadgets',
            'slug'        => 'electronics',
            'description' => 'Tech gadgets and hardware.',
            'status'      => 'active',
        ]);
    }

    /**
     * Test 1: Category dropdown contains Organic Food option.
     */
    public function test_category_dropdown_contains_organic_food(): void
    {
        $response = $this->actingAs($this->seller, 'seller')->get(route('seller.products.create'));
        $response->assertStatus(200);
        $response->assertSee('Organic Food');
        $response->assertSee('agronomic-ledger-section');
    }

    /**
     * Test 2: Creating an Organic Food product saves agronomic ledger fields and computes expiry.
     */
    public function test_creating_organic_food_product_saves_agronomic_ledger(): void
    {
        $harvestDate = Carbon::today()->toDateString();
        $payload = [
            'name'          => 'Certified Organic Spinach',
            'category_id'   => $this->organicCategory->id,
            'price'         => 45.00,
            'unit_type'     => 'kg',
            'stock'         => 50,
            'farm_origin'   => 'Plot 4, Blackburn Organic Farm',
            'harvest_grade' => 'Grade A+ Export',
            'harvest_date'  => $harvestDate,
            'expiry_days'   => 4,
            'is_perishable' => 1,
        ];

        $response = $this->actingAs($this->seller, 'seller')->post(route('seller.products.store'), $payload);
        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('name', 'Certified Organic Spinach')->first();
        $this->assertNotNull($product);
        $this->assertEquals($this->organicCategory->id, $product->category_id);
        $this->assertTrue((bool) $product->is_perishable);
        $this->assertEquals('Plot 4, Blackburn Organic Farm', $product->farm_origin);
        $this->assertEquals(Carbon::today()->addDays(4)->toDateString(), $product->expiry_date->toDateString());
    }

    /**
     * Test 3: Creating a non-organic product (e.g. Electronics) does not mark perishable or force agronomic data.
     */
    public function test_creating_non_organic_product_clears_agronomic_ledger(): void
    {
        $payload = [
            'name'          => 'Wireless Mechanical Keyboard',
            'category_id'   => $this->electronicsCategory->id,
            'price'         => 3500.00,
            'unit_type'     => 'piece',
            'stock'         => 20,
        ];

        $response = $this->actingAs($this->seller, 'seller')->post(route('seller.products.store'), $payload);
        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('name', 'Wireless Mechanical Keyboard')->first();
        $this->assertNotNull($product);
        $this->assertEquals($this->electronicsCategory->id, $product->category_id);
        $this->assertFalse((bool) $product->is_perishable);
        $this->assertNull($product->harvest_date);
        $this->assertNull($product->expiry_date);
    }

    /**
     * Test 4: Seller products index filters by Organic Food category.
     */
    public function test_seller_products_index_filters_by_organic_category(): void
    {
        Product::create([
            'seller_id'   => $this->seller->id,
            'category_id' => $this->organicCategory->id,
            'name'        => 'Organic Apples',
            'slug'        => 'organic-apples',
            'price'       => 120.00,
            'unit_type'   => 'kg',
            'stock'       => 15,
            'status'      => 'active',
        ]);

        Product::create([
            'seller_id'   => $this->seller->id,
            'category_id' => $this->electronicsCategory->id,
            'name'        => 'Gaming Mouse',
            'slug'        => 'gaming-mouse',
            'price'       => 999.00,
            'unit_type'   => 'piece',
            'stock'       => 8,
            'status'      => 'active',
        ]);

        $response = $this->actingAs($this->seller, 'seller')->get(route('seller.products.index', [
            'category_id' => $this->organicCategory->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Organic Apples');
        $response->assertDontSee('Gaming Mouse');
    }
}
