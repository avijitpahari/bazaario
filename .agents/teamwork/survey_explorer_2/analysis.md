# Comprehensive Database, Eloquent Model, Concurrency & Data Integrity Survey
**Bazaario Marketplace Admin Platform**  
**Investigator**: survey_explorer_2  
**Date**: September 28, 2026  
**Target Codebase**: `c:\xampp\htdocs\bazaario`  
**Database**: MySQL (`bazaario` database, 35 tables)

---

## 1. Executive Summary & Architectural Overview

The Bazaario Admin Platform is built on Laravel 11.x utilizing MySQL 8.x with InnoDB as the primary storage engine. The platform orchestrates a high-concurrency multi-vendor marketplace featuring:
- **Multi-tenant Merchant Management & KYC Verification** with statutory compliance (GSTIN, PAN, trade license, bank accounts, IFSC, FSSAI).
- **Consignment Multi-Seller Orders** with two-tier hierarchy (`orders` as parent transactions and `seller_orders` as merchant consignments with itemized commissions and shipping tracking).
- **Real-Time Live Auction Terminal** with anti-sniping velocity engine (+2 minute extension window if bids arrive within 120s of lot expiration), dynamic bidding ladders, and atomic hammer-down / cancellation controls.
- **Automated Escrow Payout Clearinghouse** supporting atomic single and batch NEFT releases guarded by three-layer pre-flight validations (verified merchant banking credentials, approved KYC status, and lockout against open buyer disputes or cancelled orders).
- **Dispute Mediation Desk** arbitrating customer return and damage claims against fulfillment telemetry with automated buyer refunds and merchant payout cancellation safeguards.
- **Taxonomy, Coupon Campaigns, and AI Engine Operations** with referential deletion barriers (active products, active subcategories, coupon redemption histories).

The platform architecture exhibits a mature implementation of `DB::transaction` blocks and pessimistic row locking (`lockForUpdate()`) across administrative mutation controllers. However, this deep-dive survey uncovered several notable schema anomalies, relationship mismatches, foreign key constraint omissions in early migrations, and a critical concurrency vulnerability in consumer-facing auction bidding.

---

## 2. Comprehensive Eloquent Model Catalog (25 Models)

Across `app/Models/`, 25 Eloquent models are registered. Below is the systematic audit of each model, its underlying table, fillable attributes, casts, relationships, and structural characteristics.

