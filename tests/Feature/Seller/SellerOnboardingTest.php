<?php

namespace Tests\Feature\Seller;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerOnboardingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Verify database schema extensions exist on products, seller_orders, and seller_profiles.
     */
    public function test_m1_database_schema_has_required_columns(): void
    {
        // Products table columns
        $this->assertTrue(Schema::hasColumn('products', 'harvest_date'), 'products.harvest_date missing');
        $this->assertTrue(Schema::hasColumn('products', 'expiry_days'), 'products.expiry_days missing');
        $this->assertTrue(Schema::hasColumn('products', 'expiry_date'), 'products.expiry_date missing');
        $this->assertTrue(Schema::hasColumn('products', 'is_perishable'), 'products.is_perishable missing');
        $this->assertTrue(Schema::hasColumn('products', 'auto_hide_expired'), 'products.auto_hide_expired missing');
        $this->assertTrue(Schema::hasColumn('products', 'farm_origin'), 'products.farm_origin missing');
        $this->assertTrue(Schema::hasColumn('products', 'harvest_grade'), 'products.harvest_grade missing');
        $this->assertTrue(Schema::hasColumn('products', 'low_stock_threshold'), 'products.low_stock_threshold missing');

        // Seller orders table columns
        $this->assertTrue(Schema::hasColumn('seller_orders', 'delivery_slot'), 'seller_orders.delivery_slot missing');

        // Seller profiles table columns
        $this->assertTrue(Schema::hasColumn('seller_profiles', 'address'), 'seller_profiles.address missing');
        $this->assertTrue(Schema::hasColumn('seller_profiles', 'operating_radius_km'), 'seller_profiles.operating_radius_km missing');
    }

    /**
     * Test 2: Product model freshness, expiry calculation, and low stock helpers and scopes.
     */
    public function test_m1_product_model_freshness_and_expiry_logic(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $category = Category::create(['name' => 'Vegetables', 'slug' => 'vegetables']);

        // Test saving hook: auto-compute expiry_date from harvest_date + expiry_days
        $product = Product::create([
            'seller_id'           => $seller->id,
            'category_id'         => $category->id,
            'name'                => 'Fresh Spinach',
            'slug'                => 'fresh-spinach',
            'sale_type'           => 'fixed_price',
            'unit_type'           => 'kg',
            'price'               => 40.00,
            'stock'               => 5,
            'low_stock_threshold' => 10,
            'is_perishable'       => true,
            'harvest_date'        => Carbon::today()->toDateString(),
            'expiry_days'         => 3,
            'status'              => 'active',
        ]);

        $this->assertEquals(Carbon::today()->addDays(3)->toDateString(), $product->expiry_date->toDateString());
        $this->assertFalse($product->isExpired());
        $this->assertFalse($product->isStale());
        $this->assertTrue($product->isLowStock());

        // Test expired product
        $expiredProduct = Product::create([
            'seller_id'           => $seller->id,
            'category_id'         => $category->id,
            'name'                => 'Old Strawberries',
            'slug'                => 'old-strawberries',
            'sale_type'           => 'fixed_price',
            'unit_type'           => 'kg',
            'price'               => 120.00,
            'stock'               => 20,
            'low_stock_threshold' => 5,
            'is_perishable'       => true,
            'harvest_date'        => Carbon::today()->subDays(10)->toDateString(),
            'expiry_days'         => 3,
            'expiry_date'         => Carbon::today()->subDays(7)->toDateString(),
            'auto_hide_expired'   => true,
            'status'              => 'active',
        ]);

        $this->assertTrue($expiredProduct->isExpired());
        $this->assertTrue($expiredProduct->isStale());
        $this->assertFalse($expiredProduct->isLowStock());

        // Test scopes
        $freshCount = Product::query()->fresh()->count();
        $this->assertEquals(1, $freshCount);

        $staleCount = Product::query()->stale()->count();
        $this->assertEquals(1, $staleCount);

        $lowStockCount = Product::query()->lowStock()->count();
        $this->assertEquals(1, $lowStockCount);

        $publicVisibleCount = Product::query()->publicVisible()->count();
        $this->assertEquals(1, $publicVisibleCount);
    }

    /**
     * Test 3: SellerOrder delivery_slot fillable and backward-compatible fallback accessor.
     */
    public function test_m1_seller_order_delivery_slot_accessor_and_fallback(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // Parent order with delivery slot in notes
        $order = Order::create([
            'order_number'            => 'ORD-M1-001',
            'user_id'                 => $buyer->id,
            'subtotal'                => 500.00,
            'total_amount'            => 550.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'pending',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => '123 Market Rd',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'WB',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700001',
            'notes'                   => 'Time Slot: Morning 8AM - 11AM | Fragile items',
        ]);

        // Seller order without direct delivery_slot (tests fallback extraction)
        $sellerOrder1 = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => 'SO-001',
            'subtotal'            => 500,
            'status'              => 'placed',
        ]);

        $this->assertEquals('Morning 8AM - 11AM', $sellerOrder1->delivery_slot);

        // Seller order with explicit delivery_slot
        $sellerOrder2 = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => 'SO-002',
            'subtotal'            => 300,
            'status'              => 'placed',
            'delivery_slot'       => 'Evening 4PM - 7PM',
        ]);

        $this->assertEquals('Evening 4PM - 7PM', $sellerOrder2->delivery_slot);
    }

    /**
     * Test 4: SellerProfile model helper methods and scopes.
     */
    public function test_m1_seller_profile_scopes_and_helpers(): void
    {
        $seller1 = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $profile1 = SellerProfile::create([
            'user_id'   => $seller1->id,
            'shop_name' => 'Approved Agro',
            'shop_slug' => 'approved-agro',
            'status'    => 'approved',
        ]);

        $seller2 = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $profile2 = SellerProfile::create([
            'user_id'   => $seller2->id,
            'shop_name' => 'Pending Farm',
            'shop_slug' => 'pending-farm',
            'status'    => 'pending',
        ]);

        $this->assertTrue($profile1->isApproved());
        $this->assertFalse($profile1->isPending());
        $this->assertTrue($profile2->isPending());
        $this->assertFalse($profile2->isApproved());

        $this->assertEquals(1, SellerProfile::query()->approved()->count());
        $this->assertEquals(1, SellerProfile::query()->pending()->count());
    }

    /**
     * Test 5: SellerMiddleware access gate enforces approval check.
     */
    public function test_m1_middleware_blocks_unapproved_seller_from_dashboard(): void
    {
        $seller = User::factory()->create([
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ]);

        SellerProfile::create([
            'user_id'   => $seller->id,
            'shop_name' => 'Unapproved Stall',
            'shop_slug' => 'unapproved-stall',
            'status'    => 'pending',
        ]);

        // Unapproved seller accessing dashboard is redirected to seller.pending
        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $response->assertRedirect(route('seller.pending'));

        // Unapproved seller accessing seller.pending returns 200 (no loop)
        $pendingResponse = $this->actingAs($seller, 'seller')->get(route('seller.pending'));
        $pendingResponse->assertStatus(200);

        // Unapproved seller accessing onboarding wizard returns 200
        $onboardingResponse = $this->actingAs($seller, 'seller')->get(route('seller.onboarding'));
        $onboardingResponse->assertStatus(200);
    }

    /**
     * Test 6: Approved seller can access dashboard and is redirected from pending and onboarding.
     */
    public function test_m1_middleware_allows_approved_seller_and_redirects_from_onboarding(): void
    {
        $seller = User::factory()->create([
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ]);

        SellerProfile::create([
            'user_id'   => $seller->id,
            'shop_name' => 'Approved Stall',
            'shop_slug' => 'approved-stall',
            'status'    => 'approved',
        ]);

        // Approved seller visits dashboard -> HTTP 200
        $dashboardResponse = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $dashboardResponse->assertStatus(200);

        // Approved seller visits pending -> redirected to dashboard
        $pendingResponse = $this->actingAs($seller, 'seller')->get(route('seller.pending'));
        $pendingResponse->assertRedirect(route('seller.dashboard'));

        // Approved seller visits onboarding -> redirected to dashboard
        $onboardingResponse = $this->actingAs($seller, 'seller')->get(route('seller.onboarding'));
        $onboardingResponse->assertRedirect(route('seller.dashboard'));
    }

    /**
     * Test 7: Onboarding wizard form validation.
     */
    public function test_m1_onboarding_wizard_rejects_missing_or_invalid_fields(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);

        $response = $this->actingAs($seller, 'seller')->post(route('seller.onboarding.submit'), [
            // Missing shop_name, address, city, state, latitude, longitude
            'seller_type' => 'Farmer',
        ]);

        $response->assertSessionHasErrors(['shop_name', 'address', 'city', 'state', 'latitude', 'longitude']);
    }

    /**
     * Test 8: Successful onboarding wizard submission with storefront photo upload.
     */
    public function test_m1_onboarding_wizard_successful_submission_and_profile_creation(): void
    {
        Storage::fake('public');

        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $fakeImage = UploadedFile::fake()->createWithContent('storefront.png', $pngContent);

        $payload = [
            'seller_type'      => 'farmer', // Tests case/slug normalization to 'Farmer'
            'shop_name'        => 'Green Valley Agro Farm',
            'bio'              => 'Naturally produced organic crops and pulses in East Midnapore.',
            'storefront_image' => $fakeImage,
            'address'          => 'Plot 104, Tamluk Highway, Contai',
            'city'             => 'Contai',
            'state'            => 'West Bengal',
            'postal_code'      => '721401',
            'latitude'         => 21.7781,
            'longitude'        => 87.7516,
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.onboarding.submit'), $payload);

        $response->assertRedirect(route('seller.pending'));
        $response->assertSessionHas('success');

        // Check database
        $this->assertDatabaseHas('seller_profiles', [
            'user_id'     => $seller->id,
            'seller_type' => 'Farmer',
            'shop_name'   => 'Green Valley Agro Farm',
            'shop_slug'   => 'green-valley-agro-farm',
            'city'        => 'Contai',
            'status'      => 'pending',
            'address'     => 'Plot 104, Tamluk Highway, Contai',
        ]);

        $profile = SellerProfile::where('user_id', $seller->id)->first();
        $this->assertNotNull($profile->logo_path);
        Storage::disk('public')->assertExists($profile->logo_path);
    }

    /**
     * Test 9: Pending view terminal renders timeline, status, and summary strip.
     */
    public function test_m1_pending_terminal_renders_timeline_and_summary_strip(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id'     => $seller->id,
            'seller_type' => 'Kirana Store',
            'shop_name'   => 'Maa Tara Grocery',
            'shop_slug'   => 'maa-tara-grocery',
            'status'      => 'pending',
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.pending'));

        $response->assertStatus(200);
        $response->assertSee('Awaiting Review');
        $response->assertSee('Application Submitted');
        $response->assertSee('Review Progress Timeline');
        $response->assertSee('Maa Tara Grocery');
        $response->assertSee('Kirana Store');
        $response->assertSee('Restricted Access');
    }
}
