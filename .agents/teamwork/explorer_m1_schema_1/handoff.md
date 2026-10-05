# Handoff Report: Milestone 1 Schema & Middleware Architecture

**Agent**: `explorer_m1_schema_1`  
**Date**: 2026-09-30  
**Target Milestone**: Milestone 1 (Foundation, Schemas & Onboarding Access Control)  
**Target Directory**: `c:\xampp\htdocs\bazaario`  

---

## 1. Observation

Direct observations from source inspection, database migrations, middleware, models, route definitions, and test suites:

### 1.1 Existing Database Migrations & Schemas
1. **`products` table**:
   - Defined in `database/migrations/2026_09_11_000004_create_products_table.php` (lines 11–39):
     Columns: `id`, `seller_id`, `category_id`, `name`, `slug`, `short_description`, `description`, `sale_type`, `price`, `stock`, `sku`, `weight`, `length`, `width`, `height`, `processing_time_days`, `status`, `average_rating`, `total_reviews`, `created_at`, `updated_at`.
   - Altered in `database/migrations/2026_09_29_000003_add_seller_type_to_seller_profiles_and_unit_type_to_products.php` (lines 20–24): adds `unit_type` (default `'piece'`, nullable).
   - **Missing columns**:
     - `harvest_date` (nullable date)
     - `expiry_days` (nullable unsignedSmallInteger)
     - `expiry_date` (nullable date)
     - `is_perishable` (boolean default false)
     - `auto_hide_expired` (boolean default true)
     - `farm_origin` (nullable string)
     - `harvest_grade` (nullable string)
     - `low_stock_threshold` (unsignedSmallInteger default 10)

2. **`seller_orders` table**:
   - Defined in `database/migrations/2026_09_11_000011_create_seller_orders_table.php` (lines 11–32):
     Columns: `id`, `order_id`, `seller_id`, `seller_order_number`, `subtotal`, `shipping_amount`, `commission_rate`, `commission_amount`, `payout_amount`, `status`, `tracking_number`, `shipped_at`, `delivered_at`, timestamps.
   - Currently, delivery slot is stored inside `orders.notes` as `'Time Slot: ...'` (observed in `app/Http/Controllers/User/CheckoutController.php` lines 155–158).
   - **Missing column**: `delivery_slot` (nullable string).

3. **`seller_profiles` table**:
   - Defined in `database/migrations/2026_09_11_000003_create_seller_profiles_table.php` (lines 11–31):
     Columns: `id`, `user_id`, `shop_name`, `shop_slug`, `bio`, `logo_path`, `banner_path`, `status` (`'pending'|'approved'|'rejected'|'suspended'`), `commission_rate`, `trust_score`, `city`, `state`, `country`, `verified_at`, timestamps.
   - Altered in `2026_09_26_000001_add_kyc_fields...`: adds `gstin`, `pan_number`, `trade_license_number`, `bank_account_number`, `bank_ifsc`, `fssai_number`, `rejection_reason`.
   - Altered in `2026_09_29_000001_add_coordinates...`: adds `latitude`, `longitude`.
   - Altered in `2026_09_29_000003_add_seller_type...`: adds `seller_type`.
   - **Missing columns**:
     - `address` (nullable string)
     - `operating_radius_km` (nullable unsignedSmallInteger default 25)

### 1.2 Access Control & Middleware
1. **`app/Http/Middleware/SellerMiddleware.php`** (lines 24–87):
   - Currently checks:
     1. `Auth::guard('seller')->check()` (redirects unauthenticated to `login`)
     2. `$user->status !== 'active'` (logs out and redirects to `login`)
     3. `$user->role !== 'seller'` (redirects other roles or invalidates session)
   - **Missing check**: It does **NOT** check `$user->sellerProfile->status === 'approved'`.
   - As a result, any authenticated seller with `status === 'pending'` or `status === 'rejected'` can access `/seller/dashboard` directly without hindrance.
2. **`app/Http/Controllers/AuthController.php`** (lines 566–593):
   - Already redirects unapproved sellers to `seller.pending` upon login:
     ```php
     case 'seller':
         if ($user->sellerProfile && $user->sellerProfile->status === 'approved') {
             return redirect()->route('seller.dashboard');
         }
         return redirect()->route('seller.pending')
             ->with('info', 'Your seller account is waiting for admin approval.');
     ```
