# ONBOARDING UI & WIZARD SPECIFICATION REPORT (MILESTONE 1 - R1)

**Role:** Onboarding UI & Wizard Spec Miner (`spec_miner_m1_1`)  
**Target Working Directory:** `c:\xampp\htdocs\bazaario`  
**Authoritative Reference:** `c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md` (Timestamp: 2026-09-30T04:46:52Z)  
**Primary Design References:**  
- `stitch_bazaario_seller_onboarding_portal/warm_modernist_commerce/DESIGN.md`  
- `stitch_bazaario_seller_onboarding_portal/bazaario_seller_logo/code.html`  
- `stitch_bazaario_seller_onboarding_portal/bazaario_seller_onboarding_approval/code.html`  
- `stitch_bazaario_seller_onboarding_portal/bazaario_seller_dashboard_performance/code.html`  

---

## 1. Observation

### 1.1 Source Template Evidence
1. **Design System Specification (`warm_modernist_commerce/DESIGN.md:1-210`)**:
   - Canvas Foundation: `#FFFDF8` (Warm off-white base)
   - Elevated Card Surfaces: `#FFFFFF`
   - Commanding Deep Slate: `#0F172A`
   - Amber Action Cue: `#F5A623` (Dark Amber: `#D98205` / `#B45309`)
   - Success Green: `#16A34A` (`#009842`)
   - Alert / Error Red: `#BA1A1A` (`#FFDAD6`)
   - Muted Copy: `#45464D`
   - Structural Outline: `#76777D` / `#E2DFD7` (applied at 20%–40% opacity)
   - Typography Stack:
     * Display / Headings: `Space Grotesk`
     * Interface / Form Body: `Inter`
     * Data / Currency / Codes: `JetBrains Mono`
   - Universal Geometry: Locked to `14px` border-radius (`rounded-[14px]`). Micro-chips and badges use `6px` (`rounded-[6px]`). **Pill buttons (`rounded-full`) are strictly prohibited** except for circular pips/avatars.

2. **Brand Vector Asset (`bazaario_seller_logo/code.html:1-10`)**:
   - Exact SVG format (`viewBox="0 0 240 60"`):
     * Slate container: `rect width="48" height="48" rx="14" fill="#0F172A"`
     * Monogram glyph: Amber "B" path (`#F5A623`) with slate inner negative spaces (`#0F172A`)
     * Green status indicator: `circle cx="38" cy="18" r="4" fill="#16A34A"`
     * Brand text: `text font-family="'Space Grotesk'" font-size="28" font-weight="700" fill="#0F172A"` ("BAZAARIO")
     * Seller tag: Amber badge `rx="6" fill="#F5A623" fill-opacity="0.18"` with JetBrains Mono `SELLER` text (`#B45309`).

3. **Onboarding Wizard & Approval Terminal (`bazaario_seller_onboarding_approval/code.html:1-1514`)**:
   - Lines 80–121: Top brand header with Bazaario Seller logo, Sign In link, and "Seller Help" modal trigger.
   - Lines 125–141: Responsive mobile progress bar (`STEP 01 OF 06`, linear bar with dynamic width, 6 stage dots).
   - Lines 145–237: Desktop sticky sidebar (`col-span-4`) with vertical timeline (Steps 01 to 06) and Verified Seller Guarantee trust badge (zero commission for 90 days, 24-48h compliance review).
   - Lines 243–332: **Step 01 Account Registration** with full name, email, phone (+91 prefix), password with 4-bar reactive strength meter, confirm password, and terms checkbox.
   - Lines 336–447: **Step 02 Seller Type Selection** with 4 interactive selection cards: Farmer 🌾, Kirana Store 🏪, Dark Store 📦, Individual 👤.
   - Lines 451–563: **Step 03 Shop / Farm Details** with dynamic type badge, common shop fields, dynamic produce/hours attributes per seller type, business description, and storefront image drag-and-drop dropzone with preview/replace/remove actions.
   - Lines 567–691: **Step 04 Business Location & Geo Pin** with browser HTML5 GPS auto-detect button, address fields (street, city, district, state, PIN), visual mock radar canvas with pulse ring, and manual Latitude / Longitude coordinate controls.
   - Lines 695–816: **Step 05 Application Review** with 4 consolidated cards (Account, Seller Type, Business, Location) each featuring a 1-click "Edit ✏️" jump button back to that specific step, plus "Submit for Approval" CTA.
   - Lines 820–948 & 1327–1502: **Step 06 Await Admin Approval Terminal** with 5-stage status machine (`pending`, `approved`, `more_info`, `rejected`, `suspended`), 3-stage progress timeline (Submitted -> Under Review -> Approved), summary strip, dynamic notice boxes, and locked dashboard security notice.
   - Lines 951–970: Confirmation Modal triggered prior to final submission.

