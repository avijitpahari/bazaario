# Live Auction Automation & Lifecycle Management (R2) — Codebase Survey Report

**Target Requirement:** Requirement 2: Live Auction Automation & Lifecycle Management (R2)  
**Author:** Subagent Explorer Survey R2  
**Date:** October 12, 2026  
**Scope:** Read-Only Codebase Investigation & Technical Architecture Blueprint  
**Target Path:** `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_r2\report.md`  

---

## 1. Executive Summary

A comprehensive survey of the Bazaario codebase was performed to map the requirements for **R2: Live Auction Automation & Lifecycle Management**.

### Key Discoveries:
1. **Console Command & Scheduler Missing**:
   - `app/Console/` directory does not currently exist.
   - `routes/console.php` only contains the default `inspire` command.
   - No Artisan command or cron scheduler exists to automatically transition expired auctions, choose winners, or create orders/invoices.
2. **Auction Closure Logic Fragmented & Incomplete**:
   - `AdminDashboardController::endAuction($id)` exists for manual admin closure, but it only sets `status = 'ended'` and `winner_id`. It **does NOT** create `Order`, `SellerOrder`, `OrderItem`, or `Invoice` records, nor does it emit notifications.
   - No background automation currently closes live auctions where `ends_at <= now()`.
3. **Outbid Notification Missing in Bidding Flow**:
   - `App\Http\Controllers\User\AuctionController::placeBid` and `quickBid` accept new bids and update `current_price`, but **never emit an in-app notification** when a user is outbid.
   - The `notifications` table and `App\Models\Notification` model already exist and are used in checkout, but the bidding flow never creates an `outbid` notification record for previously leading bidders.
4. **Critical Bug in Customer Countdown Timer JavaScript**:
   - In `resources/views/user/account/auction-show.blade.php` (line 468), `resources/views/user/account/auctions.blade.php` (line 462), and `resources/views/user/account/bids.blade.php` (line 748), the countdown timer calculation has an identical mathematical bug:
     ```javascript
     const minutes = Math.floor((distance % (1000 * 60)) / 1000); // BUG: Identical to seconds formula!
     const seconds = Math.floor((distance % (1000 * 60)) / 1000);
     ```
     `minutes` and `seconds` evaluate to the exact same value! Minutes should be `Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))`. In addition, countdown timers discard days (`distance % (1000 * 60 * 60 * 24)`), showing 0-23 hours even when days remain.
5. **Database Models & Schema Fully Compatible**:
   - `auctions`, `bids`, `orders`, `seller_orders`, `order_items`, `invoices`, and `notifications` tables have all the columns needed. Notably, `orders.order_type` is an enum with `['cart', 'auction']`, which specifically accommodates auction winner order records.

---

## 2. Deep Dive: Investigation Tasks

### Task 1: Models, Schema & Migrations

| Model | Table | Key Columns & Notes | Existing Relations |
|---|---|---|---|
| `App\Models\Auction` | `auctions` | `id`, `product_id`, `seller_id` (references `seller_profiles.id`), `starting_price`, `reserve_price`, `current_price`, `minimum_increment`, `starts_at`, `ends_at`, `status` (`scheduled`, `live`, `ended`, `cancelled`), `winner_id` (references `users.id`). | `product`, `seller` (`SellerProfile`), `sellerProfile`, `winner` (`User`), `bids` (`AuctionBid`). |
| `App\Models\AuctionBid` | `bids` | `id`, `auction_id`, `user_id`, `amount`, `created_at`, `updated_at`. Table name is `bids`. | `auction` (`Auction`), `user` (`User`). |
| `App\Models\Order` | `orders` | `id`, `order_number` (unique), `user_id`, `coupon_id`, `order_type` (`cart`, `auction`), `subtotal`, `discount_amount`, `shipping_amount`, `total_amount`, `payment_method`, `payment_status`, `order_status`, delivery address fields, `notes`, `placed_at`. | `user`, `sellerOrders`, `items` (HasManyThrough), `invoice`, `payments`. |
| `App\Models\SellerOrder` | `seller_orders` | `id`, `order_id`, `seller_id` (references `users.id`), `seller_order_number`, `subtotal`, `shipping_amount`, `commission_rate`, `commission_amount`, `payout_amount`, `status` (`placed`, `processing`, ...), `tracking_number`. | `order`, `seller` (`User`), `items` (`OrderItem`), `payout`. |
| `App\Models\OrderItem` | `order_items` | `id`, `seller_order_id`, `product_id`, `product_name`, `product_image`, `sku`, `unit_price`, `quantity`, `total_price`. | `sellerOrder`, `product`. |
| `App\Models\Invoice` | `invoices` | `id`, `order_id` (unique foreign key), `invoice_number` (unique), `subtotal`, `discount`, `shipping_fee`, `tax`, `total`, `pdf_path`, `issued_at`. | `order`. |
| `App\Models\Notification` | `notifications` | `id`, `user_id`, `type`, `title`, `message`, `data` (JSON), `read_at`. | `user`. |
| `App\Models\SellerProfile` | `seller_profiles` | `id`, `user_id`, `shop_name`, `commission_rate`, `status`. `auctions.seller_id` maps here. | `user`, `auctions`. |

