# Milestone 1: Routes & Controller Design Specification (Seller Onboarding & Access Control)

**Agent Role**: Routes & Controller Explorer for Milestone 1 (`explorer_m1_routes_1`)  
**Date**: 2026-09-30  
**Target Project**: Bazaario Marketplace (`c:\xampp\htdocs\bazaario`)  
**Output Target**: Recommendations for `App\Http\Controllers\Seller\SellerOnboardingController` and `routes/web.php`  
**Reference Templates**: `C:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal\bazaario_seller_onboarding_approval\code.html`  

---

## 1. Observation

### 1.1 Existing Routes & Route Groups in `routes/web.php`
Direct inspection of `routes/web.php` (lines 188–206) reveals:
```php
// ── Seller Panel ────────────────────────────────────────────────────────────

Route::prefix('seller')->name('seller.')->group(function () {

    Route::middleware(['auth:seller', 'seller'])->group(function () {

        Route::get('/pending', function () {
            $user = Auth::guard('seller')->user();
            if ($user && $user->role === 'seller' && $user->sellerProfile && $user->sellerProfile->status === 'approved') {
                return redirect()->route('seller.dashboard');
            }
            return view('seller.pending', compact('user'));
        })->name('pending');

        Route::get('/dashboard', function () {
            $user = Auth::guard('seller')->user();
            return view('seller.dashboard', compact('user'));
        })->name('dashboard');
    });
});
```
- Only `/seller/pending` and `/seller/dashboard` are defined.
- No onboarding routes (`/seller/onboarding`, `/seller/onboarding/submit`) exist yet.
- The directory `app/Http/Controllers/Seller/` does not yet exist.
- Operational seller routes (products, orders, payouts, auctions, account) are completely absent.

### 1.2 Existing Middleware & Auth Guards
- `config/auth.php` (lines 41–59) defines `seller` guard with session driver and `users` provider.
- `app/Http/Middleware/SellerMiddleware.php` (lines 24–87) currently enforces:
  1. `Auth::guard('seller')->check()` — redirects unauthenticated users to `login`.
  2. `$user->status !== 'active'` — terminates session and redirects to `login`.
  3. `$user->role !== 'seller'` — redirects non-seller users according to their role.
- **Critical Finding**: `SellerMiddleware` does **not** check whether `$user->sellerProfile->status === 'approved'`. If applied indiscriminately to all `/seller/*` routes with an approval check, an unapproved seller visiting `/seller/pending` or `/seller/onboarding` would enter an infinite redirect loop unless those routes are specifically exempted.
- `bootstrap/app.php` (lines 18–22) aliases `'seller'` to `\App\Http\Middleware\SellerMiddleware::class`.

### 1.3 Existing Model & Database Schema (`SellerProfile`)
- Migration files:
  - `database/migrations/2026_09_11_000003_create_seller_profiles_table.php`
  - `database/migrations/2026_09_29_000001_add_coordinates_to_seller_profiles_table.php`
  - `database/migrations/2026_09_29_000003_add_seller_type_to_seller_profiles_and_unit_type_to_products.php`
- `app/Models/SellerProfile.php` fillables:
  `user_id`, `shop_name`, `shop_slug`, `bio`, `logo_path`, `banner_path`, `status`, `seller_type`, `commission_rate`, `trust_score`, `city`, `state`, `country`, `latitude`, `longitude`, `verified_at`, `gstin`, `pan_number`, `trade_license_number`, `bank_account_number`, `bank_ifsc`, `fssai_number`, `rejection_reason`.
- Milestone 1 schema extensions add: `address`, `postal_code`, and `operating_radius_km` to `seller_profiles`.

### 1.4 Existing Tests Asserting Seller Route Behavior
- In `tests/Feature/ChallengerM1AuthLocalizationTest.php`:
  - Line 58–60: `GET route('seller.pending')` must return HTTP 200 for pending sellers.
  - Line 125–127: `GET route('seller.dashboard')` returns HTTP 200 for approved sellers.
  - Line 129–131: `GET route('seller.pending')` redirects approved sellers to `route('seller.dashboard')`.
  - Line 485–487: Customer (`role = 'user'`) accessing `seller.dashboard` is redirected to `login`.
  - Line 539–541: Logged-out seller accessing `seller.dashboard` is redirected to `login`.
