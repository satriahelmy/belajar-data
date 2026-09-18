# BelajarData — Production Design Specification

**Status:** Visual Direction V2  
**Role:** Production design source of truth for BelajarData.  
**Visual references:** `design-reference/claude-v2/`  
**Product/content truth:** `docs/PRD.md` and `docs/curriculum.md`  
**Technical truth:** `docs/architecture.md`

> BelajarData is a **clean editorial × interactive data-learning product**.  
> Its identity comes from **sequence, numbering, typography, and real analytical artifacts** — not decorative illustration, card grids, or gamification.

---

## 1. Design Goals

BelajarData should help learners feel that there is a clear path through data analysis without making the product feel like a generic LMS, documentation site, or SaaS dashboard.

The interface should feel:

- clean, calm, and modern;
- educational rather than promotional;
- analytical rather than decorative;
- structured without feeling locked;
- credible enough for professional learners;
- interactive where interaction genuinely improves understanding.

The design must support the product principle:

> **Guide, don’t overwhelm. Practice, don’t memorize.**

The visual expression should support another core principle:

> **Analysis is the curriculum. Tools are not the curriculum.**

---

## 2. Visual Identity

### 2.1 Signature device — Sequence & Numbering

The primary visual signature is the curriculum sequence:

`01 → 02 → 03 → … → 13`

Large, thin-weight numbers replace most generic icons and decorative illustrations.

Use sequence/numbering for:

- curriculum modules;
- learning phases;
- module topic sequences;
- lesson sections;
- project stages;
- progress indicators.

Numbers should help orientation and progression, not merely decorate the page.

A screenshot without the BelajarData logo should still plausibly feel like BelajarData because of:

1. sequence and numbering;
2. strong editorial typography;
3. restrained green accent;
4. analytical artifacts such as tables, code, metrics, and findings;
5. generous whitespace and thin rules.

### 2.2 Positive art direction

Do not define the product only by what it avoids.

The positive direction is:

**Clean + bold + educational + analytical + product-focused.**

Homepage may be bolder and more product-led.  
Learning Path is an editorial journey.  
Module Detail is a modern textbook chapter opener.  
Lesson Detail is an interactive textbook / analytical workspace.

These pages share one visual language but do not need identical compositions.

---

## 3. Typography

Use:

- **Plus Jakarta Sans** — display and headings;
- **Inter** — body copy, navigation, interface text;
- **JetBrains Mono** — code, data values, technical metadata, eyebrow labels, status labels, sequence metadata.

Recommended hierarchy:

| Role | Typeface | Weight |
|---|---|---|
| Hero / page H1 | Plus Jakarta Sans | 700–800 |
| Major section heading | Plus Jakarta Sans | 700 |
| Subheading | Plus Jakarta Sans | 600–700 |
| Large sequence number | Plus Jakarta Sans | 300 |
| Navigation | Inter | 500–600 |
| Body | Inter | 400 |
| Interface label | Inter | 500–600 |
| Code / SQL / data | JetBrains Mono | 400–500 |
| Technical eyebrow | JetBrains Mono | 500 |

Do not use Plus Jakarta Sans for long-form body copy.

Reading comfort has priority on Lesson pages. Avoid overly small text, compressed line-height, or long line lengths.

---

## 4. Color System

Production should be cleaner and whiter than the original Claude prototype while preserving its character.

Suggested semantic tokens:

```css
--bg: #ffffff;
--bg-subtle: #fafbf9;
--surface: #ffffff;

--ink: #111827;
--ink-soft: #5f6368;
--ink-faint: #8b9299;

--line: #e5e7e5;

--accent: #087f67;
--accent-dark: #066653;
--accent-soft: #eaf8f2;

--code-bg: #111827;
--code-text: #d9fff3;
```

Exact values may be tuned during implementation for contrast and consistency, but the relationships must remain.

Rules:

- White is the dominant canvas.
- Very subtle warm/neutral off-white may separate major sections.
- Use one restrained green/teal accent family.
- Dark navy/ink is appropriate for code and analytical surfaces.
- Mint/accent-soft is for current state, insight, interaction, or teaching emphasis.
- Do not introduce blue-tinted application surfaces as a second visual identity.
- No gradients.
- No glassmorphism.
- Shadows should be minimal and exceptional.

---

## 5. Spacing, Width & Surfaces

### 5.1 General

Use whitespace, typography, alignment, and thin rules before adding containers.

Avoid the pattern:

`section → card → card → badge → icon → button`

A bordered or tinted surface must have a reason.

Appropriate bounded surfaces:

- interactive components;
- practice exercises;
- warnings;
- important analytical findings;
- code/data workspaces;
- occasional highlighted challenges.

Normal explanatory content should usually live directly on the page.

### 5.2 Radius

- Buttons: approximately 8px.
- Inputs and small controls: 6–8px.
- Larger bounded learning surfaces: maximum approximately 12–14px.
- Never default to oversized rounded cards.
- Avoid pill shapes except where the interaction genuinely requires compact segmented controls.

### 5.3 Desktop canvas

Design reference canvas: approximately 1440px.

Use a generous central container rather than stretching content edge-to-edge.

Reading content must remain comfortably narrow even on wide displays.

---

## 6. Core UI Patterns

### 6.1 Status indicators

Use small status dots:

- **Completed:** filled accent dot.
- **Current / in progress:** accent ring with light/white center.
- **Not started:** thin neutral ring.

Not-started content is **not disabled**.

Do not use lock icons for future modules/topics.

Status labels may use small mono text where useful, but avoid badge proliferation.

### 6.2 Progress rail

A horizontal rail of small connected dots may represent module/topic progress.

It should communicate location, not gamification.

### 6.3 Section heading

Preferred pattern:

`01  Section Title ─────────────────`

Use a small mono index, strong heading, and thin rule where appropriate.

### 6.4 Buttons

Three primary families:

- **Primary:** accent fill.
- **Secondary:** ink/neutral outline.
- **Dark:** dark ink fill, especially inside analytical/workspace contexts.

Buttons should be compact and purposeful, not oversized marketing pills.

### 6.5 Analytical artifacts

Prefer real analytical artifacts over illustration:

- dataset tables;
- SQL/code blocks;
- formula fragments;
- metric comparisons;
- data-quality findings;
- small charts when they are actually being taught;
- insight callouts;
- analysis notes.

Artifacts must teach or prove something. Never add meaningless charts merely to make a page look “data-like.”

### 6.6 Insight callout

Use accent-soft background with a restrained accent edge/rule.

Callouts are for conclusions, important principles, warnings, or teaching moments—not ordinary paragraphs.

---

## 7. Navigation

Primary navigation should remain simple and content-oriented.

Do not make the top navigation resemble a SaaS dashboard toolbar.

Navigation labels must reflect actual product scope defined by the PRD.

Desktop navigation should be visually quiet so page content remains dominant.

Mobile/tablet navigation may collapse into an accessible disclosure/menu.

Use:

- semantic navigation;
- visible keyboard focus;
- `aria-current` for current locations;
- accessible labels;
- reduced-motion support.

---

## 8. Homepage

### Purpose

Explain immediately:

1. what BelajarData is;
2. why the learning path exists;
3. what learning here looks like.

### Hero

Use a two-column composition.

**Left:**
- contextual label such as `DATA ANALYST PATH`;
- proposition: **“Belajar data, tanpa bingung mulai dari mana.”**
- concise supporting copy;
- primary CTA: `Mulai belajar`;
- secondary CTA: `Lihat learning path`.

**Right:**
A meaningful analytical learning artifact using a coherent case such as NusaMart:

`PERTANYAAN → DATA → ANALISIS → INSIGHT`

Example ingredients:

- business question;
- small dataset/table;
- SQL or analytical step;
- metric result;
- concise finding.

This is product proof, not decorative mockup.

Do not invent product claims, dataset counts, reviews, certificates, or capabilities.

