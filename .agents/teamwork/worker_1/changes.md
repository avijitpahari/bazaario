# Changes Summary — Worker 1

**Agent**: `worker_1`  
**Date**: 2026-09-28  
**Scope**: Models & Relationship Integrity, Concurrency Locking, Design System & Validation Alerting, Operational Domain Interactive Controls

---

## 1. Files Modified & Created

### Models & Database
1. **`app/Models/Auction.php`**
   - Corrected `seller()` relationship to:
     `return $this->belongsTo(SellerProfile::class, 'seller_id');`
     Fixes critical mapping bug where `auctions.seller_id` pointed to `seller_profiles.id`, returning incorrect user accounts when mapped to `User`.
   - Removed phantom relationships `winningBid()` and `winningOrder()` which referenced non-existent columns `winning_bid_id` and `winning_order_id`.

2. **`app/Models/Product.php`**
   - Removed ghost relationship `aiRecommendations()` that referenced non-existent class `AiRecommendation::class`.

3. **`app/Models/Order.php`**
   - Removed phantom relationship `winningAuction()` that referenced non-existent column `winning_order_id`.
   - Added genuine Eloquent relationships:
     - `invoice()`: `hasOne(Invoice::class)`
     - `payments()`: `hasMany(Payment::class)`

4. **`app/Models/Invoice.php`** (Created)
   - Created model for table `invoices` with full `$fillable`, decimal and datetime casts, and `belongsTo(Order::class)`.

5. **`app/Models/Payment.php`** (Created)
   - Created model for table `payments` with full `$fillable`, decimal and datetime casts, and `belongsTo(Order::class)` / `belongsTo(User::class)`.

6. **`app/Models/Notification.php`** (Created)
   - Created model for table `notifications` with full `$fillable`, array and datetime casts, `belongsTo(User::class)`, and helpers `isRead()` and `markAsRead()`.

7. **`app/Models/EmailOtp.php`** (Created)
   - Created model for table `email_otps` with `$fillable`, `const UPDATED_AT = null`, datetime casts, and helper `isExpired()`.

8. **`database/seeders/DatabaseSeeder.php`**
   - Integrated `AdminOperationsDataSeeder::class` in `$this->call([...])`, ensuring operational datasets (pending merchants, consignments, disputes, payouts) seed automatically.

### Concurrency Safety
9. **`app/Http/Controllers/AuctionController.php`** (Created)
   - Implemented `placeBid(Request $request, $id)` with pessimistic row locking:
     - Acquires `Auction::where('id', $id)->lockForUpdate()->firstOrFail()` inside `DB::transaction`.
     - Re-evaluates auction status ('live' / 'active') and expiration under lock.
     - Re-evaluates minimum bid increment requirement under lock (`$minNextBid = $auction->current_price + $auction->minimum_increment`).
     - Atomically creates `AuctionBid` and updates `$auction->current_price = $bidAmount`.
     - Executes anti-sniping extension atomically within lock boundary.

### UI Design System & Validation Alerting
10. **`resources/views/layouts/admin.blade.php`**
    - Updated Tailwind configuration `borderRadius.xl` from `"0.75rem"` (12px) to `"0.875rem"` (14px), aligning runtime cards with the 14px design token specification.
    - Added `@if(isset($errors) && $errors->any())` alert notification banner to the flash messages canvas, rendering all validation redirect error messages with dismissal controls.

11. **`resources/views/admin/orders/index.blade.php`**
    - Added defensive float casting `(float)($order->total_amount ?? 0)` to `number_format` calls to eliminate PHP 8.1+ deprecation risks on null order amounts.

### Operational Domain Interactive Controls
12. **`resources/views/admin/sellers/approvals.blade.php`**
    - Added an interactive Rejection Feedback Modal with a required reason textarea (`name="reason"`) on `POST admin/sellers/{id}/reject` (`admin.sellers.reject`), allowing administrators to provide structured feedback for merchant KYC rejection.

13. **`resources/views/admin/categories/index.blade.php`**
    - Added an interactive Category Edit Modal with inputs for `name`, `slug`, `description`, and `is_active` (`@csrf`, `@method('PUT')`), connecting to `PUT admin/categories/{id}` (`admin.categories.update`).
    - Added an Edit button in the table actions column for each taxonomy row.

14. **`resources/views/admin/products/index.blade.php`**
    - Added an interactive Quick Edit Stock & Price modal connecting to `POST admin/products/{id}/update-stock` (`admin.products.update-stock`), with inputs for `stock` and `price`, `@csrf`.
    - Added quick-trigger buttons in the Actions column and on the Price and Stock table cells.
