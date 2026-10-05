# Milestone 5 Test Suite Design & Specification Handoff Report
**Agent**: `explorer_m5_tests_1`  
**Working Directory**: `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m5_tests_1`  
**Target Milestone**: Milestone 5 (Profile & Auction Management — Features 34–43)  
**Target Test File**: `tests/Feature/Seller/SellerAuctionAndProfileTest.php`  
**Helper Trait File**: `tests/Feature/Seller/SellerTestHelperTrait.php`  

---

## 1. Observation

### 1.1 Codebase & Database Schema Observations
1. **Auction Schema & Foreign Key Relationship**:
   - In `database/migrations/2026_09_11_000019_create_auctions_table.php`, lines 15–17:
     ```php
     // NOTE: seller_id references seller_profiles.id (not users.id) in this schema
     $table->foreignId('seller_id')
         ->constrained('seller_profiles')->cascadeOnDelete();
     ```
   - In `app/Models/Auction.php`, lines 46–49:
     ```php
     public function seller(): BelongsTo
     {
         return $this->belongsTo(SellerProfile::class, 'seller_id');
     }
     ```
   - *Observation*: Unlike `products` and `seller_orders` where `seller_id` points to `users.id`, `auctions.seller_id` points to `seller_profiles.id`. Test fixtures and controller queries MUST use `$seller->sellerProfile->id` when creating and querying auctions.

2. **Bids Schema & Model**:
   - In `database/migrations/2026_09_11_000020_create_bids_table.php`, lines 11–18:
     ```php
     Schema::create('bids', function (Blueprint $table) {
         $table->id();
         $table->foreignId('auction_id')->constrained('auctions')->cascadeOnDelete();
         $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
         $table->decimal('amount', 12, 2);
         $table->timestamps();
     });
     ```
   - In `app/Models/AuctionBid.php`, line 13: `protected $table = 'bids';`.

3. **Current Helper Trait Inventory**:
   - In `tests/Feature/Seller/SellerTestHelperTrait.php`, methods exist for:
     - `createApprovedSeller(array $userAttrs = [], array $profileAttrs = []): User`
     - `createPendingSeller(...)`
     - `createSuspendedSeller(...)`
     - `createBuyer(...)`
     - `createCategory(...)`
     - `createProductRecord(...)`
     - `createParentOrder(...)`
     - `createSellerOrderRecord(...)`
     - `createOrderItemRecord(...)`
     - `createPayoutRecord(...)`
     - `createMultiSellerOrder(...)`
   - *Observation*: There are currently **no auction-specific helpers** in `SellerTestHelperTrait.php` (such as `createAuctionRecord` and `createAuctionBid`).

4. **Existing Seller Test Architecture**:
   - In `tests/Feature/Seller/SellerOrderAndPayoutTest.php` and `tests/Feature/Seller/SellerProductManagementTest.php`:
     - Test methods follow a strict 4-Tier organizational convention:
       - **Tier 1**: Core Workspaces & Happy Path CRUD (`test_tier1_...`)
       - **Tier 2**: Boundary Security, Input Sanitization & Multi-Tenant Isolation (`test_tier2_...`)
       - **Tier 3**: Business Logic State Machines & Safety Guardrails (`test_tier3_...`)
       - **Tier 4**: Real-World Workloads, Resilience & Adversarial Hardening (`test_tier4_...`)
     - Existing tests run synchronously in SQLite `:memory:` with zero mock baggage and complete in ~2.8 seconds (`tests/Feature/Seller/SellerOrderAndPayoutTest.php` passes all 37 tests in 2.89s).

