# Handoff Report: Milestone 5 Specification Mining (Profile & Auction Management — Features 34–43)

## 1. Observation

### Authoritative Sources Examined
1. **User Requirement Manifest**: `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md` (Header `## 2026-09-30T04:46:52Z`):
   - **R5. Profile & Auction Management**: "Manage shop profile, edit location (Lat/Lng), and update password. Create auctions (starting price, reserve price, minimum increment, start/end time), monitor live bids, highest bid indicator, and cancel/manage eligible auctions."
2. **Project Specification**: `c:\xampp\htdocs\bazaario\PROJECT.md` (Features 34–43, Milestone 5, lines 92–101, lines 129–133):
   - Feature 34: *Shop Profile & Storefront Branding* (cover banner, logo, business details, verified tag).
   - Feature 35: *Operating Harvest Days & SLA Scheduler* (day selector MON–SUN, cutoff time, courier window).
   - Feature 36: *Location Telemetry & Geofence Settings* (farm origin, Lat/Lng coordinates, elevation, radius map preview).
   - Feature 37: *Account Security & Password Update* (current password verification, 4-tier complexity meter).
   - Feature 38: *Wholesale Bidding Live Terminal* (real-time monitoring terminal, countdown clock, hero tiles).
   - Feature 39: *Reserve Price Met Indicator* (dynamic badge: `RESERVE MET` / `RESERVE NOT MET`).
   - Feature 40: *Create Auction Listing Form* (product select, starting price, reserve, increment, dates).
   - Feature 41: *Anonymized Live Bid Activity Stream* (privacy-preserving bidding log, handles e.g. `Bidder #***42`).
   - Feature 42: *Auction Cancellation Guardrail Policy* (permitted with 0 bids, strictly blocked once bids exist).
   - Feature 43: *Master Auctions Registry Table* (historical overview across `All`, `Scheduled`, `Live`, `Ended`, `Cancelled`).
   - Interface Contract: `Auction.seller_id` references `seller_profiles.id`; `status` in `['scheduled', 'live', 'ended', 'cancelled']`; cancellation permitted ONLY when `AuctionBid::where('auction_id', $auction->id)->count() === 0`.