4. **Master Workspace Layout (`bazaario_seller_dashboard_performance/code.html:1-120`)**:
   - Fixed `w-72` (`288px`) left sidebar (`bg-surface-container-low` / `#FAF8F2` with subtle shadow):
     * Header: Brand logo + Space Grotesk `BAZAARIO` + JetBrains Mono `SELLER CENTER` uppercase.
     * Navigation: Dashboard, Orders (counter badge), Products (collapsible: All Products, Add Product, Inventory), Auctions (collapsible: My Auctions, Create Auction, Live Auctions, History), Payouts (currency pill), Trust & Performance, Shop Profile, Settings.
     * Bottom utilities: Help & Support, Notifications (counter pill), Logout action.
   - Fixed top header (`left-72 right-0 h-16 bg-surface/80 backdrop-blur-xl`):
     * Global search bar with `search` icon and `⌘K` keyboard badge.
     * Notification bell with unread alert badge.
     * Profile pill: Shop Name, verified checkmark (`check_circle` `#16A34A`), seller type badge (e.g. `Farmer • Verified Seller`).
   - Content canvas (`pl-72 pt-16 min-h-screen bg-surface`):
     * Accommodates global auto-dismissing flash alerts and `@yield('content')`.

### 1.2 Codebase State
- `resources/views/layouts/seller-onboarding.blade.php`: **Does not exist yet** (0 files found).
- `resources/views/layouts/seller.blade.php`: **Does not exist yet** (0 files found).
- `resources/views/seller/onboarding/wizard.blade.php`: **Does not exist yet** (directory `resources/views/seller/onboarding/` does not exist).
- `resources/views/seller/pending.blade.php`: Exists as a 363-line early draft using older tokens (`Plus Jakarta Sans`, raw CSS gradients) rather than extending `layouts.seller-onboarding`, lacking the full 5-stage state transitions and exact visual fidelity of `code.html`.
- `app/Http/Middleware/SellerMiddleware.php`: Only checks role `seller` and status `active`; does not yet verify `sellerProfile->status === 'approved'` or redirect pending sellers to `/seller/pending`.

---

## 2. Features Discovered

