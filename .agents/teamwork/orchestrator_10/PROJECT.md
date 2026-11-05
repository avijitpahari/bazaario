# Project: Bazaario High-Impact Core Modules Completion

## Architecture
- **Framework & Stack**: Laravel 11, PHP 8.2, Blade, Tailwind CSS, Alpine.js, MySQL (production) / SQLite `:memory:` (testing).
- **Module 1 (R1) - Admin Platform Configuration & Branding Settings**:
  - Store: `site_settings` key-value table managed via `App\Models\SiteSetting`.
  - Keys: `site_name`, `site_tagline`, `site_logo`, `commission_rate` / `platform_commission_base`, `platform_mode` (`multi_vendor` | `single_vendor`).
  - Controller: `App\Http\Controllers\Admin\AdminDashboardController` methods `settings()` and `updateSettings(Request $request)`.
  - Views: `resources/views/admin/settings/index.blade.php`, `general.blade.php`, `platform.blade.php`, `commissions.blade.php`.
  - Layout branding dynamic binding: `resources/views/layouts/admin.blade.php`, `layouts/app.blade.php`, `components/nav.blade.php`, `components/footer.blade.php`.
  - Mode switch logic: In Single-Vendor mode (`isSingleVendor()`), hide/disable seller registration (`/register` role radio & `AuthController::sendOtp`), seller onboarding (`/seller/onboard`), seller documentation (`/seller/become-a-seller`, `/seller/fees-and-commission`), and homepage seller shelves ("Featured Sellers", "Nearby Stalls").
- **Module 2 (R2) - Live Auction Automation & Lifecycle Management**:
  - Console Command: `App\Console\Commands\CloseExpiredAuctions` (`php artisan auctions:close-expired`).
  - Scheduling: Scheduled in `routes/console.php` with `Schedule::command(CloseExpiredAuctions::class)->everyMinute()`.
  - Transition: For live auctions with `ends_at <= now()`, atomically transition to `ended` within `DB::transaction`.
  - Winner & Financials: If highest bid meets or exceeds `reserve_price`, set `winner_id = $highestBid->user_id`, create `Order` (with `order_type = 'auction'`, status `placed`/`pending`), `SellerOrder`, `OrderItem`, and `Invoice`. If reserve not met or 0 bids, `winner_id = null`.
  - Outbid Notifications: In `App\Http\Controllers\User\AuctionController::placeBid`, detect supplanted leader and create an `outbid` in-app record in `notifications` table for previous leading bidder.
  - Client-Side Countdown Timer: Fix JavaScript division bug `(distance % (1000 * 60)) / 1000` -> `(distance % (1000 * 60 * 60)) / (1000 * 60)` in `auction-show.blade.php`, `auctions.blade.php`, and `bids.blade.php`. Ensure dynamic UTC-safe countdown rendering.
- **Module 3 (R3) - Gemini AI Integration Suite with Graceful Fallbacks**:
  - Service: `App\Services\GeminiApiService` handling recommendations, comparisons, and customer support chatbot.
  - Backward compatibility: `App\Services\GeminiService` delegates to `GeminiApiService`.
  - Configuration: Add `'gemini'` configuration key in `config/services.php`. Precedence: `SiteSetting::get('gemini_api_key')` -> `config('services.gemini.api_key')` -> `env('GEMINI_API_KEY')`.
  - Fallback architecture: Zero-500 deterministic local fallback when `GEMINI_API_KEY` is missing/empty or external API throws errors. Returns structured data with `source => 'local_fallback'`.
  - Database lookup tools: Scoped lookup tools for authenticated user orders (prevents cross-user data leakage), active product catalog searches, store policy definitions (48h perishables, 7d handicrafts, escrow refund rules), and live auction telemetry.
  - Frontend Views & Endpoints: `/ai/chat`, `/ai/recommendations`, `/ai/compare`, `user/account/compare.blade.php`, `user/account/recommendations.blade.php`.