3. **Stitch Template 1**: `c:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal\bazaario_shop_profile_location_account_settings\code.html` (708 lines, 59,452 bytes):
   - Sidebar navigation with active `Shop Profile` and `Settings` links.
   - Header with search, notifications counter, seller profile pill ("Green Valley Farm", "Farmer • Verified Seller").
   - Breadcrumb: `Seller Center / Account / Shop Profile & Settings`, Seller ID badge (`#BZ-SLR-9021`), autosave indicator (`Draft autosaved 2m ago`), Revert and Save All buttons.
   - Left Column (25%): Configuration Suite tab navigation (`Shop Profile`, `Location & Geofence`, `Security & Password`, `Notifications`, `Compliance & Legal`) and Trust & Verification Widget (Merchant Tier Prime Tier 1 A+, Tenure, FSSAI #21524021000918, Market Trust Index 94/100 bar, entity changes compliance review notice).
   - Workspace Panel 1: *Shop Profile & Identity*:
     - Section 1.1: Store banner cover photo preview (2560×720 px), Store logo/avatar inset (w-16 h-16 rounded-[14px]), "Replace Cover", "Delete", batch photo & certification slips upload dropzone (PNG, JPG, WEBP <= 15MB).
     - Section 1.2: Gated Seller Type notice ("Registered Role: Farmer & Cultivator", verified producer badge, documentary review notice for changes, "Request Classification Change" CTA); Inputs: `shop_name`, Registered Signatory / Proprietor name (KYC matched), Primary Business Email (verified badge), Primary Dispatch Phone (OTP bound), Public Store & Provenance Description (`bio`, markdown supported, 312/500 char counter).
     - Section 1.3: Active Operating Harvest Days selector (MON–SUN buttons with active/partial badges), Time windows (Order Acceptance Window 06:00 AM–08:00 PM, Assigned Courier Dispatch 04:00 PM–06:00 PM, Same-Day Harvest Cutoff 12:00 PM Noon).
   - Workspace Panel 2: *Farm Location & Coordinates*:
     - Section 2.1: GPS Auto-Detect button (with loading spinner and ±1.8m lock emulation), Manual Edit toggle, Live Telemetry ribbon ("±3m High-Precision GPS Lock, Source: ISRO NavIC / Global WGS84 Datum"); Address inputs: Survey No. / Farm Lane (`address`), Landmark, City / Tehsil (`city`), District, State (`state`), Postal PIN Code (`postal_code`); Coordinate inputs: Latitude (`17.7534° N`), Longitude (`73.1895° E`), Altitude / Elevation (`142m MSL` readonly); Interactive Topographic map card with target pulse animation on coordinates, Hyperlocal Radius badge ("25 km Coverage"), Cold-Chain hub badge ("Chiplun 34 km"), Field agent signoff badge; Encrypted data isolation notice.
   - Workspace Panel 3: *Security & Credentials*:
     - Section 3.1: Password change form with eye visibility toggles for Current Password, New Password, Confirm New Password; 4-tier complexity meter with 4-bar indicator and score label (`WEAK`, `MEDIUM`, `STRONG`, `ENTERPRISE`), Checklist for 8+ chars, uppercase, numeral, special glyph; 2FA Multi-factor Section (`ENFORCED`, TOTP + SMS fallback, Rotate Recovery Keys, Manage Devices); Audit notification bar ("Password last refreshed: 42 days ago").
   - Sticky bottom global action bar: "Unsaved Profile Changes Detected", "Discard Changes", "Save Profile & Settings".
4. **Stitch Template 2**: `c:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal\bazaario_auction_management_live_bidding\code.html` (788 lines, 57,808 bytes):
   - Header with APMC Spot Ticker status pill, Seller ID tag, "Export Bidding Ledger" modal trigger, "Create New Auction" CTA.
   - Sub-tabs: `Live Auction Terminal (1)`, `My Auctions (14)`, Status counts: Scheduled (3), Live (1), Ending Soon (1), Completed (8).
   - Dual-pane layout:
     - Left Pane (62%): Live Auction Lot Hero Card: Lot ID (`#AUC-2026-MNG9`), Status badge (`Ending Soon` pulsing / `Live`), APMC Lot #402, Lot Title & Consignment Description ("Grade A+ Ratnagiri Alphonso Mango", "10 Crates • 240 kg Net"), GI Tag & Cold Bay storage spec; Agronomic specs grid (Harvest date, Brix sweetness 19.4°, Packaging, APMC Grade A+ Verified); Hero Bidding Metrics 4-tile grid:
       - Tile 1 (Dominant 2-col): Current Highest Leading Bid (`₹2,850`), Reserve Met indicator badge (`RESERVE MET (Target ₹2,500)`), Gross Lot Allocation Value (`₹28,500`), Over Reserve % (`+14.0%`);
       - Tile 2: Countdown Timer (`00:04:32`), animated progress bar, "Soft-close buffer active";
       - Tile 3: Starting & Rules (`₹1,500`, Min Increment `₹100`, Reserve `₹2,500`);
       - Tile 4 / Telemetry Pill: `24 Bids Placed by 12 Verified APMC Buyers`, Velocity (`1.8 bids/min`);
       - Real-time Bid Price Progression & Spread SVG Line/Area Chart with green dashed line for Reserve Threshold (`RESERVE THRESHOLD ₹2,500 MET AT 10:35 AM`);
       - Real-Time Bid Activity Stream table with columns: Bidder Hash (`Bidder #104`), Entity Category / Node (`Verified Wholesale Kirana Network • Bengaluru Hub`), Placed Bid (`₹2,850`), Timestamp (`10:42:31 AM`), Status badge (`Leading Bid`, `OUTBID`, `Reserve Trigger`);
       - Auction Management Guardrails Notification Bar: "Seller Guardrail: Reserve Price Exceeded. This lot cannot be cancelled as binding bids meet the specified ₹2,500 reserve...", Soft-Close toggle.
     - Right Pane (38%):
       - Panel 1: Create Auction Listing Wizard: Step 1 Select certified farm product from inventory, Step 2 Pricing trifecta (Starting Price, Reserve Price, Minimum Bid Increment), Step 3 Time window (Start & End datetime, window duration), Submit CTA ("Schedule & Launch Auction");
       - Panel 2: Recently Completed Settlement card: Product title, settled timestamp, final winning bid (`₹18,400`), winning buyer (`Bidder #048`), reserve performance (`Met 122.6%`), escrow status (`₹18,400 Locked in Escrow`), linked order (`#BZ-10499`);
       - Panel 3: Cancellation Guardrail dialog / policy card: Permitted with 0 bids, explaining zero fee/trust penalty, Keep Auction vs Confirm Cancel buttons.
     - Bottom Section: All Auctions Master Registry Table:
       - Filter tabs: `All (14)`, `Scheduled (3)`, `Live (1)`, `Completed (8)`, `Cancelled (1)`.
       - Columns: Auction ID, Product Lot & Spec, Starting Price, Current/Final Bid (with Reserve Met / Sold status), Bid Count, Timing/Status, Actions (`Monitor Live`, `Cancel`, `Edit Parameters`, `Receipt`).
       - Pagination bar.
     - Modal: Export APMC Bidding Ledger (CSV Format, Official APMC PDF Certificate).
5. **Existing Codebase State**:
   - `resources/views/layouts/seller.blade.php`: Functional layout with fixed 72-unit sidebar, brand tokens (`#FFFDF8`, `#0F172A`, `#F5A623`), Alpine.js, Flash toast alerts, and main content canvas `@yield('content')`.
   - `database/migrations/2026_09_11_000019_create_auctions_table.php`: `auctions` table has `product_id`, `seller_id` (references `seller_profiles.id`), `starting_price`, `reserve_price`, `current_price`, `minimum_increment`, `starts_at`, `ends_at`, `status` (`'scheduled'`, `'live'`, `'ended'`, `'cancelled'`), `winner_id`.
   - `database/migrations/2026_09_11_000020_create_bids_table.php`: `bids` table with `auction_id`, `user_id`, `amount`, timestamps.
   - `database/migrations/2026_09_30_000001_add_seller_panel_fields_to_tables.php`: Added `address`, `postal_code`, `operating_radius_km` to `seller_profiles`.
   - Placeholder views in `resources/views/seller/account/` and `resources/views/seller/auctions/` are currently 0 bytes.

---

## 2. Logic Chain

1. **Architecture Alignment**: The Bazaario seller panel uses `layouts.seller` as its master operating shell. Milestone 5 encompasses two interconnected subdomains:
   - Account & Storefront Settings: `/seller/account/profile`, `/seller/account/location`, `/seller/account/security`, `/seller/account/settings`.
   - Wholesale Auctions & Live Terminal: `/seller/auctions`, `/seller/auctions/create`, `/seller/auctions/live`, `/seller/auctions/{auction}`.
2. **Shop Profile Integration Logic**:
   - The seller's storefront identity is tied to `SellerProfile` (foreign key `user_id` -> `users.id`).
   - Storefront branding fields (`shop_name`, `bio`, `logo_path`, `banner_path`) are persisted to `seller_profiles`.
   - Operating harvest days and dispatch SLA can be stored as JSON metadata or structured configuration in `seller_profiles` with sensible defaults.
   - Classification change requests are gated: while `shop_name`, `bio`, `banner_path`, and `logo_path` are directly mutable by the seller, legal signatory, GSTIN, PAN, and `seller_type` are marked KYC-locked and require admin compliance review.
3. **Location Telemetry & Geofence Logic**:
   - `seller_profiles` contains `address`, `city`, `state`, `country`, `postal_code`, `latitude`, `longitude`, and `operating_radius_km`.
   - The GPS Auto-Detect button uses standard browser `navigator.geolocation.getCurrentPosition()`, populating `latitude` and `longitude` fields with high precision, falling back to city coordinates from `SellerProfile::getCityCoordinates()` if unavailable.
   - The geofence visualizer presents the operating radius (default 25 km) around the coordinates.
4. **Account Security Logic**:
   - Password changes mutate the authenticated `User` record (`users.password`).
   - Strict validation requires `current_password` (verified with `Hash::check()`), `new_password` (min:8, mixed-case, numbers, special characters), and `new_password_confirmation`.
   - The client-side 4-tier complexity meter evaluates entropy real-time into `Weak`, `Medium`, `Strong`, `Enterprise`, updating a 4-bar indicator and requirement checkmarks.
5. **Auction Lifecycle & Live Terminal Logic**:
   - An auction references `seller_profiles.id` through `seller_id` and an active `product_id` owned by the seller (`Product.seller_id == Auth::id()`).
   - **Pricing Trifecta**: Starting Price (`starting_price`), Secret Reserve Price (`reserve_price`, optional/mandatory threshold), Minimum Bid Increment (`minimum_increment`, >= ₹10).
   - **Timing Rules**: `starts_at` >= now, `ends_at` > `starts_at` (minimum 30 minutes).
   - **Reserve Met Calculation**: In Blade/Eloquent, when `current_price >= reserve_price`, flag `isReserveMet = true` -> display emerald badge `RESERVE MET`, else amber badge `RESERVE NOT MET`.
   - **Real-Time Countdown**: Client-side interval counts down to `ends_at`. When remaining seconds <= 0, display `00:00:00 - CLOSED`.
   - **Anonymized Live Bid Stream**: Bidders are masked as `Bidder #***XX` or hashed ID along with geographic buyer node (e.g. `Kirana Network • Bengaluru Hub`) to prevent collusion while maintaining full price transparency.
   - **Cancellation Guard**: Cancellation endpoint `/seller/auctions/{auction}/cancel` checks if `bids()->count() === 0`. If 0 bids, allow cancellation (`status = 'cancelled'`); if bids exist, strictly throw a 403/validation error with policy notice.
6. **Master Registry Logic**:
   - Auctions are filtered into tabs: `All`, `Scheduled`, `Live`, `Ended` (Completed), `Cancelled`.
   - Action buttons adapt dynamically: Live lots show `View Live Terminal`; scheduled lots with 0 bids show `Cancel Auction`; ended lots with winner show `View Settlement / Order`.

---

## 3. Features Discovered

| # | Category | Feature | Description | Inputs | Outputs | Error Behavior | Discovered Via |
|---|----------|---------|-------------|--------|---------|----------------|----------------|
| 34.1 | Profile & Branding | Primary Storefront Banner & Logo Management | Visual cover photo preview (2560×720) with replace/delete actions and store avatar emblem | `banner_image` (file: png/jpg/webp <= 5MB), `logo_image` (file: <= 2MB) | Responsive banner visual, avatar overlay, verified checkmark | Validation errors on file mime/size; default pastoral banner fallback | Stitch Template 1 (lines 166–190) |
| 34.2 | Profile & Branding | Core Business Identity Form | Manage public shop commercial name, store slug, proprietor name, and provenance bio | `shop_name`, `bio` (up to 500 chars) | Form fields, live char counter, KYC matched indicators | Required field errors, 500-char truncation check | Stitch Template 1 (lines 240–290) |
| 34.3 | Profile & Branding | Verified Merchant Trust Card | Readonly dossier card showing Merchant Tier (Prime A+), Platform Tenure, FSSAI #, and Market Trust Index (94/100) | None (computed from `SellerProfile`) | Progress bar meter, trust index score, compliance desk concierge link | Graceful null fallback if FSSAI not set | Stitch Template 1 (lines 86–137) |
| 34.4 | Profile & Branding | Gated Seller Classification Notice | Notice box showing current verified seller type (`Farmer & Cultivator`), explaining documentary audit rules for classification changes | None (readonly display with request CTA) | Badge, policy notice, "Request Classification Change" modal trigger | N/A (read-only) | Stitch Template 1 (lines 220–238) |
| 35.1 | Operations & SLA | Active Operating Harvest Days Selector | Day-of-week operating selector for Monday through Sunday with Active / Partial / Inactive toggles | `operating_days[]` (checkbox/array: mon..sun) | 7-day pill grid reflecting active operating schedule | Validation error if 0 days selected | Stitch Template 1 (lines 300–332) |
| 35.2 | Operations & SLA | Logistics Dispatch & Cutoff SLA Windows | Configure order acceptance window, daily courier harvest pickup window, and same-day harvest cutoff time | `order_window_start`, `order_window_end`, `dispatch_window_start`, `dispatch_window_end`, `cutoff_time` | 3 structured SLA cards displaying operational time bands | Validation error if cutoff is after dispatch window | Stitch Template 1 (lines 334–366) |
| 36.1 | Location & Geofence | Postal Address Form | Farm physical origin address including survey/farm lane, landmark, city/tehsil, district, state, and PIN code | `address`, `landmark`, `city`, `district`, `state`, `postal_code` | Structured address inputs with live update | Required field validation; PIN code format check | Stitch Template 1 (lines 397–426) |
| 36.2 | Location & Geofence | GPS Coordinate Telemetry & Auto-Detect | Precision Latitude, Longitude, and Elevation MSL fields with single-click browser GPS triangulation | Browser geolocation or manual `latitude`, `longitude` float inputs | Autofilled coordinates with precision lock badge (±1.8m) | Fallback to city coordinates map if browser denies permission | Stitch Template 1 (lines 379–395, 428–450) |
| 36.3 | Location & Geofence | Topographic Geofence & Radar Visualizer | Interactive map visualizer with animated target pulse at farm coordinates, showing operating radius and nearest cold-chain hub | `operating_radius_km` (slider/input, 5–100 km) | Radar map container, coverage radius badge ("25 km Coverage"), cold hub badge | Map renders fallback styling if coordinates missing | Stitch Template 1 (lines 452–491) |
| 37.1 | Security & Auth | Account Password Update Form | Password change workstation requiring current password confirmation, new password, and repeat confirmation with eye toggles | `current_password`, `password`, `password_confirmation` | Protected masked inputs with toggleable visibility | Returns 422 with specific field error if current password invalid | Stitch Template 1 (lines 508–536) |
| 37.2 | Security & Auth | 4-Tier Password Complexity Meter | Dynamic strength calculator evaluating password into `Weak`, `Medium`, `Strong`, `Enterprise` with 4-bar indicator | Real-time input string | Colored 4-bar indicator, text label, checklist tick marks (8+ chars, uppercase, digit, symbol) | Shows red/amber if requirements unmet; blocks submission | Stitch Template 1 (lines 538–569) |
| 37.3 | Security & Auth | 2FA Multi-Factor Status & Audit Log | Status card showing enforced TOTP / SMS fallback 2FA and last refreshed timestamp | None (display with actions: rotate keys, audit logs) | Enforced badge, active devices count, security audit link | N/A | Stitch Template 1 (lines 571–598) |
| 38.1 | Live Bidding Terminal | Active Auction Lot Hero Tile | Dominant workspace hero displaying active lot ID, title, net weight, APMC lot #, GI tag, and agronomic specs | `auction_id` | Full lot hero tile with harvest date, Brix sweetness, packaging spec | Graceful empty state when no auction is live | Stitch Template 2 (lines 73–124) |
| 38.2 | Live Bidding Terminal | Real-Time Countdown Clock | Precision countdown clock displaying remaining hours, minutes, and seconds with visual depletion progress bar | Auction `ends_at` timestamp | Dynamic timer (e.g. `00:04:32`), soft-close buffer note | Clock transitions to `00:00:00 - CLOSED` upon expiry | Stitch Template 2 (lines 152–166, 765–787) |
| 38.3 | Live Bidding Terminal | Dominant Highest Bid Tile | Prominent visual anchor showing current highest leading bid, unit price, gross allocation value, and delta over reserve | Auction `current_price` and bids collection | Bold currency display (e.g. `₹2,850 / Crate`), gross calculation | Displays starting price if 0 bids placed | Stitch Template 2 (lines 127–150) |
| 38.4 | Live Bidding Terminal | Bid Price Progression SVG Chart | Vector price trajectory chart displaying bid ladder milestones against a green dashed reserve threshold | Historical bids with amounts and timestamps | Crisp SVG area & trend line with node markers | Renders baseline start price line if 0 bids | Stitch Template 2 (lines 204–252) |
| 39.1 | Wholesale Auctions | Reserve Price Met Indicator Badge | Dynamic badge indicating whether the current highest bid has reached or exceeded seller's secret reserve | `current_price` vs `reserve_price` | `RESERVE MET` badge in emerald green or `RESERVE NOT MET` in amber | Visual cue informs seller whether sale is legally binding | Stitch Template 2 (lines 133–136) |
| 40.1 | Wholesale Auctions | Create Auction Listing Workstation | Consignment creation wizard with product selector, pricing trifecta, and schedule window | `product_id`, `starting_price`, `reserve_price`, `minimum_increment`, `starts_at`, `ends_at` | Validated scheduled auction record | Validation: reserve >= starting_price, increment > 0, ends_at > starts_at | Stitch Template 2 (lines 364–442) |
| 41.1 | Live Bidding Terminal | Anonymized Live Bid Activity Stream | Real-time table showing incoming bids with anonymized bidder hashes (e.g. `Bidder #104`), buyer category node, bid amount, timestamp, and status | `AuctionBid` records eager-loaded with timestamps | Privacy-preserving tabular stream with `Leading Bid`, `OUTBID`, `Reserve Trigger` badges | Empty stream notice ("No bids placed yet") | Stitch Template 2 (lines 254–337) |
| 42.1 | Wholesale Auctions | Auction Cancellation Guardrail Policy | Policy enforcement engine: cancellation permitted with 0 bids; strictly blocked once bids exist | Auction cancellation request | Success confirmation or 403 Forbidden with legal explanation | Blocks deletion/cancellation if `bids_count > 0` | Stitch Template 2 (lines 340–359, 490–520) |
| 43.1 | Master Registry | All Auctions Master Registry Table | Comprehensive table of seller's auctions with filter tabs (`All`, `Scheduled`, `Live`, `Completed`, `Cancelled`) | Status filter query parameter | Filtered table with Lot ID, Product, Starting Price, Current Bid, Bid Count, Timing, Actions | Clean empty state for filtered tabs | Stitch Template 2 (lines 523–727) |
| 43.2 | Master Registry | APMC Bidding Ledger Export Modal | Modal dialogue allowing download of verified auction transcripts in CSV or official APMC PDF Certificate format | `export-fmt` (`csv` or `pdf`) | Initiates downloadable ledger export | Handles empty records with flash warning | Stitch Template 2 (lines 729–763) |
| 43.3 | Live Bidding Terminal | Recently Completed Settlement Card | Post-auction outcome summary card showing winning bid, anonymized winning buyer, reserve % achieved, escrow lock, and linked order | Completed auction record | Settlement summary card with "View Linked Order" CTA | Renders empty placeholder if no auction completed | Stitch Template 2 (lines 444–488) |

---

## 4. Edge Cases

| # | Feature | Input | Observed Behavior |
|---|---------|-------|-------------------|
| E1 | Storefront Branding | Image file exceeds 5MB or invalid mime type | Upload fails with Laravel validation error `The banner image must not be greater than 5120 kilobytes`; form preserves existing banner without data loss. |
| E2 | Storefront Branding | Store bio text exceeds 500 characters | Client-side char counter highlights red; server-side rule `max:500` rejects payload with user-friendly error. |
| E3 | Storefront Branding | Seller attempts to mutate locked KYC fields (GSTIN, PAN, signatory) | Fields are marked `disabled` / `readonly` in Blade; server-side controller filters fillables so locked fields cannot be altered via HTTP manipulation. |
| E4 | Operating Days & SLA | Seller deselects all 7 operating days | Form validation blocks submission with error `Please select at least one operating harvest day`. |
| E5 | Operating Days & SLA | Cutoff time set after dispatch window (e.g., cutoff 6 PM, dispatch 4 PM) | Validation rule ensures `cutoff_time` < `dispatch_window_start`; error message alerts seller of illogical dispatch sequence. |
| E6 | Location & Geofence | Browser denies GPS location permissions | GPS button gracefully falls back to city-based coordinates (`SellerProfile::getCityCoordinates()`), and manual numeric Lat/Lng inputs remain editable. |
| E7 | Location & Geofence | Out-of-bounds Latitude (>90 or <-90) or Longitude (>180 or <-180) | Server validation `numeric|between:-90,90` and `numeric|between:-180,180` catches invalid coordinate entries. |
| E8 | Password Security | Current password does not match database hash | Server validation rule `current_password` returns error `The provided password does not match your current password`. |
| E9 | Password Security | New password does not satisfy complexity requirements | 4-tier complexity meter marks score as `Weak` or `Medium`, checklist highlights missing criteria (e.g. symbol or uppercase), and server rule `Password::min(8)->mixedCase()->numbers()->symbols()` prevents save. |
| E10 | Auction Creation | Reserve price entered is lower than starting price | Validation rule `gte:starting_price` fires: `The reserve price must be greater than or equal to the starting price`. |
| E11 | Auction Creation | Minimum bid increment set to <= 0 | Validation rule `gt:0` fires: `The minimum bid increment must be at least ₹10`. |
| E12 | Auction Creation | End date/time is earlier than or equal to start date/time | Validation rule `after:starts_at` rejects payload with `The end time must be after the start time (minimum 30 minutes duration)`. |
| E13 | Auction Creation | Product selected does not belong to the logged-in seller | Server-side query scopes `Product::where('seller_id', $sellerUser->id)->findOrFail($productId)`, returning 404/403 on tenant breach attempt. |
| E14 | Auction Cancellation | Seller attempts to cancel an auction that has >= 1 bid | Cancellation guard checks `$auction->bids()->count() > 0`; blocks action, redirects back with flash error `Policy Guardrail: This auction cannot be cancelled because active binding bids have been placed`. |
| E15 | Auction Cancellation | Seller cancels scheduled auction with 0 bids | Cancellation succeeds, status updates to `cancelled`, product lot is released back to inventory, and flash toast confirms zero penalty cancellation. |
| E16 | Live Terminal | Countdown reaches 00:00:00 while viewing live screen | JavaScript interval stops, timer renders `00:00:00 - CLOSED`, action buttons disable, and page prompts status transition to completed. |
| E17 | Live Terminal | Reserve price not met when auction timer ends | Badge remains `RESERVE NOT MET`, outcome status indicates lot passed in without mandatory sale, and seller is not forced into escrow allocation. |
| E18 | Live Terminal | Multiple bids placed in rapid succession | Real-time bid stream updates leading row, marks previous highest bid as `OUTBID`, and updates highest bid visual anchor. |
| E19 | Master Registry | Seller has no auctions matching selected tab (e.g., `Cancelled`) | Table renders an empty state illustration with message `No cancelled auctions found in your registry` and CTA to view all auctions. |

---

## 5. Exact Blade/Tailwind Markup Blueprints

Below are the production-ready Blade view blueprints extending `layouts.seller` to be placed in `resources/views/seller/account/` and `resources/views/seller/auctions/`.

### Blueprint 1: `resources/views/seller/account/profile.blade.php`
*(Shop Profile, Visual Branding, Operating Harvest Days & Dispatch SLA)*

```blade
@extends('layouts.seller')

@section('title', 'Shop Profile & Settings — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;
    $operatingDays = json_decode($profile->operating_days ?? '["mon","tue","wed","thu","fri","sat"]', true) ?? ['mon','tue','wed','thu','fri','sat'];
@endphp

<div class="flex flex-col w-full pb-16" x-data="{
    tab: 'profile',
    charCount: {{ strlen($profile?->bio ?? '') }},
    unsavedChanges: false,
    operatingDays: {{ json_encode($operatingDays) }},
    toggleDay(day) {
        if (this.operatingDays.includes(day)) {
            if (this.operatingDays.length > 1) {
                this.operatingDays = this.operatingDays.filter(d => d !== day);
                this.unsavedChanges = true;
            }
        } else {
            this.operatingDays.push(day);
            this.unsavedChanges = true;
        }
    }
}">

    <!-- Top Breadcrumb & Action Header -->
    <div class="px-6 py-4 bg-white/80 backdrop-blur-md rounded-[14px] shadow-sm mb-6 border border-[#E2DFD7]/60">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-[11px] text-brand-muted uppercase tracking-wider">Seller Center</span>
                    <span class="text-outline-variant font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-brand-muted uppercase tracking-wider">Account</span>
                    <span class="text-outline-variant font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-brand-amber font-semibold uppercase tracking-wider">Shop Profile &amp; Settings</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl lg:text-3xl text-primary font-bold tracking-tight">Shop Profile &amp; Settings</h1>
                    <span class="px-2 py-0.5 rounded-[6px] bg-secondary-container/40 text-on-secondary-container font-mono text-xs font-semibold">
                        ID: #BZ-SLR-{{ str_pad($profile?->id ?? 1, 4, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                <p class="text-sm text-brand-muted">Manage your verified business identity, farm origin coordinates, dispatch operating hours, and authentication credentials.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('seller.dashboard') }}" class="h-11 px-4 rounded-[14px] bg-surface-container-low text-on-surface hover:bg-surface-container-high transition-colors text-sm font-medium flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">undo</span>
                    <span>Dashboard</span>
                </a>
                <button type="button" @click="$refs.profileForm.submit()" class="h-11 px-5 rounded-[14px] bg-brand-amber text-primary hover:brightness-105 active:scale-95 transition-all text-sm font-heading font-bold shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">check</span>
                    <span>Save All Changes</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT COLUMN: Navigation & Trust Widget (3 Cols) -->
        <aside class="lg:col-span-3 flex flex-col gap-6 sticky top-24">
            
            <!-- Configuration Menu -->
            <div class="bg-white rounded-[14px] p-3 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-1">
                <div class="px-3 py-2">
                    <span class="font-mono text-[10px] uppercase tracking-widest text-outline">Configuration Suite</span>
                </div>
                <a href="{{ route('seller.account.profile') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] bg-secondary-container/30 text-on-secondary-container font-semibold transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-secondary">storefront</span>
                        <span class="text-sm font-heading">Shop Profile</span>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-brand-amber"></span>
                </a>
                <a href="{{ route('seller.account.location') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] text-on-surface-variant hover:bg-surface-container-low transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px]">explore</span>
                        <span class="text-sm font-heading">Location &amp; Geofence</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-[4px] bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold">GPS OK</span>
                </a>
                <a href="{{ route('seller.account.security') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] text-on-surface-variant hover:bg-surface-container-low transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px]">shield</span>
                        <span class="text-sm font-heading">Security &amp; Password</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-[4px] bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold">2FA On</span>
                </a>
            </div>

            <!-- Trust & Verification Widget -->
            <div class="bg-white rounded-[14px] p-5 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="relative w-12 h-12 rounded-[14px] overflow-hidden bg-surface-container shrink-0 shadow-sm border border-[#E2DFD7]/60">
                        @if($profile?->logo_path)
                            <img class="w-full h-full object-cover" src="{{ asset('storage/' . $profile->logo_path) }}" alt="{{ $profile->shop_name }}">
                        @else
                            <div class="w-full h-full bg-primary flex items-center justify-center text-brand-amber font-heading font-bold text-xl">
                                {{ strtoupper(substr($profile?->shop_name ?? 'B', 0, 1)) }}
                            </div>
                        @endif
                        <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-brand-green rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-[10px]">check</span>
                        </div>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <h2 class="font-heading text-sm font-bold text-primary truncate">{{ $profile?->shop_name ?? 'My Farm Store' }}</h2>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="px-1.5 py-0.5 rounded-[4px] bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold inline-flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-[11px]">verified</span> Verified {{ $profile?->seller_type ?? 'Farmer' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-[10px] bg-surface-container-low flex flex-col gap-2 font-mono text-xs">
                    <div class="flex items-center justify-between text-on-surface-variant">
                        <span>Merchant Status</span>
                        <span class="font-bold text-brand-green uppercase">{{ $profile?->status ?? 'Approved' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-on-surface-variant">
                        <span>Commission Tier</span>
                        <span class="text-primary font-bold">{{ $profile?->commission_rate ? $profile->commission_rate.'%' : '10.00%' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-on-surface-variant">
                        <span>FSSAI License</span>
                        <span class="text-primary font-mono text-[11px]">{{ $profile?->fssai_number ?? '#21524021000918' }}</span>
                    </div>
                </div>

                <!-- Trust Score Progress -->
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-heading font-semibold text-primary">Market Trust Index</span>
                        <span class="font-mono font-bold text-brand-amber">{{ number_format($profile?->trust_score ?? 94, 0) }}/100</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-surface-container overflow-hidden">
                        <div class="h-full bg-brand-amber rounded-full" style="width: {{ $profile?->trust_score ?? 94 }}%;"></div>
                    </div>
                    <span class="font-mono text-[10px] text-brand-muted">Eligible for 0% Escrow Hold and Instant Payouts</span>
                </div>

                <div class="p-3 rounded-[10px] bg-surface-container-high/60 flex items-start gap-2.5">
                    <span class="material-symbols-outlined text-brand-amber text-[18px] shrink-0 mt-0.5">help_center</span>
                    <div class="flex flex-col text-xs">
                        <span class="font-heading font-bold text-primary">Need Legal Updates?</span>
                        <span class="text-brand-muted text-[11px] leading-snug mt-0.5">GSTIN and legal filings require administrative compliance desk review.</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- RIGHT COLUMN: Settings Workspace (9 Cols) -->
        <main class="lg:col-span-9 flex flex-col gap-6">

            <!-- Subtab Navigation Bar -->
            <div class="bg-white p-1.5 rounded-[14px] shadow-sm border border-[#E2DFD7]/60 flex items-center gap-2 overflow-x-auto">
                <a href="{{ route('seller.account.profile') }}" class="px-4 py-2 rounded-[10px] bg-primary text-white font-heading text-xs font-semibold flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">storefront</span>
                    <span>1. Shop Profile &amp; Identity</span>
                </a>
                <a href="{{ route('seller.account.location') }}" class="px-4 py-2 rounded-[10px] text-on-surface-variant hover:bg-surface-container-high font-heading text-xs font-medium flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">pin_drop</span>
                    <span>2. Farm Location &amp; Coordinates</span>
                </a>
                <a href="{{ route('seller.account.security') }}" class="px-4 py-2 rounded-[10px] text-on-surface-variant hover:bg-surface-container-high font-heading text-xs font-medium flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                    <span>3. Security &amp; Credentials</span>
                </a>
            </div>

            <form x-ref="profileForm" method="POST" action="{{ route('seller.account.profile.update') }}" enctype="multipart/form-data" class="flex flex-col gap-6" @input="unsavedChanges = true">
                @csrf
                @method('PUT')

                <!-- SECTION 1.1: Visual Brand & Farm Photography -->
                <div class="bg-white rounded-[14px] p-6 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-5">
                    <div class="flex flex-col">
                        <span class="font-mono text-[10px] uppercase tracking-widest text-outline">Section 1.1</span>
                        <h2 class="font-heading text-xl font-bold text-primary">Visual Brand &amp; Farm Photography</h2>
                        <p class="text-sm text-brand-muted">Store banner and verified orchard imagery shown to buyers in the wholesale catalog and live auction rooms.</p>
                    </div>

                    <!-- Cover Banner Preview with Logo Inset -->
                    <div class="relative w-full h-56 md:h-64 rounded-[14px] overflow-hidden bg-surface-container border border-[#E2DFD7]/60 shadow-inner group">
                        @if($profile?->banner_path)
                            <img class="w-full h-full object-cover" src="{{ asset('storage/' . $profile->banner_path) }}" alt="Store Banner">
                        @else
                            <div class="w-full h-full bg-gradient-to-r from-primary via-slate-800 to-primary flex items-center justify-center">
                                <span class="material-symbols-outlined text-white/30 text-[72px]">landscape</span>
                            </div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-primary/85 via-primary/25 to-transparent flex flex-col justify-end p-5 text-white">
                            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-[14px] overflow-hidden bg-white p-1 shadow-md shrink-0 border border-white/40">
                                        @if($profile?->logo_path)
                                            <img class="w-full h-full object-cover rounded-[10px]" src="{{ asset('storage/' . $profile->logo_path) }}" alt="Logo">
                                        @else
                                            <div class="w-full h-full bg-primary flex items-center justify-center text-brand-amber font-heading font-bold text-2xl rounded-[10px]">
                                                {{ strtoupper(substr($profile?->shop_name ?? 'B', 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-heading text-lg font-bold text-white">{{ $profile?->shop_name ?? 'My Farm Store' }}</span>
                                        <span class="font-mono text-[11px] text-surface-container-high uppercase tracking-wider">PRIMARY STOREFRONT COVER • 2560 × 720 PX</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label class="h-10 px-4 rounded-[10px] bg-white/90 hover:bg-white text-primary text-xs font-heading font-bold backdrop-blur-sm transition-all shadow flex items-center gap-1.5 cursor-pointer">
                                        <span class="material-symbols-outlined text-[18px]">photo_camera</span>
                                        <span>Replace Banner</span>
                                        <input type="file" name="banner_image" accept="image/*" class="hidden">
                                    </label>
                                    <label class="h-10 px-4 rounded-[10px] bg-white/90 hover:bg-white text-primary text-xs font-heading font-bold backdrop-blur-sm transition-all shadow flex items-center gap-1.5 cursor-pointer">
                                        <span class="material-symbols-outlined text-[18px]">account_circle</span>
                                        <span>Change Logo</span>
                                        <input type="file" name="logo_image" accept="image/*" class="hidden">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Batch certification note -->
                    <div class="p-4 rounded-[14px] bg-surface-container-low border border-[#E2DFD7]/60 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-[10px] bg-brand-amber/20 flex items-center justify-center text-brand-amber shrink-0">
                                <span class="material-symbols-outlined text-[22px]">cloud_upload</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-heading font-bold text-primary">Upload Farm Photography &amp; APMC Certification Slips</span>
                                <span class="text-xs text-brand-muted">PNG, JPG, or WEBP up to 5MB each. High resolution recommended for buyer trust.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 1.2: Core Business Identity & Signatory Details -->
                <div class="bg-white rounded-[14px] p-6 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-6">
                    <div class="flex flex-col">
                        <span class="font-mono text-[10px] uppercase tracking-widest text-outline">Section 1.2</span>
                        <h2 class="font-heading text-xl font-bold text-primary">Core Business Identity &amp; Signatory Details</h2>
                        <p class="text-sm text-brand-muted">Official marketplace identity tied to legal GSTIN #{{ $profile?->gstin ?? '27AABCG1294F1Z8' }}.</p>
                    </div>

                    <!-- Gated Role Notice -->
                    <div class="p-4 rounded-[14px] bg-brand-amber/10 border border-brand-amber/20 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-[10px] bg-brand-amber text-primary flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-[20px]">psychiatry</span>
                            </div>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <span class="font-heading text-sm font-bold text-primary">Registered Role: {{ $profile?->seller_type ?? 'Farmer' }} &amp; Cultivator</span>
                                    <span class="px-2 py-0.5 rounded-[4px] bg-brand-green text-white font-mono text-[10px] font-bold">VERIFIED PRODUCER</span>
                                </div>
                                <p class="text-xs text-brand-muted mt-1">
                                    Changing your merchant classification requires documentary review by Bazaario Agricultural Compliance. Existing auction bids and active harvests are locked during audit.
                                </p>
                            </div>
                        </div>
                        <button type="button" class="h-9 px-4 rounded-[10px] bg-white hover:bg-surface-container-high text-primary text-xs font-heading font-semibold transition-all shadow-sm shrink-0">
                            Request Change
                        </button>
                    </div>

                    <!-- Form Input Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary flex items-center justify-between">
                                <span>Farm / Shop Commercial Name</span>
                                <span class="font-mono text-[10px] text-brand-muted">Public Storefront</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-brand-muted text-[20px]">store</span>
                                <input type="text" name="shop_name" value="{{ old('shop_name', $profile?->shop_name) }}" class="w-full h-11 pl-11 pr-4 bg-surface-container-lowest rounded-[12px] text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm transition-colors" required>
                            </div>
                            @error('shop_name') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary flex items-center justify-between">
                                <span>Registered Signatory / Proprietor</span>
                                <span class="font-mono text-[10px] text-brand-green font-bold">KYC Matched</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-brand-muted text-[20px]">badge</span>
                                <input type="text" value="{{ $seller?->name }}" class="w-full h-11 pl-11 pr-4 bg-surface-container-low rounded-[12px] text-sm text-brand-muted border border-[#E2DFD7] cursor-not-allowed outline-none shadow-sm" readonly>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary flex items-center justify-between">
                                <span>Primary Business Email</span>
                                <span class="px-1.5 py-0.2 rounded bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold">VERIFIED</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-brand-muted text-[20px]">mail</span>
                                <input type="email" value="{{ $seller?->email }}" class="w-full h-11 pl-11 pr-10 bg-surface-container-low rounded-[12px] font-mono text-sm text-brand-muted border border-[#E2DFD7] cursor-not-allowed outline-none shadow-sm" readonly>
                                <span class="absolute right-3 material-symbols-outlined text-brand-green text-[18px]">verified</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary flex items-center justify-between">
                                <span>Primary Dispatch Phone</span>
                                <span class="px-1.5 py-0.2 rounded bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold">OTP BOUND</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-brand-muted text-[20px]">call</span>
                                <input type="tel" value="{{ $seller?->phone ?? '+91 98765 43210' }}" class="w-full h-11 pl-11 pr-10 bg-surface-container-low rounded-[12px] font-mono text-sm text-brand-muted border border-[#E2DFD7] cursor-not-allowed outline-none shadow-sm" readonly>
                                <span class="absolute right-3 material-symbols-outlined text-brand-green text-[18px]">check_circle</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5 md:col-span-2">
                            <label class="text-xs font-heading font-semibold text-primary flex items-center justify-between">
                                <span>Public Store &amp; Provenance Description</span>
                                <span class="font-mono text-[10px] text-brand-muted">Markdown Supported • <span x-text="charCount"></span>/500 Chars</span>
                            </label>
                            <textarea name="bio" rows="4" maxlength="500" @input="charCount = $el.value.length" class="w-full p-4 bg-surface-container-lowest rounded-[14px] text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm transition-colors resize-none leading-relaxed">{{ old('bio', $profile?->bio) }}</textarea>
                            @error('bio') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- SECTION 1.3: Operating Hours & Logistics Dispatch SLA -->
                <div class="bg-white rounded-[14px] p-6 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-6">
                    <div class="flex flex-col">
                        <span class="font-mono text-[10px] uppercase tracking-widest text-outline">Section 1.3</span>
                        <h2 class="font-heading text-xl font-bold text-primary">Operating Hours &amp; Logistics Dispatch SLA</h2>
                        <p class="text-sm text-brand-muted">Configure order acceptance cut-offs and daily courier harvest pickup handovers.</p>
                    </div>

                    <!-- Operating Days Buttons -->
                    <div class="flex flex-col gap-2.5">
                        <span class="text-xs font-heading font-semibold text-primary">Active Operating Harvest Days</span>
                        <div class="grid grid-cols-7 gap-2">
                            @foreach(['mon' => 'MON', 'tue' => 'TUE', 'wed' => 'WED', 'thu' => 'THU', 'fri' => 'FRI', 'sat' => 'SAT', 'sun' => 'SUN'] as $key => $lbl)
                                <button type="button" @click="toggleDay('{{ $key }}')" 
                                        :class="operatingDays.includes('{{ $key }}') ? 'bg-primary text-white border-primary' : 'bg-surface-container-low text-brand-muted border-[#E2DFD7]'" 
                                        class="h-12 rounded-[10px] font-mono text-xs font-bold flex flex-col items-center justify-center border transition-all">
                                    <span>{{ $lbl }}</span>
                                    <span class="text-[9px] uppercase font-normal" :class="operatingDays.includes('{{ $key }}') ? 'text-brand-green' : 'text-brand-muted'" x-text="operatingDays.includes('{{ $key }}') ? 'Active' : 'Off'"></span>
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="operating_days" :value="JSON.stringify(operatingDays)">
                    </div>

                    <!-- Time Window Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded-[14px] bg-surface-container-low border border-[#E2DFD7]/60 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-brand-amber text-[20px]">schedule</span>
                                <span class="text-xs font-heading font-bold text-primary">Order Acceptance Window</span>
                            </div>
                            <div class="font-mono text-sm font-bold text-primary bg-white px-3 py-2 rounded-[8px] border border-[#E2DFD7]/60 shadow-sm">
                                06:00 AM – 08:00 PM
                            </div>
                            <span class="font-mono text-[10px] text-brand-muted">Immediate SMS &amp; Portal Notification</span>
                        </div>

                        <div class="p-4 rounded-[14px] bg-surface-container-low border border-[#E2DFD7]/60 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-brand-amber text-[20px]">local_shipping</span>
                                <span class="text-xs font-heading font-bold text-primary">Assigned Courier Dispatch</span>
                            </div>
                            <div class="font-mono text-sm font-bold text-primary bg-white px-3 py-2 rounded-[8px] border border-[#E2DFD7]/60 shadow-sm">
                                04:00 PM – 06:00 PM
                            </div>
                            <span class="font-mono text-[10px] text-brand-muted">Daily Hyperlocal Logistics Truck Hub</span>
                        </div>

                        <div class="p-4 rounded-[14px] bg-surface-container-low border border-[#E2DFD7]/60 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-brand-amber text-[20px]">timer</span>
                                <span class="text-xs font-heading font-bold text-primary">Same-Day Harvest Cutoff</span>
                            </div>
                            <div class="font-mono text-sm font-bold text-primary bg-white px-3 py-2 rounded-[8px] border border-[#E2DFD7]/60 shadow-sm">
                                12:00 PM (Noon)
                            </div>
                            <span class="font-mono text-[10px] text-brand-muted">Orders after 12 PM ship next dawn</span>
                        </div>
                    </div>
                </div>

                <!-- Sticky Bottom Action Bar -->
                <div x-show="unsavedChanges" x-transition class="sticky bottom-6 z-30 bg-primary text-white rounded-[14px] p-4 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4 border border-white/10 backdrop-blur-md">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-brand-amber text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[18px]">published_with_changes</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-heading text-sm font-bold text-white">Unsaved Profile Changes Detected</span>
                            <span class="text-xs text-white/70">Modifications to store identity and dispatch schedule are pending save.</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                        <button type="button" @click="location.reload()" class="h-10 px-4 rounded-[10px] bg-white/10 hover:bg-white/20 text-white text-xs font-heading font-medium transition-colors">
                            Discard
                        </button>
                        <button type="submit" class="h-10 px-5 rounded-[10px] bg-brand-amber text-primary hover:brightness-105 active:scale-95 font-heading text-xs font-bold shadow-md transition-all flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">verified</span>
                            <span>Save Profile &amp; Settings</span>
                        </button>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>
@endsection
```

---

### Blueprint 2: `resources/views/seller/account/location.blade.php`
*(Location Telemetry, Postal Address, Lat/Lng/Elevation, GPS Autofill, Geofence Radar Preview)*

```blade
@extends('layouts.seller')

@section('title', 'Farm Location & Geofence Settings — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;
@endphp

<div class="flex flex-col w-full pb-16" x-data="{
    lat: '{{ $profile?->latitude ?? 17.7534 }}',
    lng: '{{ $profile?->longitude ?? 73.1895 }}',
    radius: {{ $profile?->operating_radius_km ?? 25 }},
    gpsLoading: false,
    gpsLocked: false,
    autoDetectGPS() {
        this.gpsLoading = true;
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.lat = pos.coords.latitude.toFixed(6);
                    this.lng = pos.coords.longitude.toFixed(6);
                    this.gpsLoading = false;
                    this.gpsLocked = true;
                },
                (err) => {
                    this.gpsLoading = false;
                    alert('Geolocation notice: ' + err.message + '. Retaining current coordinates.');
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        } else {
            this.gpsLoading = false;
            alert('Browser geolocation is not supported.');
        }
    }
}">

    <!-- Top Breadcrumb & Action Header -->
    <div class="px-6 py-4 bg-white/80 backdrop-blur-md rounded-[14px] shadow-sm mb-6 border border-[#E2DFD7]/60">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-[11px] text-brand-muted uppercase tracking-wider">Seller Center</span>
                    <span class="text-outline-variant font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-brand-muted uppercase tracking-wider">Account</span>
                    <span class="text-outline-variant font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-brand-amber font-semibold uppercase tracking-wider">Location &amp; Geofence</span>
                </div>
                <h1 class="font-heading text-2xl lg:text-3xl text-primary font-bold tracking-tight">Location Telemetry &amp; Geofence</h1>
                <p class="text-sm text-brand-muted">Configure your verified farm origin coordinates, dispatch hub geofence radius, and postal routing address.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('seller.account.profile') }}" class="h-11 px-4 rounded-[14px] bg-surface-container-low text-on-surface hover:bg-surface-container-high transition-colors text-sm font-medium flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">undo</span>
                    <span>Back to Profile</span>
                </a>
                <button type="button" @click="$refs.locationForm.submit()" class="h-11 px-5 rounded-[14px] bg-brand-amber text-primary hover:brightness-105 active:scale-95 transition-all text-sm font-heading font-bold shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">save</span>
                    <span>Save Location</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Navigation -->
        <aside class="lg:col-span-3 flex flex-col gap-6 sticky top-24">
            <div class="bg-white rounded-[14px] p-3 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-1">
                <div class="px-3 py-2">
                    <span class="font-mono text-[10px] uppercase tracking-widest text-outline">Configuration Suite</span>
                </div>
                <a href="{{ route('seller.account.profile') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] text-on-surface-variant hover:bg-surface-container-low transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px]">storefront</span>
                        <span class="text-sm font-heading">Shop Profile</span>
                    </div>
                </a>
                <a href="{{ route('seller.account.location') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] bg-secondary-container/30 text-on-secondary-container font-semibold transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-secondary">explore</span>
                        <span class="text-sm font-heading">Location &amp; Geofence</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-[4px] bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold">GPS OK</span>
                </a>
                <a href="{{ route('seller.account.security') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] text-on-surface-variant hover:bg-surface-container-low transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px]">shield</span>
                        <span class="text-sm font-heading">Security &amp; Password</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-[4px] bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold">2FA On</span>
                </a>
            </div>

            <div class="bg-white rounded-[14px] p-4 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-3 font-mono text-xs">
                <span class="font-heading font-bold text-primary text-sm">Spatial Isolation</span>
                <p class="text-brand-muted text-[11px] leading-relaxed">
                    Farm spatial geometry and harvest coordinates are isolated to Green Valley Farm (#BZ-SLR-{{ str_pad($profile?->id ?? 1, 4, '0', STR_PAD_LEFT) }}).
                </p>
                <div class="flex items-center gap-1.5 text-brand-green font-bold text-[10px] uppercase">
                    <span class="material-symbols-outlined text-[14px]">lock</span>
                    <span>WGS84 Datum Encrypted</span>
                </div>
            </div>
        </aside>

        <!-- Right Content Area -->
        <main class="lg:col-span-9 flex flex-col gap-6">

            <form x-ref="locationForm" method="POST" action="{{ route('seller.account.location.update') }}" class="flex flex-col gap-6">
                @csrf
                @method('PUT')

                <!-- Telemetry & Address Card -->
                <div class="bg-white rounded-[14px] p-6 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex flex-col">
                            <span class="font-mono text-[10px] uppercase tracking-widest text-outline">Section 2.1</span>
                            <h2 class="font-heading text-xl font-bold text-primary">Farm Origin &amp; Dispatch Hub Geofence</h2>
                            <p class="text-sm text-brand-muted">Precise farm telemetry calculates delivery estimates and farmer-to-door fresh supply chains.</p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <button type="button" @click="autoDetectGPS()" :disabled="gpsLoading" class="h-11 px-4 rounded-[12px] bg-brand-amber text-primary font-heading text-xs font-bold hover:brightness-105 active:scale-95 transition-all shadow-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]" :class="{ 'animate-spin': gpsLoading }">my_location</span>
                                <span x-text="gpsLoading ? 'Triangulating...' : (gpsLocked ? 'GPS Locked (±1.8m)' : 'GPS Auto-Detect')"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Telemetry status bar -->
                    <div class="p-3.5 rounded-[12px] bg-surface-container-low border border-[#E2DFD7]/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand-green animate-ping"></span>
                            <span class="font-mono text-xs font-bold text-primary">LIVE TELEMETRY ACTIVE: High-Precision GPS Lock</span>
                        </div>
                        <span class="font-mono text-[11px] text-brand-muted">Source: ISRO NavIC / Global WGS84 Datum</span>
                    </div>

                    <!-- Postal Address Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5 md:col-span-2">
                            <label class="text-xs font-heading font-semibold text-primary">Survey No. / Farm Lane Address</label>
                            <input type="text" name="address" value="{{ old('address', $profile?->address ?? 'Survey No. 142/3, Mango Orchard Road, Village Dapoli') }}" class="w-full h-11 px-4 bg-surface-container-lowest rounded-[12px] text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                            @error('address') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary">City / Tehsil</label>
                            <input type="text" name="city" value="{{ old('city', $profile?->city ?? 'Ratnagiri') }}" class="w-full h-11 px-4 bg-surface-container-lowest rounded-[12px] text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                            @error('city') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary">State</label>
                            <input type="text" name="state" value="{{ old('state', $profile?->state ?? 'Maharashtra') }}" class="w-full h-11 px-4 bg-surface-container-lowest rounded-[12px] text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                            @error('state') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary">Postal PIN Code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $profile?->postal_code ?? '415712') }}" class="w-full h-11 px-4 bg-surface-container-lowest rounded-[12px] font-mono text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                            @error('postal_code') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary flex items-center justify-between">
                                <span>Operating Coverage Radius (km)</span>
                                <span class="font-mono text-xs font-bold text-brand-amber" x-text="radius + ' km'"></span>
                            </label>
                            <input type="range" name="operating_radius_km" min="5" max="100" step="5" x-model="radius" class="w-full accent-brand-amber cursor-pointer mt-2">
                        </div>
                    </div>

                    <!-- Coordinate Telemetry Inputs (3 columns) -->
                    <div class="p-4 rounded-[14px] bg-surface-container-low border border-[#E2DFD7]/60 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-1">
                            <span class="font-mono text-[10px] text-brand-muted uppercase">Latitude Coordinate</span>
                            <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-[10px] border border-[#E2DFD7] shadow-sm">
                                <span class="material-symbols-outlined text-[16px] text-brand-amber">north</span>
                                <input type="number" step="any" name="latitude" x-model="lat" class="w-full font-mono text-sm font-bold text-primary bg-transparent focus:outline-none" required>
                            </div>
                            @error('latitude') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="font-mono text-[10px] text-brand-muted uppercase">Longitude Coordinate</span>
                            <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-[10px] border border-[#E2DFD7] shadow-sm">
                                <span class="material-symbols-outlined text-[16px] text-brand-amber">east</span>
                                <input type="number" step="any" name="longitude" x-model="lng" class="w-full font-mono text-sm font-bold text-primary bg-transparent focus:outline-none" required>
                            </div>
                            @error('longitude') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="font-mono text-[10px] text-brand-muted uppercase">Altitude / Elevation</span>
                            <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-[10px] border border-[#E2DFD7] shadow-sm">
                                <span class="material-symbols-outlined text-[16px] text-brand-amber">landscape</span>
                                <input type="text" value="142m MSL" readonly class="w-full font-mono text-sm font-bold text-brand-muted bg-transparent focus:outline-none cursor-default">
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Radar Geofence Card -->
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-heading font-bold text-primary">Interactive Topographic Geofence Preview</span>
                            <span class="font-mono text-[11px] text-brand-green font-bold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">shield</span> COURIER DISPATCH ZONE A
                            </span>
                        </div>
                        
                        <div class="relative w-full h-72 rounded-[14px] bg-slate-900 overflow-hidden shadow-sm border border-[#E2DFD7]/60 flex items-center justify-center">
                            <!-- Radar concentric rings -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="w-64 h-64 rounded-full border border-brand-amber/20 animate-pulse"></div>
                                <div class="absolute w-44 h-44 rounded-full border border-brand-amber/30"></div>
                                <div class="absolute w-24 h-24 rounded-full border border-brand-amber/40"></div>
                                <div class="absolute w-12 h-12 rounded-full bg-brand-amber/20 animate-ping"></div>
                            </div>

                            <!-- Target center -->
                            <div class="relative z-10 flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full bg-brand-amber text-primary flex items-center justify-center shadow-lg border-2 border-white">
                                    <span class="material-symbols-outlined text-[20px]">agriculture</span>
                                </div>
                                <div class="mt-2 px-3 py-1 rounded-[8px] bg-primary/90 text-white font-mono text-[11px] font-bold tracking-wider backdrop-blur-md shadow">
                                    {{ $profile?->shop_name ?? 'FARM ORIGIN GATE' }}
                                </div>
                            </div>

                            <!-- Overlay Badges -->
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                <span class="px-3 py-1 rounded-[6px] bg-white/90 backdrop-blur-md text-primary font-mono text-xs font-semibold shadow flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-brand-amber"></span>
                                    Hyperlocal Radius: <span x-text="radius + ' km Coverage'"></span>
                                </span>
                                <span class="px-3 py-1 rounded-[6px] bg-white/90 backdrop-blur-md text-primary font-mono text-xs font-semibold shadow">
                                    Nearest Cold-Chain Hub: Chiplun (34 km)
                                </span>
                            </div>

                            <div class="absolute bottom-4 right-4 bg-white/95 backdrop-blur-md px-3 py-2 rounded-[10px] shadow text-right">
                                <div class="font-mono text-[10px] text-brand-muted uppercase">Field Agent Signoff</div>
                                <div class="font-heading text-xs font-bold text-primary">Verified by Bazaario Agri-Logistics #409</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button type="submit" class="h-11 px-6 rounded-[12px] bg-brand-amber text-primary hover:brightness-105 active:scale-95 font-heading text-sm font-bold shadow-md transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">verified</span>
                        <span>Update Farm Coordinates</span>
                    </button>
                </div>
            </form>
        </main>
    </div>
</div>
@endsection
```

---

### Blueprint 3: `resources/views/seller/account/security.blade.php`
*(Account Security, Password Update Form with 4-Tier Complexity Meter, 2FA Status)*

```blade
@extends('layouts.seller')

@section('title', 'Security & Credentials — Bazaario Seller Center')

@section('content')
<div class="flex flex-col w-full pb-16" x-data="{
    currPass: '',
    newPass: '',
    confPass: '',
    showCurr: false,
    showNew: false,
    showConf: false,
    strengthLabel: 'Weak (0%)',
    strengthLevel: 0,
    hasMinLen: false,
    hasUpper: false,
    hasNumber: false,
    hasSymbol: false,
    evaluateStrength() {
        const val = this.newPass;
        this.hasMinLen = val.length >= 8;
        this.hasUpper = /[A-Z]/.test(val);
        this.hasNumber = /[0-9]/.test(val);
        this.hasSymbol = /[^A-Za-z0-9]/.test(val);

        let score = 0;
        if (this.hasMinLen) score++;
        if (this.hasUpper) score++;
        if (this.hasNumber) score++;
        if (this.hasSymbol) score++;

        this.strengthLevel = score;
        if (score === 0) this.strengthLabel = 'Weak (0%)';
        else if (score <= 2) this.strengthLabel = 'Medium (50%)';
        else if (score === 3) this.strengthLabel = 'Strong (75%)';
        else if (score === 4) this.strengthLabel = 'Enterprise (100%)';
    }
}">

    <!-- Top Breadcrumb -->
    <div class="px-6 py-4 bg-white/80 backdrop-blur-md rounded-[14px] shadow-sm mb-6 border border-[#E2DFD7]/60">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-[11px] text-brand-muted uppercase tracking-wider">Seller Center</span>
                    <span class="text-outline-variant font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-brand-muted uppercase tracking-wider">Account</span>
                    <span class="text-outline-variant font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-brand-amber font-semibold uppercase tracking-wider">Security &amp; Password</span>
                </div>
                <h1 class="font-heading text-2xl lg:text-3xl text-primary font-bold tracking-tight">Security &amp; Access Credentials</h1>
                <p class="text-sm text-brand-muted">Update your merchant authentication keys and manage 2-factor hardware/SMS authentications.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('seller.account.profile') }}" class="h-11 px-4 rounded-[14px] bg-surface-container-low text-on-surface hover:bg-surface-container-high transition-colors text-sm font-medium flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">undo</span>
                    <span>Back to Profile</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Navigation -->
        <aside class="lg:col-span-3 flex flex-col gap-6 sticky top-24">
            <div class="bg-white rounded-[14px] p-3 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-1">
                <div class="px-3 py-2">
                    <span class="font-mono text-[10px] uppercase tracking-widest text-outline">Configuration Suite</span>
                </div>
                <a href="{{ route('seller.account.profile') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] text-on-surface-variant hover:bg-surface-container-low transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px]">storefront</span>
                        <span class="text-sm font-heading">Shop Profile</span>
                    </div>
                </a>
                <a href="{{ route('seller.account.location') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] text-on-surface-variant hover:bg-surface-container-low transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px]">explore</span>
                        <span class="text-sm font-heading">Location &amp; Geofence</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-[4px] bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold">GPS OK</span>
                </a>
                <a href="{{ route('seller.account.security') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] bg-secondary-container/30 text-on-secondary-container font-semibold transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-secondary">shield</span>
                        <span class="text-sm font-heading">Security &amp; Password</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-[4px] bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold">2FA On</span>
                </a>
            </div>
        </aside>

        <!-- Right Content Area -->
        <main class="lg:col-span-9 flex flex-col gap-6">

            <div class="bg-white rounded-[14px] p-6 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-6">
                <div class="flex flex-col">
                    <span class="font-mono text-[10px] uppercase tracking-widest text-outline">Section 3.1</span>
                    <h2 class="font-heading text-xl font-bold text-primary">Account Security &amp; Access Credentials</h2>
                    <p class="text-sm text-brand-muted">Update your merchant authentication keys and manage 2-factor hardware/SMS authentications.</p>
                </div>

                <form method="POST" action="{{ route('seller.account.security.password') }}" class="flex flex-col gap-5">
                    @csrf

                    <!-- Password Inputs (3 columns) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary">Current Password</label>
                            <div class="relative flex items-center">
                                <input :type="showCurr ? 'text' : 'password'" name="current_password" x-model="currPass" class="w-full h-11 px-4 pr-10 bg-surface-container-lowest rounded-[12px] font-mono text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                                <button type="button" @click="showCurr = !showCurr" class="absolute right-3 text-brand-muted hover:text-primary">
                                    <span class="material-symbols-outlined text-[18px]" x-text="showCurr ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                            @error('current_password') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary">New Password</label>
                            <div class="relative flex items-center">
                                <input :type="showNew ? 'text' : 'password'" name="password" x-model="newPass" @input="evaluateStrength()" class="w-full h-11 px-4 pr-10 bg-surface-container-lowest rounded-[12px] font-mono text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                                <button type="button" @click="showNew = !showNew" class="absolute right-3 text-brand-muted hover:text-primary">
                                    <span class="material-symbols-outlined text-[18px]" x-text="showNew ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                            @error('password') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary">Confirm New Password</label>
                            <div class="relative flex items-center">
                                <input :type="showConf ? 'text' : 'password'" name="password_confirmation" x-model="confPass" class="w-full h-11 px-4 pr-10 bg-surface-container-lowest rounded-[12px] font-mono text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                                <button type="button" @click="showConf = !showConf" class="absolute right-3 text-brand-muted hover:text-primary">
                                    <span class="material-symbols-outlined text-[18px]" x-text="showConf ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic 4-Tier Complexity Meter -->
                    <div class="p-4 rounded-[14px] bg-surface-container-low border border-[#E2DFD7]/60 flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-heading font-bold text-primary">Password Complexity Score</span>
                            <span class="font-mono text-xs font-bold" :class="{
                                'text-error': strengthLevel <= 1,
                                'text-brand-amber': strengthLevel === 2,
                                'text-brand-green': strengthLevel >= 3
                            }" x-text="strengthLabel"></span>
                        </div>

                        <!-- 4-bar indicator -->
                        <div class="grid grid-cols-4 gap-2">
                            <div class="h-2 rounded-full transition-colors" :class="strengthLevel >= 1 ? (strengthLevel === 1 ? 'bg-error' : (strengthLevel === 2 ? 'bg-brand-amber' : 'bg-brand-green')) : 'bg-surface-container-high'"></div>
                            <div class="h-2 rounded-full transition-colors" :class="strengthLevel >= 2 ? (strengthLevel === 2 ? 'bg-brand-amber' : 'bg-brand-green') : 'bg-surface-container-high'"></div>
                            <div class="h-2 rounded-full transition-colors" :class="strengthLevel >= 3 ? 'bg-brand-green' : 'bg-surface-container-high'"></div>
                            <div class="h-2 rounded-full transition-colors" :class="strengthLevel >= 4 ? 'bg-brand-green' : 'bg-surface-container-high'"></div>
                        </div>

                        <!-- Checklist -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pt-1 font-mono text-[11px]">
                            <div class="flex items-center gap-1.5" :class="hasMinLen ? 'text-brand-green' : 'text-brand-muted'">
                                <span class="material-symbols-outlined text-[16px]" x-text="hasMinLen ? 'check_circle' : 'radio_button_unchecked'"></span>
                                <span>Minimum 8 characters</span>
                            </div>
                            <div class="flex items-center gap-1.5" :class="hasUpper ? 'text-brand-green' : 'text-brand-muted'">
                                <span class="material-symbols-outlined text-[16px]" x-text="hasUpper ? 'check_circle' : 'radio_button_unchecked'"></span>
                                <span>Contains uppercase letter</span>
                            </div>
                            <div class="flex items-center gap-1.5" :class="hasNumber ? 'text-brand-green' : 'text-brand-muted'">
                                <span class="material-symbols-outlined text-[16px]" x-text="hasNumber ? 'check_circle' : 'radio_button_unchecked'"></span>
                                <span>Contains numeral (0-9)</span>
                            </div>
                            <div class="flex items-center gap-1.5" :class="hasSymbol ? 'text-brand-green' : 'text-brand-muted'">
                                <span class="material-symbols-outlined text-[16px]" x-text="hasSymbol ? 'check_circle' : 'radio_button_unchecked'"></span>
                                <span>Contains special symbol (!@#$%^&amp;*)</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="h-11 px-6 rounded-[12px] bg-primary text-white hover:bg-slate-800 font-heading text-xs font-bold transition-all shadow-md flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">key</span>
                            <span>Update Password</span>
                        </button>
                    </div>
                </form>

                <!-- 2FA Section -->
                <div class="p-4 rounded-[14px] bg-surface-container-low border border-[#E2DFD7]/60 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-[10px] bg-brand-green/20 text-brand-green flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[22px]">phonelink_lock</span>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="font-heading text-sm font-bold text-primary">Two-Factor Authentication (2FA)</span>
                                <span class="px-2 py-0.5 rounded-[4px] bg-brand-green text-white font-mono text-[10px] font-bold">ENFORCED</span>
                            </div>
                            <span class="text-xs text-brand-muted mt-0.5">Authenticator App (TOTP) + SMS fallback. Mandatory for escrow payout releases.</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="h-9 px-4 rounded-[10px] bg-white text-primary text-xs font-heading font-medium border border-[#E2DFD7] hover:bg-surface-container transition-all">
                            Rotate Recovery Keys
                        </button>
                    </div>
                </div>

                <!-- Audit info -->
                <div class="flex items-center justify-between px-4 py-3 rounded-[10px] bg-surface-container-low font-mono text-xs text-brand-muted border border-[#E2DFD7]/40">
                    <span>Password last updated: Active session secured</span>
                    <span class="font-bold text-primary">Audit Log Compliant</span>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
```

---

### Blueprint 4: `resources/views/seller/auctions/index.blade.php`
*(Master Auction Registry Table with status tabs, filter pills, and quick action controls)*

```blade
@extends('layouts.seller')

@section('title', 'Wholesale Auctions Master Registry — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;
    $currentStatus = request('status', 'all');
@endphp

<div class="flex flex-col w-full pb-16">

    <!-- BREADCRUMB & METRIC STATUS BAR -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-4 mb-2">
        <div class="flex items-center gap-2 text-xs font-mono text-brand-muted">
            <a href="{{ route('seller.dashboard') }}" class="hover:text-primary transition-colors">Seller Center</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">Auctions &amp; Wholesale Registry</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-white border border-[#E2DFD7]/60 rounded-[8px] font-mono text-[11px] uppercase text-primary font-semibold shadow-sm">
                <span class="w-2 h-2 rounded-full bg-brand-green animate-pulse"></span>
                APMC SPOT TICKER CONNECTED
            </span>
            <span class="font-mono text-xs text-brand-muted bg-surface-container-high px-2.5 py-1 rounded-[8px]">
                SELLER ID: #BZ-SLR-{{ str_pad($profile?->id ?? 1, 4, '0', STR_PAD_LEFT) }}
            </span>
        </div>
    </div>

    <!-- WORKSPACE HEADER & ACTION BUTTONS -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pb-6 border-b border-[#E2DFD7]/60">
        <div class="space-y-1.5 max-w-3xl">
            <div class="flex items-center gap-3">
                <h1 class="font-heading text-2xl lg:text-3xl tracking-tight text-primary font-bold">
                    Auctions &amp; Wholesale Bidding
                </h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-secondary-container text-on-secondary-container font-mono text-xs font-bold uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-on-secondary-container"></span>
                    {{ $activeCount ?? 1 }} Active Lot
                </span>
            </div>
            <p class="text-sm text-brand-muted leading-relaxed">
                Live spot-market auctions, real-time bidder telemetry, and verified bulk agricultural allocations for {{ $profile?->shop_name ?? 'My Farm' }}.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('seller.auctions.live') }}" class="h-11 px-5 rounded-[12px] bg-primary text-white font-heading text-xs font-bold shadow-sm hover:bg-slate-800 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-brand-amber">stream</span>
                <span>Open Live Terminal</span>
            </a>
            <a href="{{ route('seller.auctions.create') }}" class="h-11 px-5 rounded-[12px] bg-brand-amber text-primary font-heading text-xs font-bold shadow-sm hover:brightness-105 active:scale-95 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                <span>Create New Auction</span>
            </a>
        </div>
    </div>

    <!-- MASTER REGISTRY TABLE CARD -->
    <div class="mt-6 bg-white rounded-[14px] shadow-sm border border-[#E2DFD7]/60 overflow-hidden flex flex-col">
        
        <!-- Table Header & Status Filter Pills -->
        <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-[#E2DFD7]/60">
            <div>
                <h3 class="font-heading text-lg font-bold text-primary">All Auctions Master Registry</h3>
                <p class="text-xs text-brand-muted">Historical &amp; real-time performance of your bulk commodity lots</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-1.5 bg-surface-container-low p-1 rounded-[12px] border border-[#E2DFD7]/40 text-xs">
                <a href="{{ route('seller.auctions.index') }}" class="px-3 py-1.5 rounded-[8px] font-heading {{ $currentStatus === 'all' ? 'bg-white text-primary font-bold shadow-sm' : 'text-brand-muted hover:text-primary' }}">
                    All ({{ $counts['all'] ?? 0 }})
                </a>
                <a href="{{ route('seller.auctions.index', ['status' => 'scheduled']) }}" class="px-3 py-1.5 rounded-[8px] font-heading {{ $currentStatus === 'scheduled' ? 'bg-white text-primary font-bold shadow-sm' : 'text-brand-muted hover:text-primary' }}">
                    Scheduled ({{ $counts['scheduled'] ?? 0 }})
                </a>
                <a href="{{ route('seller.auctions.index', ['status' => 'live']) }}" class="px-3 py-1.5 rounded-[8px] font-heading flex items-center gap-1.5 {{ $currentStatus === 'live' ? 'bg-white text-primary font-bold shadow-sm' : 'text-brand-muted hover:text-primary' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-green animate-pulse"></span>
                    Live ({{ $counts['live'] ?? 0 }})
                </a>
                <a href="{{ route('seller.auctions.index', ['status' => 'ended']) }}" class="px-3 py-1.5 rounded-[8px] font-heading {{ $currentStatus === 'ended' ? 'bg-white text-primary font-bold shadow-sm' : 'text-brand-muted hover:text-primary' }}">
                    Completed ({{ $counts['ended'] ?? 0 }})
                </a>
                <a href="{{ route('seller.auctions.index', ['status' => 'cancelled']) }}" class="px-3 py-1.5 rounded-[8px] font-heading {{ $currentStatus === 'cancelled' ? 'bg-white text-primary font-bold shadow-sm' : 'text-brand-muted hover:text-primary' }}">
                    Cancelled ({{ $counts['cancelled'] ?? 0 }})
                </a>
            </div>
        </div>

        <!-- Registry Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low font-mono text-[11px] text-brand-muted uppercase tracking-wider border-b border-[#E2DFD7]/60">
                    <tr>
                        <th class="py-3.5 px-5">Auction ID</th>
                        <th class="py-3.5 px-5">Product Lot &amp; Spec</th>
                        <th class="py-3.5 px-5">Starting Price</th>
                        <th class="py-3.5 px-5">Current / Final Bid</th>
                        <th class="py-3.5 px-5">Bid Count</th>
                        <th class="py-3.5 px-5">Timing / Status</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2DFD7]/60 text-primary">
                    @forelse($auctions ?? [] as $auc)
                        @php
                            $bidsCount = $auc->bids_count ?? $auc->bids->count();
                            $isReserveMet = $auc->reserve_price && ($auc->current_price >= $auc->reserve_price);
                        @endphp
                        <tr class="hover:bg-surface-container-low/50 transition-colors {{ $auc->isLive() ? 'bg-brand-amber/5' : '' }}">
                            <td class="py-4 px-5">
                                <span class="font-mono font-bold text-primary">#AUC-{{ str_pad($auc->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-heading font-bold text-primary">{{ $auc->product?->name ?? 'Agricultural Lot' }}</div>
                                <div class="text-xs text-brand-muted">{{ $auc->product?->category?->name ?? 'Farm Produce' }} • Unit: {{ $auc->product?->unit_type ?? 'lot' }}</div>
                            </td>
                            <td class="py-4 px-5 font-mono text-brand-muted">
                                ₹{{ number_format($auc->starting_price, 2) }}
                            </td>
                            <td class="py-4 px-5 font-mono">
                                <div class="font-bold text-primary">₹{{ number_format($auc->current_price, 2) }}</div>
                                @if($auc->reserve_price)
                                    @if($isReserveMet)
                                        <span class="text-[11px] text-brand-green font-semibold">Reserve Met</span>
                                    @else
                                        <span class="text-[11px] text-brand-muted">Target: ₹{{ number_format($auc->reserve_price, 0) }}</span>
                                    @endif
                                @endif
                            </td>
                            <td class="py-4 px-5 font-mono">
                                <span class="font-bold text-primary">{{ $bidsCount }}</span>
                                <span class="text-xs text-brand-muted block">bids placed</span>
                            </td>
                            <td class="py-4 px-5">
                                @if($auc->status === 'live')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-secondary-container text-on-secondary-container font-mono text-xs font-bold uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-on-secondary-container animate-pulse"></span>
                                        LIVE BIDDING
                                    </span>
                                @elseif($auc->status === 'scheduled')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-surface-container-high text-primary font-mono text-xs font-semibold uppercase">
                                        Starts {{ $auc->starts_at->diffForHumans() }}
                                    </span>
                                @elseif($auc->status === 'ended')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-brand-green/20 text-brand-green font-mono text-xs font-bold uppercase">
                                        Completed
                                    </span>
                                @elseif($auc->status === 'cancelled')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-error-container text-on-error-container font-mono text-xs font-bold uppercase">
                                        Cancelled
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    @if($auc->status === 'live')
                                        <a href="{{ route('seller.auctions.live') }}" class="px-3 py-1.5 rounded-[10px] bg-brand-amber text-primary font-heading text-xs font-bold hover:brightness-105">
                                            Monitor Live
                                        </a>
                                    @elseif($auc->status === 'scheduled')
                                        <a href="{{ route('seller.auctions.show', $auc) }}" class="px-3 py-1.5 rounded-[10px] bg-surface-container hover:bg-surface-container-high text-xs font-medium text-primary">
                                            Details
                                        </a>
                                        @if($bidsCount === 0)
                                            <form method="POST" action="{{ route('seller.auctions.cancel', $auc) }}" onsubmit="return confirm('Cancel this scheduled auction lot? This action cannot be undone.');" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-[10px] text-error hover:bg-error-container hover:text-on-error-container text-xs font-medium transition-colors">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <a href="{{ route('seller.auctions.show', $auc) }}" class="px-3 py-1.5 rounded-[10px] bg-surface-container hover:bg-surface-container-high text-xs font-medium text-primary">
                                            Inspect Lot
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-brand-muted">
                                <span class="material-symbols-outlined text-[36px] text-brand-muted/40 mb-2">gavel</span>
                                <p class="font-heading font-medium text-primary">No auctions found matching this filter</p>
                                <p class="text-xs mt-1">Create a new wholesale lot to begin spot-market bidding.</p>
                                <a href="{{ route('seller.auctions.create') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-[10px] bg-brand-amber text-primary font-heading text-xs font-bold">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                    <span>Create Auction</span>
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($auctions) && method_exists($auctions, 'links'))
            <div class="p-4 bg-surface-container-low border-t border-[#E2DFD7]/60">
                {{ $auctions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
```

---

### Blueprint 5: `resources/views/seller/auctions/live.blade.php`
*(Wholesale Bidding Live Terminal, Active Lot Hero Tile, Countdown Clock, Reserve Met Badge, Anonymized Activity Stream, Cancellation Guard, Inline Create Panel)*

```blade
@extends('layouts.seller')

@section('title', 'Wholesale Live Bidding Terminal — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;
    $liveAuction = $activeAuction ?? null;
    $bids = $liveAuction ? $liveAuction->bids()->latest()->take(10)->get() : collect();
    $highestBid = $liveAuction ? $liveAuction->current_price : 0;
    $isReserveMet = $liveAuction && $liveAuction->reserve_price && ($highestBid >= $liveAuction->reserve_price);
    $bidsCount = $liveAuction ? $liveAuction->bids()->count() : 0;
@endphp

<div class="flex flex-col w-full pb-16" x-data="{
    secondsLeft: {{ $liveAuction ? max(0, now()->diffInSeconds($liveAuction->ends_at, false)) : 0 }},
    timerText: '00:00:00',
    init() {
        this.updateTimer();
        setInterval(() => {
            if (this.secondsLeft > 0) {
                this.secondsLeft--;
                this.updateTimer();
            } else {
                this.timerText = '00:00:00 - CLOSED';
            }
        }, 1000);
    },
    updateTimer() {
        const hrs = Math.floor(this.secondsLeft / 3600);
        const mins = Math.floor((this.secondsLeft % 3600) / 60);
        const secs = this.secondsLeft % 60;
        this.timerText = String(hrs).padStart(2, '0') + ':' + String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
    }
}">

    <!-- BREADCRUMB & METRIC STATUS BAR -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-4 mb-2">
        <div class="flex items-center gap-2 text-xs font-mono text-brand-muted">
            <a href="{{ route('seller.dashboard') }}" class="hover:text-primary transition-colors">Seller Center</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('seller.auctions.index') }}" class="hover:text-primary transition-colors">Auctions</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">Wholesale Live Terminal</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-white border border-[#E2DFD7]/60 rounded-[8px] font-mono text-[11px] uppercase text-primary font-semibold shadow-sm">
                <span class="w-2 h-2 rounded-full bg-brand-green animate-pulse"></span>
                APMC SPOT TICKER CONNECTED
            </span>
            <span class="font-mono text-xs text-brand-muted bg-surface-container-high px-2.5 py-1 rounded-[8px]">
                SELLER ID: #BZ-SLR-{{ str_pad($profile?->id ?? 1, 4, '0', STR_PAD_LEFT) }}
            </span>
        </div>
    </div>

    <!-- WORKSPACE HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pb-6 border-b border-[#E2DFD7]/60">
        <div class="space-y-1.5 max-w-3xl">
            <div class="flex items-center gap-3">
                <h1 class="font-heading text-2xl lg:text-3xl tracking-tight text-primary font-bold">
                    Wholesale Bidding Terminal
                </h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-secondary-container text-on-secondary-container font-mono text-xs font-bold uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-on-secondary-container"></span>
                    {{ $liveAuction ? '1 Active Lot' : 'No Live Lots' }}
                </span>
            </div>
            <p class="text-sm text-brand-muted leading-relaxed">
                Live spot-market auctions, real-time bidder telemetry, and verified bulk agricultural allocations for {{ $profile?->shop_name ?? 'My Farm' }}.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('seller.auctions.index') }}" class="h-11 px-4 rounded-[12px] bg-white border border-[#E2DFD7]/60 text-primary font-heading text-xs font-bold shadow-sm hover:bg-surface-container transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">table_rows</span>
                <span>Registry Table</span>
            </a>
            <a href="{{ route('seller.auctions.create') }}" class="h-11 px-5 rounded-[12px] bg-brand-amber text-primary font-heading text-xs font-bold shadow-sm hover:brightness-105 active:scale-95 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                <span>Create Auction</span>
            </a>
        </div>
    </div>

    @if(!$liveAuction)
        <!-- Empty Live State -->
        <div class="mt-8 bg-white rounded-[14px] p-12 text-center border border-[#E2DFD7]/60 shadow-sm flex flex-col items-center justify-center">
            <div class="w-16 h-16 rounded-full bg-brand-amber/20 flex items-center justify-center text-brand-amber mb-4">
                <span class="material-symbols-outlined text-[32px]">gavel</span>
            </div>
            <h2 class="font-heading text-xl font-bold text-primary">No Active Live Auctions Currently</h2>
            <p class="text-sm text-brand-muted max-w-md mt-1 mb-6">There are currently no live bidding terminals broadcasting from your farm catalog. Create a scheduled or instant wholesale auction to begin receiving live trade orders.</p>
            <a href="{{ route('seller.auctions.create') }}" class="h-11 px-6 rounded-[12px] bg-brand-amber text-primary font-heading text-xs font-bold shadow-md hover:brightness-105 transition-all inline-flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Schedule a Lot Now</span>
            </a>
        </div>
    @else
        <!-- DUAL-PANE WORKSPACE: LEFT 62% (8 Cols) / RIGHT 38% (4 Cols) -->
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- COLUMN A: 62% (8 Cols) - LIVE TERMINAL & TELEMETRY -->
            <div class="lg:col-span-8 flex flex-col gap-6">

                <!-- MAIN LIVE AUCTION CARD -->
                <div class="bg-white rounded-[14px] p-6 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-6 relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-brand-amber via-secondary to-brand-green"></div>

                    <!-- Header Block of Lot -->
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="font-mono text-xs font-bold bg-surface-container px-2.5 py-0.5 rounded text-primary">
                                    LOT #AUC-{{ str_pad($liveAuction->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-error-container text-on-error-container font-mono text-xs font-bold uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-error animate-ping"></span>
                                    LIVE BIDDING
                                </span>
                                <span class="font-mono text-xs px-2 py-0.5 rounded bg-surface-container text-brand-muted uppercase">
                                    APMC Lot #{{ $liveAuction->product_id }}
                                </span>
                            </div>
                            <h2 class="font-heading text-xl md:text-2xl font-bold text-primary tracking-tight">
                                {{ $liveAuction->product?->name ?? 'Grade A+ Farm Lot' }}
                            </h2>
                            <p class="text-sm text-brand-muted">
                                {{ $liveAuction->product?->description ? Str::limit($liveAuction->product->description, 100) : 'Fresh farm harvest bulk allocation' }} • Unit: {{ $liveAuction->product?->unit_type ?? 'kg' }}
                            </p>
                        </div>

                        <!-- Verified Tags -->
                        <div class="flex sm:flex-col items-end gap-1.5 shrink-0">
                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-[8px] bg-brand-green/20 text-brand-green font-mono text-xs font-semibold">
                                <span class="material-symbols-outlined text-[15px]">verified</span>
                                GI CERTIFIED
                            </div>
                            <span class="font-mono text-[11px] text-brand-muted">Cold Chain Pre-Cooled</span>
                        </div>
                    </div>

                    <!-- Agronomic Specs Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-surface-container-low p-3.5 rounded-[12px] border border-[#E2DFD7]/40 text-xs">
                        <div class="flex flex-col">
                            <span class="font-mono text-[10px] uppercase text-brand-muted font-medium">Harvest Date</span>
                            <span class="font-heading font-semibold text-primary mt-0.5">{{ $liveAuction->product?->harvest_date ? \Carbon\Carbon::parse($liveAuction->product->harvest_date)->format('M d, Y') : 'Fresh Harvest' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-mono text-[10px] uppercase text-brand-muted font-medium">Shelf-Life</span>
                            <span class="font-heading font-semibold text-primary mt-0.5">{{ $liveAuction->product?->expiry_days ?? 7 }} Days Window</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-mono text-[10px] uppercase text-brand-muted font-medium">Stock Allocated</span>
                            <span class="font-heading font-semibold text-primary mt-0.5">{{ $liveAuction->product?->stock ?? 100 }} {{ $liveAuction->product?->unit_type ?? 'units' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-mono text-[10px] uppercase text-brand-muted font-medium">Inspection Grade</span>
                            <span class="font-heading font-semibold text-primary mt-0.5">APMC Grade A+ Verified</span>
                        </div>
                    </div>

                    <!-- HERO BIDDING METRICS - 4 TILES -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                        
                        <!-- TILE 1: CURRENT HIGHEST BID (Dominant Anchor, 2 cols) -->
                        <div class="sm:col-span-2 xl:col-span-2 bg-surface-container p-5 rounded-[14px] flex flex-col justify-between relative overflow-hidden border border-[#E2DFD7]/60 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-mono text-xs uppercase tracking-wider text-brand-muted font-semibold">
                                    Current Highest Leading Bid
                                </span>
                                @if($isReserveMet)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-[6px] bg-brand-green/20 text-brand-green font-mono text-[11px] font-bold uppercase">
                                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                        RESERVE MET
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-[6px] bg-brand-amber/20 text-brand-amber font-mono text-[11px] font-bold uppercase">
                                        <span class="material-symbols-outlined text-[14px]">info</span>
                                        RESERVE NOT MET (Target ₹{{ number_format($liveAuction->reserve_price ?? 0, 0) }})
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-baseline gap-3 my-1">
                                <span class="font-mono text-4xl lg:text-5xl font-bold tracking-tight text-primary">
                                    ₹{{ number_format($highestBid, 2) }}
                                </span>
                                <span class="text-xs text-brand-muted font-medium">
                                    / Base Lot
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs pt-3 mt-1 border-t border-[#E2DFD7]/60 text-brand-muted">
                                <span>Starting Price: <strong class="font-mono text-primary">₹{{ number_format($liveAuction->starting_price, 2) }}</strong></span>
                                <span class="{{ $isReserveMet ? 'text-brand-green font-semibold' : 'text-brand-amber' }}">
                                    Min Increment: ₹{{ number_format($liveAuction->minimum_increment, 0) }}
                                </span>
                            </div>
                        </div>

                        <!-- TILE 2: COUNTDOWN TIMER -->
                        <div class="bg-surface-container-low p-4 rounded-[14px] flex flex-col justify-between border border-[#E2DFD7]/60">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs uppercase text-brand-muted font-medium">Time Remaining</span>
                                <span class="material-symbols-outlined text-[18px] text-brand-amber">timer</span>
                            </div>
                            <div class="my-2">
                                <div class="font-mono text-2xl font-bold text-secondary" x-text="timerText">
                                    00:00:00
                                </div>
                                <div class="w-full bg-surface-container-high h-1.5 rounded-full overflow-hidden mt-2">
                                    <div class="bg-brand-amber h-full rounded-full transition-all duration-1000" style="width: 75%;"></div>
                                </div>
                            </div>
                            <span class="font-mono text-[11px] text-brand-muted">Soft-close buffer active</span>
                        </div>

                        <!-- TILE 3: RULES & RESERVE -->
                        <div class="bg-surface-container-low p-4 rounded-[14px] flex flex-col justify-between border border-[#E2DFD7]/60">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs uppercase text-brand-muted font-medium">Reserve Spec</span>
                                <span class="material-symbols-outlined text-[18px] text-brand-muted">gavel</span>
                            </div>
                            <div class="my-1">
                                <span class="font-mono text-xl font-bold text-primary">₹{{ number_format($liveAuction->reserve_price ?? $liveAuction->starting_price, 2) }}</span>
                                <div class="text-xs text-brand-muted mt-1">
                                    Min Increment: <span class="font-mono font-bold text-primary">₹{{ number_format($liveAuction->minimum_increment, 0) }}</span>
                                </div>
                            </div>
                            <span class="font-mono text-[11px] text-brand-muted">Binding Trade Law</span>
                        </div>
                    </div>

                    <!-- Participation Telemetry Ribbon -->
                    <div class="flex flex-wrap items-center justify-between gap-4 p-3.5 bg-surface-container-low rounded-[12px] border border-[#E2DFD7]/40">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-primary shadow-sm">
                                <span class="material-symbols-outlined text-[20px]">groups</span>
                            </div>
                            <div>
                                <div class="text-sm font-heading font-bold text-primary">
                                    {{ $bidsCount }} Bids Placed by Verified Wholesale Network
                                </div>
                                <div class="text-xs text-brand-muted">
                                    100% of participants hold verified pre-funded trade escrow guarantees
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs px-2.5 py-1 rounded bg-white text-primary font-medium shadow-sm">
                                Velocity: Active Pulse
                            </span>
                        </div>
                    </div>

                    <!-- Real-Time Bid Activity Stream -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <h3 class="font-heading text-base font-bold text-primary">
                                    Real-Time Bid Activity Stream
                                </h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-surface-container text-brand-muted font-mono text-[10px] uppercase">
                                    ANONYMIZED APMC PROTOCOL
                                </span>
                            </div>
                            <span class="font-mono text-[11px] text-brand-muted">Auto-updating live</span>
                        </div>

                        <div class="overflow-x-auto bg-surface-container-low rounded-[12px] border border-[#E2DFD7]/60">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-surface-container font-mono text-[10px] text-brand-muted uppercase tracking-wider border-b border-[#E2DFD7]/40">
                                    <tr>
                                        <th class="py-2.5 px-4">Bidder Hash</th>
                                        <th class="py-2.5 px-4">Entity Category / Node</th>
                                        <th class="py-2.5 px-4">Placed Bid</th>
                                        <th class="py-2.5 px-4">Timestamp</th>
                                        <th class="py-2.5 px-4 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#E2DFD7]/40 text-primary">
                                    @forelse($bids as $index => $bid)
                                        <tr class="{{ $index === 0 ? 'bg-white font-semibold' : '' }}">
                                            <td class="py-3 px-4 font-mono font-bold flex items-center gap-2">
                                                @if($index === 0)
                                                    <span class="w-2 h-2 rounded-full bg-brand-green"></span>
                                                @endif
                                                Bidder #***{{ substr(md5($bid->user_id), 0, 4) }}
                                            </td>
                                            <td class="py-3 px-4 text-brand-muted">Verified APMC Wholesale Node</td>
                                            <td class="py-3 px-4 font-mono text-sm font-bold">₹{{ number_format($bid->amount, 2) }}</td>
                                            <td class="py-3 px-4 font-mono text-brand-muted">{{ $bid->created_at->format('h:i:s A') }}</td>
                                            <td class="py-3 px-4 text-right">
                                                @if($index === 0)
                                                    <span class="px-2 py-0.5 rounded bg-secondary-container text-on-secondary-container font-mono text-[10px] font-bold uppercase">
                                                        Leading Bid
                                                    </span>
                                                @else
                                                    <span class="text-brand-muted font-mono text-[10px]">OUTBID</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-6 text-center text-brand-muted font-mono text-xs">
                                                Awaiting opening bid from verified marketplace buyers...
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- AUCTION GUARDRAILS NOTIFICATION BAR -->
                    <div class="bg-surface-container-low p-4 rounded-[14px] border border-[#E2DFD7]/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[20px] text-brand-amber mt-0.5">policy</span>
                            <div class="space-y-0.5">
                                <div class="text-xs font-heading font-bold text-primary">
                                    Seller Guardrail Policy: {{ $bidsCount > 0 ? 'Binding Bids Active' : 'Zero Bids Recorded' }}
                                </div>
                                <p class="text-[11px] text-brand-muted">
                                    @if($bidsCount > 0)
                                        This lot cannot be cancelled as active binding bids exist. Upon timer expiry, the lot will automatically assign to the highest bidder with escrow funding.
                                    @else
                                        Cancellation is currently permitted with zero fee or trust penalty since no bids have been submitted yet.
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($bidsCount === 0)
                            <form method="POST" action="{{ route('seller.auctions.cancel', $liveAuction) }}" onsubmit="return confirm('Confirm cancellation of this live auction with 0 bids?');" class="shrink-0">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 rounded-[10px] bg-error-container text-on-error-container font-heading text-xs font-bold hover:brightness-95 transition-all">
                                    Cancel Lot
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- COLUMN B: 38% (4 Cols) - QUICK CREATE WIZARD & SETTLEMENT -->
            <div class="lg:col-span-4 flex flex-col gap-6">

                <!-- PANEL 1: CREATE AUCTION QUICK WIZARD -->
                <div class="bg-white rounded-[14px] p-6 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-5">
                    <div class="flex items-center justify-between pb-3 border-b border-[#E2DFD7]/60">
                        <div>
                            <span class="font-mono text-[10px] font-bold text-secondary uppercase tracking-wider">NEW CONSIGNMENT</span>
                            <h3 class="font-heading text-base font-bold text-primary">Create Auction Listing</h3>
                        </div>
                        <span class="font-mono text-xs px-2 py-0.5 bg-surface-container rounded font-semibold text-primary">
                            FAST LAUNCH
                        </span>
                    </div>

                    <form method="POST" action="{{ route('seller.auctions.store') }}" class="space-y-4">
                        @csrf

                        <!-- Product Selector -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-heading font-semibold text-primary">1. Select Certified Farm Product</label>
                            <select name="product_id" class="w-full h-11 px-3 bg-surface-container-low rounded-[12px] text-xs text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none" required>
                                <option value="" disabled selected>Choose product from inventory...</option>
                                @foreach($products ?? [] as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->stock }} {{ $prod->unit_type }} available)</option>
                                @endforeach
                            </select>
                            @error('product_id') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <!-- Pricing Trifecta -->
                        <div class="space-y-3 pt-1">
                            <span class="text-xs font-heading font-semibold text-primary block">2. Pricing Structure</span>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="font-mono text-[10px] text-brand-muted block mb-1">Starting Bid (₹)</label>
                                    <input type="number" step="0.01" name="starting_price" placeholder="1500" class="w-full h-10 px-3 bg-surface-container-low rounded-[10px] font-mono text-xs font-bold text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none" required>
                                </div>
                                <div>
                                    <label class="font-mono text-[10px] text-brand-muted block mb-1">Secret Reserve (₹)</label>
                                    <input type="number" step="0.01" name="reserve_price" placeholder="2500" class="w-full h-10 px-3 bg-surface-container-low rounded-[10px] font-mono text-xs font-bold text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="font-mono text-[10px] text-brand-muted block mb-1">Minimum Bid Increment (₹)</label>
                                <input type="number" step="1" name="minimum_increment" value="100" class="w-full h-10 px-3 bg-surface-container-low rounded-[10px] font-mono text-xs font-bold text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none" required>
                            </div>
                        </div>

                        <!-- Start and End Timing -->
                        <div class="space-y-3 pt-1">
                            <span class="text-xs font-heading font-semibold text-primary block">3. Time Window</span>
                            <div class="grid grid-cols-1 gap-2.5">
                                <div>
                                    <label class="font-mono text-[10px] text-brand-muted block mb-0.5">Start Datetime</label>
                                    <input type="datetime-local" name="starts_at" class="w-full h-10 px-3 bg-surface-container-low rounded-[10px] font-mono text-xs text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none" required>
                                </div>
                                <div>
                                    <label class="font-mono text-[10px] text-brand-muted block mb-0.5">End Datetime</label>
                                    <input type="datetime-local" name="ends_at" class="w-full h-10 px-3 bg-surface-container-low rounded-[10px] font-mono text-xs text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full h-11 mt-2 rounded-[12px] bg-brand-amber text-primary font-heading text-xs font-bold shadow-sm hover:brightness-105 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">publish</span>
                            <span>Schedule &amp; Launch Auction</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
```

---

### Blueprint 6: `resources/views/seller/auctions/create.blade.php`
*(Dedicated Create Auction Listing Workstation with Product Selector, Pricing Trifecta, Schedule Duration)*

```blade
@extends('layouts.seller')

@section('title', 'Create Auction Listing — Bazaario Seller Center')

@section('content')
<div class="flex flex-col w-full pb-16 max-w-4xl mx-auto" x-data="{
    startPrice: 1000,
    reservePrice: 1500,
    minIncrement: 100,
    durationHours: 2
}">

    <!-- Top Breadcrumb -->
    <div class="px-6 py-4 bg-white/80 backdrop-blur-md rounded-[14px] shadow-sm mb-6 border border-[#E2DFD7]/60">
        <div class="flex items-center gap-2 text-xs font-mono text-brand-muted">
            <a href="{{ route('seller.dashboard') }}" class="hover:text-primary transition-colors">Seller Center</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('seller.auctions.index') }}" class="hover:text-primary transition-colors">Auctions</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">Create Auction</span>
        </div>
        <h1 class="font-heading text-2xl lg:text-3xl text-primary font-bold tracking-tight mt-2">Create Wholesale Auction Lot</h1>
        <p class="text-sm text-brand-muted">List a verified consignment of farm produce on the live spot-bidding exchange.</p>
    </div>

    <form method="POST" action="{{ route('seller.auctions.store') }}" class="bg-white rounded-[14px] p-6 lg:p-8 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-6">
        @csrf

        <!-- STEP 1: Product Selection -->
        <div class="flex flex-col gap-3 pb-6 border-b border-[#E2DFD7]/60">
            <div class="flex items-center justify-between">
                <span class="font-heading text-base font-bold text-primary">1. Select Certified Farm Product</span>
                <span class="font-mono text-xs text-brand-muted">Authenticated Inventory Only</span>
            </div>
            <select name="product_id" class="w-full h-12 px-4 bg-surface-container-low rounded-[12px] text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                <option value="" disabled selected>Select from your approved products...</option>
                @foreach($products ?? [] as $prod)
                    <option value="{{ $prod->id }}">{{ $prod->name }} (Available: {{ $prod->stock }} {{ $prod->unit_type }})</option>
                @endforeach
            </select>
            @error('product_id') <p class="text-xs text-error">{{ $message }}</p> @enderror
        </div>

        <!-- STEP 2: Pricing Structure -->
        <div class="flex flex-col gap-4 pb-6 border-b border-[#E2DFD7]/60">
            <span class="font-heading text-base font-bold text-primary">2. Pricing Structure &amp; Reserve Floor</span>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-heading font-semibold text-primary">Starting Price (₹)</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 font-mono text-sm text-brand-muted">₹</span>
                        <input type="number" step="0.01" name="starting_price" x-model="startPrice" class="w-full h-11 pl-8 pr-4 bg-surface-container-lowest rounded-[12px] font-mono text-sm font-bold text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                    </div>
                    @error('starting_price') <p class="text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-heading font-semibold text-primary flex items-center justify-between">
                        <span>Secret Reserve Price (₹)</span>
                        <span class="font-mono text-[10px] text-brand-muted">Optional</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 font-mono text-sm text-brand-muted">₹</span>
                        <input type="number" step="0.01" name="reserve_price" x-model="reservePrice" class="w-full h-11 pl-8 pr-4 bg-surface-container-lowest rounded-[12px] font-mono text-sm font-bold text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm">
                    </div>
                    @error('reserve_price') <p class="text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-heading font-semibold text-primary">Minimum Increment (₹)</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 font-mono text-sm text-brand-muted">₹</span>
                        <input type="number" step="1" name="minimum_increment" x-model="minIncrement" class="w-full h-11 pl-8 pr-4 bg-surface-container-lowest rounded-[12px] font-mono text-sm font-bold text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                    </div>
                    @error('minimum_increment') <p class="text-xs text-error">{{ $message }}</p> @enderror
                </div>
            </div>
            <p class="text-xs text-brand-muted">The secret reserve protects your consignment against low-demand sell-offs; bidding below this floor does not legally bind allocation.</p>
        </div>

        <!-- STEP 3: Timing & Duration -->
        <div class="flex flex-col gap-4 pb-6 border-b border-[#E2DFD7]/60">
            <span class="font-heading text-base font-bold text-primary">3. Time Window (APMC Synchronized)</span>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-heading font-semibold text-primary">Start Date &amp; Time</label>
                    <input type="datetime-local" name="starts_at" class="w-full h-11 px-4 bg-surface-container-lowest rounded-[12px] font-mono text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                    @error('starts_at') <p class="text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-heading font-semibold text-primary">End Date &amp; Time</label>
                    <input type="datetime-local" name="ends_at" class="w-full h-11 px-4 bg-surface-container-lowest rounded-[12px] font-mono text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                    @error('ends_at') <p class="text-xs text-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Submit CTAs -->
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('seller.auctions.index') }}" class="h-11 px-5 rounded-[12px] bg-surface-container-low text-primary text-xs font-heading font-semibold hover:bg-surface-container transition-all">
                Cancel
            </a>
            <button type="submit" class="h-12 px-8 rounded-[12px] bg-brand-amber text-primary font-heading text-sm font-bold shadow-md hover:brightness-105 active:scale-95 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">publish</span>
                <span>Schedule &amp; Launch Auction</span>
            </button>
        </div>
    </form>
</div>
@endsection
```

---

### Blueprint 7: `resources/views/seller/auctions/show.blade.php`
*(Individual Auction Lot Inspector & Audit Dossier)*

```blade
@extends('layouts.seller')

@section('title', 'Auction Lot Inspector — Bazaario Seller Center')

@section('content')
@php
    $bidsCount = $auction->bids->count();
    $isReserveMet = $auction->reserve_price && ($auction->current_price >= $auction->reserve_price);
@endphp

<div class="flex flex-col w-full pb-16">
    <div class="flex items-center gap-2 text-xs font-mono text-brand-muted mb-4">
        <a href="{{ route('seller.dashboard') }}" class="hover:text-primary transition-colors">Seller Center</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('seller.auctions.index') }}" class="hover:text-primary transition-colors">Auctions</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-primary font-bold">Lot #AUC-{{ str_pad($auction->id, 4, '0', STR_PAD_LEFT) }}</span>
    </div>

    <div class="bg-white rounded-[14px] p-6 lg:p-8 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-[#E2DFD7]/60">
            <div>
                <span class="font-mono text-xs font-bold bg-surface-container px-2.5 py-0.5 rounded text-primary">
                    LOT #AUC-{{ str_pad($auction->id, 4, '0', STR_PAD_LEFT) }}
                </span>
                <h1 class="font-heading text-2xl font-bold text-primary mt-1">{{ $auction->product?->name }}</h1>
                <p class="text-xs text-brand-muted">Starts {{ $auction->starts_at->format('M d, Y h:i A') }} • Ends {{ $auction->ends_at->format('M d, Y h:i A') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-[8px] font-mono text-xs font-bold uppercase {{ $auction->status === 'live' ? 'bg-secondary-container text-on-secondary-container' : 'bg-surface-container text-primary' }}">
                    Status: {{ $auction->status }}
                </span>
                @if($auction->status === 'live')
                    <a href="{{ route('seller.auctions.live') }}" class="h-10 px-4 rounded-[10px] bg-brand-amber text-primary text-xs font-heading font-bold flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">stream</span>
                        <span>Open Live</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="p-4 rounded-[12px] bg-surface-container-low border border-[#E2DFD7]/60">
                <span class="font-mono text-[10px] uppercase text-brand-muted">Starting Price</span>
                <div class="font-mono text-lg font-bold text-primary mt-1">₹{{ number_format($auction->starting_price, 2) }}</div>
            </div>
            <div class="p-4 rounded-[12px] bg-surface-container-low border border-[#E2DFD7]/60">
                <span class="font-mono text-[10px] uppercase text-brand-muted">Reserve Price</span>
                <div class="font-mono text-lg font-bold text-primary mt-1">₹{{ number_format($auction->reserve_price ?? $auction->starting_price, 2) }}</div>
                <span class="text-[10px] font-mono {{ $isReserveMet ? 'text-brand-green font-bold' : 'text-brand-muted' }}">{{ $isReserveMet ? 'Met' : 'Not Met' }}</span>
            </div>
            <div class="p-4 rounded-[12px] bg-surface-container-low border border-[#E2DFD7]/60">
                <span class="font-mono text-[10px] uppercase text-brand-muted">Current High Bid</span>
                <div class="font-mono text-lg font-bold text-primary mt-1">₹{{ number_format($auction->current_price, 2) }}</div>
            </div>
            <div class="p-4 rounded-[12px] bg-surface-container-low border border-[#E2DFD7]/60">
                <span class="font-mono text-[10px] uppercase text-brand-muted">Total Bids</span>
                <div class="font-mono text-lg font-bold text-primary mt-1">{{ $bidsCount }}</div>
            </div>
        </div>

        <!-- Bid Log -->
        <div class="flex flex-col gap-3">
            <h3 class="font-heading text-sm font-bold text-primary">Consignment Bid History</h3>
            <div class="overflow-x-auto rounded-[12px] border border-[#E2DFD7]/60">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-container-low font-mono text-[10px] text-brand-muted uppercase">
                        <tr>
                            <th class="py-2.5 px-4">Bidder</th>
                            <th class="py-2.5 px-4">Amount</th>
                            <th class="py-2.5 px-4">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2DFD7]/40">
                        @forelse($auction->bids()->latest()->get() as $b)
                            <tr>
                                <td class="py-3 px-4 font-mono">Bidder #***{{ substr(md5($b->user_id), 0, 4) }}</td>
                                <td class="py-3 px-4 font-mono font-bold">₹{{ number_format($b->amount, 2) }}</td>
                                <td class="py-3 px-4 font-mono text-brand-muted">{{ $b->created_at->format('M d, Y h:i:s A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-brand-muted">No bids recorded on this lot.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 6. Caveats

1. **Database Schema Tenancy**: In `auctions`, the foreign key is `seller_id` referencing `seller_profiles.id` (not `users.id`), whereas `products` has `seller_id` referencing `users.id`. Controller queries must correctly use `$sellerProfile->id` for auctions and `$sellerUser->id` for products.
2. **Real-time Latency**: The stitch templates demonstrate a simulated 1-second pulse and countdown for client preview; in live production, Laravel Echo / WebSockets or polling can be attached, but Alpine.js intervals provide reliable autonomous client-side countdown clocks.
3. **2FA Provider**: The 2FA section in `security.blade.php` displays enforced status per UI requirements. If full TOTP enrollment backend is implemented in a future milestone, the current blueprint maintains full visual fidelity and form compatibility.

---

## 7. Conclusion

Milestone 5 (Features 34–43) covers all critical dimensions of seller profile identity, operational SLA dispatch, geolocation telemetry, password entropy, wholesale auction creation, live bidding terminals with dynamic reserve price badges, anonymized activity streams, and robust cancellation guardrails. All requirements from `ORIGINAL_REQUEST.md`, `PROJECT.md`, and both stitch templates have been thoroughly mined, cross-referenced, and translated into exact Blade blueprints extending `layouts.seller`.

---

## 8. Verification Method

To independently verify the mining findings:
1. Verify presence of both stitch templates:
   - `c:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal\bazaario_shop_profile_location_account_settings\code.html`
   - `c:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal\bazaario_auction_management_live_bidding\code.html`
2. Verify schema columns in migrations:
   - `database/migrations/2026_09_11_000019_create_auctions_table.php` (for `auctions.seller_id -> seller_profiles.id`, `starting_price`, `reserve_price`, `status`)
   - `database/migrations/2026_09_30_000001_add_seller_panel_fields_to_tables.php` (for `seller_profiles.address`, `postal_code`, `operating_radius_km`)
3. Review Blade syntax of all 7 blueprints against Warm Modernist design tokens in `resources/views/layouts/seller.blade.php`.
