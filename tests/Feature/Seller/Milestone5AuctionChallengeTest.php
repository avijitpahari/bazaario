<?php

namespace Tests\Feature\Seller;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class Milestone5AuctionChallengeTest
 *
 * EMPIRICAL ADVERSARIAL CHALLENGE SUITE FOR MILESTONE 5 (Features 38–43)
 *
 * Verifies with empirical test harnesses:
 * 1. Foreign Key Contract: auctions.seller_id strictly references seller_profiles.id (NOT users.id)
 * 2. Strict Cancellation Guardrail Policy:
 *    - 0 bids: cancels cleanly, transitions to 'cancelled'
 *    - 1+ bids: strictly blocks cancellation via HTML redirect (error flash) and JSON 403, status remains 'live'
 *    - Terminal status immutability: 'ended' and 'cancelled' auctions cannot be cancelled
 *    - Cross-tenant cancellation strictly blocked (HTTP 403)
 * 3. Reserve Met Dynamic Indicator:
 *    - Highest bid > reserve: renders RESERVE MET
 *    - Highest bid == reserve: renders RESERVE MET (exact equality boundary)
 *    - Highest bid < reserve: renders RESERVE NOT MET
 *    - 0 bids placed: renders RESERVE NOT MET even if starting_price >= reserve_price
 *    - Null reserve price: handles gracefully without 500 error
 * 4. Bidding Stream Anonymization & PII Protection:
 *    - Bidder username, email, phone never exposed in live terminal or lot inspector
 *    - Hashed identifier `Bidder #***` displayed
 *    - Leading bid vs outbid tracking
 * 5. Terminal Auction Status Transitions & Filter Tabs:
 *    - Scheduled vs Live initialization based on timing window
 *    - Status filter tabs (?status=all, scheduled, live, ended, cancelled) with precise counts
 *    - Live terminal active lot resolution and empty state resilience
 */
class Milestone5AuctionChallengeTest extends TestCase
{
    use RefreshDatabase, SellerTestHelperTrait;

    protected Category $defaultCategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->defaultCategory = $this->createCategory('Wholesale Agro Staples');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    // =========================================================================
    // SECTION 1: FOREIGN KEY CONTRACT & MULTI-TENANT IDENTITY SEPARATION
    // =========================================================================

    /**
     * Challenge 1.1: Verify auctions.seller_id maps to seller_profiles.id when User.id != SellerProfile.id.
     * This is a critical edge case: in production, user_id and seller_profile_id frequently diverge.
     */
    public function test_fk_contract_auctions_seller_id_strictly_references_seller_profiles_id(): void
    {
        // Create 10 dummy users to increment users.id to 10+
        User::factory()->count(10)->create();

        // Create seller: User ID will be 11, but SellerProfile ID will be 1
        $sellerUser = $this->createApprovedSeller();
        $this->assertNotEquals(
            $sellerUser->id,
            $sellerUser->sellerProfile->id,
            'Prerequisite check: seller User.id and SellerProfile.id must differ for this test.'
        );

        $product = $this->createProductRecord($sellerUser, 'Contai Betel Leaves Bulk Lot', 2000.00, 100, 'bundle');

        $startsAt = Carbon::now()->addHour()->toDateTimeString();
        $endsAt = Carbon::now()->addHours(6)->toDateTimeString();

        $payload = [
            'product_id'        => $product->id,
            'starting_price'    => 2000.00,
            'reserve_price'     => 3000.00,
            'minimum_increment' => 150.00,
            'starts_at'         => $startsAt,
            'ends_at'           => $endsAt,
        ];

        // Store auction via controller
        $response = $this->actingAs($sellerUser, 'seller')
            ->post(route('seller.auctions.store'), $payload);

        $response->assertRedirect(route('seller.auctions.index'));
        $response->assertSessionHas('success');

        $auction = Auction::where('product_id', $product->id)->first();
        $this->assertNotNull($auction, 'Auction was not created in database.');

        // EMPIRICAL ORACLE: seller_id MUST equal seller_profiles.id (1), NOT users.id (11)
        $this->assertEquals(
            $sellerUser->sellerProfile->id,
            $auction->seller_id,
            "Foreign key contract violation: auctions.seller_id ({$auction->seller_id}) does not match seller_profiles.id ({$sellerUser->sellerProfile->id})."
        );
        $this->assertNotEquals(
            $sellerUser->id,
            $auction->seller_id,
            "Foreign key bug: auctions.seller_id matches users.id ({$sellerUser->id}) instead of seller_profiles.id."
        );

        // Verify Eloquent relationships resolve seamlessly
        $this->assertInstanceOf(SellerProfile::class, $auction->sellerProfile);
        $this->assertEquals($sellerUser->sellerProfile->id, $auction->sellerProfile->id);
        $this->assertTrue($sellerUser->sellerProfile->auctions->contains($auction));
    }

