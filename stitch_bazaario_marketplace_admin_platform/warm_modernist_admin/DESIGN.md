---
name: Warm Modernist Admin
colors:
  surface: '#faf8ff'
  surface-dim: '#dad9e2'
  surface-bright: '#faf8ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f4f3fb'
  surface-container: '#eeedf6'
  surface-container-high: '#e8e7f0'
  surface-container-highest: '#e2e2ea'
  on-surface: '#1a1b21'
  on-surface-variant: '#524534'
  inverse-surface: '#2f3037'
  inverse-on-surface: '#f1f0f9'
  outline: '#857462'
  outline-variant: '#d7c3ae'
  surface-tint: '#835500'
  primary: '#835500'
  on-primary: '#ffffff'
  primary-container: '#f5a623'
  on-primary-container: '#644000'
  inverse-primary: '#ffb955'
  secondary: '#565e74'
  on-secondary: '#ffffff'
  secondary-container: '#dae2fd'
  on-secondary-container: '#5c647a'
  tertiary: '#006e2d'
  on-tertiary: '#ffffff'
  tertiary-container: '#50ce6f'
  on-tertiary-container: '#005421'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffddb4'
  primary-fixed-dim: '#ffb955'
  on-primary-fixed: '#291800'
  on-primary-fixed-variant: '#633f00'
  secondary-fixed: '#dae2fd'
  secondary-fixed-dim: '#bec6e0'
  on-secondary-fixed: '#131b2e'
  on-secondary-fixed-variant: '#3f465c'
  tertiary-fixed: '#7ffc97'
  tertiary-fixed-dim: '#62df7d'
  on-tertiary-fixed: '#002109'
  on-tertiary-fixed-variant: '#005320'
  background: '#faf8ff'
  on-background: '#1a1b21'
  surface-variant: '#e2e2ea'
typography:
  display:
    fontFamily: Space Grotesk
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 44px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Space Grotesk
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
    letterSpacing: -0.015em
  headline-lg-mobile:
    fontFamily: Space Grotesk
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Space Grotesk
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Space Grotesk
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
  label-lg:
    fontFamily: JetBrains Mono
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.02em
  label-md:
    fontFamily: JetBrains Mono
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.01em
  label-sm:
    fontFamily: JetBrains Mono
    fontSize: 11px
    fontWeight: '500'
    lineHeight: 14px
    letterSpacing: 0.02em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1.25rem
  gutter-desktop: 1.5rem
  margin: 1rem
  margin-tablet: 1.5rem
  margin-desktop: 2rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.25rem
  space-xl: 2rem
---

## Brand & Style

This design system embodies Warm Modernist Minimalism engineered specifically for high-velocity enterprise marketplace administration. It reconciles high-density operational data with an inviting, architectural warmth. 

The interface avoids cold, clinical grays in favor of an ivory and deep slate canvas, balanced by high-visibility amber accents. The design style combines:
- **Warm Architectural Minimalism:** Generous spacing paired with structured content framing on a warm ivory substrate.
- **Tonal Hierarchy:** Crisp, pure white operational surfaces set over a subtle, off-white foundation, anchored by authoritative slate structural panels.
- **Utilitarian Precision:** Technical clarity via structured monospaced metadata, clear data density controls, and zero decorative noise.

## Colors

The palette establishes a high-contrast, functional hierarchy tailored for operational workflows and rapid scanning.

### Core Roles
- **Canvas Base (`#FFFDF8` - Warm Ivory):** The global viewport background. Emits warmth and reduces eye fatigue during extended administrative shifts.
- **Primary Accent (`#F5A623` - Amber Action):** Reserved strictly for primary interactive triggers, active tab indicators, and key operational alerts. Always paired with Deep Slate text (`#0F172A`) for WCAG AAA compliance.
- **Structural Neutral (`#0F172A` - Deep Slate):** Primary text color, authoritative surface panels (such as side navigation), and heavy structural dividers.
- **Surface Layer (`#FFFFFF` - Pure White):** All functional cards, tables, popovers, and form fields rest on pure white to lift content naturally from the warm ivory canvas.
- **Text & Borders:**
  - Primary Typography: `#0F172A`
  - Secondary/Muted Typography: `#45464D`
  - Low-Contrast Borders: `rgba(15, 23, 42, 0.10)`
  - Form Fields & Focused Outlines: `#76777D`

### Semantic Feedback
- **Positive / Active:** `#16A34A` (Status green for completed orders, verified merchants, and active catalog items)
- **Warning / Pending:** `#D97706` (Escrow holds, pending settlements, low stock alerts)
- **Critical / Destructive:** `#DC2626` (Failed transactions, merchant suspensions, cancellations)
- **Informational / Tonal:** `#334155` (Audit log events, neutral system tags, inactive filters)

## Typography

The typography system uses three typefaces:
- **Headlines (Space Grotesk):** Provides structured character and modernity for key metrics, page headers, and section groupings. Weights are limited strictly to 700 for major titles and 600 for card headers.
- **Body & Controls (Inter):** Highly legible, neutral workhorse for tabular lists, form labels, tooltips, and general interface reading.
- **Monospace Metadata (JetBrains Mono):** Mandatory for all marketplace IDs (orders, SKUs, seller IDs), timestamps, exact counts, status codes, and financial amounts formatted in Indian Rupee (₹). Ensures fixed vertical and horizontal alignment in dense administrative tables.

