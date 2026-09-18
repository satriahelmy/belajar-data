# BelajarData — Architecture Specification

**Product:** BelajarData  
**Version:** V1  
**Status:** Technical architecture baseline  
**References:** `curriculum.md`, `PRD.md`, `design.md`

## 1. Architecture Goal

BelajarData V1 should remain simple enough for conventional PHP/shared-hosting infrastructure while supporting rich browser-based learning. Optimize for maintainability, low operational complexity, inexpensive hosting, content iteration, deterministic exercises, safe learner-code execution, and graceful degradation.

> **Laravel manages the learning product. The browser executes specialized learning tools where practical.**

## 2. Constraints

- Laravel + MySQL on PHP-compatible shared hosting/cPanel.
- No required long-running production Node process.
- No Kubernetes, mandatory Redis, microservices, or server-side arbitrary code execution.
- Frontend assets are compiled before deployment.
- Learner SQL must never execute against the application database.
- Learner Python must never execute through the Laravel server in V1.

## 3. High-Level Architecture

```text
Browser
├── Server-rendered Laravel UI
├── JavaScript enhancements
├── SQL runtime (browser)
├── Spreadsheet engine (browser)
├── Visualization/statistics widgets
└── Optional Pyodide
          │ HTTPS
          ▼
Laravel modular monolith
├── Authentication
├── Curriculum/content delivery
├── Progress & history
├── Bookmarks
├── Assessments
├── Projects
└── Dataset metadata
          │
          ▼
MySQL

Static assets
├── CSV / JSON / XLSX
├── images
└── optional notebooks

External professional tool
└── Tableau / Tableau Public
```

## 4. Architectural Style

Use a **modular Laravel monolith**, with logical boundaries for Learning, Learner, Projects, and Datasets. They are not separately deployed services.

Laravel owns routing, auth, repository content loading/rendering, progress, bookmarks, attempts, projects, recent-learning state, and authorization.

Browser JavaScript owns specialized interaction such as SQL execution, spreadsheet calculation, chart rendering, simulations, Metric Tree Builder, Communication Builder, and optional Python/Pandas execution. Persist meaningful learning state, not every keystroke.

## 5. Content Architecture

Use a **repository-first content architecture** for V1. Curriculum and authored learning content are version-controlled files; MySQL primarily stores learner/application state.

The content layer is deliberately split by responsibility:

```text
Markdown
→ instructional narrative

JSON / structured config
→ curriculum metadata, exercises, validators, interactive configuration

CSV / XLSX / JSON
→ learning datasets

JavaScript
→ reusable playground and interactive engines

MySQL
→ users and learner state
```

Do not put the full curriculum into MySQL merely to make it "dynamic." V1 has no requirement for a non-technical CMS, and database-backed authoring would add editor/admin complexity without improving the learning experience.

**Version-controlled Markdown:** long-form lesson and challenge/project narrative.

```text
content/data-analyst/
├── 01-thinking-with-data/
├── 02-spreadsheet/
├── 03-sql/
└── ...
```

Markdown supports a deliberately limited set: headings, prose, lists, tables, code, links, images, and trusted custom learning blocks. Do not allow uncontrolled authored HTML/scripts.

Conceptual custom block:

```text
:::callout type="common-mistake"
A JOIN can change the grain of your result.
:::

:::practice id="sql-03-05-01"
:::
```

Rendering pipeline:

```text
Markdown → parse → sanitize/whitelist → resolve learning blocks → HTML → cache
```

Every curriculum object uses a stable machine key independent of its display title.

## 6. Curriculum & Content File Model

Keep hierarchy intentionally simple:

```text
Phase → Module → Topic → Content/Practice
```

Do not introduce Topic → Lesson → Unit layers unless real content later requires them.

Recommended repository structure:

```text
content/
└── data-analyst/
    ├── path.json
    ├── 01-thinking-with-data/
    │   ├── module.json
    │   ├── 01-analyst-role/
    │   │   ├── lesson.md
    │   │   └── exercises.json
    │   └── ...
    ├── 03-sql/
    │   ├── module.json
    │   ├── 05-joins/
    │   │   ├── lesson.md
    │   │   └── exercises.json
    │   └── challenge/
    │       ├── challenge.md
    │       └── exercises.json
    └── ...

datasets/
└── nusamart/
    └── v1/
        ├── manifest.json
        ├── orders.csv
        ├── order_items.csv
        ├── products.csv
        └── customers.csv
```

### `path.json`

Defines stable curriculum structure and phase ordering.

### `module.json`

Defines module metadata such as:

```text
key
number
title
description
estimated_minutes
position
recommended_knowledge
publication state
topic order
```

