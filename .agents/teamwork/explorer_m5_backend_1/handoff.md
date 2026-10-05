# Milestone 5 (Profile & Auction Management) — Backend Architecture & Implementation Handoff Report

## 1. Observation

### 1.1 Existing Routes in `routes/web.php`
- **Location**: `routes/web.php:236-252`
- **Verbatim Code**:
```php
    Route::prefix('auctions')->name('auctions.')->group(function () {
        Route::get('/', fn () => view('seller.auctions.index'))->name('index');
        Route::get('/create', fn () => view('seller.auctions.create'))->name('create');
        Route::post('/', fn () => redirect()->route('seller.auctions.index'))->name('store');
        Route::get('/history', fn () => view('seller.auctions.history'))->name('history');
        Route::get('/live', fn () => view('seller.auctions.live'))->name('live');
        Route::get('/{auction}', fn () => view('seller.auctions.show'))->name('show');
        Route::post('/{auction}/cancel', fn () => back())->name('cancel');
    });

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/profile', fn () => view('seller.account.profile'))->name('profile');
        Route::get('/location', fn () => view('seller.account.location'))->name('location');
        Route::get('/security', fn () => view('seller.account.security'))->name('security');
        Route::get('/settings', fn () => view('seller.account.settings'))->name('settings');
    });
```
- **Observations**:
  1. All 11 routes under `auctions` and `account` are currently inline stub closures returning empty views or performing dummy redirects.
  2. There are **no mutation routes** (`PUT` or `POST`) defined for updating profile information, farm location/coordinates, or password security.
  3. Visiting `/seller/account/location`, `/seller/auctions/live`, or `/seller/auctions/history` will fail with ViewNotFound exceptions because `resources/views/seller/account/location.blade.php`, `resources/views/seller/auctions/live.blade.php`, and `resources/views/seller/auctions/history.blade.php` do not exist on disk.
  4. Existing views `resources/views/seller/account/profile.blade.php`, `security.blade.php`, and `resources/views/seller/auctions/index.blade.php`, `create.blade.php`, `show.blade.php` are 0-byte blank files.

### 1.2 Model Specifications & Relationships
- **`app/Models/SellerProfile.php`**:
  - Fillables include: `'user_id', 'shop_name', 'shop_slug', 'bio', 'logo_path', 'banner_path', 'status', 'seller_type', 'commission_rate', 'trust_score', 'address', 'city', 'state', 'country', 'postal_code', 'latitude', 'longitude', 'operating_radius_km', 'verified_at', 'gstin', 'pan_number', 'trade_license_number', 'bank_account_number', 'bank_ifsc', 'fssai_number', 'rejection_reason'` (`lines 9-36`).
  - Casts: `'latitude' => 'float'`, `'longitude' => 'float'`, `'operating_radius_km' => 'integer'` (`lines 44-46`).
  - Relationships: Has `user()` (`belongsTo(User::class)` line 116) and `products()` (`hasMany(Product::class, 'seller_id', 'user_id')` line 121).
  - **Defect/Gap**: Does **NOT** define `auctions()` relationship (`hasMany(Auction::class, 'seller_id')`).
  - Helper methods: Already implements `distanceTo(?float $lat, ?float $lng)` Haversine calculation (`lines 92-114`) and city coordinate fallback booted lifecycle listener (`lines 50-65`).

- **`app/Models/Auction.php`**:
  - **Interface Contract**: Foreign key `auctions.seller_id` references `seller_profiles.id` (not `users.id`), as defined in `database/migrations/2026_09_11_000019_create_auctions_table.php:16-17`:
    ```php
    $table->foreignId('seller_id')->constrained('seller_profiles')->cascadeOnDelete();
    ```
  - Fillables: `'product_id', 'seller_id', 'starting_price', 'reserve_price', 'current_price', 'minimum_increment', 'starts_at', 'ends_at', 'status', 'winner_id'` (`lines 14-25`).
  - Relationships:
    - `seller()`: `belongsTo(SellerProfile::class, 'seller_id')` (`line 46-49`).
    - **Defect/Gap**: `sellerProfile()` alias relationship is missing.
    - `product()`: `belongsTo(Product::class)` (`line 41-44`).
    - `winner()`: `belongsTo(User::class, 'winner_id')` (`line 51-54`).
    - `bids()`: `hasMany(AuctionBid::class)` (`line 56-59`).
  - Status enum in DB: `['scheduled', 'live', 'ended', 'cancelled']`.
  - Helper methods:
    - Only `isLive()` exists (`line 70-74`).
    - **Defect/Gap**: `isReserveMet()` is missing.
    - **Defect/Gap**: `canBeCancelled()` is missing.
    - **Defect/Gap**: Scopes for `scopeScheduled()`, `scopeLive()`, `scopeEnded()`, `scopeCancelled()` are missing (only `scopeActive()` exists).

