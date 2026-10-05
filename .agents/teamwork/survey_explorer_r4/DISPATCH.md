## 2026-10-01T09:20:06Z
You are survey_explorer_r4, a teamwork_preview_explorer.
Working directory: c:\xampp\htdocs\bazaario\.agents\teamwork\survey_explorer_r4
Project root: c:\xampp\htdocs\bazaario

MANDATORY FIRST STEP: Read ORIGINAL_REQUEST.md at c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md before doing anything else.
Also read UI_LOGIC_PROBLEMS.md at c:\xampp\htdocs\bazaario\UI_LOGIC_PROBLEMS.md.

Your objective:
Conduct a detailed codebase exploration of Requirement R4 (UI Interactive Components & Navigation Integrity) and Design System Unification, covering issues P17–P22, P29–P30, P34, P36–P38, along with establishing the baseline test and route health.

Specifically investigate in the codebase:
1. Design System Unification (P17):
   - Compare Tailwind configuration and tokens between customer views (tailwind.config.js, resources/css/app.css) and seller views (inline config in layouts/seller.blade.php).
   - Detail the token mapping: amber-action vs brand-primary, font-display vs font-heading, rounded-2xl vs rounded-[14px], surface colors. How to unify them cleanly into tailwind.config.js and app.css without breaking either panel.
2. Interactive Components (P18, P19, P34, P37, P38):
   - resources/views/components/nav-user.blade.php: Notification bell badge hardcoded "3 New" (line 231) — check how to query/bind unread count dynamically.
   - Cart popover @mouseenter (line 264–266) — analyze touch device behavior and how to support both touch click and desktop hover gracefully.
   - Viewport meta in resources/views/user/products/index.blade.php (line 13) and check all other views for user-scalable=no or maximum-scale=1.0.
   - Footer currency switcher (components/footer.blade.php): Analyze bazaarioLocalization() Alpine component, currOpen state, and missing toggle button.
   - Footer translation switch back to 'en': Analyze applyTranslation and window.location.reload().
3. Navigation & Links (P20, P21, P22, P29, P30, P36):
   - Footer dead links in components/footer.blade.php: Social media links (lines 15–27), legal links (lines 54, 61–64 for /privacy, /terms, /return-policy).
   - "Deals" filter in footer: line 37, check filter=escrow vs filter=deals.
   - Home navbar unification (P29): Compare components/nav.blade.php vs components/nav-user.blade.php.
   - Popular search chips in index.blade.php: lines 100–108, hardcoded product names.
   - Category filter in nav-user.blade.php: lines 102–126, search params vs category slug routes.
4. Baseline Test & Route Execution:
   - Run php artisan test to record the exact baseline test suite count and assertions.
   - Run php artisan route:list to detect any currently broken route bindings or missing controllers.

Produce a comprehensive technical report with exact file paths, line numbers, test outputs, and actionable implementation recommendations.
Write your report to c:\xampp\htdocs\bazaario\.agents\teamwork\survey_explorer_r4\report.md and write a standard handoff.md.
Then send a completion message back to parent.
