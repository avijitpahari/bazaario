<?php

namespace Tests\Feature\Seller;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class Milestone5AuctionEngineChallengeTest
 *
 * Empirical Adversarial Challenge Suite for Milestone 5:
 * Features 38-43 covering Wholesale Auction Engine, Live Terminal, Reserve Met Indicator,
 * Cancellation Policy, Anonymized Stream, and Master Registry Table.
 *
 *  - Category 1: Wholesale Auction Creation & Validation Gate (Feature 40)
 *  - Category 2: Dynamic Reserve Met Engine & Precision Math (Feature 39)
 *  - Category 3: Anonymized Live Bid Activity Stream & Privacy Protection (Feature 41)
 *  - Category 4: Strict Auction Cancellation Policy & Guardrails (Feature 42)
 *  - Category 5: Live Terminal & Master Registry Resilience & Tenancy (Features 38, 43)
 */
class Milestone5AuctionEngineChallengeTest extends TestCase
{
    use RefreshDatabase, SellerTestHelperTrait;

    // =========================================================================
    // CATEGORY 1: WHOLESALE AUCTION CREATION & VALIDATION GATE (Feature 40)
    // =========================================================================

    /**
     * Challenge 1.1: Auction creation rejects starting price equal to zero or negative.
     */
    public function test_challenge_auction_creation_rejects_zero_or_negative_starting_price(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Test Crate', 1000.00);

        // Test 0
        $responseZero = $this->actingAs($seller, 'seller')->post(route('seller.auctions.store'), [
            'product_id'        => $product->id,
            'starting_price'    => 0,
            'minimum_increment' => 50.00,
            'starts_at'         => now()->addHour()->toDateTimeString(),
            'ends_at'           => now()->addHours(3)->toDateTimeString(),
        ]);
        $responseZero->assertSessionHasErrors('starting_price');

        // Test negative
        $responseNeg = $this->actingAs($seller, 'seller')->post(route('seller.auctions.store'), [
            'product_id'        => $product->id,
            'starting_price'    => -150.00,
            'minimum_increment' => 50.00,
            'starts_at'         => now()->addHour()->toDateTimeString(),
            'ends_at'           => now()->addHours(3)->toDateTimeString(),
        ]);
        $responseNeg->assertSessionHasErrors('starting_price');

        $this->assertDatabaseCount('auctions', 0);
    }

    /**
     * Challenge 1.2: Auction creation rejects reserve price less than starting price by even 1 paisa.
     */
    public function test_challenge_auction_creation_rejects_reserve_price_strictly_below_starting_price(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Precision Crate', 1000.00);

        $response = $this->actingAs($seller, 'seller')->post(route('seller.auctions.store'), [
            'product_id'        => $product->id,
            'starting_price'    => 1000.00,
            'reserve_price'     => 999.99, // 1 cent/paisa below starting price
            'minimum_increment' => 50.00,
            'starts_at'         => now()->addHour()->toDateTimeString(),
            'ends_at'           => now()->addHours(3)->toDateTimeString(),
        ]);

        $response->assertSessionHasErrors('reserve_price');
        $this->assertDatabaseCount('auctions', 0);
    }

    /**
     * Challenge 1.3: Auction creation accepts reserve price equal to starting price.
     */
    public function test_challenge_auction_creation_accepts_reserve_price_equal_to_starting_price(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Equal Floor Crate', 1000.00);

        $response = $this->actingAs($seller, 'seller')->post(route('seller.auctions.store'), [
            'product_id'        => $product->id,
            'starting_price'    => 1000.00,
            'reserve_price'     => 1000.00,
            'minimum_increment' => 50.00,
            'starts_at'         => now()->addHour()->toDateTimeString(),
            'ends_at'           => now()->addHours(3)->toDateTimeString(),
        ]);

        $response->assertRedirect(route('seller.auctions.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('auctions', [
            'product_id'     => $product->id,
            'starting_price' => 1000.00,
            'reserve_price'  => 1000.00,
        ]);
    }

    /**
     * Challenge 1.4: Auction creation accepts nullable/empty reserve price.
     */
    public function test_challenge_auction_creation_accepts_null_reserve_price(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'No Reserve Crate', 1200.00);