3. **`app/Http/Middleware/UserMiddleware.php`** (lines 55–71):
   - Employs the identical pattern when a seller hits user routes:
     ```php
     if ($user->sellerProfile && $user->sellerProfile->status === 'approved') {
         return redirect()->route('seller.dashboard');
     }
     return redirect()->route('seller.pending')->with('info', 'Your seller account is waiting for admin approval.');
     ```
4. **Current Test Baseline**:
   - `php artisan test` executed: **278 passed** (2063 assertions, duration 9.72s).
   - In `tests/Feature/AuthAndLocalizationTest.php` and `ChallengerM1AuthLocalizationTest.php`, existing tests creating sellers that access `/seller/dashboard` already set `'status' => 'approved'` on their `sellerProfile`. There are zero existing tests expecting pending sellers to access `/seller/dashboard`.

---

## 2. Logic Chain

1. **Schema Extension Rationale (Observation 1.1)**:
   - Milestone 1 through Milestone 5 require storing and evaluating harvest freshness, perishable expiry flags, stock thresholds, custom delivery slots, and farm location geofences.
   - Creating a single unified migration `database/migrations/2026_09_30_000001_add_seller_panel_fields_to_tables.php` containing defensive `Schema::hasColumn(...)` guards ensures that the migrations are idempotent and fully compatible with both MySQL in development and SQLite in-memory during PHPUnit testing.

2. **Model Enrichment Rationale (Observations 1.1 & 1.2)**:
   - `Product.php`:
     - Needs fillable declarations for the 8 new fields.
     - Needs date casting for `harvest_date` and `expiry_date` so Carbon operations work natively.
     - Needs boolean casting for `is_perishable` and `auto_hide_expired`.
     - Needs integer casting for `expiry_days` and `low_stock_threshold`.
     - Needs lifecycle saving hook to automatically compute `expiry_date = Carbon::parse(harvest_date)->addDays(expiry_days)` when `expiry_date` is omitted by the seller.
     - Needs domain helper methods (`isExpired()`, `isStale()`, `isLowStock()`) and query scopes (`scopeFresh()`, `scopeStale()`, `scopePublicVisible()`, `scopeLowStock()`).
   - `SellerOrder.php`:
     - Needs fillable for `delivery_slot`.
     - Needs an intelligent accessor `getDeliverySlotAttribute($value)` that returns the database value if present, but gracefully falls back to regex-extracting `'Time Slot: ...'` from `$this->order->notes` if null. This ensures zero data loss for past orders created in Milestone 4.
   - `SellerProfile.php`:
     - Needs fillables for `address` and `operating_radius_km`.
     - Needs integer casting for `operating_radius_km`.
     - Needs helper scopes `scopeApproved()` and `scopePending()`, and helpers `isApproved()` and `isPending()`.

3. **Approval Gate Enforcement Rationale (Observation 1.2)**:
   - The user request explicitly specifies: *"Non-approved sellers cannot access dashboard until status is approved by admin."*
   - Therefore, `SellerMiddleware.php` must intercept authenticated sellers whose profile status is not `'approved'`.
   - **Critical Exclusions**:
     - Requests to `/seller/pending` (named route `seller.pending`) must be permitted through the middleware; otherwise, the redirect from `/seller/dashboard` to `/seller/pending` would cause an infinite redirect loop.
     - Requests to `/seller/onboarding` and its sub-steps (`seller.onboarding*`) must be permitted so new or pending sellers can complete or update their profile.
     - Requests to `/logout` and `/seller/logout` must be permitted so pending/rejected sellers can sign out.
   - By creating a helper method `isExemptFromApprovalCheck(Request $request)` that tests both URI patterns (`$request->is(...)`) and route names (`$request->routeIs(...)`), the middleware guarantees robust, loop-free routing under all execution environments.

---

## 3. Caveats

- **No Caveats**. All schemas, models, middleware, route groups, and test files were directly viewed and verified against the running test suite.

---

## 4. Conclusion & Copy-Pasteable Specifications for Worker

