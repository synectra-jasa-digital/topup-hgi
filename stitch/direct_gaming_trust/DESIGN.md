---
name: Direct Gaming Trust
colors:
  surface: '#f8f9ff'
  surface-dim: '#d0dbed'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e6eeff'
  surface-container-high: '#dee9fc'
  surface-container-highest: '#d9e3f6'
  on-surface: '#121c2a'
  on-surface-variant: '#434655'
  inverse-surface: '#27313f'
  inverse-on-surface: '#eaf1ff'
  outline: '#737686'
  outline-variant: '#c3c6d7'
  surface-tint: '#0053db'
  primary: '#004ac6'
  on-primary: '#ffffff'
  primary-container: '#2563eb'
  on-primary-container: '#eeefff'
  inverse-primary: '#b4c5ff'
  secondary: '#4059aa'
  on-secondary: '#ffffff'
  secondary-container: '#8fa7fe'
  on-secondary-container: '#1d3989'
  tertiary: '#485767'
  on-tertiary: '#ffffff'
  tertiary-container: '#606f80'
  on-tertiary-container: '#e9f2ff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dbe1ff'
  primary-fixed-dim: '#b4c5ff'
  on-primary-fixed: '#00174b'
  on-primary-fixed-variant: '#003ea8'
  secondary-fixed: '#dce1ff'
  secondary-fixed-dim: '#b6c4ff'
  on-secondary-fixed: '#00164e'
  on-secondary-fixed-variant: '#264191'
  tertiary-fixed: '#d5e4f8'
  tertiary-fixed-dim: '#b9c8db'
  on-tertiary-fixed: '#0e1d2b'
  on-tertiary-fixed-variant: '#3a4858'
  background: '#f8f9ff'
  on-background: '#121c2a'
  surface-variant: '#d9e3f6'
  success: '#16A34A'
  warning: '#F59E0B'
  danger: '#DC2626'
  neutral-50: '#F9FAFB'
  neutral-200: '#E5E7EB'
  neutral-500: '#6B7280'
  neutral-900: '#1F2937'
  surface-white: '#FFFFFF'
typography:
  headline-xl:
    fontFamily: Plus Jakarta Sans
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
  headline-xl-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  headline-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-lg-medium:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '500'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-sm-medium:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
  caption:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  space-1: 4px
  space-2: 8px
  space-3: 12px
  space-4: 16px
  space-6: 24px
  space-8: 32px
  space-12: 48px
  space-16: 64px
---

## Brand & Style

The design system establishes an ultra-frictionless, reliable, and authentic gaming transaction environment. Designed specifically for instant game top-ups—where users transacting without an account demand utmost reassurance—the brand voice prioritizes clarity, speed, transparency, and bulletproof security. 

Drawing from a blend of **Corporate Modernism** and **Tactile Simplicity**, the aesthetic avoids gamer-oriented clutter, dark neon distractions, and intrusive visual noise. Instead, it deploys a clean, high-clarity canvas where transactional status, verified payment rails, and denomination cards speak directly to user confidence. The visual tone is open, responsive, and systematic, transforming transactional speed into an experience that feels as safe and established as institutional banking.

## Colors

The color palette is functionally disciplined. Chromatic brand accents direct intent, while high-contrast status feedback communicates transaction progress.

- **Primary (`#2563EB`)**: Drives primary calls-to-action, selected interactive states, and essential progression paths.
- **Secondary (`#1E3A8A`)**: Provides visual anchor points across topbars, footers, headers, and active hover elevation.
- **Tertiary / Tint (`#DBEAFE`)**: Applied as the active surface background for selected denomination items and payment rail cards.
- **Neutral Palette (`#1F2937` to `#F9FAFB`)**: `#1F2937` commands primary typography and high-priority data points. `#6B7280` handles secondary utility details, while `#E5E7EB` constructs structured separation borders. `#F9FAFB` grounds page backdrops, and pure white (`#FFFFFF`) isolates cards and inputs.
- **Semantic Feedback**: `#16A34A` (Success), `#F59E0B` (Pending/Processing), and `#DC2626` (Failed/Danger) are exclusively reserved for transaction feedback, order verification badges, and critical inline alerts.

## Typography

The type system blends the energetic geometry of **Plus Jakarta Sans** for structural headers with the neutral clarity of **Inter** for dense transactional interfaces, pricing metrics, and forms.

- **Headings**: `headline-xl` is strictly capped at one occurrence per view (Page Title). `headline-lg` structures major content groups (Item Nominal, Payment Methods, Order Check).
- **Body & Numerical Values**: Denomination rates, balance calculations, and user identification input rely on `Inter` with tabular figures where necessary, ensuring accurate scanning across varying device widths.
- **Labels & Forms**: Form fields require explicit labels mapped to `label-md` or `body-sm-medium`. Placeholders must never replace functional labels.