### Learning journey

Show the curriculum as sequence, not a course-card grid.

Five phases:

1. **Foundations** — Modules 01–04
2. **Working with Data** — Modules 05–07
3. **From Analysis to Decision** — Modules 08–10
4. **Business Impact** — Modules 11–12
5. **Apply Your Skills** — Module 13

The numbers and phase lines should carry the visual identity.

### Learning model

Communicate:

`Learn → Practice → Challenge → Project`

This may use a strong horizontal editorial band. Avoid four generic feature cards.

---

## 9. Learning Path (`/learn`)

### Purpose

Help learners understand:

- the full journey;
- where they currently are;
- what comes next;
- what each module teaches.

The path is **recommended, never locked**.

### Structure

Use a vertical editorial sequence grouped by the five phases.

Each module row may contain:

- status dot;
- large module number;
- module title;
- one-line learning outcome;
- approximate effort when useful;
- compact state/action.

Do not turn 13 modules into 13 large cards.

Current module may use a subtle accent-soft horizontal highlight.

Phase changes should be visible through spacing, labels, and rules.

Projects / Module 13 may receive a slightly stronger treatment as the culmination, without becoming a promotional card.

### Progress

Progress is descriptive:

- completed;
- current;
- not started.

It must never imply that later modules are inaccessible.

Avoid dashboard-like summary panels unless they materially improve orientation.

---

## 10. Module Detail

### Purpose

Treat the module as the opening of a chapter.

Communicate:

`module identity → learning outcome → topic sequence → module challenge → next module`

### Module opener

Include:

- phase;
- module number;
- title;
- concise purpose/outcome;
- topic count;
- approximate effort;
- clear start/continue action.

Avoid filling the opener with three generic feature cards.

### Topic sequence

The sequence should dominate the page.

Preferred treatment:

```text
01  Topic title
    Short outcome
    |
02  Topic title
    Short outcome
    |
...
07  Topic title
    Short outcome
    |
●   MODULE CHALLENGE
```

Learners may jump to any topic.

No locks.

### Module Challenge

The challenge is the culmination of the sequence, not an unrelated sidebar card.

It may use a bounded accent-soft surface because it represents a distinct applied activity.

For Module 01, the canonical challenge is the NusaMart Sales Drop Investigation.

---

## 11. Lesson Detail

### Product model

The Lesson page is the most important learning surface.

It should feel like:

> **A beautifully designed data-analysis textbook that happens to be interactive.**

It must not feel like:

- documentation;
- a blog;
- a generic LMS;
- a dashboard;
- a Markdown renderer.

### 11.1 Desktop composition

Use approximately:

- main reading column: **720–780px**;
- gap: generous;
- minimal sticky section navigator: **180–220px**.

The right side exists for orientation, not extra content.

Do not fill the sidebar with:

- glossary cards;
- tips;
- related resources;
- achievements;
- XP;
- stats;
- promotional content.

### 11.2 Sticky lesson navigator

Show:

- current topic, e.g. `TOPIK 04 / 07`;
- lesson section list;
- subtle current-section indicator.

Example:

```text
TOPIK 04 / 07

DI HALAMAN INI
01  Kenapa Ini Penting
02  Konsep Inti
03  Lihat Datanya
04  Eksplorasi Granularitas
05  Latihan
06  Bacaan Lanjutan
```

Prefer typography, numbering, a thin vertical rule, and small indicators over a large sidebar card.

On smaller screens, collapse this navigator into a compact accessible disclosure or omit the sticky behavior while preserving navigation access.

### 11.3 Lesson flow

Default conceptual flow:

`WHY → LEARN → EXAMPLE / INSPECT → INTERACTIVE (when useful) → THINK → PRACTICE → TAKEAWAY → FURTHER READING → NEXT`

Not every lesson must mechanically contain every section.

The structure must follow the learning objective.

### 11.4 Reading rhythm

Normal prose stays within the reading column.

Data-heavy or interactive components may intentionally break wider than the text column, approximately 900–1000px when useful.