**Foreign Key Consistency Warning:**
- In `auctions`, `seller_id` references `seller_profiles.id`.
- In `seller_orders`, `seller_id` references `users.id` (the user account of the merchant).
- When creating a `SellerOrder` from a won auction, the seller's user ID should be resolved via:
  `$sellerUserId = $auction->sellerProfile?->user_id ?? $auction->product?->seller_id;`

---

### Task 2: Console Commands & Scheduling

1. **Current Directory State**:
   - `app/Console/` does not exist on disk.
   - `routes/console.php` contains only:
     ```php
     Artisan::command('inspire', function () {
         $this->comment(Inspiring::quote());
     })->purpose('Display an inspiring quote');
     ```
   - `bootstrap/app.php` sets up routing:
     ```php
     commands: __DIR__.'/../routes/console.php',
     ```

2. **Required Command Implementation**:
   - File to create: `app/Console/Commands/CloseExpiredAuctions.php`
   - Class name: `App\Console\Commands\CloseExpiredAuctions`
   - Signature: `auctions:close-expired`
   - Description: `Close expired live auctions, determine winning bids, create orders/invoices, and notify participants.`
   - Scheduling registration in `routes/console.php`:
     ```php
     use App\Console\Commands\CloseExpiredAuctions;
     use Illuminate\Support\Facades\Schedule;

     Schedule::command(CloseExpiredAuctions::class)->everyMinute();
     ```
   - In Laravel 11, `app/Console/Commands/` files can be registered automatically or via `Schedule::command(CloseExpiredAuctions::class)` / `withCommands([CloseExpiredAuctions::class])` in `bootstrap/app.php`. Registering both guarantees CLI discovery for `php artisan auctions:close-expired` and scheduled runner execution.

---

### Task 3: Bidding, Reserve Evaluation & Outbid Notification Engine

1. **Current Bidding Controller**:
   - Routes `/auctions/{auction}/bid` and `/auctions/{auction}/quick-bid` point to `App\Http\Controllers\User\AuctionController::placeBid` and `quickBid`.
   - `placeBid` performs concurrency locking:
     `$lockedAuction = Auction::where('id', $auction->id)->lockForUpdate()->firstOrFail();`
   - It validates `now() <= ends_at` and `amount >= current_price + minimum_increment`.
   - Anti-sniping: Extends `ends_at` by 2 minutes if `secondsLeft <= 120`.

