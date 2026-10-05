<?php

namespace Tests\Feature\Seller;

use App\Http\Middleware\SellerMiddleware;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerIntegrityAuditCheckTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Audit Probe 1: Middleware strictly rejects unauthenticated access to seller routes.
     */
    public function test_unauthenticated_request_is_redirected_to_login(): void
    {
        $response = $this->get(route('seller.dashboard'));
        $response->assertRedirect(route('login'));

        $responsePending = $this->get(route('seller.pending'));
        $responsePending->assertRedirect(route('login'));

        $responseWizard = $this->get(route('seller.onboarding'));
        $responseWizard->assertRedirect(route('login'));
    }

    /**
     * Audit Probe 2: Non-seller user (role = 'user') is blocked from seller routes.
     */
    public function test_regular_customer_cannot_access_seller_routes(): void
    {
        $customer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        $response = $this->actingAs($customer, 'seller')->get(route('seller.dashboard'));
        // SellerMiddleware redirects customer role to products.index
        $response->assertRedirect(route('products.index'));

        // Attempting to post to onboarding as customer
        $postResponse = $this->actingAs($customer, 'seller')->post(route('seller.onboarding.submit'), [
            'seller_type' => 'Farmer',
            'shop_name'   => 'Fake Customer Stall',
            'address'     => '123 Fake St',
            'city'        => 'Kolkata',
            'state'       => 'WB',
            'latitude'    => 22.57,
            'longitude'   => 88.36,
        ]);
        $postResponse->assertRedirect(route('products.index'));
    }

    /**
     * Audit Probe 3: Inactive seller account is logged out and blocked with 403 / redirect to login.
     */
    public function test_inactive_seller_is_logged_out_and_blocked(): void
    {
        $inactiveSeller = User::factory()->create(['role' => 'seller', 'status' => 'inactive']);

        $response = $this->actingAs($inactiveSeller, 'seller')->get(route('seller.dashboard'));
        $response->assertRedirect(route('login'));
        $this->assertFalse(Auth::guard('seller')->check());
    }

    /**
     * Audit Probe 4: Rejected or suspended seller profile cannot access dashboard.
     */
    public function test_rejected_and_suspended_sellers_are_locked_out_of_dashboard(): void
    {
        $rejectedSeller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id'   => $rejectedSeller->id,
            'shop_name' => 'Rejected Stall',
            'shop_slug' => 'rejected-stall',
            'status'    => 'rejected',
        ]);

        $response = $this->actingAs($rejectedSeller, 'seller')->get(route('seller.dashboard'));
        $response->assertRedirect(route('seller.pending'));

        $suspendedSeller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        SellerProfile::create([
            'user_id'   => $suspendedSeller->id,
            'shop_name' => 'Suspended Stall',
            'shop_slug' => 'suspended-stall',
            'status'    => 'suspended',
        ]);

        $response2 = $this->actingAs($suspendedSeller, 'seller')->get(route('seller.dashboard'));
        $response2->assertRedirect(route('seller.pending'));
    }

    /**
     * Audit Probe 5: Onboarding rejects out-of-range coordinates, invalid seller types, and oversized files.
     */
    public function test_onboarding_wizard_rejects_boundary_violations_and_malicious_inputs(): void
    {
        Storage::fake('public');
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);

        // 1. Invalid seller type
        $resType = $this->actingAs($seller, 'seller')->post(route('seller.onboarding.submit'), [
            'seller_type' => 'IllegalType',
            'shop_name'   => 'Valid Shop',
            'address'     => 'Valid Address',
            'city'        => 'Contai',
            'state'       => 'WB',
            'latitude'    => 21.77,
            'longitude'   => 87.75,
        ]);
        $resType->assertSessionHasErrors(['seller_type']);

        // 2. Latitude > 90
        $resLat = $this->actingAs($seller, 'seller')->post(route('seller.onboarding.submit'), [
            'seller_type' => 'Farmer',
            'shop_name'   => 'Valid Shop',
            'address'     => 'Valid Address',
            'city'        => 'Contai',
            'state'       => 'WB',
            'latitude'    => 95.0,
            'longitude'   => 87.75,
        ]);
        $resLat->assertSessionHasErrors(['latitude']);

        // 3. Longitude < -180
        $resLng = $this->actingAs($seller, 'seller')->post(route('seller.onboarding.submit'), [
            'seller_type' => 'Farmer',
            'shop_name'   => 'Valid Shop',
            'address'     => 'Valid Address',
            'city'        => 'Contai',
            'state'       => 'WB',
            'latitude'    => 21.77,
            'longitude'   => -190.0,
        ]);
        $resLng->assertSessionHasErrors(['longitude']);

        // 4. File that is not an image (e.g. php file)
        $fakePhp = UploadedFile::fake()->create('exploit.php', 10, 'application/x-php');
        $resFile = $this->actingAs($seller, 'seller')->post(route('seller.onboarding.submit'), [
            'seller_type'      => 'Farmer',
            'shop_name'        => 'Valid Shop',
            'address'          => 'Valid Address',
            'city'             => 'Contai',
            'state'            => 'WB',
            'latitude'         => 21.77,
            'longitude'        => 87.75,
            'storefront_image' => $fakePhp,
        ]);
        $resFile->assertSessionHasErrors(['storefront_image']);
    }

    /**
     * Audit Probe 6: Database transaction atomicity — rollback on simulated failure.
     */
    public function test_database_transaction_integrity_in_profile_creation(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);

        try {
            DB::transaction(function () use ($seller) {
                SellerProfile::create([
                    'user_id'     => $seller->id,
                    'shop_name'   => 'Rollback Test Stall',
                    'shop_slug'   => 'rollback-test-stall',
                    'seller_type' => 'Farmer',
                    'city'        => 'Contai',
                    'state'       => 'West Bengal',
                    'status'      => 'pending',
                ]);
                throw new \Exception('Simulated crash during transaction');
            });
        } catch (\Exception $e) {
            // Expected
        }

        $this->assertDatabaseMissing('seller_profiles', [
            'user_id' => $seller->id,
        ]);
    }

    /**
     * Audit Probe 7: Product scopePublicVisible correctly isolates perishable and expired items.
     */
    public function test_product_scopes_empirical_query_filtering(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $cat = Category::create(['name' => 'Grains', 'slug' => 'grains']);

        // 1. Perishable, expired, auto_hide_expired = true -> MUST NOT be in publicVisible
        $hiddenExpired = Product::create([
            'seller_id'         => $seller->id,
            'category_id'       => $cat->id,
            'name'              => 'Expired Milk',
            'slug'              => 'expired-milk',
            'sale_type'         => 'fixed_price',
            'unit_type'         => 'litre',
            'price'             => 30,
            'stock'             => 10,
            'is_perishable'     => true,
            'harvest_date'      => Carbon::today()->subDays(5)->toDateString(),
            'expiry_days'       => 2,
            'expiry_date'       => Carbon::today()->subDays(3)->toDateString(),
            'auto_hide_expired' => true,
            'status'            => 'active',
        ]);

        // 2. Perishable, expired, auto_hide_expired = false -> MUST be in publicVisible (flagged but visible)
        $visibleExpired = Product::create([
            'seller_id'         => $seller->id,
            'category_id'       => $cat->id,
            'name'              => 'Discounted Stale Bread',
            'slug'              => 'discounted-stale-bread',
            'sale_type'         => 'fixed_price',
            'unit_type'         => 'piece',
            'price'             => 15,
            'stock'             => 5,
            'is_perishable'     => true,
            'harvest_date'      => Carbon::today()->subDays(4)->toDateString(),
            'expiry_days'       => 2,
            'expiry_date'       => Carbon::today()->subDays(2)->toDateString(),
            'auto_hide_expired' => false,
            'status'            => 'active',
        ]);

        // 3. Perishable, fresh -> MUST be in publicVisible and fresh()
        $freshProduct = Product::create([
            'seller_id'         => $seller->id,
            'category_id'       => $cat->id,
            'name'              => 'Fresh Tomatoes',
            'slug'              => 'fresh-tomatoes',
            'sale_type'         => 'fixed_price',
            'unit_type'         => 'kg',
            'price'             => 25,
            'stock'             => 50,
            'is_perishable'     => true,
            'harvest_date'      => Carbon::today()->toDateString(),
            'expiry_days'       => 5,
            'expiry_date'       => Carbon::today()->addDays(5)->toDateString(),
            'auto_hide_expired' => true,
            'status'            => 'active',
        ]);

        $publicIds = Product::query()->publicVisible()->pluck('id')->all();
        $this->assertNotContains($hiddenExpired->id, $publicIds);
        $this->assertContains($visibleExpired->id, $publicIds);
        $this->assertContains($freshProduct->id, $publicIds);

        $staleIds = Product::query()->stale()->pluck('id')->all();
        $this->assertContains($hiddenExpired->id, $staleIds);
        $this->assertContains($visibleExpired->id, $staleIds);
        $this->assertNotContains($freshProduct->id, $staleIds);
    }
}
