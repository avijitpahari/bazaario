## 2026-09-30T04:59:46Z
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m1_schema_1

MANDATORY FIRST STEP: Read the user's authoritative request at:
c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md

Also read the project architecture at:
c:\xampp\htdocs\bazaario\PROJECT.md
and prior survey findings at:
c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_backend_survey_1\handoff.md

Your role is Schema & Middleware Explorer for Milestone 1.
Your task is to plan the exact database migration and middleware changes:
1. Design the migration `database/migrations/2026_09_30_000001_add_seller_panel_fields_to_tables.php`:
   - Add to `products`: `harvest_date` (nullable date), `expiry_days` (nullable unsignedSmallInteger), `expiry_date` (nullable date), `is_perishable` (boolean default false), `auto_hide_expired` (boolean default true), `farm_origin` (nullable string), `harvest_grade` (nullable string), `low_stock_threshold` (unsignedSmallInteger default 10).
   - Add to `seller_orders`: `delivery_slot` (nullable string).
   - Add to `seller_profiles`: `address` (nullable string), `operating_radius_km` (nullable unsignedSmallInteger default 25).
2. Detail necessary model updates (`Product.php`, `SellerOrder.php`, `SellerProfile.php` fillables, casts, accessors, scopes).
3. Design the access control middleware update:
   - In `app/Http/Middleware/SellerMiddleware.php`, ensure pending sellers cannot access `/seller/dashboard` or operational routes. If `!$user->sellerProfile || $user->sellerProfile->status !== 'approved'`, redirect to `route('seller.pending')` with an explanatory message.
   - Ensure `/seller/pending`, `/seller/onboarding`, and logout are excluded from the approval requirement.

Recommend exact, copy-pasteable class and method signatures for the worker. Do NOT implement the code yourself.
Write your report to:
c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_m1_schema_1\handoff.md
and send a completion message back to the orchestrator.