| # | Category | Feature | Description | Inputs | Outputs | Error Behavior | Discovered Via |
|---|---|---|---|---|---|---|---|
| 1 | Layout | Onboarding Master Container | Distraction-free Warm Modernist container layout with top brand bar, help modal, Space Grotesk / Inter / JetBrains Mono typography, Material Symbols Outlined, and Tailwind theme tokens. | Page title, flash sessions (`success`, `error`, `info`) | Clean header with logo, help modal, centered `max-w-7xl` content slot, footer. | Unauthenticated requests gracefully handled. | `warm_modernist_commerce/DESIGN.md`, `bazaario_seller_onboarding_approval/code.html:80-121` |
| 2 | Layout | Help & FAQ Modal | Accessible Alpine.js modal triggered from header "Seller Help" button displaying registration FAQs and support contact details. | Click trigger `#help` | Slide-down modal with KYC guidelines, contact links (`support@bazaario.in`), phone. | Traps focus, dismisses on backdrop click or `Escape`. | `bazaario_seller_onboarding_approval/code.html:113-118` |
| 3 | Layout | Seller Workspace Layout (`layouts.seller`) | Full seller workspace shell with fixed `w-72` sidebar, top header with search & notification, profile pill, and flash toast alerts. | Active route name, authenticated seller user & profile | Full operating shell with route-aware sidebar links, badge counters, search bar with `⌘K`, verified profile pill. | Redirects non-sellers or unapproved sellers to login/pending. | `bazaario_seller_dashboard_performance/code.html:1-120` |
| 4 | Layout | Global Auto-Dismissing Flash Toasts | Floating toast notification component handling session flash feedback. | `session('success')`, `session('error')`, `session('warning')`, `session('info')` | Top-right floating card with icon, message, dismiss button, auto-fade after 4-5s. | Gracefully handles empty session states without DOM clutter. | `layouts/user.blade.php:137-154`, `bazaario_seller_dashboard_performance/code.html` |
| 5 | Wizard | Step 01: Account Registration | Form capturing user personal credentials with real-time 4-bar password complexity evaluation and terms checkbox. | `name`, `email`, `phone` (+91), `password`, `password_confirmation`, `terms` | Sanitized credentials payload, password meter update, Step 02 activation. | Prevents step navigation on missing fields or password mismatch. | `bazaario_seller_onboarding_approval/code.html:243-332` |
| 6 | Wizard | Real-time Password Strength Meter | 4-tier visual password complexity evaluator with color-coded bars (Weak, Fair, Strong, Very Secure). | `password` input string | 4 colored bars (`#EF4444`, `#F5A623`, `#16A34A`), percentage and label text. | Shows 0% / gray for empty input; requires uppercase, number, special char. | `bazaario_seller_onboarding_approval/code.html:286-298, 1225-1248` |
| 7 | Wizard | Step 02: Seller Type Selection Cards | 4 interactive merchant type selection cards: Farmer 🌾, Kirana Store 🏪, Dark Store 📦, Individual 👤 with custom badges. | `seller_type` (`farmer`, `kirana`, `darkstore`, `individual`) | Amber highlighted active card with checkmark pip, dynamic step 3 configuration. | Blocks proceeding to step 3 until a type is selected. | `bazaario_seller_onboarding_approval/code.html:336-447, 1202-1222` |
| 8 | Wizard | Step 03: Business & Farm Details | Captures commercial business info with dynamic labels and fields adapted to selected seller type. | `shop_name`, `owner_name`, `contact_phone`, `public_email`, dynamic type fields | Populated commercial profile, Step 04 unlocked. | Missing required fields stop form submit with red borders. | `bazaario_seller_onboarding_approval/code.html:451-563, 976-1120` |
| 9 | Wizard | Dynamic Type-Specific Attribute Fields | Type-specific input group: Farm Type/Produce/Acres (Farmer), Category/Hours (Kirana), Dispatch/Shifts (Dark Store), Category/Capacity (Individual). | Dynamic change trigger from Step 02 | Contextual input fields injected into Step 03 without page refresh. | Persists entered values when switching between types. | `bazaario_seller_onboarding_approval/code.html:986-1105` |
| 10 | Wizard | Storefront Image Dropzone Component | Drag-and-drop / file browse target with client-side image preview thumbnail, file size calculation, and Replace/Remove buttons. | File input (`image/*`, PNG/JPG/WEBP <= 5MB) | Thumbnail preview (`1600x900`), filename, size badge, "Ready for upload" green status. | Rejects files > 5MB or non-image MIME types. | `bazaario_seller_onboarding_approval/code.html:507-548, 1251-1270` |
| 11 | Wizard | Step 04: Geolocation & Coordinate Capture | Captures address and GPS coordinates with browser auto-detect button, manual Lat/Lng overrides, and radar map preview. | `address_line1`, `city`, `district`, `state`, `pincode`, `latitude`, `longitude` | Auto-populated coordinates, radar pulse animation, "Radius: 15 km delivery" tag. | Graceful fallback to manual inputs if browser GPS denied. | `bazaario_seller_onboarding_approval/code.html:567-691, 1272-1286` |
| 12 | Wizard | HTML5 Browser GPS Auto-Detect Button | 1-click button triggering `navigator.geolocation.getCurrentPosition()` with loading spinner and locked status. | Click trigger `#btn-detect-loc` | Populated lat/lng coordinates, green "GPS Synced" status. | Shows error message if location access denied; leaves manual inputs editable. | `bazaario_seller_onboarding_approval/code.html:589-595, 1272-1286` |
| 13 | Wizard | Stylized Radar Map Preview | Visual topographic canvas with radial dot grid, pulsating amber wave, central pin marker, and shop name tag. | `latitude`, `longitude`, `shop_name` | Animated radar sweep/pulse showing localized delivery radius. | Renders default coordinate center if coordinates unselected. | `bazaario_seller_onboarding_approval/code.html:642-661` |
| 14 | Wizard | Step 05: Review Matrix & 1-Click Edit Jumps | Consolidated review layout displaying 4 cards (Account, Seller Type, Business, Location) each with a 1-click "Edit ✏️" button. | Review view activation (`goToStep(5)`) | Synchronized summary values across all 4 previous steps with quick jump anchors. | Missing sections prompt the user to complete preceding step. | `bazaario_seller_onboarding_approval/code.html:695-816, 1288-1306` |
| 15 | Wizard | Final Submission Confirmation Modal | Modal dialog prompting merchant to verify application authenticity before final server submission. | Click "Submit for Approval" | Modal with warning notice, "Go Back & Edit", and "Yes, Submit Now" triggers. | Closes on backdrop click or Go Back; submits form on confirm. | `bazaario_seller_onboarding_approval/code.html:951-970, 1308-1325` |
| 16 | Gate | 5-Stage Approval Waiting Terminal (`seller.pending`) | Dedicated status dashboard reflecting 5 possible merchant states: `pending`, `approved`, `more_info`, `rejected`, `suspended`. | `$profile->status`, `$profile->rejection_reason` | Status header, 3-stage timeline, summary strip, contextual notice box, action CTA. | Redirects approved sellers to `/seller/dashboard`. | `bazaario_seller_onboarding_approval/code.html:820-948, 1327-1502` |
| 17 | Gate | Interactive Approval State Simulator | Dev/preview mode bar allowing switching between 5 states (`pending`, `approved`, `more_info`, `rejected`, `suspended`) for visual verification. | Click triggers `#btn-state-*` | Instant UI transition between the 5 terminal states with updated badges and timeline. | Visible only in local/dev environment or testing. | `bazaario_seller_onboarding_approval/code.html:822-844` |
| 18 | Gate | Dashboard Security Lockout Notice | Visual security banner informing unapproved sellers that the operational dashboard is locked. | Non-approved seller session | Banner: "Restricted Access: Live Seller Operating Dashboard locked until admin approval." | Blocks access to `/seller/dashboard` via `SellerMiddleware`. | `bazaario_seller_onboarding_approval/code.html:931-938` |

---

## 3. Edge Cases & Observed Spec Behaviors