- In `tests/Feature/AuthAndLocalizationTest.php`:
  - Line 201–219: Guests, customer users, and suspended sellers are rejected from `seller.dashboard`.
- Full test suite baseline: `php artisan test` passes **278 tests (2063 assertions)** in 10.03s.

### 1.5 Stitch Onboarding Template Elements
In `C:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal\bazaario_seller_onboarding_approval\code.html`:
- 6-step progress indicator: Step 1 (Account), Step 2 (Seller Type: `Farmer`, `Kirana Store`, `Dark Store`, `Individual`), Step 3 (Shop/Farm Details & Storefront Image), Step 4 (Location: address, city, state, PIN, Lat/Lng GPS coordinates), Step 5 (Review & Submission Matrix), Step 6 (Approval Gate / Pending Review).
- Submitting Step 5 moves the merchant into the pending verification state (`seller.pending`).

---

## 2. Logic Chain

```
[Observation 1.1 & 1.2: SellerMiddleware protects /seller/* but does not check approval; routes/web.php has only pending & dashboard stubs]
      │
      ▼
[Step 1: Access Control Partitioning]
Two distinct route tiers exist under prefix('seller'):
1. Unapproved-Accessible Routes:
   - GET /seller/onboarding (render wizard)
   - POST /seller/onboarding (submit wizard)
   - GET /seller/pending (dossier review waiting gate)
   Accessible to ANY authenticated seller with role='seller' and status='active', regardless of approval status.
2. Approval-Required Routes:
   - GET /seller/dashboard
   - Operational routes (products, orders, payouts, auctions, account)
   Strictly accessible ONLY when sellerProfile->status === 'approved'.
      │
      ▼
[Step 2: Preventing Redirect Loops]
If approval enforcement is applied universally, pending sellers trying to access /seller/pending will be redirected to /seller/pending indefinitely.
Therefore, approval enforcement must either:
Option A: Be isolated to an inner middleware group (e.g. `middleware('seller.approved')` using `EnsureSellerApproved`), OR
Option B: Be handled inside `SellerMiddleware` with an explicit route exemption whitelist (`seller.pending`, `seller.onboarding`, `seller.onboarding.submit`).
      │
      ▼
[Step 3: showWizard() State Evaluation]
When a seller visits GET /seller/onboarding:
- If unauthenticated or role !== 'seller' → redirect to login.
- If $profile && $profile->status === 'approved' → redirect to seller.dashboard (approved sellers must not re-onboard).
- If $profile && $profile->status === 'pending' → pass $user and $profile to the wizard view so existing fields are pre-populated for review or edits. If query param ?view=pending is present, redirect to seller.pending.
      │
      ▼
[Step 4: submitWizard() Processing & Persistence]
When submitting POST /seller/onboarding:
1. Validate inputs: seller_type, shop_name, bio, storefront_image, address, city, state, postal_code, latitude, longitude.
2. Handle storefront image upload: store in public disk (`storefronts/`), save path to `logo_path` and `banner_path`.
3. Generate clean, unique `shop_slug`.
4. Atomically updateOrCreate `SellerProfile` with `status => 'pending'`.
5. Log activity with context (`Log::info(...)`).
6. Redirect to route('seller.pending') with flash success message.
      │
      ▼
[Step 5: Error Bag & Step Navigation Harmony]
In a multi-step form, a validation failure on Step 4 must not blindly open Step 1.
Blade/Alpine.js must evaluate `$errors->hasAny([...])` to dynamically default `currentStep` to the earliest step with an invalid field, and highlight inputs with `@error('field') border-rose-500 @enderror`.
```

---

## 3. Caveats

1. **Database Schema Dependencies**: The fields `address` and `postal_code` are added to `seller_profiles` in Milestone 1 migration `2026_09_30_000001_add_seller_panel_fields_to_tables.php`. If that migration has not run yet in an isolated test, `SellerProfile::updateOrCreate()` should be guarded using `array_filter` or `Schema::hasColumn` to prevent SQL column-not-found exceptions.
2. **Initial Registration Profile**: During buyer registration as a seller in `AuthController.php` (line 476), a barebones `SellerProfile` is created with `status = 'pending'` and a default shop name. `showWizard()` must recognize this initial state and allow full wizard editing rather than bouncing the merchant away.
3. **Storefront Image Storage Disk**: The uploaded file must be saved using the `public` storage disk (`Storage::disk('public')->put(...)` or `$file->store('storefronts', 'public')`) so it is publicly accessible via `/storage/storefronts/...`.
4. **Existing Test Compatibility**: Existing tests in `ChallengerM1AuthLocalizationTest` directly call `route('seller.pending')` and `route('seller.dashboard')`. The route names, parameter expectations, and redirect behaviors must remain 100% identical.