- **`app/Models/AuctionBid.php`**:
  - Table name: `protected $table = 'bids';` (`line 13`).
  - Fillables: `'auction_id', 'user_id', 'amount'` (`lines 17-21`).
  - Casts: `'amount' => 'decimal:2'` (`line 26`).
  - Relationships: `auction()` (`belongsTo(Auction::class)`), `user()` (`belongsTo(User::class)`).
  - Timestamps enabled.

- **`app/Models/User.php`**:
  - Password field: `'password'` in `$fillable` (`line 22`), `$hidden` (`line 32`).
  - Linked profile: `sellerProfile()` (`hasOne(SellerProfile::class)` line 50).
  - Helper: Could benefit from `auctions()` (`hasManyThrough(Auction::class, SellerProfile::class, 'user_id', 'seller_id')`).

### 1.3 Database Schema & Migrations in `database/migrations/`
- `2026_09_11_000019_create_auctions_table.php`:
  - Columns: `id`, `product_id` (FK products), `seller_id` (FK seller_profiles), `starting_price` (decimal 12,2), `reserve_price` (decimal 12,2 nullable), `current_price` (decimal 12,2), `minimum_increment` (decimal 12,2 default 100), `starts_at` (dateTime), `ends_at` (dateTime), `status` (enum: scheduled, live, ended, cancelled), `winner_id` (FK users nullable), timestamps.
  - Foreign key constraint: strictly enforces `seller_id` -> `seller_profiles.id`.
- `2026_09_30_000001_add_seller_panel_fields_to_tables.php`:
  - Successfully added `address` (string 255), `postal_code` (string 20), and `operating_radius_km` (unsignedSmallInteger default 25) to `seller_profiles`.
- **Schema Migration Assessment**:
  - The core database schema for `auctions`, `bids`, and `seller_profiles` is **100% complete** and sufficient for Milestone 5 features.
  - For Feature 35 (Operating Harvest Days & SLA Scheduler: `operating_days`, `order_cutoff_time`, `courier_dispatch_window`) and elevation telemetry, an optional additive migration `2026_09_30_000003_add_sla_fields_to_seller_profiles_table.php` can be added with safe `Schema::hasColumn` checks.

### 1.4 Controllers in `app/Http/Controllers/Seller/`
- Directory currently contains:
  - `SellerDashboardController.php`
  - `SellerOnboardingController.php`
  - `SellerProductController.php`
  - `SellerOrderController.php`
  - `SellerPayoutController.php`
- `SellerProfileController.php` and `SellerAuctionController.php` **DO NOT EXIST**.

---

## 2. Logic Chain

### 2.1 Route Architecture & Controller Separation
1. Based on Observation 1.1, the stub closures in `routes/web.php` fail to process user inputs or persist changes, and missing views trigger fatal errors when requested.
2. Therefore, two dedicated controllers must be introduced under `app/Http/Controllers/Seller/`:
   - `SellerProfileController`: Handles Features 34, 35, 36, and 37 (Shop profile branding, harvest schedule, location telemetry, and password security).
   - `SellerAuctionController`: Handles Features 38, 39, 40, 41, 42, and 43 (Wholesale live terminal, reserve indicators, auction creation, anonymized bid stream, cancellation guardrail, and master auctions registry).
3. The routing in `routes/web.php` must be refactored from closures to standard controller action tuples:
   - Profile routes:
     * `GET  /seller/account/profile` -> `SellerProfileController@profile` (`name('account.profile')`)
     * `PUT  /seller/account/profile` -> `SellerProfileController@updateProfile` (`name('account.profile.update')`)
     * `GET  /seller/account/location` -> `SellerProfileController@location` (`name('account.location')`)
     * `PUT  /seller/account/location` -> `SellerProfileController@updateLocation` (`name('account.location.update')`)
     * `GET  /seller/account/security` -> `SellerProfileController@security` (`name('account.security')`)
     * `PUT  /seller/account/security` -> `SellerProfileController@updatePassword` (`name('account.security.update')`)
     * `GET  /seller/account/settings` -> alias redirecting to `seller.account.profile` (`name('account.settings')`)
   - Auction routes:
     * `GET  /seller/auctions` -> `SellerAuctionController@index` (`name('auctions.index')`)
     * `GET  /seller/auctions/create` -> `SellerAuctionController@create` (`name('auctions.create')`)
     * `POST /seller/auctions` -> `SellerAuctionController@store` (`name('auctions.store')`)
     * `GET  /seller/auctions/live` -> `SellerAuctionController@liveTerminal` (`name('auctions.live')`)
     * `GET  /seller/auctions/history` -> `SellerAuctionController@index` with completed filter or dedicated method (`name('auctions.history')`)
     * `GET  /seller/auctions/{auction}` -> `SellerAuctionController@show` (`name('auctions.show')`)
     * `POST /seller/auctions/{auction}/cancel` -> `SellerAuctionController@cancel` (`name('auctions.cancel')`)

