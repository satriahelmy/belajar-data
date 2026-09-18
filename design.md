# BelajarData — Design Specification

**Product:** BelajarData  
**Version:** V1  
**Status:** Design direction for implementation  
**References:** `curriculum.md`, `PRD.md`

---

## 1. Design Intent

BelajarData should feel like a deliberately designed learning product, not a generic SaaS dashboard and not an AI-generated landing page.

The interface must make a large curriculum feel calm, understandable, and approachable.

The product should communicate:

- clarity;
- structure;
- seriousness without stiffness;
- progress without gamification;
- technical credibility;
- warmth without excessive decoration.

The design should disappear behind the learning experience.

> **Content is the interface.**

The strongest visual hierarchy should come from typography, spacing, alignment, and content structure—not gradients, glowing cards, excessive icons, or decorative effects.

---

## 2. Anti-"AI Slop" Direction

This section is intentionally explicit because generic AI-generated UI patterns are not the desired aesthetic.

### Avoid

- large purple/blue gradient hero backgrounds;
- glowing gradient blobs;
- glassmorphism;
- excessive rounded cards;
- every section placed inside a card;
- floating decorative dashboard widgets;
- fake charts used only as decoration;
- oversized pill-shaped navigation everywhere;
- rainbow gradients;
- gradient text headlines;
- unnecessary shadows;
- huge border radii;
- generic icon inside colored circle for every feature;
- repeated three-column feature-card grids;
- "Trusted by..." sections without real evidence;
- fabricated user testimonials;
- fake activity feeds;
- fake learner counts;
- fake company logos;
- excessive badges such as "AI Powered", "New", "Pro", "Smart";
- animated counters with no product value;
- decorative illustrations that do not teach anything;
- excessive use of emojis in the product UI;
- generic stock photos of people looking at laptops;
- a dashboard homepage full of unrelated statistics;
- overuse of dark navy + neon accent simply because the product is technical.

### Do instead

- use a strong editorial layout;
- let headings and body copy carry the hierarchy;
- use whitespace intentionally;
- use thin borders and subtle surface changes;
- keep navigation stable;
- show real curriculum content as the product preview;
- use actual exercises/datasets/code snippets when visual examples are needed;
- allow some pages to have no cards at all;
- use compact controls;
- keep component shapes restrained;
- prefer one clear accent over multiple decorative colors.

### Visual test

Before accepting a screen, ask:

> If the logo and product name were removed, would this still look like a deliberate education product—or like a generic AI-generated SaaS template?

If it looks interchangeable with an AI startup landing page, revise it.

---

## 3. Product Personality

BelajarData should feel:

**Structured**  
The learner always knows where they are and what comes next.

**Practical**  
Examples, datasets, queries, formulas, and analytical decisions are visible.

**Calm**  
The interface does not compete for attention.

**Credible**  
No exaggerated marketing language or artificial social proof.

**Human**  
Copy is direct Indonesian, not corporate jargon.

**Focused**  
A learner opening a lesson should feel that there is one main thing to do.

---

## 4. Visual Inspiration Model

Do not copy another product literally.

The intended design language is closer to the intersection of:

- editorial documentation;
- modern technical learning environments;
- carefully designed reading applications;
- restrained developer tools.

The interface may borrow useful qualities such as:

- documentation-style navigation;
- editorial typography;
- code-editor precision;
- textbook-like content rhythm;
- lightweight progress indicators.

It should **not** visually imitate a corporate analytics dashboard.

---

## 5. Core Design Principles

### 5.1 Typography before decoration

Use typography to distinguish:

- page title;
- module title;
- lesson title;
- section heading;
- explanatory copy;
- labels;
- metadata;
- code;
- captions.

Do not create visual hierarchy by putting every level into a different colored box.

### 5.2 One dominant action per context

A lesson might primarily ask the learner to:

> Continue lesson

or:

> Run query

or:

> Check answer

Do not place several equally prominent buttons beside one another.

### 5.3 Progressive disclosure

Do not show every possible action at once.

Examples:

- Further Reading can appear after the core lesson;
- hints appear on demand;
- reference approaches appear after an attempt or explicit request;
- advanced notes remain secondary.

### 5.4 Learning state, not game state

Progress should communicate orientation, not addiction mechanics.

Use:

- percentage/progress;
- completed check;
- current lesson;
- resume;
- project stage.

Avoid:

- XP;
- streak pressure;
- coins;
- levels;
- confetti;
- competitive rank.

### 5.5 Interfaces should reflect analytical work

When possible, product visuals should contain real:

- tables;
- SQL;
- formulas;
- charts;
- metric trees;
- analytical briefs.

These are more distinctive and useful than generic illustrations.

---

## 6. Color Direction

Use a predominantly neutral interface with one primary accent.

### Neutral foundation

The majority of the UI should be:

- near-white / warm-white backgrounds;
- dark neutral text;
- subtle neutral borders;
- muted secondary text.

Avoid pure black against pure white across large reading areas if a slightly softer neutral improves comfort.

### Accent

Choose one recognizable BelajarData accent color.

Use it selectively for:

- active navigation;
- primary actions;
- links;
- current progress;
- selected states;
- small emphasis.

The accent must not flood entire pages.

### Semantic colors

Reserve semantic colors for actual meaning:

- success;
- warning;
- error;
- information.

Do not use semantic colors as decorative module colors unless there is a strong system behind them.

### Phase differentiation

Do not assign five loud colors to the five curriculum phases.

If phases need distinction, use:

- labels;
- numbering;
- subtle tint;
- typography;
- small markers.

---

## 7. Typography

Typography is a major part of the identity.

### Recommended direction

Use a clean sans-serif optimized for long-form web reading and UI.

A secondary monospace font is required for:

- SQL;
- Python;
- formulas where appropriate;
- code output.

Avoid combining many font families.

### Scale

Use a restrained type scale.

Suggested hierarchy:

- Display / landing headline: 44–56 px desktop
- Page title: 32–40 px
- Module title: 28–32 px
- Lesson title: 26–32 px
- Section heading: 20–24 px
- Subheading: 16–18 px
- Body: 16–18 px
- UI labels: 13–15 px
- Metadata: 12–14 px
- Code: 13–15 px

Exact values may be adjusted during implementation.

### Reading width

Long-form lesson prose should generally remain around:

**640–760 px**

Do not stretch paragraphs across a 1440 px viewport.

---

## 8. Spacing

Use a consistent spacing system.

Recommended base:

`4, 8, 12, 16, 24, 32, 48, 64, 96`

Large whitespace is encouraged between conceptual sections.

Dense areas such as:

- tables;
- code editors;
- result grids;
- navigation;

may use tighter spacing.

Do not apply the same spacing density everywhere.

---

## 9. Shape and Elevation

### Border radius

Keep radius restrained.

Recommended:

- controls: 6–8 px;
- cards/panels: 8–12 px;
- large feature surfaces: max around 12–16 px when justified.

Avoid 24–32 px rounded rectangles throughout the interface.

### Shadows

Use shadows rarely.

Prefer:

- border;
- background contrast;
- whitespace.

A floating menu/dialog may use elevation. Normal curriculum cards generally should not.

---

## 10. Iconography

Icons should support recognition, not decorate every label.

Use icons for:

- navigation where useful;
- bookmark;
- completion;
- expand/collapse;
- external link;
- run/reset;
- warning;
- file/download.

Do not place an icon inside a colored circular container for every lesson or feature.

Use one consistent icon family.

---

# INFORMATION ARCHITECTURE

## 11. Global Navigation

Desktop primary navigation:

```text
BelajarData        Learn     Explore Skills     Projects

                                      Search    [Account]
```

If Search is deferred:

```text
BelajarData        Learn     Explore Skills     Projects

                                               [Account]
```

For guests:

```text
Masuk
```

should be visible but not visually dominant over learning.

Avoid a large application sidebar globally. A sidebar is appropriate inside learning contexts, not necessarily on the marketing/home surface.

---