### `lesson.md`

Contains the instructional narrative:

- explanations;
- examples;
- tables;
- code blocks;
- callouts;
- references to registered practice/interactive components.

It must not contain arbitrary executable JavaScript.

### `exercises.json`

Contains structured exercise configuration:

- exercise key/type;
- prompt/instructions where appropriate;
- dataset reference;
- expected result/validator configuration;
- hints;
- feedback;
- interactive component reference.

Do not place large datasets inside exercise config.

### Further Reading

Further Reading may live in topic metadata or `exercises.json`/a small topic metadata file. Keep it version-controlled with the lesson rather than in MySQL for V1.

### Publication

Publication state is repository/config-driven for V1. A content change becomes live through the normal application deployment process.

This is intentional: V1 does not require an admin CMS.

### Stable keys

Every phase, module, topic, practice, challenge, project, and dataset has a stable machine key. Learner state in MySQL references these keys rather than relying on content database row IDs.

This allows authored content to evolve without requiring a database record for every content object.

## 8. Learner/Application Database

MySQL primarily stores state created by application usage.

Suggested tables:

### `users`

Laravel authentication fields.

### `user_topic_progress`

```text
id
user_id
topic_key
status
started_at_nullable
completed_at_nullable
last_activity_at
timestamps
```

Use a unique constraint on `(user_id, topic_key)`.

Suggested progress states: `in_progress`, `completed`; absence means not started.

Module and phase progress are derived from topic progress plus the repository curriculum definition.

### `practice_attempts`

```text
id
user_id
practice_key
status
answer_json
score_nullable
completed_at_nullable
timestamps
```

### `challenge_attempts`

```text
id
user_id
challenge_key
status
answer_json
completed_at_nullable
timestamps
```

### `bookmarks`

```text
id
user_id
content_type
content_key
timestamps
```

Supported bookmark types are explicitly constrained in application logic.

Do not mirror full lesson/module/topic records into MySQL unless a later CMS/search requirement justifies it.

## 8. Projects Data Model

### `projects`
`id, key, number, title, focus, difficulty, description, estimated_minutes, position, is_published, timestamps`

### `project_stages`
`id, project_id, key, number, title, content_path, position, timestamps`

Canonical stages:
`brief → understand → plan → investigate → validate → communicate`

### `user_project_progress`
`id, user_id, project_id, status, current_stage_id_nullable, started_at_nullable, completed_at_nullable, last_activity_at, timestamps`

Use `user_project_stage_progress` only if stage-level answer persistence is required. Prefer that over one giant JSON document.

## 9. Guest Progress

Guest progress lives in `localStorage` and contains only lightweight, non-sensitive state.

Create a persistence abstraction rather than scattering direct localStorage calls:

```text
ProgressStore
├── getTopicProgress()
├── markStarted()
├── markCompleted()
├── getRecent()
└── setRecent()

Implementations:
GuestProgressStore
AuthenticatedProgressStore
```

On login/register, merge guest and server progress by stable content key. Completion wins over in-progress; never downgrade server completion. Delete guest state only after a successful merge.

## 10. Authentication

Use standard Laravel session-based web authentication, CSRF protection, password hashing, registration/login/logout/password reset, and optional email verification. Do not introduce JWT for the first-party web application.

## 11. SQL Playground

Use an isolated browser-side SQL engine.

```text
Versioned dataset
→ browser SQL runtime
→ learner query
→ result table
→ local result validator
→ completion state to Laravel
```

Prototype a SQLite-compatible WASM engine and/or DuckDB-Wasm, then choose the lightest option that supports the curriculum. Do not choose an engine merely for feature breadth.

Teach broadly portable analytical SQL and clearly document runtime-specific differences.

Validate **results, not query strings**. Configuration can define columns, ordering requirements, and numeric tolerance. Unordered answers compare normalized row sets.

Practical limits:
- execute in a Web Worker where feasible;
- cap rendered rows;
- support runtime reset;
- prevent remote database connectivity;
- terminate runaway execution.

## 12. Spreadsheet Playground

Implement only curriculum-required features: predefined grid/data, formula entry, references/ranges, selected functions, basic calculations, sorting/filtering where required, and expected-result validation.

Evaluate a maintained browser formula/grid library before writing a custom engine. Selection criteria: license, bundle size, required formulas, maintenance, deterministic behavior, and no backend dependency.

Grade requested outputs/cells rather than formula text so multiple valid formulas can pass.

## 13. Python & Pandas

Browser Python is a **prototype gate**, not a guaranteed architectural dependency.

Prototype Pyodide against actual Module 04 needs:
- Python import;
- Pandas;
- CSV loading;
- filtering;
- transformations;
- groupby;
- merge;
- dates;
- dataframe output;
- simple `.plot()` only if practical.

