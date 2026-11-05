## 2026-10-12T05:21:03Z
You are an exploration subagent surveying the codebase for Requirement 1: Admin Platform Configuration & Branding Settings (R1).
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_r1
Project root: c:\xampp\htdocs\bazaario
Original Request file: c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (read timestamp ## 2026-10-12T05:17:47Z).

Objective:
Survey the authoritative codebase to map all components, models, controllers, routes, views, and configuration needed for:
1. Admin Platform Configuration & Branding Settings (R1):
   - Admin settings views and backend controllers to manage platform branding (site name, logo upload, tagline), global commission rates, and platform mode switch (Multi-Vendor vs Single-Vendor mode) persisted via `site_settings`.
   - Acceptance criteria: Admin can view and save changes for site name, logo, tagline, and commission rate in admin settings portal. Toggling between Multi-Vendor and Single-Vendor mode updates platform behavior (disabling or hiding multi-vendor registration/stores when in Single-Vendor mode).

Investigation Tasks:
1. Check existing admin routes (`routes/admin.php`, `routes/web.php`) and controllers (`app/Http/Controllers/Admin/...`). Are there existing settings controllers or methods?
2. Check existing admin settings views in `resources/views/admin/settings/` or related admin views. What UI exists?
3. Check if `site_settings` table exists in database/migrations or if `SiteSetting` model exists. If so, inspect schema and existing usage. If not, what schema is needed?
4. Find how platform branding (site name, logo, tagline) is currently rendered across the app (in `resources/views/layouts/`, headers, footers, etc.), and how settings are or should be accessed (e.g. helper function, config cache, or model).
5. Investigate Multi-Vendor vs Single-Vendor mode:
   - Where is seller registration (`/seller/register` or onboarding wizard) handled?
   - Where are seller stores/stalls, nearby stalls, and seller badges listed?
   - How should Single-Vendor mode disable or hide multi-vendor registration and stores?
6. Check existing test coverage for admin settings and identify what tests need to be written.

Deliverables:
- Write your comprehensive survey report to `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_r1\report.md`.
- Write your structured handoff to `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_r1\handoff.md`.
- Send a completion message to parent with the file paths and summary of findings.
Scope boundary: Read-only exploration. DO NOT edit or modify source code files.
