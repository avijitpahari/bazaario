## 2026-09-29T05:59:13Z
You are worker_m1.
Your working directory is c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m1.
Write all your reports, analysis, and handoffs strictly into your working directory.

Read the authoritative user request at:
c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically under ## 2026-09-29T05:45:59Z).
Read the project scope at:
c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_2\PROJECT.md

Read the survey findings from:
- c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_1\survey_r1_r3.md
- c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_1\handoff.md
- c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_3\survey_r7_r8_tests.md
- c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_3\handoff.md

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Your Mission: Implement and harden Milestone 1: Core Foundation & User Identity (Features 1-8, 50-54).
Write Ownership:
You have exclusive write ownership of:
- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/LanguageController.php`
- `app/Http/Middleware/SetLocale.php`
- `app/Http/Middleware/SellerMiddleware.php`
- `app/Http/Controllers/User/ProfileController.php`
- `app/Http/Controllers/User/AddressController.php`
- `app/Models/User.php`
- `database/migrations/*` (for adding bio column to users table)
- `resources/views/auth/*` (forgot-password, reset-password, login link)
- `resources/views/components/nav.blade.php` and `resources/views/components/nav-user.blade.php` (language selector)
- `resources/views/user/account/*` (profile.blade.php, edit-profile.blade.php, addresses.blade.php)
- `lang/` translation dictionaries (`en.json`, `hi.json`, `bn.json`)
- `routes/web.php` (auth routes, password reset routes, language switch route)

Specific Acceptance Criteria to Implement:
1. Feature 1: Registration form creates account in `users` table via OTP.
2. Feature 2: Login authenticates credentials cleanly. Fix the seller redirect loop bug: in `AuthController@login`, when `$user->role === 'seller'`, authenticate the `seller` guard (`Auth::guard('seller')->login($user)`) so that `auth:seller` middleware does not redirect the seller back to `/login`.
3. Feature 3: Logout terminates all guards cleanly.
4. Feature 4 & 5: Forgot Password & Password Reset flow. Provide routes (`GET /forgot-password`, `POST /forgot-password`, `GET /reset-password/{token}`, `POST /reset-password`), controller methods, Blade views (`auth/forgot-password.blade.php`, `auth/reset-password.blade.php`), and wire the link on `login.blade.php`.
5. Feature 6: RBAC. Implement real check in `SellerMiddleware` (check seller guard, status not suspended) and ensure customer, seller, and admin routes are strictly guarded.
6. Feature 7: Language Selector dropdown in top navigation (nav.blade.php, nav-user.blade.php) and footer supporting English, Hindi, Bengali.
7. Feature 8: Language persistence across user sessions, cookie, and database (`users.preferred_language`). Create `LanguageController@switch` (`POST /language`), register `SetLocale` middleware in `bootstrap/app.php` web middleware group, and populate `lang/en.json`, `lang/hi.json`, `lang/bn.json` with marketplace translations.
8. Feature 50: View Profile displays personal information and a clear buyer trust rank tier badge (e.g. "Tier-1 Verified Buyer").
9. Feature 51: Edit Profile allows updating name, phone, and bio. Create migration adding `bio` to `users` table, update `$fillable` in `User.php`, validate/update in `ProfileController@update`, and add bio textarea in `edit-profile.blade.php`.
10. Feature 52: Upload Profile Image functionality.
11. Feature 53: Change Password verifies old password and updates to new password.
12. Feature 54: Address CRUD. Ensure `addresses.blade.php` has a complete Edit Address modal/form calling `PUT /user/addresses/{address}`.

Verification:
- Run `php artisan test` and verify that all tests pass without errors.
- Run `php -l` on all modified PHP files.
- Run `php artisan route:list` to ensure all routes resolve cleanly.
- Write your complete handoff report to `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m1\handoff.md`.
- Report back with send_message when done.
