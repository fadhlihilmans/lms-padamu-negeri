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

Border-radius rules (STRICT — must match across all components):
- **Cards & Modals:** `rounded-xl` (12px) — the definitive radius for all content containers and dialog panels.
- **Buttons, Inputs, Select, Badges:** `rounded-lg` (8px).
- **Avatars, Dot indicators, Pill tags:** `rounded-full`.
- **Bottom-sheet modal top corners (mobile):** `rounded-t-2xl` (16px) — only for the mobile bottom-sheet variant.
- Never use `rounded-2xl` or `rounded-3xl` on standard desktop components.

## Elevation & Depth
This design system uses **Ambient Shadows** to create a sense of organized layering. Depth is communicative, not just decorative.

- **Level 0 (Background):** `#F1F5F9` - The canvas.
- **Level 1 (Cards/Sidebar):** White surface with `shadow-sm`. Default state for all content containers.
- **Level 2 (Dropdowns/Modals):** White surface with `shadow-xl shadow-black/10`.
- **Outlines:** All cards use `border border-[#c5c5d7]` (NOT `#E2E8F0`) — use this consistently.
- **Modal Backdrop:** Always `bg-black/50 backdrop-blur-sm`. This is non-negotiable.

## Components
- **Buttons:** Primary `bg-[#3c50e0]` hover `bg-[#2e3eb0]`, white text. Always `py-2.5` for 44px touch target. Always `cursor-pointer`. Secondary: white fill + `border-[#c5c5d7]`. Danger: `border-[#ba1a1a] text-[#ba1a1a]` hover `bg-[#ffdad6]`.
- **Inputs:** White bg, `border-[#c5c5d7]`, `rounded-lg`. Focus: `focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0]`. Always `py-2.5` for touch target. Labels above the field.
- **Cards:** Always `bg-white rounded-xl border border-[#c5c5d7] shadow-sm`. Header sections separated by `border-b border-[#c5c5d7]`.
- **Modals:** Bottom-sheet on mobile (`items-end rounded-t-2xl`), centered on desktop (`sm:items-center sm:rounded-xl`). Always `backdrop-blur-sm`. Body must be `overflow-y-auto max-h-[90dvh]`. Footer buttons `flex-col-reverse sm:flex-row`.
- **Sidebar Items:** Icons sized at 20px. Active: left 3px accent bar + `bg-primary/8`. Inactive: `text-secondary hover:bg-surface-container-low`.
- **Data Tables:** Wrap in `overflow-x-auto`. No vertical borders. Header: `text-[11px] font-semibold text-[#505f76] uppercase tracking-wider`. Secondary columns: `hidden sm:table-cell`. Row hover: `hover:bg-[#f6fafe]`. Row divider: `divide-y divide-[#f0f4f8]`.

## Mobile-First Rules (CRITICAL)
- All layouts start at 1 column, expand with `sm:`, `md:`, `lg:` breakpoints.
- Sidebar collapses to hamburger at `<lg`. Implementation already exists in `app.blade.php` — never duplicate.
- Page action buttons: `w-full sm:w-auto` so they're full-width on mobile.
- Button groups in footers: `flex-col-reverse sm:flex-row` — cancel button below primary on mobile.
- Form grids: always `grid-cols-1 sm:grid-cols-2`.
- Modal footer: buttons must be `w-full sm:w-auto` and stacked on mobile.
- Page heading: responsive sizes `text-[20px] sm:text-[24px]`.
- Stat card grids: `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4`.
- All pages must have an empty state (`forelse` + empty message with icon) — never an empty list.

> For complete code patterns, see `docs/design-guide.md` Bagian E & F.