### 2.2 Tenancy Isolation & Interface Contract Adherence
1. In `PROJECT.md` and database migration `2026_09_11_000019_create_auctions_table.php`, `auctions.seller_id` references `seller_profiles.id` (not `users.id`).
2. In contrast, `Product.seller_id` and `SellerOrder.seller_id` reference `users.id`.
3. Consequently, in `SellerAuctionController`:
   - The authenticated seller is resolved via `$user = Auth::guard('seller')->user() ?? Auth::user()`.
   - The profile is `$profile = $user->sellerProfile`.
   - All auction queries **must strictly scope** by `where('seller_id', $profile->id)`.
   - Any attempt by Seller A to view, manage, or cancel an auction belonging to Seller B must be blocked with `abort(403)`.
   - When creating an auction in `store()`, the `seller_id` attribute must be set to `$profile->id`, and the selected `product_id` must be validated to ensure `$product->seller_id === $user->id`.

### 2.3 Cancellation Guardrail Policy Formulation
1. The requirement states:
   - Cancellation must be permitted **ONLY** when `AuctionBid::where('auction_id', $auction->id)->count() === 0`.
   - If bids exist, cancellation must be rejected with 403 or error redirect.
2. In `Auction.php`, `canBeCancelled()` must evaluate:
   ```php
   public function canBeCancelled(): bool
   {
       if (in_array($this->status, ['ended', 'cancelled'])) {
           return false;
       }
       return $this->bids()->count() === 0;
   }
   ```
3. In `SellerAuctionController@cancel`:
   ```php
   if (!$auction->canBeCancelled()) {
       if ($request->wantsJson()) {
           return response()->json(['error' => 'Cancellation blocked: Auction has binding bids or is already closed.'], 403);
       }
       return back()->with('error', 'Cancellation blocked: This auction has active binding bids and cannot be cancelled under APMC trading rules.');
   }
   
   DB::transaction(function () use ($auction) {
       $auction->update(['status' => 'cancelled']);
   });
   
   return redirect()->route('seller.auctions.index')->with('success', "Auction #AUC-{$auction->id} was successfully cancelled.");
   ```

### 2.4 Reserve Price Met Indicator Logic
1. The requirement states:
   - If highest bid >= reserve_price, evaluate as reserve met.
2. In `Auction.php`, `isReserveMet()` must evaluate:
   ```php
   public function isReserveMet(): bool
   {
       if (is_null($this->reserve_price) || (float) $this->reserve_price <= 0) {
           return true;
       }
       
       $highestBid = $this->relationLoaded('bids')
           ? (float) ($this->bids->max('amount') ?? 0)
           : (float) ($this->bids()->max('amount') ?? 0);
           
       if ($highestBid <= 0 && $this->bids()->count() > 0) {
           $highestBid = (float) $this->current_price;
       }
       
       return $highestBid >= (float) $this->reserve_price;
   }
   ```
3. In views, when reserve is met:
   - Display a green badge: `<span class="bg-surface-container-lowest text-on-tertiary-container">Reserve Met</span>`
   - When not met:
     - Display an amber badge: `<span class="bg-secondary-fixed/50 text-on-secondary-container">Reserve Pending</span>`

### 2.5 Password Update & Security Hardening
1. In `SellerProfileController@updatePassword`:
   - Enforce current password verification using `Hash::check($request->current_password, $user->password)`.
   - Enforce password complexity via `Password::min(8)->letters()->mixedCase()->numbers()->symbols()`.
   - Update password via `DB::transaction` updating `$user->password = Hash::make($request->password)`.
   - Flash success feedback and redirect back.

---

## 3. Caveats