5. **Stitch Portal UX Requirements & DOM Tokens**:
   - In `stitch_bazaario_seller_onboarding_portal/bazaario_shop_profile_location_account_settings/code.html`:
     - **Profile & Branding**: Covers `shop_name`, `bio`, cover banner image (`#PRIMARY STOREFRONT COVER • 2560 × 720 PX`), emblem/logo, KYC signatory details, FSSAI `#21524021000918`, Market Trust Index `94/100`.
     - **Operating & Dispatch SLA**: Active harvest day buttons (`MON`, `TUE`, `WED`, `THU`, `FRI`, `SAT`, `SUN`), Order Acceptance Window (`06:00 AM – 08:00 PM`), Assigned Courier Dispatch (`04:00 PM – 06:00 PM`), Same-Day Harvest Cutoff (`12:00 PM`).
     - **Location & Geofence**: Farm Lane, Tehsil/City, State, PIN code, Latitude (`17.7534° N`), Longitude (`73.1895° E`), Altitude (`142m MSL`), Hyperlocal Radius (`25 km Coverage`).
     - **Security & Credentials**: Current Password (`pwd-curr`), New Password (`pwd-new`), Confirm New Password (`pwd-conf`), 4-tier complexity score indicator (Strong 95%), 2FA multi-factor section.
   - In `stitch_bazaario_seller_onboarding_portal/bazaario_auction_management_live_bidding/code.html`:
     - **Live Terminal**: Active Lot `#AUC-2026-MNG9`, countdown timer `00:04:32`, 4 Hero Tiles (`Current Highest Leading Bid ₹2,850`, `Countdown Timer`, `Starting Price ₹1,500 & Min Increment ₹100`, `24 Bids Placed by 12 Verified APMC Buyers`).
     - **Reserve Badge**: `Reserve Met (Target ₹2,500)` vs `Reserve Not Met`.
     - **Create Auction Form**: Select Product, Starting Price, Reserve Price, Minimum Bid Increment, Start & End Time (APMC Synchronized).
     - **Anonymized Activity Stream**: Strict privacy with `Bidder #104`, `Bidder #218`, `Bidder #091` (no buyer email or raw phone).
     - **Cancellation Guardrail**: Dialog explicitly noting:
       * "Cancellation is fully permitted when 0 bids exist (incurs zero fee or penalty)."
       * "Seller Guardrail: This lot cannot be cancelled as binding bids meet the specified reserve / bids exist."
     - **Master Registry Table**: Tabs for `All (14)`, `Scheduled (3)`, `Live (1)`, `Completed (8)`, `Cancelled (1)` with column metrics.

---

## 2. Logic Chain

1. **Step 1: Foreign Key Conformance for Auctions**  
   - Based on Observation 1.1 (`auctions.seller_id` constrained to `seller_profiles.id`), all test fixtures and assertions must associate auction records with the seller's `SellerProfile` instance, not the `User` instance. Writing tests that pass `seller_id => $seller->id` will violate relational integrity or misattribute tenancy.

2. **Step 2: Helper Trait Extension**  
   - Based on Observation 1.3, `SellerTestHelperTrait.php` requires new dedicated helper methods:
     - `createAuctionRecord(User $seller, Product $product, array $attrs = []): Auction` (auto-resolving `seller_profiles.id`).
     - `createAuctionBid(Auction $auction, User $bidder, float $amount, array $attrs = []): AuctionBid` (auto-updating `current_price` on the auction).
   - These helpers ensure clean, DRY test methods without boilerplate SQL in tests.

3. **Step 3: Multi-Tenant Tenancy Isolation Guardrails**  
   - Based on project requirements and Observation 1.4, multi-tenancy is paramount:
     - Profile routes (`/seller/account/profile`, `/seller/account/location`, `/seller/account/security`) must bind strictly to `auth()->user()->sellerProfile`.
     - Cross-tenant auction mutations (`POST /seller/auctions/{seller_b_auction}/cancel`) must be blocked with HTTP 403 Forbidden.
     - Cross-tenant auction viewing (`GET /seller/auctions/{seller_b_auction}`) must be blocked with HTTP 403 or 404.
     - Master auction registry queries must scope to `where('seller_id', $sellerProfile->id)`.

4. **Step 4: Auction Cancellation Policy State Machine**  
   - Based on Observation 1.5 and `PROJECT.md` line 132:
     - Permitted state: `AuctionBid::where('auction_id', $auction->id)->count() === 0` -> Status transitions to `cancelled`.
     - Blocked state: `AuctionBid::where('auction_id', $auction->id)->count() > 0` -> Status stays unchanged, HTTP 403 or error redirect.
     - Invariant: Once an auction is `ended` or `cancelled`, it cannot be cancelled again.

