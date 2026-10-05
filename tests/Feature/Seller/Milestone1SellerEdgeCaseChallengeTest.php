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
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Milestone1SellerEdgeCaseChallengeTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create([
            'role'   => 'seller',
            'status' => 'active',
        ]);

        $this->category = Category::create([
            'name' => 'Farm Fresh Produce',
            'slug' => 'farm-fresh-produce',
        ]);
    }

    /**
     * Challenge 1.1: Automatic expiry date calculation edge cases.
     * Tests:
     * - Auto-computation when expiry_date is null
     * - Preservation of explicit expiry_date when already supplied
     * - Non-perishable products do not auto-compute expiry_date
     * - Null harvest_date or null expiry_days do not fail or auto-compute
     */
    public function test_product_expiry_date_automatic_calculation_edge_cases(): void
    {
        // Case A: Standard automatic calculation (harvest_date + expiry_days)
        $productAuto = Product::create([
            'seller_id'     => $this->seller->id,
            'category_id'   => $this->category->id,
            'name'          => 'Organic Tomatoes',
            'slug'          => 'organic-tomatoes',
            'sale_type'     => 'fixed_price',
            'price'         => 35.00,
            'stock'         => 50,
            'is_perishable' => true,
            'harvest_date'  => '2026-09-25',
            'expiry_days'   => 7,
            'status'        => 'active',
        ]);

        $this->assertNotNull($productAuto->expiry_date);
        $this->assertEquals('2026-10-02', $productAuto->expiry_date->toDateString());

        // Case B: Explicit expiry_date supplied upfront must NOT be overwritten by saving hook
        $productExplicit = Product::create([
            'seller_id'     => $this->seller->id,
            'category_id'   => $this->category->id,
            'name'          => 'Treated Potatoes',
            'slug'          => 'treated-potatoes',
            'sale_type'     => 'fixed_price',
            'price'         => 25.00,
            'stock'         => 100,
            'is_perishable' => true,
            'harvest_date'  => '2026-09-20',
            'expiry_days'   => 5,
            'expiry_date'   => '2026-10-15', // Custom override
            'status'        => 'active',
        ]);

        $this->assertEquals('2026-10-15', $productExplicit->expiry_date->toDateString(), 'Explicit expiry_date must be preserved');

        // Case C: Non-perishable product must NOT calculate expiry_date
        $productNonPerishable = Product::create([
            'seller_id'     => $this->seller->id,
            'category_id'   => $this->category->id,
            'name'          => 'Jute Bag',
            'slug'          => 'jute-bag',
            'sale_type'     => 'fixed_price',
            'price'         => 150.00,
            'stock'         => 20,
            'is_perishable' => false,
            'harvest_date'  => '2026-09-20',
            'expiry_days'   => 30,
            'status'        => 'active',
        ]);

        $this->assertNull($productNonPerishable->expiry_date, 'Non-perishable products must have null expiry_date');

        // Case D: Missing expiry_days leaves expiry_date null without exception
        $productNoDays = Product::create([
            'seller_id'     => $this->seller->id,
            'category_id'   => $this->category->id,
            'name'          => 'Fresh Basil',
            'slug'          => 'fresh-basil',
            'sale_type'     => 'fixed_price',
            'price'         => 20.00,
            'stock'         => 15,
            'is_perishable' => true,
            'harvest_date'  => '2026-09-28',
            'expiry_days'   => null,
            'status'        => 'active',
        ]);

        $this->assertNull($productNoDays->expiry_date);
    }

    /**
     * Challenge 1.2: Model isExpired() and isStale() and query scopes across edge dates:
     * - Yesterday: Expired, Stale, excluded from fresh/publicVisible (when auto_hide_expired is true)
     * - Today: Fresh, NOT expired (endOfDay has not passed), NOT stale, included in fresh & publicVisible
     * - Tomorrow: Fresh, NOT expired, NOT stale, included in fresh & publicVisible
     * - Non-perishable with past date: NOT expired, NOT stale
     * - Expired perishable with auto_hide_expired = false remains visible in publicVisible
     */
    public function test_product_expiry_and_staleness_across_edge_dates(): void
    {
        // 1. Product expiring YESTERDAY with auto_hide_expired = true
        $yesterdayProduct = Product::create([
            'seller_id'         => $this->seller->id,
            'category_id'       => $this->category->id,
            'name'              => 'Yesterday Milk',
            'slug'              => 'yesterday-milk',
            'sale_type'         => 'fixed_price',
            'price'             => 60.00,
            'stock'             => 10,
            'is_perishable'     => true,
            'expiry_date'       => Carbon::yesterday()->toDateString(),
            'auto_hide_expired' => true,
            'status'            => 'active',
        ]);

        // 2. Product expiring YESTERDAY but auto_hide_expired = false (visible for clearance)
        $yesterdayClearance = Product::create([
            'seller_id'         => $this->seller->id,
            'category_id'       => $this->category->id,
            'name'              => 'Yesterday Clearance Bread',
            'slug'              => 'yesterday-clearance-bread',
            'sale_type'         => 'fixed_price',
            'price'             => 15.00,
            'stock'             => 5,
            'is_perishable'     => true,
            'expiry_date'       => Carbon::yesterday()->toDateString(),
            'auto_hide_expired' => false,
            'status'            => 'active',
        ]);

        // 3. Product expiring TODAY
        $todayProduct = Product::create([
            'seller_id'         => $this->seller->id,
            'category_id'       => $this->category->id,
            'name'              => 'Today Fresh Bread',
            'slug'              => 'today-fresh-bread',
            'sale_type'         => 'fixed_price',
            'price'             => 40.00,
            'stock'             => 20,
            'is_perishable'     => true,
            'expiry_date'       => Carbon::today()->toDateString(),
            'auto_hide_expired' => true,
            'status'            => 'active',
        ]);

        // 4. Product expiring TOMORROW
        $tomorrowProduct = Product::create([
            'seller_id'         => $this->seller->id,
            'category_id'       => $this->category->id,
            'name'              => 'Tomorrow Green Apples',
            'slug'              => 'tomorrow-green-apples',
            'sale_type'         => 'fixed_price',
            'price'             => 180.00,
            'stock'             => 30,
            'is_perishable'     => true,
            'expiry_date'       => Carbon::tomorrow()->toDateString(),
            'auto_hide_expired' => true,
            'status'            => 'active',
        ]);

        // 5. Non-perishable with past date
        $nonPerishableProduct = Product::create([
            'seller_id'         => $this->seller->id,
            'category_id'       => $this->category->id,
            'name'              => 'Canned Honey',
            'slug'              => 'canned-honey',
            'sale_type'         => 'fixed_price',
            'price'             => 450.00,
            'stock'             => 15,
            'is_perishable'     => false,
            'expiry_date'       => Carbon::yesterday()->toDateString(),
            'auto_hide_expired' => true,
            'status'            => 'active',
        ]);

        // Assert Model Method Behaviors
        // Yesterday
        $this->assertTrue($yesterdayProduct->isExpired(), 'Yesterday product must be expired');
        $this->assertTrue($yesterdayProduct->isStale(), 'Yesterday product must be stale');
        $this->assertTrue($yesterdayClearance->isExpired());
        $this->assertTrue($yesterdayClearance->isStale());

        // Today
        $this->assertFalse($todayProduct->isExpired(), 'Product expiring today is valid until end of day');
        $this->assertFalse($todayProduct->isStale(), 'Product expiring today must not be stale');

        // Tomorrow
        $this->assertFalse($tomorrowProduct->isExpired(), 'Tomorrow product must not be expired');
        $this->assertFalse($tomorrowProduct->isStale(), 'Tomorrow product must not be stale');

        // Non-perishable
        $this->assertFalse($nonPerishableProduct->isExpired(), 'Non-perishable product is never expired');
        $this->assertFalse($nonPerishableProduct->isStale(), 'Non-perishable product is never stale');

        // Assert Query Scopes Consistency
        $staleIds = Product::query()->stale()->pluck('id')->all();
        $this->assertContains($yesterdayProduct->id, $staleIds);
        $this->assertContains($yesterdayClearance->id, $staleIds);
        $this->assertNotContains($todayProduct->id, $staleIds);
        $this->assertNotContains($tomorrowProduct->id, $staleIds);
        $this->assertNotContains($nonPerishableProduct->id, $staleIds);

        $freshIds = Product::query()->fresh()->pluck('id')->all();
        $this->assertNotContains($yesterdayProduct->id, $freshIds);
        $this->assertNotContains($yesterdayClearance->id, $freshIds);
        $this->assertContains($todayProduct->id, $freshIds);
        $this->assertContains($tomorrowProduct->id, $freshIds);
        $this->assertContains($nonPerishableProduct->id, $freshIds);

        $visibleIds = Product::query()->publicVisible()->pluck('id')->all();
        $this->assertNotContains($yesterdayProduct->id, $visibleIds, 'Yesterday expired product with auto_hide_expired=true must be hidden');
        $this->assertContains($yesterdayClearance->id, $visibleIds, 'Yesterday expired product with auto_hide_expired=false must remain visible');
        $this->assertContains($todayProduct->id, $visibleIds);
        $this->assertContains($tomorrowProduct->id, $visibleIds);
        $this->assertContains($nonPerishableProduct->id, $visibleIds);
    }

    /**
     * Challenge 1.3: Low stock threshold evaluation.
     */
    public function test_product_low_stock_threshold_logic(): void
    {
        // Custom threshold = 5
        $lowCustom = Product::create([
            'seller_id'           => $this->seller->id,
            'category_id'         => $this->category->id,
            'name'                => 'Custom Low',
            'slug'                => 'custom-low',
            'sale_type'           => 'fixed_price',
            'price'               => 10.00,
            'stock'               => 5,
            'low_stock_threshold' => 5,
            'status'              => 'active',
        ]);
        $this->assertTrue($lowCustom->isLowStock(), 'Stock equal to custom threshold must be low stock');

        $adequateCustom = Product::create([
            'seller_id'           => $this->seller->id,
            'category_id'         => $this->category->id,
            'name'                => 'Custom Adequate',
            'slug'                => 'custom-adequate',
            'sale_type'           => 'fixed_price',
            'price'               => 10.00,
            'stock'               => 6,
            'low_stock_threshold' => 5,
            'status'              => 'active',
        ]);
        $this->assertFalse($adequateCustom->isLowStock(), 'Stock above custom threshold is not low stock');

        // Default threshold when omitted (database schema defaults to 10)
        $defaultLow = Product::create([
            'seller_id'   => $this->seller->id,
            'category_id' => $this->category->id,
            'name'        => 'Default Low',
            'slug'        => 'default-low',
            'sale_type'   => 'fixed_price',
            'price'       => 10.00,
            'stock'       => 10,
            'status'      => 'active',
        ]);
        $this->assertTrue($defaultLow->isLowStock(), 'Stock at 10 with default schema threshold must be low stock');

        $defaultAdequate = Product::create([
            'seller_id'   => $this->seller->id,
            'category_id' => $this->category->id,
            'name'        => 'Default Adequate',
            'slug'        => 'default-adequate',
            'sale_type'   => 'fixed_price',
            'price'       => 10.00,
            'stock'       => 11,
            'status'      => 'active',
        ]);
        $this->assertFalse($defaultAdequate->isLowStock(), 'Stock at 11 with default schema threshold is not low stock');

        $lowStockQueryIds = Product::query()->lowStock()->pluck('id')->all();
        $this->assertContains($lowCustom->id, $lowStockQueryIds);
        $this->assertContains($defaultLow->id, $lowStockQueryIds);
        $this->assertNotContains($adequateCustom->id, $lowStockQueryIds);
        $this->assertNotContains($defaultAdequate->id, $lowStockQueryIds);
    }

    /**
     * Challenge 1.4: SellerOrder delivery slot direct column vs legacy notes regex fallback.
     * Tests:
     * - Direct column takes priority over notes
     * - Fallback correctly parses various formats (casing, spaces, pipe delimitations)
     * - Empty string falls back to notes
     * - Gracefully returns null when notes is missing or unformatted
     * - Safe when order relation is not loaded or null
     */
    public function test_seller_order_delivery_slot_direct_and_regex_fallback(): void
    {
        $buyer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        $parentOrder = Order::create([
            'order_number'            => 'ORD-CH-001',
            'user_id'                 => $buyer->id,
            'subtotal'                => 1000.00,
            'total_amount'            => 1050.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'pending',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => '45 MG Road',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'West Bengal',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700025',
            'notes'                   => 'Time Slot: Evening 4PM - 7PM | Leave at gate',
        ]);

        // Sub-test A: Direct column takes precedence over notes
        $soDirect = SellerOrder::create([
            'order_id'            => $parentOrder->id,
            'seller_id'           => $this->seller->id,
            'seller_order_number' => 'SO-CH-001',
            'subtotal'            => 600,
            'status'              => 'placed',
            'delivery_slot'       => 'Morning 8AM - 11AM',
        ]);
        $this->assertEquals('Morning 8AM - 11AM', $soDirect->delivery_slot, 'Direct column must override parent notes');

        // Sub-test B: Null direct column extracts slot from parent notes
        $soFallback = SellerOrder::create([
            'order_id'            => $parentOrder->id,
            'seller_id'           => $this->seller->id,
            'seller_order_number' => 'SO-CH-002',
            'subtotal'            => 400,
            'status'              => 'placed',
            'delivery_slot'       => null,
        ]);
        $this->assertEquals('Evening 4PM - 7PM', $soFallback->delivery_slot, 'Fallback must extract slot from parent notes');

        // Sub-test C: Case insensitive matching without trailing pipe
        $parentOrderC = Order::create([
            'order_number'            => 'ORD-CH-002',
            'user_id'                 => $buyer->id,
            'subtotal'                => 200.00,
            'total_amount'            => 220.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'pending',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => '12 Park Street',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'West Bengal',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700016',
            'notes'                   => 'time slot:   Early Bird 6AM - 8AM  ',
        ]);

        $soFallbackC = SellerOrder::create([
            'order_id'            => $parentOrderC->id,
            'seller_id'           => $this->seller->id,
            'seller_order_number' => 'SO-CH-003',
            'subtotal'            => 200,
            'status'              => 'placed',
            'delivery_slot'       => '', // Empty string should also fallback
        ]);
        $this->assertEquals('Early Bird 6AM - 8AM', $soFallbackC->delivery_slot);

        // Sub-test D: Notes without slot returns null safely
        $parentOrderD = Order::create([
            'order_number'            => 'ORD-CH-003',
            'user_id'                 => $buyer->id,
            'subtotal'                => 100.00,
            'total_amount'            => 110.00,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'pending',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => '88 Lake View',
            'delivery_city'           => 'Kolkata',
            'delivery_state'          => 'West Bengal',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '700029',
            'notes'                   => 'Please ring doorbell twice.',
        ]);

        $soFallbackD = SellerOrder::create([
            'order_id'            => $parentOrderD->id,
            'seller_id'           => $this->seller->id,
            'seller_order_number' => 'SO-CH-004',
            'subtotal'            => 100,
            'status'              => 'placed',
            'delivery_slot'       => null,
        ]);
        $this->assertNull($soFallbackD->delivery_slot);

        // Sub-test E: Standalone SellerOrder with no parent order id returns null safely
        $soStandalone = new SellerOrder([
            'seller_id'           => $this->seller->id,
            'seller_order_number' => 'SO-CH-005',
            'subtotal'            => 50,
            'status'              => 'placed',
        ]);
        $this->assertNull($soStandalone->delivery_slot);
    }

    /**
     * Challenge 2: Storefront image upload formats and storage resilience.
     * Tests:
     * - PNG image upload
     * - JPG/JPEG image upload
     * - WEBP image upload
     * - Storage persistence on 'public' disk in 'storefronts/' folder
     * - Disallow invalid mime types (e.g. PDF, TXT)
     * - Disallow oversized images (> 5MB)
     */
    public function test_storefront_image_upload_formats_and_storage_persistence(): void
    {
        Storage::fake('public');

        // Valid binary PNG 1x1
        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $pngFile = UploadedFile::fake()->createWithContent('farm_logo.png', $pngBytes);

        $responsePng = $this->actingAs($this->seller, 'seller')->post(route('seller.onboarding.submit'), [
            'seller_type'      => 'Farmer',
            'shop_name'        => 'Sunflower Dairy Farm',
            'address'          => 'Village Road 12',
            'city'             => 'Contai',
            'state'            => 'West Bengal',
            'latitude'         => 21.78,
            'longitude'        => 87.75,
            'storefront_image' => $pngFile,
        ]);

        $responsePng->assertRedirect(route('seller.pending'));
        $profile = SellerProfile::where('user_id', $this->seller->id)->first();
        $this->assertNotNull($profile->logo_path);
        $this->assertStringStartsWith('storefronts/', $profile->logo_path);
        $this->assertStringEndsWith('.png', $profile->logo_path);
        Storage::disk('public')->assertExists($profile->logo_path);

        // Valid binary JPG 1x1
        $seller2 = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $jpgBytes = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        $jpgFile = UploadedFile::fake()->createWithContent('kirana_facade.jpg', $jpgBytes);

        $responseJpg = $this->actingAs($seller2, 'seller')->post(route('seller.onboarding.submit'), [
            'seller_type'      => 'Kirana Store',
            'shop_name'        => 'New Bengal Store',
            'address'          => 'Station Bazaar',
            'city'             => 'Kolkata',
            'state'            => 'West Bengal',
            'latitude'         => 22.57,
            'longitude'        => 88.36,
            'storefront_image' => $jpgFile,
        ]);

        $responseJpg->assertRedirect(route('seller.pending'));
        $profile2 = SellerProfile::where('user_id', $seller2->id)->first();
        $this->assertNotNull($profile2->logo_path);
        $this->assertStringStartsWith('storefronts/', $profile2->logo_path);
        $this->assertStringEndsWith('.jpg', $profile2->logo_path);
        Storage::disk('public')->assertExists($profile2->logo_path);

        // Valid binary WEBP 1x1
        $seller3 = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $webpBytes = base64_decode('UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4HAA==');
        $webpFile = UploadedFile::fake()->createWithContent('darkstore_front.webp', $webpBytes);

        $responseWebp = $this->actingAs($seller3, 'seller')->post(route('seller.onboarding.submit'), [
            'seller_type'      => 'Dark Store',
            'shop_name'        => 'Speedy Hub Express',
            'address'          => 'Salt Lake Sector V',
            'city'             => 'Kolkata',
            'state'            => 'West Bengal',
            'latitude'         => 22.58,
            'longitude'        => 88.42,
            'storefront_image' => $webpFile,
        ]);

        $responseWebp->assertRedirect(route('seller.pending'));
        $profile3 = SellerProfile::where('user_id', $seller3->id)->first();
        $this->assertNotNull($profile3->logo_path);
        $this->assertStringStartsWith('storefronts/', $profile3->logo_path);
        $this->assertStringEndsWith('.webp', $profile3->logo_path);
        Storage::disk('public')->assertExists($profile3->logo_path);

        // Reject invalid format (PDF document)
        $seller4 = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $pdfFile = UploadedFile::fake()->create('contract.pdf', 500, 'application/pdf');

        $responsePdf = $this->actingAs($seller4, 'seller')->post(route('seller.onboarding.submit'), [
            'seller_type'      => 'Individual',
            'shop_name'        => 'Artisan Pottery',
            'address'          => 'Crafts Lane',
            'city'             => 'Contai',
            'state'            => 'West Bengal',
            'latitude'         => 21.78,
            'longitude'        => 87.75,
            'storefront_image' => $pdfFile,
        ]);

        $responsePdf->assertSessionHasErrors(['storefront_image']);

        // Reject oversized file (> 5MB)
        $seller5 = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $largeFile = UploadedFile::fake()->create('giant_photo.jpg', 6000, 'image/jpeg'); // 6MB

        $responseLarge = $this->actingAs($seller5, 'seller')->post(route('seller.onboarding.submit'), [
            'seller_type'      => 'Individual',
            'shop_name'        => 'Handicrafts Studio',
            'address'          => 'Studio 4',
            'city'             => 'Contai',
            'state'            => 'West Bengal',
            'latitude'         => 21.78,
            'longitude'        => 87.75,
            'storefront_image' => $largeFile,
        ]);

        $responseLarge->assertSessionHasErrors(['storefront_image']);
    }

    /**
     * Challenge 3: Slug collision resolution and re-submission idempotency.
     */
    public function test_shop_slug_collision_and_resubmission_idempotency(): void
    {
        $sellerA = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $sellerB = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $sellerC = User::factory()->create(['role' => 'seller', 'status' => 'active']);

        $basePayload = [
            'seller_type' => 'Farmer',
            'shop_name'   => 'Golden Harvest Organics',
            'address'     => 'Main Agro Road',
            'city'        => 'Contai',
            'state'       => 'West Bengal',
            'latitude'    => 21.78,
            'longitude'   => 87.75,
        ];

        // Seller A creates first
        $this->actingAs($sellerA, 'seller')->post(route('seller.onboarding.submit'), $basePayload);
        $profileA = SellerProfile::where('user_id', $sellerA->id)->first();
        $this->assertEquals('golden-harvest-organics', $profileA->shop_slug);

        // Seller B creates identical shop name -> should get -1 suffix
        $this->actingAs($sellerB, 'seller')->post(route('seller.onboarding.submit'), $basePayload);
        $profileB = SellerProfile::where('user_id', $sellerB->id)->first();
        $this->assertEquals('golden-harvest-organics-1', $profileB->shop_slug);

        // Seller C creates identical shop name -> should get -2 suffix
        $this->actingAs($sellerC, 'seller')->post(route('seller.onboarding.submit'), $basePayload);
        $profileC = SellerProfile::where('user_id', $sellerC->id)->first();
        $this->assertEquals('golden-harvest-organics-2', $profileC->shop_slug);

        // Seller A re-submits their own profile with same shop name -> must NOT increment suffix
        $this->actingAs($sellerA, 'seller')->post(route('seller.onboarding.submit'), array_merge($basePayload, [
            'bio' => 'Updated bio description for Golden Harvest.',
        ]));
        $profileARefreshed = SellerProfile::where('user_id', $sellerA->id)->first();
        $this->assertEquals('golden-harvest-organics', $profileARefreshed->shop_slug, 'Re-submitting own profile must preserve unique slug');
        $this->assertEquals('Updated bio description for Golden Harvest.', $profileARefreshed->bio);
    }

    /**
     * Challenge 4: Coordinate boundaries and seller type normalization.
     */
    public function test_coordinate_boundaries_and_seller_type_normalization(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);

        // Test boundary coordinates (-90, +90, -180, +180)
        $validBoundaries = [
            'seller_type' => 'darkstore', // Normalized to 'Dark Store'
            'shop_name'   => 'Pole to Pole Logistics',
            'address'     => 'Geographic Center',
            'city'        => 'Nagpur',
            'state'       => 'Maharashtra',
            'latitude'    => 90.0,
            'longitude'   => -180.0,
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.onboarding.submit'), $validBoundaries);
        $response->assertRedirect(route('seller.pending'));

        $profile = SellerProfile::where('user_id', $seller->id)->first();
        $this->assertEquals('Dark Store', $profile->seller_type);
        $this->assertEquals(90.0, (float) $profile->latitude);
        $this->assertEquals(-180.0, (float) $profile->longitude);

        // Test invalid coordinates (> 90 lat, > 180 lng)
        $invalidCoords = [
            'seller_type' => 'Farmer',
            'shop_name'   => 'Mars Farm',
            'address'     => 'Space',
            'city'        => 'Orbit',
            'state'       => 'Cosmos',
            'latitude'    => 95.5,
            'longitude'   => 195.0,
        ];

        $respInvalid = $this->actingAs($seller, 'seller')->post(route('seller.onboarding.submit'), $invalidCoords);
        $respInvalid->assertSessionHasErrors(['latitude', 'longitude']);
    }

    /**
     * Challenge 5: Multi-Tenant Access Control & Middleware Approval Isolation across operational endpoints.
     * Tests:
     * - Pending / unapproved seller is blocked from operational seller routes (orders, products, payouts, auctions, account)
     * - Suspended seller profile is blocked and redirected to seller.pending
     * - Inactive seller user is logged out and redirected to login
     * - Non-seller user attempting to access seller dashboard is blocked
     */
    public function test_seller_middleware_comprehensive_isolation_and_lockouts(): void
    {
        $pendingSeller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id'   => $pendingSeller->id,
            'shop_name' => 'Pending Express',
            'shop_slug' => 'pending-express',
            'status'    => 'pending',
        ]);

        // Attempting to visit operational endpoints must redirect to seller.pending
        $operationalRoutes = [
            'seller.dashboard',
            'seller.products.index',
            'seller.products.create',
            'seller.orders.index',
            'seller.payouts.index',
            'seller.auctions.index',
            'seller.account.profile',
        ];

        foreach ($operationalRoutes as $route) {
            $resp = $this->actingAs($pendingSeller, 'seller')->get(route($route));
            $resp->assertRedirect(route('seller.pending'), "Pending seller must be blocked from $route");
        }

        // Suspended profile status
        $suspendedSeller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id'   => $suspendedSeller->id,
            'shop_name' => 'Suspended Shop',
            'shop_slug' => 'suspended-shop',
            'status'    => 'suspended',
        ]);

        $suspendedResp = $this->actingAs($suspendedSeller, 'seller')->get(route('seller.dashboard'));
        $suspendedResp->assertRedirect(route('seller.pending'));

        // Inactive user in users table
        $inactiveSeller = User::factory()->create(['role' => 'seller', 'status' => 'inactive']);
        $inactiveResp = $this->actingAs($inactiveSeller, 'seller')->get(route('seller.dashboard'));
        $inactiveResp->assertRedirect(route('login'));

        // Customer role ('user') visiting seller dashboard
        $customer = User::factory()->create(['role' => 'user', 'status' => 'active']);
        $custResp = $this->actingAs($customer, 'seller')->get(route('seller.dashboard'));
        $custResp->assertRedirect(route('products.index'));
    }
}