---

## 4. Conclusion & Recommended Implementations

### 4.1 Controller Specification: `App\Http\Controllers\Seller\SellerOnboardingController`

Recommended path: `app/Http/Controllers/Seller/SellerOnboardingController.php`

```php
<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SellerOnboardingController extends Controller
{
    /**
     * Render the multi-step onboarding wizard.
     *
     * State Machine:
     * - Unauthenticated or non-seller: Redirect to login.
     * - Approved seller: Redirect to seller.dashboard.
     * - Pending / Unsubmitted seller: Render wizard with prefilled user & profile data.
     */
    public function showWizard(Request $request): View|RedirectResponse
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();

        if (!$user || $user->role !== 'seller') {
            return redirect()->route('login');
        }

        $profile = $user->sellerProfile;

        // 1. If already approved, redirect to seller dashboard
        if ($profile && $profile->status === 'approved') {
            return redirect()->route('seller.dashboard');
        }

        // 2. If explicit redirection to pending gate is requested via query
        if ($request->query('view') === 'pending' && $profile && $profile->status === 'pending') {
            return redirect()->route('seller.pending');
        }

        // 3. Render wizard view, passing authenticated user and existing profile (if any)
        $viewName = view()->exists('seller.onboarding.wizard') 
            ? 'seller.onboarding.wizard' 
            : 'seller.onboarding';

        return view($viewName, compact('user', 'profile'));
    }

    /**
     * Handle complete onboarding form submission.
     *
     * Processes seller_type, shop_name, bio, storefront image, address,
     * city, state, postal code, and latitude/longitude coordinates.
     */
    public function submitWizard(Request $request): RedirectResponse
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();

        if (!$user || $user->role !== 'seller') {
            return redirect()->route('login');
        }

        // 1. Strict Server-Side Form Validation
        $validated = $request->validate([
            'seller_type'      => ['required', 'string', Rule::in(['Farmer', 'Kirana Store', 'Dark Store', 'Individual'])],
            'shop_name'        => ['required', 'string', 'min:3', 'max:150'],
            'bio'              => ['nullable', 'string', 'max:1000'],
            'storefront_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Max 5MB
            'address'          => ['required', 'string', 'max:255'],
            'city'             => ['required', 'string', 'max:100'],
            'state'            => ['required', 'string', 'max:100'],
            'country'          => ['nullable', 'string', 'max:100'],
            'postal_code'      => ['required', 'string', 'max:20'],
            'latitude'         => ['required', 'numeric', 'between:-90,90'],
            'longitude'        => ['required', 'numeric', 'between:-180,180'],
        ], [
            'seller_type.required'     => 'Please choose a seller classification (Farmer, Kirana, Dark Store, or Individual).',
            'seller_type.in'           => 'The selected seller classification is invalid.',
            'shop_name.required'       => 'Your shop or farm name is required.',
            'shop_name.min'            => 'Shop or farm name must be at least 3 characters.',
            'storefront_image.image'   => 'The storefront photo must be an image file (JPEG, PNG, JPG, or WEBP).',
            'storefront_image.max'     => 'Storefront image size must not exceed 5MB.',
            'address.required'         => 'Operating street address or farm plot is required.',
            'city.required'            => 'City or town is required.',
            'state.required'           => 'State or province is required.',
            'postal_code.required'     => 'Postal / PIN code is required.',
            'latitude.required'        => 'Latitude coordinate is required for hyperlocal discovery.',
            'latitude.between'         => 'Latitude must be between -90 and 90 degrees.',
            'longitude.required'       => 'Longitude coordinate is required for hyperlocal discovery.',
            'longitude.between'        => 'Longitude must be between -180 and 180 degrees.',
        ]);

        // 2. Handle Storefront Image Upload to Public Disk
        $imagePath = null;
        if ($request->hasFile('storefront_image')) {
            $file = $request->file('storefront_image');
            $filename = 'storefront_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('storefronts', $filename, 'public');
        }

        // 3. Generate Deterministic Unique Shop Slug
        $baseSlug = Str::slug($validated['shop_name']);
        if (empty($baseSlug)) {
            $baseSlug = 'shop-' . $user->id;
        }
        $slug = $baseSlug;
        $counter = 1;
        while (SellerProfile::where('shop_slug', $slug)->where('user_id', '!=', $user->id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        // 4. Atomic Database Mutation
        $profile = DB::transaction(function () use ($user, $validated, $slug, $imagePath) {
            $attributes = [
                'shop_name'       => $validated['shop_name'],
                'shop_slug'       => $slug,
                'seller_type'     => $validated['seller_type'],
                'bio'             => $validated['bio'] ?? null,
                'city'            => $validated['city'],
                'state'           => $validated['state'],
                'country'         => $validated['country'] ?? 'India',
                'latitude'        => (float) $validated['latitude'],
                'longitude'       => (float) $validated['longitude'],
                'status'          => 'pending',
            ];

            // Only attach columns if they exist in schema or model fillables
            if (Schema::hasColumn('seller_profiles', 'address')) {
                $attributes['address'] = $validated['address'];
            }
            if (Schema::hasColumn('seller_profiles', 'postal_code')) {
                $attributes['postal_code'] = $validated['postal_code'];
            }

            if ($imagePath) {
                $attributes['logo_path'] = $imagePath;
                $attributes['banner_path'] = $imagePath;
            }

            return SellerProfile::updateOrCreate(
                ['user_id' => $user->id],
                $attributes
            );
        });

        // 5. Activity Logging
        Log::info('Seller submitted onboarding wizard dossier', [
            'user_id'      => $user->id,
            'email'        => $user->email,
            'seller_type'  => $profile->seller_type,
            'shop_name'    => $profile->shop_name,
            'shop_slug'    => $profile->shop_slug,
            'city'         => $profile->city,
            'coordinates'  => [$profile->latitude, $profile->longitude],
            'submitted_at' => now()->toIso8601String(),
        ]);

        // 6. Redirect to Pending Approval Gate
        return redirect()->route('seller.pending')
            ->with('success', 'Your onboarding dossier has been submitted successfully and is awaiting administrator verification.');
    }

    /**
     * Render the pending approval review dossier page.
     */
    public function pending(Request $request): View|RedirectResponse
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();

        if (!$user || $user->role !== 'seller') {
            return redirect()->route('login');
        }

        $profile = $user->sellerProfile;

        // If approved, redirect immediately to dashboard
        if ($profile && $profile->status === 'approved') {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.pending', compact('user', 'profile'));
    }
}
```