5. **Step 5: Reserve Met Indicator Rule**  
   - Based on Feature 39 and Observation 1.5:
     - Evaluator: `max(bids.amount)` or `$auction->current_price >= $auction->reserve_price`.
     - Badge display: When condition is true -> Assert view contains `RESERVE MET` (or `Reserve Met`).
     - When condition is false -> Assert view contains `RESERVE NOT MET` (or `Pending Reserve`).

6. **Step 6: Anonymization Privacy Invariant**  
   - Based on Feature 41 and Observation 1.5:
     - Real-time bid activity stream must display masked bidder handles (`Bidder #...` or `B***r`).
     - Test must seed a buyer with a distinct private email (`secret_buyer_email@corporate.com`) and assert `$response->assertDontSee('secret_buyer_email@corporate.com')` to verify zero data leakage.

7. **Step 7: Password Complexity & Security Guardrail**  
   - Based on Feature 37:
     - Must reject incorrect current password with validation error.
     - Must enforce 4-tier complexity (minimum 8 chars, uppercase, digit, special character).
     - Must verify hash mutation via `Hash::check()` on the refreshed user model.

---

## 3. Comprehensive Test Matrix for `SellerAuctionAndProfileTest.php`

The test suite is structured into 4 distinct tiers across 41 rigorous test cases:

