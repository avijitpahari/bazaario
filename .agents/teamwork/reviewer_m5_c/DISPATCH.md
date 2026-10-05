## 2026-09-30T11:07:11Z
You are reviewer_m5_c (TypeName: teamwork_preview_reviewer).
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\reviewer_m5_c

MANDATORY FIRST STEP:
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically header ## 2026-09-30T04:46:52Z) and c:\xampp\htdocs\bazaario\PROJECT.md.
Also read the implementation handoff report:
`c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m5_impl\handoff.md`

Task:
Perform thorough code review, quality assessment, and interface conformance review for Milestone 5 (Profile & Auction Management — Features 34–43):
1. Review `app/Http/Controllers/Seller/SellerProfileController.php` and `SellerAuctionController.php`.
   - Verify Interface Contract: `auctions.seller_id` references `seller_profiles.id`.
   - Verify multi-tenancy isolation (`where('seller_id', $sellerProfile->id)`, 403 on cross-tenant access/cancel).
   - Verify auction creation validation (product ownership, starting price > 0, reserve >= starting, min increment > 0, ends_at > starts_at).
   - Verify cancellation guardrail (permitted with 0 bids, strictly blocked once bids exist).
   - Verify password update logic (Hash::check on current password, complexity validation).
2. Review Blade views in `resources/views/seller/account/` (`profile.blade.php`, `location.blade.php`, `security.blade.php`) and `resources/views/seller/auctions/` (`index.blade.php`, `create.blade.php`, `live.blade.php`, `show.blade.php`).
3. Run verification commands:
   - `php -l` on modified files
   - `php artisan view:clear` and `php artisan view:cache`
   - `php artisan test tests/Feature/Seller/SellerAuctionAndProfileTest.php`
4. Deliver your explicit verdict: **APPROVE** or **REQUEST_CHANGES** in `c:\xampp\htdocs\bazaario\.agents\teamwork\reviewer_m5_c\handoff.md` and send a message back to parent.