---

### 4.2 Route Group Specification for `routes/web.php`

Recommended replacement for lines 187–206 in `routes/web.php`:

```php
use App\Http\Controllers\Seller\SellerOnboardingController;
use App\Http\Controllers\Seller\SellerDashboardController;

// ── Seller Panel ────────────────────────────────────────────────────────────

Route::prefix('seller')->name('seller.')->middleware(['auth:seller', 'seller'])->group(function () {

    // ── Unapproved-Accessible Routes (Onboarding & Waiting Gate) ─────────────
    Route::get('/onboarding', [SellerOnboardingController::class, 'showWizard'])->name('onboarding');
    Route::post('/onboarding', [SellerOnboardingController::class, 'submitWizard'])->name('onboarding.submit');
    Route::get('/pending', [SellerOnboardingController::class, 'pending'])->name('pending');

    // ── Approval-Required Routes (Guarded by seller.approved) ────────────────
    Route::middleware('seller.approved')->group(function () {
        Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');

        // Products & Inventory Management (Placeholders for M3)
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', fn () => view('seller.products.index'))->name('index');
            Route::get('/create', fn () => view('seller.products.create'))->name('create');
            Route::post('/', fn () => redirect()->route('seller.products.index'))->name('store');
            Route::get('/inventory', fn () => view('seller.products.inventory'))->name('inventory');
            Route::get('/{product}', fn () => view('seller.products.show'))->name('show');
            Route::get('/{product}/edit', fn () => view('seller.products.edit'))->name('edit');
            Route::put('/{product}', fn () => redirect()->route('seller.products.index'))->name('update');
            Route::delete('/{product}', fn () => redirect()->route('seller.products.index'))->name('destroy');
        });

        // Orders & Fulfillment Management (Placeholders for M4)
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', fn () => view('seller.orders.index'))->name('index');
            Route::get('/{order}', fn () => view('seller.orders.show'))->name('show');
            Route::patch('/{order}/status', fn () => back())->name('update-status');
        });

        // Payouts & Commission Breakdown (Placeholders for M4)
        Route::prefix('payouts')->name('payouts.')->group(function () {
            Route::get('/', fn () => view('seller.payouts.index'))->name('index');
            Route::get('/{payout}', fn () => view('seller.payouts.show'))->name('show');
        });

        // Live Wholesale Auctions (Placeholders for M5)
        Route::prefix('auctions')->name('auctions.')->group(function () {
            Route::get('/', fn () => view('seller.auctions.index'))->name('index');
            Route::get('/create', fn () => view('seller.auctions.create'))->name('create');
            Route::post('/', fn () => redirect()->route('seller.auctions.index'))->name('store');
            Route::get('/{auction}', fn () => view('seller.auctions.show'))->name('show');
            Route::get('/{auction}/live', fn () => view('seller.auctions.live'))->name('live');
            Route::post('/{auction}/cancel', fn () => back())->name('cancel');
        });

        // Shop Profile, Location Telemetry & Security (Placeholders for M5)
        Route::prefix('account')->name('account.')->group(function () {
            Route::get('/profile', fn () => view('seller.account.profile'))->name('profile');
            Route::get('/location', fn () => view('seller.account.location'))->name('location');
            Route::get('/security', fn () => view('seller.account.security'))->name('security');
        });
    });
});
```