        $response = $this->actingAs($seller, 'seller')->post(route('seller.auctions.store'), [
            'product_id'        => $product->id,
            'starting_price'    => 1200.00,
            'reserve_price'     => '',
            'minimum_increment' => 50.00,
            'starts_at'         => now()->addHour()->toDateTimeString(),
            'ends_at'           => now()->addHours(3)->toDateTimeString(),
        ]);

        $response->assertRedirect(route('seller.auctions.index'));
        $response->assertSessionHas('success');

        $auction = Auction::where('product_id', $product->id)->first();
        $this->assertNotNull($auction);
        $this->assertNull($auction->reserve_price);
    }

    /**
     * Challenge 1.5: Auction creation rejects zero or negative minimum increment.
     */
    public function test_challenge_auction_creation_rejects_zero_or_negative_increment(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Increment Test', 500.00);

        // Zero
        $resZero = $this->actingAs($seller, 'seller')->post(route('seller.auctions.store'), [
            'product_id'        => $product->id,
            'starting_price'    => 500.00,
            'minimum_increment' => 0,
            'starts_at'         => now()->addHour()->toDateTimeString(),
            'ends_at'           => now()->addHours(3)->toDateTimeString(),
        ]);
        $resZero->assertSessionHasErrors('minimum_increment');

        // Negative
        $resNeg = $this->actingAs($seller, 'seller')->post(route('seller.auctions.store'), [
            'product_id'        => $product->id,
            'starting_price'    => 500.00,
            'minimum_increment' => -10,
            'starts_at'         => now()->addHour()->toDateTimeString(),
            'ends_at'           => now()->addHours(3)->toDateTimeString(),
        ]);
        $resNeg->assertSessionHasErrors('minimum_increment');
    }

    /**
     * Challenge 1.6: Auction creation rejects identical start and end timestamps.
     */
    public function test_challenge_auction_creation_rejects_identical_start_and_end_time(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Timing Test', 500.00);
        $timeStr = now()->addHours(2)->toDateTimeString();

        $response = $this->actingAs($seller, 'seller')->post(route('seller.auctions.store'), [
            'product_id'        => $product->id,
            'starting_price'    => 500.00,
            'minimum_increment' => 50.00,
            'starts_at'         => $timeStr,
            'ends_at'           => $timeStr,
        ]);

        $response->assertSessionHasErrors('ends_at');
    }

    /**
     * Challenge 1.7: Cross-tenant product isolation: Seller A cannot auction Seller B's product.
     */
    public function test_challenge_cross_tenant_product_isolation_prevented(): void
    {
        $sellerA = $this->createApprovedSeller();
        $sellerB = $this->createApprovedSeller();

        $productB = $this->createProductRecord($sellerB, 'Seller B High Value Crop', 5000.00);

        $response = $this->actingAs($sellerA, 'seller')->post(route('seller.auctions.store'), [
            'product_id'        => $productB->id,
            'starting_price'    => 1000.00,
            'minimum_increment' => 100.00,
            'starts_at'         => now()->addHour()->toDateTimeString(),
            'ends_at'           => now()->addHours(3)->toDateTimeString(),
        ]);

        $response->assertSessionHasErrors('product_id');
        $this->assertDatabaseMissing('auctions', [
            'product_id' => $productB->id,
        ]);
    }

    /**
     * Challenge 1.8: Auto-status resolution assigns 'live' if starts_at is in past and ends_at is in future.
     */
    public function test_challenge_auction_status_resolution_live_vs_scheduled(): void
    {
        $seller = $this->createApprovedSeller();
        $product1 = $this->createProductRecord($seller, 'Immediate Produce', 800.00);
        $product2 = $this->createProductRecord($seller, 'Future Produce', 900.00);

        // Immediate / Live window
        $this->actingAs($seller, 'seller')->post(route('seller.auctions.store'), [
            'product_id'        => $product1->id,
            'starting_price'    => 800.00,
            'minimum_increment' => 50.00,
            'starts_at'         => now()->subMinute()->toDateTimeString(),
            'ends_at'           => now()->addHours(2)->toDateTimeString(),
        ]);

        // Future / Scheduled window
        $this->actingAs($seller, 'seller')->post(route('seller.auctions.store'), [
            'product_id'        => $product2->id,
            'starting_price'    => 900.00,
            'minimum_increment' => 50.00,
            'starts_at'         => now()->addHours(2)->toDateTimeString(),
            'ends_at'           => now()->addHours(6)->toDateTimeString(),
        ]);

        $auc1 = Auction::where('product_id', $product1->id)->first();
        $auc2 = Auction::where('product_id', $product2->id)->first();

        $this->assertEquals('live', $auc1->status);
        $this->assertEquals('scheduled', $auc2->status);
    }

    // =========================================================================
    // CATEGORY 2: DYNAMIC RESERVE MET ENGINE & PRECISION MATH (Feature 39)
    // =========================================================================

    /**
     * Challenge 2.1: Precision decimal math: Reserve met evaluations when bids match reserve down to decimals.
     */
    public function test_challenge_reserve_met_precision_decimal_evaluations(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Decimal Grain Lot', 1234.50);

        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 1234.50,
            'reserve_price'  => 1999.75,
            'current_price'  => 1234.50,
            'status'         => 'live',
        ]);

        // 0 bids -> not met
        $this->assertFalse($auction->isReserveMet());

        // Bid 1: 1 paisa below reserve (1999.74) -> not met
        $this->createAuctionBid($auction, $buyer, 1999.74);
        $this->assertFalse($auction->fresh()->isReserveMet());

        // Bid 2: Exact reserve match (1999.75) -> MET
        $this->createAuctionBid($auction, $buyer, 1999.75);
        $this->assertTrue($auction->fresh()->isReserveMet());

        // Bid 3: Above reserve (2000.00) -> MET
        $this->createAuctionBid($auction, $buyer, 2000.00);
        $this->assertTrue($auction->fresh()->isReserveMet());
    }

    /**
     * Challenge 2.2: Consistent evaluation between eager-loaded relation and direct SQL count/max queries.
     */
    public function test_challenge_reserve_met_eager_loaded_vs_lazy_loaded_consistency(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Eager Load Test', 1000.00);

        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 1000.00,
            'reserve_price'  => 1500.00,
            'status'         => 'live',
        ]);

        // Unloaded
        $unloaded = Auction::find($auction->id);
        $this->assertFalse($unloaded->relationLoaded('bids'));
        $this->assertFalse($unloaded->isReserveMet());
        $this->assertTrue($unloaded->canBeCancelled());

        // Loaded
        $loaded = Auction::with('bids')->find($auction->id);
        $this->assertTrue($loaded->relationLoaded('bids'));
        $this->assertFalse($loaded->isReserveMet());
        $this->assertTrue($loaded->canBeCancelled());

        // Add qualifying bid
        $this->createAuctionBid($auction, $buyer, 1600.00);

        $unloadedAfter = Auction::find($auction->id);
        $this->assertFalse($unloadedAfter->relationLoaded('bids'));
        $this->assertTrue($unloadedAfter->isReserveMet());
        $this->assertFalse($unloadedAfter->canBeCancelled());

        $loadedAfter = Auction::with('bids')->find($auction->id);
        $this->assertTrue($loadedAfter->relationLoaded('bids'));
        $this->assertTrue($loadedAfter->isReserveMet());
        $this->assertFalse($loadedAfter->canBeCancelled());
    }

    /**
     * Challenge 2.3: Reserve Met badge UI representation across live, show, and index views.
     */
    public function test_challenge_reserve_met_ui_badges_in_all_views(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'UI Badge Produce', 1000.00);

        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 1000.00,
            'reserve_price'  => 1500.00,
            'status'         => 'live',
            'starts_at'      => now()->subMinute(),
            'ends_at'        => now()->addHour(),
        ]);

        // Prior to meeting reserve:
        // Live view should show RESERVE NOT MET
        $resLive1 = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));
        $resLive1->assertStatus(200);
        $resLive1->assertSee('RESERVE NOT MET');

        // Show view should show Reserve Not Met
        $resShow1 = $this->actingAs($seller, 'seller')->get(route('seller.auctions.show', $auction));
        $resShow1->assertStatus(200);
        $resShow1->assertSee('Reserve Not Met');

        // Index view should show Target
        $resIndex1 = $this->actingAs($seller, 'seller')->get(route('seller.auctions.index'));
        $resIndex1->assertStatus(200);
        $resIndex1->assertSee('Target: ₹1,500');

        // Now place qualifying bid
        $this->createAuctionBid($auction, $buyer, 1750.00);

        // Live view should show RESERVE MET
        $resLive2 = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));
        $resLive2->assertStatus(200);
        $resLive2->assertSee('RESERVE MET');

        // Show view should show Reserve Met
        $resShow2 = $this->actingAs($seller, 'seller')->get(route('seller.auctions.show', $auction));
        $resShow2->assertStatus(200);
        $resShow2->assertSee('Reserve Met');
        $resShow2->assertDontSee('Reserve Not Met');

        // Index view should show Reserve Met
        $resIndex2 = $this->actingAs($seller, 'seller')->get(route('seller.auctions.index'));
        $resIndex2->assertStatus(200);
        $resIndex2->assertSee('Reserve Met');
    }

    // =========================================================================
    // CATEGORY 3: ANONYMIZED LIVE BID STREAM & PRIVACY PROTECTION (Feature 41)
    // =========================================================================

    /**
     * Challenge 3.1: Bidder PII is completely shielded and masked via MD5 hash prefix.
     */
    public function test_challenge_bidder_pii_strictly_masked_in_all_views(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer([
            'name'  => 'Prashant Agrawal',
            'email' => 'prashant.agrawal@agriwholesalers.in',
            'phone' => '9820012345',
        ]);
        $product = $this->createProductRecord($seller, 'Privacy Shield Crop', 2000.00);

        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 2000.00,
            'status'         => 'live',
            'starts_at'      => now()->subMinute(),
            'ends_at'        => now()->addHour(),
        ]);

        $this->createAuctionBid($auction, $buyer, 2500.00);

        $expectedHash = substr(md5($buyer->id), 0, 4);

        // Check Live Terminal View
        $liveResponse = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));
        $liveResponse->assertStatus(200);
        $liveResponse->assertSee("Bidder #***{$expectedHash}");
        $liveResponse->assertDontSee('Prashant Agrawal');
        $liveResponse->assertDontSee('prashant.agrawal@agriwholesalers.in');
        $liveResponse->assertDontSee('9820012345');

        // Check Show Inspector View
        $showResponse = $this->actingAs($seller, 'seller')->get(route('seller.auctions.show', $auction));
        $showResponse->assertStatus(200);
        $showResponse->assertSee("Bidder #***{$expectedHash}");
        $showResponse->assertDontSee('Prashant Agrawal');
        $showResponse->assertDontSee('prashant.agrawal@agriwholesalers.in');
        $showResponse->assertDontSee('9820012345');
    }

    /**
     * Challenge 3.2: Bid ladder hierarchy highlights leading bid and tags previous as OUTBID.
     */
    public function test_challenge_bid_stream_leading_vs_outbid_hierarchy(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer1 = $this->createBuyer();
        $buyer2 = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Ladder Grain', 1000.00);

        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 1000.00,
            'status'         => 'live',
            'starts_at'      => now()->subMinute(),
            'ends_at'        => now()->addHour(),
        ]);

        // Bid 1 by Buyer 1
        $this->createAuctionBid($auction, $buyer1, 1200.00);
        // Bid 2 by Buyer 2 (Higher)
        $this->createAuctionBid($auction, $buyer2, 1400.00);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));
        $response->assertStatus(200);

        // Should see both Leading Bid and OUTBID tags
        $response->assertSee('Leading Bid');
        $response->assertSee('OUTBID');
        $response->assertSee('1,400.00');
        $response->assertSee('1,200.00');
    }

    // =========================================================================
    // CATEGORY 4: STRICT AUCTION CANCELLATION POLICY & GUARDRAILS (Feature 42)
    // =========================================================================

    /**
     * Challenge 4.1: Cancellation is permitted with zero bids on scheduled lots.
     */
    public function test_challenge_cancellation_permitted_with_zero_bids_scheduled(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Cancel Zero Scheduled', 1000.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'status' => 'scheduled',
        ]);

        $this->assertTrue($auction->canBeCancelled());

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));

        $response->assertRedirect(route('seller.auctions.index'));
        $response->assertSessionHas('success');
        $this->assertEquals('cancelled', $auction->fresh()->status);
    }

    /**
     * Challenge 4.2: Cancellation is permitted with zero bids on live lots.
     */
    public function test_challenge_cancellation_permitted_with_zero_bids_live(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Cancel Zero Live', 1000.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'status' => 'live',
        ]);

        $this->assertTrue($auction->canBeCancelled());

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));

        $response->assertRedirect(route('seller.auctions.index'));
        $response->assertSessionHas('success');
        $this->assertEquals('cancelled', $auction->fresh()->status);
    }

    /**
     * Challenge 4.3: Cancellation strictly blocked once 1 bid exists (even below reserve).
     */
    public function test_challenge_cancellation_blocked_with_single_bid_below_reserve(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Single Bid Lot', 1000.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 1000.00,
            'reserve_price'  => 2000.00,
            'status'         => 'live',
        ]);

        $this->createAuctionBid($auction, $buyer, 1100.00);

        $this->assertFalse($auction->fresh()->canBeCancelled());

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));

        // Must fail with error and keep status as live
        $this->assertTrue($response->isRedirect() || $response->status() === 403);
        $this->assertEquals('live', $auction->fresh()->status);
    }

    /**
     * Challenge 4.4: Cancellation strictly blocked via JSON request returning HTTP 403.
     */
    public function test_challenge_cancellation_blocked_via_json_request(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'JSON Guarded Lot', 1000.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 1000.00,
            'status'         => 'live',
        ]);

        $this->createAuctionBid($auction, $buyer, 1200.00);

        $response = $this->actingAs($seller, 'seller')
            ->postJson(route('seller.auctions.cancel', $auction));

        $response->assertStatus(403);
        $response->assertJsonStructure(['error']);
        $this->assertStringContainsString('Policy Guardrail', $response->json('error'));
        $this->assertEquals('live', $auction->fresh()->status);
    }

    /**
     * Challenge 4.5: Terminal auction status (cancelled or ended) cannot be re-cancelled.
     */
    public function test_challenge_terminal_status_cannot_be_re_cancelled_json_and_web(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Terminal Lot', 1000.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'status' => 'cancelled',
        ]);

        // Web attempt
        $webRes = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));
        $webRes->assertSessionHas('error');

        // JSON attempt
        $jsonRes = $this->actingAs($seller, 'seller')
            ->postJson(route('seller.auctions.cancel', $auction));
        $jsonRes->assertStatus(422);
        $jsonRes->assertJsonStructure(['error']);

        $this->assertEquals('cancelled', $auction->fresh()->status);
    }

    /**
     * Challenge 4.6: Strict tenant isolation on cancellation endpoint (Seller A cannot cancel Seller B lot).
     */
    public function test_challenge_cross_tenant_cancellation_strictly_rejected(): void
    {
        $sellerA = $this->createApprovedSeller();
        $sellerB = $this->createApprovedSeller();

        $productB = $this->createProductRecord($sellerB, 'Seller B Protected Lot', 1000.00);
        $auctionB = $this->createAuctionRecord($sellerB, $productB, [
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($sellerA, 'seller')
            ->post(route('seller.auctions.cancel', $auctionB));

        $response->assertStatus(403);
        $this->assertEquals('scheduled', $auctionB->fresh()->status);
    }

    // =========================================================================
    // CATEGORY 5: LIVE TERMINAL & MASTER REGISTRY WORKSPACE RESILIENCE (Features 38, 43)
    // =========================================================================

    /**
     * Challenge 5.1: Live terminal zero state renders clean marketing empty-state card.
     */
    public function test_challenge_live_terminal_zero_state_resilience(): void
    {
        $seller = $this->createApprovedSeller();

        $response = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));

        $response->assertStatus(200);
        $response->assertSee('No Active Live Auctions Currently');
        $response->assertSee('Schedule a Lot Now');
        $response->assertDontSee('LOT #AUC-');
    }

    /**
     * Challenge 5.2: Live terminal with scheduled fallback renders auction info cleanly.
     */
    public function test_challenge_live_terminal_fallback_to_recent_scheduled_lot(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Upcoming Harvest Lot', 1500.00);
        $auction = $this->createAuctionRecord($seller, $product, [
            'status'    => 'scheduled',
            'starts_at' => now()->addHours(2),
            'ends_at'   => now()->addHours(6),
        ]);

        $response = $this->actingAs($seller, 'seller')->get(route('seller.auctions.live'));

        $response->assertStatus(200);
        $response->assertSee('Upcoming Harvest Lot');
        $response->assertSee('LOT #AUC-');
    }

    /**
     * Challenge 5.3: Master registry status filters correctly isolate subsets and maintain tenant scoping.
     */
    public function test_challenge_master_registry_status_filters_and_tenant_scoping(): void
    {
        $sellerA = $this->createApprovedSeller();
        $sellerB = $this->createApprovedSeller();

        $pA1 = $this->createProductRecord($sellerA, 'Seller A Scheduled', 1000.00);
        $pA2 = $this->createProductRecord($sellerA, 'Seller A Live', 2000.00);
        $pA3 = $this->createProductRecord($sellerA, 'Seller A Ended', 3000.00);
        $pA4 = $this->createProductRecord($sellerA, 'Seller A Cancelled', 4000.00);

        $pB1 = $this->createProductRecord($sellerB, 'Seller B Secret Scheduled', 1000.00);
        $pB2 = $this->createProductRecord($sellerB, 'Seller B Secret Live', 2000.00);

        $this->createAuctionRecord($sellerA, $pA1, ['status' => 'scheduled']);
        $this->createAuctionRecord($sellerA, $pA2, ['status' => 'live']);
        $this->createAuctionRecord($sellerA, $pA3, ['status' => 'ended']);
        $this->createAuctionRecord($sellerA, $pA4, ['status' => 'cancelled']);

        $this->createAuctionRecord($sellerB, $pB1, ['status' => 'scheduled']);
        $this->createAuctionRecord($sellerB, $pB2, ['status' => 'live']);

        // 1. All tab for Seller A
        $resAll = $this->actingAs($sellerA, 'seller')->get(route('seller.auctions.index', ['status' => 'all']));
        $resAll->assertStatus(200);
        $resAll->assertSee('Seller A Scheduled');
        $resAll->assertSee('Seller A Live');
        $resAll->assertSee('Seller A Ended');
        $resAll->assertSee('Seller A Cancelled');
        $resAll->assertDontSee('Seller B Secret Scheduled');
        $resAll->assertDontSee('Seller B Secret Live');

        // 2. Ended tab
        $resEnded = $this->actingAs($sellerA, 'seller')->get(route('seller.auctions.index', ['status' => 'ended']));
        $resEnded->assertStatus(200);
        $resEnded->assertSee('Seller A Ended');
        $resEnded->assertDontSee('Seller A Live');
        $resEnded->assertDontSee('Seller A Scheduled');
        $resEnded->assertDontSee('Seller A Cancelled');

        // 3. Cancelled tab
        $resCanc = $this->actingAs($sellerA, 'seller')->get(route('seller.auctions.index', ['status' => 'cancelled']));
        $resCanc->assertStatus(200);
        $resCanc->assertSee('Seller A Cancelled');
        $resCanc->assertDontSee('Seller A Live');
    }

    /**
     * Challenge 5.4: Master registry handles SQL injection injection payloads in status query parameter gracefully.
     */
    public function test_challenge_master_registry_handles_sqli_payloads_in_filter(): void
    {
        $seller = $this->createApprovedSeller();
        $p = $this->createProductRecord($seller, 'Legit Auction', 1000.00);
        $this->createAuctionRecord($seller, $p, ['status' => 'live']);

        $sqliPayload = "' OR 1=1 --";
        $response = $this->actingAs($seller, 'seller')->get(route('seller.auctions.index', ['status' => $sqliPayload]));

        $response->assertStatus(200);
        $response->assertSee('Legit Auction');
    }

    /**
     * Challenge 5.5: Show inspector verifies strict tenant isolation.
     */
    public function test_challenge_show_inspector_strictly_denies_cross_tenant_access(): void
    {
        $sellerA = $this->createApprovedSeller();
        $sellerB = $this->createApprovedSeller();

        $productB = $this->createProductRecord($sellerB, 'Seller B Show Produce', 2000.00);
        $auctionB = $this->createAuctionRecord($sellerB, $productB);

        $response = $this->actingAs($sellerA, 'seller')->get(route('seller.auctions.show', $auctionB));

        $response->assertStatus(403);
    }
}
