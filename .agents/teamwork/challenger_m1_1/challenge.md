# Empirical Challenge Report: Admin Platform Configuration & Branding Settings (M1)

**Challenger**: `challenger_m1_1` (Critic / Empirical Challenger)  
**Target Milestone**: Milestone 1 (Requirement 1: Admin Platform Configuration & Branding Settings)  
**Worker Under Review**: `worker_m1`  
**Test Suite Executed**: `tests/Feature/AdminSettingsChallenge1Test.php`  
**Date**: 2026-10-12  

---

## Challenge Summary

**Overall risk assessment**: **LOW** (Empirically Robust)  
**Verdict**: **APPROVE**

Worker `worker_m1`'s implementation of Admin Platform Configuration & Branding Settings withstood all 14 adversarial challenge vectors across 102 assertions. The endpoints, guards, validation layers, database persistence models, Blade rendering escapes, and mode-switching lifecycle hooks behaved strictly according to specification under attack inputs.

---

## Challenges & Stress-Test Scenarios

### [High] Challenge 1: Unauthorized Mutation of Platform Settings by Untrusted Actors
- **Assumption challenged**: Only authenticated, active administrators with the `admin` guard can view or mutate platform-wide configuration settings (`site_name`, `site_tagline`, `commission_rate`, `platform_mode`, `site_logo`).
- **Attack scenario**:
  1. Unauthenticated guest attempts `POST /admin/settings` (both standard web form and JSON API requests).
  2. Authenticated standard customer (`role: user`) attempts `POST /admin/settings`.
  3. Authenticated merchant seller (`role: seller`) attempts `POST /admin/settings`.
  4. Deactivated/suspended administrator (`role: admin`, `status: suspended`) attempts `POST /admin/settings`.
- **Blast radius**: Complete compromise of platform branding, commission hijack (e.g. setting commission to 0% or 100%), or unauthorized marketplace mode toggles.
- **Empirical test**: Executed `test_guest_unauthorized_attempts_to_mutate_settings_are_rejected`, `test_customer_cannot_mutate_admin_settings`, `test_merchant_seller_cannot_mutate_admin_settings`, and `test_suspended_admin_cannot_mutate_admin_settings`.
- **Result**: **PASS**. Guests are redirected to `admin.login` (web) or receive HTTP 401 (JSON). Customers, sellers, and suspended admins receive HTTP 302/403 with session logout/invalidation via `AdminMiddleware`. Database records in `SiteSetting` remain unmodified.

---

### [High] Challenge 2: Rapid Mode Toggling & Immediate Cache / Query Consistency
- **Assumption challenged**: Toggling between `multi_vendor` and `single_vendor` modes applies immediately to seller registration gates, role selection cards, and homepage shelf queries without stale cache anomalies or database desynchronization.
- **Attack scenario**: Admin rapidly switches mode from `multi_vendor` to `single_vendor`, back to `multi_vendor`, and immediately back to `single_vendor`. At each step, immediate requests are fired against `POST /register/send-otp`, `GET /register`, and `GET /`.
- **Blast radius**: In single-vendor mode, prospective sellers registering or seeing vendor cards; in multi-vendor mode, legitimate merchants being locked out due to stale cached flags.
- **Empirical test**: Executed `test_rapid_platform_mode_toggling_immediate_consistency` and `test_invalid_platform_mode_input_is_rejected`.
- **Result**: **PASS**. 
  - `single_vendor` immediately triggers 422 JSON on `POST /register/send-otp` with `"Seller registration is currently disabled in Single-Vendor mode."`.
  - `#card-type-seller` is immediately suppressed in `/register`.
  - `#nearby-stalls` is immediately suppressed in `/`.
  - Restoring `multi_vendor` immediately restores all seller entry points.
  - Submitting an unsupported mode (`platform_mode: invalid_hybrid_mode`) triggers validation failure without altering DB state.

---

### [Medium] Challenge 3: Commission Rate Boundary Values & Arithmetic Extremes
- **Assumption challenged**: Commission rates accept extreme legitimate values (0.0% zero-fee marketplace and 100.0% full retention consignments) as well as fractional percentages, while rejecting negative numbers, numbers >100%, non-numeric strings, and SQL injection strings.
- **Attack scenario**:
  1. Boundary inputs: `commission_rate = 0.0` and `commission_rate = 100.0`.
  2. Fractional inputs: `commission_rate = 12.375`.
  3. Negative values: `commission_rate = -5.0`.
  4. Over-limit values: `commission_rate = 100.5`.
  5. Non-numeric / injection strings: `"twelve_percent"` and `'10; DROP TABLE site_settings;'`.
- **Blast radius**: Financial calculation errors, negative payouts, platform fee calculation crashes, or SQL injection vulnerabilities.
- **Empirical test**: Executed `test_commission_rate_boundaries_zero_and_hundred_percent` and `test_commission_rate_negative_and_out_of_bounds_rejected`.
- **Result**: **PASS**. 0.0% and 100.0% are cleanly persisted and read by `SiteSetting::getCommissionRate()` without fallback collision. Negative, >100%, and string inputs are rejected by Laravel request validation (`nullable|numeric|min:0|max:100`), preserving existing rates.

---