2. **What Is Missing in Bidding**:
   - **Outbid Notification**: When User B places a higher bid, User A (who was previously leading) receives zero notifications.
   - Fix to implement in `placeBid`:
     ```php
     // Identify previous leading bid before recording the new bid
     $previousLeadingBid = AuctionBid::where('auction_id', $lockedAuction->id)
         ->orderByDesc('amount')
         ->first();

     // Create new bid
     AuctionBid::create([
         'auction_id' => $lockedAuction->id,
         'user_id'    => $user->id,
         'amount'     => $bidAmount,
     ]);
     $lockedAuction->current_price = $bidAmount;

     // Trigger outbid notification if previous leader was a different user
     if ($previousLeadingBid && (int) $previousLeadingBid->user_id !== (int) $user->id) {
         try {
             \App\Models\Notification::create([
                 'user_id' => $previousLeadingBid->user_id,
                 'type'    => 'outbid',
                 'title'   => 'You have been outbid!',
                 'message' => "You have been outbid on Lot #AUC-{$lockedAuction->id} ({$lockedAuction->product?->name}). Current leading bid is ₹" . number_format($bidAmount, 2),
                 'data'    => [
                     'auction_id' => $lockedAuction->id,
                     'product_id' => $lockedAuction->product_id,
                     'new_bid'    => $bidAmount,
                 ],
             ]);
         } catch (\Throwable $e) {
             \Illuminate\Support\Facades\Log::warning('Outbid notification failed: ' . $e->getMessage());
         }
     }
     ```

3. **Reserve Evaluation Rules**:
   - In `App\Models\Auction::isReserveMet()`:
     - If `$auction->reserve_price === null` or `<= 0`: Reserve is met.
     - If `$highestBid->amount >= $auction->reserve_price`: Reserve is met.
     - If `$highestBid->amount < $auction->reserve_price`: Reserve is NOT met.
     - If no bids exist: Reserve is NOT met.

---

### Task 4: Post-Auction Order & Invoice Generation

When an auction expires (`ends_at <= now()`) and is evaluated:

1. **Case A: Auction has a qualifying winning bid (meets or exceeds reserve)**:
   - Within an atomic `DB::transaction`:
     1. Mark `$auction->status = 'ended'`, `$auction->winner_id = $winningBid->user_id`, `$auction->current_price = $winningBid->amount`.
     2. Create `Order`:
        - `order_number`: `'ORD-AUC-' . $auction->id . '-' . date('Ymd') . '-' . strtoupper(Str::random(4))`
        - `user_id`: `$winner->id`
        - `order_type`: `'auction'`
        - `subtotal`: `$winningBid->amount`
        - `discount_amount`: `0.00`
        - `shipping_amount`: `0.00`
        - `total_amount`: `$winningBid->amount`
        - `payment_method`: `'cod'`
        - `payment_status`: `'pending'`
        - `order_status`: `'pending'`
        - `delivery_full_name`: `$address?->full_name ?? $winner->name`
        - `delivery_phone`: `$address?->phone ?? $winner->phone ?? '9999999999'`
        - `delivery_address_line_1`: `$address?->address_line_1 ?? 'Auction Lot Delivery Address'`
        - `delivery_city`: `$address?->city ?? 'Kolkata'`
        - `delivery_state`: `$address?->state ?? 'West Bengal'`
        - `delivery_country`: `$address?->country ?? 'India'`
        - `delivery_postal_code`: `$address?->postal_code ?? '700001'`
        - `notes`: `"Won auction lot #AUC-{$auction->id} for '{$auction->product?->name}'"`
        - `placed_at`: `now()`
     3. Create `SellerOrder`:
        - `order_id`: `$order->id`
        - `seller_id`: `$sellerUserId`
        - `seller_order_number`: `'SO-AUC-' . $auction->id . '-' . strtoupper(Str::random(4))`
        - `subtotal`: `$winningBid->amount`
        - `commission_rate`: `$commissionRate`
        - `commission_amount`: `round($winningBid->amount * ($commissionRate / 100), 2)`
        - `payout_amount`: `max(0, $winningBid->amount - $commissionAmount)`
        - `status`: `'placed'`
        - `tracking_number`: `'BZ-TRK-AUC-' . strtoupper(Str::random(6))`
     4. Create `OrderItem`:
        - `seller_order_id`: `$sellerOrder->id`
        - `product_id`: `$auction->product_id`
        - `product_name`: `$auction->product?->name ?? 'Auction Product'`
        - `product_image`: `$auction->product?->primaryImage?->image_path ?? $auction->product?->main_image_url`
        - `sku`: `$auction->product?->sku ?? ('AUC-LOT-' . $auction->id)`
        - `unit_price`: `$winningBid->amount`
        - `quantity`: 1
        - `total_price`: `$winningBid->amount`
     5. Create `Invoice`:
        - `order_id`: `$order->id`
        - `invoice_number`: `'INV-AUC-' . $order->id . '-' . date('Ymd') . '-' . strtoupper(Str::random(4))`
        - `subtotal`: `$winningBid->amount`
        - `discount`: `0.00`
        - `shipping_fee`: `0.00`
        - `tax`: `0.00`
        - `total`: `$winningBid->amount`
        - `issued_at`: `now()`
     6. Emit notifications:
        - Winner: Notification of auction victory and order creation.
        - Seller: Notification of successful auction clearance.

