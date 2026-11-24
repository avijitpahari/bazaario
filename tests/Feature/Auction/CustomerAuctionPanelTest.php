<?php

namespace Tests\Feature\Auction;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class CustomerAuctionPanelTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;
    protected User $sellerUser;
    protected SellerProfile $sellerProfile;
    protected Product $product;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name'     => 'Auction Customer',
            'email'    => 'customer_' . Str::random(6) . '@bazaario.test',
            'role'     => 'user',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ]);

        $this->otherUser = User::factory()->create([
            'name'     => 'Rival Bidder',
            'email'    => 'rival_' . Str::random(6) . '@bazaario.test',
            'role'     => 'user',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ]);

        $this->sellerUser = User::factory()->create([
            'name'     => 'Seller Account',
            'email'    => 'seller_' . Str::random(6) . '@bazaario.test',
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ]);

        $this->sellerProfile = SellerProfile::create([
            'user_id'         => $this->sellerUser->id,
            'shop_name'       => 'Vintage Antiques Hub',
            'shop_slug'       => 'vintage-antiques-' . Str::random(6),
            'seller_type'     => 'Individual',
            'city'            => 'Kolkata',
            'state'           => 'West Bengal',
            'country'         => 'India',
            'status'          => 'approved',
            'commission_rate' => 8.50,
            'trust_score'     => 94.00,
        ]);

        $this->category = Category::firstOrCreate(
            ['slug' => 'antiques'],
            ['name' => 'Antiques', 'status' => 'active']
        );

        $this->product = Product::create([
            'seller_id'   => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name'        => 'Ancient Mughal Coin',
            'slug'        => 'ancient-mughal-coin-' . Str::random(6),
            'description' => 'Genuine historical rare coin artifact.',
            'price'       => 5000.00,
            'stock'       => 1,
            'status'      => 'active',
        ]);
    }

    public function test_customer_auction_panel_renders_all_dynamic_pills_and_metrics(): void
    {
        // 1. Live auction (user is leading)
        $liveAuction1 = Auction::create([
            'product_id'        => $this->product->id,
            'seller_id'         => $this->sellerProfile->id,
            'starting_price'    => 1000.00,
            'reserve_price'     => 1500.00,
            'current_price'     => 2000.00,
            'minimum_increment' => 100.00,
            'starts_at'         => now()->subHour(),
            'ends_at'           => now()->addHours(5),
            'status'            => 'live',
        ]);
        AuctionBid::create([
            'auction_id' => $liveAuction1->id,
            'user_id'    => $this->user->id,
            'amount'     => 2000.00,
        ]);

        // 2. Live auction (user is outbid)
        $liveAuction2 = Auction::create([
            'product_id'        => $this->product->id,
            'seller_id'         => $this->sellerProfile->id,
            'starting_price'    => 2000.00,
            'reserve_price'     => 3000.00,
            'current_price'     => 3500.00,
            'minimum_increment' => 200.00,
            'starts_at'         => now()->subHour(),
            'ends_at'           => now()->addMinutes(20), // Ending soon
            'status'            => 'live',
        ]);
        AuctionBid::create([
            'auction_id' => $liveAuction2->id,
            'user_id'    => $this->user->id,
            'amount'     => 3000.00,
        ]);
        AuctionBid::create([
            'auction_id' => $liveAuction2->id,
            'user_id'    => $this->otherUser->id,
            'amount'     => 3500.00,
        ]);

        // 3. Scheduled / Upcoming auction
        $upcomingAuction = Auction::create([
            'product_id'        => $this->product->id,
            'seller_id'         => $this->sellerProfile->id,
            'starting_price'    => 500.00,
            'reserve_price'     => 1000.00,
            'current_price'     => 500.00,
            'minimum_increment' => 50.00,
            'starts_at'         => now()->addDay(),
            'ends_at'           => now()->addDays(2),
            'status'            => 'scheduled',
        ]);

        // 4. Won auction by user
        $wonAuction = Auction::create([
            'product_id'        => $this->product->id,
            'seller_id'         => $this->sellerProfile->id,
            'starting_price'    => 1000.00,
            'reserve_price'     => 1200.00,
            'current_price'     => 1800.00,
            'minimum_increment' => 100.00,
            'starts_at'         => now()->subDays(2),
            'ends_at'           => now()->subDay(),
            'status'            => 'ended',
            'winner_id'         => $this->user->id,
        ]);
        AuctionBid::create([
            'auction_id' => $wonAuction->id,
            'user_id'    => $this->user->id,
            'amount'     => 1800.00,
        ]);

        $response = $this->actingAs($this->user)->get(route('auctions.index'));

        $response->assertOk();
        $response->assertSee('Live Floor (2)');
        $response->assertSee('Upcoming (1)');
        $response->assertSee('Ending Soon (1)');
        $response->assertSee('Active Bids (2)');
        $response->assertSee('Reserve Met (2)');
        $response->assertSee('Outbid Alerts (1)');
        $response->assertSee('Won Lots (1)');

        // Check dynamic telemetry data passed to view
        $response->assertViewHas('telemetry', function ($telemetry) {
            return $telemetry['total_lots'] === 2
                && $telemetry['upcoming_count'] === 1
                && $telemetry['ending_soon_count'] === 1
                && $telemetry['user_active_count'] === 2
                && $telemetry['user_leading_count'] === 1
                && $telemetry['user_outbid_count'] === 1
                && $telemetry['won_count'] === 1
                && $telemetry['reserve_met_count'] === 2;
        });
    }

    public function test_customer_auction_panel_filters_active_bids_correctly(): void
    {
        $liveAuction = Auction::create([
            'product_id'        => $this->product->id,
            'seller_id'         => $this->sellerProfile->id,
            'starting_price'    => 1000.00,
            'reserve_price'     => 1500.00,
            'current_price'     => 2000.00,
            'minimum_increment' => 100.00,
            'starts_at'         => now()->subHour(),
            'ends_at'           => now()->addHours(5),
            'status'            => 'live',
        ]);
        AuctionBid::create([
            'auction_id' => $liveAuction->id,
            'user_id'    => $this->user->id,
            'amount'     => 2000.00,
        ]);

        // Another live auction user did NOT bid on
        $unbiddedLiveAuction = Auction::create([
            'product_id'        => $this->product->id,
            'seller_id'         => $this->sellerProfile->id,
            'starting_price'    => 1000.00,
            'reserve_price'     => 1500.00,
            'current_price'     => 1000.00,
            'minimum_increment' => 100.00,
            'starts_at'         => now()->subHour(),
            'ends_at'           => now()->addHours(5),
            'status'            => 'live',
        ]);

        $response = $this->actingAs($this->user)->get(route('auctions.index', ['filter' => 'active_bids']));

        $response->assertOk();
        $response->assertViewHas('auctions', function ($auctions) use ($liveAuction, $unbiddedLiveAuction) {
            return $auctions->contains('id', $liveAuction->id)
                && !$auctions->contains('id', $unbiddedLiveAuction->id);
        });
    }

    public function test_customer_auction_panel_filters_outbid_alerts_correctly(): void
    {
        // Auction where user is outbid
        $outbidAuction = Auction::create([
            'product_id'        => $this->product->id,
            'seller_id'         => $this->sellerProfile->id,
            'starting_price'    => 1000.00,
            'reserve_price'     => 1500.00,
            'current_price'     => 2200.00,
            'minimum_increment' => 100.00,
            'starts_at'         => now()->subHour(),
            'ends_at'           => now()->addHours(5),
            'status'            => 'live',
        ]);
        AuctionBid::create([
            'auction_id' => $outbidAuction->id,
            'user_id'    => $this->user->id,
            'amount'     => 2000.00,
        ]);
        AuctionBid::create([
            'auction_id' => $outbidAuction->id,
            'user_id'    => $this->otherUser->id,
            'amount'     => 2200.00,
        ]);

        // Auction where user is leading
        $leadingAuction = Auction::create([
            'product_id'        => $this->product->id,
            'seller_id'         => $this->sellerProfile->id,
            'starting_price'    => 1000.00,
            'reserve_price'     => 1500.00,
            'current_price'     => 2000.00,
            'minimum_increment' => 100.00,
            'starts_at'         => now()->subHour(),
            'ends_at'           => now()->addHours(5),
            'status'            => 'live',
        ]);
        AuctionBid::create([
            'auction_id' => $leadingAuction->id,
            'user_id'    => $this->user->id,
            'amount'     => 2000.00,
        ]);

        $response = $this->actingAs($this->user)->get(route('auctions.index', ['filter' => 'outbid_alerts']));

        $response->assertOk();
        $response->assertViewHas('auctions', function ($auctions) use ($outbidAuction, $leadingAuction) {
            return $auctions->contains('id', $outbidAuction->id)
                && !$auctions->contains('id', $leadingAuction->id);
        });
    }

    public function test_customer_auction_panel_filters_won_lots_correctly(): void
    {
        $wonAuction = Auction::create([
            'product_id'        => $this->product->id,
            'seller_id'         => $this->sellerProfile->id,
            'starting_price'    => 1000.00,
            'reserve_price'     => 1200.00,
            'current_price'     => 1800.00,
            'minimum_increment' => 100.00,
            'starts_at'         => now()->subDays(2),
            'ends_at'           => now()->subDay(),
            'status'            => 'ended',
            'winner_id'         => $this->user->id,
        ]);

        $lostAuction = Auction::create([
            'product_id'        => $this->product->id,
            'seller_id'         => $this->sellerProfile->id,
            'starting_price'    => 1000.00,
            'reserve_price'     => 1200.00,
            'current_price'     => 2500.00,
            'minimum_increment' => 100.00,
            'starts_at'         => now()->subDays(2),
            'ends_at'           => now()->subDay(),
            'status'            => 'ended',
            'winner_id'         => $this->otherUser->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('auctions.index', ['filter' => 'won']));

        $response->assertOk();
        $response->assertViewHas('auctions', function ($auctions) use ($wonAuction, $lostAuction) {
            return $auctions->contains('id', $wonAuction->id)
                && !$auctions->contains('id', $lostAuction->id);
        });
        $response->assertSee('YOU WON LOT');
    }

    public function test_guest_is_redirected_to_login_on_protected_filters(): void
    {
        $response = $this->get(route('auctions.index', ['filter' => 'active_bids']));
        $response->assertRedirect(route('login'));

        $response2 = $this->get(route('auctions.index', ['filter' => 'outbid_alerts']));
        $response2->assertRedirect(route('login'));

        $response3 = $this->get(route('auctions.index', ['filter' => 'won']));
        $response3->assertRedirect(route('login'));
    }
}