| # | Feature | Input / Trigger | Observed Spec Behavior |
|---|---|---|---|
| 1 | Onboarding Access Gate | Non-approved seller enters `/seller/dashboard` URL | `SellerMiddleware` intercepts request; redirects user to `route('seller.pending')` with flash warning notice (`code.html:931-938`). |
| 2 | Approved Seller on Pending Gate | Approved seller visits `/seller/pending` | Route handler / middleware detects `$profile->status === 'approved'` and immediately redirects to `route('seller.dashboard')`. |
| 3 | GPS Permission Denied | User clicks "Use Current Location" but browser denies permission | The browser geolocation error callback triggers; the button displays "Manual Entry Required"; the manual Latitude & Longitude inputs are highlighted with an informative helper note (`code.html:663-675`). |
| 4 | Dynamic Seller Type Switch | Merchant changes selection from "Farmer" to "Kirana Store" at Step 02 | Dynamic fields container in Step 03 replaces Farm Type / Acres with Kirana Category / Daily Operating Hours; review summary in Step 05 immediately reflects the new icon (🏪) and type metadata (`code.html:977-1045`). |
| 5 | Invalid Image Upload | Merchant drops a 12MB file or non-image file (`.pdf`) | Dropzone rejects the file; empty state remains visible with a red error toast: "Image must be PNG, JPG or WEBP under 5MB" (`code.html:525-526`). |
| 6 | Weak Password Entry | Merchant enters a 6-character lowercase password | Password strength meter displays "Very Weak (25%)" with 1 red bar; form submit is blocked by `required` and `minlength="8"` client validation (`code.html:286-298, 1225-1248`). |
| 7 | Step Jump via Review Matrix | Merchant clicks "Edit ✏️" on Location card in Step 05 | Wizard instantaneously jumps to Step 04 with all previously entered street address, PIN, and Lat/Lng coordinates preserved in the inputs (`code.html:784-786`). |
| 8 | More Info Required State | Admin requests document clarification (`status = 'more_info'`) | `/seller/pending` displays blue warning header, compliance desk remark box with timestamp and instructions, and an "Update Application Now" CTA button (`code.html:1421-1448`). |
| 9 | Rejection State | Admin rejects application (`status = 'rejected'`) | `/seller/pending` displays red header, rejection reason with required corrections box, timeline dot 2 changes to red ✕, and a "Correct Location & Resubmit" button (`code.html:1449-1477`). |
| 10 | Suspended State | Admin suspends seller account (`status = 'suspended'`) | `/seller/pending` displays neutral slate suspension notice, compliance notice, and a "Contact Support" link to `support@bazaario.in` (`code.html:1478-1502`). |

---

## 4. Component-Level Implementation Strategy for Worker

### 4.1 Component 1: `resources/views/layouts/seller-onboarding.blade.php`
- **Purpose**: Distraction-free, focused container layout for the seller registration wizard, KYC submission, and approval waiting terminal.
- **Head Requirements**:
  * Viewport, UTF-8, CSRF token meta tags.
  * Fonts: Google Fonts `Space Grotesk:wght@500;600;700`, `Inter:wght@400;500;600;700`, `JetBrains Mono:wght@500;700`.
  * Google Material Symbols Outlined.
  * Tailwind CSS with Warm Modernist theme tokens:
    ```javascript
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              bg: '#FFFDF8',
              slate: '#0F172A',
              amber: '#F5A623',
              'amber-dark': '#D98205',
              green: '#16A34A',
              muted: '#45464D',
              outline: '#E2DFD7',
              surface: '#FFFFFF',
              card: '#FFFFFF',
              subtle: '#FAF8F2'
            }
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
            heading: ['Space Grotesk', 'sans-serif'],
            mono: ['JetBrains Mono', 'monospace'],
          },
          borderRadius: {
            'custom': '14px',
            '14': '14px',
          }
        }
      }
    };
    ```
  * Alpine.js (`defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"`).
- **Body Structure**:
  * Root wrapper: `min-h-screen bg-brand-bg flex flex-col selection:bg-brand-amber selection:text-brand-slate`.
  * **Top Brand Header (`h-20 border-b border-[#EAE6DC] bg-white/95 backdrop-blur sticky top-0 z-40`)**:
    - Left: Bazaario Seller Logo vector asset (from `bazaario_seller_logo/code.html`) with Space Grotesk `BAZAARIO` and JetBrains Mono `SELLER` badge. Subtitle: "Smarter Shopping. Local Sellers. Live Auctions."
    - Right:
      * Quick preview step pills (`01`, `02`, `03`, `04`, `05`, `06 Status`) in dev mode.
      * "Sign In" link to `route('login')` (or Home if authenticated).
      * "Seller Help" modal trigger button with Material Symbol `help` / amber icon.
  * **Seller Help Modal (`x-show="helpModalOpen"` via Alpine.js)**:
    - Backdrop with `bg-brand-slate/60 backdrop-blur-sm`.
    - Modal card (`max-w-lg rounded-[14px] bg-white p-6 shadow-modal border border-brand-outline`).
    - FAQs: "How does onboarding work?", "What documents are required?", "Commission structure (0% first 90 days, 10% thereafter)".
    - Direct support: WhatsApp helpline (`+91 98765 43210`), Email (`support@bazaario.in`).
    - Close button with `Escape` / click-outside dismiss.
  * **Flash Toast Notifications**:
    - Floating fixed container for `session('success')`, `session('error')`, `session('info')`.
    - Auto-dismiss after 4 seconds with manual close trigger.
  * **Main Content Container**:
    - `<main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">@yield('content')</main>`.
  * **Footer**:
    - Minimalist Warm Modernist footer with copyright, links to Marketplace, Terms, Privacy Policy.

