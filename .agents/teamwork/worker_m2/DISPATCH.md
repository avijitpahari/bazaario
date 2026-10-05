## 2026-09-29T06:38:16Z
You are worker_m2.
Your working directory is c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m2.
Write all your reports, logs, and handoffs strictly into your working directory.

Read the authoritative user request at:
c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically under ## 2026-09-29T05:45:59Z).
Read the project scope at:
c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_2\PROJECT.md
Read the survey reports at:
- c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_1\survey_r1_r3.md
- c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_2\survey_r4_r6.md

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Your Mission: Implement and harden Milestone 2: Catalog, Discovery & Hyperlocal Browsing (Features 9 to 23).
Write Ownership:
You have exclusive write ownership of:
- `app/Http/Controllers/ProductController.php`
- `app/Models/SellerProfile.php`
- `database/migrations/*` (for adding coordinates or schema columns needed for distance filtering)
- `resources/views/index.blade.php`
- `resources/views/pages/how-it-works.blade.php`
- `resources/views/components/footer.blade.php`
- `resources/views/user/products/index.blade.php`
- `resources/views/user/products/category.blade.php`
- `routes/web.php` (product routes, category routes, documentation routes)

Specific Acceptance Criteria to Implement:
1. Feature 9: Hero Banner renders responsive marketing visuals & CTA (`index.blade.php`).
2. Feature 10: Featured Sellers section displays verified business profiles with real DB query (`index.blade.php`).
3. Feature 11: Nearby Stalls displays hyperlocal sellers based on radius / distance with real spatial distance calculations (`index.blade.php`). Add `latitude` and `longitude` to `seller_profiles` via migration and populate default coordinates if null.
4. Feature 12: Categories section lists all active product categories (`index.blade.php`).
5. Feature 13: Trending Products displays popular marketplace listings with real ratings and prices (`index.blade.php`).
6. Feature 14: 'For Sellers' Transparent Pricing page renders commission structure (`/seller/fees-and-commission`).
7. Feature 15: How It Works / About page renders comprehensive platform documentation (`/how-it-works` or `/about`) and link it in `footer.blade.php`.
8. Feature 16: View All Products page lists catalog items with Laravel server-side pagination (`paginate(12)`) and `$products->links()`, replacing client-side-only slicing.
9. Feature 17: Filter by Category updates catalog results dynamically.
10. Feature 18: Filter by Price Range filters items within min/max bounds.
11. Feature 19: Filter by Seller Rating filters products by minimum rating stars.
12. Feature 20: Filter by Distance/Radius filters items from nearby sellers using radius slider and coordinates.
13. Feature 21: Keyword Search performs live fuzzy search on product names and descriptions.
14. Feature 22: View Search Results displays matching items with match counts and handles empty state gracefully.
15. Feature 23: Sort Products orders items by price (asc/desc), rating, or newest.
16. Replace static mockup in `resources/views/user/products/category.blade.php` with dynamic database products iteration.
17. Ensure all catalog routing delegates to a dedicated, clean `ProductController.php`.

Verification:
- Run `php artisan test tests/Feature/CatalogAndDiscoveryTest.php` and verify all tests pass (0 failures).
- Run full regression suite `php artisan test` and ensure all tests pass.
- Run `php -l` on all modified PHP files.
- Write your complete handoff report to `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m2\handoff.md`.
- Report back with send_message when done.
