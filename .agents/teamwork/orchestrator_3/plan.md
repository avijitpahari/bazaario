# Plan — Bazaario Seller Panel UI Integration

## Objective
Build a complete, responsive, dynamic Seller Panel UI for Bazaario integrating stitch templates located at `C:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal` into Laravel views, models, controllers, routes, and comprehensive automated test suites.

## Work Breakdown & Milestones

### Phase 0: Survey & Scope Mapping
- Explorer 1 (Spec Miner): Deep-dive into stitch templates at `C:\xampp\htdocs\bazaario\stitch_bazaario_seller_onboarding_portal` (HTML/CSS/JS components, forms, layouts, assets).
- Explorer 2 (Backend Codebase): Investigate existing Laravel models, migrations, controllers, routes, auth, seller structure in `c:\xampp\htdocs\bazaario`.
- Explorer 3 (Test Infra): Investigate existing PHPUnit/Pest test infrastructure, database seeding, test runners in `c:\xampp\htdocs\bazaario`.
- Synthesize into `PROJECT.md` at project root with Architecture, Feature Inventory, Milestones, and Interface Contracts.

### Milestone 1: Seller Onboarding Module & Access Control (R1)
- Multi-step guided wizard for seller type (Farmer, Kirana Store, Dark Store, Individual).
- Shop/farm details, geo-location coordinates (Lat/Lng).
- Pending approval state ("Await Admin Approval").
- Access control middleware / gates preventing unapproved sellers from accessing the seller dashboard.

### Milestone 2: Seller Dashboard & Performance Analytics (R2)
- Real-time dynamic stats: Total orders, revenue, listed products, low-stock alerts, own trust score.
- Dynamic data binding to Laravel backend.
- Responsive layout integrated from stitch templates.

### Milestone 3: Product & Inventory Management (R3)
- Product CRUD with custom unit types (kg, dozen, bundle, litre).
- Harvest date & expiry window tracking; auto-flag / hide stale perishable listings.
- Image uploads & stock quantity management.

### Milestone 4: Order & Payout Management (R4)
- Seller-scoped order filtering (view own orders only).
- Order status transitions (Processing, Out for Delivery, Fulfilled).
- Assigned delivery slots display.
- Transparent commission rate breakdown & net payout summary calculation.

### Milestone 5: Profile & Auction Management (R5)
- Shop profile editing, location coordinates (Lat/Lng), password update.
- Auction creation: starting price, reserve price, minimum increment, start/end timestamps.
- Live bids monitoring, highest bid indicator, auction cancellation/management.

### Milestone 6: E2E Test Suite Pass & Adversarial Hardening
- Complete automated test suite covering Tiers 1-4.
- Adversarial test coverage hardening (Tier 5).
- Full regression pass against existing marketplace tests.
- Forensic integrity audit.

### Final Phase: Compilation & Sentinel Handoff
- Compile handoff.md with evidence chains, test runs, and layout verification.
- Report completion to Sentinel.