    /**
     * Challenge 1.2: Verify database foreign key constraint rejects non-existent seller_profile_id.
     */
    public function test_fk_constraint_database_rejects_non_existent_seller_profile_id(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Organic Paddy Grain', 1800.00);

        // Turn on SQLite foreign keys if on SQLite
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        $nonExistentProfileId = 999999;

        $this->expectException(QueryException::class);

        Auction::create([
            'seller_id'         => $nonExistentProfileId,
            'product_id'        => $product->id,
            'starting_price'    => 1800.00,
            'current_price'     => 1800.00,
            'minimum_increment' => 100.00,
            'starts_at'         => now(),
            'ends_at'           => now()->addHours(2),
            'status'            => 'live',
        ]);
    }

    /**
     * Challenge 1.3: Multi-tenant isolation with divergent User/Profile IDs.
     * Seller A cannot access Seller B's auctions across index, show, live terminal, or cancel.
     */
    public function test_multi_tenant_isolation_with_divergent_user_and_profile_ids(): void
    {
        User::factory()->count(5)->create();
        $sellerA = $this->createApprovedSeller([], ['shop_name' => 'Seller Alpha Farm']);
        $sellerB = $this->createApprovedSeller([], ['shop_name' => 'Seller Beta Farm']);

        $productA = $this->createProductRecord($sellerA, 'Alpha Alphonso Lot', 1500.00);
        $productB = $this->createProductRecord($sellerB, 'Beta Cashew Lot', 2500.00);

        $auctionA = $this->createAuctionRecord($sellerA, $productA, ['status' => 'live']);
        $auctionB = $this->createAuctionRecord($sellerB, $productB, ['status' => 'live']);

        // Seller A viewing index sees only Lot A
        $responseIndex = $this->actingAs($sellerA, 'seller')->get(route('seller.auctions.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Alpha Alphonso Lot');
        $responseIndex->assertDontSee('Beta Cashew Lot');

        // Seller A viewing Seller B auction returns 403 Forbidden
        $responseShow = $this->actingAs($sellerA, 'seller')->get(route('seller.auctions.show', $auctionB));
        $responseShow->assertStatus(403);

        // Seller A attempting to cancel Seller B auction returns 403 Forbidden
        $responseCancel = $this->actingAs($sellerA, 'seller')->post(route('seller.auctions.cancel', $auctionB));
        $responseCancel->assertStatus(403);
        $this->assertEquals('live', $auctionB->fresh()->status);
    }

    // =========================================================================
    // SECTION 2: STRICT CANCELLATION GUARDRAIL POLICY
    // =========================================================================

    /**
     * Challenge 2.1: Auction with 0 bids cancels successfully in scheduled status.
     */
    public function test_cancellation_guardrail_zero_bids_scheduled_auction_cancels_successfully(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Fresh Dragonfruit Lot', 1200.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'status'    => 'scheduled',
            'starts_at' => now()->addHours(2),
            'ends_at'   => now()->addHours(8),
        ]);