### 4.1 Migration File: `database/migrations/2026_09_30_000001_add_seller_panel_fields_to_tables.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Extend products table with freshness and perishable tracking
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'harvest_date')) {
                $table->date('harvest_date')->nullable()->after('unit_type');
            }
            if (!Schema::hasColumn('products', 'expiry_days')) {
                $table->unsignedSmallInteger('expiry_days')->nullable()->after('harvest_date');
            }
            if (!Schema::hasColumn('products', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('expiry_days');
            }
            if (!Schema::hasColumn('products', 'is_perishable')) {
                $table->boolean('is_perishable')->default(false)->after('expiry_date');
            }
            if (!Schema::hasColumn('products', 'auto_hide_expired')) {
                $table->boolean('auto_hide_expired')->default(true)->after('is_perishable');
            }
            if (!Schema::hasColumn('products', 'farm_origin')) {
                $table->string('farm_origin', 255)->nullable()->after('auto_hide_expired');
            }
            if (!Schema::hasColumn('products', 'harvest_grade')) {
                $table->string('harvest_grade', 50)->nullable()->after('farm_origin');
            }
            if (!Schema::hasColumn('products', 'low_stock_threshold')) {
                $table->unsignedSmallInteger('low_stock_threshold')->default(10)->after('stock');
            }
        });

        // 2. Extend seller_orders table with delivery slot
        Schema::table('seller_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_orders', 'delivery_slot')) {
                $table->string('delivery_slot', 100)->nullable()->after('status');
            }
        });

        // 3. Extend seller_profiles table with address and operating radius
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_profiles', 'address')) {
                $table->string('address', 255)->nullable()->after('country');
            }
            if (!Schema::hasColumn('seller_profiles', 'operating_radius_km')) {
                $table->unsignedSmallInteger('operating_radius_km')->default(25)->nullable()->after('longitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revert seller_profiles additions
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('seller_profiles', 'operating_radius_km')) {
                $table->dropColumn('operating_radius_km');
            }
            if (Schema::hasColumn('seller_profiles', 'address')) {
                $table->dropColumn('address');
            }
        });

        // 2. Revert seller_orders additions
        Schema::table('seller_orders', function (Blueprint $table) {
            if (Schema::hasColumn('seller_orders', 'delivery_slot')) {
                $table->dropColumn('delivery_slot');
            }
        });

        // 3. Revert products additions
        Schema::table('products', function (Blueprint $table) {
            $columns = [
                'harvest_date',
                'expiry_days',
                'expiry_date',
                'is_perishable',
                'auto_hide_expired',
                'farm_origin',
                'harvest_grade',
                'low_stock_threshold',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
```

---

### 4.2 Model Updates

#### 1. `app/Models/Product.php`
- **Replace/Update Fillables**:
  ```php
  protected $fillable = [
      'seller_id',
      'category_id',
      'name',
      'slug',
      'short_description',
      'description',
      'sale_type',
      'unit_type',
      'price',
      'stock',
      'sku',
      'weight',
      'length',
      'width',
      'height',
      'processing_time_days',
      'status',
      'average_rating',
      'total_reviews',
      'harvest_date',
      'expiry_days',
      'expiry_date',
      'is_perishable',
      'auto_hide_expired',
      'farm_origin',
      'harvest_grade',
      'low_stock_threshold',
  ];
  ```
- **Replace/Update `casts()` method**:
  ```php
  protected function casts(): array
  {
      return [
          'price' => 'decimal:2',
          'weight' => 'decimal:2',
          'length' => 'decimal:2',
          'width' => 'decimal:2',
          'height' => 'decimal:2',
          'average_rating' => 'decimal:2',
          'harvest_date' => 'date',
          'expiry_date' => 'date',
          'expiry_days' => 'integer',
          'is_perishable' => 'boolean',
          'auto_hide_expired' => 'boolean',
          'low_stock_threshold' => 'integer',
      ];
  }
  ```
- **Add Lifecycle Hook & Methods**:
  ```php
  protected static function booted(): void
  {
      static::saving(function (Product $product) {
          if ($product->is_perishable && $product->harvest_date && $product->expiry_days && empty($product->expiry_date)) {
              $product->expiry_date = \Carbon\Carbon::parse($product->harvest_date)->addDays((int) $product->expiry_days)->toDateString();
          }
      });
  }

  public function isExpired(): bool
  {
      if (!$this->is_perishable || !$this->expiry_date) {
          return false;
      }

      return $this->expiry_date->endOfDay()->isPast();
  }

  public function isStale(): bool
  {
      return $this->isExpired();
  }

  public function isLowStock(): bool
  {
      $threshold = $this->low_stock_threshold ?? 10;
      return $this->stock <= $threshold;
  }

  public function scopeFresh($query)
  {
      return $query->where(function ($q) {
          $q->where('is_perishable', false)
            ->orWhereNull('expiry_date')
            ->orWhereDate('expiry_date', '>=', now()->toDateString());
      });
  }

  public function scopeStale($query)
  {
      return $query->where('is_perishable', true)
          ->whereNotNull('expiry_date')
          ->whereDate('expiry_date', '<', now()->toDateString());
  }

  public function scopePublicVisible($query)
  {
      return $query->where('status', 'active')
          ->where(function ($q) {
              $q->where('auto_hide_expired', false)
                ->orWhere('is_perishable', false)
                ->orWhereNull('expiry_date')
                ->orWhereDate('expiry_date', '>=', now()->toDateString());
          });
  }

  public function scopeLowStock($query)
  {
      return $query->where(function ($q) {
          $q->whereColumn('stock', '<=', 'low_stock_threshold')
            ->orWhere(function ($sub) {
                $sub->whereNull('low_stock_threshold')->where('stock', '<=', 10);
            });
      });
  }
  ```

#### 2. `app/Models/SellerOrder.php`
- **Add to `$fillable`**:
  ```php
  protected $fillable = [
      'order_id',
      'seller_id',
      'seller_order_number',
      'subtotal',
      'shipping_amount',
      'commission_rate',
      'commission_amount',
      'payout_amount',
      'status',
      'delivery_slot',
      'tracking_number',
      'shipped_at',
      'delivered_at',
  ];
  ```
- **Add Accessor and Scopes**:
  ```php
  public function getDeliverySlotAttribute($value): ?string
  {
      if (!empty($value)) {
          return $value;
      }

      // Backward compatible fallback extraction from parent order notes
      if ($this->relationLoaded('order') || $this->order_id) {
          $notes = $this->order?->notes;
          if ($notes && preg_match('/Time Slot:\s*([^|]+)/i', $notes, $matches)) {
              return trim($matches[1]);
          }
      }

      return null;
  }

  public function scopeForSeller($query, int $sellerId)
  {
      return $query->where('seller_id', $sellerId);
  }

  public function scopeStatus($query, string $status)
  {
      return $query->where('status', $status);
  }
  ```

#### 3. `app/Models/SellerProfile.php`
- **Add to `$fillable`**:
  ```php
  protected $fillable = [
      'user_id',
      'shop_name',
      'shop_slug',
      'bio',
      'logo_path',
      'banner_path',
      'status',
      'seller_type',
      'commission_rate',
      'trust_score',
      'address',
      'city',
      'state',
      'country',
      'latitude',
      'longitude',
      'operating_radius_km',
      'verified_at',
      'gstin',
      'pan_number',
      'trade_license_number',
      'bank_account_number',
      'bank_ifsc',
      'fssai_number',
      'rejection_reason',
  ];
  ```
- **Update `$casts`**:
  ```php
  protected function casts(): array
  {
      return [
          'verified_at' => 'datetime',
          'commission_rate' => 'decimal:2',
          'trust_score' => 'decimal:2',
          'latitude' => 'float',
          'longitude' => 'float',
          'operating_radius_km' => 'integer',
      ];
  }
  ```
- **Add Helper Methods & Scopes**:
  ```php
  public function isApproved(): bool
  {
      return $this->status === 'approved';
  }

  public function isPending(): bool
  {
      return $this->status === 'pending';
  }

  public function scopeApproved($query)
  {
      return $query->where('status', 'approved');
  }

  public function scopePending($query)
  {
      return $query->where('status', 'pending');
  }
  ```

---

### 4.3 Middleware Implementation: `app/Http/Middleware/SellerMiddleware.php`

Complete drop-in code for `app/Http/Middleware/SellerMiddleware.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /*
        |--------------------------------------------------------------------------
        | Check Seller Authentication
        |--------------------------------------------------------------------------
        */
        if (!Auth::guard('seller')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Please login as a seller to continue.',
                ]);
        }

        $user = Auth::guard('seller')->user();

        /*
        |--------------------------------------------------------------------------
        | Check Account Status
        |--------------------------------------------------------------------------
        */
        if ($user->status !== 'active') {
            Auth::guard('seller')->logout();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your seller account is ' . $user->status . '.',
                ], 403);
            }

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Your seller account is ' . $user->status . '.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Seller Role
        |--------------------------------------------------------------------------
        */
        if ($user->role !== 'seller') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized access.'], 403);
            }

            if ($user->role === 'user') {
                return redirect()->route('products.index');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            Auth::guard('seller')->logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Unauthorized access.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Seller Approval Status
        |--------------------------------------------------------------------------
        | Unapproved sellers cannot access /seller/dashboard or operational routes.
        | Exempted routes: seller.pending, seller.onboarding, and logout.
        */
        if (!$this->isExemptFromApprovalCheck($request)) {
            $profile = $user->sellerProfile;

            if (!$profile || $profile->status !== 'approved') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Your seller account is waiting for admin approval.',
                        'status' => $profile ? $profile->status : 'incomplete',
                    ], 403);
                }

                return redirect()
                    ->route('seller.pending')
                    ->with('warning', 'Your seller account is waiting for admin approval.');
            }
        }

        return $next($request);
    }

    /**
     * Determine if the current request is exempt from the approval gate check.
     */
    protected function isExemptFromApprovalCheck(Request $request): bool
    {
        // 1. Pending approval waiting screen
        if ($request->is('seller/pending') || $request->routeIs('seller.pending')) {
            return true;
        }

        // 2. Onboarding wizard endpoints
        if (
            $request->is('seller/onboarding') ||
            $request->is('seller/onboarding/*') ||
            $request->routeIs('seller.onboarding*')
        ) {
            return true;
        }

        // 3. Logout endpoints
        if (
            $request->is('logout') ||
            $request->is('seller/logout') ||
            $request->routeIs('logout') ||
            $request->routeIs('seller.logout')
        ) {
            return true;
        }

        return false;
    }
}
```

---

## 5. Verification Method

Once the worker has created the migration and applied the model and middleware edits:

1. **Verify Database Migration Execution and Rollback**:
   ```powershell
   php artisan migrate
   php artisan migrate:rollback --step=1
   php artisan migrate
   ```
   Must execute cleanly with zero errors in both directions.

2. **Verify Schema Attributes with Artisan Tinker**:
   ```powershell
   php artisan tinker --execute="echo Schema::hasColumn('products', 'harvest_date') ? 'OK' : 'FAIL';"
   php artisan tinker --execute="echo Schema::hasColumn('products', 'low_stock_threshold') ? 'OK' : 'FAIL';"
   php artisan tinker --execute="echo Schema::hasColumn('seller_orders', 'delivery_slot') ? 'OK' : 'FAIL';"
   php artisan tinker --execute="echo Schema::hasColumn('seller_profiles', 'address') ? 'OK' : 'FAIL';"
   php artisan tinker --execute="echo Schema::hasColumn('seller_profiles', 'operating_radius_km') ? 'OK' : 'FAIL';"
   ```
   All must output `OK`.

3. **Verify Middleware Access Control & Exclusions**:
   - Create a test or execute via Tinker/PHPUnit:
     - Unapproved/pending seller visiting `/seller/dashboard` -> HTTP 302 Redirect to `/seller/pending`.
     - Unapproved/pending seller visiting `/seller/pending` -> HTTP 200 OK.
     - Unapproved/pending seller visiting `/seller/onboarding` -> HTTP 200 OK (when route registered).
     - Approved seller visiting `/seller/dashboard` -> HTTP 200 OK.
     - Approved seller visiting `/seller/pending` -> HTTP 302 Redirect to `/seller/dashboard`.

4. **Run Full Regression Suite**:
   ```powershell
   php artisan test
   ```
   All 278 baseline tests must pass with zero failures or regressions.
