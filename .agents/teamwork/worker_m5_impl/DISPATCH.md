## 2026-09-30T10:55:30Z
You are worker_m5_impl (TypeName: teamwork_preview_worker).
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m5_impl

MANDATORY FIRST STEP:
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically header ## 2026-09-30T04:46:52Z) and c:\xampp\htdocs\bazaario\PROJECT.md.
Also read the detailed handoff reports prepared by the 3 exploration agents:
1. `c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m5_1\handoff.md` (Stitch UI layout, profile tabs, auction live terminal, reserve indicator, styling tokens)
2. `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m5_backend_1\handoff.md` (Controllers, models, routes, foreign key contracts, cancellation guardrails)
3. `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m5_tests_1\handoff.md` (Test matrix, test cases, assertions)

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Scope Boundaries & Exclusive Write Ownership:
You own exclusively:
- `app/Http/Controllers/Seller/SellerProfileController.php`
- `app/Http/Controllers/Seller/SellerAuctionController.php`
- `routes/web.php` (for seller profile/account and auction routes)
- `app/Models/SellerProfile.php`
- `app/Models/Auction.php`
- `resources/views/seller/account/profile.blade.php`
- `resources/views/seller/account/location.blade.php`
- `resources/views/seller/account/security.blade.php`
- `resources/views/seller/auctions/index.blade.php`
- `resources/views/seller/auctions/create.blade.php`
- `resources/views/seller/auctions/live.blade.php`
- `resources/views/seller/auctions/show.blade.php`
- `tests/Feature/Seller/SellerTestHelperTrait.php`
- `tests/Feature/Seller/SellerAuctionAndProfileTest.php`

Deliverables:
1. Implement `SellerProfileController.php`:
   - `profile`: renders `seller.account.profile` with shop profile data, cover banner, logo, business details, operating harvest days (MON-SUN), SLA windows.
   - `updateProfile`: validates and updates shop details, store name, description, operating days, and uploads banner/logo images.
   - `location`: renders `seller.account.location` with farm origin address, Lat/Lng coordinates, elevation, geofence radius.
   - `updateLocation`: validates and updates address, latitude, longitude, and geofence radius.
   - `security`: renders `seller.account.security` with password change form and 2FA status card.
   - `updatePassword`: validates current password (`Hash::check`), enforces password complexity rules, updates user password cleanly.
2. Implement `SellerAuctionController.php`:
   - Note Interface Contract: `Auction.seller_id` references `seller_profiles.id`! Retrieve seller's `SellerProfile` via `$sellerProfile = Auth::guard('seller')->user()->sellerProfile`.
   - `index`: queries auctions for this seller profile (`where('seller_id', $sellerProfile->id)`), supports status filter tabs (`all`, `scheduled`, `live`, `ended`, `cancelled`), renders `seller.auctions.index`.
   - `create`: lists seller's approved products for lot creation, renders `seller.auctions.create`.
   - `store`: validates product ownership, starting_price, reserve_price, min_increment, start_time, end_time (`after:start_time`), persists new `Auction` linked to `seller_profiles.id`.
   - `show`: lot details, bid history, renders `seller.auctions.show`.
   - `liveTerminal`: renders active live auction lot with countdown timer, highest bid tile, `RESERVE MET` / `RESERVE NOT MET` dynamic indicator badge, anonymized bid stream (`Bidder #***42`), quick cancellation CTA.
   - `cancel`: Cancellation Guardrail Policy: Check `AuctionBid::where('auction_id', $auction->id)->count() === 0`. If 0 bids, allow cancellation (`status = 'cancelled'`). If 1 or more bids exist, strictly block cancellation with HTTP 403 or error redirect.
3. Wire the routes in `routes/web.php` under `prefix('seller')` with `middleware(['auth:seller', 'seller'])`.
4. Update `SellerProfile.php` and `Auction.php` with necessary relationships and helper methods (`isReserveMet()`, `canBeCancelled()`, scopes).
5. Build all Blade views in `resources/views/seller/account/` and `resources/views/seller/auctions/` extending `layouts.seller` matching the Warm Modernist design tokens from Stitch templates.
6. Extend `SellerTestHelperTrait.php` with auction test fixtures and build `tests/Feature/Seller/SellerAuctionAndProfileTest.php` covering Features 34–43 and all edge cases.
7. Run verification:
   - `php -l` on all modified/created files
   - `php artisan view:clear` and `php artisan view:cache`
   - `php artisan test tests/Feature/Seller/SellerAuctionAndProfileTest.php`
   - Full regression test run: `php artisan test` (must pass 566+ existing tests with 0 failures and 0 regressions)
8. Compile full handoff report in `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m5_impl\handoff.md` and send completion message back to parent.
