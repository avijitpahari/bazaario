## 2026-09-29T09:52:21Z
You are worker_m3_impl.
Your working directory is c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3_impl.
Write all your reports, logs, and handoffs strictly into your working directory.

Read the authoritative user request at:
c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically under ## 2026-09-29T05:45:59Z).
Read the project scope at:
c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_2\PROJECT.md
Read the survey reports at:
- c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_2\survey_r4_r6.md
- c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_2\handoff.md

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Your Mission: Implement and harden Milestone 3: Product Detail, Reputation & Cart Engine (Features 24 to 38).
Write Ownership:
You have exclusive write ownership of:
- `database/migrations/*` (for adding `seller_type` to `seller_profiles` and `unit_type` to `products`)
- `app/Models/SellerProfile.php`
- `app/Models/Product.php`
- `app/Http/Controllers/User/ReviewController.php`
- `app/Http/Controllers/User/CartController.php`
- `resources/views/user/products/show.blade.php`
- `resources/views/user/cart/index.blade.php`
- `resources/views/components/nav.blade.php` and `resources/views/components/nav-user.blade.php` (cart count badge)
- `routes/web.php` (cart, review, buy-now endpoints)

Specific Acceptance Criteria to Implement:
1. Feature 24: Product Detail Page renders full responsive image gallery (`show.blade.php`).
2. Feature 25: Renders complete product description & dynamic specifications table (replace hardcoded iPhone specs with real database product attributes: SKU, weight, dimensions, sale type, category, stock).
3. Feature 26: Displays Seller Type badge (`Farmer`, `Kirana Store`, `Dark Store`, `Individual`). Add `seller_type` column to `seller_profiles` via migration and update model `$fillable`.
4. Feature 27: Displays dynamic Price & real-time Stock availability.
5. Feature 28: Displays Unit Type badge (`kg`, `dozen`, `bundle`, `litre`). Add `unit_type` column to `products` via migration and update model `$fillable`.
6. Feature 29: Displays Seller Trust Score badge.
7. Feature 30: 'Add to Cart' adds product to session/database cart with instant feedback and top nav cart badge count update.
8. Feature 31: 'Buy Now' adds item to cart and immediately navigates to checkout (`/checkout`).
9. Feature 32: View Reviews renders real database customer reviews (`$product->reviews`) with reviewer name, rating stars, and comments (replace static mock reviews).
10. Feature 33: Add Review & Rating form allows authenticated buyers to submit feedback. In `ReviewController.php`, validate and save into the database column `'comment'` (fix prior `'body'` mismatch) and re-calculate product aggregate ratings.
11. Feature 34: Cart items are clearly grouped by Seller into merchant blocks (`cart/index.blade.php`).
12. Feature 35: Update Item Quantity updates quantity and subtotals dynamically.
13. Feature 36: Remove Item removes specific product from cart cleanly.
14. Feature 37: Renders Seller-wise Subtotal breakdown per merchant block (`cart/index.blade.php`).
15. Feature 38: Apply Coupon Code validates promo code (`minimum_order_amount`, `maximum_discount_amount`, `usage_limit`, expiration) and deducts discount cleanly.

Verification:
- Run `php artisan test tests/Feature/ProductDetailAndCartTest.php` and verify all tests pass.
- Run full regression suite `php artisan test` and verify 100% pass rate.
- Run `php -l` on all modified PHP files.
- Write your complete handoff report to `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3_impl\handoff.md`.
- Report back with send_message when done.
