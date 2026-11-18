---
name: Meta Sarpras Mobile
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#44474e'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#74777f'
  outline-variant: '#c4c6cf'
  surface-tint: '#465f88'
  primary: '#002046'
  on-primary: '#ffffff'
  primary-container: '#1b365d'
  on-primary-container: '#87a0cd'
  inverse-primary: '#aec7f7'
  secondary: '#964900'
  on-secondary: '#ffffff'
  secondary-container: '#ff8928'
  on-secondary-container: '#642f00'
  tertiary: '#002618'
  on-tertiary: '#ffffff'
  tertiary-container: '#003e29'
  on-tertiary-container: '#39b282'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d6e3ff'
  primary-fixed-dim: '#aec7f7'
  on-primary-fixed: '#001b3d'
  on-primary-fixed-variant: '#2e476f'
  secondary-fixed: '#ffdcc6'
  secondary-fixed-dim: '#ffb786'
  on-secondary-fixed: '#311300'
  on-secondary-fixed-variant: '#723600'
  tertiary-fixed: '#85f8c4'
  tertiary-fixed-dim: '#68dba9'
  on-tertiary-fixed: '#002114'
  on-tertiary-fixed-variant: '#005137'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
typography:
  headline-xl:
    fontFamily: Plus Jakarta Sans
    fontSize: 28px
    fontWeight: '800'
    lineHeight: 36px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 22px
    fontWeight: '700'
    lineHeight: 30px
    letterSpacing: -0.015em
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '700'
    lineHeight: 26px
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 15px
    fontWeight: '400'
    lineHeight: 22px
  body-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 18px
  label-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
  label-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
  label-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 10px
    fontWeight: '700'
    lineHeight: 14px
    letterSpacing: 0.03em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1rem
  gutter-mobile: 0.75rem
  margin: 1.25rem
  margin-mobile: 1rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 0.875rem
  space-lg: 1.25rem
  space-xl: 1.75rem
---

## Brand & Style

This design system serves the vocational, engineering, and polytechnic ecosystem of Politeknik Meta Industri Cikarang. The application functions as a high-utility Progressive Web App (PWA) and mobile interface dedicated to campus infrastructure management: reporting damaged facilities and managing laboratory/practicum equipment loans.

### Personality & Emotional Response
- **Industrious & Trustworthy:** Anchored by deep institutional navy blue and the iconic collegiate rhino shield emblem, evoking institutional discipline, industrial competence, and operational rigor.
- **Responsive & Action-Oriented:** Energized by vibrant industrial amber-orange accents that signal immediate action, work-in-progress ticketing, and operational agility.
- **Transparent & Calming:** Clean white cards and slate-tinted canvas mitigate frustration during facility breakdowns and loan late-fee penalties through crystal-clear status badging.

### Design Style Movement
- **Modern Industrial Utility:** A marriage of clean contemporary SaaS clarity and touch-first ergonomics.
- **Card-Centric Elevation:** Elevated pure-white cards on subtle slate backgrounds (`#F8FAFC`), rounded corners (`rounded-2xl` / 1rem radius), structured metric counters, and high-visibility bottom navigation optimized for single-thumb field reporting and equipment scanning.

## Colors

The color palette reflects the institutional identity of Politeknik Meta Industri Cikarang, pairing deep academic navy with vibrant industrial amber.

### Primary Role (Meta Navy)
- **#1B365D (Meta Deep Navy):** The core institutional shade used for top app headers, primary welcome cards, hero gradient bases, active tab accents, and strong structural boundaries.
- **#0F2A4A (Midnight Navy):** Deepened contrast for high-emphasis headings, user NIM meta information, and bottom navigation safe areas.

### Secondary Role (Meta Industrial Amber / Orange)
- **#F58220 / #EA580C (Meta Amber Orange):** Action triggers, primary submit buttons, "Buat Pengaduan" and "Ajukan Peminjaman" floating actions, indicator pills for "Diproses" status, and active navigation badges.

### Supporting Semantics
- **Emerald Green (`#059669` / bg: `#ECFDF5`):** Status indicator for resolved reports ("Selesai") and successfully returned equipment ("Dikembalikan").
- **Rose Red (`#E11D48` / bg: `#FFF1F2`):** Overdue indicators ("Terlambat"), disciplinary warnings, and monetary fines/denda tags (`Rp 30.000`).
- **Sky Blue (`#0284C7` / bg: `#F0F9FF`):** Officer response notifications, verified tickets, and equipment quantity chips.

### Surface & Background Tokens
- **Canvas Base:** `#F8FAFC` (Slate 50) and `#F1F5F9` (Slate 100) for contrast against mobile card groups.
- **Card Surface:** `#FFFFFF` (Pure White).
- **Dividers & Strokes:** `#E2E8F0` (Slate 200) for crisp, low-contrast ghost borders.

## Typography

The design system exclusively adopts **Plus Jakarta Sans** across all roles. The geometric clarity and open counters maintain legible hierarchy on high-density mobile screens, laboratory benches, and sunlight-exposed campus environments.

### Hierarchy Guidelines
- **Header & Metric Scales (`headline-xl`, `headline-lg`):** Reserved for student greeting banners ("Selamat Datang, Ahmad Rizky"), aggregate fine counts, and facility dashboard summary numbers.
- **Section Headers (`headline-md`, `headline-sm`):** Categorizes modules such as "Pengaduan Fasilitas Terakhir" and "Peminjaman Alat Aktif".
- **Body Text (`body-md`, `body-sm`):** Handles ticket descriptions, officer notes ("Tanggapan Petugas"), and equipment specifications.
- **Labels & Micro-Tags (`label-md`, `label-sm`):** Applied to uppercase status badges, quantity labels (`Qty: 4 unit`), and tabular timestamps.