This creates rhythm:

```text
      explanatory text

┌─────────────────────────────┐
│ interactive/data workspace  │
└─────────────────────────────┘

      explanation / reasoning
```

Do not use wide components simply because space exists.

### 11.5 Lesson header

Show once:

- module/topic context;
- lesson title;
- estimated time;
- topic position;
- learning objective.

Do not repeat the H1 inside Markdown/content.

Avoid a large TOC box before the lesson.

---

## 12. Practice & Interactive Components

### Principle

Interaction is not decoration.

> **If manipulating a variable and seeing the consequence helps build intuition, make it interactive. Otherwise, don’t.**

Two component families:

### Practice Playground

Learner performs a skill:

- SQL;
- Spreadsheet;
- bounded Pandas/code exercise;
- visualization exercise.

### Concept Interactive

Learner manipulates a concept:

- JOIN row multiplication;
- sampling & uncertainty;
- visualization choices;
- metric tree;
- communication builder.

### Visual behavior

Interactive components may be more product-like than editorial prose, but they must still belong to the same design system.

Use:

- clear active state;
- restrained tabs/segmented controls;
- immediate feedback;
- real data;
- clear instructions;
- compact status.

Controls must visibly look interactive.

Do not make active tabs indistinguishable from static headings.

Do not build mini-Tableau, mini-Excel, or generic dashboard builders.

---

## 13. Tables, Code & Data

### Tables

Tables are teaching objects, not decorative UI.

Use:

- clear headers;
- restrained row separators;
- mono treatment where appropriate;
- horizontal overflow on narrow screens;
- highlighting only when pedagogically useful.

### Code

Code blocks may use dark analytical surfaces.

Keep syntax/code readable and copyable where relevant.

Lesson narrative should teach concepts rather than turn into a SQLite/Python syntax manual.

### Metrics

Small metric comparisons may appear inline or in compact stat blocks.

Avoid dashboard-style KPI grids unless the lesson specifically teaches dashboard/KPI design.

---

## 14. Checkpoints & Exercises

### Think / reasoning checkpoint

Keep lightweight.

Example:

> If this table contains one row per order item, can `COUNT(*)` directly represent total orders?

The checkpoint should feel embedded in the lesson, not like a giant quiz card.

### Auto-checkable practice

Use:

- selection;
- numeric answer;
- SQL output;
- table result;
- bounded interactive result.

Provide clear `Check Answer` / `Run` action and immediate feedback.

### Open-ended reasoning

Do not fake automatic grading.

Use:

`Submit → Reference Answer + Checklist → Mark Complete`

No AI grading in V1.

---

## 15. Further Reading

Further Reading is curated secondary content.

Each item may show:

- title;
- source;
- type/level where useful;
- estimated time;
- one sentence: why this is useful.

Target 2–4 high-quality resources per topic.

Prefer understated rows/list treatment.

Do not use large promotional cards.

---

## 16. Lesson End Navigation

End lessons with clear journey continuity:

- Previous topic;
- Current topic;
- Next topic;
- Mark complete where applicable.

Use numbering prominently.

Do not make future topics look disabled or locked.

---

## 17. Responsive Behavior

### Desktop

Use the full editorial composition and optional sticky lesson navigator.

### Tablet

- preserve comfortable reading width;
- reduce side margins;
- convert wide two-column layouts when needed;
- lesson navigator may become compact or non-sticky.

### Mobile

- single-column reading flow;
- no persistent right sidebar;
- curriculum/module rows remain list-based rather than becoming stacked oversized cards;
- tables/code use safe horizontal overflow;
- interactive controls must remain touch-friendly;
- homepage analytical artifact stacks below hero copy;
- learning sequence must remain readable without relying only on horizontal layout.

Do not solve mobile by hiding essential learning content.

---

## 18. Accessibility

Required:

- semantic headings;
- semantic navigation and landmarks;
- keyboard-accessible controls;
- visible `:focus-visible`;
- sufficient color contrast;
- active/completed/current states not communicated by color alone;
- accessible form labels;
- accessible validation feedback;
- `aria-current` where appropriate;
- reduced-motion support;
- no interaction that requires hover only.

