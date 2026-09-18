# BelajarData — Design System

Editorial, analytical learning product for Indonesian data-analysis education. Identity is built from sequence, numbering, and typography — not cards, illustrations, or gamification.

## Typography
- **Plus Jakarta Sans** — display/headings (weights 300–800). Weight 300 for large sequence numbers, 700–800 for titles.
- **Inter** — body copy and interface text (400–700).
- **JetBrains Mono** — code, data values, technical metadata, eyebrow labels, status tags.

## Color (OKLCH)
| Token | Value | Use |
|---|---|---|
| `--bg` | `oklch(98% 0.004 95)` | Page background (warm off-white) |
| `--surface` | `oklch(100% 0 0)` | Card/table surfaces |
| `--ink` | `oklch(19% 0.01 260)` | Primary text |
| `--ink-soft` | `oklch(46% 0.01 260)` | Secondary text |
| `--ink-faint` | `oklch(65% 0.008 260)` | Tertiary text, disabled numbers |
| `--line` | `oklch(89% 0.006 95)` | Rules, borders |
| `--accent` | `oklch(47% 0.1 165)` | Brand teal/green — primary actions, current state |
| `--accent-dark` | `oklch(33% 0.08 165)` | Accent text on light backgrounds |
| `--accent-soft` | `oklch(95% 0.03 165)` | Tinted callout/highlight backgrounds |
| `--code-bg` / `--code-text` | dark navy / mint | Code blocks |

One accent color only. No gradients, no glassmorphism, minimal shadows.

## Core Patterns
- **Sequence numbering**: large thin-weight (300) numbers (e.g. `01`–`13`) as the primary visual device for progression, replacing icons/illustrations.
- **Status indicators**: small dots — filled accent (selesai), accent ring on white (sedang berjalan/current), thin line ring (belum dimulai). No pills, no badges beyond mono status labels.
- **Progress rail**: horizontal line of dots connected by segments, filled up to current position.
- **Analytical artifacts**: real data tables, SQL/code blocks, stat comparisons, insight callouts (accent-soft background + left border) — used in place of decorative charts/illustrations.
- **Buttons**: primary (accent fill), secondary (ink outline), dark (ink fill); 8px radius, never pill-shaped.
- **Sections**: numbered mono index + heading + thin rule extending full width.

## Pages Built
1. **BelajarData Homepage.dc.html** — hero with analytical artifact (pertanyaan→data→analisis→insight), learning journey as numbered sequence, Learn→Practice→Challenge→Project band.
2. **Learning Path.dc.html** — full 13-module curriculum grouped by phase, progress rail, per-module state/effort metadata.
3. **Module Detail.dc.html** — module opener with topic sequence timeline culminating in a Module Challenge block.
4. **Lesson Detail.dc.html** — interactive lesson (Why → Learn → Inspect data → Granularity Explorer → Think checkpoint → Practice exercise → Takeaway → Further reading → navigation), 760px reading column + minimal sticky section nav.
5. **Design System.dc.html** — living style guide: colors, type specimens, numbering/status patterns, buttons, artifact components.

## Principles to Preserve
- Learning path is recommended, never locked — no future content is visually disabled.
- No cards-as-default-container; reserve bordered/tinted boxes for things that need bounding (interactive components, warnings, key findings).
- No fake stats, testimonials, logos, XP, or leaderboards.
- Desktop canvas: 1440px wide.