## Feature Inventory
| # | Feature | Description | Milestone | Source |
|---|---------|-------------|-----------|--------|
| 1 | Site Settings Schema & Accessors | Add `getSiteName()`, `getTagline()`, `getLogoUrl()`, `isSingleVendor()`, `getCommissionRate()` helpers to `SiteSetting` | M1 | survey_r1 |
| 2 | Admin Settings Controller & Routes | Add `settings()` and `updateSettings()` to `AdminDashboardController` and map `admin.settings.index`, `admin.settings.update` in `routes/web.php` | M1 | survey_r1 |
| 3 | Admin Settings Blade Views | Populate `admin/settings/index.blade.php`, `general.blade.php`, `platform.blade.php`, `commissions.blade.php` | M1 | survey_r1 |
| 4 | Platform Branding Dynamic Binding | Dynamically bind site name, logo, tagline in admin and user layouts using `SiteSetting` helpers | M1 | survey_r1 |
| 5 | Single-Vendor Platform Mode Enforcement | Disable/hide seller registration, seller documentation, onboarding wizard, and homepage seller shelves when `isSingleVendor()` is true | M1 | survey_r1 |
| 6 | Admin Platform Settings Test Suite | Comprehensive feature tests in `tests/Feature/AdminPlatformSettingsTest.php` | M1 | survey_r1 |
| 7 | Artisan CloseExpiredAuctions Command | Create `app/Console/Commands/CloseExpiredAuctions.php` (`php artisan auctions:close-expired`) | M2 | survey_r2 |
| 8 | Command Scheduler Registration | Register `CloseExpiredAuctions` every minute in `routes/console.php` | M2 | survey_r2 |
| 9 | Atomic Auction Winner Lot & Order/Invoice Creation | DB transaction wrapping auction transition to `ended`, winner assignment, and creation of `Order`, `SellerOrder`, `OrderItem`, `Invoice` | M2 | survey_r2 |
| 10 | Real-time In-app Outbid Notifications | Emit in-app notification in `notifications` table to outbid user when higher bid is placed in `placeBid` | M2 | survey_r2 |
| 11 | Client-Side Countdown Timer Fix | Fix minutes/seconds math bug in `auction-show.blade.php`, `auctions.blade.php`, `bids.blade.php` | M2 | survey_r2 |
| 12 | Live Auction Lifecycle Test Suite | Comprehensive feature tests in `tests/Feature/Auction/AuctionLifecycleAutomationTest.php` | M2 | survey_r2 |
| 13 | Extensible GeminiApiService | Build `App\Services\GeminiApiService` supporting recommendations, comparisons, and customer support chat | M3 | survey_r3 |
| 14 | Graceful API Key Missing Fallback | Deterministic local fallback providing structured recommendations/comparisons/chat with zero 500 errors when key is missing or fails | M3 | survey_r3 |
| 15 | Chatbot Secure Database Lookup Tools | Scoped order tracking tool (user tenant isolation), catalog search tool, and store policy lookup tool | M3 | survey_r3 |
| 16 | Structured Product Comparisons & Recommendations | Detailed comparison matrix (specs, pros/cons, use-cases) and personalized recommendations leveraging interaction history | M3 | survey_r3 |
| 17 | Gemini AI Integration Test Suite | Comprehensive feature tests in `tests/Feature/GeminiAiIntegrationTest.php` | M3 | survey_r3 |
| 18 | Full System Integration & Regression Pass | Verify all 750+ existing tests pass with 0 regressions, route check, and complete acceptance criteria verification | M4 | orchestrator |

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| 1 | M1: Admin Platform Configuration & Branding Settings | Features 1–6 (R1) | none | DONE |
| 2 | M2: Live Auction Automation & Lifecycle Management | Features 7–12 (R2) | none | DONE |
| 3 | M3: Gemini AI Integration Suite with Graceful Fallbacks | Features 13–17 (R3) | M1 (settings) | IN_PROGRESS |
| 4 | M4: Comprehensive E2E Verification, Forensic Audit & Certification | Feature 18 & all Acceptance Criteria | M1, M2, M3 | PLANNED |

## Interface Contracts
### SiteSetting ↔ Platform Mode
- `SiteSetting::get('platform_mode', 'multi_vendor')` returns `'multi_vendor'` or `'single_vendor'`.
- `SiteSetting::isSingleVendor(): bool` returns `true` if `platform_mode === 'single_vendor'`.
- `SiteSetting::getCommissionRate(): float` returns float commission percentage (default 8.5%).
- When `isSingleVendor()` is true:
  - `AuthController::sendOtp` rejects registration with `'role' => 'seller'` with error "Seller registration is currently closed in Single-Vendor mode."
  - `register.blade.php` hides the Seller role radio card.
  - `ProductController::home` sets `featuredSellers` and `nearbyStalls` to empty collections.
  - `index.blade.php` conditionally hides Sections 5.5 and 5.6 if empty or `isSingleVendor()`.
  - `ProductController::becomeASeller` and `feesAndCommission` redirect or display single-vendor notice.

### CloseExpiredAuctions ↔ Orders & Financials
- Signature: `auctions:close-expired`.
- Scoped to: `Auction::where('status', 'live')->where('ends_at', '<=', now())`.
- For each expired auction in `DB::transaction`:
  - `lockForUpdate()`.
  - Check highest bid: `AuctionBid::where('auction_id', $auction->id)->orderByDesc('amount')->first()`.
  - Reserve check: `!$auction->reserve_price || $highestBid->amount >= $auction->reserve_price`.
  - Status transition: `status = 'ended'`.
  - If reserve met:
    - `winner_id = $highestBid->user_id`.
    - Create `Order`: `user_id = $winner->id`, `order_type = 'auction'`, `order_status = 'processing'`, `total_amount = $highestBid->amount`.
    - Create `SellerOrder`: `order_id = $order->id`, `seller_id = $auction->seller_profile->user_id`, `subtotal = $highestBid->amount`.
    - Create `OrderItem`: `order_id = $order->id`, `product_id = $auction->product_id`, `price = $highestBid->amount`, `quantity = 1`.
    - Create `Invoice`: `order_id = $order->id`, `invoice_number = 'INV-AUC-' . strtoupper(Str::random(8))`, `total = $highestBid->amount`, `status = 'unpaid'` / `status = 'pending'`.
  - If reserve not met or 0 bids: `winner_id = null`.

### GeminiApiService ↔ Consumers
- `GeminiApiService::getPersonalizedRecommendations(?User $user, int $limit = 6): array`
  - Returns `['items' => array, 'source' => 'gemini'|'local_fallback', 'status' => 'success']`.
- `GeminiApiService::compareProducts(array $productIds): array`
  - Returns `['products' => array, 'attributes' => array, 'pros_cons' => array, 'recommendation' => string, 'verdict' => string, 'source' => 'gemini'|'local_fallback']`.
- `GeminiApiService::chat(string $prompt, array $history = [], ?User $user = null): array`
  - Returns `['reply' => string, 'tools_used' => array, 'source' => 'gemini'|'local_fallback']`.
  - Database lookup tools:
    - Orders: queries `$user->orders` only.
    - Products: queries active products.
    - Policies: queries platform returns/refunds/escrow rules.
