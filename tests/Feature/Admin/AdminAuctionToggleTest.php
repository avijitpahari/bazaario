<?php

namespace Tests\Feature\Admin;

use App\Models\Auction;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuctionToggleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected User $sellerUser;
    protected SellerProfile $sellerProfile;
    protected Auction $auction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@bazaario.com',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
            'status'   => 'active',
        ]);

        $this->customer = User::create([
            'name'     => 'Rohit Bidder',
            'email'    => 'rohit@bazaario.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
            'status'   => 'active',
        ]);

        $this->sellerUser = User::create([
            'name'     => 'Artisan Seller',
            'email'    => 'artisan@bazaario.com',
            'password' => Hash::make('password123'),
            'role'     => 'seller',
            'status'   => 'active',
        ]);

        $this->sellerProfile = SellerProfile::create([
            'user_id'         => $this->sellerUser->id,
            'shop_name'       => 'Artisan Pottery',
            'shop_slug'       => 'artisan-pottery',
            'seller_type'     => 'artisan',
            'status'          => 'approved',
            'commission_rate' => 8.5,
        ]);

        $category = Category::create([
            'name'   => 'Handmade Terracotta',
            'slug'   => 'handmade-terracotta',
            'status' => 'active',
        ]);

        $product = Product::create([
            'seller_id'    => $this->sellerUser->id,
            'category_id'  => $category->id,
            'name'         => 'Terracotta Vase Lot',
            'slug'         => 'terracotta-vase-lot',
            'price'        => 500,
            'stock'        => 10,
            'status'       => 'active',
        ]);

        $this->auction = Auction::create([
            'product_id'        => $product->id,
            'seller_id'         => $this->sellerProfile->id,
            'starting_price'    => 500,
            'current_price'     => 500,
            'reserve_price'     => 800,
            'minimum_increment' => 50,
            'starts_at'         => now()->subMinutes(10),
            'ends_at'           => now()->addHours(2),
            'status'            => 'live',
        ]);
    }

    public function test_admin_can_toggle_auction_system_via_terminal_button(): void
    {
        // Initially enabled
        $this->assertTrue(SiteSetting::isAuctionEnabled());

        // Admin pauses auctions
        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.auctions.toggleSystem'), [
            'enabled' => '0',
        ]);
        $response->assertStatus(302);
        $this->assertFalse(SiteSetting::isAuctionEnabled());

        // Admin enables auctions back
        $enableResponse = $this->actingAs($this->admin, 'admin')->post(route('admin.auctions.toggleSystem'), [
            'enabled' => '1',
        ]);
        $enableResponse->assertStatus(302);
        $this->assertTrue(SiteSetting::isAuctionEnabled());
    }

    public function test_admin_can_update_auction_status_via_platform_settings(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.settings.update'), [
            'auction_system_enabled' => '0',
        ]);
        $response->assertStatus(302);
        $this->assertFalse(SiteSetting::isAuctionEnabled());

        $response2 = $this->actingAs($this->admin, 'admin')->post(route('admin.settings.update'), [
            'auction_system_enabled' => '1',
        ]);
        $response2->assertStatus(302);
        $this->assertTrue(SiteSetting::isAuctionEnabled());
    }

    public function test_when_auctions_disabled_user_cannot_place_bid(): void
    {
        SiteSetting::set('auction_system_enabled', '0');
        $this->assertFalse(SiteSetting::isAuctionEnabled());

        $bidResponse = $this->actingAs($this->customer, 'user')->post(route('auctions.placeBid', $this->auction->id), [
            'amount' => 600,
        ]);

        $bidResponse->assertSessionHas('error');
        $this->assertStringContainsString('paused', session('error'));

        // JSON check
        $jsonResponse = $this->actingAs($this->customer, 'user')->postJson(route('auctions.placeBid', $this->auction->id), [
            'amount' => 600,
        ]);
        $jsonResponse->assertStatus(403);
    }

    public function test_when_auctions_disabled_seller_cannot_create_auction(): void
    {
        SiteSetting::set('auction_system_enabled', '0');

        $response = $this->actingAs($this->sellerUser, 'seller')->get(route('seller.auctions.create'));
        $response->assertRedirect(route('seller.auctions.index'));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('paused', session('error'));
    }

    public function test_auctions_index_and_detail_render_paused_banner_when_disabled(): void
    {
        SiteSetting::set('auction_system_enabled', '0');

        $indexView = $this->get(route('auctions.index'));
        $indexView->assertStatus(200);
        $indexView->assertSee('WHOLESALE AUCTIONS PAUSED');

        $showView = $this->get(route('auctions.show', $this->auction->id));
        $showView->assertStatus(200);
        $showView->assertSee('Wholesale Auction Floor Paused');
    }
}