Decorative numbering must not confuse screen-reader order.

---

## 19. Motion

Motion is optional and restrained.

Appropriate:

- subtle tab state transition;
- small progress transition;
- interactive data update;
- focus/hover feedback.

Avoid:

- parallax;
- scroll spectacle;
- bouncing CTAs;
- animated decorative charts;
- motion that delays learning.

Respect `prefers-reduced-motion`.

---

## 20. Content & Product Integrity

Prototype HTML files are **visual references, not product truth**.

Codex must not copy invented content or features from a prototype.

Source hierarchy:

1. `docs/curriculum.md` — curriculum/content truth;
2. `docs/PRD.md` — product requirements;
3. `docs/design.md` — production visual/UX rules;
4. `docs/architecture.md` — implementation boundaries;
5. `design-reference/claude-v2/` — visual reference only;
6. `task.md` — implementation sequence based on actual repository.

If a prototype conflicts with curriculum/PRD/architecture, the docs win.

Examples of things that must not be invented from visual prototypes:

- fake learner counts;
- fake dataset counts;
- certificates;
- XP;
- leaderboard;
- 1:1 review;
- unsupported downloadable syllabus;
- locked modules;
- unsupported tools or curriculum topics.

---

## 21. Implementation Guidance for Codex

The reference HTML may be inspected for:

- composition;
- spacing;
- type scale;
- proportions;
- section rhythm;
- interaction appearance;
- analytical artifact styling.

It must **not** be copied as a replacement architecture.

Preserve the existing Laravel/Blade/content architecture and progressive-enhancement boundaries.

Prefer reusable production patterns/components over page-specific copied HTML.

Do not add dependencies merely to reproduce a decorative detail.

Interactive components must respect the technical contracts in `docs/architecture.md`.

The M1A technical foundation is already valid; this visual direction is a refinement, not a greenfield rebuild.

---

## 22. Page Reference Mapping

Use the corresponding files under `design-reference/claude-v2/` as visual references:

| Production surface | Visual reference |
|---|---|
| Homepage | `BelajarData Homepage.dc.html` |
| Learning Path | `Learning Path.dc.html` |
| Module Detail | `Module Detail.dc.html` |
| Lesson Detail | `Lesson Detail.dc.html` |
| Shared visual system | `Design System.dc.html` |
| Original Claude notes | `design.md` |

If filenames differ in the repository, preserve the actual exported filenames and update this table.

---

## 23. Anti-Patterns

Avoid:

- generic SaaS dashboard appearance;
- generic LMS appearance;
- documentation-site appearance;
- course marketplace card grids;
- card around every section;
- excessive pills/badges;
- gradients;
- glassmorphism;
- large decorative shadows;
- generic 3D illustrations;
- random icons;
- floating blobs;
- meaningless charts;
- fake social proof;
- fake product statistics;
- excessive gamification;
- duplicated page/lesson titles;
- locked-looking future content;
- oversized empty hero areas with no product proof;
- overly narrow desktop lesson columns that create accidental empty space;
- filling whitespace with unrelated sidebar widgets.

---

## 24. Final Quality Bar

Before accepting a page, ask:

1. Is the learner’s next action obvious?
2. Does hierarchy come primarily from typography, spacing, numbering, and artifacts rather than cards?
3. Does the page visibly belong to the `01 → 13` BelajarData learning system?
4. Is every analytical artifact meaningful?
5. Is every interactive element visibly interactive?
6. Does the page remain calm enough for extended learning?
7. Does it avoid looking like documentation, an LMS dashboard, or an AI-generated SaaS landing page?
8. Are future modules/topics clearly accessible rather than locked?
9. Does the implementation follow the PRD/curriculum rather than prototype hallucinations?
10. Would the page still feel like BelajarData if the logo were removed?

If several answers are “no”, refine the composition before adding more decoration.
