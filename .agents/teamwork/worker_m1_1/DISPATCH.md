## 2026-09-30T05:05:04Z

Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m1_1

MANDATORY FIRST STEP: Read the user's authoritative request at:
c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Also read:
- Project architecture: c:\xampp\htdocs\bazaario\PROJECT.md
- UI Spec: c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m1_1\handoff.md
- Schema & Middleware Spec: c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m1_schema_1\handoff.md
- Routes & Controller Spec: c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m1_routes_1\handoff.md

Your role is Implementation Worker for Milestone 1: Foundation, Schemas & Onboarding Access Control (R1).

You exclusively own and must implement the following files:
1. `database/migrations/2026_09_30_000001_add_seller_panel_fields_to_tables.php`:
   - Adds `harvest_date`, `expiry_days`, `expiry_date`, `is_perishable`, `auto_hide_expired`, `farm_origin`, `harvest_grade`, `low_stock_threshold` to `products`.
   - Adds `delivery_slot` to `seller_orders`.
   - Adds `address`, `operating_radius_km` to `seller_profiles`.
2. Model updates:
   - `app/Models/Product.php`: fillables, casts, dates, scopes (`scopeFresh`, `scopeStale`, `scopeLowStock`, `scopePublicVisible`), helpers (`isExpired`, `isStale`, `isLowStock`), saving lifecycle hook for expiry_date.
   - `app/Models/SellerOrder.php`: fillable `delivery_slot`, accessor `getDeliverySlotAttribute($value)` with fallback regex extraction from `$this->order->notes`.
   - `app/Models/SellerProfile.php`: fillables `address`, `operating_radius_km`, cast `operating_radius_km`, scopes `scopeApproved`, `scopePending`, helpers `isApproved`, `isPending`.
3. `app/Http/Middleware/SellerMiddleware.php`:
   - Enforce approval gate: if seller user is not approved (`!$user->sellerProfile || $user->sellerProfile->status !== 'approved'`), redirect to `route('seller.pending')`.
   - Whitelist exempt routes: `seller.pending`, `seller.onboarding`, `seller.onboarding.submit`, and logout routes to prevent redirect loops.
4. `app/Http/Controllers/Seller/SellerOnboardingController.php`:
   - `showWizard(Request $request)`: redirects approved sellers to dashboard; renders wizard for unapproved/pending sellers with prefilled data.
   - `submitWizard(Request $request)`: validates inputs, handles storefront image upload to `public` disk, updates or creates `SellerProfile` in `pending` status, logs submission, and redirects to `seller.pending` with flash success.
5. `routes/web.php`:
   - Group under prefix `seller`, name `seller.`, middleware `['auth:seller', 'seller']`.
   - Unapproved-accessible: `seller.onboarding`, `seller.onboarding.submit`, `seller.pending`.
   - Approval-required: `seller.dashboard` and placeholders for future modules.
6. Layouts:
   - `resources/views/layouts/seller-onboarding.blade.php`: Clean, distraction-free Warm Modernist container with brand logo SVG, fonts, Material Symbols, Alpine help modal, and flash toasts.
   - `resources/views/layouts/seller.blade.php`: Master operating workspace with fixed w-72 sidebar, route-aware navigation links, search with ⌘K, profile pill, and flash toast alerts.
7. Views:
   - `resources/views/seller/onboarding/wizard.blade.php`: Full 5-step guided wizard (Account, Seller Types [Farmer, Kirana, Dark Store, Individual], Shop & Farm details with image dropzone, Location with GPS detect & Lat/Lng manual inputs & radar preview, Application review matrix with 1-click edit jumps, confirmation modal).
   - `resources/views/seller/pending.blade.php`: Comprehensive 5-stage approval waiting terminal with timeline, summary strip, contextual notice box, and dashboard lockout security notice.

After implementation:
- Run `php artisan migrate` (or verify migrations via test runner).
- Run `php artisan test` and verify that all 278 existing tests pass with 0 regressions.
- Write unit/feature verification for M1 onboarding & approval gate.

Update progress.md regularly. When complete, compile handoff.md with verified build/test outputs and notify the orchestrator.
