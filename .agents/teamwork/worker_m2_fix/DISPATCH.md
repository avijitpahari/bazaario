## 2026-09-29T07:15:24Z
You are worker_m2_fix.
Your working directory is c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m2_fix.
Write all your reports, logs, and handoffs strictly into your working directory.

Read the authoritative user request at:
c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (under ## 2026-09-29T05:45:59Z).
Read the Gate Status at:
c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_2\GATE_STATUS.md
Read the Reviewer handoffs:
- c:\xampp\htdocs\bazaario\.agents\teamwork\reviewer_m2_a\handoff.md
- c:\xampp\htdocs\bazaario\.agents\teamwork\reviewer_m2_b\handoff.md

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Your Mission: Resolve all findings identified by the reviewers for Milestone 2:
Write Ownership:
You have exclusive write ownership of:
- `app/Http/Controllers/ProductController.php`
- `resources/views/user/products/index.blade.php`
- `resources/views/user/products/category.blade.php`

Actionable Fixes Required:
1. In `app/Http/Controllers/ProductController.php`:
   - Line 75: In `Nearby Stalls` query, strictly enforce `whereHas('sellerProfile', fn($q) => $q->where('status', 'approved'))`. Unapproved/pending sellers must NEVER appear in nearby stalls.
   - Line 58: In `Featured Sellers` query, remove the fallback that queried all/unapproved sellers when approved sellers are absent. Never leak unapproved merchants.
   - Lines 88–94: In Nearby Stalls radius filtering, remove the fallback that returns distant stalls (1600+ km away) when 0 stalls match the radius. If no stalls are within the selected radius, return an empty collection.
   - In `index()` method (catalog browsing):
     - Sanitize `$category` input: `$category = is_array($request->category) ? ($request->category[0] ?? null) : $request->category;` so array parameters like `/products?category[]=textiles` do not crash with a 500 error in `index.blade.php`.
     - Support seller rating filter and product rating filter cleanly.
   - In `show()` method: If a product slug is not found or product is inactive, throw a 404 (`firstOrFail()`) instead of falling back to `Product::first()`.
2. In `resources/views/user/products/index.blade.php` and `resources/views/user/products/category.blade.php`:
   - Remove hardcoded rating facades `$product->average_rating ?: 4.8` and review count facades `$product->total_reviews ?: 14`!
   - Replace with genuine dynamic values:
     `$product->average_rating ? number_format($product->average_rating, 1) : 'New'`
     and `($product->total_reviews ?? 0) . ' reviews'`.
3. Verification:
   - Run `php artisan test tests/Feature/CatalogAndDiscoveryTest.php`
   - Run `php artisan test tests/Feature/CatalogSearchAndFacetFilterChallengeTest.php`
   - Run `php artisan test tests/Feature/Milestone2EmpiricalChallengeTest.php`
   - Run full regression suite `php artisan test`
   - Verify all tests pass with 0 failures.
   - Report actual test outputs in `handoff.md`.
4. Write your complete handoff report to `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m2_fix\handoff.md`.
5. Report back with send_message when done.