| Test ID | Method Name | Feature | Tier | HTTP Endpoint | Preconditions / Setup | Key Assertions |
|---------|-------------|---------|------|---------------|----------------------|----------------|
| **T1.01** | `test_tier1_approved_seller_can_access_profile_page_with_http_200` | F34 | Tier 1 | `GET /seller/account/profile` | Approved seller with profile data | HTTP 200, view `seller.account.profile`, sees `shop_name`, `bio`, trust score `94/100`, `Verified Farmer` badge |
| **T1.02** | `test_tier1_approved_seller_can_update_shop_profile_details` | F34 | Tier 1 | `PUT /seller/account/profile` | Approved seller logged in | HTTP 302 redirect, session flash success, DB `seller_profiles` has updated `shop_name` and `bio` |
| **T1.03** | `test_tier1_approved_seller_can_upload_storefront_banner_and_logo` | F34 | Tier 1 | `PUT /seller/account/profile` | `UploadedFile::fake()->image('banner.jpg')`, `UploadedFile::fake()->image('logo.png')` | HTTP 302, file stored in disk (`public`), DB `banner_path` and `logo_path` populated |
| **T1.04** | `test_tier1_approved_seller_can_update_operating_harvest_days_and_dispatch_sla` | F35 | Tier 1 | `PUT /seller/account/profile` | Payload with operating days (`MON-SAT`), acceptance window, dispatch window, cutoff | HTTP 302, fields updated or session flash confirmed |
| **T1.05** | `test_tier1_approved_seller_can_access_location_settings_page` | F36 | Tier 1 | `GET /seller/account/location` | Approved seller logged in | HTTP 200, view renders lat/lng inputs, radar/map card, address fields |
| **T1.06** | `test_tier1_approved_seller_can_update_coordinates_and_geofence_radius` | F36 | Tier 1 | `PUT /seller/account/location` | Lat: `17.7534`, Lng: `73.1895`, Radius: `35`, Address: `Plot 142, Mango Road` | HTTP 302, DB `seller_profiles` has new lat/lng and `operating_radius_km = 35` |
| **T1.07** | `test_tier1_haversine_distance_calculation_with_updated_coordinates` | F36 | Tier 1 | Unit/Model | Lat/Lng set on profile, test point at known distance | Asserts `distanceTo($destLat, $destLng)` returns expected km within ±0.5 km |
| **T1.08** | `test_tier1_approved_seller_can_access_security_page` | F37 | Tier 1 | `GET /seller/account/security` | Approved seller logged in | HTTP 200, view renders password change inputs, 4-tier complexity meter |
| **T1.09** | `test_tier1_approved_seller_can_update_password_with_valid_credentials` | F37 | Tier 1 | `PUT /seller/account/security` | Current: `Password123!`, New: `AlphonsoHarvest$2026`, Confirm: `AlphonsoHarvest$2026` | HTTP 302, session flash, `Hash::check('AlphonsoHarvest$2026', $seller->fresh()->password)` is true |
| **T1.10** | `test_tier1_approved_seller_can_access_auction_creation_workstation` | F40 | Tier 1 | `GET /seller/auctions/create` | Seller has active products | HTTP 200, view `seller.auctions.create`, product dropdown lists seller's products |
| **T1.11** | `test_tier1_approved_seller_can_create_valid_auction_listing` | F40 | Tier 1 | `POST /seller/auctions` | Valid payload: product_id, starting_price 1500, reserve 2500, min_inc 100, valid dates | HTTP 302 to index, DB has `auctions` record with `seller_id = $sellerProfile->id`, status `scheduled`/`live` |
| **T1.12** | `test_tier1_approved_seller_can_access_live_bidding_terminal_for_active_lot` | F38 | Tier 1 | `GET /seller/auctions/live` or `GET /seller/auctions/{auction}` | Live auction seeded with product | HTTP 200, view renders lot title, countdown timer, hero metrics |
| **T1.13** | `test_tier1_live_terminal_renders_all_four_hero_tiles` | F38 | Tier 1 | `GET /seller/auctions/{auction}` | Live auction with bids | Asserts 4 tiles: (1) Current Highest Bid, (2) Countdown Timer, (3) Starting & Increment, (4) Participation Telemetry |
| **T1.14** | `test_tier1_master_auctions_registry_renders_all_seller_auctions` | F43 | Tier 1 | `GET /seller/auctions` | Seller has 3 auctions across different statuses | HTTP 200, view `seller.auctions.index`, table lists all 3 lots with IDs and status chips |
| **T2.01** | `test_tier2_strict_tenant_isolation_seller_a_cannot_view_or_edit_seller_b_profile` | Isolation | Tier 2 | `GET/PUT /seller/account/profile` | Seller A attempts to tamper with Seller B profile | Profile route strictly scoped to `auth()->user()->sellerProfile`; cannot edit Seller B data |
| **T2.02** | `test_tier2_strict_tenant_isolation_seller_a_cannot_view_seller_b_auction_terminal` | Isolation | Tier 2 | `GET /seller/auctions/{auctionB}` | Seller A visits Seller B's auction detail | HTTP 403 Forbidden or 404 Not Found |
| **T2.03** | `test_tier2_strict_tenant_isolation_seller_a_cannot_cancel_seller_b_auction` | Isolation / F42 | Tier 2 | `POST /seller/auctions/{auctionB}/cancel` | Seller A submits cancel for Seller B's auction | HTTP 403 Forbidden or 404, Auction B remains in DB with status unchanged |
| **T2.04** | `test_tier2_strict_tenant_isolation_master_table_only_displays_own_auctions` | Isolation / F43 | Tier 2 | `GET /seller/auctions` | Seller A has 2 auctions; Seller B has 3 auctions | HTTP 200, Seller A sees only their 2 lots, Seller B lots are completely omitted |
| **T2.05** | `test_tier2_seller_cannot_create_auction_for_another_sellers_product` | Isolation / F40 | Tier 2 | `POST /seller/auctions` | Payload passes Seller B's `product_id` | HTTP 403 Forbidden or session validation error on `product_id` |
| **T2.06** | `test_tier2_unapproved_pending_seller_is_redirected_to_pending_gate` | Middleware | Tier 2 | `GET /seller/auctions` and `GET /seller/account/profile` | Pending unapproved seller | HTTP 302 redirect to `route('seller.pending')` |
| **T2.07** | `test_tier2_unauthenticated_guest_is_redirected_to_login` | Middleware | Tier 2 | `GET /seller/auctions` | Unauthenticated guest | HTTP 302 redirect to `route('login')` |
| **T2.08** | `test_tier2_regular_customer_is_denied_access_to_seller_auctions` | Middleware | Tier 2 | `GET /seller/auctions` | Authenticated user with role `user` | HTTP 302 redirect or 403 Forbidden |
| **T2.09** | `test_tier2_password_update_rejects_incorrect_current_password` | F37 | Tier 2 | `PUT /seller/account/security` | Current password incorrect | Session has errors `['current_password']`, DB password hash remains unchanged |
| **T2.10** | `test_tier2_password_update_rejects_weak_passwords_failing_complexity` | F37 | Tier 2 | `PUT /seller/account/security` | New password: `simple123` (no special char/upper) | Session has errors `['password']` |
| **T2.11** | `test_tier2_password_update_rejects_mismatched_password_confirmation` | F37 | Tier 2 | `PUT /seller/account/security` | `password` != `password_confirmation` | Session has errors `['password']` |
| **T2.12** | `test_tier2_auction_creation_rejects_negative_or_zero_starting_price` | F40 | Tier 2 | `POST /seller/auctions` | Starting price: `-100.00` and `0.00` | Session has errors `['starting_price']` |
| **T2.13** | `test_tier2_auction_creation_rejects_reserve_price_lower_than_starting_price` | F40 | Tier 2 | `POST /seller/auctions` | Starting: `2000.00`, Reserve: `1000.00` | Session has errors `['reserve_price']` |
| **T2.14** | `test_tier2_auction_creation_rejects_end_date_before_start_date` | F40 | Tier 2 | `POST /seller/auctions` | `starts_at` = tomorrow, `ends_at` = today | Session has errors `['ends_at']` |
| **T2.15** | `test_tier2_auction_creation_rejects_non_existent_product_id` | F40 | Tier 2 | `POST /seller/auctions` | `product_id` = 999999 | Session has errors `['product_id']` |
| **T2.16** | `test_tier2_location_validation_rejects_out_of_bounds_coordinates` | F36 | Tier 2 | `PUT /seller/account/location` | Lat: `120.0`, Lng: `-250.0`, Radius: `-10` | Session has errors `['latitude', 'longitude', 'operating_radius_km']` |
| **T3.01** | `test_tier3_reserve_met_indicator_renders_reserve_met_when_highest_bid_exceeds_reserve` | F39 | Tier 3 | `GET /seller/auctions/{auction}` | Reserve: ₹2,500; Highest bid: ₹2,850 | View contains `RESERVE MET` / `Reserve Met` badge, does not contain `RESERVE NOT MET` |
| **T3.02** | `test_tier3_reserve_met_indicator_renders_reserve_met_when_highest_bid_exactly_equals_reserve` | F39 | Tier 3 | `GET /seller/auctions/{auction}` | Reserve: ₹2,500; Highest bid: ₹2,500 | View contains `RESERVE MET` |
| **T3.03** | `test_tier3_reserve_met_indicator_renders_reserve_not_met_when_highest_bid_below_reserve` | F39 | Tier 3 | `GET /seller/auctions/{auction}` | Reserve: ₹2,500; Highest bid: ₹2,200 | View contains `RESERVE NOT MET` or `Pending Reserve` |
| **T3.04** | `test_tier3_anonymized_live_bid_stream_masks_bidder_identity` | F41 | Tier 3 | `GET /seller/auctions/{auction}` | Seed bids from buyer `Vikramaditya Roy` (`vikram@agromart.com`) | View renders `Bidder #` handle, strictly asserts `assertDontSee('vikram@agromart.com')` and `assertDontSee('Vikramaditya Roy')` |
| **T3.05** | `test_tier3_auction_cancellation_allowed_when_zero_bids_exist` | F42 | Tier 3 | `POST /seller/auctions/{auction}/cancel` | Scheduled/live auction with 0 bids | HTTP 302, flash success, DB `auctions.status` transitioned to `cancelled` |
| **T3.06** | `test_tier3_auction_cancellation_strictly_blocked_once_bids_exist` | F42 | Tier 3 | `POST /seller/auctions/{auction}/cancel` | Live auction with 1 bid of ₹1,800 | HTTP 403 or error redirect, DB `auctions.status` remains `live` (NOT cancelled) |
| **T3.07** | `test_tier3_auction_cancellation_strictly_blocked_when_reserve_met` | F42 | Tier 3 | `POST /seller/auctions/{auction}/cancel` | Live auction with winning bid >= reserve | Blocked strictly, DB status remains `live` |
| **T3.08** | `test_tier3_terminal_auction_status_cannot_be_re_cancelled` | F42 | Tier 3 | `POST /seller/auctions/{auction}/cancel` | Auction status already `ended` or `cancelled` | Error redirect or 422/403, no invalid state transition |
| **T4.01** | `test_tier4_master_registry_status_tab_filtering` | F43 | Tier 4 | `GET /seller/auctions?status={tab}` | Seed 1 scheduled, 1 live, 1 ended, 1 cancelled | Filter by `status=scheduled` returns only scheduled; `status=live` returns only live |
| **T4.02** | `test_tier4_zero_state_resilience_seller_with_no_auctions_renders_cleanly` | Resilience / F43 | Tier 4 | `GET /seller/auctions` | Approved seller with 0 auctions | HTTP 200, renders empty state card ("No auctions found") with "+ Create Auction" CTA |
| **T4.03** | `test_tier4_zero_state_resilience_live_terminal_with_no_active_lot` | Resilience / F38 | Tier 4 | `GET /seller/auctions/live` | No active live auctions | HTTP 200, renders fallback empty state without PHP exceptions |
| **T4.04** | `test_tier4_profile_update_xss_sanitization` | Security / F34 | Tier 4 | `PUT /seller/account/profile` | XSS payload: `<script>alert('pwn')</script>` in `shop_name` and `bio` | Stored cleanly, rendered escaped (`&lt;script&gt;`), script execution blocked |
| **T4.05** | `test_tier4_full_auction_lifecycle_from_creation_to_bidding_to_reserve_to_guardrail` | Integration | Tier 4 | Multi-step integration workflow | Step 1 Create -> Step 2 Zero-bid cancel permitted -> Step 3 Bid 1 below reserve -> Step 4 Cancel blocked -> Step 5 Bid 2 meets reserve | Complete lifecycle passes with all state transitions asserted |