| # | Model Class | Database Table | Primary Keys & Indexes | Key Relationships | Key Casts & Traits |
|---|-------------|----------------|------------------------|-------------------|--------------------|
| 1 | `User` | `users` | `id` (PK), `email` (UQ), `phone` (UQ), `role`, `status` | `hasOne(SellerProfile)`, `hasMany(Product)`, `hasMany(Order)`, `hasMany(Address)`, `hasOne(Cart)`, `hasOne(Wishlist)`, `hasMany(Review)`, `hasMany(CouponUsage)` | `email_verified_at => datetime`, `Notifiable` |
| 2 | `SellerProfile` | `seller_profiles` | `id` (PK), `user_id` (UQ), `shop_slug` (UQ), `status` (IDX) | `belongsTo(User)`, `hasMany(Product, 'seller_id', 'user_id')` | `verified_at => datetime`, `commission_rate => decimal:2`, `trust_score => decimal:2` |
| 3 | `Product` | `products` | `id` (PK), `slug` (UQ), `sku` (UQ), `seller_id` (IDX), `category_id` (IDX), `status` (IDX), `sale_type` (IDX) | `belongsTo(User, 'seller_id')`, `belongsTo(Category)`, `hasMany(ProductImage)`, `hasOne(ProductImage)->where('is_primary', true)`, `hasMany(Review)`, `hasMany(CartItem)`, `hasMany(OrderItem)`, `hasOne(Auction)`, `hasMany(UserProductInteraction)`, `hasMany(AiRecommendation)` *(see gap)* | `price => decimal:2`, `weight => decimal:2`, `length => decimal:2`, `width => decimal:2`, `height => decimal:2`, `average_rating => decimal:2`, `HasFactory` |
| 4 | `ProductImage` | `product_images` | `id` (PK), `product_id` (IDX) | `belongsTo(Product)` | `is_primary => boolean`, `created_at => datetime`, `$timestamps = false` |
| 5 | `Category` | `categories` | `id` (PK), `slug` (UQ), `parent_id` (IDX), `status` (IDX) | `belongsTo(Category, 'parent_id')`, `hasMany(Category, 'parent_id')`, `hasMany(Product)` | `HasFactory` |
| 6 | `Order` | `orders` | `id` (PK), `order_number` (UQ), `user_id` (IDX), `coupon_id` (IDX), `order_status` (IDX) | `belongsTo(User)`, `belongsTo(Coupon)`, `hasMany(SellerOrder)`, `hasManyThrough(OrderItem, SellerOrder)`, `hasMany(OrderReturn)`, `hasOne(CouponUsage)`, `hasOne(Auction, 'winning_order_id')` *(see gap)* | `subtotal => decimal:2`, `discount_amount => decimal:2`, `shipping_amount => decimal:2`, `total_amount => decimal:2`, `placed_at => datetime`, `HasFactory` |
| 7 | `SellerOrder` | `seller_orders` | `id` (PK), `seller_order_number` (UQ), `order_id` (IDX), `seller_id` (IDX), `status` (IDX) | `belongsTo(Order)`, `belongsTo(User, 'seller_id')`, `hasMany(OrderItem)`, `hasMany(Payout)` | `subtotal => decimal:2`, `shipping_amount => decimal:2`, `commission_rate => decimal:2`, `commission_amount => decimal:2`, `payout_amount => decimal:2`, `shipped_at => datetime`, `delivered_at => datetime`, `HasFactory` |
| 8 | `OrderItem` | `order_items` | `id` (PK), `seller_order_id` (IDX), `product_id` (IDX) | `belongsTo(SellerOrder)`, `belongsTo(Product)`, `hasOne(Review)` | `unit_price => decimal:2`, `total_price => decimal:2`, `created_at => datetime`, `$timestamps = false`, `HasFactory` |
| 9 | `Payout` | `payouts` | `id` (PK), `seller_id` (IDX), `seller_order_id` (IDX), `status` (IDX) | `belongsTo(User, 'seller_id')`, `belongsTo(SellerOrder)` | `gross_amount => decimal:2`, `commission_amount => decimal:2`, `net_amount => decimal:2`, `paid_at => datetime`, `HasFactory` |
| 10 | `OrderReturn` | `returns` | `id` (PK), `order_id` (FK), `order_item_id` (FK), `user_id` (FK), `status` (IDX) | `belongsTo(Order)`, `belongsTo(OrderItem)`, `belongsTo(User)` | `refund_amount => decimal:2`, `requested_at => datetime`, `approved_at => datetime`, `completed_at => datetime`, `HasFactory` |
| 11 | `Auction` | `auctions` | `id` (PK), `product_id` (FK), `seller_id` (FK to `seller_profiles`), `status` (IDX), `starts_at`/`ends_at` (IDX) | `belongsTo(Product)`, `belongsTo(User, 'seller_id')` *(see gap)*, `belongsTo(User, 'winner_id')`, `belongsTo(AuctionBid, 'winning_bid_id')` *(see gap)*, `belongsTo(Order, 'winning_order_id')` *(see gap)*, `hasMany(AuctionBid)` | `starting_price => decimal:2`, `reserve_price => decimal:2`, `current_price => decimal:2`, `minimum_increment => decimal:2`, `starts_at => datetime`, `ends_at => datetime`, `HasFactory` |
| 12 | `AuctionBid` | `bids` | `id` (PK), `auction_id` (FK), `user_id` (FK), `amount` | `belongsTo(Auction)`, `belongsTo(User)` | `amount => decimal:2`, `created_at => datetime`, `updated_at => datetime`, `HasFactory` |
| 13 | `Coupon` | `coupons` | `id` (PK), `code` (UQ), `status` (IDX) | `hasMany(Order)`, `hasMany(CouponUsage)` | `discount_value => decimal:2`, `minimum_order_amount => decimal:2`, `maximum_discount_amount => decimal:2`, `starts_at => datetime`, `expires_at => datetime`, `HasFactory` |
| 14 | `CouponUsage` | `coupon_usages` | `id` (PK), `coupon_id` (IDX), `user_id` (IDX), `order_id` (IDX) | `belongsTo(Coupon)`, `belongsTo(User)`, `belongsTo(Order)` | `discount_amount => decimal:2`, `used_at => datetime`, `$timestamps = false`, `HasFactory` |
| 15 | `Address` | `addresses` | `id` (PK), `user_id` (IDX), `is_default` (IDX) | `belongsTo(User)` | `is_default => boolean`, `latitude => decimal:7`, `longitude => decimal:7`, `HasFactory` |
| 16 | `Cart` | `carts` | `id` (PK), `user_id` (UQ) | `belongsTo(User)`, `hasMany(CartItem)` | `HasFactory` |
| 17 | `CartItem` | `cart_items` | `id` (PK), `cart_id` (IDX), `product_id` (IDX) | `belongsTo(Cart)`, `belongsTo(Product)` | `unit_price => decimal:2`, `quantity => integer`, `HasFactory` |
| 18 | `Wishlist` | `wishlists` | `id` (PK), `user_id` (UQ) | `belongsTo(User)`, `hasMany(WishlistItem)` | `HasFactory` |
| 19 | `WishlistItem` | `wishlist_items` | `id` (PK), `wishlist_id` (IDX), `product_id` (IDX) | `belongsTo(Wishlist)`, `belongsTo(Product)` | `const UPDATED_AT = null`, `HasFactory` |
| 20 | `Review` | `reviews` | `id` (PK), `product_id` (IDX), `user_id` (IDX), `order_item_id` (IDX), `status` (IDX) | `belongsTo(Product)`, `belongsTo(User)`, `belongsTo(OrderItem)` | `rating => integer`, `HasFactory` |
| 21 | `SiteSetting` | `site_settings` | `id` (PK), `setting_key` (UQ) | None | `setting_value => string/json`, `HasFactory` |
| 22 | `TrustScoreLog` | `trust_score_logs` | `id` (PK), `seller_id` (IDX) | `belongsTo(User, 'seller_id')` | `fulfillment_rate => decimal:2`, `average_rating => decimal:2`, `verification_bonus => decimal:2`, `computed_score => decimal:2`, `created_at => datetime`, `$timestamps = false`, `HasFactory` |
| 23 | `UserProductInteraction` | `user_product_interactions` | `id` (PK), `user_id` (IDX), `product_id` (IDX), `interaction_type` (IDX) | `belongsTo(User)`, `belongsTo(Product)` | `metadata => array`, `created_at => datetime`, `$timestamps = false`, `HasFactory` |
| 24 | `ChatConversation` | `chat_conversations` | `id` (PK), `user_id` (IDX), `status` (IDX) | `belongsTo(User)`, `hasMany(ChatMessage, 'conversation_id')` | `HasFactory` |
| 25 | `ChatMessage` | `chat_messages` | `id` (PK), `conversation_id` (IDX), `role` | `belongsTo(ChatConversation, 'conversation_id')` | `tool_data => array`, `created_at => datetime`, `$timestamps = false`, `HasFactory` |

