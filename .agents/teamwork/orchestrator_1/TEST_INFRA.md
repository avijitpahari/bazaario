# E2E Test Infra: Bazaario Marketplace Admin Platform

## Test Philosophy
- Opaque-box, requirement-driven.
- Derived from `ORIGINAL_REQUEST.md` and user-facing specifications.
- Methodology: Category-Partition + BVA + Pairwise + Workload Testing + Adversarial Hardening.

## Feature Inventory & Test Coverage Goals
| # | Feature | Source | Tier 1 (Coverage) | Tier 2 (BVA) | Tier 3 (Pairwise) | Tier 4 (Scenario) |
|---|---------|--------|:-----------------:|:------------:|:-----------------:|:-----------------:|
| 1 | Admin Authentication & Guard | ORIGINAL_REQUEST §R1 | ≥5 tests | ≥5 tests | ✓ | ✓ |
| 2 | Model & Relationship Integrity | survey_explorer_2 | ≥5 tests | ≥5 tests | ✓ | ✓ |
| 3 | Concurrency & Transactions | ORIGINAL_REQUEST §R2 | ≥5 tests | ≥5 tests | ✓ | ✓ |
| 4 | Design System & Validation Alert | ORIGINAL_REQUEST §R3 | ≥5 tests | ≥5 tests | ✓ | ✓ |
| 5 | Merchant KYC Approvals & Rejection | ORIGINAL_REQUEST §R1 | ≥5 tests | ≥5 tests | ✓ | ✓ |
| 6 | Taxonomy & Campaigns | ORIGINAL_REQUEST §R1 | ≥5 tests | ≥5 tests | ✓ | ✓ |
| 7 | Product Catalog & Inline Stock | ORIGINAL_REQUEST §R1 | ≥5 tests | ≥5 tests | ✓ | ✓ |
| 8 | Multi-Seller Consignment Orders | ORIGINAL_REQUEST §R1 | ≥5 tests | ≥5 tests | ✓ | ✓ |
| 9 | Live Auction Terminal | ORIGINAL_REQUEST §R1 | ≥5 tests | ≥5 tests | ✓ | ✓ |
| 10 | Escrow Payouts & Batch NEFT | ORIGINAL_REQUEST §R1 | ≥5 tests | ≥5 tests | ✓ | ✓ |
| 11 | Dispute Mediation & Refunds | ORIGINAL_REQUEST §R1 | ≥5 tests | ≥5 tests | ✓ | ✓ |
| 12 | AI Hub Configuration | ORIGINAL_REQUEST §R1 | ≥5 tests | ≥5 tests | ✓ | ✓ |
| 13 | Real-Time Telemetry & Exports | ORIGINAL_REQUEST §R2 | ≥5 tests | ≥5 tests | ✓ | ✓ |

## Test Architecture
- Test runner: `php artisan test`
- Pass/Fail semantics: Exit code 0, 100% assertions passing
- Target files:
  - `tests/Feature/AdminHardeningTest.php`
  - `tests/Feature/AdminE2ETest.php`
- Layout compliance: All 16 administrative templates render with 0 errors.