---

### 4.3 Approval Gate Middleware Specification: `EnsureSellerApproved`

Recommended path: `app/Http/Middleware/EnsureSellerApproved.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerApproved
{
    /**
     * Handle an incoming request.
     *
     * Ensures the authenticated seller has an 'approved' SellerProfile status.
     * Unapproved sellers are redirected to route('seller.pending') with a flash notice.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();

        if (!$user || $user->role !== 'seller') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized seller session.'], 403);
            }
            return redirect()->route('login');
        }

        $profile = $user->sellerProfile;

        if (!$profile || $profile->status !== 'approved') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your seller account is awaiting administrator approval.',
                    'status'  => $profile?->status ?? 'pending',
                ], 403);
            }

            return redirect()->route('seller.pending')
                ->with('info', 'Your seller account is currently under administrative review.');
        }

        return $next($request);
    }
}
```

**Registration in `bootstrap/app.php`**:
```php
$middleware->alias([
    'admin'           => \App\Http\Middleware\AdminMiddleware::class,
    'user'            => \App\Http\Middleware\UserMiddleware::class,
    'seller'          => \App\Http\Middleware\SellerMiddleware::class,
    'seller.approved' => \App\Http\Middleware\EnsureSellerApproved::class,
]);
```

*Note on `SellerMiddleware` backward-compatibility*: If `SellerMiddleware` is also configured to enforce approval directly, it MUST include this exemption whitelist to prevent redirect loops:
```php
if ($request->routeIs('seller.pending', 'seller.onboarding', 'seller.onboarding.submit')) {
    return $next($request);
}
```

---

### 4.4 Form Validation Rules & Error Bag Handling

| Input Field | Type | Validation Rules | Custom Error Message |
|:---|:---|:---|:---|
| `seller_type` | string (radio/card) | `['required', 'string', Rule::in(['Farmer', 'Kirana Store', 'Dark Store', 'Individual'])]` | `"Please choose a seller classification (Farmer, Kirana, Dark Store, or Individual)."` |
| `shop_name` | string | `['required', 'string', 'min:3', 'max:150']` | `"Your shop or farm name is required."` |
| `bio` | string (textarea) | `['nullable', 'string', 'max:1000']` | `"Business description cannot exceed 1000 characters."` |
| `storefront_image` | file upload | `['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120']` | `"Storefront photo must be an image file (JPEG, PNG, JPG, or WEBP) under 5MB."` |
| `address` | string | `['required', 'string', 'max:255']` | `"Operating street address or farm plot is required."` |
| `city` | string | `['required', 'string', 'max:100']` | `"City or town is required."` |
| `state` | string | `['required', 'string', 'max:100']` | `"State or province is required."` |
| `country` | string | `['nullable', 'string', 'max:100']` | — (Defaults to 'India') |
| `postal_code` | string | `['required', 'string', 'max:20']` | `"Postal / PIN code is required."` |
| `latitude` | numeric | `['required', 'numeric', 'between:-90,90']` | `"Latitude coordinate must be between -90 and 90 degrees."` |
| `longitude` | numeric | `['required', 'numeric', 'between:-180,180']` | `"Longitude coordinate must be between -180 and 180 degrees."` |

