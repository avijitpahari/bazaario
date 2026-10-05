# DISPATCH

## 2026-10-05T08:47:26Z
You are orchestrator_9, the Project Orchestrator for the Bazaario project.
Working directory: c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_9
Project root: c:\xampp\htdocs\bazaario
Original request file: c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md
Issue tracker file: c:\xampp\htdocs\bazaario\UI_LOGIC_PROBLEMS.md
Predecessor scope & project plan: c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_8\PROJECT.md
Predecessor Gate Status: c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_8\GATE_STATUS.md

Mission:
Fix all 42 UI, logic, layout, asset, and design system issues documented in C:\xampp\htdocs\bazaario\UI_LOGIC_PROBLEMS.md across the Bazaario Laravel codebase.

Current Project Status:
- Milestone 1 (Asset & Infrastructure Optimization: P1–P4, P17, P34): GATE PASSED & CERTIFIED.
- Milestone 2 (Logic, Routes & Controller Reliability: P5–P10, P20–P22, P26, P28, P31–P32, P39, P41): GATE PASSED & CERTIFIED.
- Milestone 3 (Responsive Layout & Mobile Navigation: P11–P16, P33, P35, P42): IMPLEMENTATION COMPLETED by worker_m3_layout across layouts/seller.blade.php (mobile drawer toggle < 1024px), user/products/show.blade.php (pb-24 spacing & SVG image fallbacks), index.blade.php (screen.png onerror, responsive categories), ProductController.php (nationwide fallback for nearby stalls), nav.blade.php & nav-user.blade.php.
  Verify Milestone 3 and Certify Gate 3.
- Milestone 4 (Design System Unification & UI Components: P18, P19, P23–P25, P27, P29–P30, P36–P38, P40): Needs implementation of touch-friendly cart popover trigger (avoid pure hover @mouseenter breaking touch), dynamic notification badge count binding in nav/seller headers, unification of remaining stitch tokens/classes.
- Milestone 5 (Full E2E Regression Pass & System Certification): Run full test suite (php artisan test), route compilation (php artisan route:list), and verify all 6 Acceptance Criteria.

Acceptance Criteria:
- [ ] All automated tests pass: php artisan test completes with 0 failures.
- [ ] Route compilation check: php artisan route:list returns cleanly with zero missing routes or broken controller bindings.
- [ ] No double CSS/JS script inclusion in DOM rendered by index, layouts/app, layouts/seller, and user/products/index.
- [ ] Seller layout renders responsive hamburger menu and drawer below lg viewport breakpoint.
- [ ] No hardcoded fake data in seller dashboard when empty state is present.
- [ ] Footer legal links (/privacy, /terms, /return-policy) respond with HTTP 200.