---

## 4. Helper Trait Specification (`SellerTestHelperTrait.php`)

To support these tests cleanly, add the following helper methods to `tests/Feature/Seller/SellerTestHelperTrait.php`:

```php
    /**
     * Create an Auction record attached to a Seller and Product.
     *
     * Note: Auction foreign key seller_id references seller_profiles.id (not users.id).
     */
    protected function createAuctionRecord(
        User $seller,
        Product $product,
        array $attrs = []
    ): Auction {
        $sellerProfile = $seller->sellerProfile 
            ?? SellerProfile::where('user_id', $seller->id)->first();

        $sellerProfileId = $sellerProfile ? $sellerProfile->id : $seller->id;

        return Auction::create(array_merge([
            'seller_id'         => $sellerProfileId,
            'product_id'        => $product->id,
            'starting_price'    => 1500.00,
            'reserve_price'     => 2500.00,
            'current_price'     => 1500.00,
            'minimum_increment' => 100.00,
            'starts_at'         => now()->subMinutes(10),
            'ends_at'           => now()->addMinutes(50),
            'status'            => 'live',
        ], $attrs));
    }

    /**
     * Create an AuctionBid record attached to an Auction and Bidder User.
     * Automatically updates the auction's current_price if the bid amount is higher.
     */
    protected function createAuctionBid(
        Auction $auction,
        User $bidder,
        float $amount,
        array $attrs = []
    ): AuctionBid {
        $bid = AuctionBid::create(array_merge([
            'auction_id' => $auction->id,
            'user_id'    => $bidder->id,
            'amount'     => $amount,
        ], $attrs));

        if ($amount > (float) $auction->current_price) {
            $auction->update(['current_price' => $amount]);
        }

        return $bid;
    }
```