        $this->assertEquals(0, $auction->bids()->count());
        $this->assertTrue($auction->canBeCancelled());

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));

        $response->assertRedirect(route('seller.auctions.index'));
        $response->assertSessionHas('success');

        $auction->refresh();
        $this->assertEquals('cancelled', $auction->status);
        $this->assertFalse($auction->canBeCancelled());
    }

    /**
     * Challenge 2.2: Auction with 0 bids cancels successfully in live status.
     */
    public function test_cancellation_guardrail_zero_bids_live_auction_cancels_successfully(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Live Organic Ginger Lot', 800.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'status'    => 'live',
            'starts_at' => now()->subHour(),
            'ends_at'   => now()->addHours(4),
        ]);

        $this->assertEquals(0, $auction->bids()->count());
        $this->assertTrue($auction->canBeCancelled());

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));

        $response->assertRedirect(route('seller.auctions.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('cancelled', $auction->fresh()->status);
    }

    /**
     * Challenge 2.3: Auction with 1+ bids strictly rejects cancellation via HTML form submit (redirect + error flash).
     */
    public function test_cancellation_guardrail_one_bid_rejects_cancellation_html_redirect(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Turmeric Root Bulk Crate', 1500.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'status'         => 'live',
            'starting_price' => 1500.00,
            'reserve_price'  => 3000.00,
            'current_price'  => 1500.00,
        ]);

        // Place 1 bid below reserve
        $this->createAuctionBid($auction, $buyer, 1600.00);

        $this->assertEquals(1, $auction->bids()->count());
        $this->assertFalse($auction->fresh()->canBeCancelled());

        // Attempt cancellation
        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Policy Guardrail', session('error'));

        // Status MUST remain live
        $this->assertEquals('live', $auction->fresh()->status);
    }

    /**
     * Challenge 2.4: Auction with 1+ bids strictly rejects cancellation via JSON request with HTTP 403.
     */
    public function test_cancellation_guardrail_one_bid_rejects_cancellation_json_403(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Alphonso Mango Crate B42', 2000.00);
        $auction = $this->createAuctionRecord($seller, $product, ['status' => 'live']);

        $this->createAuctionBid($auction, $buyer, 2200.00);

        $response = $this->actingAs($seller, 'seller')
            ->postJson(route('seller.auctions.cancel', $auction));

        $response->assertStatus(403);
        $response->assertJsonStructure(['error']);
        $this->assertStringContainsString('Policy Guardrail', $response->json('error'));

        $this->assertEquals('live', $auction->fresh()->status);
    }

    /**
     * Challenge 2.5: Multiple bids from multiple bidders strictly block cancellation.
     */
    public function test_cancellation_guardrail_multiple_bids_strictly_blocks_cancellation(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer1 = $this->createBuyer(['name' => 'Buyer Alpha']);
        $buyer2 = $this->createBuyer(['name' => 'Buyer Beta']);
        $product = $this->createProductRecord($seller, 'High Volume Grain Lot', 5000.00);
        $auction = $this->createAuctionRecord($seller, $product, ['status' => 'live', 'starting_price' => 5000.00]);

        $this->createAuctionBid($auction, $buyer1, 5200.00);
        $this->createAuctionBid($auction, $buyer2, 5500.00);
        $this->createAuctionBid($auction, $buyer1, 6000.00);

        $this->assertEquals(3, $auction->bids()->count());

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));

        $response->assertSessionHas('error');
        $this->assertEquals('live', $auction->fresh()->status);
    }

    /**
     * Challenge 2.6: Terminal status immutability — ended auctions cannot be cancelled.
     */
    public function test_cancellation_guardrail_terminal_ended_auction_cannot_be_cancelled(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Finished Harvest Lot', 1000.00);
        $auction = $this->createAuctionRecord($seller, $product, ['status' => 'ended']);

        $this->assertFalse($auction->canBeCancelled());

        // HTML form submit
        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));

        $response->assertSessionHas('error');
        $this->assertEquals('ended', $auction->fresh()->status);

        // JSON submit
        $responseJson = $this->actingAs($seller, 'seller')
            ->postJson(route('seller.auctions.cancel', $auction));

        $responseJson->assertStatus(422);
        $this->assertEquals('ended', $auction->fresh()->status);
    }

    /**
     * Challenge 2.7: Terminal status immutability — already cancelled auctions cannot be re-cancelled.
     */
    public function test_cancellation_guardrail_terminal_cancelled_auction_cannot_be_re_cancelled(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Voided Lot', 1000.00);
        $auction = $this->createAuctionRecord($seller, $product, ['status' => 'cancelled']);

        $this->assertFalse($auction->canBeCancelled());

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));

        $response->assertSessionHas('error');
        $this->assertEquals('cancelled', $auction->fresh()->status);
    }

    // =========================================================================
    // SECTION 3: RESERVE MET DYNAMIC INDICATOR
    // =========================================================================

    /**
     * Challenge 3.1: Reserve Met badge renders `RESERVE MET` when highest bid strictly exceeds reserve_price.
     */
    public function test_reserve_indicator_renders_reserve_met_when_highest_bid_exceeds_reserve(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Saffron Honey Lot', 2000.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'status'         => 'live',
            'starting_price' => 2000.00,
            'reserve_price'  => 3500.00,
            'current_price'  => 2000.00,
        ]);

        $this->createAuctionBid($auction, $buyer, 4200.00);

        $auction->refresh();
        $this->assertTrue($auction->isReserveMet());

        // Live terminal check
        $liveResponse = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));
        $liveResponse->assertStatus(200);
        $liveResponse->assertSee('RESERVE MET');
        $liveResponse->assertDontSee('RESERVE NOT MET');

        // Show inspector check
        $showResponse = $this->actingAs($seller, 'seller')->get(route('seller.auctions.show', $auction));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Reserve Met', false);
        $showResponse->assertDontSee('Reserve Not Met', false);
    }

    /**
     * Challenge 3.2: Reserve Met badge renders `RESERVE MET` at exact equality boundary (highest bid == reserve_price).
     */
    public function test_reserve_indicator_renders_reserve_met_at_exact_equality_boundary(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Darjeeling First Flush Lot', 5000.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'status'         => 'live',
            'starting_price' => 5000.00,
            'reserve_price'  => 8000.00,
            'current_price'  => 5000.00,
        ]);

        // Place bid exactly equal to reserve
        $this->createAuctionBid($auction, $buyer, 8000.00);

        $auction->refresh();
        $this->assertTrue($auction->isReserveMet(), 'Exact reserve boundary did not evaluate to true.');

        $liveResponse = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));
        $liveResponse->assertStatus(200);
        $liveResponse->assertSee('RESERVE MET');

        $showResponse = $this->actingAs($seller, 'seller')->get(route('seller.auctions.show', $auction));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Reserve Met', false);
    }

    /**
     * Challenge 3.3: Reserve Met badge renders `RESERVE NOT MET` when highest bid < reserve_price.
     */
    public function test_reserve_indicator_renders_reserve_not_met_when_highest_bid_below_reserve(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Alphonso Mango Export Grade', 3000.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'status'         => 'live',
            'starting_price' => 3000.00,
            'reserve_price'  => 5000.00,
            'current_price'  => 3000.00,
        ]);

        // Bid ₹4,999.00 (₹1.00 below reserve)
        $this->createAuctionBid($auction, $buyer, 4999.00);

        $auction->refresh();
        $this->assertFalse($auction->isReserveMet());

        $liveResponse = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));
        $liveResponse->assertStatus(200);
        $liveResponse->assertSee('RESERVE NOT MET');

        $showResponse = $this->actingAs($seller, 'seller')->get(route('seller.auctions.show', $auction));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Reserve Not Met', false);
    }

    /**
     * Challenge 3.4: Zero bids placed renders `RESERVE NOT MET` even if starting_price equals reserve_price.
     * With no binding bids placed, reserve has not been legally met.
     */
    public function test_reserve_indicator_renders_reserve_not_met_when_zero_bids_exist(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Unbidded Premium Lot', 4000.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'status'         => 'live',
            'starting_price' => 4000.00,
            'reserve_price'  => 4000.00, // starting == reserve
            'current_price'  => 4000.00,
        ]);

        $this->assertEquals(0, $auction->bids()->count());
        $this->assertFalse($auction->isReserveMet());

        $liveResponse = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));
        $liveResponse->assertStatus(200);
        $liveResponse->assertSee('RESERVE NOT MET');

        $showResponse = $this->actingAs($seller, 'seller')->get(route('seller.auctions.show', $auction));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Reserve Not Met', false);
    }

    /**
     * Challenge 3.5: Handling null or zero reserve_price gracefully without errors.
     */
    public function test_reserve_indicator_handles_null_reserve_price_gracefully(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'No Reserve Farm Lot', 1500.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'status'         => 'live',
            'starting_price' => 1500.00,
            'reserve_price'  => null,
            'current_price'  => 1500.00,
        ]);

        $this->assertTrue($auction->isReserveMet());

        $liveResponse = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));
        $liveResponse->assertStatus(200);
        $liveResponse->assertSee('RESERVE MET');

        $showResponse = $this->actingAs($seller, 'seller')->get(route('seller.auctions.show', $auction));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Reserve Met', false);
    }

    // =========================================================================
    // SECTION 4: BIDDING STREAM ANONYMIZATION & PRIVACY PRESERVATION
    // =========================================================================

    /**
     * Challenge 4.1: Live terminal anonymizes bidder username, email, and phone.
     */
    public function test_bidding_stream_anonymization_in_live_terminal(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer([
            'name'  => 'Harshavardhan Agrawal',
            'email' => 'harshavardhan.secret@mumbaiwholesale.org',
            'phone' => '9820019283',
        ]);

        $product = $this->createProductRecord($seller, 'Strict Privacy Lot 99', 2000.00);
        $auction = $this->createAuctionRecord($seller, $product, ['status' => 'live']);

        $this->createAuctionBid($auction, $buyer, 2500.00);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));

        $response->assertStatus(200);
        // Assert masked bidder hash is present
        $response->assertSee('Bidder #***');
        // Assert sensitive PII is strictly absent
        $response->assertDontSee('Harshavardhan');
        $response->assertDontSee('Agrawal');
        $response->assertDontSee('harshavardhan.secret@mumbaiwholesale.org');
        $response->assertDontSee('9820019283');
    }

    /**
     * Challenge 4.2: Show lot inspector anonymizes bidder username, email, and phone.
     */
    public function test_bidding_stream_anonymization_in_show_inspector(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer([
            'name'  => 'Subhashree Mukherjee',
            'email' => 'subhashree@bengalexports.in',
            'phone' => '9831122334',
        ]);

        $product = $this->createProductRecord($seller, 'Strict Privacy Lot 100', 3000.00);
        $auction = $this->createAuctionRecord($seller, $product, ['status' => 'live']);

        $this->createAuctionBid($auction, $buyer, 3500.00);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.auctions.show', $auction));

        $response->assertStatus(200);
        $response->assertSee('Bidder #***');
        $response->assertDontSee('Subhashree');
        $response->assertDontSee('Mukherjee');
        $response->assertDontSee('subhashree@bengalexports.in');
        $response->assertDontSee('9831122334');
    }

    /**
     * Challenge 4.3: Multiple bids stream displays leading bid status and outbid tags.
     */
    public function test_bidding_stream_leading_and_outbid_progression(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer1 = $this->createBuyer(['name' => 'Bidder One']);
        $buyer2 = $this->createBuyer(['name' => 'Bidder Two']);

        $product = $this->createProductRecord($seller, 'Multi Bid Privacy Lot', 1000.00);
        $auction = $this->createAuctionRecord($seller, $product, ['status' => 'live']);

        $this->createAuctionBid($auction, $buyer1, 1200.00);
        $this->createAuctionBid($auction, $buyer2, 1600.00);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));

        $response->assertStatus(200);
        $response->assertSee('Leading Bid');
        $response->assertSee('OUTBID');
        $response->assertSee('1,600.00');
        $response->assertSee('1,200.00');
    }

    // =========================================================================
    // SECTION 5: TERMINAL AUCTION STATUS TRANSITIONS & STATUS FILTER TABS
    // =========================================================================

    /**
     * Challenge 5.1: Store endpoint initializes status to 'scheduled' if starts_at is in future.
     */
    public function test_auction_store_sets_status_scheduled_when_starts_at_in_future(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Future Auction Lot', 1000.00);

        $payload = [
            'product_id'        => $product->id,
            'starting_price'    => 1000.00,
            'reserve_price'     => 1500.00,
            'minimum_increment' => 50.00,
            'starts_at'         => now()->addHours(2)->toDateTimeString(),
            'ends_at'           => now()->addHours(8)->toDateTimeString(),
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.auctions.store'), $payload);
        $response->assertRedirect(route('seller.auctions.index'));

        $auction = Auction::where('product_id', $product->id)->first();
        $this->assertEquals('scheduled', $auction->status);
    }

    /**
     * Challenge 5.2: Store endpoint initializes status to 'live' if starts_at is in past and ends_at in future.
     */
    public function test_auction_store_sets_status_live_when_starts_at_in_past_and_ends_at_in_future(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Instant Live Auction Lot', 1000.00);

        $payload = [
            'product_id'        => $product->id,
            'starting_price'    => 1000.00,
            'reserve_price'     => 1500.00,
            'minimum_increment' => 50.00,
            'starts_at'         => now()->subMinutes(10)->toDateTimeString(),
            'ends_at'           => now()->addHours(2)->toDateTimeString(),
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.auctions.store'), $payload);
        $response->assertRedirect(route('seller.auctions.index'));

        $auction = Auction::where('product_id', $product->id)->first();
        $this->assertEquals('live', $auction->status);
    }

    /**
     * Challenge 5.3: Status filter tabs correctly isolate scheduled, live, ended, and cancelled lots.
     */
    public function test_registry_status_filter_tabs_and_counts_integrity(): void
    {
        $seller = $this->createApprovedSeller();

        $p1 = $this->createProductRecord($seller, 'Sched Lot 1', 1000.00);
        $p2 = $this->createProductRecord($seller, 'Sched Lot 2', 1100.00);
        $p3 = $this->createProductRecord($seller, 'Live Lot 1', 1200.00);
        $p4 = $this->createProductRecord($seller, 'Ended Lot 1', 1300.00);
        $p5 = $this->createProductRecord($seller, 'Cancelled Lot 1', 1400.00);

        $this->createAuctionRecord($seller, $p1, ['status' => 'scheduled']);
        $this->createAuctionRecord($seller, $p2, ['status' => 'scheduled']);
        $this->createAuctionRecord($seller, $p3, ['status' => 'live']);
        $this->createAuctionRecord($seller, $p4, ['status' => 'ended']);
        $this->createAuctionRecord($seller, $p5, ['status' => 'cancelled']);

        // 1. All tab: shows 5 lots and count badges
        $responseAll = $this->actingAs($seller, 'seller')->get(route('seller.auctions.index'));
        $responseAll->assertStatus(200);
        $responseAll->assertSee('All (5)');
        $responseAll->assertSee('Scheduled (2)');
        $responseAll->assertSee('Live (1)');
        $responseAll->assertSee('Completed (1)');
        $responseAll->assertSee('Cancelled (1)');
        $responseAll->assertSee('Sched Lot 1');
        $responseAll->assertSee('Live Lot 1');
        $responseAll->assertSee('Ended Lot 1');
        $responseAll->assertSee('Cancelled Lot 1');

        // 2. Scheduled tab: shows only scheduled
        $responseSched = $this->actingAs($seller, 'seller')->get(route('seller.auctions.index', ['status' => 'scheduled']));
        $responseSched->assertStatus(200);
        $responseSched->assertSee('Sched Lot 1');
        $responseSched->assertSee('Sched Lot 2');
        $responseSched->assertDontSee('Live Lot 1');
        $responseSched->assertDontSee('Ended Lot 1');
        $responseSched->assertDontSee('Cancelled Lot 1');

        // 3. Live tab: shows only live
        $responseLive = $this->actingAs($seller, 'seller')->get(route('seller.auctions.index', ['status' => 'live']));
        $responseLive->assertStatus(200);
        $responseLive->assertSee('Live Lot 1');
        $responseLive->assertDontSee('Sched Lot 1');
        $responseLive->assertDontSee('Ended Lot 1');
        $responseLive->assertDontSee('Cancelled Lot 1');

        // 4. Ended tab: shows only ended
        $responseEnded = $this->actingAs($seller, 'seller')->get(route('seller.auctions.index', ['status' => 'ended']));
        $responseEnded->assertStatus(200);
        $responseEnded->assertSee('Ended Lot 1');
        $responseEnded->assertDontSee('Live Lot 1');
        $responseEnded->assertDontSee('Sched Lot 1');

        // 5. Cancelled tab: shows only cancelled
        $responseCancelled = $this->actingAs($seller, 'seller')->get(route('seller.auctions.index', ['status' => 'cancelled']));
        $responseCancelled->assertStatus(200);
        $responseCancelled->assertSee('Cancelled Lot 1');
        $responseCancelled->assertDontSee('Live Lot 1');
        $responseCancelled->assertDontSee('Ended Lot 1');
    }

    /**
     * Challenge 5.4: Live terminal resolution prioritizes active live auction.
     */
    public function test_live_terminal_priority_picks_active_live_lot(): void
    {
        $seller = $this->createApprovedSeller();
        $p1 = $this->createProductRecord($seller, 'Future Sched Lot', 1000.00);
        $p2 = $this->createProductRecord($seller, 'Active Leading Lot', 2000.00);

        $this->createAuctionRecord($seller, $p1, [
            'status'    => 'scheduled',
            'starts_at' => now()->addHours(5),
            'ends_at'   => now()->addHours(10),
        ]);
        $this->createAuctionRecord($seller, $p2, [
            'status'    => 'live',
            'starts_at' => now()->subHour(),
            'ends_at'   => now()->addHours(3),
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));
        $response->assertStatus(200);
        $response->assertSee('Active Leading Lot');
        $response->assertSee('LIVE BIDDING');
    }

    /**
     * Challenge 5.5: Live terminal empty state when seller has zero auctions.
     */
    public function test_live_terminal_empty_state_when_seller_has_no_auctions(): void
    {
        $seller = $this->createApprovedSeller();

        $response = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));

        $response->assertStatus(200);
        $response->assertSee('No Active Live Auctions Currently');
        $response->assertSee('Schedule a Lot Now');
    }
}