## 12. Header Behavior

The global header should be:

- compact;
- stable;
- visually quiet;
- optionally sticky;
- separated using a thin border rather than a large shadow.

On lesson pages, the header may reduce visual prominence to maximize reading space.

---

# PUBLIC / HOME EXPERIENCE

## 13. Homepage Goal

The homepage has one job:

> Help a visitor understand what BelajarData teaches and let them start learning quickly.

Do not turn the homepage into a long SaaS sales funnel.

Recommended structure:

```text
Header

Hero
↓
Data Analyst Learning Path preview
↓
How learning works
↓
Practice preview
↓
Projects preview
↓
Final start-learning CTA
↓
Footer
```

Keep it relatively short.

---

## 14. Hero

Use a restrained editorial hero.

Example structure:

```text
Belajar data, tanpa bingung mulai dari mana.

Jalur belajar Data Analyst yang terstruktur,
dari memahami masalah hingga mengkomunikasikan insight.

[Mulai belajar]   Lihat kurikulum

────────────────────────────────────────────

01 Thinking with Data
02 Spreadsheet
03 SQL
04 Python & Pandas
...
```

The curriculum itself can become the visual asset.

### Do not use

- abstract 3D illustration;
- floating dashboards;
- gradient sphere;
- fake AI chat;
- laptop mockup unless it shows the actual product;
- huge marketing badges.

The hero should be believable before the product has thousands of users.

---

## 15. Curriculum Preview

Instead of generic feature cards, show a real abbreviated path.

Example:

```text
FOUNDATIONS
01  Thinking with Data
02  Spreadsheet for Analysis
03  SQL for Data Analysis
04  Python & Pandas for Analysis

WORKING WITH DATA
05  Data Cleaning
06  Exploratory Data Analysis
07  Statistics for Analysts
```

Continue or offer:

`Lihat seluruh learning path →`

This immediately differentiates BelajarData from generic course websites.

---

## 16. How Learning Works

Use a simple horizontal or vertical sequence, not four giant cards.

```text
LEARN
Pahami konsep

→

PRACTICE
Coba langsung

→

CHALLENGE
Gunakan dalam kasus

→

PROJECT
Kerjakan analisis utuh
```

Short explanation only.

---

## 17. Practice Preview

Show a credible miniature of a real learning interaction.

For example:

```text
SQL Playground

Find revenue by category.

SELECT
    category,
    SUM(revenue)
FROM orders
...

[Run]

category        revenue
Furniture       ...
Technology      ...
```

This is preferable to a decorative screenshot.

---

# LEARN EXPERIENCE

## 18. Learn Landing Page

The Learn page is the learner's map.

Desktop structure:

```text
Data Analyst Learning Path

Belajar dari analytical thinking hingga
mampu mengerjakan business analysis sendiri.

[Overall progress when available]

────────────────────────────────────

FOUNDATIONS
4 modules

01  Thinking with Data
    Understand how analysts turn business problems into...
    7 topics · ~X min                       [Start/Continue]

02  Spreadsheet for Analysis
    ...
```

Use **rows or restrained module blocks**, not a grid of 13 oversized cards.

Why:

- sequence matters;
- titles are easier to scan vertically;
- progress reads naturally;
- the page feels like a curriculum, not a course marketplace.

---

## 19. Phase Presentation

A phase should be a clear section break.

Example:

```text
FOUNDATIONS                                      2 / 4

Build the analytical and technical foundation
used throughout the rest of the path.

─────────────────────────────────────────────────────
01  Thinking with Data                     Completed ✓
─────────────────────────────────────────────────────
02  Spreadsheet for Analysis               6 / 8
─────────────────────────────────────────────────────
03  SQL for Data Analysis                  Continue →
─────────────────────────────────────────────────────
```

Avoid giant colored phase banners.

---

## 20. Module Detail Page

Recommended structure:

```text
← Data Analyst Path

03
SQL for Data Analysis

Learn to answer analytical questions
using relational data.

Recommended knowledge
Thinking with Data

8 topics · estimated time

[Continue learning]

────────────────────────

Topics

01 Understanding Data in Tables          ✓
02 Filtering Data                        ✓
03 Aggregations                          Current
04 Breaking Down Performance
05 JOINs
...
────────────────────────

Module Challenge
Sales Investigation
```

Module pages should feel like a syllabus.

---

# LESSON EXPERIENCE

## 21. Desktop Lesson Layout

Recommended three-region layout:

```text
┌────────────────────────────────────────────────────────────┐
│ Global header                                              │
├────────────────┬─────────────────────────────┬─────────────┤
│ Module nav     │ Lesson content              │ On this page│
│                │                             │             │
│ 01 ✓           │ 03.5 JOINs                  │ Concept     │
│ 02 ✓           │                             │ Example     │
│ 03 ✓           │ Why JOINs can multiply rows │ Practice    │
│ 04             │                             │ Think       │
│ 05 ← current   │ ...                         │ Reading     │
│                │                             │             │
└────────────────┴─────────────────────────────┴─────────────┘
```

The right "On this page" column is optional and may disappear for shorter lessons.

### Width

- left navigation: ~240–280 px;
- content: readable max width;
- right outline: ~180–220 px;
- generous outer margins.

For playground-heavy lessons, content can temporarily expand.

---

## 22. Lesson Navigation

Left module navigation should show:

- topic number;
- concise title;
- completion;
- current position.

Do not show excessive descriptions.

Allow collapse on smaller screens.

Mobile uses a top lesson selector/drawer rather than a permanently visible sidebar.

---

## 23. Lesson Header

Example:

```text
03 · SQL for Data Analysis

05
JOINs Without Duplicating Your Metrics

Understand why joins can unexpectedly increase
row counts and distort analytical results.

~12 min
```

Avoid:

- decorative icon;
- colored gradient header;
- large hero card inside every lesson.

---

## 24. Lesson Content Rhythm

A lesson should visually alternate between prose and purposeful learning objects.

Example:

```text
WHY THIS MATTERS

short prose

UNDERSTAND THE IDEA

explanation
diagram/table

EXAMPLE

realistic example

TRY IT

practice environment

THINK

one reflection/check

FURTHER READING

curated resources
```

Labels do not need to be uppercase in the actual UI; this demonstrates hierarchy.

---

## 25. Callouts

Use callouts sparingly.

Supported types might include:

- Note
- Important
- Common mistake
- Remember

Visual treatment:

- subtle border or left rule;
- light neutral/semantic tint;
- small label.

Do not create brightly colored alert cards every few paragraphs.

---

## 26. Tables

Tables are first-class learning objects.

Requirements:

- readable headers;
- compact but comfortable row height;
- horizontal scroll on small screens;
- numeric alignment where useful;
- highlighted cells only when pedagogically relevant.

Avoid excessive zebra striping or ornamental styling.

---

## 27. Code Blocks

Code blocks should look like technical content, not decorative product screenshots.

Support:

- monospace;
- line wrapping strategy appropriate to code;
- copy action;
- optional line numbers when useful;
- syntax highlighting;
- clear contrast.

Do not use fake macOS traffic-light dots unless the block actually represents a window.

---

# PRACTICE COMPONENTS

## 28. Shared Practice Shell

Where possible, interactive practice should share a consistent frame.

```text
Task

Calculate total revenue by category.

────────────────────────────────

[working area]

────────────────────────────────

[Check answer]

Hint                         Reset
```

Feedback appears in context rather than in a modal whenever possible.

---

## 29. Feedback States

### Correct

```text
Correct

Your result matches the expected output.

Why this matters:
...
```

### Incorrect

Do not only show:

> Wrong.

Instead:

```text
Not quite yet

Your result contains 12 rows.
The expected result contains 4.

Check whether your GROUP BY matches
the requested grain.
```

Feedback should help reasoning without immediately giving the answer.

---

## 30. Hints

Hints are progressive.

Example:

```text
Hint 1
Which field represents the grouping level?

[Show another hint]

Hint 2
You may need GROUP BY category.
```