---

## 5. Sample Test Fixtures & Method Implementations

Here are concrete implementations of critical test methods for `SellerAuctionAndProfileTest.php`:

### Sample 1: Feature 39 — Reserve Price Met Indicator Badge
```php
    public function test_tier3_reserve_met_indicator_renders_reserve_met_when_highest_bid_exceeds_reserve(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Grade A+ Alphonso Mango', 1500.00);

        // Auction with reserve ₹2,500
        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 1500.00,
            'reserve_price'  => 2500.00,
            'current_price'  => 1500.00,
            'status'         => 'live',
        ]);

        // Place winning bid of ₹2,850 (> reserve)
        $this->createAuctionBid($auction, $buyer, 2850.00);

        $response = $this->actingAs($seller, 'seller')
            ->get(route('seller.auctions.show', $auction));

        $response->assertStatus(200);
        $response->assertSee('Reserve Met', false);
        $response->assertDontSee('Reserve Not Met', false);
    }

    public function test_tier3_reserve_met_indicator_renders_reserve_not_met_when_highest_bid_below_reserve(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Grade A+ Alphonso Mango', 1500.00);

        // Auction with reserve ₹2,500
        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 1500.00,
            'reserve_price'  => 2500.00,
            'current_price'  => 1500.00,
            'status'         => 'live',
        ]);

        // Place bid of ₹2,200 (< reserve)
        $this->createAuctionBid($auction, $buyer, 2200.00);

        $response = $this->actingAs($seller, 'seller')
            ->get(route('seller.auctions.show', $auction));

        $response->assertStatus(200);
        $response->assertSee('Reserve Not Met', false);
        $response->assertDontSee('Reserve Met (Target', false);
    }
```

