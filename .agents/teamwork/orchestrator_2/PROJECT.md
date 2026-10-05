# Project: AI-Powered Configurable E-Commerce Marketplace (Bazaario)

## Architecture
- **Framework**: Laravel 11.x (PHP 8.2+) with Blade templates, Alpine.js, Tailwind CSS
- **Authentication**: Multi-guard session authentication (`user`, `seller`, `admin`) with Role-Based Access Control (RBAC)
- **Database**: SQLite (testing `:memory:`) / MySQL (production) with Eloquent ORM, Foreign Key constraints, DB Transactions (`DB::transaction`) and Row Locking (`lockForUpdate`)
- **Localization**: Multi-lingual support (English `en`, Hindi `hi`, Bengali `bn`) with persistent session, database `users.preferred_language`, and `SetLocale` middleware
- **Hyperlocal Commerce**: Distance/radius calculations using seller coordinates (`latitude`, `longitude`)
- **Cart & Pricing**: Hybrid session/database cart with multi-seller item grouping and merchant-specific subtotals
- **Order Lifecycle**: Dual-tier order model (`orders` and `seller_orders`) with courier tracking, cancellation windows, and 1-click reorder

---

## Feature Inventory
| # | Feature | Description | Milestone | Source |
|---|---------|-------------|-----------|--------|
| 1 | User Registration | Registration form creates account in `users` table via OTP | M1 | ORIGINAL_REQUEST §R1 |
| 2 | User Login | User Login authenticates credentials cleanly across roles | M1 | ORIGINAL_REQUEST §R1 |
| 3 | User Logout | User Logout terminates session and redirects | M1 | ORIGINAL_REQUEST §R1 |
| 4 | Forgot Password | Initiates OTP / reset email token flow | M1 | ORIGINAL_REQUEST §R1 |
| 5 | Password Reset | Sets new password via token/OTP verification | M1 | ORIGINAL_REQUEST §R1 |
| 6 | Role-Based Access | RBAC prevents unauthorized access across customer, seller, admin | M1 | ORIGINAL_REQUEST §R1 |
| 7 | Language Selector | Dropdown allows selecting English, Hindi, or Bengali | M1 | ORIGINAL_REQUEST §R2 |
| 8 | Language Persistence | Preference persists across sessions and DB user profiles | M1 | ORIGINAL_REQUEST §R2 |
| 9 | Hero Banner | Responsive marketing visuals & CTA | M2 | ORIGINAL_REQUEST §R3 |
| 10 | Featured Sellers | Displays verified seller business profiles | M2 | ORIGINAL_REQUEST §R3 |
| 11 | Nearby Stalls | Displays hyperlocal sellers based on radius / distance | M2 | ORIGINAL_REQUEST §R3 |
| 12 | Categories Grid | Lists all active product categories | M2 | ORIGINAL_REQUEST §R3 |
| 13 | Trending Products | Displays popular marketplace listings | M2 | ORIGINAL_REQUEST §R3 |
| 14 | 'For Sellers' Pricing | Renders transparent commission structure | M2 | ORIGINAL_REQUEST §R3 |
| 15 | How It Works / About | Platform documentation for buyers and sellers | M2 | ORIGINAL_REQUEST §R3 |
| 16 | View All Products | Catalog browsing `/products` with server pagination | M2 | ORIGINAL_REQUEST §R4 |
| 17 | Filter by Category | Updates catalog results by selected category | M2 | ORIGINAL_REQUEST §R4 |
| 18 | Filter by Price Range | Filters items within min/max price bounds | M2 | ORIGINAL_REQUEST §R4 |
| 19 | Filter by Seller Rating | Filters products by minimum rating stars | M2 | ORIGINAL_REQUEST §R4 |
| 20 | Filter by Distance/Radius | Filters items from nearby sellers using radius slider | M2 | ORIGINAL_REQUEST §R4 |
| 21 | Keyword Search | Performs fuzzy search on product names and descriptions | M2 | ORIGINAL_REQUEST §R4 |
| 22 | Search Results Grid | Displays matching items with match counts and empty state | M2 | ORIGINAL_REQUEST §R4 |
| 23 | Sort Products | Orders items by price (asc/desc), rating, or newest | M2 | ORIGINAL_REQUEST §R4 |
| 24 | Product Detail Gallery | Full responsive multi-image gallery | M3 | ORIGINAL_REQUEST §R5 |
| 25 | Description & Specs | Complete product description & specifications table | M3 | ORIGINAL_REQUEST §R5 |
| 26 | Seller Type Badge | Badges: Farmer, Kirana Store, Dark Store, Individual | M3 | ORIGINAL_REQUEST §R5 |
| 27 | Dynamic Price & Stock | Displays real-time price & stock availability | M3 | ORIGINAL_REQUEST §R5 |
| 28 | Unit Type Badge | Badges: kg, dozen, bundle, litre | M3 | ORIGINAL_REQUEST §R5 |
| 29 | Seller Trust Score | Displays seller trust score badge | M3 | ORIGINAL_REQUEST §R5 |
| 30 | Add to Cart | Adds product to cart with flash feedback & top nav badge | M3 | ORIGINAL_REQUEST §R5 |
| 31 | Buy Now | Adds item and immediately navigates to checkout | M3 | ORIGINAL_REQUEST §R5 |
| 32 | View Reviews | Renders real customer reviews and rating breakdown | M3 | ORIGINAL_REQUEST §R5 |
| 33 | Add Review & Rating | Authenticated buyers submit rating & comment | M3 | ORIGINAL_REQUEST §R5 |
| 34 | Seller-Grouped Cart | Cart items clearly grouped by seller block | M3 | ORIGINAL_REQUEST §R6 |
| 35 | Update Item Quantity | Dynamic quantity update and subtotal recalculation | M3 | ORIGINAL_REQUEST §R6 |
| 36 | Remove Item | Removes item from cart cleanly | M3 | ORIGINAL_REQUEST §R6 |
| 37 | Seller-wise Subtotals | Renders subtotal breakdown per merchant block | M3 | ORIGINAL_REQUEST §R6 |
| 38 | Promo Coupon Code | Validates coupon (min amount, max discount, limit) and applies discount | M3 | ORIGINAL_REQUEST §R6 |
| 39 | Proceed to Checkout | Initiates checkout workflow from cart | M4 | ORIGINAL_REQUEST §R7 |
| 40 | Enter Delivery Address | Select existing address or enter new address inline | M4 | ORIGINAL_REQUEST §R7 |
| 41 | Select Delivery Time Slot | Available delivery time windows picker | M4 | ORIGINAL_REQUEST §R7 |
| 42 | Select Payment Method | Supports Cash on Delivery (COD) payment | M4 | ORIGINAL_REQUEST §R7 |
| 43 | Place Order | Commits parent Order, SellerOrders, OrderItems, decrements stock | M4 | ORIGINAL_REQUEST §R7 |
| 44 | View Order Summary | Presents comprehensive order receipt breakdown | M4 | ORIGINAL_REQUEST §R7 |
| 45 | View Order History | Lists all past customer orders with details | M4 | ORIGINAL_REQUEST §R7 |
| 46 | Track Order (Per Seller) | Displays courier progress and telemetry per merchant | M4 | ORIGINAL_REQUEST §R7 |
| 47 | View Order Status | Live status: Pending, Processing, Shipped, Delivered, Cancelled | M4 | ORIGINAL_REQUEST §R7 |
| 48 | Cancel Order | Allows buyer to cancel order before shipment | M4 | ORIGINAL_REQUEST §R7 |
| 49 | 1-Click Reorder | Populates cart with items from past order | M4 | ORIGINAL_REQUEST §R7 |
| 50 | View Profile | Displays personal info & buyer trust rank badge | M1 | ORIGINAL_REQUEST §R8 |
| 51 | Edit Profile | Updates name, phone, and bio | M1 | ORIGINAL_REQUEST §R8 |
| 52 | Upload Profile Image | Uploads and updates user avatar | M1 | ORIGINAL_REQUEST §R8 |
| 53 | Change Password | Verifies old password and updates to new password | M1 | ORIGINAL_REQUEST §R8 |
| 54 | Manage Delivery Addresses | Full CRUD for customer addresses | M1 | ORIGINAL_REQUEST §R8 |