2. **Case B: No bids or highest bid below reserve**:
   - Within `DB::transaction`:
     1. Mark `$auction->status = 'ended'`, `$auction->winner_id = null`.
     2. Do NOT create `Order`, `SellerOrder`, `OrderItem`, or `Invoice`.
     3. Emit notification to seller explaining that the auction closed without meeting reserve or with 0 bids.

---

### Task 5: Auction Views & Client-Side Countdown Timers

#### 1. Auction Product Detail Page (`resources/views/user/account/auction-show.blade.php`)
- Route: `GET /auctions/{auction}` (`route('auctions.show')`).
- Controller: `App\Http\Controllers\User\AuctionController::show`.
- Existing features:
  - Lot media gallery, specifications, seller badge, starting floor, min increment.
  - HUD countdown bar: `data-countdown="{{ $auction->ends_at->toIso8601String() }}"`.
  - Leading/Outbid status banners:
    - `@if($isUserLeading)`: Displays "You are currently the highest bidder for this lot!"
    - `@elseif($isUserOutbid)`: Displays "You have been outbid! Raise your collateral to reclaim the lead."
  - 1-Click quick bidding presets and manual custom bid form.
- **Bug Identified**:
  - Line 468:
    ```javascript
    const minutes = Math.floor((distance % (1000 * 60)) / 1000);
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
    ```
    This causes minutes and seconds to render identically.
  - Fix:
    ```javascript
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    const hStr = String(hours).padStart(2, '0');
    const mStr = String(minutes).padStart(2, '0');
    const sStr = String(seconds).padStart(2, '0');

    display.innerText = days > 0 ? `${days}d ${hStr}:${mStr}:${sStr}` : `${hStr}:${mStr}:${sStr}`;
    ```
  - In addition, `$auction->ends_at->toIso8601String()` must be used instead of `isoFormat('YYYY-MM-DDTHH:mm:ss')` to ensure valid timezone offset parsing across all browser engines.

#### 2. Live Floor Listing Page (`resources/views/user/account/auctions.blade.php`)
- Route: `GET /auctions` (`route('auctions.index')`).
- Filter tabs: `All Floor`, `Ending Soon`, `Active Bids`, `Reserve Met`, `Outbid Alerts`.
- Contains `data-countdown` on each card and identical countdown timer script bug. Requires same fix.

#### 3. User Bids Page (`resources/views/user/account/bids.blade.php`)
- Route: `GET /bids` (`route('bids')`).
- Displays user's leading vs outbid positions, locked escrow summary, and countdowns.
- Contains identical countdown timer script bug. Requires same fix.

---

### Task 6: Existing Test Coverage & Proposed Test Plan

#### Existing Tests:
- `tests/Feature/Seller/Milestone5AuctionEngineChallengeTest.php`: 24 tests passing. Covers seller wholesale auction creation, seller cancellation guardrail, reserve met decimal calculations.
- `tests/Feature/Seller/SellerAuctionAndProfileTest.php`: 43 tests passing. Covers seller profile, auction creation, live terminal view.

#### New Test Suite to Create:
Create `tests/Feature/Auction/AuctionLifecycleAutomationTest.php` with 10 comprehensive tests:
1. `test_artisan_command_transitions_expired_auctions_to_ended()`:
   Verifies running `php artisan auctions:close-expired` transitions auctions where `ends_at <= now()` and `status = 'live'` to `ended`.
2. `test_artisan_command_does_not_close_active_unexpired_auctions()`:
   Verifies auctions where `ends_at > now()` remain `live`.
