## 2026-09-30T04:59:46Z
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m1_routes_1

MANDATORY FIRST STEP: Read the user's authoritative request at:
c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md

Also read the project architecture at:
c:\xampp\htdocs\bazaario\PROJECT.md
and prior survey findings at:
c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_backend_survey_1\handoff.md
and test survey findings at:
c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_test_survey_1\handoff.md

Your role is Routes & Controller Explorer for Milestone 1.
Your task is to design:
1. `App\Http\Controllers\Seller\SellerOnboardingController`:
   - `showWizard()`: renders the multi-step onboarding wizard. If already approved, redirects to dashboard. If already submitted and pending, allows viewing/editing or redirects to pending.
   - `submitWizard(Request $request)`: handles complete onboarding form submission (seller_type, shop_name, bio, storefront image upload to public disk, address, city, state, postal code, latitude, longitude), updates or creates `SellerProfile` in `pending` status, attaches to authenticated user, logs activity, and redirects to `seller.pending`.
2. Seller route group structure in `routes/web.php`:
   - Prefix `seller`, name `seller.`
   - Auth guard `auth:seller`, middleware `seller`
   - Unapproved-accessible routes: `seller.onboarding`, `seller.onboarding.submit`, `seller.pending`
   - Approval-required routes: `seller.dashboard`, plus placeholders for products, orders, payouts, auctions, account.
3. Form validation rules and error bag handling for wizard inputs.

Recommend exact controller logic and route specifications for the worker. Do NOT implement the code yourself.
Write your report to:
c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m1_routes_1\handoff.md
and send a completion message back to the orchestrator.