Avoid instantly revealing complete solutions.

---

# SQL PLAYGROUND

## 31. SQL Layout

Desktop:

```text
┌──────────────────────────────────────────────┐
│ Task                                         │
├──────────────────────┬───────────────────────┤
│ Dataset / schema     │ SQL editor            │
│                      │                       │
│ orders               │ SELECT ...            │
│ order_items          │                       │
│ products             │                       │
│                      │              [Run]    │
├──────────────────────┴───────────────────────┤
│ Results                                      │
│                                              │
└──────────────────────────────────────────────┘
```

The editor is the visual focus.

Do not wrap every panel in a thick floating card.

Use borders/split panes.

---

## 32. SQL Schema Browser

Show only useful information:

```text
orders
  order_id
  customer_id
  order_date

order_items
  order_id
  product_id
  quantity
  sales
```

Optional small sample preview.

Avoid building a full database IDE.

---

# SPREADSHEET PLAYGROUND

## 33. Spreadsheet Layout

The spreadsheet should visually resemble a data workspace, but remain deliberately limited.

```text
Task
──────────────────────────────────

fx  =SUMIFS(...)

    A          B          C
1   Region     Sales      Profit
2   West       ...
3   East       ...

──────────────────────────────────
[Check answer]
```

The exercise instructions may sit above or beside the grid.

Avoid reproducing Excel's entire ribbon.

---

# PYTHON / PANDAS

## 34. Python Practice Layout

```text
Task

Find revenue by category using Pandas.

────────────────────────────────────

import pandas as pd

df = ...

[Run]

────────────────────────────────────

Output
...
```

The runtime should feel closer to a focused exercise editor than a notebook product.

Do not implement:

- notebook file tree;
- kernels;
- terminal;
- extensions;
- multiple arbitrary cells unless curriculum requires them.

---

# VISUALIZATION

## 35. Visualization Playground

Recommended split:

```text
┌──────────────────┬───────────────────────────┐
│ Analytical task  │                           │
│                  │         CHART             │
│ Metric           │                           │
│ [Revenue      v] │                           │
│                  │                           │
│ Dimension        │                           │
│ [Category     v] │                           │
│                  │                           │
│ Chart            │                           │
│ [Bar          v] │                           │
│                  │                           │
│ Sort             │                           │
│ [Descending   v] │                           │
└──────────────────┴───────────────────────────┘
```

Keep controls intentionally finite.

The learner should make analytical/design decisions, not build arbitrary dashboards.

---

# SPECIAL INTERACTIVES

## 36. JOIN Row Multiplication

Use tables as the visual language.

Example:

```text
CUSTOMERS              ORDERS

C01  Rina              O01  C01
C02  Budi              O02  C01
                       O03  C02

        JOIN
          ↓

C01 Rina O01
C01 Rina O02   ← customer row repeats
C02 Budi O03
```

Allow the learner to change join fields or inspect row counts.

The interactive should make grain visible.

No decorative animation is necessary.

---

## 37. Sampling & Uncertainty

Use a simple population and repeated sample visualization.

Controls may include:

- sample size;
- resample;
- number of samples.

Show how estimates vary.

The purpose is intuition, not a statistical simulation laboratory.

---

## 38. Metric Tree Builder

Use a structured tree workspace.

Example:

```text
Revenue
├── Orders
└── Average Order Value
    ├── Units / Order
    └── Price / Unit
```

Learners can select/arrange predefined metrics.

Do not build a generic node-diagram editor.

---

## 39. Communication Builder

Use a writing-focused layout.

```text
Q3 Revenue Investigation

Headline
[____________________________________]

Key evidence
[____________________________________]

What we know
[____________________________________]

What we don't know
[____________________________________]

Next step
[____________________________________]

[Review response]
```

After review, show:

- reference approach;
- evidence checklist;
- language-strength checklist.

Avoid AI-style chat bubbles.

---

# CHALLENGES

## 40. Module Challenge Page

A challenge should visually feel like a case, not another lesson.

Example:

```text
MODULE CHALLENGE

NusaMart Sales Investigation

The brief
────────────────────────
NusaMart's sales team noticed...

Dataset
orders.csv
products.csv

Your task
1. ...
2. ...

[Start challenge]
```

Once started, use the appropriate practice environment.

---

# PROJECTS

## 41. Projects Landing Page

Do not present projects like generic course cards.

Recommended:

```text
Projects

Apply your skills to less-prescriptive
business problems.

──────────────────────────────────────────────

01
NusaMart Revenue Slowdown

PERFORMANCE DIAGNOSIS · GUIDED

Investigate why growth slowed and determine
where the business should look next.

Estimated effort 4–6 hours
Progress 3 / 6 stages

[Continue]

──────────────────────────────────────────────

02
Customer Retention Analysis

CUSTOMER BEHAVIOR · INTERMEDIATE
...
```

Use editorial rows with strong numbering.

---

## 42. Project Workspace

Project pages need more workspace than normal lessons.

Desktop:

```text
┌───────────────────┬────────────────────────────────────┐
│ Project stages    │ Project workspace                  │
│                   │                                    │
│ 01 Brief ✓        │ INVESTIGATE                        │
│ 02 Understand ✓   │                                    │
│ 03 Plan ✓         │ Business question                  │
│ 04 Investigate ←  │ Dataset links                      │
│ 05 Validate       │ Checkpoints                        │
│ 06 Communicate    │ Notes / responses                  │
│                   │                                    │
│ Reference         │                                    │
└───────────────────┴────────────────────────────────────┘
```

Project stages are not styled as mini-courses.

---

## 43. Reference Approach

Reference Approach should have a distinct but understated treatment.

Show:

- framing;
- metrics;
- analysis path;
- findings;
- evidence;
- limitations;
- communication example.

Use:

> **One possible approach**

rather than:

> **Correct solution**

If opened before completion, a simple confirmation is enough:

> We recommend attempting the project first. View the reference anyway?

No hard lock.

---

# EXPLORE SKILLS

## 44. Explore Skills Page

Explore Skills is an alternate index into canonical curriculum.

Use a simple directory.

```text
Explore Skills

SQL
8 topics across the Data Analyst Path
[Explore SQL]

Spreadsheet
8 topics
[Explore Spreadsheet]

Python & Pandas
8 topics
[Explore Python & Pandas]

Tableau
7 topics
[Explore Tableau]
```

Do not make each skill look like a separate paid course.

The page should communicate that these topics belong to the same learning system.

---

# PROGRESS

## 45. Progress Page

Progress should answer:

> Where am I, what have I completed, and what could I continue?

Recommended:

```text
Your Progress

CORE LEARNING
62%

Foundations              4 / 4
Working with Data        2 / 3
Analysis to Decision     1 / 3
Business Impact          0 / 2

PROJECTS
1 / 3

Recent
SQL — JOINs                       Continue →
Statistics — Sampling             Continue →
```

Avoid:

- giant donut charts;
- many KPI cards;
- streak heatmaps in V1;
- ranking.

---

## 46. Completion

Completion feedback should be quiet.

Example:

```text
Module completed

SQL for Data Analysis

You can now use SQL to answer analytical
questions across related tables.

Next recommended:
Python & Pandas for Analysis

[Continue]
```

No confetti required.

---

# BOOKMARKS / HISTORY

## 47. Bookmarks

Simple list grouped by type or module.

```text
Bookmarks

LESSONS

SQL
JOINs Without Duplicating Metrics

Statistics
Samples, Populations & Bias

FURTHER READING

Pandas GroupBy Documentation
```

Avoid a Pinterest-style card grid.

---

# AUTHENTICATION

## 48. Login / Registration

Authentication should be lightweight.

Do not make registration look like an onboarding funnel.

Example:

```text
Save your learning progress

Create an account to sync progress,
bookmarks, and projects across devices.

Email
[________________]

Password
[________________]

[Create account]

Already have an account? Sign in
```

The message should reinforce that learning itself is not paywalled behind registration.

---

