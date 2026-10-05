---
name: Warm Modernist Commerce
colors:
  surface: '#fbf9f4'
  surface-dim: '#dbdad5'
  surface-bright: '#fbf9f4'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f5f3ee'
  surface-container: '#efeee9'
  surface-container-high: '#eae8e3'
  surface-container-highest: '#e4e2de'
  on-surface: '#1b1c19'
  on-surface-variant: '#45464d'
  inverse-surface: '#30312e'
  inverse-on-surface: '#f2f1ec'
  outline: '#76777d'
  outline-variant: '#c6c6cd'
  surface-tint: '#565e74'
  primary: '#000000'
  on-primary: '#ffffff'
  primary-container: '#131b2e'
  on-primary-container: '#7c839b'
  inverse-primary: '#bec6e0'
  secondary: '#835500'
  on-secondary: '#ffffff'
  secondary-container: '#feae2c'
  on-secondary-container: '#6b4500'
  tertiary: '#000000'
  on-tertiary: '#ffffff'
  tertiary-container: '#002109'
  on-tertiary-container: '#009842'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dae2fd'
  primary-fixed-dim: '#bec6e0'
  on-primary-fixed: '#131b2e'
  on-primary-fixed-variant: '#3f465c'
  secondary-fixed: '#ffddb4'
  secondary-fixed-dim: '#ffb955'
  on-secondary-fixed: '#291800'
  on-secondary-fixed-variant: '#633f00'
  tertiary-fixed: '#7ffc97'
  tertiary-fixed-dim: '#62df7d'
  on-tertiary-fixed: '#002109'
  on-tertiary-fixed-variant: '#005320'
  background: '#fbf9f4'
  on-background: '#1b1c19'
  surface-variant: '#e4e2de'
typography:
  display-hero:
    fontFamily: Space Grotesk
    fontSize: 48px
    fontWeight: '600'
    lineHeight: 56px
    letterSpacing: -0.02em
  display-hero-mobile:
    fontFamily: Space Grotesk
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Space Grotesk
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.015em
  headline-lg-mobile:
    fontFamily: Space Grotesk
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Space Grotesk
    fontSize: 24px
    fontWeight: '500'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Space Grotesk
    fontSize: 18px
    fontWeight: '500'
    lineHeight: 26px
    letterSpacing: 0em
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
    letterSpacing: -0.005em
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
    letterSpacing: 0em
  body-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 18px
    letterSpacing: 0.005em
  system-code:
    fontFamily: JetBrains Mono
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 18px
    letterSpacing: 0em
  system-label:
    fontFamily: JetBrains Mono
    fontSize: 11px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.06em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1.5rem
  gutter-mobile: 1rem
  margin: 3rem
  margin-mobile: 1.25rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2.5rem
---

## Brand & Style
This design system defines a merchant onboarding and seller management platform rooted in Warm Modernist Minimalism. The visual tone is clear, architectural, and hospitable—balancing the pragmatic efficiency of commercial tooling with the tactile luxury of an editorial studio. 

The aesthetic is constructed around an organic linen-white base, grounded by deep structural slate typography, focused amber action cues, and crisp, micro-bordered containment. Layouts eliminate gratuitous decoration, relying instead on deliberate proportional space, calibrated typographic hierarchy, and consistent structural framing. Interactions feel decisive, highly legible, and empowering for commercial sellers entering enterprise marketplace workflows.

## Colors
The palette pairs functional utility with a warm, non-sterile atmosphere:

- **Canvas (`#FFFDF8`):** The primary warm off-white foundation for application views, workspace panels, and primary page backdrops.
- **Surface Pure (`#FFFFFF`):** High-clarity elevated cards, input fills, and active working areas.
- **Deep Slate (`#0F172A`):** The commanding primary tone for key navigational elements, authoritative actions, headers, and strong borders.
- **Amber Action (`#F5A623`):** Reserved strictly for primary flow advancement, step-completion anchors, status highlights, and focused attention states.
- **Success Green (`#16A34A`):** Verification stamps, merchant KYC approvals, completed flow milestones, and positive delta values.
- **Muted Text (`#45464D`):** Balanced secondary body copy, helper labels, input field guidance, and secondary table contents.
- **Structural Outline (`#76777D`):** Deliberate perimeter borders, divider strokes, and inactive field boundaries, applied at balanced opacities (typically 20%–40% on light surfaces).

## Typography
Typographic rhythm is established using three distinct typefaces serving specialized architectural roles:

1. **Space Grotesk (Headings & Display):** Imparts structural modernity and mechanical precision to page headers, onboarding phase titles, and section dividers.
2. **Inter (Body & Interface):** Handles all high-density narrative text, descriptions, field inputs, form prompts, and transactional UI copy with neutral legibility.
3. **JetBrains Mono (Data, Badges & Labels):** Deployed for store IDs, tax identification formats, currency representations, SKU counters, and micro-tier categorical labels. It lends an authentic operational texture to seller infrastructure.

## Layout & Spacing
The layout follows an asymmetric split or centered container grid anchored to a standard 12-column system on desktop, collapsing to 4 columns on mobile devices:

- **Desktop (>= 1024px):** Primary seller onboarding flows utilize a two-panel split (40% fixed informational guide / 60% fluid interactive card), bounded within a maximum content constraint of `1280px`. Grid gutters are locked at `1.5rem` (`24px`), with canvas margins set to `3rem` (`48px`).
- **Tablet (768px - 1023px):** Collapses into a single-column stacked hierarchy. Outer canvas margins reduce to `2rem` (`32px`).
- **Mobile (< 768px):** Stepper headers stick to the top viewport with compact pagination, gutters compress to `1rem` (`16px`), and margins reduce to `1.25rem` (`20px`).
- **Vertical Rhythm:** Spacing between atomic elements (e.g., input field to descriptive label) uses `space-xs` (4px) to `space-sm` (8px). Structural separation between logical form sections strictly adheres to `space-xl` (40px).

## Elevation & Depth
Depth in this system avoids heavy, atmospheric skeuomorphism in favor of tonal layering punctuated by precise structural borders:

- **Level 0 (Canvas):** Tone `#FFFDF8`. Non-interactive flat background.
- **Level 1 (Card & Content Blocks):** Pure white `#FFFFFF` fills framed with a single 1px solid border (`#76777D` at 25% opacity: `rgba(118, 119, 125, 0.25)`). A subtle ambient lift is achieved using an offset shadow: `0 2px 8px rgba(15, 23, 42, 0.04)`.
- **Level 2 (Active Modals & Step Drawers):** `#FFFFFF` surfaces paired with a structured 1px outline (`#76777D` at 35% opacity) and an ambient, low-spread drop shadow: `0 12px 32px rgba(15, 23, 42, 0.08)`.
- **Level 3 (Popovers & Context Dropdowns):** `#FFFFFF` fill, 1px border (`#0F172A` at 15% opacity), and elevation shadow: `0 8px 24px rgba(15, 23, 42, 0.06)`.

## Shapes
The structural geometry is unified by a strict universal corner radius. Cards, primary and secondary action buttons, text input fields, selection tiles, and modal dialogs all employ an exact `14px` border radius (`0.875rem`). 

Pill-shaped containers (`border-radius: 9999px`) are strictly prohibited across all interaction elements and state tags. Small badges, chips, and micro status indicators instead adopt a reduced sub-radius of `6px` or retain a proportional `8px` corner to preserve the balanced geometric integrity of the workspace.

## Components

### Buttons
- **Shape & Dimension:** Fixed `14px` radius. Height defaults to `48px` for primary interactive elements, with `38px` for secondary contextual triggers. Strict prohibition against pill contours.
- **Primary Action:** Solid Deep Slate (`#0F172A`) or Amber Action (`#F5A623`) with contrasting typography (`#FFFFFF` on Slate; `#0F172A` on Amber). Focus states use an outline offset of `2px` in `#0F172A`.
- **Secondary Action:** Pure white background (`#FFFFFF`) with a 1px border (`rgba(118, 119, 125, 0.3)`), hover transition to canvas warm tone (`#FFFDF8`) and border darkening to `#0F172A`.

### Input Fields & Selectors
- **Geometry:** Height of `48px`, `14px` corner radius, `#FFFFFF` background fill.
- **Border States:** Resting border is 1px solid `rgba(118, 119, 125, 0.35)`. Focused state thickens to 1.5px solid `#0F172A` with an ambient ring: `0 0 0 3px rgba(15, 23, 42, 0.06)`.
- **Typography:** Labels use `Inter` 13px medium (`#45464D`). Values are rendered in `Inter` 14px regular (`#0F172A`). Masked data, account numbers, and currency inputs use `JetBrains Mono`.

### Step Progress & Milestone Trackers
- **Hierarchy:** Monospaced phase tags (`STEP 01/04` in `JetBrains Mono`) sitting above a `Space Grotesk` header.
- **Indicators:** Segmented linear bars with 2px gaps, featuring a solid 4px height and subtle `2px` corners. Completed steps transition to Success Green (`#16A34A`), current active steps render in Amber Action (`#F5A623`), and remaining steps rest in `rgba(118, 119, 125, 0.2)`.

### Cards & Grouping Surfaces
- **Specification:** Pure white (`#FFFFFF`) interior, bounded by a 1px outline in `rgba(118, 119, 125, 0.25)`, wrapped in a `14px` radius. Internal padding is mapped directly to `space-lg` (`24px`).
- **Interactive Option Tiles (e.g., Business Type Selectors):** Resting state matches standard cards. Selected state switches to a 1.5px `#0F172A` border with an internal active badge rendered in `#0F172A` or `#F5A623`.

### Badges & Status Chips
- **Specification:** Corner radius locked to `6px`. Typographic scale set to `system-label` (11px uppercase in `JetBrains Mono`). 
- **Variants:** 
  - Verified / Active: Light green fill (`#DCFCE7`) with `#16A34A` label.
  - Pending Review: Light amber fill (`#FEF3C7`) with `#B45309` label.
  - Draft / Inactive: Off-slate fill (`#F1F5F9`) with `#45464D` label.

### Checkboxes & Radio Selectors
- **Checkboxes:** `18px × 18px` square with a `4px` corner radius. Inactive border is 1.5px `rgba(118, 119, 125, 0.4)`. Checked state is solid `#0F172A` with a `#FFFFFF` checkmark icon.
- **Radio Buttons:** `18px × 18px` circular control, checked state displays an inner solid pip of `8px` in `#0F172A`.