---

## 3. Specialized Marketplace Domain Field & Data Safeguard Verification

### 3.1 Merchant Hub & KYC Compliance Architecture
- **Database Table**: `seller_profiles` (populated via migrations `2026_09_11_000003` and `2026_09_26_000001`).
- **Statutory Identity Fields**:
  - `gstin`: `VARCHAR(20)`, nullable. Holds the 15-character Indian Goods and Services Tax Identification Number.
  - `pan_number`: `VARCHAR(20)`, nullable. Permanent Account Number for corporate/individual income tax.
  - `trade_license_number`: `VARCHAR(50)`, nullable. Municipal or state commercial trade registration.
  - `fssai_number`: `VARCHAR(30)`, nullable. Food Safety and Standards Authority registration.
- **Banking Settlement Credentials**:
  - `bank_account_number`: `VARCHAR(50)`, nullable. Commercial current/savings account for NEFT disbursements.
  - `bank_ifsc`: `VARCHAR(20)`, nullable. 11-character Indian Financial System Code.
- **Commission & Trust Ratings**:
  - `commission_rate`: `DECIMAL(5, 2)`, nullable. Platform take-rate override per merchant.
  - `trust_score`: `DECIMAL(5, 2)`, default `0.00`. Weighted reliability metric (order fulfillment + rating).
- **Verification Statuses**:
  - `status`: `ENUM('pending', 'approved', 'rejected', 'suspended')`.
  - `rejection_reason`: `TEXT`, nullable. Stores structured auditor feedback when rejected.
  - `verified_at`: `TIMESTAMP`, nullable. Audit timestamp populated atomically on KYC approval.
- **Account Synchronizations**:
  - When a seller is approved (`approveSeller`), the user record's `role` is upgraded to `'seller'` and `status` is set to `'active'`.
  - When rejected (`rejectSeller`), the associated user account is set to `'suspended'`.

### 3.2 Consignment Multi-Seller Orders
- **Parent Order (`orders`)**:
  - Contains overall billing: `subtotal`, `discount_amount`, `shipping_amount`, `total_amount`.
  - Global statuses: `order_status` (`pending`, `processing`, `completed`, `cancelled`, `refunded`), `payment_status` (`pending`, `paid`, `failed`, `refunded`), `payment_method` (`cod`, `card`, `upi`, `net_banking`, `wallet`).
  - Delivery metadata: Full recipient name, phone, two address lines, city, state, country, postal code.
- **Sub-Order Consignment Split (`seller_orders`)**:
  - Foreign key: `order_id` references parent order.
  - Seller identification: `seller_id` references `users.id`.
  - Sub-order reference: `seller_order_number` (e.g. `BZ-10482-S1`).
  - Itemized Financials:
    - `subtotal`: Sum of order items under this merchant.
    - `shipping_amount`: Seller-specific fulfillment freight.
    - `commission_rate`: Platform take-rate applied to this consignment.
    - `commission_amount`: `DECIMAL(12, 2)` platform revenue retained.
    - `payout_amount`: Net payable to seller (`subtotal + shipping_amount - commission_amount`).
  - Consignment Status: `status` (`placed`, `processing`, `packed`, `shipped`, `delivered`, `cancelled`, `returned`).
  - Tracking Telemetry: `tracking_number` (string, e.g. `BLUEDART-8492014`), `shipped_at` (timestamp), `delivered_at` (timestamp).