---

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| M1 | Core Foundation & User Identity | Features 1-8, 50-54 (Auth, Security, RBAC, Localization, Profile & Address CRUD) | None | DONE |
| M2 | Catalog, Discovery & Hyperlocal Browsing | Features 9-23 (Homepage discovery, Nearby stalls, ProductController, Catalog filtering, Pagination) | M1 | DONE |
| M3 | Product Detail, Reputation & Cart Engine | Features 24-38 (Product view specs/gallery, Seller/Unit badges, Reviews, Cart grouping, Coupons) | M1, M2 | DONE |
| M4 | Checkout & Order Lifecycle Engine | Features 39-49 (Checkout UI, Time slots, COD, Multi-seller order split, Cancellation, Reorder) | M1, M2, M3 | DONE |
| M5 | E2E Test Suite Pass & Adversarial Hardening | Phase 1: 100% E2E test pass; Phase 2: Adversarial coverage hardening | M1, M2, M3, M4, TEST_READY | DONE |

---

## Interface Contracts
### Auth & Localization ↔ All Views
- `SetLocale` middleware checks `session('locale')`, fallback to `auth()->user()->preferred_language`, fallback to `config('app.locale')`.
- Language switch endpoint: `POST /language` with payload `['locale' => 'en'|'hi'|'bn']`.
- Role redirection helper: `redirectByRole(User $user)` correctly logs in the appropriate guard (`user`, `seller`, `admin`) to prevent redirect loops.

