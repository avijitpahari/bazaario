# E2E Test Infra: Bazaario Marketplace

## Test Philosophy
- Opaque-box, requirement-driven. Derived strictly from `ORIGINAL_REQUEST.md` (Features 1 to 54).
- Methodology: Category-Partition + Boundary Value Analysis (BVA) + Pairwise Combinatorial Testing + Real-World Workload Testing.

## Feature Inventory & Target Tiers
| # | Feature | Source | Tier 1 (Min 5) | Tier 2 (Min 5) | Tier 3 (Pairwise) | Tier 4 (Scenario) |
|---|---------|--------|:--------------:|:--------------:|:-----------------:|:-----------------:|
| 1-6 | Authentication & RBAC | ORIGINAL_REQUEST §R1 | 30 | 30 | ✓ | ✓ |
| 7-8 | Vernacular Localization | ORIGINAL_REQUEST §R2 | 10 | 10 | ✓ | ✓ |
| 9-15 | Discovery & Public Info | ORIGINAL_REQUEST §R3 | 35 | 35 | ✓ | ✓ |
| 16-23 | Product Browsing & Filtering | ORIGINAL_REQUEST §R4 | 40 | 40 | ✓ | ✓ |
| 24-33 | Product Detail & Reputation | ORIGINAL_REQUEST §R5 | 50 | 50 | ✓ | ✓ |
| 34-38 | Cart & Multi-Seller Operations | ORIGINAL_REQUEST §R6 | 25 | 25 | ✓ | ✓ |
| 39-49 | Checkout & Order Lifecycle | ORIGINAL_REQUEST §R7 | 55 | 55 | ✓ | ✓ |
| 50-54 | User Profile & Address CRUD | ORIGINAL_REQUEST §R8 | 25 | 25 | ✓ | ✓ |

## Test Architecture
- Test Runner: `php artisan test` (PHPUnit on SQLite `:memory:` database)
- Pass/Fail Semantics: 0 exit code, 0 failures, 0 errors, 100% assertions passed
- Directory Layout: `tests/Feature/E2E/` and `tests/Feature/`
  - `tests/Feature/AuthAndLocalizationTest.php`
  - `tests/Feature/CatalogAndDiscoveryTest.php`
  - `tests/Feature/ProductDetailAndCartTest.php`
  - `tests/Feature/CheckoutAndOrderLifecycleTest.php`
  - `tests/Feature/UserProfileAndAddressTest.php`
  - `tests/Feature/MarketplaceE2EWorkloadTest.php`

## Real-World Application Scenarios (Tier 4)
| # | Scenario | Features Exercised | Complexity |
|---|----------|--------------------|------------|
| 1 | Full Buyer Onboarding to First Purchase | F1, F2, F7, F8, F9, F16, F21, F24, F30, F39, F40, F41, F42, F43, F44 | High |
| 2 | Multi-Seller Mixed Cart & Coupon Checkout | F16, F26, F30, F34, F35, F37, F38, F39, F42, F43, F46 | High |
| 3 | Hyperlocal Discovery to Nearby Stall Purchase | F11, F20, F26, F29, F30, F31, F40, F43 | High |
| 4 | Order Cancellation and 1-Click Reorder Flow | F43, F45, F47, F48, F49, F34, F44 | High |
| 5 | Buyer Reputation & Review Submission Cycle | F43, F45, F24, F32, F33, F50, F51 | Medium |
| 6 | Vernacular Switch Across Entire Navigation | F7, F8, F9, F12, F16, F24, F34, F39, F50 | High |

## Coverage Thresholds
- Tier 1: ≥5 test cases per feature (270 total)
- Tier 2: ≥5 boundary/edge test cases per feature (270 total)
- Tier 3: Pairwise cross-feature interactions (≥54 combinations)
- Tier 4: Real-world multi-step application scenarios (≥27 scenarios)
- Total Target: ≥621 test assertions verifying all 54 features
