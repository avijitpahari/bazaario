## 2026-09-30T10:50:05Z
You are explorer_m5_tests_1 (TypeName: teamwork_preview_explorer).
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m5_tests_1

MANDATORY FIRST STEP:
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically header ## 2026-09-30T04:46:52Z) and c:\xampp\htdocs\bazaario\PROJECT.md.

Task:
Investigate test suite design and test scenarios for Milestone 5 (Profile & Auction Management — Features 34–43):
1. Inspect existing test helpers in `tests/Feature/Seller/SellerTestHelperTrait.php`.
2. Design a comprehensive test matrix for `tests/Feature/Seller/SellerAuctionAndProfileTest.php`:
   - Feature 34: Shop profile display and update (store name, bio, storefront banner, logo, business details).
   - Feature 35: Operating harvest days & dispatch SLA schedule updates.
   - Feature 36: Location telemetry: update Lat/Lng coordinates and geofence radius.
   - Feature 37: Account security: update password with current password verification (reject incorrect current password, validate complexity).
   - Feature 38: Live wholesale bidding terminal renders active auction, countdown timer, hero tiles.
   - Feature 38: Live wholesale bidding terminal renders active auction, countdown timer, hero tiles.
   - Feature 39: Reserve price met indicator badge: assert badge renders `RESERVE MET` when highest bid >= reserve, `RESERVE NOT MET` when highest bid < reserve.
   - Feature 40: Create auction listing form: create valid auction with product, starting price, reserve price, minimum increment, start & end timestamps; assert validation rules reject invalid dates/prices.
   - Feature 41: Anonymized live bid activity stream: assert bids rendered with masked bidder identity.
   - Feature 42: Auction cancellation guardrail: cancellation allowed when 0 bids exist; strictly blocked (403/error redirect) once bids exist or reserve met.
   - Feature 43: Master auctions registry table: assert listing all seller auctions across status tabs (`scheduled`, `live`, `ended`, `cancelled`).
   - Multi-tenancy isolation: assert Seller A cannot edit Seller B's profile or cancel/manage Seller B's auction (HTTP 403).
3. Draft sample test fixtures and methods.

Deliver your test matrix specification in `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m5_tests_1\handoff.md` and send a message back to parent.