#### Step-Aware Error Resolution for Alpine.js & Blade
To ensure the multi-step wizard automatically opens the tab where validation failed:
```blade
@php
    $initialStep = 1;
    if ($errors->hasAny(['seller_type'])) {
        $initialStep = 2;
    } elseif ($errors->hasAny(['shop_name', 'bio', 'storefront_image'])) {
        $initialStep = 3;
    } elseif ($errors->hasAny(['address', 'city', 'state', 'postal_code', 'latitude', 'longitude'])) {
        $initialStep = 4;
    } elseif ($errors->any()) {
        $initialStep = 5;
    }
@endphp

<div x-data="{ currentStep: {{ $initialStep }}, sellerType: '{{ old('seller_type', $profile?->seller_type ?? 'Farmer') }}' }">
    <!-- Wizard Step Content -->
</div>
```

#### Field-Level Error Highlighting Pattern
```blade
<div class="space-y-1">
    <label for="shop_name" class="block text-xs font-semibold font-heading text-brand-slate uppercase tracking-wider">
        Shop / Farm Name <span class="text-rose-500">*</span>
    </label>
    <input type="text" id="shop_name" name="shop_name" 
           value="{{ old('shop_name', $profile?->shop_name ?? '') }}" 
           class="w-full px-4 py-3.5 bg-brand-subtle border @error('shop_name') border-rose-500 ring-2 ring-rose-200 @else border-brand-outline @enderror text-brand-slate text-sm rounded-[14px] focus:outline-none focus:border-brand-amber transition"
           required>
    @error('shop_name')
        <p class="text-xs text-rose-600 flex items-center gap-1 font-medium mt-1">
            <span class="material-symbols-outlined text-[14px]">error</span>
            {{ $message }}
        </p>
    @enderror
</div>
```

---

## 5. Verification Method

### 5.1 Route & Middleware Registration Verification
Verify that all routes compile and load without collisions:
```powershell
php artisan route:list --path=seller
```
*Expected Output*:
- `GET seller/onboarding` -> `seller.onboarding` -> `SellerOnboardingController@showWizard`
- `POST seller/onboarding` -> `seller.onboarding.submit` -> `SellerOnboardingController@submitWizard`
- `GET seller/pending` -> `seller.pending` -> `SellerOnboardingController@pending`
- `GET seller/dashboard` -> `seller.dashboard` -> `SellerDashboardController@index` (protected by `seller.approved`)

### 5.2 PHP Syntax Validation
```powershell
php -l app/Http/Controllers/Seller/SellerOnboardingController.php
php -l app/Http/Middleware/EnsureSellerApproved.php
php -l routes/web.php
```

### 5.3 Automated Feature & Regression Testing
Run the existing test suite:
```powershell
php artisan test --filter=ChallengerM1AuthLocalizationTest
php artisan test --filter=AuthAndLocalizationTest
```
Run the new Seller test suite once created:
```powershell
php artisan test --filter=SellerOnboardingTest
```
Full regression pass (must maintain 278/278 tests passing):
```powershell
php artisan test
```

### 5.4 Invalidation Conditions
- If visiting `/seller/pending` redirects to `/seller/pending`, a redirect loop bug is present in `SellerMiddleware`.
- If an approved seller visiting `/seller/onboarding` does not redirect to `/seller/dashboard`, `showWizard()` state logic is broken.
- If submitting without a `seller_type` or with invalid coordinates (`lat: 95`) passes validation, input validation is insufficient.
- If an uploaded storefront image is stored on the `local` disk rather than `public`, storefront photos will fail to load in public views.
