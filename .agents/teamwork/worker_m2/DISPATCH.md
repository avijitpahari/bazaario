## 2026-10-12T06:03:14Z
You are Worker M2 tasked with implementing Milestone 2: Live Auction Automation & Lifecycle Management (R2) for Bazaario.
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m2
Project root: c:\xampp\htdocs\bazaario
Original Request file: c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (read timestamp ## 2026-10-12T05:17:47Z).
Project Specification: c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_10\PROJECT.md
Explorer Survey Report: c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_r2\report.md
Explorer Survey Handoff: c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_r2\handoff.md

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

File Ownership:
You own exclusively:
- `app/Console/Commands/CloseExpiredAuctions.php`
- `routes/console.php`
- `app/Http/Controllers/User/AuctionController.php` (outbid notification logic in `placeBid`)
- `resources/views/user/account/auction-show.blade.php`, `auctions.blade.php`, `bids.blade.php` (client-side countdown timer)
- `tests/Feature/Auction/AuctionLifecycleAutomationTest.php`

Implementation Tasks:
1. Create Artisan Scheduled Command `app/Console/Commands/CloseExpiredAuctions.php`:
   - Signature: `auctions:close-expired`
   - Description: `Close expired live auctions, select winning bidders, and generate winning order/invoice records.`
   - Logic:
     - Find live auctions where `ends_at <= now()`.
     - Process each auction inside an atomic `DB::transaction`:
       - Query `Auction::where('id', $auction->id)->lockForUpdate()->first()`.
       - Check if status is still `'live'` (idempotency guard).
       - Find highest bid: `AuctionBid::where('auction_id', $auction->id)->orderByDesc('amount')->first()`.
       - Evaluate reserve price: `$hasMetReserve = $highestBid && (!$auction->reserve_price || (float)$highestBid->amount >= (float)$auction->reserve_price);`
       - Update auction status: `$auction->update(['status' => 'ended', 'winner_id' => $hasMetReserve ? $highestBid->user_id : null, 'current_price' => $highestBid ? $highestBid->amount : $auction->current_price]);`
       - When reserve is met:
         - Create `Order`: `user_id => $highestBid->user_id`, `order_number => 'ORD-AUC-' . strtoupper(Str::random(8))`, `order_type => 'auction'`, `total_amount => $highestBid->amount`, `subtotal => $highestBid->amount`, `order_status => 'processing'`, `payment_status => 'pending'`.
         - Create `SellerOrder`: `order_id => $order->id`, `seller_id => $auction->seller_profile?->user_id ?? $auction->seller_id`, `seller_order_number => 'SO-AUC-' . strtoupper(Str::random(8))`, `subtotal => $highestBid->amount`, `status => 'processing'`.
         - Create `OrderItem`: `order_id => $order->id`, `product_id => $auction->product_id`, `price => $highestBid->amount`, `quantity => 1`, `subtotal => $highestBid->amount`.
         - Create `Invoice`: `order_id => $order->id`, `invoice_number => 'INV-AUC-' . strtoupper(Str::random(8))`, `total_amount => $highestBid->amount`, `status => 'pending'` (or `'unpaid'`), `issued_date => now()`, `due_date => now()->addDays(3)`.
         - Emit in-app notification to winner in `notifications` table (`type => 'auction_won'`).
       - If reserve not met or 0 bids: `winner_id` is set to null, auction ended.
   - Outputs informative console feedback: `Closed X expired auction(s). Y winning order(s) generated.`
2. Register Scheduled Command in `routes/console.php`:
   - Add: `Schedule::command(\App\Console\Commands\CloseExpiredAuctions::class)->everyMinute();`
3. Outbid Notifications in `app/Http/Controllers/User/AuctionController.php`:
   - In `placeBid()` (inside the transaction before creating new `AuctionBid`):
     - Identify current leading bid: `$currentLeader = AuctionBid::where('auction_id', $lockedAuction->id)->orderByDesc('amount')->first();`
     - If `$currentLeader && $currentLeader->user_id !== $user->id`:
       - Create record in `notifications` table:
         `user_id => $currentLeader->user_id`, `type => 'outbid'`, `title => 'You have been outbid!'`, `message => 'A higher bid of ₹' . number_format($bidAmount, 2) . ' was placed on ' . ($lockedAuction->product->name ?? 'the auction lot')`, `link => route('auctions.show', $lockedAuction->id)`.
4. Fix Client-Side Live Countdown Timer in Blade Views:
   - In `resources/views/user/account/auction-show.blade.php`:
     - Fix math bug:
       `const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));`
       `const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));`
       `const seconds = Math.floor((distance % (1000 * 60)) / 1000);`
     - Format `data-countdown` with ISO 8601 UTC string: `$auction->ends_at->toIso8601String()`.
     - Ensure timer dynamically updates every 1 second, showing days (if > 0), hours, minutes, seconds, and switches to "Auction Ended" when distance <= 0.
   - Also apply this fix to `resources/views/user/account/auctions.blade.php` and `resources/views/user/account/bids.blade.php`.
5. Automated Tests:
   - Create `tests/Feature/Auction/AuctionLifecycleAutomationTest.php`:
     - Test 1: Command transitions live auction past `ends_at` to `ended`.
     - Test 2: Active auction with `ends_at > now()` is NOT closed by command.
     - Test 3: Highest bidder meeting reserve price is assigned as `winner_id`.
     - Test 4: Winning auction creates `Order` with `order_type = 'auction'`, `SellerOrder`, `OrderItem`, and `Invoice`.
     - Test 5: Expired auction failing reserve price sets `winner_id = null` and creates no order.
     - Test 6: Expired auction with 0 bids transitions to `ended` with `winner_id = null`.
     - Test 7: Outbid notification is created in `notifications` table when a user is outbid.
     - Test 8: No outbid notification when user increases their own leading bid.
     - Test 9: Auction show view renders countdown timer container with valid end timestamp.
     - Test 10: Idempotency: Running command multiple times does not duplicate orders or invoices.
   - Run tests:
     `php artisan test tests/Feature/Auction/AuctionLifecycleAutomationTest.php`
     `php artisan test tests/Feature/Seller/Milestone5AuctionEngineChallengeTest.php`
     `php artisan test tests/Feature/Seller/SellerAuctionAndProfileTest.php`
   - Ensure all pass with 0 failures.

Deliverables:
- Write changes to `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m2\changes.md`.
- Write handoff report to `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m2\handoff.md`.
- Send completion message to parent upon finishing.