---

### 4.2 Component 2: `resources/views/layouts/seller.blade.php`
- **Purpose**: Full operational seller workspace shell hosting dashboard, catalog management, orders, payouts, wholesale auctions, and settings.
- **Head Requirements**:
  * Same Warm Modernist font stack (`Space Grotesk`, `Inter`, `JetBrains Mono`) and Material Symbols Outlined.
  * Full Tailwind configuration with extended color palette matching `warm_modernist_commerce/DESIGN.md`:
    - `surface: '#fbf9f4'`, `surface-container-low: '#f5f3ee'`, `surface-container: '#efeee9'`, `surface-container-high: '#eae8e3'`, `surface-container-highest: '#e4e2de'`
    - `primary: '#0F172A'`, `secondary: '#835500'`, `secondary-container: '#feae2c'`, `on-secondary-container: '#6b4500'`
    - `on-tertiary-container: '#009842'`, `error: '#ba1a1a'`, `outline: '#76777d'`
- **Fixed Left Sidebar (`w-72` / `288px`, fixed, h-screen)**:
  * Container: `bg-surface-container-low z-50 flex flex-col justify-between py-space-md px-space-sm shadow-[0_1px_8px_rgba(0,0,0,0.04)] border-r border-[#E2DFD7]/60`.
  * Top Section:
    - Bazaario Seller Center logo (inline SVG from `bazaario_seller_logo/code.html`).
    - Store Title: `BAZAARIO` (`headline-sm`, font-semibold) + `SELLER CENTER` (`system-label`, uppercase, text-secondary).
  * Navigation Menu (`overflow-y-auto max-h-[calc(100vh-280px)]`):
    - **Dashboard**: `route('seller.dashboard')` (icon: `dashboard`).
    - **Orders**: `route('seller.orders.index')` (icon: `inventory_2`, count pill for pending orders).
    - **Products Group** (icon: `shopping_bag`, collapsible via Alpine `x-data="{ open: true }"`):
      * All Products (`route('seller.products.index')`)
      * Add Product (`route('seller.products.create')`)
      * Inventory (`route('seller.products.inventory')`)
    - **Auctions Group** (icon: `gavel`, collapsible via Alpine `x-data="{ open: true }"`):
      * My Auctions (`route('seller.auctions.index')`)
      * Create Auction (`route('seller.auctions.create')`)
      * Live Auctions (`route('seller.auctions.live')`)
      * Auction History (`route('seller.auctions.history')`)
    - **Payouts**: `route('seller.payouts.index')` (icon: `account_balance_wallet`, amount pill e.g. `₹12.4k`).
    - **Trust & Performance**: (icon: `verified_user`, 94/100 score badge).
    - **Shop Profile**: `route('seller.account.profile')` (icon: `storefront`).
    - **Settings**: `route('seller.account.settings')` (icon: `settings`).
  * Bottom Section:
    - Help & Support (icon: `help`).
    - Notifications (icon: `notifications`, unread count pill).
    - Logout action form (`route('logout')`, text-error styling with `logout` icon).
- **Fixed Top Header (`left-72 right-0 h-16 fixed bg-surface/80 backdrop-blur-xl z-40`)**:
  * Search Bar: Max-w-lg container with `search` icon, placeholder "Search orders, products, auctions, payouts...", and `⌘K` keyboard shortcut badge.
  * Right Controls:
    - Notification icon with unread alert pip.
    - Verified Seller Pill: Avatar icon, Shop Name (`$profile->shop_name`), verified checkmark (`check_circle` `#16A34A`), and Seller Type tag (e.g. `Farmer • Verified Seller`).
- **Main Operating Canvas (`pl-72 pt-16 min-h-screen bg-surface px-8`)**:
  * Flash Alert Toasts container.
  * `@yield('content')`.

---

### 4.3 Component 3: `resources/views/seller/onboarding/wizard.blade.php`
- **Extends**: `layouts.seller-onboarding`.
- **Layout Architecture**: 2-Column Split Layout (`grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start`).
- **Left Column (`lg:col-span-4 sticky top-28 space-y-6`)**:
  * Partner Onboarding context card: Title, description of seller advantages.
  * **Vertical Stepper Navigation (`space-y-1 relative`)**:
    - Connecting line: `absolute left-[19px] top-4 bottom-4 w-0.5 bg-[#EAE6DC]`.
    - Step 01: Account (Basic credentials & verification).
    - Step 02: Seller Type (Farmer, Kirana, Dark Store, Individual).
    - Step 03: Shop / Farm (Business info & catalogue profile).
    - Step 04: Location (Geo coordinates & service district).
    - Step 05: Review (Verify submission packet).
    - Step 06: Approval (Admin review & live dashboard gate).
    - States: Completed (`bg-brand-green text-white ✓`), Active (`bg-brand-amber text-brand-slate ring-4 ring-amber-100`), Inactive (`bg-[#FAF8F2] border border-brand-outline text-brand-muted`).
  * Verified Seller Guarantee trust card: Green shield icon, 0% commission for 90 days, 24-48h review.
