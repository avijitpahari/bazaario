# Dispatch Assignment: Challenger M3-A (Product Detail & Review Recalculation)

## Mission
Empirically stress test Product Detail and Reputation functionality (Features 24 to 33):
- Test review submission edge cases: invalid ratings (<1 or >5), empty reviews, excessively long text, multiple reviews from same user.
- Verify exact recalculation of `average_rating` and `total_reviews` in the database.
- Verify image gallery and specifications table rendering with products having various attributes or missing attributes (null weight, null dimensions).
- Verify 'Buy Now' sets session or DB state and redirects to `/checkout`.

## Authoritative Inputs
- `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md` (specifically under ## 2026-09-29T05:45:59Z)
- `c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_2\PROJECT.md`
- `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3_impl\handoff.md`

## Deliverables
- Run tests and automated assertions.
- Record explicit verdict (`APPROVE` or `CHALLENGE_FAILED`) in `c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m3_a\handoff.md`.

## 2026-09-29T10:16:01Z
You are challenger_m3_a.
Your working directory is c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m3_a.
Write all your challenge code, logs, and handoffs strictly into your working directory.

Read the authoritative user request at:
c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (specifically under ## 2026-09-29T05:45:59Z).
Read the project scope at:
c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_2\PROJECT.md
Read the worker handoff at:
c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3_impl\handoff.md
Read your dispatch instructions at:
c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m3_a\DISPATCH.md

Your Mission: Empirically stress test Product Detail and Reputation functionality (Features 24 to 33).
Challenge Scenarios:
1. Review submission stress test:
   - Submit review with rating 1, 3, 5, and verify `products.average_rating` and `products.total_reviews` update accurately in the database.
   - Challenge invalid ratings (e.g., rating 0, rating 6, string rating) and verify validation failure (422 / error session).
   - Test review text saving into `comment` DB column.
2. Dynamic attributes:
   - Test product with null weight/dimensions and verify view renders cleanly without errors or broken tables.
   - Test products with various seller types (`Farmer`, `Kirana Store`, `Dark Store`, `Individual`) and unit types (`kg`, `dozen`, `bundle`, `litre`).
3. Out-of-stock and Buy Now:
   - Test product with stock 0: verify CTA buttons disable or show out-of-stock indicator.
   - Test 'Buy Now': verify item is added to cart and redirect to `/checkout` occurs.

Tasks:
- Run automated tests or execute empirical challenge tests (e.g. via `php artisan test`).
- Record explicit verdict (APPROVE or CHALLENGE_FAILED) in `c:\xampp\htdocs\bazaario\.agents\teamwork\challenger_m3_a\handoff.md`.
- Report back with send_message when done.