## Layout & Spacing

The layout is optimized for a Mobile-First PWA interface while adapting gracefully to larger tablet views used by lab technicians.

### Layout Rhythm & Fluidity
- **Screen Margin:** 16px (`1rem`) on standard mobile displays, expanding to 20px (`1.25rem`) on tablets.
- **Vertical Rhythm:** 14px to 16px gaps between cards to maintain separation without requiring excessive scrolling.
- **Bottom Navigation Offset:** A dedicated clearance of `5rem` (`80px`) padding-bottom on the main scrolling viewport ensures content is never obscured by the persistent three-tab bottom bar.
- **Metric Grid:** 2x2 grid on mobile viewports for quick statistics:
  1. Total Pengaduan
  2. Pengaduan Diproses
  3. Peminjaman Aktif
  4. Catatan Denda

## Elevation & Depth

This system avoids heavy drop shadows, instead utilizing crisp tonal contrast, soft boundary borders, and subtle diffuse elevation for high-touch utility.

### Surface Hierarchy
1. **Level 0 (Canvas):** Ground layer in `#F8FAFC`.
2. **Level 1 (Content Cards & Tables):** Pure white `#FFFFFF` surface accompanied by a subtle border (`1px solid #E2E8F0`) and an ambient diffuse shadow (`box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.05), 0 1px 2px -1px rgba(15, 23, 42, 0.05)`).
3. **Level 2 (Active Metric & Status Cards):** White card accented with a 4px left-border or top-border strip indicating metric status (Orange for Pending, Blue for Active, Red for Denda).
4. **Level 3 (Fixed Chrome & Bottom Bar):** Semi-translucent frosted glass (`rgba(255, 255, 255, 0.92)` with `backdrop-filter: blur(12px)`) elevated by `0 -4px 16px rgba(15, 23, 42, 0.06)` along the bottom edge.
5. **Level 4 (Hero Welcome Banner):** Solid deep Meta Navy gradient (`linear-gradient(135deg, #1B365D 0%, #0F2A4A 100%)`) with an embossed gold rim incorporating the official campus shield rhino emblem watermark.

## Shapes

The design system uses a friendly, modern rounded style with strong industrial curves (`roundedness: 2`).

### Radius Tokens & Implementation
- **Cards & Hero Banners:** `rounded-2xl` (`1rem` / 16px) creates soft, modern containers for reports and loan details.
- **Buttons & Form Inputs:** `rounded-xl` (`0.75rem` / 12px) providing ample, finger-friendly tap targets of minimum 44px height.
- **Pills & Status Badges:** Fully rounded (`rounded-full` / 9999px) for chips, quantity tags, and condition indicators.
- **Image Thumbnails:** `rounded-lg` (`0.5rem` / 8px) with subtle borders for uploaded facility damage photos and equipment thumbnails.

## Components

### 1. Mobile Bottom Navigation Bar
- **Structure:** Fixed at the bottom of the viewport with safe-area inset support. Contains 3 primary functional tabs and an auxiliary student profile trigger:
  - **Beranda:** Home icon with direct overview of active tickets and quick counters.
  - **Pengaduan Fasilitas:** Megaphone icon linking to reports and status tracking.
  - **Peminjaman Alat:** Package/Box icon linking to the lab inventory catalog and borrowed equipment.
  - **Profil:** Avatar icon accessing student profile, NIM, study program ("Teknik Industri"), and settings.
- **Active State:** Highlighted in `#F58220` with a smooth 4px top indicator or pill background; inactive items render in `#64748B`.

### 2. Status Badges & Pills
- **Diproses (Under Review / In Repair):**
  - Background: `#FFF7ED` (Amber 50)
  - Text: `#C2410C` (Amber 700)
  - Dot Indicator: 6px solid `#EA580C`
- **Selesai / Dikembalikan (Resolved / Returned):**
  - Background: `#ECFDF5` (Emerald 50)
  - Text: `#047857` (Emerald 700)
  - Dot Indicator: 6px solid `#10B981`
- **Denda / Terlambat (Overdue / Fined):**
  - Background: `#FFF1F2` (Rose 50)
  - Text: `#BE123C` (Rose 700)
  - Dot Indicator: 6px solid `#F43F5E`

### 3. Action Buttons
- **Primary Action (New Report / Loan Request):**
  - Background: `#F58220` (Meta Amber) with hover/active state `#EA580C`.
  - Content: Pure white text, semi-bold weight, prefixed with contextual icons.
- **Secondary Action (View Detail & Responses):**
  - Background: `#F8FAFC`, Border: `1px solid #E2E8F0`, Text: `#1B365D`.
- **Tertiary Action (Filter / Inline Actions):**
  - Ghost styling with text `#1B365D` and chevron indicators.

### 4. Input Fields & Search
- **Search Bar:** Height 44px, full width, padded with magnifying glass icon, `border: 1px solid #CBD5E1`, background `#FFFFFF`, focus ring with `#F58220` tint.
- **Form Controls:** Float-labeled inputs for damage description, room selection (e.g., "Lab Komputer Industri 2"), and date/time pickers for loan durations.

### 5. Metric Counter Cards
- Metric cards feature pure white backgrounds, subtle border lines, top or left accent color markers, bold numerical counters, and contextual emoji/vector iconography (Megaphone for Pengaduan, Hourglass for Processing, Box for Active Loans, Cash/Receipt for Denda).

### 6. Official Campus Branding Element
- The collegiate shield rhino emblem is showcased in the top header adjacent to "POLITEKNIK META INDUSTRI CIKARANG" and subtly watermarked in monochrome white at 10% opacity inside the navy welcome hero banner.