### Products & Discovery ↔ Spatial Coordinates
- `SellerProfile` model contains `latitude` and `longitude` fields (or defaults to linked address coordinates).
- Proximity formula (Haversine formula in SQLite/MySQL) filters by radius in kilometers.

### Cart ↔ Checkout
- Cart items grouped by `product.seller_id`.
- Checkout accepts `delivery_address_id`, `delivery_time_slot`, `payment_method` (COD).
- Order creation commits:
  - 1 `orders` row
  - N `seller_orders` rows (one per unique seller)
  - M `order_items` rows linked to parent order and sub-orders
  - Decrements `products.stock`
  - Creates `payments` row (`payment_method` = `'cod'`, `status` = `'pending'`)

---

## Code Layout
- Controllers:
  - `app/Http/Controllers/AuthController.php` (Auth, Password Reset, RBAC)
  - `app/Http/Controllers/LanguageController.php` (Language switching)
  - `app/Http/Controllers/ProductController.php` (Catalog, Category, Product Detail, Search, Filtering)
  - `app/Http/Controllers/User/CartController.php` (Seller-grouped cart, coupons)
  - `app/Http/Controllers/User/CheckoutController.php` (Checkout flow, order split)
  - `app/Http/Controllers/User/OrderController.php` (Order history, tracking, cancel, reorder)
  - `app/Http/Controllers/User/ProfileController.php` (Profile view/edit, bio, avatar)
  - `app/Http/Controllers/User/AddressController.php` (Address CRUD)
  - `app/Http/Controllers/User/ReviewController.php` (Review store & aggregation)
- Middleware:
  - `app/Http/Middleware/SetLocale.php`
  - `app/Http/Middleware/SellerMiddleware.php`
- Views:
  - `resources/views/auth/` (`forgot-password.blade.php`, `reset-password.blade.php`)
  - `resources/views/user/products/` (`index.blade.php`, `show.blade.php`, `category.blade.php`)
  - `resources/views/user/cart/index.blade.php`
  - `resources/views/user/checkout/` (`index.blade.php`, `success.blade.php`)
  - `resources/views/user/account/orders/` (`index.blade.php`, `show.blade.php`)
  - `resources/views/user/account/` (`profile.blade.php`, `edit-profile.blade.php`, `addresses.blade.php`)
  - `resources/views/pages/` (`how-it-works.blade.php`)
- Tests:
  - `tests/Feature/AuthAndLocalizationTest.php`
  - `tests/Feature/CatalogAndDiscoveryTest.php`
  - `tests/Feature/ProductDetailAndCartTest.php`
  - `tests/Feature/CheckoutAndOrderLifecycleTest.php`
  - `tests/Feature/UserProfileAndAddressTest.php`
  - `tests/Feature/E2EMarketplaceSuiteTest.php`
