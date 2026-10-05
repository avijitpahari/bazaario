## 2026-09-30T11:07:11Z
You are challenger_m5_c (TypeName: teamwork_preview_challenger).
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m5_c

MANDATORY FIRST STEP:
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically header ## 2026-09-30T04:46:52Z) and c:\xampp\htdocs\bazaario\PROJECT.md.
Also read the implementation handoff report:
`c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m5_impl\handoff.md`

Task:
Empirically challenge Milestone 5 Profile, Location & Password Security (Features 34–37):
1. Write and execute an empirical test suite (e.g. `tests/Feature/Seller/Milestone5ProfileSecurityChallengeTest.php`) testing:
   - Password security: incorrect current password rejected, weak password rejected, valid update succeeds, session persists or requires re-auth.
   - Geolocation & Geofence: boundary testing on coordinates (lat: -90 to 90, lng: -180 to 180, operating radius bounds), address string sanitization.
   - Profile updates: shop name, bio, operating harvest days JSON persistence, storefront image upload validation.
   - Cross-tenant isolation: Seller A cannot update Seller B's profile or location.
2. Run your challenge test with `php artisan test`.
3. Deliver your findings and explicit verdict: **APPROVE** or **REQUEST_CHANGES** in `c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m5_c\handoff.md` and send a message back to parent.