### 3.3 Live Auction Engine & Anti-Sniping Protection
- **Lot Definition (`auctions`)**:
  - Lot financial parameters: `starting_price`, `reserve_price`, `current_price`, `minimum_increment` (default ₹100.00).
  - Timing: `starts_at`, `ends_at`.
  - Lifecycle: `status` (`scheduled`, `live`, `ended`, `cancelled`).
  - Outcome: `winner_id` (`users.id`).
- **Bidding History (`bids`)**:
  - `auction_id`, `user_id`, `amount`, `created_at`, `updated_at`.
- **Anti-Sniping Velocity Engine**:
  - Defined in `app/Http/Controllers/User/AuctionController.php::placeBid()`:
    ```php
    $minutesLeft = now()->diffInSeconds($auction->ends_at, false);
    if ($minutesLeft >= 0 && $minutesLeft <= 120) {
        $auction->ends_at = $auction->ends_at->addMinutes(2);
    }
    ```
    If any valid bid is submitted within 120 seconds of scheduled auction close, the expiration timer is extended by +2 minutes to prevent automated snipe bots.
- **Hammer-Down Moderator Action (`AdminDashboardController::endAuction`)**:
  - Atomically locks auction and bids using pessimistic row locks (`lockForUpdate()`).
  - Determines highest bid via `AuctionBid::where('auction_id', ...)->lockForUpdate()->orderByDesc('amount')->first()`.
  - Evaluates reserve price: if `$highestBid->amount >= $auction->reserve_price`, marks `winner_id = $highestBid->user_id` and sets `status = 'ended'`. If reserve price not met, closes lot with `winner_id = null`.

### 3.4 Escrow Payouts & Batch Settlements
- **Payout Ledger (`payouts`)**:
  - `seller_id`, `seller_order_id`, `gross_amount`, `commission_amount`, `net_amount`.
  - Lifecycle `status`: `ENUM('pending', 'processing', 'paid', 'failed')`.
  - Payout reference: `payout_reference` (`NEFT-BZ-YYYYMMDD-XXXX` or `BATCH-NEFT-YYYYMMDD-XXXX`).
  - Paid timestamp: `paid_at`.
- **Three-Tier Pre-Flight Guardrails**:
  1. *Bank Verification Pre-Flight*: Validates non-empty `bank_account_number` and `bank_ifsc` on `SellerProfile`.
  2. *KYC & Trading Access Pre-Flight*: Rejects release if `SellerProfile::status !== 'approved'` or user `status === 'suspended'`.
  3. *Dispute & Cancellation Lockout Pre-Flight*: Inspects associated consignment and parent order:
     - Blocks payout if `sellerOrder->status` is `cancelled` or `returned`.
     - Blocks payout if parent `order_status` is `cancelled` or `refunded`.
     - Blocks payout if active open dispute exists in `returns` (`status IN ('requested', 'pickup_scheduled', 'received', 'refund_processing')`).

### 3.5 Dispute Mediation Desk (`returns` table)
- **Model Mapping**: `OrderReturn` maps to table `returns`.
- **Fields**: `order_id`, `order_item_id`, `user_id`, `reason`, `description`, `status` (`requested`, `approved`, `rejected`, `pickup_scheduled`, `received`, `refund_processing`, `refunded`), `refund_amount`, `requested_at`, `approved_at`, `completed_at`.
- **Arbitration Action (`AdminDashboardController::arbitrateDispute`)**:
  - Re-arbitration guard: Blocks execution if `status !== 'requested'`.
  - If approved:
    - Sets dispute `status = 'approved'`, `approved_at = now()`.
    - Updates parent `orders.payment_status = 'refunded'`.
    - Updates `seller_orders.status = 'returned'`.
    - Automatically voids any pending seller payout (`payouts.status = 'failed'`, `payout_reference = 'DISPUTE-REFUNDED-YYYYMMDD'`) ensuring merchant escrow funds are not disbursed for refunded merchandise.
  - If rejected:
    - Sets dispute `status = 'rejected'`, `completed_at = now()`. Merchant escrow is retained.

### 3.6 Category Deletion & Coupon Usage History Safeguards
- **Category Safeguards (`AdminDashboardController::deleteCategory`)**:
  - Executes inside `DB::transaction`.
  - Locks category with `lockForUpdate()->withCount(['products', 'children'])`.
  - If `products_count > 0`: Rejection flash error ("Cannot delete category because it contains X active products").
  - If `children_count > 0`: Rejection flash error ("Cannot delete category because it contains X active sub-categories").