1. **Stitch Templates Tab Structure**:
   The stitch template `bazaario_shop_profile_location_account_settings/code.html` provides a single unified page with tabs (`profile`, `location`, `security`). However, the existing routes in `routes/web.php` and sidebar navigation expose individual URLs (`/seller/account/profile`, `/seller/account/location`, `/seller/account/security`). The controller and Blade implementation should support direct navigation to each route (e.g. defaulting active tab or rendering individual blades extending `layouts.seller`), ensuring full URL deep-linking compatibility.
2. **Auction Anti-Sniping & Concurrency**:
   Public bidding is handled in `App\Http\Controllers\AuctionController::placeBid` and `App\Http\Controllers\User\AuctionController::placeBid` using `lockForUpdate()` and a 2-minute anti-sniping extension window. `SellerAuctionController` is responsible for seller monitoring, creation, and cancellation, not public bid placement.
3. **Optional SLA Scheduler Columns**:
   If the database migration for `operating_days`, `order_cutoff_time`, `courier_dispatch_window`, and `elevation` is applied, `SellerProfile` fillables must include these columns. If not applied immediately, the controller must gracefully fall back without throwing SQL missing column exceptions.

---

## 4. Conclusion

The backend architecture for Milestone 5 has clear interfaces and strict contracts:
1. **Models to enhance**:
   - `app/Models/SellerProfile.php`: Add `auctions()` relationship.
   - `app/Models/Auction.php`: Add `sellerProfile()` alias, `isReserveMet()`, `canBeCancelled()`, and status scopes (`scopeScheduled`, `scopeLive`, `scopeEnded`, `scopeCancelled`).
2. **Controllers to create**:
   - `app/Http/Controllers/Seller/SellerProfileController.php` implementing `profile`, `updateProfile`, `location`, `updateLocation`, `security`, `updatePassword`.
   - `app/Http/Controllers/Seller/SellerAuctionController.php` implementing `index`, `create`, `store`, `show`, `liveTerminal`, `cancel`.
3. **Routes to update in `routes/web.php`**:
   - Replace inline closures for `/seller/auctions/*` and `/seller/account/*` with calls to `SellerAuctionController` and `SellerProfileController`. Add `PUT` routes for mutations.
4. **Guardrails**:
   - Strictly reject auction cancellations if `bids()->count() > 0` with HTTP 403 or error flash.
   - Strictly scope all auction actions to the authenticated seller's profile ID (`auctions.seller_id === $sellerProfile->id`).
5. **Views to implement**:
   - `resources/views/seller/account/profile.blade.php`, `location.blade.php`, `security.blade.php`.
   - `resources/views/seller/auctions/index.blade.php`, `create.blade.php`, `live.blade.php`, `show.blade.php`.

---

## 5. Verification Method

### 5.1 Verification Commands
1. Check routes:
   ```bash
   php artisan route:list --path=seller/account
   php artisan route:list --path=seller/auctions
   ```
   *Expected*: All routes point to `SellerProfileController` and `SellerAuctionController` action methods.

2. Syntax check:
   ```bash
   php -l app/Models/SellerProfile.php
   php -l app/Models/Auction.php
   php -l app/Http/Controllers/Seller/SellerProfileController.php
   php -l app/Http/Controllers/Seller/SellerAuctionController.php
   php -l routes/web.php
   ```

3. Regression tests run:
   ```bash
   php artisan test tests/Feature/Seller/SellerDashboardTest.php
   php artisan test tests/Feature/Seller/SellerOrderAndPayoutTest.php
   php artisan test tests/Feature/Seller/SellerProductManagementTest.php
   ```

4. Milestone 5 Feature Test:
   Create and execute `tests/Feature/Seller/SellerAuctionAndProfileTest.php` covering:
   - Tier 1: Route access (profile, location, security, auctions index, create, show, live terminal) returning HTTP 200.
   - Tier 2: Tenancy isolation: Seller cannot see or cancel another seller's auctions.
   - Tier 3: Cancellation guardrail: Allowed when bids == 0; strictly rejected (403/redirect) when bids > 0.
   - Tier 3: Reserve price met: Returns true when highest bid >= reserve_price, false otherwise.
   - Tier 4: Password update: Verifies old password, rejects incorrect old password, hashes new password with complexity.
   - Tier 4: Location update: Updates Lat/Lng, address, operating radius with validation.

### 5.2 Invalidation Conditions
- If `auctions.seller_id` is queried using `user_id` instead of `seller_profiles.id`, multi-tenant isolation will fail.
- If an auction with existing bids can be cancelled, the financial/regulatory guardrail is violated.
- If unapproved sellers can create or manage auctions, the approval gate is violated.
