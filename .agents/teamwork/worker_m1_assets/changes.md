# Milestone 1: Asset & Infrastructure Optimization — Changes Report

## Overview
Milestone 1 addresses Issues **P1, P2, P3, P4, P17, and P34** by optimizing the asset pipeline, unifying design system tokens under Tailwind CSS v4, bundling Alpine.js natively via Vite, and resolving WCAG 1.4.4 viewport scaling compliance.

---

## 1. P1: Elimination of Double CSS Loading
### Files Modified:
- `resources/views/layouts/app.blade.php`
- `resources/views/index.blade.php`

### Changes:
- **`resources/views/layouts/app.blade.php`**:
  - Removed static legacy fallback `<link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}">`.
  - Removed redundant Tailwind CDN script (`<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>`) and inline `tailwind.config` block.
  - Retained Vite asset directive `@vite(['resources/css/app.css', 'resources/js/app.js'])`.
- **`resources/views/index.blade.php`**:
  - Removed static legacy fallback `<link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}">`.
  - Retained `@vite(['resources/css/app.css', 'resources/js/app.js'])`.

---

## 2. P2 & P17: Tailwind CDN Removal & Design System Unification
### Files Modified:
- `resources/css/app.css`
- `resources/views/layouts/seller.blade.php`

### Changes:
- **`resources/css/app.css`**:
  - Under Tailwind CSS v4 `@theme`, registered all seller panel tokens alongside customer tokens:
    - **Typography**: `--font-heading: 'Space Grotesk', sans-serif;`
    - **Brand Colors**: `--color-brand-primary`, `--color-brand-bg`, `--color-brand-slate`, `--color-brand-amber`, `--color-brand-amber-dark`, `--color-brand-green`, `--color-brand-muted`, `--color-brand-outline`.
    - **Role / Theme Colors**: `--color-primary`, `--color-secondary`, `--color-secondary-container`, `--color-on-secondary-container`, `--color-on-tertiary-container`.
    - **Surfaces**: `--color-surface` (`#FBF9F4`), `--color-surface-container-lowest` (`#FFFFFF`), `--color-surface-container-low` (`#F5F3EE`), `--color-surface-container` (`#EFEEE9`), `--color-surface-container-high` (`#EAE8E3`), `--color-surface-container-highest` (`#E4E2DE`).
    - **Text & Outline**: `--color-on-surface` (`#1B1C19`), `--color-on-surface-variant` (`#45464D`), `--color-outline` (`#76777D`), `--color-outline-variant` (`#C6C6CD`).
    - **Status & Error**: `--color-error` (`#BA1A1A`), `--color-error-container` (`#FFDAD6`), `--color-on-error-container` (`#93000A`).
    - **Borders & Radii**: `--radius-custom: 14px;`, `--radius-14: 14px;`.
    - **Elevation Shadows**: `--shadow-subtle`, `--shadow-card`.
- **`resources/views/layouts/seller.blade.php`**:
  - Removed CDN `<script src="https://cdn.tailwindcss.com"></script>` and entire inline `<script>tailwind.config = { ... }</script>` block.
  - Replaced with `@vite(['resources/css/app.css', 'resources/js/app.js'])`.

---

## 3. P3 & P4: Alpine.js Single Instance & Vite Bundling
### Files Modified:
- `package.json`
- `resources/js/app.js`
- `resources/views/user/products/index.blade.php`
- `resources/views/index.blade.php`
- `resources/views/layouts/seller.blade.php`
- `resources/views/pages/how-it-works.blade.php`
- `resources/views/docs/fees-and-commission.blade.php`
- `resources/views/docs/become-a-seller.blade.php`

### Changes:
- **`package.json`**:
  - Installed `alpinejs` (`^3.17.4`) in dependencies.
- **`resources/js/app.js`**:
  - Added clean Alpine import and initialization:
    ```javascript
    import './bootstrap';
    import Alpine from 'alpinejs';

    window.Alpine = Alpine;

    Alpine.start();
    ```
- **CDN Script Cleanup**:
  - Removed `<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>` from:
    - `resources/views/user/products/index.blade.php`
    - `resources/views/index.blade.php`
    - `resources/views/layouts/seller.blade.php`
    - `resources/views/pages/how-it-works.blade.php`
    - `resources/views/docs/fees-and-commission.blade.php`
    - `resources/views/docs/become-a-seller.blade.php`

---

## 4. P34: Viewport Meta WCAG 1.4.4 Compliance
### Files Modified / Created:
- `resources/views/user/products/index.blade.php`
- `resources/views/pages/how-it-works.blade.php`
- `resources/views/docs/fees-and-commission.blade.php`
- `resources/views/docs/become-a-seller.blade.php`
- `resources/views/pages/fees-and-commission.blade.php` (created copy with standardized tags)
- `resources/views/pages/become-a-seller.blade.php` (created copy with standardized tags)

### Changes:
- Replaced `<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />`
  with `<meta name="viewport" content="width=device-width, initial-scale=1.0">`.
- Enabled pinch-to-zoom across mobile devices in full compliance with WCAG 1.4.4.

---

## 5. Verification Results
- **`npm run build`**:
  - Exit code: 0
  - Generated: `public/build/assets/app-CiNVm5cs.css` (218.27 kB), `public/build/assets/app-WC-ZjLzv.js` (106.90 kB), `manifest.json`.
- **`php artisan route:list`**:
  - Exit code: 0
  - 154 routes verified, 0 broken bindings.
- **`php artisan test`**:
  - Exit code: 0
  - 706 tests passed (5001 assertions), 0 failures.