- **Coupon Usage Safeguards (`AdminDashboardController::deleteCoupon`)**:
  - Executes inside `DB::transaction`.
  - Locks coupon with `lockForUpdate()->withCount(['usages', 'orders'])`.
  - If `usages_count > 0`, `orders_count > 0`, or `used_count > 0`: Rejection flash error ("Cannot delete coupon because it has already been redeemed in X order(s). You can deactivate it instead.").
  - Blade UI: Delete button is replaced with a locked disabled badge when redemptions exist.

---

## 4. Concurrency & Transaction Boundary Audit

A comprehensive code inspection was conducted across all 20 mutation methods in `AdminDashboardController` and user mutation endpoints.

### 4.1 Mutation Methods Matrix

| Method | Target Entity | `DB::transaction` | Pessimistic Locking (`lockForUpdate`) | Cascading Locks / Updates | Concurrency Risk Assessment |
|---|---|---|---|---|---|
| `approveSeller` | `SellerProfile` | Yes | Yes (`SellerProfile`) | `User` updated without `lockForUpdate()` | **Low**: Merchant approvals are infrequent admin actions; low race probability. |
| `rejectSeller` | `SellerProfile` | Yes | Yes (`SellerProfile`) | `User` updated without `lockForUpdate()` | **Low**: Low race probability. |
| `toggleSellerStatus` | `SellerProfile` | Yes | Yes (`SellerProfile`) | `User` updated without `lockForUpdate()` | **Low**: Guarded by status whitelist. |
| `updateSellerCommission` | `SellerProfile` | Yes | Yes (`SellerProfile`) | None | **None**: Strict atomic update. |
| `toggleProductStatus` | `Product` | Yes | Yes (`Product`) | Reads `Auction` status without row lock | **Low**: Live auction check prevents deactivation. |
| `updateProductStock` | `Product` | Yes | Yes (`Product`) | None | **None**: Strict atomic update. |
| `deleteProduct` | `Product` | Yes | Yes (`Product`) | Reads `Auction` status without row lock | **Low**: Auction existence check prevents deletion. |
| `storeCategory` | `Category` | Yes | N/A (Insert) | Loop handles slug collision | **None**: Dynamic slug disambiguation. |
| `updateCategory` | `Category` | Yes | Yes (`Category`) | Loop handles slug collision | **None**: Strict atomic update. |
| `deleteCategory` | `Category` | Yes | Yes (`Category`) | `withCount(['products', 'children'])` | **None**: Strong referential integrity safeguards. |
| `updateOrderStatus` | `Order` | Yes | Yes (`Order`) | Updates `seller_orders` & `payouts` | **Low-Medium**: Checks `Payout::where(...)->where('status', 'paid')->exists()` before cancel. InnoDB row locks acquired during update. |
| `endAuction` | `Auction` | Yes | Yes (`Auction`) | Yes (`AuctionBid::lockForUpdate()`) | **None**: Strict atomic winner lot assignment. |
| `cancelAuction` | `Auction` | Yes | Yes (`Auction`) | None | **None**: Strict atomic update. |
| `releasePayout` | `Payout` | Yes | Yes (`Payout`) | Yes (`SellerOrder::lockForUpdate()`) | **Low**: Comprehensive pre-flight checks and locks. |
| `batchReleasePayouts` | `Payout` collection | Yes | Yes (`Payout::lockForUpdate()`) | Checks seller profiles and disputes | **Low**: All pending payouts locked at query inception. |
| `arbitrateDispute` | `OrderReturn` | Yes | Yes (`OrderReturn`) | Updates `orders`, `seller_orders`, `payouts` | **None**: Strict atomic arbitration and payout voiding. |
| `toggleCustomerStatus` | `User` | Yes | Yes (`User`) | None | **None**: Strict atomic update. |
| `storeCoupon` | `Coupon` | Yes | N/A (Insert) | Code uppercase & sanitization | **None**: Unique code constraint. |
| `toggleCouponStatus` | `Coupon` | Yes | Yes (`Coupon`) | None | **None**: Strict atomic update. |
| `deleteCoupon` | `Coupon` | Yes | Yes (`Coupon`) | `withCount(['usages', 'orders'])` | **None**: Redemptions safeguard prevents deletion. |
| `updateAiSettings` | `SiteSetting` | Yes | N/A (Upsert) | Key-value iterations | **None**: Strict configuration atomic upsert. |
| `placeBid` *(User Controller)* | `Auction` / `AuctionBid` | Yes | **MISSING** | Updates `Auction` without `lockForUpdate()` | **HIGH CONCURRENCY RISK**: See Section 4.2. |

