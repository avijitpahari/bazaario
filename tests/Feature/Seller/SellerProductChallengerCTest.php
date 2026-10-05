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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SellerProductChallengerCTest
 *
 * Adversarial Empirical Verification Suite by Challenger M3-C for Milestone 3:
 * Product & Inventory Management (Custom Units & Perishables).
 *
 * Rigorous Stress Testing Matrix:
 *  1. Complex Perishable Lifecycles & Exact Boundary Timestamps (Midnight transition, today, yesterday, non-perishable fallbacks)
 *  2. Multi-Tenant Security & Isolation (Cross-tenant delete, cross-tenant stock adjust, cross-tenant update, cross-tenant view, 3-way isolation)
 *  3. Special Characters, XSS, and Unicode Sanitization (Bengali, Hindi, emojis, quotes, XSS script injection, slug/SKU non-ASCII fallback)
 *  4. Safe Deletion Guardrails & Order/Auction State Boundaries (Active statuses blocked, terminal allowed, mixed history, JSON 422, 404 race handling)
 *  5. Inventory Stock Manipulation Edge Cases & Guardrails (Underflow clamping to 0, negative validation, threshold boundaries, astronomical quantities)
 */
class SellerProductChallengerCTest extends TestCase
{
    use RefreshDatabase;

    protected Category $categoryFruits;
    protected Category $categoryVegetables;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryFruits = Category::create([
            'name'   => 'Adversarial Fruits',
            'slug'   => 'adversarial-fruits',
            'status' => 'active',
        ]);

