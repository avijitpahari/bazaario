<?php

namespace Tests\Feature\Seller;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payout;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DynamicCommissionRateTest extends TestCase
{
    use RefreshDatabase, SellerTestHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear site setting cache
        \Illuminate\Support\Facades\Cache::forget('site_settings_all');
    }

    /**
     * Test 1: Seller inherits dynamic platform commission rate set by admin in SiteSetting.
     */
    public function test_seller_inherits_admin_global_commission_setting(): void
    {
        // Admin sets platform commission to 7.5%
        SiteSetting::set('commission_rate', 7.5);

        $seller = $this->createApprovedSeller([], ['commission_rate' => null]);
        $profile = $seller->sellerProfile;

        $this->assertEquals(7.5, $profile->getEffectiveCommissionRate());
        $this->assertEquals(7.5, $seller->getEffectiveCommissionRate());

        $response = $this->actingAs($seller, 'seller')->get(route('seller.payouts.index'));
        $response->assertStatus(200);
        $response->assertSee('7.5%');
        $response->assertSee('No hidden fees');
    }

    /**
     * Test 2: Seller with custom admin commission override uses their custom rate.
     */
    public function test_seller_uses_custom_admin_commission_override(): void
    {
        SiteSetting::set('commission_rate', 10.0);

        // Admin gave this specific seller a 5.0% VIP rate
        $seller = $this->createApprovedSeller([], ['commission_rate' => 5.0]);
        $profile = $seller->sellerProfile;

        $this->assertEquals(5.0, $profile->getEffectiveCommissionRate());
        $this->assertEquals(5.0, $seller->getEffectiveCommissionRate());

        $response = $this->actingAs($seller, 'seller')->get(route('seller.payouts.index'));
        $response->assertStatus(200);
        $response->assertSee('5.0%');
        $response->assertSee('Custom Agreement');
    }

    /**
     * Test 3: Changing admin global commission rate immediately updates payouts ledger view.
     */
    public function test_updating_admin_commission_rate_reflects_in_payouts_ledger(): void
    {
        SiteSetting::set('commission_rate', 8.5);

        $seller = $this->createApprovedSeller([], ['commission_rate' => null]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.payouts.index'));
        $response->assertStatus(200);
        $response->assertSee('8.5%');

        // Admin updates rate to 12.0%
        SiteSetting::set('commission_rate', 12.0);

        $response2 = $this->actingAs($seller, 'seller')->get(route('seller.payouts.index'));
        $response2->assertStatus(200);
        $response2->assertSee('12.0%');
    }

    /**
     * Test 4: Checkout creates SellerOrder with dynamic commission rate from admin.
     */
    public function test_checkout_uses_dynamic_admin_commission_rate(): void
    {
        SiteSetting::set('commission_rate', 6.5);

        $seller = $this->createApprovedSeller([], ['commission_rate' => null]);
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Fresh Strawberries', 500.00, 100);

        $address = \App\Models\Address::create([
            'user_id'        => $buyer->id,
            'type'           => 'home',
            'full_name'      => $buyer->name,
            'phone'          => '9876543210',
            'address_line_1' => 'Plot 42, Sector V, Salt Lake',
            'city'           => 'Kolkata',
            'state'          => 'West Bengal',
            'postal_code'    => '700091',
            'country'        => 'India',
            'is_default'     => true,
        ]);

        // Buyer creates cart and adds item
        $cart = \App\Models\Cart::create(['user_id' => $buyer->id]);
        \App\Models\CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 2,
            'unit_price' => 500.00,
        ]);

        $response = $this->actingAs($buyer, 'user')->post('/checkout', [
            'address_id'     => $address->id,
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect();

        $sellerOrder = SellerOrder::where('seller_id', $seller->id)->first();
        $this->assertNotNull($sellerOrder);
        // Subtotal = 500 * 2 = 1000
        $this->assertEquals(1000.00, (float) $sellerOrder->subtotal);
        $this->assertEquals(6.5, (float) $sellerOrder->commission_rate);
        $this->assertEquals(65.00, (float) $sellerOrder->commission_amount);
        $this->assertEquals(935.00, (float) $sellerOrder->payout_amount);
    }

    /**
     * Test 5: Seller orders show and index views display dynamic commission rate.
     */
    public function test_seller_order_views_display_dynamic_commission_rate(): void
    {
        SiteSetting::set('commission_rate', 8.5);

        $seller = $this->createApprovedSeller([], ['commission_rate' => null]);
        $buyer = $this->createBuyer();

        $sellerOrder = $this->createSellerOrderRecord($seller, $buyer, 2000.00, 'SO-DYN-01', 'placed', [
            'commission_rate'   => 8.5,
            'commission_amount' => 170.00,
            'payout_amount'     => 1830.00,
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.orders.index', ['order_id' => $sellerOrder->id]));
        $response->assertStatus(200);
        $response->assertSee('8.5%');
        $response->assertSee('170.00');

        $responseShow = $this->actingAs($seller, 'seller')->get(route('seller.orders.show', $sellerOrder));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('8.5%');
        $responseShow->assertSee('170.00');
    }

    /**
     * Test 6: Admin updating a seller's commission rate automatically synchronizes their existing orders and payouts.
     */
    public function test_admin_commission_update_synchronizes_existing_orders_and_payouts(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@bazaario.com']);
        $seller = $this->createApprovedSeller([], ['commission_rate' => 10.00]);
        $buyer = $this->createBuyer();

        $sellerOrder = $this->createSellerOrderRecord($seller, $buyer, 1000.00, 'SO-SYNC-01', 'delivered', [
            'commission_rate'   => 10.00,
            'commission_amount' => 100.00,
            'payout_amount'     => 900.00,
        ]);

        $payout = Payout::create([
            'seller_id'         => $seller->id,
            'seller_order_id'   => $sellerOrder->id,
            'gross_amount'      => 1000.00,
            'commission_amount' => 100.00,
            'apmc_cess'         => 0.00,
            'net_amount'        => 900.00,
            'status'            => 'pending',
            'payout_reference'  => 'PO-SYNC-01',
        ]);

        // Admin updates seller's commission to 5.0%
        $response = $this->actingAs($admin, 'admin')->post(route('admin.sellers.commission', $seller->sellerProfile->id), [
            'commission_rate' => 5.0,
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Verify seller profile is updated
        $profile = $seller->sellerProfile->fresh();
        $this->assertEquals(5.0, (float) $profile->commission_rate);

        // Verify order is synchronized to 5.0%
        $sellerOrder->refresh();
        $this->assertEquals(5.0, (float) $sellerOrder->commission_rate);
        $this->assertEquals(50.00, (float) $sellerOrder->commission_amount);
        $this->assertEquals(935.00, (float) $sellerOrder->payout_amount);

        // Verify payout is synchronized to 5.0%
        $payout->refresh();
        $this->assertEquals(50.00, (float) $payout->commission_amount);
        $this->assertEquals(935.00, (float) $payout->net_amount);

        // Payouts index should now show 5.0% and ₹50.00
        $payoutsResponse = $this->actingAs($seller, 'seller')->get(route('seller.payouts.index'));
        $payoutsResponse->assertStatus(200);
        $payoutsResponse->assertSee('5.0%');
        $payoutsResponse->assertSee('50.00');
        $payoutsResponse->assertSee('935.00');
    }
}