- **Mobile Progress Tracker (`lg:hidden mb-8`)**:
  * Step counter text (`STEP 01 OF 06`), step title, 16.66% fill progress bar, 6 status dots.
- **Right Column (`lg:col-span-8`)**:
  * Form wrapping with POST route `route('seller.onboarding.submit')` and `enctype="multipart/form-data"`.
  * **Step 1: Account Registration**:
    - Inputs: `full_name`, `email`, `phone` (+91 prefix), `password`, `password_confirmation`.
    - Real-time password strength meter:
      * 4 bar segments evaluated dynamically on input.
      * Text label: Weak/Fair/Strong/Very Secure.
    - Terms and conditions checkbox.
    - Action: "Continue" button jumps to Step 2.
  * **Step 2: Seller Type Selection**:
    - 4 Interactive Cards Grid (`grid-cols-1 sm:grid-cols-2 gap-4`):
      1. Farmer: 🌾 icon, "Direct Harvest" and "Live Auctions" chips.
      2. Kirana Store: 🏪 icon, "Hyperlocal 30m" and "FMCG Stock" chips.
      3. Dark Store: 📦 icon, "High Volume" and "Automated Dispatch" chips.
      4. Individual: 👤 icon, "Home Made" and "No GST Req." chips.
    - Selected state: `border-2 border-brand-amber bg-[#FFFDF7]` with amber checkmark pip.
    - Hidden input: `<input type="hidden" name="seller_type" id="input-seller-type" value="farmer">`.
    - Actions: "Back" (to Step 1) and "Continue" (to Step 3).
  * **Step 3: Business & Farm Details**:
    - Badge: Dynamically displays `FARMER PROFILE`, `KIRANA STORE PROFILE`, etc.
    - Common Fields:
      * `shop_name` (dynamic label: "Farm / Agro Enterprise Name" for Farmer, "Kirana Store Name" for Kirana, etc.).
      * `owner_name` ("Owner / Representative Name").
      * `contact_phone` ("Business Contact Phone").
      * `public_email` ("Public Contact Email").
    - Dynamic Type-Specific Fields Container (`#dynamic-type-fields`):
      * Farmer: `farm_type` (dropdown: Organic Agriculture, Hydroponic, Dairy, Horticulture), `farm_produce` (text), `farm_area` (acres text).
      * Kirana: `kirana_cat` (dropdown: Grocery & Staples, Packaged Foods, Household Care), `kirana_hours` (operating hours text).
      * Dark Store: `dark_type` (dropdown: 15-Minute Express, Cold Chain, Regional Hub), `dark_hours` (facility hours text).
      * Individual: `ind_cat` (dropdown: Home Made Food, Handicrafts, Handloom, Pottery), `ind_capacity` (daily packages text).
    - `description`: Textarea for farm/shop narrative.
    - **Storefront Image Upload Component**:
      * Drag & drop zone with dashed border.
      * Hidden file input `<input type="file" name="storefront_image" accept="image/*">`.
      * Empty state with camera icon, "Browse Files", and file constraints (PNG, JPG, WEBP <= 5MB).
      * Preview state: image thumbnail, file size badge, filename, "Ready for upload" green badge, "Replace" and "Remove" buttons.
    - Actions: "Back" (to Step 2) and "Continue" (to Step 4).
  * **Step 4: Location & Geo Coordinates**:
    - Recommended GPS Banner: "Use Current Location" button triggering browser `navigator.geolocation`.
    - Loading spinner during GPS acquisition; green "✓ GPS Synced" confirmation on success.
    - Address Fields: `address_line1`, `city`, `district`, `state`, `pincode`.
    - Location Preview & Pin Coordinates Card:
      * Stylized Radar Map Preview: Radial dot background, pulsating amber wave, pin marker with shop name badge, "Radius: 15 km delivery" tag.
      * Manual coordinate inputs: `latitude` (e.g. `22.2984`), `longitude` (e.g. `87.9251`).
    - Actions: "Back" (to Step 3) and "Continue" (to Step 5).
  * **Step 5: Review Application Matrix**:
    - 4 Review Dossier Cards:
      1. Account Credentials: Full Name, Email, Phone (+91) with 1-click "Edit ✏️" button jumping to Step 1.
      2. Seller Type: Selected type icon, title, description with 1-click "Edit ✏️" button jumping to Step 2.
      3. Business Details: Shop name, capacity/produce, description with 1-click "Edit ✏️" button jumping to Step 3.
      4. Location & Geo Pin: Street address, district/state/PIN, coordinates with 1-click "Edit ✏️" button jumping to Step 4.
    - Real-time synchronization function `syncReviewData()` populating all fields prior to display.
    - Action: "Submit for Approval" CTA button (Solid Green `#16A34A`).
  * **Confirmation Modal**:
    - Triggered by "Submit for Approval".
    - Title: "Submit Application?"
    - Warning text regarding accuracy of GPS coordinates and land/shop details.
    - Buttons: "Go Back & Edit" and "Yes, Submit Now" (dispatches POST request to backend).

