<?php

namespace Tests\Feature\Admin;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\AuctionDispute;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuctionDisputeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected User $runnerUpCustomer;
    protected User $sellerUser;
    protected SellerProfile $sellerProfile;
    protected Category $category;
    protected Product $product;
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
            'name'     => 'Winning Bidder',
            'email'    => 'winner@bazaario.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
            'status'   => 'active',
        ]);

        $this->runnerUpCustomer = User::create([
            'name'     => 'Runner Up Bidder',
            'email'    => 'runnerup@bazaario.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
            'status'   => 'active',
        ]);

        $this->sellerUser = User::create([
            'name'     => 'Consignment Seller',
            'email'    => 'consignor@bazaario.com',
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

        $this->category = Category::create([
            'name' => 'Wholesale Ceramics',
            'slug' => 'wholesale-ceramics',
        ]);

        $this->product = Product::create([
            'seller_id'   => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name'        => 'Handmade Terracotta Lot 50kg',
            'slug'        => 'handmade-terracotta-lot-50kg',
            'price'       => 10000,
            'stock'       => 10,
            'status'      => 'active',
        ]);

        $this->auction = Auction::create([
            'seller_id'         => $this->sellerProfile->id,
            'product_id'        => $this->product->id,
            'title'             => 'Handmade Terracotta Lot 50kg Auction',
            'starting_price'    => 5000,
            'reserve_price'     => 8000,
            'current_price'     => 12000,
            'minimum_increment' => 500,
            'winner_id'         => $this->customer->id,
            'status'            => 'ended',
            'starts_at'         => now()->subDays(2),
            'ends_at'           => now()->subHours(2),
        ]);

        // Place runner-up bid
        AuctionBid::create([
            'auction_id' => $this->auction->id,
            'user_id'    => $this->runnerUpCustomer->id,
            'amount'     => 10500,
            'created_at' => now()->subHours(3),
        ]);

        // Place winning bid
        AuctionBid::create([
            'auction_id' => $this->auction->id,
            'user_id'    => $this->customer->id,
            'amount'     => 12000,
            'created_at' => now()->subHours(2),
        ]);
    }

    public function test_admin_can_view_auction_disputes_index(): void
    {
        $dispute = AuctionDispute::create([
            'auction_id'        => $this->auction->id,
            'user_id'           => $this->customer->id,
            'seller_id'         => $this->sellerProfile->id,
            'winner_id'         => $this->customer->id,
            'dispute_type'      => 'quality_mismatch',
            'priority'          => 'high',
            'status'            => 'open',
            'subject'           => 'Items delivered broken and mismatched grade',
            'claim_description' => 'The consignment arrived in cracked state and grade B instead of grade A.',
            'contested_amount'  => 12000,
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.auctions.disputes.index'));

        $response->assertOk();
        $response->assertSee('Wholesale Auction Disputes');
        $response->assertSee($dispute->dispute_number);
        $response->assertSee('Catalog / Grade Mismatch');
        $response->assertSee('₹12,000.00');
    }

    public function test_admin_can_view_auction_dispute_dossier(): void
    {
        $dispute = AuctionDispute::create([
            'auction_id'        => $this->auction->id,
            'user_id'           => $this->customer->id,
            'seller_id'         => $this->sellerProfile->id,
            'winner_id'         => $this->customer->id,
            'dispute_type'      => 'damaged_transit',
            'priority'          => 'urgent',
            'status'            => 'open',
            'subject'           => 'Severe transit damage to pottery crates',
            'claim_description' => 'Four out of five crates were smashed in transit.',
            'contested_amount'  => 12000,
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.auctions.disputes.show', $dispute->id));

        $response->assertOk();
        $response->assertSee($dispute->dispute_number);
        $response->assertSee('Severe transit damage to pottery crates');
        $response->assertSee('Executive Arbitration Panel');
        $response->assertSee('Full Escrow Refund to Buyer');
        $response->assertSee('Runner Up Bidder');
    }

    public function test_admin_can_lodge_auction_dispute_from_lot_control_center(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.auctions.disputes.create', $this->auction->id), [
            'user_id'           => $this->customer->id,
            'dispute_type'      => 'non_payment',
            'priority'          => 'high',
            'subject'           => 'Winner unresponsive for wire transfer',
            'claim_description' => 'Customer failed to wire payment within 48 hours of lot hammer fall.',
            'contested_amount'  => 12000,
        ]);

        $dispute = AuctionDispute::where('auction_id', $this->auction->id)->first();
        $this->assertNotNull($dispute);
        $this->assertEquals('non_payment', $dispute->dispute_type);
        $this->assertEquals('open', $dispute->status);
        $this->assertStringStartsWith('AUC-DSP-', $dispute->dispute_number);

        $response->assertRedirect(route('admin.auctions.disputes.show', $dispute->id));
    }

    public function test_admin_can_arbitrate_with_refund_buyer(): void
    {
        $dispute = AuctionDispute::create([
            'auction_id'        => $this->auction->id,
            'user_id'           => $this->customer->id,
            'seller_id'         => $this->sellerProfile->id,
            'winner_id'         => $this->customer->id,
            'dispute_type'      => 'quality_mismatch',
            'priority'          => 'high',
            'status'            => 'open',
            'subject'           => 'Substandard material claim',
            'claim_description' => 'Claim description notes.',
            'contested_amount'  => 12000,
        ]);

        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.auctions.disputes.arbitrate', $dispute->id), [
            'ruling'        => 'refund_buyer',
            'ruling_notes'  => 'Buyer evidence confirmed product degradation. Full refund authorized.',
            'refund_amount' => 12000,
        ]);

        $response->assertRedirect(route('admin.auctions.disputes.show', $dispute->id));

        $dispute->refresh();
        $this->assertEquals('resolved', $dispute->status);
        $this->assertEquals('refund_buyer', $dispute->ruling);
        $this->assertEquals(12000, (float)$dispute->refund_amount);
        $this->assertEquals($this->admin->id, $dispute->arbitrated_by);

        $this->auction->refresh();
        $this->assertEquals('cancelled', $this->auction->status);
    }

    public function test_admin_can_arbitrate_with_reassign_winner(): void
    {
        $dispute = AuctionDispute::create([
            'auction_id'        => $this->auction->id,
            'user_id'           => $this->customer->id,
            'seller_id'         => $this->sellerProfile->id,
            'winner_id'         => $this->customer->id,
            'dispute_type'      => 'non_payment',
            'priority'          => 'urgent',
            'status'            => 'open',
            'subject'           => 'Winner default on payment',
            'claim_description' => 'Top bidder defaulted, re-award requested.',
            'contested_amount'  => 12000,
        ]);

        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.auctions.disputes.arbitrate', $dispute->id), [
            'ruling'       => 'reassign_winner',
            'ruling_notes' => 'Top bidder disqualified. Lot hammer reassigned to runner-up valid bid.',
        ]);

        $response->assertRedirect(route('admin.auctions.disputes.show', $dispute->id));

        $dispute->refresh();
        $this->assertEquals('resolved', $dispute->status);
        $this->assertEquals('reassign_winner', $dispute->ruling);

        $this->auction->refresh();
        // The winner should now be runnerUpCustomer and current price 10500
        $this->assertEquals($this->runnerUpCustomer->id, $this->auction->winner_id);
        $this->assertEquals(10500, (float)$this->auction->current_price);
        $this->assertEquals('ended', $this->auction->status);
    }

    public function test_admin_can_arbitrate_with_payout_seller_and_dismissed(): void
    {
        $dispute = AuctionDispute::create([
            'auction_id'        => $this->auction->id,
            'user_id'           => $this->customer->id,
            'seller_id'         => $this->sellerProfile->id,
            'winner_id'         => $this->customer->id,
            'dispute_type'      => 'reserve_dispute',
            'priority'          => 'low',
            'status'            => 'open',
            'subject'           => 'Frivolous reserve claim',
            'claim_description' => 'Claim lacks proof.',
            'contested_amount'  => 500,
        ]);

        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.auctions.disputes.arbitrate', $dispute->id), [
            'ruling'       => 'dismissed',
            'ruling_notes' => 'Complainant allegations unsupported by inspection evidence.',
        ]);

        $response->assertRedirect(route('admin.auctions.disputes.show', $dispute->id));

        $dispute->refresh();
        $this->assertEquals('dismissed', $dispute->status);
        $this->assertEquals('dismissed', $dispute->ruling);
    }

    public function test_unauthorized_user_cannot_arbitrate_dispute(): void
    {
        $dispute = AuctionDispute::create([
            'auction_id'        => $this->auction->id,
            'user_id'           => $this->customer->id,
            'seller_id'         => $this->sellerProfile->id,
            'winner_id'         => $this->customer->id,
            'dispute_type'      => 'shill_bidding',
            'priority'          => 'medium',
            'status'            => 'open',
            'subject'           => 'Shill allegation',
            'claim_description' => 'Unverified claim.',
            'contested_amount'  => 1000,
        ]);

        $response = $this->actingAs($this->customer, 'user')->post(route('admin.auctions.disputes.arbitrate', $dispute->id), [
            'ruling'       => 'void_lot',
            'ruling_notes' => 'Attempted breach.',
        ]);

        $response->assertRedirect();
    }
}