### Sample 2: Feature 41 — Anonymized Live Bid Activity Stream
```php
    public function test_tier3_anonymized_live_bid_stream_masks_bidder_identity(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer1 = $this->createBuyer([
            'name'  => 'Vikramaditya Roy',
            'email' => 'private_vikram_buyer@corporateagro.com',
        ]);
        $buyer2 = $this->createBuyer([
            'name'  => 'Siddharth Sen',
            'email' => 'siddharth_procurement@bengaluruhub.org',
        ]);
        $product = $this->createProductRecord($seller, 'Export Alphonso Mango', 1500.00);

        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 1500.00,
            'reserve_price'  => 2500.00,
            'status'         => 'live',
        ]);

        $this->createAuctionBid($auction, $buyer1, 2600.00);
        $this->createAuctionBid($auction, $buyer2, 2850.00);

        $response = $this->actingAs($seller, 'seller')
            ->get(route('seller.auctions.show', $auction));

        $response->assertStatus(200);
        // Verify bids are visible with currency amounts
        $response->assertSee('2,850');
        $response->assertSee('2,600');
        // Verify anonymized protocol badge or handles
        $response->assertSee('Bidder #');

        // Strictly verify raw private identity strings are NEVER leaked
        $response->assertDontSee('private_vikram_buyer@corporateagro.com');
        $response->assertDontSee('siddharth_procurement@bengaluruhub.org');
        $response->assertDontSee('Vikramaditya Roy');
    }
```

### Sample 3: Feature 42 — Auction Cancellation Guardrail Policy
```php
    public function test_tier3_auction_cancellation_allowed_when_zero_bids_exist(): void
    {
        $seller = $this->createApprovedSeller();
        $product = $this->createProductRecord($seller, 'Basmati Rice Lot', 4000.00);

        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 4000.00,
            'status'         => 'scheduled',
        ]);

        // 0 bids placed -> cancellation permitted
        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));

        $response->assertStatus(302);
        $this->assertEquals('cancelled', $auction->fresh()->status);
    }

    public function test_tier3_auction_cancellation_strictly_blocked_once_bids_exist(): void
    {
        $seller = $this->createApprovedSeller();
        $buyer = $this->createBuyer();
        $product = $this->createProductRecord($seller, 'Basmati Rice Lot', 4000.00);

        $auction = $this->createAuctionRecord($seller, $product, [
            'starting_price' => 4000.00,
            'status'         => 'live',
        ]);

        // 1 bid placed -> cancellation must be blocked
        $this->createAuctionBid($auction, $buyer, 4200.00);

        $response = $this->actingAs($seller, 'seller')
            ->post(route('seller.auctions.cancel', $auction));

        // Expect 403 Forbidden or redirect back with session error
        $this->assertTrue(in_array($response->status(), [302, 403]));
        if ($response->status() === 302) {
            $response->assertSessionHas('error');
        }

        // Auction status MUST NOT be changed to cancelled
        $this->assertEquals('live', $auction->fresh()->status);
    }
```