---

### 4.4 Component 4: `resources/views/seller/pending.blade.php`
- **Extends**: `layouts.seller-onboarding`.
- **Purpose**: Dedicated waiting gate and compliance terminal displaying the seller's application review status across 5 possible states:
  1. `pending`: Awaiting Review (default state upon submission).
  2. `approved`: Application Approved & Verified (with "Proceed to Seller Terminal" link).
  3. `more_info`: More Information Required (with compliance desk remark and update prompt).
  4. `rejected`: Application Not Approved (with rejection reason and resubmit prompt).
  5. `suspended`: Account Access Suspended (with support contact links).
- **Core Elements**:
  * **Interactive State Simulator Bar** (for dev/testing): Buttons to preview all 5 states (`pending`, `approved`, `more_info`, `rejected`, `suspended`).
  * **Main Status Card (`rounded-[14px] p-6 sm:p-10 shadow-card bg-white border border-brand-outline`)**:
    - Status Badge: Monospaced uppercase pill with animated pulse pip (e.g. `STATUS: AWAITING ADMIN APPROVAL`).
    - Main Headline & Subtitle: Dynamic copy based on state.
    - **Key Summary Info Strip (`grid-cols-2 sm:grid-cols-4 p-5 bg-brand-subtle rounded-[14px]`)**:
      * Application ID: `#BZ-{{ str_pad($profile?->id ?? 1, 5, '0', STR_PAD_LEFT) }}`
      * Submission Date: `$profile?->created_at?->format('M d, Y') ?? now()->format('M d, Y')`
      * Seller Type: `$profile?->seller_type_label ?? $profile?->seller_type ?? 'Farmer'`
      * Shop / Farm Name: `$profile?->shop_name ?? 'My Shop'`
    - **Review Progress Timeline (3 Stages)**:
      * Stage 1: Application Submitted (Green ✓ circle, "Uploaded & encrypted").
      * Stage 2: Under Review (Amber ⏳ circle with `ring-4 ring-amber-100`, "Regional verification desk").
      * Stage 3: Approved (Muted 03 circle, "Marketplace live clearance" — transitions to Green ✓ when approved; transitions to Red ✕ if rejected).
    - **Dynamic Notice Box**:
      * In `pending`: "What happens next?" with 24-48h verification SLA explanation.
      * In `more_info`: Blue callout box with compliance officer remark and timestamp.
      * In `rejected`: Red callout box with detailed rejection reason and required corrections.
      * In `approved`: Green celebration box with confirmation of auction and catalog access.
      * In `suspended`: Gray notice box with merchant concierge contact information.
    - **Security Gate Notice**:
      * Red dot indicator + "Restricted Access: Live Seller Operating Dashboard locked until admin approval."
    - **Primary Action CTA**:
      * In `pending`: "Review Submission Summary" button.
      * In `approved`: "Proceed to Seller Terminal" button (`route('seller.dashboard')`).
      * In `more_info`: "Update Application Now" button (jumps to edit details).
      * In `rejected`: "Correct Location & Resubmit" button.
      * In `suspended`: "Contact Support" mailto link.

---

## 5. Logic Chain

1. **Direct Alignment with Authoritative Requirements**:
   - `ORIGINAL_REQUEST.md` (2026-09-30T04:46:52Z) explicitly requests R1 Seller Onboarding Module: step-by-step guided wizard (Farmer, Kirana Store, Dark Store, Individual), shop/farm details, Lat/Lng geolocation, and pending approval gate before granting dashboard access.
   - `PROJECT.md` confirms Milestone 1 scope: layouts (`layouts.seller`, `layouts.seller-onboarding`), `SellerMiddleware` approval enforcement, multi-step onboarding wizard, Lat/Lng GPS capture, pending waiting gate.
2. **Design Tokens & Aesthetic Architecture**:
   - `warm_modernist_commerce/DESIGN.md` establishes clear constraints: `#FFFDF8` canvas, `#0F172A` slate, `#F5A623` amber, `#16A34A` green, `14px` universal corner radii, no pill buttons, Space Grotesk / Inter / JetBrains Mono typography.
   - Extracting the vector asset from `bazaario_seller_logo/code.html` guarantees exact brand identity across both layouts.
