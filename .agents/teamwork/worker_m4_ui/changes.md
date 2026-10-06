# Changes Record — Milestone 4 (worker_m4_ui)

## 1. CSS Theme & Design System Unification
- **File**: `resources/css/app.css`
- **Details**:
  - Defined all missing stitch tokens under `@theme`:
    - `--color-surface-tint: #4A671E;`
    - `--color-primary-container: #CEF096;`
    - `--color-secondary-fixed: #D3E8D0;`
    - `--color-secondary-fixed-dim: #B7CCB5;`
    - `--color-tertiary-fixed: #A2F1DE;`
    - `--color-tertiary-fixed-dim: #86D4C2;`
    - `--color-tertiary-container: #A2F1DE;`
    - `--color-on-tertiary-container: #00201B;`
  - Rebuilt via Vite (`npm run build` completed cleanly, generating `public/build/assets/app-S82Odjvt.css` in 1.16s).

## 2. Footer Internationalization & Currency Switcher (P37, P38)
- **File**: `resources/views/components/footer.blade.php`
- **Details**:
  - **P37**: Added visible currency selector toggle button with Alpine.js dropdown menu (`currencyOpen`, supports INR, USD, EUR, GBP, JPY, AED, SGD with currency symbols and names).
  - **P38**: Replaced disruptive `window.location.reload()` translation handler with an in-memory `WeakMap` (`textCache`). On switching back to English (`'en'`), text nodes are restored directly in DOM without reloading the page or losing input state.

## 3. Homepage Search Chips (P30)
- **File**: `resources/views/index.blade.php`
- **Details**:
  - Replaced arbitrary non-existent chip categories ("Organic Honey", "Cold Pressed Oils", etc.) with authentic catalog categories and search terms: "Electronics", "Fresh Vegetables", "Handcrafted", "Spices", and "Fashion".
  - Bound chip links to catalog query routes (`route('products.index', ['search' => '...'])`).

## 4. User Navigation Component (P18, P19, P36)
- **File**: `resources/views/components/nav-user.blade.php`
- **Details**:
  - **P18**: Dynamically queried unread notifications count (`$unreadNotificationsCount`), rendering notification badge only when count > 0; removed static "3 New" text and hardcoded dots.
  - **P19**: Refactored mini-cart popover from pure CSS hover to touch-compatible Alpine.js dropdown (`x-data="{ cartOpen: false }"` with touch toggle, click-outside dismissal, and hover fallback).
  - **P36**: Converted category navigation dropdown items from raw query parameters (`/search?q=...`) to clean slug-based category routes (`route('products.category', $cat->slug)`).

## 5. Guest Navigation Component (P29, P36)
- **File**: `resources/views/components/nav.blade.php`
- **Details**:
  - **P29**: Achieved feature parity with `nav-user.blade.php`: unified container width to `max-w-7xl`, added category dropdown with catalog links, integrated search bar with category selector.
  - **P36**: Bound category dropdown to category slug routes (`route('products.category', $cat->slug)`).
  - Added touch-friendly Alpine.js cart popover with click-outside listener.

## 6. Seller Layout & Security Guardrails (P18, P27, P40)
- **File**: `resources/views/layouts/seller.blade.php`
- **Details**:
  - **P40**: Strengthened authentication guardrails: verifies user role is `seller`, checks seller profile approval status, and redirects non-sellers / unapproved accounts away to prevent customer data exposure.
  - **P18**: Dynamically computed `$sellerUnreadNotificationsCount`, rendering notification count badge only when unread count > 0.
  - **P27**: Wired seller notification bell button to `route('seller.account.notifications')`.

## 7. Seller Dashboard Revenue Telemetry (P25, P40)
- **File**: `resources/views/seller/dashboard.blade.php`
- **Details**:
  - **P40**: Added authorization check ensuring only authenticated and approved sellers access dashboard metrics.
  - **P25**: Implemented dynamic 7-day revenue calculation (`$pastSevenDaysRevenue`), chart heights based on actual sales, and an elegant SVG empty state with descriptive notice when revenue in the last 7 days is 0.

## 8. Seller Products Bulk Actions Route & Controller (P24)
- **File**: `routes/web.php`
  - Registered `Route::post('/bulk', [SellerProductController::class, 'bulkAction'])->name('bulk');` under seller products group.
- **File**: `app/Http/Controllers/Seller/SellerProductController.php`
  - Implemented `bulkAction(Request $request)` handling:
    - Validation for `action` (`activate`, `deactivate`, `draft`, `archive`, `delete`) and array of `product_ids`.
    - Strict multi-tenant scoping: only updates products owned by authenticated seller (`seller_id === $user->id`).
    - Safe deletion guardrails: verifies no active orders (`OrderItem -> sellerOrder -> status` not in delivered/cancelled/returned) and no active/scheduled auctions before deletion.
    - Flash messaging and JSON response support.

## 9. Seller Products Master View Bulk Actions (P24, P40)
- **File**: `resources/views/seller/products/index.blade.php`
- **Details**:
  - **P40**: Added seller authorization check.
  - **P24**: Added dedicated `<form id="bulkActionForm" action="{{ route('seller.products.bulk') }}" method="POST">`.
  - Bound product row checkboxes via HTML5 `form="bulkActionForm" name="product_ids[]" value="{{ $product->id }}"`.
  - Added Alpine.js dropdown for Bulk Actions (Active, Inactive, Archive, Delete).
  - Implemented `submitBulkAction(actionName)` JavaScript handler with empty check, confirmation on delete, and form submission.

## 10. Automated Feature Test Suite (P24)
- **File**: `tests/Feature/Seller/SellerProductBulkActionTest.php`
- **Details**:
  - Added 7 comprehensive automated tests:
    1. `test_seller_can_bulk_activate_draft_products`
    2. `test_seller_can_bulk_deactivate_products`
    3. `test_seller_can_bulk_archive_products`
    4. `test_seller_can_bulk_delete_products_without_active_orders`
    5. `test_seller_cannot_bulk_delete_products_with_active_orders_or_auctions`
    6. `test_strict_multi_tenant_isolation_cannot_bulk_mutate_other_seller_products`
    7. `test_validation_fails_on_empty_product_ids_or_invalid_action`
  - Result: 7 passed (24 assertions).
