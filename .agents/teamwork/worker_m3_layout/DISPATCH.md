# DISPATCH: Milestone 3 — Responsive Layout & Mobile Navigation

## Objective
Implement Milestone 3 of the Bazaario UI & Logic remediation project:
Resolve issues P11, P12, P13, P14, P15, P16, P33, P35, P42 across layouts, components, and views.

## Assigned Scope & Files:
1. **P11 (Seller Mobile Layout & Drawer)**: `resources/views/layouts/seller.blade.php`
   - Implement responsive drawer below `lg` (< 1024px) breakpoint with hamburger menu toggle button in the header.
   - Drawer must slide in smoothly on mobile, have backdrop overlay, close button, and contain full navigation items.
   - Adjust main content container: `pl-0 lg:pl-72`.
2. **P12 (Home Navbar)**: `resources/views/index.blade.php` & `resources/views/components/nav.blade.php`
   - Ensure navbar on homepage renders cleanly without errors, full parity with customer navigation or seamless `components.nav-user` integration.
3. **P13 (Mobile Bottom Nav Overlap)**: `resources/views/user/products/show.blade.php` and customer views
   - Ensure bottom spacing (`pb-24 lg:pb-8`) so fixed bottom nav bar does not overlap content or CTAs.
4. **P14 (Hero Screen Image Fallback)**: `resources/views/index.blade.php`
   - Add `onerror` fallback and background styling for `screen.png` so missing images do not show broken image icon.
5. **P15 (Category Grid Responsiveness)**: `resources/views/index.blade.php`
   - Responsive grid (`grid-cols-2 xs:grid-cols-3 sm:grid-cols-4 lg:grid-cols-8`) and prevent unicode mid-character truncation for Indian languages.
6. **P16 (Featured Sellers Empty State)**: `resources/views/index.blade.php`
   - Replace fake hardcoded Unsplash seller cards with clean "Be the first featured seller" empty state CTA with link to register/onboard as a seller.
7. **P33 (Nearby Stalls Geolocation Fallback)**: `resources/views/index.blade.php` & `app/Http/Controllers/ProductController.php`
   - Graceful fallback for geolocation rather than hardcoded Kolkata coordinates. Provide clean browser location prompt or fallback to country-wide featured stalls if no coordinates provided.
8. **P35 (Product Details Fallback Images)**: `resources/views/user/products/show.blade.php`
   - Replace hardcoded Unsplash leather bag fallback images with clean generic Bazaario product placeholder or SVG icon.
9. **P42 (Category Mega-Menu Width Overflow)**: `resources/views/components/nav-user.blade.php`
   - Add `max-w-[calc(100vw-2rem)]` or position adjustments to prevent `w-[540px]` panel overflowing on 768px-1024px laptop viewports.

## Mandatory Warnings:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

## Requirements:
1. Read `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md` before starting work.
2. Read `c:\xampp\htdocs\bazaario\UI_LOGIC_PROBLEMS.md` for detailed issue specifications.
3. Read `c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_8\PROJECT.md`.
4. Verify changes by running tests: `php artisan test` and `php artisan route:list`.
5. Write detailed report in `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3_layout\handoff.md` and `changes.md`.
6. Send message back to parent orchestrator_8 upon completion.

## 2026-10-05T07:05:21Z
You are worker_m3_layout working in c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3_layout.
Your parent is orchestrator_8 (conversation ID: df590ce7-f260-4c31-867f-d002c38deaf1).

MANDATORY FIRST STEP:
Read c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md.
Also read c:\xampp\htdocs\bazaario\UI_LOGIC_PROBLEMS.md, c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_8\PROJECT.md, and c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3_layout\DISPATCH.md.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Mission: Implement Milestone 3 (Responsive Layout & Mobile Navigation)
Resolve issues P11, P12, P13, P14, P15, P16, P33, P35, P42.

Assigned File Boundaries:
- resources/views/layouts/seller.blade.php (P11)
- resources/views/index.blade.php (P12, P14, P15, P16, P33)
- resources/views/components/nav.blade.php (P12)
- resources/views/components/nav-user.blade.php (P13, P42)
- resources/views/user/products/show.blade.php (P13, P35)
- app/Http/Controllers/ProductController.php (P33 if needed for nearby stalls fallback)

Requirements:
1. P11: Add responsive sidebar drawer with hamburger toggle in layouts/seller.blade.php (< 1024px). Ensure pl-0 lg:pl-72 on main container, backdrop overlay, slide-over transition, and accessible close button.
2. P12: Verify and ensure home page navbar (components/nav.blade.php) has full feature parity with components/nav-user or unifies cleanly.
3. P13: Fix mobile bottom nav bar overlap with pb-24 lg:pb-8 on show.blade.php and relevant customer views.
4. P14: Add onerror fallback (and gradient/fallback styling) for hero screen.png image in index.blade.php.
5. P15: Category grid responsiveness: prevent overflow on small screens and prevent unicode mid-character truncation for category names.
6. P16: Replace fake fallback seller cards in index.blade.php with clean "Be the first featured seller" empty state CTA with seller onboarding link.
7. P33: Nearby stalls: remove hardcoded Kolkata coordinates fallback; implement graceful fallback logic (browser geolocation or nationwide stalls fallback).
8. P35: In show.blade.php, replace hardcoded Unsplash leather bag fallback images with generic product placeholders.
9. P42: In nav-user.blade.php, prevent category mega-menu (w-[540px]) overflowing on small laptop screens (add max-w-[calc(100vw-2rem)]).

Verification:
- Run php artisan test to ensure 0 test regressions.
- Run php artisan route:list to ensure route cleanliness.
- Verify syntax with php -l on modified PHP/Blade files.
- Document all changes in c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3_layout\changes.md and handoff report in c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3_layout\handoff.md.
- Send completion message to parent orchestrator_8.