3. **Step-by-Step Flow Logic**:
   - Step 1 collects account credentials and evaluates password strength to prevent weak credentials from entering the system.
   - Step 2 determines the merchant archetype (Farmer, Kirana, Dark Store, Individual).
   - Step 3 tailors business questions directly to that archetype (e.g. acres for farmers, operating shifts for dark stores) and accepts storefront imagery.
   - Step 4 uses HTML5 Geolocation to acquire precise Lat/Lng coordinates with manual fallbacks and localized radar visualization.
   - Step 5 provides a 1-click edit review matrix so merchants can fix errors before sending the packet to admin compliance.
   - Step 6 / `seller/pending.blade.php` serves as the non-bypassable security gate keeping non-approved merchants out of `/seller/dashboard`.
4. **Clean Decoupling for Downstream Workers**:
   - Dividing the work into `layouts/seller-onboarding.blade.php`, `layouts/seller.blade.php`, `seller/onboarding/wizard.blade.php`, and `seller/pending.blade.php` provides clean, isolated targets for implementation, rendering tests, and functional gates.

---

## 6. Caveats

1. **Browser Geolocation Sensor Permissions**: The HTML5 Geolocation API requires user consent and HTTPS (or `localhost`). If permission is denied or running in automated test runners without GPS hardware, the implementation must gracefully fall back to default or manually typed coordinates without throwing JavaScript exceptions.
2. **File Upload Mocking in Client Previews**: In the stitch template, image preview uses a client-side `FileReader.readAsDataURL(file)`. In Laravel, the form must submit `multipart/form-data` and backend controllers will persist the uploaded file to `storage/app/public/seller/storefronts/` or `logo_path`.
3. **Dual State Modes on Pending Gate**: In production, `seller/pending.blade.php` renders based on `$profile->status`. The interactive state switcher buttons (`pending`, `approved`, `more_info`, `rejected`, `suspended`) should be enabled in development/testing mode (or bound to an optional query parameter `?preview_state=approved`) to allow visual and automated testing of all 5 UI states.
4. **No Code Implementation**: In accordance with the Specification Miner archetype, no Blade code or controller implementation was performed during this survey.

---

## 7. Conclusion

The specification for Milestone 1 UI components has been thoroughly mined, mapped, and structured down to exact CSS utility classes, DOM trees, reactive JavaScript handlers, and database contracts:
1. `resources/views/layouts/seller-onboarding.blade.php`: Ready for construction as a clean, distraction-free container with the official vector logo, Space Grotesk / Inter / JetBrains Mono font loading, and interactive Seller Help modal.
2. `resources/views/layouts/seller.blade.php`: Ready for construction as the master operating workspace featuring the fixed `w-72` sidebar, route-aware active pill states, header search with `⌘K`, verified profile pill, and auto-dismissing flash alerts.
3. `resources/views/seller/onboarding/wizard.blade.php`: Ready for construction integrating the 5-step guided wizard from `code.html` with real-time password strength, 4 seller type cards, dynamic shop attributes, storefront upload dropzone, HTML5 GPS auto-detect with radar preview, and review summary with 1-click edit jumps.
4. `resources/views/seller/pending.blade.php`: Ready for upgrade into the full 5-stage approval waiting terminal with live dossier strip, 3-stage progress timeline, contextual compliance notice boxes, and locked dashboard security guardrail.

---

## 8. Verification Method

Downstream workers and reviewers can independently verify the implementation using the following procedures:

1. **Blade Syntax & Rendering Verification**:
   ```bash
   php -l resources/views/layouts/seller-onboarding.blade.php
   php -l resources/views/layouts/seller.blade.php
   php -l resources/views/seller/onboarding/wizard.blade.php
   php -l resources/views/seller/pending.blade.php
   ```

2. **Automated Feature & Gate Tests**:
   - Verify that guest/authenticated access to onboarding wizard returns HTTP 200:
     ```bash
     php artisan test --filter=SellerOnboardingTest
     ```
   - Verify that visiting `/seller/pending` returns HTTP 200 for a seller with `status = 'pending'`:
     ```bash
     php artisan test --filter=ChallengerM1AuthLocalizationTest
     ```
   - Verify full seller onboarding flow and security gate lockout:
     ```bash
     php artisan test tests/Feature/Seller/SellerOnboardingTest.php
     ```

3. **Visual DOM Inspection Points**:
   - Inspect `resources/views/layouts/seller-onboarding.blade.php`: Confirm presence of `#0F172A`, `#F5A623`, `#16A34A`, `Space Grotesk`, `Inter`, `JetBrains Mono`, and `rounded-[14px]`.
   - Inspect `resources/views/layouts/seller.blade.php`: Confirm `w-72` fixed sidebar, top header search with `⌘K`, profile pill, and flash toast alerts.
   - Inspect `resources/views/seller/onboarding/wizard.blade.php`: Confirm all 5 steps (Account, Seller Type, Shop Details, Location, Review), 4 seller types, dynamic produce/hours fields, HTML5 GPS button, radar map preview, and 1-click edit buttons.
   - Inspect `resources/views/seller/pending.blade.php`: Confirm 5 approval states (`pending`, `approved`, `more_info`, `rejected`, `suspended`), 3-stage timeline, and dashboard locked notice.