3. `test_artisan_command_assigns_highest_bid_as_winner_when_reserve_met()`:
   Verifies `winner_id` is assigned to highest bidder when bid exceeds or matches `reserve_price`.
4. `test_artisan_command_assigns_winner_when_reserve_price_is_null()`:
   Verifies highest bidder is winner when auction has null reserve price.
5. `test_artisan_command_leaves_winner_null_when_highest_bid_below_reserve()`:
   Verifies auction transitions to `ended` with `winner_id = null` and no orders/invoices created if reserve is not met.
6. `test_artisan_command_handles_auction_with_zero_bids_gracefully()`:
   Verifies auction transitions to `ended` with `winner_id = null` when zero bids exist.
7. `test_artisan_command_creates_order_order_item_and_invoice_for_winner()`:
   Verifies `orders` record (`order_type = 'auction'`), `seller_orders` record, `order_items` record, and `invoices` record are created upon winning lot.
8. `test_placing_higher_bid_generates_outbid_notification_for_previous_leader()`:
   Verifies User A receives an `outbid` in-app `Notification` record when User B submits a higher bid.
9. `test_auction_product_detail_page_renders_active_countdown_timer_attributes()`:
   Verifies `/auctions/{id}` renders valid `data-countdown`, `.timer-display`, and countdown script.
10. `test_auction_product_detail_page_renders_outbid_and_leading_status_indicators()`:
    Verifies user sees leading badge when leading and outbid alert when outbid.

---

## 3. Implementation Blueprint & Architecture Mapping

```
                                  [ Scheduler (everyMinute) ]
                                               │
                                               ▼
                              [ php artisan auctions:close-expired ]
                                               │
                                 ┌─────────────┴─────────────┐
                                 │ Query live & ends_at <= now │
                                 └─────────────┬─────────────┘
                                               │
                                      (DB::transaction)
                                               │
                       ┌───────────────────────┴───────────────────────┐
                       ▼                                               ▼
              [ Bids Meet Reserve ]                           [ No Bids / Reserve Not Met ]
                       │                                               │
         ┌─────────────┴─────────────┐                                 ▼
         │ status = 'ended'          │                         status = 'ended'
         │ winner_id = highestBidder │                         winner_id = null
         └─────────────┬─────────────┘                                 │
                       │                                               ▼
         ┌─────────────┴─────────────┐                         Notify Seller (No Sale)
         │ Create Order              │
         │  (order_type='auction')   │
         │ Create SellerOrder        │
         │ Create OrderItem          │
         │ Create Invoice            │
         └─────────────┬─────────────┘
                       │
         ┌─────────────┴─────────────┐
         │ Notify Winner & Seller    │
         └───────────────────────────┘
```

### Files to Create:
1. `app/Console/Commands/CloseExpiredAuctions.php`
2. `tests/Feature/Auction/AuctionLifecycleAutomationTest.php`

### Files to Modify:
1. `routes/console.php` (register command & schedule)
2. `app/Http/Controllers/User/AuctionController.php` (add outbid notification in `placeBid`)
3. `app/Http/Controllers/AuctionController.php` (sync outbid notification in `placeBid`)
4. `resources/views/user/account/auction-show.blade.php` (fix minutes bug in countdown JS, add days, ISO string)
5. `resources/views/user/account/auctions.blade.php` (fix minutes bug in countdown JS)
6. `resources/views/user/account/bids.blade.php` (fix minutes bug in countdown JS)

---

## 4. Verification & Validation Gate

1. **Artisan Command Execution**:
   ```bash
   php artisan auctions:close-expired
   ```
   Must output success summary and exit 0.
2. **Automated Feature Test Execution**:
   ```bash
   php artisan test tests/Feature/Auction/AuctionLifecycleAutomationTest.php
   ```
   All 10 lifecycle tests must pass.
3. **Full Regression Check**:
   ```bash
   php artisan test tests/Feature/Seller/Milestone5AuctionEngineChallengeTest.php
   php artisan test tests/Feature/Seller/SellerAuctionAndProfileTest.php
   ```
   Both existing milestone test suites (67 tests) must continue to pass with 0 regressions.
