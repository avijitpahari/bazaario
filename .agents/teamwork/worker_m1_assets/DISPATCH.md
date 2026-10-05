## 2026-10-01T09:30:56Z
You are worker_m1_assets, a teamwork_preview_worker.
Working directory: c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m1_assets
Project root: c:\xampp\htdocs\bazaario

MANDATORY FIRST STEP: Read ORIGINAL_REQUEST.md at c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md before starting work.
Also read UI_LOGIC_PROBLEMS.md at c:\xampp\htdocs\bazaario\UI_LOGIC_PROBLEMS.md.
Also read the detailed survey reports:
- c:\xampp\htdocs\bazaario\.agents\teamwork\survey_miner_r1_r2\report.md
- c:\xampp\htdocs\bazaario\.agents\teamwork\survey_explorer_r4\report.md

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Your objective:
Implement Milestone 1: Asset & Infrastructure Optimization (Issues P1, P2, P3, P4, P17, P34).

Exclusively owned files for this milestone:
1. resources/views/layouts/app.blade.php
2. resources/views/index.blade.php
3. resources/views/layouts/seller.blade.php
4. resources/views/user/products/index.blade.php
5. resources/views/pages/fees-and-commission.blade.php
6. resources/views/pages/become-a-seller.blade.php
7. resources/views/pages/how-it-works.blade.php
8. resources/css/app.css
9. resources/js/app.js
10. package.json

Tasks:
1. P1 (Double CSS load):
   - In resources/views/layouts/app.blade.php: Remove the static <link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}"> fallback.
   - In resources/views/index.blade.php: Remove the static <link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}"> fallback.
2. P2 & P17 (Tailwind CDN Removal & Design System Unification in Seller Panel):
   - In resources/css/app.css: Under @theme (Tailwind v4), add all required seller and customer design system tokens so that seller views (surface, surface-container-*, brand.amber, brand.primary, font-heading, radius-custom, text-on-surface, border-outline, etc.) are compiled natively by Vite without relying on CDN. Refer to survey_explorer_r4\report.md § 2 for the exact token mappings.
   - In resources/views/layouts/seller.blade.php: Remove the CDN script <script src="https://cdn.tailwindcss.com"></script> and the inline <script>tailwind.config = { ... }</script>.
   - In resources/views/layouts/seller.blade.php: Replace with @vite(['resources/css/app.css', 'resources/js/app.js']).
3. P3 & P4 (Alpine.js Single Instance & Vite Bundling):
   - Check if alpinejs is installed in package.json. If not, install it (or npm install alpinejs) and configure resources/js/app.js to import Alpine and start it cleanly:
     import Alpine from 'alpinejs';
     window.Alpine = Alpine;
     Alpine.start();
   - Remove redundant CDN Alpine <script> tags from:
     * resources/views/user/products/index.blade.php (line ~24)
     * resources/views/index.blade.php (line ~1127)
     * resources/views/layouts/seller.blade.php (line ~74)
   - Ensure components registered on window (like bazaarioLocalization) continue to work properly with the bundled Alpine.
4. P34 (Viewport Meta WCAG 1.4.4 Compliance):
   - Remove maximum-scale=1.0, user-scalable=no from the viewport meta tag in:
     * resources/views/user/products/index.blade.php
     * resources/views/pages/fees-and-commission.blade.php
     * resources/views/pages/become-a-seller.blade.php
     * resources/views/pages/how-it-works.blade.php
   - Standardize to: <meta name="viewport" content="width=device-width, initial-scale=1.0">
5. Build & Test Verification:
   - Run npm run build to verify asset compilation succeeds without errors.
   - Run php artisan test to ensure all tests pass (expecting 706+ tests, 0 failures).
   - Run php artisan route:list to ensure no broken routes.

Document all changes, exact diffs, and verification commands/outputs in c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m1_assets\changes.md and handoff.md.
Then send a completion message back to parent.

## 2026-10-01T10:57:35Z
**Context**: Milestone 1 Implementation (Assets & Infrastructure)
**Content**: Checking on your current status and progress. Please report your current step and update progress.md.
**Action**: Continue implementation and report back with progress update.