## Layout & Spacing

This design system uses a fluid layout dynamic constrained within operational maximums:
- **Admin Shell:** A fixed-width or collapsable authoritative sidebar (`260px` expanded, `72px` collapsed) pinned to the left, rendered in `#0F172A`.
- **Canvas Grid:** The main workspace utilizes a 12-column responsive fluid grid with `1.5rem` (`24px`) gutters on desktop, reflowing to a 4-column layout on mobile devices.
- **Density Rhythms:**
  - Card internal padding is fixed at `1.25rem` (`20px`) across all primary modules.
  - Data tables use compact vertical padding (`0.625rem` / `10px`) per row to maximize line-item visibility above the fold.
  - Dashboard metric widgets sit in symmetrical 3-column or 4-column groupings using the default grid gutters.

## Elevation & Depth

Visual depth is achieved through **tonal stratification and low-contrast perimeter outlines** rather than heavy drop shadows.

- **Stack Hierarchy:**
  1. Base: `#FFFDF8` (Ivory canvas)
  2. Workspace Cards & Panels: `#FFFFFF` resting directly on the canvas, bounded by a `1px` solid `rgba(15, 23, 42, 0.10)` perimeter outline.
  3. Overlay Flyouts & Modal Sheets: Elevated above content using a controlled, warm-tinted ambient shadow: `0 12px 32px -4px rgba(15, 23, 42, 0.08), 0 4px 12px -2px rgba(15, 23, 42, 0.04)`.
  4. Global Drawer / Navigation Shell: Solid `#0F172A` providing zero-blur visual grounding.
- **Interactive State Transitions:** Cards and list rows do not elevate on the Z-axis upon hover. Instead, interactive cards signal hover states via an outline color shift from `rgba(15, 23, 42, 0.10)` to `#F5A623` (Amber) accompanied by an ultra-subtle tint (`rgba(245, 166, 35, 0.02)`).

## Shapes

The geometric architecture is anchored around a **uniform 14px corner radius**, establishing a crisp modernist visual cadence across the platform.

### Form Rules
- **Core Geometry (14px):** All interactive functional containers—including cards, metric tiles, modal dialogs, data table outer containers, text input fields, and standard action buttons—strictly utilize `14px` border radius (`rounded-lg` level equivalent to `0.875rem`).
- **Pill Shape Exception:** Full circular rounding (`9999px`) is reserved **strictly and exclusively** for status badges, tags, and small numerical counter chips. 
- **Buttons must never be rendered as pills.**

## Components

### Buttons
- **Primary Button:** Solid `#F5A623` (Amber Action) fill, bold `#0F172A` typography, `14px` border radius, zero shadow. Minimum height of `40px` (or `48px` for major CTA). Focus state creates a `2px` offset ring in `#F5A623`.
- **Secondary Button:** Transparent background, `1px` border in `rgba(15, 23, 42, 0.15)`, `#0F172A` medium typography, `14px` border radius. Hover introduces a solid white background `#FFFFFF` with border darkened to `#0F172A`.
- **Destructive Button:** Solid `#DC2626` with pure white text, or transparent with a `1px` `#DC2626` outline, strictly maintaining the `14px` border radius.

### Input Fields & Controls
- **Form Inputs:** Pure `#FFFFFF` background, `1px` border using `#76777D` (or `rgba(15, 23, 42, 0.15)` when unselected), `14px` border radius, `12px 16px` padding. Active/focused border snaps directly to `#0F172A` with an ambient `rgba(15, 23, 42, 0.05)` glow.
- **Checkboxes & Radios:** `rgba(15, 23, 42, 0.20)` border on pure white. When checked, checkboxes transition to solid `#0F172A` with a white checkmark icon. Radios take a central `#0F172A` disc.

### Cards & Panels
- **Structure:** Clean `#FFFFFF` fill, bounded by `1px` solid `rgba(15, 23, 42, 0.10)`. Internal padding is locked to `20px`. Radius is `14px`.
- **Interactive Variants:** Hover transitions the border color to `#F5A623` over a 150ms ease curve.

### Status Badges & Chips (Pill Geometric Exception)
- Rendered with a full pill radius (`9999px`), `4px 10px` padding, and typography set in `JetBrains Mono` (`label-sm`, uppercase).
- **Completed/Verified:** Background `rgba(22, 163, 74, 0.12)`, text `#16A34A`.
- **Pending/Review:** Background `rgba(217, 119, 6, 0.12)`, text `#D97706`.
- **Cancelled/Failed:** Background `rgba(220, 38, 38, 0.12)`, text `#DC2626`.
- **Neutral/Draft:** Background `rgba(51, 65, 85, 0.10)`, text `#334155`.

### Data Tables & Financial Lists
- **Container:** Wrapped in a unified `#FFFFFF` container with `14px` outer radius and `1px` perimeter border. Header rows use `#FFFDF8` warm ivory tint with `Inter` uppercase column labels.
- **Financial & Monospace Data:** All SKU IDs, internal references, order IDs, timestamps, and currency amounts are displayed using `JetBrains Mono` with the Indian Rupee symbol explicitly formatted (`₹ 1,48,200.00`).