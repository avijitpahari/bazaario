# Execution Plan: Orchestrator 6

## Goal
Complete Bazaario Seller Panel UI Integration by remediating Milestone 5 (Profile & Auction Management), certifying Milestone 5 Gate, executing Milestone 6 (E2E Verification, Adversarial Hardening, Full Regression Pass, Final Forensic Integrity Audit), and handing off complete certified project to parent.

---

## Step 1: Milestone 5 Remediation
- **Worker**: `worker_m5_remedy` (`teamwork_preview_worker`)
- **Working Directory**: `.agents/teamwork/worker_m5_remedy`
- **Scope**:
  1. Create migration `database/migrations/2026_09_30_000003_add_operating_days_to_seller_profiles_table.php` adding nullable json column: `$table->json('operating_days')->nullable()->after('operating_radius_km');`. Run `php artisan migrate`.
  2. Update `app/Models/SellerProfile.php`: add `'operating_days'` to `$fillable` and `'operating_days' => 'array'` to `casts()`.
  3. In `app/Http/Controllers/Seller/SellerProfileController.php::updateProfile`:
     Before validation, normalize `operating_days`:
     ```php
     if ($request->has('operating_days') && is_string($request->input('operating_days'))) {
         $decoded = json_decode($request->input('operating_days'), true);
         if (is_array($decoded)) {
             $request->merge(['operating_days' => $decoded]);
         }
     }
     ```
     Persist `$profile->operating_days = json_encode($request->input('operating_days'));` (or cast directly) on save.
  4. Run tests:
     - `php artisan test tests/Feature/Seller/Milestone5ProfileSecurityChallengeTest.php`
     - `php artisan test tests/Feature/Seller/SellerAuctionAndProfileTest.php`
     - Verify both pass cleanly with 0 failures.

---

## Step 2: Milestone 5 Gate Verification (Iteration 2)
- **Reviewers**:
  - `reviewer_m5_e` (`teamwork_preview_reviewer`): Code quality, validation integrity, Blade & controller alignment.
  - `reviewer_m5_f` (`teamwork_preview_reviewer`): Functional completeness of Profile & Auction features.
- **Challengers**:
  - `challenger_m5_e` (`teamwork_preview_challenger`): Profile, Geolocation, Operating Days, and Password Security stress tests.
  - `challenger_m5_f` (`teamwork_preview_challenger`): Live Wholesale Auction, Bidding Terminal, Reserve Met, and Cancellation Guard tests.
- **Forensic Auditor**:
  - `auditor_m5_c` (`teamwork_preview_auditor`): Forensic integrity verification across M5 implementation and migrations.

---

## Step 3: Milestone 6 (E2E Verification, Adversarial Hardening & Full Regression)
- **Sub-milestones**:
  1. E2E Test Suite Execution (Tiers 1-4) across the entire seller panel workflow (`tests/Feature/Seller/SellerE2EWorkloadTest.php`).
  2. Tier 5 Adversarial Coverage Hardening (`tests/Feature/Seller/SellerAdversarialHardeningTest.php`): Cross-tenant isolation, unauthorized mutation prevention, SQLi/XSS boundaries, malicious payload rejection.
  3. Full Application Regression Pass (`php artisan test`): 600+ tests spanning all admin, customer, and seller panel modules.
  4. Final Forensic Integrity Audit (`auditor_m6_final`): End-to-end audit for zero cheats, authentic implementations, complete feature delivery.

---

## Step 4: Final Synthesis & Handoff
- Update `PROJECT.md` marking all milestones DONE.
- Compile final `handoff.md`.
- Send completion message to Parent Sentinel (`26364ffc-7fb7-4878-8526-d74c7c0ab55a`).