        $this->categoryVegetables = Category::create([
            'name'   => 'Adversarial Vegetables',
            'slug'   => 'adversarial-vegetables',
            'status' => 'active',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    // =========================================================================
    // SECTION 1: COMPLEX PERISHABLE LIFECYCLES & EXACT BOUNDARY TIMESTAMPS
    // =========================================================================

    /**
     * Challenge 1.1: Product expiring TODAY remains fresh and publicly visible during the day.
     * At 12:00:00 on expiry day, the product is NOT yet expired, is fresh, and remains visible.
     */
    public function test_perishable_boundary_expires_today_remains_fresh_and_visible_at_noon(): void
    {
        $seller = $this->createApprovedSeller();
        $testNow = Carbon::parse('2026-06-15 12:00:00');
        Carbon::setTestNow($testNow);

        $product = Product::create([
            'seller_id'         => $seller->id,
            'category_id'       => $this->categoryFruits->id,
            'name'              => 'Ratnagiri Alphonso Expiring Today',
            'slug'              => 'alphonso-today-' . uniqid(),
            'sku'               => 'BZ-TEST-001',
            'unit_type'         => 'kg',
            'price'             => 400.00,
            'stock'             => 25,
            'status'            => 'active',
            'is_perishable'     => true,
            'auto_hide_expired' => true,
            'harvest_date'      => '2026-06-10',
            'expiry_days'       => 5,
            'expiry_date'       => '2026-06-15',
        ]);

        // Model methods
        $this->assertFalse($product->isExpired(), 'Product expiring today should NOT be expired at noon.');
        $this->assertFalse($product->isStale(), 'Product expiring today should NOT be stale at noon.');

        // Eloquent Scopes
        $this->assertTrue(Product::query()->fresh()->where('id', $product->id)->exists());
        $this->assertFalse(Product::stale()->where('id', $product->id)->exists());
        $this->assertTrue(Product::publicVisible()->where('id', $product->id)->exists());

        // Catalog UI checks: should count towards freshnessAttentionCount because expiry is within 2 days
        $response = $this->actingAs($seller, 'seller')->get(route('seller.products.index'));
        $response->assertStatus(200);
        $response->assertViewHas('freshnessAttentionCount', 1);
        $response->assertViewHas('staleCount', 0);
    }

    /**
     * Challenge 1.2: Exact second midnight transition from 23:59:59 to 00:00:00 marks product expired.
     * At 23:59:59 on expiry day -> fresh. Advance 1 second to 00:00:00 next day -> expired, stale, auto-hidden!
     */
    public function test_perishable_boundary_exact_second_midnight_transition_triggers_expiry(): void
    {
        $seller = $this->createApprovedSeller();

        // 1 second before midnight
        $boundaryBefore = Carbon::parse('2026-06-15 23:59:59');
        Carbon::setTestNow($boundaryBefore);

        $product = Product::create([
            'seller_id'         => $seller->id,
            'category_id'       => $this->categoryFruits->id,
            'name'              => 'Twilight Mango Lot',
            'slug'              => 'twilight-mango-' . uniqid(),
            'sku'               => 'BZ-TWL-001',
            'unit_type'         => 'kg',
            'price'             => 320.00,
            'stock'             => 10,
            'status'            => 'active',
            'is_perishable'     => true,
            'auto_hide_expired' => true,
            'expiry_date'       => '2026-06-15',
        ]);

        // At 23:59:59: fresh and visible
        $this->assertFalse($product->isExpired(), 'At 23:59:59 the product is still within its final valid second.');
        $this->assertTrue(Product::query()->fresh()->where('id', $product->id)->exists());
        $this->assertTrue(Product::publicVisible()->where('id', $product->id)->exists());

        // Advance time by EXACTLY 1 second to midnight of June 16
        $boundaryAfter = Carbon::parse('2026-06-16 00:00:00');
        Carbon::setTestNow($boundaryAfter);

        $refreshedProduct = $product->fresh();

        // At 00:00:00: expired, stale, excluded from fresh, and auto-hidden from publicVisible
        $this->assertTrue($refreshedProduct->isExpired(), 'At 00:00:00 on the following day the product must be expired.');
        $this->assertTrue($refreshedProduct->isStale(), 'At 00:00:00 product must be flagged stale.');
        $this->assertFalse(Product::query()->fresh()->where('id', $product->id)->exists());
        $this->assertTrue(Product::stale()->where('id', $product->id)->exists());
        $this->assertFalse(Product::publicVisible()->where('id', $product->id)->exists(), 'Expired perishable must be auto-hidden from public marketplace.');

        // In seller catalog: appears under stale tab
        $responseStale = $this->actingAs($seller, 'seller')->get(route('seller.products.index', ['tab' => 'stale']));
        $responseStale->assertStatus(200);
        $this->assertTrue($responseStale->viewData('products')->contains('id', $product->id));
    }

    /**
     * Challenge 1.3: Product expired yesterday is immediately stale and auto-hidden.
     */
    public function test_perishable_boundary_expired_yesterday_is_stale_and_auto_hidden(): void
    {
        $seller = $this->createApprovedSeller();
        Carbon::setTestNow(Carbon::parse('2026-06-16 10:00:00'));

        $product = Product::create([
            'seller_id'         => $seller->id,
            'category_id'       => $this->categoryVegetables->id,
            'name'              => 'Organic Baby Spinach',
            'slug'              => 'spinach-expired-' . uniqid(),
            'sku'               => 'BZ-SPN-001',
            'unit_type'         => 'bundle',
            'price'             => 30.00,
            'stock'             => 15,
            'status'            => 'active',
            'is_perishable'     => true,
            'auto_hide_expired' => true,
            'harvest_date'      => '2026-06-12',
            'expiry_days'       => 3,
            'expiry_date'       => '2026-06-15', // Yesterday
        ]);

        $this->assertTrue($product->isExpired());
        $this->assertTrue($product->isStale());
        $this->assertFalse(Product::publicVisible()->where('id', $product->id)->exists());
        $this->assertTrue(Product::stale()->where('id', $product->id)->exists());
    }

    /**
     * Challenge 1.4: Non-perishable product with past expiry_date is NEVER expired or auto-hidden.
     */
    public function test_non_perishable_product_with_past_date_is_never_expired_or_hidden(): void
    {
        $seller = $this->createApprovedSeller();
        Carbon::setTestNow(Carbon::parse('2026-06-16 10:00:00'));

        $product = Product::create([
            'seller_id'         => $seller->id,
            'category_id'       => $this->categoryFruits->id,
            'name'              => 'Sun-Dried Lentils in Jute Sack',
            'slug'              => 'sun-dried-lentils-' . uniqid(),
            'sku'               => 'BZ-LNT-001',
            'unit_type'         => 'kg',
            'price'             => 110.00,
            'stock'             => 100,
            'status'            => 'active',
            'is_perishable'     => false, // Explicitly non-perishable
            'auto_hide_expired' => true,
            'expiry_date'       => '2026-01-01', // Ancient date in the past
        ]);

        $this->assertFalse($product->isExpired(), 'Non-perishable products must never be evaluated as expired.');
        $this->assertFalse($product->isStale(), 'Non-perishable products must never be stale.');
        $this->assertTrue(Product::query()->fresh()->where('id', $product->id)->exists());
        $this->assertFalse(Product::stale()->where('id', $product->id)->exists());
        $this->assertTrue(Product::publicVisible()->where('id', $product->id)->exists(), 'Non-perishable products must remain public.');
    }

    /**
     * Challenge 1.5: Perishable product with NULL expiry_date defaults to fresh and visible.
     */
    public function test_perishable_with_null_expiry_date_defaults_to_fresh_and_visible(): void
    {
        $seller = $this->createApprovedSeller();
        Carbon::setTestNow(Carbon::parse('2026-06-16 10:00:00'));

        $product = Product::create([
            'seller_id'         => $seller->id,
            'category_id'       => $this->categoryFruits->id,
            'name'              => 'Heirloom Seed Potatoes',
            'slug'              => 'heirloom-potatoes-' . uniqid(),
            'sku'               => 'BZ-POT-001',
            'unit_type'         => 'kg',
            'price'             => 75.00,
            'stock'             => 40,
            'status'            => 'active',
            'is_perishable'     => true,
            'auto_hide_expired' => true,
            'expiry_date'       => null,
        ]);

        $this->assertFalse($product->isExpired());
        $this->assertFalse($product->isStale());
        $this->assertTrue(Product::query()->fresh()->where('id', $product->id)->exists());
        $this->assertFalse(Product::stale()->where('id', $product->id)->exists());
        $this->assertTrue(Product::publicVisible()->where('id', $product->id)->exists());
    }

    /**
     * Challenge 1.6: 1-Day shelf-life perishable creation calculates tomorrow's exact date.
     */
    public function test_perishable_creation_with_1_day_shelf_life_calculates_tomorrow(): void
    {
        $seller = $this->createApprovedSeller();
        Carbon::setTestNow(Carbon::parse('2026-07-10 09:00:00'));

        $payload = [
            'name'                 => 'Fresh Cow Milk Lot',
            'category_id'          => $this->categoryFruits->id,
            'price'                => 65.00,
            'unit_type'            => 'litre',
            'stock'                => 60,
            'harvest_date'         => '2026-07-10',
            'expiry_days'          => 1,
            'is_perishable'        => true,
            'status'               => 'active',
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);
        $response->assertRedirect(route('seller.products.index'));

        $created = Product::where('name', 'Fresh Cow Milk Lot')->first();
        $this->assertNotNull($created);
        $this->assertTrue((bool) $created->is_perishable);
        $this->assertEquals('2026-07-11', $created->expiry_date->toDateString());
        $this->assertFalse($created->isExpired());
    }

    /**
     * Challenge 1.7: Expired perishable with auto_hide_expired=false explicitly stays visible for clearance sale.
     */
    public function test_perishable_auto_hide_disabled_allows_clearance_sale_visibility(): void
    {
        $seller = $this->createApprovedSeller();
        Carbon::setTestNow(Carbon::parse('2026-07-15 14:00:00'));

        $product = Product::create([
            'seller_id'         => $seller->id,
            'category_id'       => $this->categoryFruits->id,
            'name'              => 'Bruised Mangoes for Processing Pulp',
            'slug'              => 'pulp-clearance-' . uniqid(),
            'sku'               => 'BZ-PLP-001',
            'unit_type'         => 'kg',
            'price'             => 40.00,
            'stock'             => 80,
            'status'            => 'active',
            'is_perishable'     => true,
            'auto_hide_expired' => false, // Clearance sale authorized
            'expiry_date'       => '2026-07-14', // Expired yesterday
        ]);

        $this->assertTrue($product->isExpired());
        $this->assertTrue($product->isStale());
        // Since auto_hide_expired is false, scopePublicVisible MUST retain it
        $this->assertTrue(Product::publicVisible()->where('id', $product->id)->exists(), 'Clearance product with auto_hide=false must remain visible.');
    }

    // =========================================================================
    // SECTION 2: MULTI-TENANT SECURITY & STRICT ISOLATION
    // =========================================================================

    /**
     * Challenge 2.1: Cross-tenant deletion attempt returns HTTP 403 Forbidden.
     */
    public function test_cross_tenant_deletion_is_strictly_forbidden_http_403(): void
    {
        $sellerVictim = $this->createApprovedSeller(['email' => 'victim@bazaario.com']);
        $sellerAttacker = $this->createApprovedSeller(['email' => 'attacker@bazaario.com']);

        $targetProduct = $this->createProduct($sellerVictim, 'Victim Premium Crop', 500.00, 30);

        $response = $this->actingAs($sellerAttacker, 'seller')->delete(route('seller.products.destroy', $targetProduct));

        $response->assertStatus(403);
        $this->assertDatabaseHas('products', ['id' => $targetProduct->id]);
    }

    /**
     * Challenge 2.2: Cross-tenant deletion via JSON API returns HTTP 403 Forbidden.
     */
    public function test_cross_tenant_json_deletion_is_strictly_forbidden_http_403(): void
    {
        $sellerVictim = $this->createApprovedSeller(['email' => 'victim_api@bazaario.com']);
        $sellerAttacker = $this->createApprovedSeller(['email' => 'attacker_api@bazaario.com']);

        $targetProduct = $this->createProduct($sellerVictim, 'Victim Protected API Crop', 750.00, 40);

        $response = $this->actingAs($sellerAttacker, 'seller')
            ->deleteJson(route('seller.products.destroy', $targetProduct));

        $response->assertStatus(403);
        $this->assertDatabaseHas('products', ['id' => $targetProduct->id]);
    }

    /**
     * Challenge 2.3: Cross-tenant stock manipulation (reduce, set, add) returns HTTP 403 Forbidden.
     */
    public function test_cross_tenant_stock_decrement_and_set_is_strictly_forbidden_http_403(): void
    {
        $sellerVictim = $this->createApprovedSeller(['email' => 'victim_stock@bazaario.com']);
        $sellerAttacker = $this->createApprovedSeller(['email' => 'attacker_stock@bazaario.com']);

        $targetProduct = $this->createProduct($sellerVictim, 'Victim High Value Stock', 1200.00, 50);

        // 1. Attack via 'reduce'
        $respReduce = $this->actingAs($sellerAttacker, 'seller')
            ->post(route('seller.products.adjust-stock', $targetProduct), [
                'action'   => 'reduce',
                'quantity' => 50,
                'reason'   => 'Malicious stock sabotage',
            ]);
        $respReduce->assertStatus(403);
        $this->assertEquals(50, $targetProduct->fresh()->stock);

        // 2. Attack via 'set' to 0
        $respSet = $this->actingAs($sellerAttacker, 'seller')
            ->post(route('seller.products.adjust-stock', $targetProduct), [
                'action'   => 'set',
                'quantity' => 0,
                'reason'   => 'Malicious stock wipeout',
            ]);
        $respSet->assertStatus(403);
        $this->assertEquals(50, $targetProduct->fresh()->stock);

        // 3. Attack via 'add'
        $respAdd = $this->actingAs($sellerAttacker, 'seller')
            ->post(route('seller.products.adjust-stock', $targetProduct), [
                'action'   => 'add',
                'quantity' => 500,
                'reason'   => 'Malicious inventory inflation',
            ]);
        $respAdd->assertStatus(403);
        $this->assertEquals(50, $targetProduct->fresh()->stock);
    }

    /**
     * Challenge 2.4: Cross-tenant product update tampering returns HTTP 403 Forbidden.
     */
    public function test_cross_tenant_product_update_tampering_is_strictly_forbidden_http_403(): void
    {
        $sellerVictim = $this->createApprovedSeller(['email' => 'victim_update@bazaario.com']);
        $sellerAttacker = $this->createApprovedSeller(['email' => 'attacker_update@bazaario.com']);

        $targetProduct = $this->createProduct($sellerVictim, 'Genuine Organic Honey', 600.00, 20);

        $payload = [
            'name'        => 'Poisoned Defaced Title',
            'category_id' => $this->categoryFruits->id,
            'price'       => 1.00,
            'unit_type'   => 'litre',
            'stock'       => 0,
        ];

        $response = $this->actingAs($sellerAttacker, 'seller')
            ->put(route('seller.products.update', $targetProduct), $payload);

        $response->assertStatus(403);
        $this->assertEquals('Genuine Organic Honey', $targetProduct->fresh()->name);
        $this->assertEquals(600.00, (float) $targetProduct->fresh()->price);
        $this->assertEquals(20, $targetProduct->fresh()->stock);
    }

    /**
     * Challenge 2.5: Cross-tenant show and edit endpoints return HTTP 403 Forbidden.
     */
    public function test_cross_tenant_show_and_edit_forms_are_strictly_forbidden_http_403(): void
    {
        $sellerVictim = $this->createApprovedSeller(['email' => 'victim_view@bazaario.com']);
        $sellerAttacker = $this->createApprovedSeller(['email' => 'attacker_view@bazaario.com']);

        $targetProduct = $this->createProduct($sellerVictim, 'Confidential Farmer Breed Crop', 350.00, 15);

        // Edit form
        $respEdit = $this->actingAs($sellerAttacker, 'seller')->get(route('seller.products.edit', $targetProduct));
        $respEdit->assertStatus(403);

        // Show page
        $respShow = $this->actingAs($sellerAttacker, 'seller')->get(route('seller.products.show', $targetProduct));
        $respShow->assertStatus(403);
    }

    /**
     * Challenge 2.6: Three-way tenant isolation: Catalog and inventory queries return 0 foreign records.
     */
    public function test_three_way_tenant_catalog_and_inventory_zero_leakage(): void
    {
        $seller1 = $this->createApprovedSeller(['email' => 'tenant1@bazaario.com']);
        $seller2 = $this->createApprovedSeller(['email' => 'tenant2@bazaario.com']);
        $seller3 = $this->createApprovedSeller(['email' => 'tenant3@bazaario.com']);

        // Create 3 distinct products for each seller
        for ($i = 1; $i <= 3; $i++) {
            $this->createProduct($seller1, "Tenant 1 Item #{$i}", 100 * $i, 10 * $i);
            $this->createProduct($seller2, "Tenant 2 Item #{$i}", 200 * $i, 20 * $i);
            $this->createProduct($seller3, "Tenant 3 Item #{$i}", 300 * $i, 30 * $i);
        }

        // 1. Verify Catalog Index for Tenant 1
        $resp1 = $this->actingAs($seller1, 'seller')->get(route('seller.products.index'));
        $resp1->assertStatus(200);
        $products1 = $resp1->viewData('products');

        $this->assertCount(3, $products1);
        $this->assertEquals(3, $resp1->viewData('totalCount'));
        foreach ($products1 as $p) {
            $this->assertEquals($seller1->id, $p->seller_id);
            $this->assertStringContainsString('Tenant 1 Item', $p->name);
        }
        $resp1->assertDontSee('Tenant 2 Item');
        $resp1->assertDontSee('Tenant 3 Item');

        // 2. Verify Warehouse Inventory for Tenant 2
        $resp2 = $this->actingAs($seller2, 'seller')->get(route('seller.products.inventory'));
        $resp2->assertStatus(200);
        $products2 = $resp2->viewData('products');

        $this->assertCount(3, $products2);
        $this->assertEquals(3, $resp2->viewData('totalTracked'));
        foreach ($products2 as $p) {
            $this->assertEquals($seller2->id, $p->seller_id);
            $this->assertStringContainsString('Tenant 2 Item', $p->name);
        }
        $resp2->assertDontSee('Tenant 1 Item');
        $resp2->assertDontSee('Tenant 3 Item');
    }

    /**
     * Challenge 2.7: Pending and unapproved sellers cannot access product catalog or mutations.
     */
    public function test_pending_seller_is_locked_out_from_catalog_and_mutations(): void
    {
        $pendingSeller = $this->createPendingSeller();

        // 1. Cannot access catalog index
        $respIndex = $this->actingAs($pendingSeller, 'seller')->get(route('seller.products.index'));
        $respIndex->assertRedirect(route('seller.pending'));

        // 2. Cannot access create workstation
        $respCreate = $this->actingAs($pendingSeller, 'seller')->get(route('seller.products.create'));
        $respCreate->assertRedirect(route('seller.pending'));

        // 3. Cannot post new product
        $respStore = $this->actingAs($pendingSeller, 'seller')->post(route('seller.products.store'), [
            'name'        => 'Unauthorized Product',
            'category_id' => $this->categoryFruits->id,
            'price'       => 100.00,
            'unit_type'   => 'kg',
            'stock'       => 10,
        ]);
        $respStore->assertRedirect(route('seller.pending'));
        $this->assertDatabaseMissing('products', ['name' => 'Unauthorized Product']);
    }

    // =========================================================================
    // SECTION 3: SPECIAL CHARACTERS, XSS, AND UNICODE SANITIZATION
    // =========================================================================

    /**
     * Challenge 3.1: XSS script tags in product name are escaped in Blade templates.
     */
    public function test_xss_injection_in_product_name_is_escaped_in_blade_views(): void
    {
        $seller = $this->createApprovedSeller();

        $xssName = '<script>alert("XSS-INJECTION-NAME")</script>';

        $payload = [
            'name'                 => $xssName,
            'category_id'          => $this->categoryFruits->id,
            'price'                => 150.00,
            'unit_type'            => 'kg',
            'stock'                => 20,
            'status'               => 'active',
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);
        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('seller_id', $seller->id)->first();
        $this->assertNotNull($product);

        // In database: raw string is stored faithfully
        $this->assertEquals($xssName, $product->name);

        // In Blade index view: raw executable script tag MUST NOT appear
        $respIndex = $this->actingAs($seller, 'seller')->get(route('seller.products.index'));
        $respIndex->assertStatus(200);
        $respIndex->assertDontSee($xssName, false); // false = do not escape the needle, check raw HTML
        $respIndex->assertSee(e($xssName), false); // Escaped entities must be present

        // In Show view
        $respShow = $this->actingAs($seller, 'seller')->get(route('seller.products.show', $product));
        $respShow->assertStatus(200);
        $respShow->assertDontSee($xssName, false);
        $respShow->assertSee(e($xssName), false);

        // In Inventory view
        $respInv = $this->actingAs($seller, 'seller')->get(route('seller.products.inventory'));
        $respInv->assertStatus(200);
        $respInv->assertDontSee($xssName, false);
        $respInv->assertSee(e($xssName), false);
    }

    /**
     * Challenge 3.2: Hostile HTML/XSS in farm_origin, descriptions, and harvest_grade are properly escaped.
     */
    public function test_xss_injection_in_farm_origin_and_descriptions_escaped(): void
    {
        $seller = $this->createApprovedSeller();

        $hostileOrigin = '<img src=x onerror=alert(document.cookie)>';
        $hostileShortDesc = '"><svg onload=alert("SVG-XSS")>';
        $hostileGrade = '<iframe src="javascript:alert(1)">';

        $payload = [
            'name'                 => 'Organic Carrots with Malicious Metadata',
            'category_id'          => $this->categoryVegetables->id,
            'price'                => 45.00,
            'unit_type'            => 'kg',
            'stock'                => 100,
            'farm_origin'          => $hostileOrigin,
            'short_description'    => $hostileShortDesc,
            'harvest_grade'        => $hostileGrade,
            'status'               => 'active',
        ];

        $respStore = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);
        $respStore->assertRedirect(route('seller.products.index'));

        $product = Product::where('name', 'Organic Carrots with Malicious Metadata')->first();
        $this->assertNotNull($product);

        // Verify index view escapes hostile tags
        $respIndex = $this->actingAs($seller, 'seller')->get(route('seller.products.index'));
        $respIndex->assertStatus(200);
        $respIndex->assertDontSee($hostileOrigin, false);
        $respIndex->assertDontSee($hostileGrade, false);

        // Verify show view escapes hostile tags
        $respShow = $this->actingAs($seller, 'seller')->get(route('seller.products.show', $product));
        $respShow->assertStatus(200);
        $respShow->assertDontSee($hostileOrigin, false);
        $respShow->assertDontSee($hostileShortDesc, false);
        $respShow->assertDontSee($hostileGrade, false);
    }

    /**
     * Challenge 3.3: Hostile script in stock adjustment reason is handled cleanly without execution.
     */
    public function test_xss_injection_in_stock_adjustment_reason(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProduct($seller, 'Audit Tested Grain', 80.00, 40);

        $hostileReason = '<script>alert("AUDIT_TRAIL_POISON")</script>';

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.adjust-stock', $product), [
            'action'   => 'add',
            'quantity' => 10,
            'reason'   => $hostileReason,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(50, $product->fresh()->stock);
    }

    /**
     * Challenge 3.4: Bengali vernacular Unicode in all fields stored losslessly and auto-generates fallback SKU/slug.
     */
    public function test_bengali_vernacular_script_in_all_fields_stored_and_rendered_losslessly(): void
    {
        $seller = $this->createApprovedSeller();

        $bengaliName = 'আলফনসো আম (রত্নগিরি স্পেশাল)';
        $bengaliOrigin = 'সুন্দরবন জৈব বাগান, ক্যানিং';
        $bengaliShortDesc = 'গাছ পাকা সুস্বাদু আম, রাসায়নিক মুক্ত';
        $bengaliGrade = 'রপ্তানি গ্রেড ১';

        $payload = [
            'name'                 => $bengaliName,
            'category_id'          => $this->categoryFruits->id,
            'price'                => 380.00,
            'unit_type'            => 'kg',
            'stock'                => 35,
            'farm_origin'          => $bengaliOrigin,
            'short_description'    => $bengaliShortDesc,
            'harvest_grade'        => $bengaliGrade,
            'status'               => 'active',
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);
        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('seller_id', $seller->id)->first();
        $this->assertNotNull($product);

        // 1. Lossless database persistence
        $this->assertEquals($bengaliName, $product->name);
        $this->assertEquals($bengaliOrigin, $product->farm_origin);
        $this->assertEquals($bengaliShortDesc, $product->short_description);
        $this->assertEquals($bengaliGrade, $product->harvest_grade);

        // 2. Slug & SKU fallback verification for non-ASCII
        $this->assertNotEmpty($product->slug);
        $this->assertNotEmpty($product->sku);
        $this->assertStringStartsWith('BZ-', $product->sku);

        // 3. UI rendering verification
        $respIndex = $this->actingAs($seller, 'seller')->get(route('seller.products.index'));
        $respIndex->assertStatus(200);
        $respIndex->assertSee($bengaliName);
        $respIndex->assertSee($bengaliOrigin);

        $respShow = $this->actingAs($seller, 'seller')->get(route('seller.products.show', $product));
        $respShow->assertStatus(200);
        $respShow->assertSee($bengaliName);
        $respShow->assertSee($bengaliShortDesc);
    }

    /**
     * Challenge 3.5: Hindi Devanagari Unicode stored losslessly and rendered cleanly.
     */
    public function test_hindi_devanagari_script_in_all_fields_stored_and_rendered_losslessly(): void
    {
        $seller = $this->createApprovedSeller();

        $hindiName = 'हापुस आम (रत्नागिरी विशेष)';
        $hindiOrigin = 'देवगढ़ फार्म, महाराष्ट्र';
        $hindiShortDesc = 'प्राकृतिक रूप से पके हुए मीठे रसीले आम';

        $payload = [
            'name'                 => $hindiName,
            'category_id'          => $this->categoryFruits->id,
            'price'                => 450.00,
            'unit_type'            => 'dozen',
            'stock'                => 20,
            'farm_origin'          => $hindiOrigin,
            'short_description'    => $hindiShortDesc,
            'status'               => 'active',
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);
        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('seller_id', $seller->id)->first();
        $this->assertNotNull($product);
        $this->assertEquals($hindiName, $product->name);
        $this->assertEquals($hindiOrigin, $product->farm_origin);

        $respIndex = $this->actingAs($seller, 'seller')->get(route('seller.products.index'));
        $respIndex->assertStatus(200);
        $respIndex->assertSee($hindiName);
    }

    /**
     * Challenge 3.6: Complex emojis, single/double quotes, and special symbols handled resiliently.
     */
    public function test_complex_emojis_symbols_and_apostrophes_handled_resiliently(): void
    {
        $seller = $this->createApprovedSeller();

        $complexName = '🥭 O\'Reilly\'s "Heritage" Apples & Pears (100% Organic) ⭐ 🚜';
        $complexOrigin = 'O\'Connor & Sons "Valley Farm" <Sector #7>';
        $complexDesc = '"Hand-picked" at twilight; 50% extra sweetness & crispness! ₹499/pack.';

        $payload = [
            'name'                 => $complexName,
            'category_id'          => $this->categoryFruits->id,
            'price'                => 499.00,
            'unit_type'            => 'pack',
            'stock'                => 15,
            'farm_origin'          => $complexOrigin,
            'short_description'    => $complexDesc,
            'status'               => 'active',
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);
        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('seller_id', $seller->id)->first();
        $this->assertNotNull($product);
        $this->assertEquals($complexName, $product->name);
        $this->assertEquals($complexOrigin, $product->farm_origin);

        $respShow = $this->actingAs($seller, 'seller')->get(route('seller.products.show', $product));
        $respShow->assertStatus(200);
        $respShow->assertSee("O'Reilly's");
    }

    // =========================================================================
    // SECTION 4: SAFE DELETION GUARDRAILS & ORDER/AUCTION STATE BOUNDARIES
    // =========================================================================

    /**
     * Challenge 4.1: Safe deletion is BLOCKED by ALL active/in-flight order statuses.
     * Statuses: 'placed', 'processing', 'packed', 'shipped'.
     */
    public function test_safe_deletion_blocked_by_all_active_order_statuses(): void
    {
        $activeStatuses = ['placed', 'processing', 'packed', 'shipped'];

        foreach ($activeStatuses as $status) {
            $seller = $this->createApprovedSeller();
            $buyer = User::factory()->create(['role' => 'user']);
            $product = $this->createProduct($seller, "Crop in {$status} Order", 200.00, 10);

            $this->attachOrderToProduct($seller, $buyer, $product, $status);

            // Attempt deletion
            $response = $this->actingAs($seller, 'seller')->delete(route('seller.products.destroy', $product));

            $response->assertSessionHas('error');
            $this->assertDatabaseHas('products', ['id' => $product->id]);
        }
    }

    /**
     * Challenge 4.2: Safe deletion is ALLOWED for terminal order statuses.
     * Statuses: 'delivered', 'cancelled', 'returned'.
     */
    public function test_safe_deletion_allowed_by_terminal_order_statuses(): void
    {
        $terminalStatuses = ['delivered', 'cancelled', 'returned'];

        foreach ($terminalStatuses as $status) {
            $seller = $this->createApprovedSeller();
            $buyer = User::factory()->create(['role' => 'user']);
            $product = $this->createProduct($seller, "Crop in {$status} Order", 150.00, 10);

            $this->attachOrderToProduct($seller, $buyer, $product, $status);

            // Attempt deletion
            $response = $this->actingAs($seller, 'seller')->delete(route('seller.products.destroy', $product));

            $response->assertRedirect(route('seller.products.index'));
            $response->assertSessionHas('success');
            $this->assertDatabaseMissing('products', ['id' => $product->id]);
        }
    }

    /**
     * Challenge 4.3: Safe deletion is BLOCKED if ANY active order exists amidst fulfilled orders.
     */
    public function test_safe_deletion_blocked_if_any_active_order_exists_among_multiple_orders(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user']);
        $product = $this->createProduct($seller, 'Mixed Order History Product', 220.00, 25);

        // Historical fulfilled order
        $this->attachOrderToProduct($seller, $buyer, $product, 'delivered');
        // Active in-flight order
        $this->attachOrderToProduct($seller, $buyer, $product, 'processing');

        $response = $this->actingAs($seller, 'seller')->delete(route('seller.products.destroy', $product));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    /**
     * Challenge 4.4: Safe deletion is BLOCKED by active and scheduled auction statuses.
     * Statuses: 'live', 'scheduled'.
     */
    public function test_safe_deletion_blocked_by_active_and_scheduled_auction_statuses(): void
    {
        $activeAuctionStatuses = ['live', 'scheduled'];

        foreach ($activeAuctionStatuses as $status) {
            $seller = $this->createApprovedSeller();
            $product = $this->createProduct($seller, "Auction Lot in {$status}", 1000.00, 100);

            Auction::create([
                'product_id'        => $product->id,
                'seller_id'         => $seller->sellerProfile->id,
                'starting_price'    => 800.00,
                'reserve_price'     => 950.00,
                'current_price'     => 850.00,
                'minimum_increment' => 50.00,
                'starts_at'         => Carbon::now()->subMinutes(10),
                'ends_at'           => Carbon::now()->addHours(2),
                'status'            => $status,
            ]);

            $response = $this->actingAs($seller, 'seller')->delete(route('seller.products.destroy', $product));

            $response->assertSessionHas('error');
            $this->assertDatabaseHas('products', ['id' => $product->id]);
        }
    }

    /**
     * Challenge 4.5: Safe deletion is ALLOWED for terminal auction statuses.
     * Statuses: 'ended', 'cancelled'.
     */
    public function test_safe_deletion_allowed_for_terminal_auction_statuses(): void
    {
        $terminalAuctionStatuses = ['ended', 'cancelled'];

        foreach ($terminalAuctionStatuses as $status) {
            $seller = $this->createApprovedSeller();
            $product = $this->createProduct($seller, "Auction Lot {$status}", 500.00, 50);

            Auction::create([
                'product_id'        => $product->id,
                'seller_id'         => $seller->sellerProfile->id,
                'starting_price'    => 400.00,
                'reserve_price'     => 480.00,
                'current_price'     => 490.00,
                'minimum_increment' => 20.00,
                'starts_at'         => Carbon::now()->subHours(5),
                'ends_at'           => Carbon::now()->subHour(),
                'status'            => $status,
            ]);

            $response = $this->actingAs($seller, 'seller')->delete(route('seller.products.destroy', $product));

            $response->assertRedirect(route('seller.products.index'));
            $response->assertSessionHas('success');
            $this->assertDatabaseMissing('products', ['id' => $product->id]);
        }
    }

    /**
     * Challenge 4.6: Safe deletion via JSON API returns HTTP 422 with structured rejection message.
     */
    public function test_safe_deletion_json_api_returns_422_with_clear_error_payload(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = User::factory()->create(['role' => 'user']);
        $product = $this->createProduct($seller, 'Protected API Lot', 180.00, 15);

        $this->attachOrderToProduct($seller, $buyer, $product, 'processing');

        $response = $this->actingAs($seller, 'seller')
            ->deleteJson(route('seller.products.destroy', $product));

        $response->assertStatus(422);
        $response->assertJsonStructure(['message']);
        $this->assertStringContainsString('active unfulfilled orders', $response->json('message'));
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    /**
     * Challenge 4.7: Sequential deletion handling: first deletion succeeds, subsequent returns HTTP 404.
     */
    public function test_concurrent_deletion_handling_second_call_returns_404(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProduct($seller, 'One Time Removable Crop', 95.00, 10);
        $productId = $product->id;

        // First deletion
        $resp1 = $this->actingAs($seller, 'seller')->delete(route('seller.products.destroy', $product));
        $resp1->assertRedirect(route('seller.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $productId]);

        // Second deletion on the same route parameter
        $resp2 = $this->actingAs($seller, 'seller')->delete(route('seller.products.destroy', $productId));
        $resp2->assertStatus(404);
    }

    // =========================================================================
    // SECTION 5: INVENTORY STOCK MANIPULATION EDGE CASES & GUARDRAILS
    // =========================================================================

    /**
     * Challenge 5.1: Stock reduction greater than current stock clamps to 0 (never negative).
     */
    public function test_stock_reduction_greater_than_current_stock_clamps_to_zero_not_negative(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProduct($seller, 'Low Quantity Crop', 50.00, 8);

        // Attempt reducing by 20 (8 - 20 = -12)
        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.adjust-stock', $product), [
            'action'   => 'reduce',
            'quantity' => 20,
            'reason'   => 'Excess spoilage write-off',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(0, $product->fresh()->stock, 'Stock reduction beyond current quantity must clamp to 0.');
    }

    /**
     * Challenge 5.2: Stock adjustment validation rejects negative quantity.
     */
    public function test_stock_adjustment_validation_rejects_negative_quantity(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProduct($seller, 'Valid Grain Lot', 70.00, 30);

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.adjust-stock', $product), [
            'action'   => 'add',
            'quantity' => -15,
            'reason'   => 'Negative quantity hack',
        ]);

        $response->assertSessionHasErrors(['quantity']);
        $this->assertEquals(30, $product->fresh()->stock);
    }

    /**
     * Challenge 5.3: Stock adjustment 'set' to exact 0 updates out_of_stock metrics.
     */
    public function test_stock_adjustment_set_to_exact_zero_transitions_product_to_out_of_stock(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProduct($seller, 'Clearing Inventory', 120.00, 45);

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.adjust-stock', $product), [
            'action'   => 'set',
            'quantity' => 0,
            'reason'   => 'All sold out via farm gate',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(0, $product->fresh()->stock);

        // Inventory view confirms out_of_stock count
        $respInv = $this->actingAs($seller, 'seller')->get(route('seller.products.inventory'));
        $respInv->assertStatus(200);
        $respInv->assertViewHas('outOfStockCount', 1);
    }

    /**
     * Challenge 5.4: Astronomical stock and price values do not cause arithmetic overflows.
     */
    public function test_astronomical_stock_and_price_values_do_not_overflow(): void
    {
        $seller = $this->createApprovedSeller();

        $payload = [
            'name'                 => 'Large Commercial Grain Elevator Reserve',
            'category_id'          => $this->categoryVegetables->id,
            'price'                => 999999.99,
            'unit_type'            => 'kg',
            'stock'                => 1000000,
            'low_stock_threshold'  => 50000,
            'status'               => 'active',
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.products.store'), $payload);
        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('name', 'Large Commercial Grain Elevator Reserve')->first();
        $this->assertNotNull($product);
        $this->assertEquals(999999.99, (float) $product->price);
        $this->assertEquals(1000000, $product->stock);

        // View inventory
        $respInv = $this->actingAs($seller, 'seller')->get(route('seller.products.inventory'));
        $respInv->assertStatus(200);
        $respInv->assertSee('1000000');
        $respInv->assertSee('Large Commercial Grain Elevator Reserve');
    }

    /**
     * Challenge 5.5: Low stock threshold custom boundary tests (threshold = 25).
     * Stock = 26 -> Healthy. Stock = 25 -> Low Stock. Stock = 24 -> Low Stock.
     */
    public function test_low_stock_threshold_custom_boundary_evaluations(): void
    {
        $seller = $this->createApprovedSeller();

        // 1. Boundary: Stock 26 with threshold 25 -> healthy
        $productHealthy = Product::create([
            'seller_id'           => $seller->id,
            'category_id'         => $this->categoryFruits->id,
            'name'                => 'Threshold Healthy Item',
            'slug'                => 'thresh-healthy-' . uniqid(),
            'sku'                 => 'BZ-TH-26',
            'unit_type'           => 'kg',
            'price'               => 100.00,
            'stock'               => 26,
            'low_stock_threshold' => 25,
            'status'              => 'active',
            'is_perishable'       => false,
        ]);

        $this->assertFalse($productHealthy->isLowStock(), 'Stock 26 > threshold 25 must NOT be low stock.');

        // 2. Boundary: Stock 25 with threshold 25 -> low stock (inclusive boundary)
        $productBoundary = Product::create([
            'seller_id'           => $seller->id,
            'category_id'         => $this->categoryFruits->id,
            'name'                => 'Threshold Boundary Item',
            'slug'                => 'thresh-bound-' . uniqid(),
            'sku'                 => 'BZ-TH-25',
            'unit_type'           => 'kg',
            'price'               => 100.00,
            'stock'               => 25,
            'low_stock_threshold' => 25,
            'status'              => 'active',
            'is_perishable'       => false,
        ]);

        $this->assertTrue($productBoundary->isLowStock(), 'Stock 25 <= threshold 25 must be low stock.');

        // 3. Boundary: Stock 24 with threshold 25 -> low stock
        $productLow = Product::create([
            'seller_id'           => $seller->id,
            'category_id'         => $this->categoryFruits->id,
            'name'                => 'Threshold Depleted Item',
            'slug'                => 'thresh-low-' . uniqid(),
            'sku'                 => 'BZ-TH-24',
            'unit_type'           => 'kg',
            'price'               => 100.00,
            'stock'               => 24,
            'low_stock_threshold' => 25,
            'status'              => 'active',
            'is_perishable'       => false,
        ]);

        $this->assertTrue($productLow->isLowStock(), 'Stock 24 <= threshold 25 must be low stock.');

        // Scope verification
        $lowStockIds = Product::lowStock()->pluck('id')->toArray();
        $this->assertContains($productBoundary->id, $lowStockIds);
        $this->assertContains($productLow->id, $lowStockIds);
        $this->assertNotContains($productHealthy->id, $lowStockIds);
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
            'shop_name'           => 'Empirical Agro Merchant ' . $seller->id,
            'shop_slug'           => 'empirical-agro-' . $seller->id,
            'seller_type'         => 'Farmer',
            'city'                => 'Contai',
            'state'               => 'West Bengal',
            'status'              => 'approved',
            'trust_score'         => 94.00,
            'commission_rate'     => 10.00,
            'address'             => 'Plot 77, Agro Research Complex',
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
            'shop_name'           => 'Pending Application ' . $seller->id,
            'shop_slug'           => 'pending-app-' . $seller->id,
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

    protected function createProduct(User $seller, string $name, float $price, int $stock, string $unitType = 'kg', int $threshold = 10, string $status = 'active'): Product
    {
        return Product::create([
            'seller_id'           => $seller->id,
            'category_id'         => $this->categoryFruits->id,
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

    protected function attachOrderToProduct(User $seller, User $buyer, Product $product, string $sellerOrderStatus): SellerOrder
    {
        $order = Order::create([
            'order_number'            => 'ORD-' . uniqid(),
            'user_id'                 => $buyer->id,
            'subtotal'                => $product->price * 2,
            'total_amount'            => $product->price * 2,
            'payment_method'          => 'cod',
            'payment_status'          => $sellerOrderStatus === 'delivered' ? 'paid' : 'pending',
            'order_status'            => $sellerOrderStatus === 'delivered' ? 'completed' : 'processing',
            'delivery_full_name'      => $buyer->name,
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Agro Test Facility',
            'delivery_city'           => 'Contai',
            'delivery_state'          => 'West Bengal',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '721401',
        ]);

        $sellerOrder = SellerOrder::create([
            'order_id'            => $order->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => 'SO-' . uniqid(),
            'subtotal'            => $product->price * 2,
            'shipping_amount'     => 0.00,
            'commission_rate'     => 10.00,
            'commission_amount'   => round(($product->price * 2) * 0.10, 2),
            'payout_amount'       => round(($product->price * 2) * 0.90, 2),
            'status'              => $sellerOrderStatus,
            'delivery_slot'       => 'Morning 8AM - 11AM',
        ]);

        OrderItem::create([
            'seller_order_id' => $sellerOrder->id,
            'product_id'      => $product->id,
            'product_name'    => $product->name,
            'sku'             => $product->sku,
            'unit_price'      => $product->price,
            'quantity'        => 2,
            'total_price'     => $product->price * 2,
        ]);

        return $sellerOrder;
    }
}
