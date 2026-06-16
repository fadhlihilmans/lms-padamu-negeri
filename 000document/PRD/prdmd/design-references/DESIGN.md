---
name: Modern Administrative Interface
colors:
  surface: '#f6fafe'
  surface-dim: '#d6dade'
  surface-bright: '#f6fafe'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f0f4f8'
  surface-container: '#eaeef2'
  surface-container-high: '#e4e9ed'
  surface-container-highest: '#dfe3e7'
  on-surface: '#171c1f'
  on-surface-variant: '#454655'
  inverse-surface: '#2c3134'
  inverse-on-surface: '#edf1f5'
  outline: '#757686'
  outline-variant: '#c5c5d7'
  surface-tint: '#384cdd'
  primary: '#1c33c8'
  on-primary: '#ffffff'
  primary-container: '#3c50e0'
  on-primary-container: '#d9dbff'
  inverse-primary: '#bcc2ff'
  secondary: '#505f76'
  on-secondary: '#ffffff'
  secondary-container: '#d0e1fb'
  on-secondary-container: '#54647a'
  tertiary: '#424a5c'
  on-tertiary: '#ffffff'
  tertiary-container: '#5a6274'
  on-tertiary-container: '#d6def4'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dfe0ff'
  primary-fixed-dim: '#bcc2ff'
  on-primary-fixed: '#000c61'
  on-primary-fixed-variant: '#172fc5'
  secondary-fixed: '#d3e4fe'
  secondary-fixed-dim: '#b7c8e1'
  on-secondary-fixed: '#0b1c30'
  on-secondary-fixed-variant: '#38485d'
  tertiary-fixed: '#dbe2f8'
  tertiary-fixed-dim: '#bec6dc'
  on-tertiary-fixed: '#131c2b'
  on-tertiary-fixed-variant: '#3f4758'
  background: '#f6fafe'
  on-background: '#171c1f'
  surface-variant: '#dfe3e7'
typography:
  headline-xl:
    fontFamily: Inter
    fontSize: 30px
    fontWeight: '700'
    lineHeight: 38px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
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
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 20px
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
  headline-lg-mobile:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '700'
    lineHeight: 28px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  container-margin: 24px
  gutter: 24px
  sidebar-width: 280px
  card-padding: 1.5rem
  stack-sm: 0.5rem
  stack-md: 1rem
  stack-lg: 1.5rem
---

## Brand & Style
The design system focuses on clarity, efficiency, and professional reliability. It is tailored for SaaS administrators who require a high-density, low-friction interface for managing complex data. 

The aesthetic is **Corporate Modern**, prioritizing a systematic approach to layout and information hierarchy. It leverages a "Clean White" philosophy where the interface recedes into the background to let user data take center stage. The emotional response should be one of organized control and modern sophistication, achieved through ample whitespace, soft tonal transitions, and a disciplined primary accent.

## Colors
The palette is rooted in a professional "Enterprise Blue" used for primary actions, active states, and brand presence. 

- **Primary (#3C50E0):** Used for CTA buttons, active navigation items, and progress indicators.
- **Surface & Background:** The main application background uses a soft gray (`#F1F5F9`) to provide contrast against the pure white (`#FFFFFF`) cards.
- **Text & Neutral:** Deep slate-grays are used for typography to ensure readability without the harshness of pure black. Secondary text utilizes `#64748B`.
- **Borders:** A consistent, subtle stroke of `#E2E8F0` defines the boundaries of components without adding visual noise.

## Typography
The system uses **Inter** for its exceptional legibility and neutral, systematic character. The scale is designed for high-density dashboards where vertical space is at a premium.

**Linguistik (Indonesian Labels):**
Semua label navigasi dan status harus menggunakan Bahasa Indonesia.
- *Dashboard* -> Beranda
- *Settings* -> Pengaturan
- *Users* -> Pengguna
- *Reports* -> Laporan
- *Search* -> Cari...

Headlines use semi-bold and bold weights with slight negative letter-spacing to appear tighter and more professional. Body text remains at 14px for optimal density in data tables.

## Layout & Spacing
The design system utilizes a **Fixed Sidebar + Fluid Content** layout model. 

- **Sidebar:** Fixed at 280px. It uses a clean white background with `#64748B` text for a light, airy feel, or a deep navy `#1C2434` for a high-contrast alternative.
- **Main Content:** Sits on the `#F1F5F9` background with a standard 24px (1.5rem) margin on all sides.
- **Grid:** A standard 12-column system is used for dashboard widgets. Widgets should typically span 3, 4, 6, or 12 columns.
- **Mobile Adaptivity:** At the 768px breakpoint, the sidebar collapses into a hamburger menu, and card margins reduce to 16px.

## Elevation & Depth
This design system uses **Ambient Shadows** to create a sense of organized layering. Depth is communicative, not just decorative.

- **Level 0 (Background):** `#F1F5F9` - The canvas.
- **Level 1 (Cards/Sidebar):** White surface with `shadow-sm` (0 1px 2px 0 rgba(0, 0, 0, 0.05)). This is the default state for content containers.
- **Level 2 (Dropdowns/Modals):** White surface with `shadow-md` (0 4px 6px -1px rgba(0, 0, 0, 0.1)).
- **Outlines:** All cards and containers feature a subtle 1px border using `#E2E8F0` to maintain definition against the soft gray background.

## Shapes
The shape language is consistently **Rounded**. 

A standard border-radius of **8px (0.5rem)** is applied to all primary containers, cards, and input fields. This creates a friendly yet structured appearance. Small components like tags (chips) or badges may use a "pill" radius to differentiate them from actionable buttons, but the core structural elements remain strictly at the 8px/16px/24px scaling tier.

## Components
- **Buttons:** Primary buttons use `#3C50E0` with white text. Hover states should darken the blue slightly. Secondary buttons use a white fill with the `#E2E8F0` border.
- **Inputs:** Fields use a white background, `#E2E8F0` border, and 8px corners. The focus state is a 1px solid `#3C50E0` with a soft blue outer glow. Labels are placed above the field in `label-md` weight using Indonesian (e.g., "Nama Lengkap").
- **Cards:** The foundational unit. Always white, 8px radius, 1px border (`#E2E8F0`), and a soft shadow. Header sections within cards should be separated by a subtle horizontal rule.
- **Sidebar Items:** Icons should be sized at 20px. Active states feature a left-hand 3px accent bar in Primary Blue and a light blue tinted background (5% opacity).
- **Data Tables:** Clean rows with no vertical borders. Header row uses `label-sm` (uppercase) with a light gray background or a thicker bottom border.