## Layout & Spacing

All structural measurements strictly follow a 4-pixel base rhythm. 

- **Layout Structure**: 
  - **Desktop (>1024px)**: Fixed maximum container of `1200px` with centered auto-margins, using a 4 to 5 column grid for product tiers and 24px gutters (`space-6`). Section gaps expand to 48px (`space-12`).
  - **Tablet (640px - 1024px)**: 3-column product matrix, 16px page margins, and 32px (`space-8`) section breaks.
  - **Mobile (<640px)**: Compact 2-column product matrix with a guaranteed base viewport test threshold at 375px. Outer page padding remains 16px (`space-4`), and vertical section spacing reduces to 32px (`space-8`).
- **Interactive Thresholds**: Touch targets on mobile surfaces must provide at least 44px of hit area. Bottom screen padding accommodates the mobile sticky order bar to prevent overlap.

## Elevation & Depth

The design system uses low-contrast borders and functional elevation to maintain a clean, lightweight layout.

- **Flat Foundation**: Product cards, denomination tiles, and tabular panels reside directly on `#FFFFFF` surfaces defined by a crisp 1px border (`#E5E7EB`), avoiding heavy drop shadows.
- **Active State Elevation**: Selected items do not use heavy shadows; they pivot to `#DBEAFE` surfaces outlined with a 1.5px `#2563EB` border.
- **Floating Overlays**:
  - *Dropdowns / Popovers*: `0 4px 12px rgba(31, 41, 55, 0.08)`
  - *Sticky Bottom Mobile Bar*: `0 -4px 16px rgba(31, 41, 55, 0.06)` with a top border in `#E5E7EB`
  - *Modals & Confirmation Dialogs*: `0 12px 32px rgba(30, 58, 138, 0.12)`, backed by an overlay of `rgba(31, 41, 55, 0.48)`

## Shapes

The geometric framework is consistently calibrated across controls and containers:

- **Tokens**:
  - `radius-sm` (6px): Contextual badges, micro-indicators, small input controls.
  - `radius-md` (10px): Primary/secondary action buttons, input fields, payment method tiles.
  - `radius-lg` (16px): Surface containers, denomination product cards, alert banners, and transactional modals.
  - `radius-full` (9999px): Transaction state pills, numerical progress steps, user account avatars.

## Components

### Buttons
- **Primary**: Solid `#2563EB` background with `#FFFFFF` text. Hover shifts to `#1E3A8A`. Height scales from 40px (Small), 44px (Medium/Default), to 48px (Large). Uses `radius-md` (10px).
- **Secondary / Outline**: Transparent surface, 1.5px border `#2563EB`, text `#2563EB`. Hover: `#DBEAFE` background.
- **Disabled**: Background `#E5E7EB`, text `#6B7280`, pointer-events disabled.

### Product & Denomination Cards
- Surface `#FFFFFF` with 1px `#E5E7EB` border, padded with `space-4` (16px), corner radius `radius-lg` (16px).
- Displays denomination tier, product icon/artwork, formatted currency, and nominal points.
- **Selected State**: Border shifts to 2px `#2563EB`, background shifts to `#DBEAFE`.

### Form Inputs & Player ID Verifier
- Stacked configuration: static `label-md` on top, input field with 12px/16px padding, and helper/error copy beneath.
- Default border `#E5E7EB`; focus ring 2px `#2563EB` with `#FFFFFF` background.
- Error state: 1.5px `#DC2626` border with immediate descriptive validation text below.

### Payment Method Selector
- Multi-row grid containing official payment channel logos (QRIS, e-Wallets, Virtual Accounts).
- Enclosed in `radius-md` (10px) card with clear fee and verification badges. Selected state mimics the product card active border.

### Status Badges
- Pill-shaped (`radius-full`), padded 4px vertically and 12px horizontally, font `label-sm`.
- **Pending/Processing**: `#F59E0B` background with white text or soft yellow tint with saturated `#B45309` text.
- **Success / Completed**: `#16A34A` background with white text.
- **Failed / Cancelled**: `#DC2626` background with white text.

### Mobile Sticky Bar
- Fixed bottom container spanning 100% viewport width with top border `#E5E7EB`, elevated via soft drop shadow.
- Displays selected aggregate item price on the left and full-height primary action button on the right.

### Tables & Data Containers (Admin / Order Lookup)
- Clean tabular rows with 16px cell padding. Alternate zebra-striping using `#F9FAFB` for even rows.
- Clear structural division using 1px horizontal borders in `#E5E7EB`.