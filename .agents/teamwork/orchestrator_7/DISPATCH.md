## 2026-10-01T09:17:53Z

You are orchestrator_7, the Project Orchestrator for the Bazaario project.
Working directory: c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_7
Project root: c:\xampp\htdocs\bazaario
Original request file: c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md
Issue tracker file: c:\xampp\htdocs\bazaario\UI_LOGIC_PROBLEMS.md

Your mission:
Fix all 42 UI, logic, layout, asset, and design system issues documented in C:\xampp\htdocs\bazaario\UI_LOGIC_PROBLEMS.md across the Bazaario Laravel codebase.

Requirements:
### R1. Asset & Infrastructure Optimization
- Remove duplicate stylesheet loading (@vite + static <link> fallback) in app.blade.php and index.blade.php.
- Remove Tailwind CDN script from layouts/seller.blade.php and rely on compiled Vite CSS.
- Remove duplicate CDN Alpine.js scripts from user/products/index.blade.php and index.blade.php to prevent runtime conflicts with bundled Alpine.

### R2. Logic, Route & Data Reliability
- Ensure all linked routes/views (user.returns, user.invoices, user.compare, user.bids, user.notifications.index) exist and resolve cleanly without 500 errors.
- Fix misleading CTA destinations ("AI Compare", "Ask Bazaario AI" floating buttons) to either point to functional views or provide clear fallback behaviors.
- Wire seller header search bar and seller bulk actions to valid form actions/routes.
- Remove hardcoded numeric fallbacks in seller/dashboard.blade.php to prevent displaying fake metrics on error.
- Differentiate seller.auctions.history route from seller.auctions.index with filtered completed auctions.

### R3. Responsive Layout & Design System Unification
- Implement full mobile responsiveness in layouts/seller.blade.php (sidebar drawer toggle for screens < 1024px).
- Unify customer and seller design system Tailwind color tokens, typography scales, and component classes.
- Ensure fix for mobile bottom nav bar overlap across all user pages (pb-24 spacing).
- Provide fallback handling for missing images (screen.png, Unsplash fallbacks on product pages).
- Replace hardcoded Kolkata default location in Nearby Stalls with graceful fallback logic.

### R4. UI Interactive Components & Navigation Integrity
- Dynamically bind notification bell badge count (remove hardcoded "3 New").
- Fix cart popover trigger on touch devices (avoid pure hover @mouseenter breaking touch interaction).
- Replace dead footer links (href="#" for Privacy, Terms, Return Policy, Social links) with working routes/stubs.
- Unify home page navbar to use components.nav-user or ensure full feature parity.
- Remove WCAG accessibility violation user-scalable=no from viewport meta.

Acceptance Criteria:
- [ ] All automated tests pass: php artisan test completes with 0 failures.
- [ ] Route compilation check: php artisan route:list returns cleanly with zero missing routes or broken controller bindings.
- [ ] No double CSS/JS script inclusion in DOM rendered by index, layouts/app, layouts/seller, and user/products/index.
- [ ] Seller layout renders responsive hamburger menu and drawer below lg viewport breakpoint.
- [ ] No hardcoded fake data in seller dashboard when empty state is present.
- [ ] Footer legal links (/privacy, /terms, /return-policy) respond with HTTP 200.

Decompose into milestones/phases, dispatch specialists, maintain progress in c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_7\progress.md and BRIEFING.md. When work is completely verified, report completion to the sentinel.