### 4.2 Critical Concurrency Risk: User Live Auction Bidding (`placeBid`)
In `app/Http/Controllers/User/AuctionController.php`, lines 276–314:
```php
public function placeBid(Request $request, Auction $auction)
{
    // ...
    $minNextBid = (float) $auction->current_price + (float) $auction->minimum_increment;
    $request->validate(['amount' => 'required|numeric|min:' . $minNextBid]);

    DB::transaction(function () use ($auction, $user, $bidAmount) {
        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id'    => $user->id,
            'amount'     => $bidAmount,
        ]);

        $auction->current_price = $bidAmount;

        $minutesLeft = now()->diffInSeconds($auction->ends_at, false);
        if ($minutesLeft >= 0 && $minutesLeft <= 120) {
            $auction->ends_at = $auction->ends_at->addMinutes(2);
        }

        $auction->save();
    });
}
```
**The Flaw**:
1. `$auction` is injected via Route Model Binding *before* entering `DB::transaction`.
2. Validation `$minNextBid` is evaluated against this stale in-memory instance.
3. If Bidder A and Bidder B submit bids simultaneously:
   - Both pass validation with the current price (e.g. ₹5,000).
   - Both transactions enter `DB::transaction`.
   - The second transaction overwrites `auction->current_price` without verifying whether a higher bid was inserted milliseconds earlier.
4. **Remediation**: Inside the transaction, reload the auction with `Auction::lockForUpdate()->findOrFail($auction->id)`, re-evaluate `$bidAmount >= ($lockedAuction->current_price + $lockedAuction->minimum_increment)`, and only then persist the bid.

---

## 5. Query Efficiency & N+1 Eager-Loading Audit

An audit of all 16 administrative view endpoints in `AdminDashboardController` was conducted to verify eager-loading (`with(...)`) coverage and eliminate N+1 queries.

### 5.1 Admin Endpoints Eager-Loading Audit Table

| Screen # | Admin Route / View | Controller Query Definition | Relations Eager-Loaded | Blade Fields Consumed | N+1 Verdict |
|---|---|---|---|---|---|
| 1 | `admin.dashboard` (`admin/dashboard.blade.php`) | `Order::with(['user', 'sellerOrders.seller.sellerProfile', 'sellerOrders.items.product'])`, `SellerProfile::with('user')`, `Auction::with(['product.category', 'seller'])` | `user`, `sellerOrders.seller.sellerProfile`, `sellerOrders.items.product`, `product.category`, `seller` | `$order->delivery_full_name`, `$order->user->name`, `$order->sellerOrders->count()`, `$order->sellerOrders->pluck('seller.name')`, `$auc->product->name`, `$auc->seller->name` | **PASS (Zero N+1)** |
| 2 | `admin.sellers.index` (`admin/sellers/index.blade.php`) | `SellerProfile::with(['user', 'products'])->withCount('products')` | `user`, `products`, `products_count` | `$seller->shop_name`, `$seller->user->name`, `$seller->products->count()` | **PASS (Zero N+1)** |
| 3 | `admin.sellers.approvals` (`admin/sellers/approvals.blade.php`) | `SellerProfile::with('user')` | `user` | `$seller->user->name`, `$seller->user->email`, `$seller->gstin` | **PASS (Zero N+1)** |
| 4 | `admin.sellers.show` (`admin/sellers/show.blade.php`) | `SellerProfile::where(...)->with(['user', 'products'])`, `SellerOrder::where(...)->with(['order.user', 'items.product'])` | `user`, `products`, `order.user`, `items.product` | Detailed merchant statutory fields, `$recentOrders` loop with `$order->order->order_number`, `$order->items` | **PASS (Zero N+1)** |
| 5 | `admin.products.index` (`admin/products/index.blade.php`) | `Product::with(['category', 'seller'])` | `category`, `seller` | `$product->category->name`, `$product->seller->name`, `$product->sku` | **PASS (Zero N+1)** |
| 6 | `admin.categories.index` (`admin/categories/index.blade.php`) | `Category::withCount(['products', 'children'])` | `products_count`, `children_count` | `$category->products_count`, `$category->children_count` | **PASS (Zero N+1)** |
| 7 | `admin.orders.index` (`admin/orders/index.blade.php`) | `Order::with(['user', 'sellerOrders.seller.sellerProfile', 'sellerOrders.items.product'])` | `user`, `sellerOrders.seller.sellerProfile`, `sellerOrders.items.product` | `$order->delivery_full_name`, `$order->user->name`, `$order->sellerOrders` | **PASS (Zero N+1)** |
| 8 | `admin.orders.show` (`admin/orders/show.blade.php`) | `Order::where(...)->with(['user', 'sellerOrders.seller.sellerProfile', 'sellerOrders.items.product', 'coupon'])` | `user`, `sellerOrders.seller.sellerProfile`, `sellerOrders.items.product`, `coupon` | Sub-orders loop, items loop, merchant shop names, customer shipping addresses | **PASS (Zero N+1)** |
| 9 | `admin.auctions.index` (`admin/auctions/index.blade.php`) | `Auction::with(['product.category', 'seller', 'winner'])->withCount('bids')` | `product.category`, `seller`, `winner`, `bids_count` | `$auc->product->name`, `$auc->product->category->name`, `$auc->seller->name`, `$auc->bids_count` | **PASS (Zero N+1)** |
| 10 | `admin.auctions.show` (`admin/auctions/show.blade.php`) | `Auction::with(['product.category', 'seller', 'winner', 'bids.user'])` | `product.category`, `seller`, `winner`, `bids.user` | `$auction->product`, `$auction->seller`, bidding ladder loop with `$bid->user->name` | **PASS (Zero N+1)** |
| 11 | `admin.payouts.index` (`admin/payouts/index.blade.php`) | `Payout::with(['seller.sellerProfile', 'sellerOrder.order'])` | `seller.sellerProfile`, `sellerOrder.order` | `$payout->seller->sellerProfile->shop_name`, `$payout->seller->sellerProfile->bank_account_number`, `$payout->seller->sellerProfile->bank_ifsc` | **PASS (Zero N+1)** |
| 12 | `admin.disputes.index` (`admin/disputes/index.blade.php`) | `OrderReturn::with(['order.user', 'user', 'orderItem.sellerOrder.seller.sellerProfile'])` | `order.user`, `user`, `orderItem.sellerOrder.seller.sellerProfile` | `$dispute->user->name`, `$dispute->order->order_number`, `$dispute->orderItem->sellerOrder->seller->sellerProfile->shop_name` | **PASS (Zero N+1)** |
| 13 | `admin.customers.index` (`admin/customers/index.blade.php`) | `User::where('role', 'user')->withCount('orders')` | `orders_count` | `$customer->name`, `$customer->email`, `$customer->orders_count` | **PASS (Zero N+1)** |
| 14 | `admin.customers.show` (`admin/customers/show.blade.php`) | `User::where(...)->with(['orders', 'addresses'])`, `AuctionBid::where(...)->with('auction.product')` | `orders`, `addresses`, `auction.product` | `$customer->orders`, `$customer->addresses`, `$bids` loop with `$bid->auction->product->name` | **PASS (Zero N+1)** |
| 15 | `admin.coupons.index` (`admin/coupons/index.blade.php`) | `Coupon::withCount('orders')` | `orders_count` | `$coupon->code`, `$coupon->used_count`, `$coupon->orders_count` | **PASS (Zero N+1)** |
| 16 | `admin.settings.ai` (`admin/settings/ai.blade.php`) | `SiteSetting::allMap()` | Key-value dictionary | Flat associative configuration array | **PASS (Zero N+1)** |

