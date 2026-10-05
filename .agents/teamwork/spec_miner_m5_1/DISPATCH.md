## 2026-09-30T10:50:04Z
You are spec_miner_m5_1 (TypeName: teamwork_preview_spec_miner).
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m5_1

MANDATORY FIRST STEP:
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically header ## 2026-09-30T04:46:52Z) and c:\xampp\htdocs\bazaario\PROJECT.md.

Task:
Mine the following stitch templates for Milestone 5 (Profile & Auction Management — Features 34–43):
1. `c:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal\bazaario_shop_profile_location_account_settings\code.html`
2. `c:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal\bazaario_auction_management_live_bidding\code.html`

Extract and document in detail:
1. Shop Profile & Settings:
   - Header, cover photo upload, store avatar/logo, store name, slug, description, contact details.
   - Operating harvest days & dispatch SLA schedule (checkboxes for Monday-Sunday, dispatch cutoff times).
   - Location Telemetry & Geofence settings: Farm origin address, Lat/Lng coordinate fields, GPS autofill button, elevation, geofence radius selector / map visualizer.
   - Account security & password update form: current password confirmation, new password, 4-tier complexity meter (`Weak`, `Medium`, `Strong`, `Enterprise`).
2. Wholesale Auction Management & Live Bidding Terminal:
   - Create Auction listing form: product selector, starting bid, secret reserve price, minimum bid increment, start date/time, end date/time.
   - Live bidding terminal: active auction lot hero tile, real-time countdown clock, reserve met indicator badge (`RESERVE MET` / `RESERVE NOT MET`), highest bid tile, anonymized live bid activity stream (bidder handle e.g. `Bidder #***42`, bid amount, timestamp, status).
   - Auction cancellation guard: cancellation CTA with clear policy notes (permitted with 0 bids, strictly blocked once bids exist).
   - Master auction registry table: list of seller auctions with tabs (`All`, `Scheduled`, `Live`, `Ended`, `Cancelled`), lot title, product, current bid, reserve status, end time, action buttons (`View Live Terminal`, `Cancel Auction`).
3. Exact Blade/Tailwind markup blueprints extending `layouts.seller` to be used in `resources/views/seller/account/` and `resources/views/seller/auctions/`.

Deliver your findings in `c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m5_1\handoff.md` and send a message back to parent.
