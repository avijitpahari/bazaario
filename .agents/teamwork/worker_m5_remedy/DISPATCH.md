# Worker Dispatch: Milestone 5 Remediation (Feature 35 Operating Harvest Days)

## Context
Milestone 5 Quality Gate in Iteration 1 had 4/5 agents approve, but `challenger_m5_c` identified a defect in Feature 35 (Operating Harvest Days).

## Scope & Concrete Actions
1. **Database Migration**:
   - Create migration `database/migrations/2026_09_30_000003_add_operating_days_to_seller_profiles_table.php`.
   - Add `$table->json('operating_days')->nullable()->after('operating_radius_km');` in `up()`, and `$table->dropColumn('operating_days');` in `down()`.
   - Run `php artisan migrate`.
2. **Model Update**:
   - In `app/Models/SellerProfile.php`, add `'operating_days'` to `$fillable` and `'operating_days' => 'array'` to `casts()`.
3. **Controller Update**:
   - In `app/Http/Controllers/Seller/SellerProfileController.php::updateProfile`, normalize `$request->input('operating_days')`:
     ```php
     if ($request->has('operating_days') && is_string($request->input('operating_days'))) {
         $decoded = json_decode($request->input('operating_days'), true);
         if (is_array($decoded)) {
             $request->merge(['operating_days' => $decoded]);
         }
     }
     ```
     Persist operating_days so that updates save properly to DB.
4. **Verification**:
   - Run `php artisan test tests/Feature/Seller/Milestone5ProfileSecurityChallengeTest.php`
   - Run `php artisan test tests/Feature/Seller/SellerAuctionAndProfileTest.php`
   - Check `php artisan tinker --execute="var_dump(Schema::hasColumn('seller_profiles', 'operating_days'));"`

## Mandatory Integrity Warning
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

## References
- `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md`
- `c:\xampp\htdocs\bazaario\PROJECT.md`
- `c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m5_c\handoff.md`

## Output
Write `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m5_remedy\handoff.md` with full details of changes made, tests run, and command outputs, then send completion message to orchestrator.