# EMPTY / ERROR STATES

## 49. Empty States

Keep them useful and quiet.

Bookmarks empty:

```text
No bookmarks yet.

Save lessons or resources you want
to return to later.

[Explore learning path]
```

No giant illustration is required.

---

## 50. Errors

Errors should explain recovery.

Bad:

> Something went wrong.

Better:

> We couldn't run this query. Your work is still in the editor. Try again.

Technical details may be expandable if useful.

---

# RESPONSIVE DESIGN

## 51. Mobile

Reading and simple exercises must work well on mobile.

### Mobile lesson

```text
Header
Module / lesson selector

Lesson title

Content
Content
Content

Practice

Previous     Next
```

Sidebars become drawers/selectors.

### Complex tools

For SQL, Spreadsheet, Python, and Tableau-oriented work, it is acceptable to show:

> **Desktop recommended for this exercise.**

Do not hide the lesson itself.

---

## 52. Tablet

Tablet may use:

- collapsible module sidebar;
- full-width content;
- stacked playground panels.

Avoid forcing desktop split panes at narrow widths.

---

# COMPONENT RULES

## 53. Buttons

Use a restrained hierarchy.

### Primary

One dominant action.

Examples:

- Mulai belajar
- Run
- Check answer
- Continue

### Secondary

- Reset
- View hint
- Bookmark

### Tertiary/link

- Further Reading
- Skip
- View reference

Avoid multiple brightly filled buttons in one cluster.

---

## 54. Cards

Cards are allowed, but they are not the default container.

Use cards when an object genuinely needs grouping:

- a project;
- an exercise;
- a downloadable dataset;
- a distinct interactive.

Do not use a card for:

- every paragraph;
- every metric;
- every navigation item;
- every phase;
- every section.

Ask:

> Would spacing and a divider communicate this grouping just as well?

If yes, do not use a card.

---

## 55. Badges

Badges are reserved for compact metadata.

Good:

- Guided
- Intermediate
- Completed
- Optional

Bad:

- Powerful
- Smart
- AI Powered
- Best
- Popular

Do not decorate every heading with a badge.

---

## 56. Progress Indicators

Use:

- thin linear progress;
- `3 / 8 topics`;
- completion check.

Do not use elaborate circular gauges unless there is a clear reason.

---

## 57. Modals

Avoid modals for normal learning flow.

Use only for:

- destructive confirmation;
- short confirmation;
- focused account action.

Hints, feedback, and Further Reading should normally stay inline.

---

# CONTENT DESIGN

## 58. Language

Primary product language: **Bahasa Indonesia**.

Technical terms may remain in English when that is how learners will encounter them professionally.

Examples:

- DataFrame;
- JOIN;
- GROUP BY;
- dashboard;
- calculated field;
- sampling;
- confidence interval.

Do not awkwardly translate established technical vocabulary solely for localization.

Explain the term in Indonesian.

---

## 59. Tone

Use direct, instructional language.

Good:

> Satu baris pada tabel ini mewakili satu item dalam order.

Avoid:

> Yuk, kita jelajahi dunia data yang seru dan penuh insight! 🚀

Also avoid overly academic language when a simpler explanation is accurate.

---

## 60. Marketing Copy

Do not overpromise career outcomes.

Avoid:

- "Become a Data Analyst in 7 days";
- "Guaranteed job-ready";
- "Master data instantly";
- "The only course you'll ever need."

Prefer:

> Pelajari fondasi dan workflow analisis data melalui jalur yang terstruktur dan latihan kontekstual.

---

# ACCESSIBILITY

## 61. Interaction

All important controls need:

- visible labels or accessible names;
- keyboard focus;
- sufficient target size;
- clear selected/disabled states.

Do not rely solely on hover.

---

## 62. Color

Completion, error, or selection must not be conveyed by color alone.

Use:

- icon;
- text;
- shape/border;
- label.

---

## 63. Motion

Motion is optional and restrained.

Allowed:

- subtle menu transition;
- feedback transition;
- diagram state change.

Avoid:

- parallax;
- constant floating objects;
- decorative looping animations;
- page transitions that slow learning.

Respect reduced-motion preferences.

---

# DESIGN SYSTEM IMPLEMENTATION

## 64. Tokens

Define design tokens rather than styling pages independently.

Minimum tokens:

- background;
- surface;
- text-primary;
- text-secondary;
- border;
- accent;
- success;
- warning;
- error;
- spacing scale;
- radius scale;
- typography scale;
- content widths.

Do not define dozens of decorative colors before they are needed.

---

## 65. Core Components

Initial reusable components:

- GlobalHeader
- CurriculumPhase
- ModuleRow
- ProgressBar
- LessonSidebar
- LessonHeader
- ContentSection
- Callout
- CodeBlock
- DataTable
- PracticeShell
- Question
- Feedback
- Hint
- FurtherReadingItem
- DatasetDownload
- ProjectStageNav
- ReferenceApproach
- BookmarkButton

Interactive tools can add specialized components after prototypes.

---

# PAGE PRIORITIES

## 66. Pages to Design First

Before designing every route, establish the system through these representative screens:

### P0

1. Homepage
2. Learn / Data Analyst Path
3. Module detail
4. Standard lesson
5. SQL playground lesson
6. Project workspace

These six screens should establish most of the visual language.

### P1

7. Spreadsheet practice
8. Python/Pandas practice
9. Visualization Playground
10. Progress
11. Explore Skills
12. Authentication

### P2

13. Bookmarks
14. Account/profile
15. specialized interactives
16. search

Do not independently invent a new visual pattern for each page.

---

# IMPLEMENTATION REVIEW CHECKLIST

## 67. Per-Screen Review

Before accepting a screen:

### Hierarchy

- Is the primary task obvious?
- Is the page title clearly dominant?
- Are secondary actions visually secondary?

### Density

- Is whitespace sufficient?
- Are too many things inside cards?
- Can any border/container be removed?

### Authenticity

- Is the screen showing real learning content?
- Are there fake metrics/testimonials/logos?
- Does anything exist only because generic SaaS pages usually contain it?

### Learning

- Does the UI help the learner understand where they are?
- Is feedback actionable?
- Is the next step clear?
- Does interaction support the concept rather than distract?

### Consistency

- Are typography, spacing, radius, and controls using the shared system?
- Does this page introduce an unnecessary new component?

---

## 68. Anti-AI-Slop Review Checklist

Reject or revise the design if several of these are true:

- hero uses gradient text;
- page contains multiple glowing surfaces;
- every feature is a rounded card;
- most cards have an icon in a colored circle;
- arbitrary decorative charts are visible;
- there are several competing accent colors;
- the page uses excessive pills/badges;
- headings use generic marketing copy;
- content could belong to any SaaS product after changing the logo;
- fake social proof is present;
- whitespace is replaced by containers;
- visual novelty is prioritized over reading/practice.

The goal is not to make the UI intentionally plain.

The goal is to make it **specific to BelajarData**.

---

# DESIGN DECISIONS SUMMARY

## 69. Fixed Direction

For V1:

- light, neutral, content-first visual direction;
- one restrained primary accent;
- editorial typography;
- modest radius;
- minimal shadows;
- no decorative gradients/glassmorphism;
- vertical curriculum presentation rather than course-card marketplace;
- lesson-centric desktop workspace;
- real learning artifacts as visual content;
- bounded practice environments;
- progress without gamification;
- Bahasa Indonesia-first product copy;
- responsive reading, desktop-preferred complex tools;
- no fabricated social proof;
- no generic AI-startup visual language.

---

## 70. Relationship to Other Documents

### `curriculum.md`

Defines:

> What should learners learn, in what order, and why?

### `PRD.md`

Defines:

> What must BelajarData provide as a product?

### `design.md`

Defines:

> How should that product feel, look, and behave?

The next implementation documents should define:

> How will we build it, and in what sequence?

Do not let implementation convenience silently change curriculum or product requirements. If a requirement needs to change, update the appropriate source document explicitly.
