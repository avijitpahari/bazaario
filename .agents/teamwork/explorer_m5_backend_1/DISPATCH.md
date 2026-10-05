## 2026-09-30T10:50:04Z
You are explorer_m5_backend_1 (TypeName: teamwork_preview_explorer).
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m5_backend_1

MANDATORY FIRST STEP:
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically header ## 2026-09-30T04:46:52Z) and c:\xampp\htdocs\bazaario\PROJECT.md.

Task:
Investigate backend architecture, database schemas, models, controllers, and routes for Milestone 5 (Profile & Auction Management — Features 34–43):
1. Routes in `routes/web.php`:
   - Inspect existing routes for `/seller/account/profile`, `/seller/account/location`, `/seller/account/security`, `/seller/auctions`.
2. Inspect models:
   - `app/Models/SellerProfile.php`: fillables, relationships (`user()`, `auctions()`), lat/lng/address/radius fields.
   - `app/Models/Auction.php`: NOTE Interface Contract from PROJECT.md: `auctions.seller_id` references `seller_profiles.id` (foreign key to `seller_profiles`)! Check relationships (`sellerProfile()`, `product()`, `bids()`), status enum/strings (`scheduled`, `live`, `ended`, `cancelled`), scopes, helper methods (`isLive()`, `isReserveMet()`, `canBeCancelled()`).
   - `app/Models/AuctionBid.php`: relationship to `Auction`, bidder info, bid amount.
   - `app/Models/User.php`: password update and current password validation.
3. Database migrations in `database/migrations/`:
   - Check `auctions` table columns, foreign keys, timestamps.
   - Check `seller_profiles` table columns.
   - Check if any schema migration is required.
4. Controllers:
   - Check if `SellerProfileController.php` and `SellerAuctionController.php` exist in `app/Http/Controllers/Seller/`.
   - Define exact methods needed:
     * `SellerProfileController`: `profile`, `updateProfile`, `location`, `updateLocation`, `security`, `updatePassword`.
     * `SellerAuctionController`: `index`, `create`, `store`, `show`, `liveTerminal`, `cancel`.
5. Cancellation guardrail policy:
   - Cancellation must be permitted ONLY when `AuctionBid::where('auction_id', $auction->id)->count() === 0`.
   - If bids exist, cancellation must be rejected with 403 or error redirect.
6. Reserve price met indicator:
   - If highest bid >= reserve_price, evaluate as reserve met.

Deliver your findings and implementation roadmap in `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m5_backend_1\handoff.md` and send a message back to parent.