### [High] Challenge 4: Cross-Site Scripting (XSS) & HTML Tag Injection in Branding
- **Assumption challenged**: Malicious scripts and HTML tags injected into `site_name` and `site_tagline` are sanitized before storage and escaped when rendered in Blade views.
- **Attack scenario**: Attacker submits payloads:
  - `site_name`: `<script>alert("PWNED_NAME")</script>Bazaario Prime`
  - `site_tagline`: `<img src=x onerror="alert('PWNED_TAGLINE')">Handmade Crafts & Gifts`
- **Blast radius**: Stored XSS executing in admin dashboard, user navigation headers, invoice titles, and email templates.
- **Empirical test**: Executed `test_html_script_injection_and_sanitization_in_site_name_and_tagline`.
- **Result**: **PASS**.
  - `AdminDashboardController::updateSettings` strips all HTML tags using `strip_tags()` before persisting to `SiteSetting`.
  - Rendered output in Blade templates (`resources/views/layouts/admin.blade.php`, `admin/settings/index.blade.php`) uses `{{ }}` escaping.
  - Zero raw script or image event handlers rendered into DOM responses.

---

### [Medium] Challenge 5: Malicious File Uploads & Oversized Branding Assets
- **Assumption challenged**: Only valid image files under 2MB can be uploaded as `site_logo`. PHP scripts, shell scripts, and oversized files are blocked.
- **Attack scenario**:
  1. Uploading `backdoor.php` with `application/x-php`.
  2. Uploading `exploit.sh` with `text/x-shellscript`.
  3. Uploading an oversized image (3.5 MB, exceeding the 2048 KB limit).
- **Blast radius**: Remote Code Execution (RCE) via uploaded script execution on web server or disk exhaustion via massive file uploads.
- **Empirical test**: Executed `test_malicious_and_oversized_logo_uploads_rejected`.
- **Result**: **PASS**. Request validation (`image|mimes:png,jpg,jpeg,svg,webp|max:2048`) caught and rejected all adversarial payloads.

---

### [Low] Challenge 6: Partial Updates & Unicode / Multilingual Characters
- **Assumption challenged**: Updating a single setting does not wipe other settings; internationalized character sets and emojis are cleanly supported.
- **Attack scenario**:
  1. Submit only `commission_rate = 18.0` without passing `site_name` or `site_tagline`.
  2. Submit Unicode and emoji strings: `"Bazaario 🛍️ Artisans"` and `"Handmade in India — शुद्ध एवं प्रामाणic ✨"`.
- **Blast radius**: Accidental loss of configuration values during partial updates, or database character encoding crashes.
- **Empirical test**: Executed `test_partial_settings_updates_preserve_existing_values` and `test_unicode_and_special_characters_in_branding`.
- **Result**: **PASS**. Partial updates strictly mutate specified keys; Unicode/emoji strings persist and render without truncation or corruption.

---

## Stress Test Results Table

| # | Test Scenario | Expected Behavior | Actual Behavior | Result |
|---|---|---|---|:---:|
| 1 | Guest attempts `POST /admin/settings` | 302 redirect / 401 JSON; 0 DB change | 302 redirect / 401 JSON; DB unchanged | **PASS** |
| 2 | Customer attempts `POST /admin/settings` | 302/403 forbidden; 0 DB change | 302 redirected; DB unchanged | **PASS** |
| 3 | Merchant attempts `POST /admin/settings` | 302/403 forbidden; 0 DB change | 302 redirected; DB unchanged | **PASS** |
| 4 | Suspended admin attempts mutation | Invalidate session; 302 to login | Session invalidated; redirected | **PASS** |
| 5 | Mode toggling (`multi` -> `single` -> `multi` -> `single`) | Immediate enforcement on OTP, views & home | Immediate enforcement on all targets | **PASS** |
| 6 | Invalid platform mode string (`invalid_hybrid_mode`) | Validation error; DB mode unchanged | Validation error; DB mode unchanged | **PASS** |
| 7 | Commission rate at 0.0% & 100.0% | Both persist cleanly; return exact floats | Stored cleanly; returns 0.0 and 100.0 | **PASS** |
| 8 | Negative, >100%, and non-numeric commission rates | Validation errors; rates unchanged | Validation errors; rates unchanged | **PASS** |
| 9 | Stored XSS injection in site name & tagline | Stripped on save; escaped in Blade | Stripped on save; escaped in Blade | **PASS** |
| 10 | Strings exceeding max lengths (>100, >255 chars) | Validation errors | Validation errors | **PASS** |
| 11 | Malicious files (`.php`, `.sh`) & oversized image (>2MB) | Validation errors; no file stored | Validation errors; no file stored | **PASS** |
| 12 | Edge-case helper calls (empty/whitespace/bad logo) | Graceful default fallbacks without errors | Safe defaults returned without errors | **PASS** |
| 13 | Partial settings update | Unspecified fields retain prior values | Unspecified fields retained | **PASS** |
| 14 | Multilingual Unicode & Emoji strings | Stored and rendered without corruption | Rendered cleanly without 500 | **PASS** |

**Summary**: 14/14 test cases passed (102 assertions), 0 failures.

---

## Unchallenged Areas

- **CDN distribution latency**: Browser cache invalidation for static images served through external CDNs is environment-dependent and outside the scope of Milestone 1 application logic.
- **Physical disk space limits on storage driver**: Physical OS disk full edge cases during file upload are handled by underlying PHP/OS disk write operations.

---

## Final Verdict

**APPROVE**. Milestone 1 (Admin Platform Configuration & Branding Settings) is resilient, properly hardened, securely gated, and fully operational.