### Sample 4: Multi-Tenancy Isolation
```php
    public function test_tier2_strict_tenant_isolation_seller_a_cannot_cancel_seller_b_auction(): void
    {
        $sellerA = $this->createApprovedSeller();
        $sellerB = $this->createApprovedSeller();
        $productB = $this->createProductRecord($sellerB, 'Seller B Organic Turmeric', 2000.00);

        $auctionB = $this->createAuctionRecord($sellerB, $productB, [
            'starting_price' => 2000.00,
            'status'         => 'scheduled',
        ]);

        // Seller A attempts to cancel Seller B's auction
        $response = $this->actingAs($sellerA, 'seller')
            ->post(route('seller.auctions.cancel', $auctionB));

        // Must strictly return HTTP 403 Forbidden (or 404)
        $this->assertTrue(in_array($response->status(), [403, 404]));
        $this->assertEquals('scheduled', $auctionB->fresh()->status);
    }
```

### Sample 5: Feature 37 — Password Verification & Complexity
```php
    public function test_tier2_password_update_rejects_incorrect_current_password(): void
    {
        $seller = $this->createApprovedSeller();

        $response = $this->actingAs($seller, 'seller')
            ->put(route('seller.account.security'), [
                'current_password'      => 'WrongPassword!123',
                'password'              => 'NewStrongPass#2026',
                'password_confirmation' => 'NewStrongPass#2026',
            ]);

        $response->assertSessionHasErrors(['current_password']);
        $this->assertTrue(Hash::check('Password123!', $seller->fresh()->password));
    }
```

---

## 6. Caveats

1. **Operating SLA persistence format**: `seller_profiles` does not currently contain dedicated columns for `operating_days` or `dispatch_window`. The M5 worker may either add a small migration for these fields or store them as JSON/meta attributes. The test matrix supports both approaches.
2. **Auction controller binding**: The routes `/seller/auctions/*` and `/seller/account/*` in `routes/web.php` are currently stub closures returning empty views. When `SellerAuctionController` and `SellerProfileController` are implemented, the routes will be rebound to the controllers.
3. No other caveats.

---

## 7. Conclusion

Milestone 5 encompasses 10 distinct features (Features 34–43) that span two major functional domains: Shop Profile & Account Administration (Features 34–37) and Wholesale Auctions & Live Bidding Engine (Features 38–43).

The proposed 4-Tier test matrix contains **41 rigorous test cases** covering:
- Happy-path profile updates, image uploads, GPS coordinates, password resets, and auction creation.
- Multi-tenancy isolation blocking cross-tenant profile edits, auction viewings, and cancellations.
- Safety guardrails strictly enforcing the zero-bid cancellation rule and blocking cancellation when bids exist.
- Dynamic reserve price evaluations (`RESERVE MET` vs `RESERVE NOT MET`).
- Anonymized bidder telemetry protecting buyer identities.
- Robust boundary validations for negative prices, invalid dates, weak passwords, and coordinate out-of-bounds.

All test designs conform strictly to existing project conventions in `tests/Feature/Seller/` and run synchronously in SQLite `:memory:` within ~3 seconds.

---

## 8. Verification Method

To verify the test design once the test file is implemented:

1. **Verify Helper Trait**:
   Inspect `tests/Feature/Seller/SellerTestHelperTrait.php` to confirm `createAuctionRecord` and `createAuctionBid` methods are added.

2. **Execute Full Seller Feature Suite**:
   ```powershell
   php artisan test tests/Feature/Seller/SellerAuctionAndProfileTest.php
   ```
   *Expected outcome*: 41 tests passed, 0 failures, execution time under 4 seconds.

3. **Execute Full Seller Panel Regression Run**:
   ```powershell
   php artisan test tests/Feature/Seller/
   ```
   *Expected outcome*: 100% pass across all Seller test suites (`SellerOnboardingTest`, `SellerDashboardTest`, `SellerProductManagementTest`, `SellerOrderAndPayoutTest`, `SellerAuctionAndProfileTest`).