Proceed only if initial load, memory, errors, device performance, and deployment are acceptable.

Fallback if not:

```text
Lesson
+ bounded code/output exercises
+ downloadable notebook
```

Do not add a Python execution server merely to preserve an ideal UX.

If Pyodide is used, lazy-load it, preferably execute in a Web Worker, cap output, provide reset, and keep datasets predefined.

## 14. Visualization

Use one established browser charting library for V1. It must cover the chart types required by the curriculum and be responsive, stable, and controllable.

Visualization Playground should be config-driven:

```json
{
  "dataset": "nusamart-sales-v1",
  "allowed_metrics": ["revenue", "profit"],
  "allowed_dimensions": ["category", "region"],
  "allowed_charts": ["bar", "line"]
}
```

No arbitrary upload, relationship model, calculated-field engine, or dashboard builder.

## 15. Special Interactives

Use a common practice contract conceptually:

```text
initialize(config)
getAnswer()
validate()
reset()
emitProgress()
```

### JOIN Row Multiplication
Predefined source/result tables, join key, row count, and grain warning. No general graph/editor needed.

### Sampling & Uncertainty
Browser-only simulation. Deterministic seeds where reproducible assessment is required.

### Metric Tree Builder
Predefined nodes and limited valid relationships. Do not build a generic diagram editor.

### Communication Builder
Structured fields for headline, evidence, known, unknown, and next step. Reference/checklist after submission. No AI prose scoring.

## 16. Assessment Engine

Keep reusable question types small:

`multiple_choice, multi_select, numeric, text_self_assessment, result_based`

Assessment should be configuration-driven where practical. Browser-visible answer keys are acceptable because V1 is not a high-stakes certification product.

Open-ended work uses reference approaches and guided self-assessment rather than automated grading.

## 17. Rendering & Routes

Prefer server-rendered Laravel pages with JavaScript enhancement. Do not build a SPA without a concrete need.

Conceptual routes:

```text
/
 /learn
 /learn/{module}
 /learn/{module}/{topic}
 /skills
 /skills/{skill}
 /projects
 /projects/{project}
 /projects/{project}/{stage}
 /progress
 /bookmarks
 /login
 /register
```

Use readable stable slugs, not database IDs.

Authenticated state endpoints may cover topic start/completion, attempts, bookmarks, and project progress. Progress operations must be idempotent with appropriate unique constraints.

## 18. Dataset Architecture

Small fictional/public-safe datasets are static versioned assets where possible.

```text
public/datasets/nusamart/v1/
├── orders.csv
├── order_items.csv
├── products.csv
└── customers.csv
```

Maintain a dataset manifest/data dictionary containing keys, grain, relationships, schema, metric definitions, version, and curriculum usage.

NusaMart must have one canonical definition. Modules may use subsets, but must not independently regenerate contradictory NusaMart worlds.

## 19. Performance

Do not load specialized runtimes globally.

```text
Normal lesson → no SQL/Python/chart runtime
SQL practice → lazy-load SQL runtime
Python practice → lazy-load Pyodide when opened
Visualization → lazy-load charting component
```

Cache parsed Markdown and curriculum navigation where useful. File/database cache is sufficient for V1; do not require distributed cache infrastructure.

## 20. Security

Use standard Laravel controls: CSRF, escaping, password hashing, session security, authorization, validation, and rate limiting where appropriate.

Sanitize Markdown and only mount registered trusted interactive components.

**Never execute arbitrary learner SQL or Python on the Laravel server.** A future server sandbox would require a separate architecture/security review.

## 21. Analytics & Logging

Log failures useful for debugging: missing content, malformed practice config, dataset failures, and state-persistence failures. Do not log secrets or unnecessary learner answer content.

Keep product analytics vocabulary small:
`lesson_started, lesson_completed, practice_started, practice_completed, challenge_started, challenge_completed, project_started, project_completed, account_created, bookmark_added`.

Do not instrument every scroll or click.

## 22. Testing

### Backend
Prioritize curriculum routing/publication, authentication, progress, guest merge, bookmarks, projects, and idempotent state updates.

### Content validation
Create a command such as:

```text
php artisan content:validate
```

It should catch missing content files, missing practices/datasets, duplicate keys, invalid ordering, and broken required references.

### Exercise fixtures
Every deterministic exercise should have reproducible expected-output tests.

### Critical browser flows
Test guest progress persistence, account persistence, guest-to-account merge, SQL run/check/persist, and project resume.

## 23. Deployment

Production should require only PHP/Laravel, MySQL, and compiled static frontend assets.

Recommended flow:

```text
Development / CI
→ composer install --no-dev
→ npm install
→ npm run build
→ tests
→ content validation
→ deploy
→ migrations
→ cache rebuild
→ smoke test
```

Production does not require a Node dev server.

Keep secrets in `.env`; never commit or overwrite production `.env`.

Markdown content ships version-controlled with application releases.

## 24. Technical Spikes Before Full Implementation

Run four small spikes before building the curriculum around assumptions.

### Spike A — SQL
Prove: dataset load → browser query → result → validation.

### Spike B — Spreadsheet
Prove: grid → formula → calculation → target validation.

### Spike C — Python/Pandas
Prove: lazy runtime → Pandas → CSV → groupby → dataframe output.

### Spike D — Content Blocks
Prove: Markdown → custom practice directive → rendered Laravel lesson → mounted JS component.

A failed spike should simplify the implementation, not silently expand infrastructure.

## 25. Recommended Build Sequence

```text
M0 Foundation
Laravel shell
authentication
design tokens/components
content loader
curriculum schema

M1 Learning Core
Learn page
module page
lesson page
guest/account progress
bookmarks

M2 Assessment Core
questions
feedback
attempt persistence
self-assessment

M3 SQL
SQL Playground
SQL lesson integration

M4 Spreadsheet
bounded Spreadsheet Playground

M5 Python/Pandas
implement approved prototype/fallback

M6 Visualization & Interactives
Visualization Playground
JOIN
Sampling
Metric Tree
Communication Builder

M7 Tableau
dataset downloads
guided tasks
checkpoints
self-assessment

M8 Projects
project workspace
two launch-ready projects

M9 Launch Hardening
content validation
responsive/accessibility
analytics
performance
SEO
QA
```

Concrete tickets belong in `task.md`.

## 26. Do Not Add Prematurely

Unless a proven V1 requirement demands them, do not introduce:

- Redis;
- queues;
- websockets;
- microservices;
- Elasticsearch;
- vector database;
- LLM infrastructure;
- server-side Python service;
- Docker requirement for learners;
- GraphQL;
- event sourcing;
- complex/headless CMS;
- separate frontend repository;
- mobile app;
- real-time collaboration.

## 27. Future Extension Boundaries

Future Explore Skills can map canonical topics into SQL, Python, Tableau, Power BI, Advanced Statistics, or AI-for-Analysts indexes without duplicating content.

Future career paths may reuse foundational topics. Leave room for mapping tables later, but do not build a generic curriculum-graph engine now.

If formal certificates/high-stakes assessment are added later, browser-side answer visibility and self-assessment are insufficient; that requires a separate assessment/security design.

## 28. Content Storage Decision

For V1, the source of truth is:

```text
Repository
├── Markdown          instructional content
├── JSON/config       curriculum + exercises + validators
├── datasets          versioned learning data
└── JavaScript        reusable interactive engines

MySQL
├── users
├── progress
├── attempts
├── bookmarks
└── project progress
```

Rationale:

- approximately dozens of lessons remain small as text files;
- Git/Codex can review and modify the complete curriculum directly;
- interactive learning is supported through references to reusable components;
- datasets and validators stay separated from prose;
- no CMS/admin editor is required for launch;
- content can later be imported/migrated to a CMS if a real operational need appears.

This is a deliberate V1 decision, not a claim that database-backed content is universally inferior.

## 28. Fixed V1 Architecture

```text
Architecture        Modular Laravel monolith
Backend             Laravel
Database            MySQL
Rendering           Server-rendered + JS enhancement
Content             Markdown + JSON/config in repository
Authentication      Laravel session auth
Guest progress      localStorage
Account progress    MySQL
SQL execution       Browser-side isolated runtime
Spreadsheet         Bounded browser component
Python/Pandas       Browser prototype first; fallback allowed
Visualization       One browser charting library
Tableau             External professional tool
Projects            Repository content + Laravel progress workspace
Deployment          Shared-hosting friendly
```

## 29. Core Boundary

Laravel is not a SQL sandbox server, Python execution server, BI engine, or notebook runtime.

Browser tools are not Excel, Tableau, Colab, or a production database IDE.

The content system is not a full CMS or arbitrary page builder.

Each layer should do only enough to support the learning product.

## 30. Relationship to Product Documents

- `curriculum.md` — what learners learn and why.
- `PRD.md` — what product capabilities must exist.
- `design.md` — how the product should look and behave.
- `architecture.md` — how the technical system is structured.
- future `task.md` — implementation sequence and concrete work packages.

Architecture must not silently override product decisions. If prototype evidence forces a compromise—especially Python/Pandas execution—record the compromise explicitly in the source documents.
