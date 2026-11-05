# Changes Made — Milestone 2: Live Auction Automation & Lifecycle Management (R2)

## 1. Created `app/Console/Commands/CloseExpiredAuctions.php`
- Implemented Artisan scheduled command `auctions:close-expired` with description: `Close expired live auctions, select winning bidders, and generate winning order/invoice records.`
- Executes atomic `DB::transaction` per expired auction with concurrency locking (`lockForUpdate()`) and idempotency guard (`status === 'live'`).
- Determines highest bid and checks whether reserve price is met (`!$auction->reserve_price || $highestBid->amount >= $auction->reserve_price`).
- Updates auction to `status = 'ended'` and assigns `winner_id` if reserve is met; otherwise sets `winner_id = null`.
- When reserve is met:
  - Creates `Order` with `order_type = 'auction'`, `order_status = 'processing'`, `payment_status = 'pending'`, and fallback shipping address details.
  - Creates `SellerOrder` linked to seller and parent order with calculated commission and payout.
  - Creates `OrderItem` with unit and line totals matching the winning bid.
  - Creates `Invoice` with subtotal and total matching the winning bid.
  - Emits in-app `Notification` of type `'auction_won'` to the winning user.
- Emits console feedback: `Closed X expired auction(s). Y winning order(s) generated.`

## 2. Updated `routes/console.php`
- Registered `Schedule::command(CloseExpiredAuctions::class)->everyMinute()` to run auction closure automation every minute.

## 3. Updated `app/Http/Controllers/User/AuctionController.php`
- In `placeBid()`: identifies current leading bid (`$currentLeader`) inside the database transaction before creating the new `AuctionBid`.
- When `$currentLeader` exists and belongs to a different user (`$currentLeader->user_id !== $user->id`), creates an in-app `Notification` record in `notifications` table with `type => 'outbid'`, informing the previous leader of the new higher bid amount and lot details.
- When a user increases their own leading bid, no redundant outbid notification is sent.

## 4. Updated Client-Side Countdown Timers in Blade Views
- `resources/views/user/account/auction-show.blade.php`:
  - Updated `data-countdown` container attribute to format end date using UTC-safe ISO 8601 string: `$auction->ends_at->toIso8601String()`.
  - Corrected countdown calculation math for days, hours, minutes, and seconds:
    `const days = Math.floor(distance / (1000 * 60 * 60 * 24));`
    `const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));`
    `const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));`
    `const seconds = Math.floor((distance % (1000 * 60)) / 1000);`
  - Formatted timer display to show `${days}d ${hStr}:${mStr}:${sStr}` when days > 0, and `${hStr}:${mStr}:${sStr}` otherwise.
  - Switches display to `'Auction Ended'` when `distance <= 0`.
- Applied identical fixes to `resources/views/user/account/auctions.blade.php` and `resources/views/user/account/bids.blade.php`.

## 5. Created Automated Feature Test Suite `tests/Feature/Auction/AuctionLifecycleAutomationTest.php`
- Test 1: `test_command_transitions_live_auction_past_ends_at_to_ended`
- Test 2: `test_active_auction_with_future_ends_at_is_not_closed`
- Test 3: `test_highest_bidder_meeting_reserve_price_is_assigned_as_winner_id`
- Test 4: `test_winning_auction_creates_order_seller_order_order_item_and_invoice`
- Test 5: `test_expired_auction_failing_reserve_price_sets_winner_null_and_creates_no_order`
- Test 6: `test_expired_auction_with_zero_bids_transitions_to_ended_with_winner_null`
- Test 7: `test_outbid_notification_created_in_notifications_table_when_user_is_outbid`
- Test 8: `test_no_outbid_notification_when_user_increases_own_leading_bid`
- Test 9: `test_auction_show_view_renders_countdown_timer_container_with_valid_timestamp`
- Test 10: `test_idempotency_running_command_multiple_times_does_not_duplicate_records`