All 16 administrative view screens possess 100% eager-loading coverage, preventing any N+1 query degradation across listings and detail dossiers.

---

## 6. Database Schema Gaps, Relational Inconsistencies & Integrity Risks

### 6.1 Critical Mismatch: `Auction::seller` Relationship vs Foreign Key Schema
- **Observation**:
  - In `create_auctions_table.php` (migration `2026_09_11_000019`), line 15–17:
    ```php
    // NOTE: seller_id references seller_profiles.id (not users.id) in this schema
    $table->foreignId('seller_id')->constrained('seller_profiles')->cascadeOnDelete();
    ```
  - In `app/Models/Auction.php`, line 46–49:
    ```php
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    ```
- **Consequence**:
  - In the database, `auctions.seller_id` contains the `id` of `seller_profiles` (e.g. `seller_id = 1`).
  - In the database, `seller_profiles.id = 1` belongs to `user_id = 9` (Heritage Leatherworks).
  - But `users.id = 1` is Avijit Pahari (a customer/buyer account).
  - When Eloquent executes `$auction->seller`, it runs `SELECT * FROM users WHERE id = 1`, mistakenly associating the auction lot with Avijit Pahari instead of the real merchant User #9!
- **Remediation**:
  Define a proper relationship in `Auction.php`:
  ```php
  public function sellerProfile(): BelongsTo
  {
      return $this->belongsTo(SellerProfile::class, 'seller_id');
  }

  public function getSellerAttribute()
  {
      return $this->sellerProfile?->user;
  }
  ```
  Or align the column in a migration so `seller_id` consistently references `users.id` across all platform tables (`products`, `seller_orders`, `payouts`, `auctions`).

### 6.2 Phantom Relationships on Non-Existent Columns
- **In `app/Models/Auction.php`**:
  ```php
  public function winningBid(): BelongsTo
  {
      return $this->belongsTo(AuctionBid::class, 'winning_bid_id');
  }

  public function winningOrder(): BelongsTo
  {
      return $this->belongsTo(Order::class, 'winning_order_id');
  }
  ```
  Neither `winning_bid_id` nor `winning_order_id` exists in the `auctions` table (verified via `DESCRIBE auctions`). Calling `$auction->winningBid` or `$auction->winningOrder` triggers an SQL `Unknown column` exception!
- **In `app/Models/Order.php`**:
  ```php
  public function winningAuction(): HasOne
  {
      return $this->hasOne(Auction::class, 'winning_order_id');
  }
  ```
  Similarly fails with an SQL exception if invoked.

### 6.3 Ghost Model: `AiRecommendation`
- In `app/Models/Product.php`, line 95–98:
  ```php
  public function aiRecommendations(): HasMany
  {
      return $this->hasMany(AiRecommendation::class);
  }
  ```
  No `AiRecommendation` model exists in `app/Models/`, nor does an `ai_recommendations` table exist in `database/migrations/`.

### 6.4 Database Tables Lacking Eloquent Models
Four operational database tables were created in migrations but lack Eloquent model classes:
1. `invoices` (`2026_09_11_000015_create_invoices_table.php`): Contains `order_id`, `invoice_number`, `subtotal`, `discount`, `shipping_fee`, `tax`, `total`, `pdf_path`, `issued_at`. No `App\Models\Invoice` exists.
2. `payments` (`2026_09_11_000016_create_payments_table.php`): Contains `order_id`, `user_id`, `payment_method`, `gateway`, `transaction_id`, `amount`, `status`, `paid_at`. No `App\Models\Payment` exists.
3. `notifications` (`2026_09_11_000025_create_notifications_table.php`): System notifications table without a dedicated model.
4. `email_otps` (`2026_09_11_000026_create_email_otps_table.php`): Auth verification OTPs without a dedicated model.

### 6.5 Inconsistent Foreign Key Constraints Across Migrations
- Early migrations (`orders`, `seller_orders`, `payouts`, `seller_profiles`) created indexes (`$table->index(...)`) but omitted database engine foreign key constraints (`$table->foreignId(...)->constrained(...)`).
- Later migrations (`returns`, `auctions`, `bids`) strictly enforce InnoDB foreign key constraints.
- This creates an asymmetric referential integrity model where some parent deletions trigger cascade errors while others leave orphaned rows.

### 6.6 Escrow Status Enum Discrepancy
- The authoritative specification lists payout status as: `('pending', 'held', 'released', 'failed')`.
- The MySQL schema enum (`payouts.status`) is: `('pending', 'processing', 'paid', 'failed')`.
- The application currently maps `'pending'` to "Escrow Hold" in UI and `'paid'` to "Disbursed" / "Released". If an external process or seeder inserts the string `'held'` or `'released'`, MySQL will reject it with a data truncation / enum constraint error.

### 6.7 Missing Statutory Tax Deductions on Consignments
- Under Section 194-O (TDS on e-commerce operators) and Section 52 of CGST Act (TCS), marketplace platforms must deduct and remit tax withholdings.
- The `seller_orders` and `payouts` tables currently track `commission_rate` and `commission_amount`, but have no columns for `tds_amount`, `tcs_amount`, or `gst_input_credit`.

### 6.8 Seeder Disconnect in `DatabaseSeeder`
- `database/seeders/DatabaseSeeder.php` only invokes `MarketplaceDataSeeder::class`.
- `AdminOperationsDataSeeder.php` (which populates admin test accounts, pending KYC sellers, consignment orders, payouts, and disputes) is not wired into `DatabaseSeeder::run()`. Running a fresh `php artisan migrate:fresh --seed` would leave the admin panel devoid of operational test data unless `--class=AdminOperationsDataSeeder` is manually specified.

---

## 7. Concrete Hardening Recommendations

1. **Fix `Auction` Seller Relationship**:
   Update `Auction.php` so `sellerProfile()` binds to `SellerProfile::class, 'seller_id'`, and create an accessor `getSellerAttribute()` that returns `$this->sellerProfile?->user`. Remove or repair phantom relationships `winningBid()` and `winningOrder()`.
2. **Pessimistic Row Lock in User Bidding**:
   Refactor `AuctionController::placeBid()` to load `$auction` with `Auction::lockForUpdate()->findOrFail($auction->id)` inside `DB::transaction`, re-verifying that the bid meets the minimum increment before persisting.
3. **Register Missing Models**:
   Generate Eloquent models for `Invoice` and `Payment` in `app/Models/` and add relationships to `Order` (`$order->invoice()`, `$order->payments()`). Remove the dead `aiRecommendations()` relation from `Product.php`.
4. **Wire Seeders**:
   Update `DatabaseSeeder.php` to call `AdminOperationsDataSeeder::class` immediately following `MarketplaceDataSeeder::class`.
5. **Normalize Enums**:
   Add an idempotent migration to expand `payouts.status` enum to include `held` and `released` aliases alongside `pending` and `paid` to avoid future enum collision.

---
*Report generated and verified against local MySQL runtime and Laravel 11.x framework.*
