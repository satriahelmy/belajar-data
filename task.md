# BelajarData V1 — Implementation Plan

Status: M0A, technical spikes A–E, and M0C are implemented. M1 has not started.

This plan is derived from the current source documents in `docs/`, using the requested hierarchy:

1. `docs/curriculum.md` — what learners learn, in what order, and why;
2. `docs/PRD.md` — product capabilities and V1 scope;
3. `docs/design.md` — visual and interaction direction;
4. `docs/architecture.md` — technical structure and boundaries.

The plan is intentionally implementation-ready without turning content authoring into a CMS project or generating the full lesson library up front.

## 1. Current repository baseline

### Repository inspection

- [x] Read `docs/curriculum.md` completely.
- [x] Read `docs/PRD.md` completely.
- [x] Read `docs/design.md` completely.
- [x] Read `docs/architecture.md` completely.
- [x] Inspect the working tree and tracked files.

Current state:

- The repository now contains the Laravel 12 application scaffold and the M0A/M0C foundation described below.
- The application includes `composer.json`, `package.json`, `artisan`, `app/`, `routes/`, `resources/`, `database/`, `public/`, tests, repository content, and versioned dataset fixtures.
- The current source documents are in `docs/` and are untracked in the current working tree.
- The previous root-level `PRD.md`, `architecture.md`, `curriculum.md`, and `design.md` are tracked as deleted. Their contents differ from the current `docs/` copies.
- The root-document deletions and the untracked `docs/` directory are existing user/worktree changes. They must not be reset or restored implicitly.

### Baseline conclusion

This remains a greenfield rebuild. The current application foundation is intentionally small; the current `docs/` files are the planning baseline for the remaining milestones, subject to the resolved canonical document location below.

### Local development environment snapshot

Checked on 2026-09-18:

- PHP 8.2.12 (`C:\xampp\php\php.exe`).
- Composer 2.9.2.
- Node.js 24.11.1.
- npm 11.6.2.
- PHP extensions required by the initial Laravel baseline are available, including `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `tokenizer`, `xml`, and `ctype`.
- A MySQL-compatible server is listening on `127.0.0.1:3306`.
- MySQL server version reported through PDO: 8.0.45.
- Local development database `belajar_data_v2` exists and is accessible with the supplied `root` credentials.
- `belajar_data_v2` currently contains 0 tables.
- The Laravel application is present; M0C verification uses its repository/content and local database boundaries.
- The MySQL CLI is not on PATH, but PHP PDO connectivity works. Database checks and migrations can use Laravel/PDO or the explicit XAMPP client path if it is added later.

Local development configuration used for M0:

```text
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=belajar_data_v2
DB_USERNAME=root
DB_PASSWORD=<local credential supplied by the user; keep only in .env>
```

Never commit the local password or replace a production `.env` during deployment.

## 2. Product and architecture strategy

BelajarData V1 should be built as a repository-first, server-rendered Laravel modular monolith for conventional PHP/MySQL shared hosting.

The main boundary is:

```text
Laravel
  routing, authentication, content delivery, progress, attempts,
  bookmarks, projects, authorization, metadata, persistence

Browser
  SQL, bounded spreadsheet work, visualizations, simulations,
  registered interactive components, optional Python/Pandas runtime

Repository
  Markdown, JSON/config, versioned datasets, trusted JavaScript engines

MySQL
  users and learner/application state; never learner SQL/Python execution
```

Guiding constraints:

- Server-render ordinary curriculum pages and enhance them with JavaScript.
- Keep curriculum and authored content version-controlled rather than placing it in MySQL.
- Keep every curriculum object addressable by a stable machine key.
- Treat prerequisites as guidance, never as access locks in V1.
- Validate exercise outputs/results rather than requiring one exact query or formula.
- Keep browser runtimes lazy-loaded and bounded.
- Use real learning artifacts—tables, formulas, SQL, charts, metric trees, and briefs—as the visual language.
- Keep the visual system editorial, calm, content-first, and restrained; do not drift toward generic SaaS dashboards.
- Do not introduce Redis, queues, microservices, server-side Python, a CMS, a SPA, arbitrary uploads, or other infrastructure without an explicit source-document change.

## 3. Assumptions, dependencies, and risks

| ID | Area | Working assumption / dependency | Risk | Planned response |
| --- | --- | --- | --- | --- |
| R-01 | Hosting | Production supports a conventional PHP Laravel deployment, MySQL, writable cache/storage, and compiled static assets. | Unknown PHP extensions, PHP/Laravel compatibility, or cPanel restrictions could invalidate the deployment shape. | Confirm the target hosting matrix in M0; keep production free of Node dev servers and long-running services. |
| R-02 | Browser runtimes | SQL, spreadsheet, charting, and possibly Python execute in the browser. | Bundle size, browser compatibility, memory, mobile performance, and worker behavior may make a candidate unsuitable. | Run the decision-gated spikes before curriculum-dependent implementation. |
| R-03 | SQL dialect | Lessons teach broadly portable analytical SQL, with a bounded runtime-specific subset. | SQLite-compatible runtimes and DuckDB-Wasm differ in date functions, window functions, and error behavior. | Test actual curriculum queries and publish runtime notes; choose the smallest viable engine. |
| R-04 | Spreadsheet scope | Only the functions and interactions required by Module 02 are supported. | Lookup, conditional aggregation, pivot-like summaries, sorting, and filtering can expand into an Excel clone. | Define an explicit feature allowlist and grade output cells/results, not formulas. |
| R-05 | Python/Pandas | Browser Python is optional until a prototype proves it is practical. | Pyodide may load slowly or exceed memory on low-end/mobile devices. | Treat Pyodide as a gate; maintain a bounded fallback with downloadable notebooks and output/code exercises. |
| R-06 | Markdown security | Authored Markdown is trusted repository content but still passes through a whitelist/sanitization boundary. | Raw HTML, scripts, unsafe links, or unregistered blocks could create XSS or content integrity problems. | Allow only the documented Markdown subset and registered custom blocks; validate and test unsafe input. |
| R-07 | Content scale | V1 eventually needs complete Modules 01–12 and at least two polished projects. | A working renderer without content completeness does not meet the PRD launch bar. | Prove the content contract on a small representative slice, then author and validate content in batches. |
| R-08 | NusaMart | NusaMart has one canonical versioned definition reused across modules. | Independently recreated tables can silently change grain, metric definitions, or expected results. | Maintain a manifest/data dictionary, version all datasets, and reference subsets rather than regenerate worlds. |
| R-09 | External Tableau | Tableau is the real external professional tool, not a simulated browser product. | Tableau UI/version changes and Tableau Public privacy mistakes can break instructions or expose unsafe data. | Use fictional/public-safe datasets, test guided checkpoints, and make publishing optional with an explicit privacy warning. |
| R-10 | Guest/account state | Guest progress is lightweight localStorage; authenticated progress is MySQL. | Merge conflicts can downgrade completion or delete guest work prematurely. | Merge by stable key, let completion win, make operations idempotent, and clear guest state only after successful persistence. |
| R-11 | Assessment | V1 uses deterministic checks and guided self-assessment, not manual or AI grading. | Open-ended answers cannot be reliably scored without expanding scope. | Store the answer when useful, show reference/checklist, and let the learner mark completion. |
| R-12 | Design | Editorial layout, restrained borders, typography, whitespace, and real artifacts are the primary visual system. | Generic card grids, gradients, badges, and decorative charts can creep in during rapid UI work. | Use the representative P0 screens and the design review checklist as acceptance gates. |
| R-13 | Analytics | Product analytics remain a small event vocabulary. | Over-instrumentation can create privacy, performance, and implementation cost without learning value. | Track only the events defined by architecture/PRD; never log secrets or unnecessary answer content. |
| R-14 | Email/auth operations | Password reset and optional verification require a reliable mail configuration. | Shared hosting mail limitations may block a seemingly complete auth flow. | Confirm mail capabilities early; keep core learning available without login and document any launch limitation. |

## 4. Technical spikes and decision gates

These are gates, not optional technology experiments. No SQL, spreadsheet, Python, charting, or Markdown library is pre-approved. A curriculum feature must not become dependent on a runtime before its gate is recorded. A failed spike must simplify the product or use the documented fallback; it must not silently create server-side infrastructure.

### Gate A — Browser-side SQL execution

Objective: prove that a learner can load a versioned NusaMart fixture, run an analytical query in the browser, inspect a bounded result, and receive deterministic result validation without touching application MySQL.

Candidate approaches to evaluate:

- SQLite-compatible WebAssembly runtime;
- DuckDB-Wasm;
- a simpler SQLite-compatible candidate if it covers the required query subset with less operational and bundle cost.

Prototype tasks:

- [x] Load a small representative relational fixture from a versioned dataset manifest.
- [x] Execute the actual Module 03 patterns: `SELECT`, column selection, `WHERE`, `ORDER BY`, `LIMIT`, aggregation, `COUNT`, `COUNT DISTINCT`, `SUM`, `AVG`, `GROUP BY`, joins, `CASE`, CTEs, dates, window functions, and `LAG`.
- [x] Run in a Web Worker and measure initial load, query latency, available memory telemetry, and reset behavior.
- [x] Render bounded result rows and useful syntax/runtime errors.
- [x] Prove normalized unordered row-set comparison, ordered result comparison, column checks, NULL handling, and numeric tolerance.
- [x] Prove that remote database connectivity is unavailable and that no query is sent to Laravel/MySQL.
- [x] Test a runaway/large-result case, worker termination/reset, and re-run after an error.
- [x] Record browser support and the practical desktop/mobile recommendation.

Acceptance criteria:

- A representative Module 03 query family executes successfully in the browser.
- Expected results are stable across reload/reset and can be validated without comparing query strings.
- The runtime cannot access the application database or an arbitrary remote database.
- Result rendering is bounded and runaway execution can be terminated or reset.
- Load/performance measurements are acceptable for the intended hosting/device baseline, or a smaller supported query subset is explicitly documented.
- The chosen runtime, supported SQL subset, known dialect differences, asset delivery approach, and fallback are recorded before M3.

Decision output:

- [x] Record selected engine and version/asset strategy.
- [x] Record unsupported SQL features and curriculum wording implications.
- [x] Record the result-validator contract.
- [x] Record the fallback if the candidate fails.

Gate A status: PASSED for the representative technical spike only.

Selected approach: `sql.js` 1.14.2 (SQLite compiled to WebAssembly), executed in a dedicated Web Worker against a predefined NusaMart fixture. Evidence, limitations, and the DuckDB-Wasm comparison are recorded in `docs/decisions/gate-a-sql.md`.

### Gate B — Bounded spreadsheet execution

Objective: prove the smallest spreadsheet experience that can teach Module 02 without becoming a general spreadsheet clone.

Candidate approaches to evaluate:

- a maintained formula engine such as HyperFormula paired with a deliberately small native grid/table UI;
- a maintained browser grid/formula library if its license, size, required functions, and behavior fit the project;
- a custom bounded evaluator only if the candidate libraries cannot meet the narrow allowlist at acceptable cost.

Prototype tasks:

- [x] Load a predefined NusaMart worksheet and a lookup/master worksheet.
- [x] Prove the required formula subset: references/ranges, arithmetic, `SUM`, `COUNT`, `AVERAGE`, `IF`, `COUNTIF(S)`, `SUMIF(S)`, and the selected lookup behavior (`VLOOKUP`) needed by the curriculum.
- [x] Prove sorting/filtering where the lesson requires it.
- [x] Define how a bounded Pivot Table-like summary is represented, or decide that the summary is a preconfigured result interaction rather than a general pivot engine.
- [x] Validate target cells/results independent of formula spelling.
- [x] Test malformed formulas, missing lookup keys, blanks, duplicate keys, numeric formats, reset, and deterministic recalculation.
- [x] Check keyboard access, readable grid behavior, desktop recommendation, and small-screen fallback.

Acceptance criteria:

- [x] The selected approach supports every feature required by the initial Module 02 exercise slice.
- [x] Calculations are deterministic and expected outputs can be validated without requiring one exact formula.
- [x] The feature allowlist is explicit and excludes arbitrary workbook authoring, macros, external links, full ribbon behavior, and arbitrary upload.
- [x] License, maintenance, bundle size, accessibility, and backend independence are acceptable.
- [x] The chosen approach and the non-goals are recorded before M4.

Decision output:

- [x] Record formula/grid approach and license decision.
- [x] Record the supported function/interaction allowlist.
- [x] Record how pivot-style tasks are bounded.
- [x] Record the fallback interaction if a full grid is not justified.

Gate B status: PASSED for the representative technical spike only.

Selected approach: native HTML tables plus a bounded custom browser evaluator with exact `VLOOKUP(..., FALSE)`, configured category summary, output validation, and no new spreadsheet dependency. Evidence and limitations are recorded in `docs/decisions/gate-b-spreadsheet.md`.

### Gate C — Python/Pandas browser feasibility

Objective: determine whether the desired Module 04 experience can run safely and acceptably in the browser without a Python service.

Candidate approaches to evaluate:

- Pyodide, lazy-loaded and preferably isolated in a Web Worker;
- a bounded code/output exercise experience with predefined results and downloadable notebooks if browser Python is too heavy.

Prototype tasks:

- [x] Measure first-load and warm-load time for the actual CDN deployment asset path.
- [x] Import Python and Pandas.
- [x] Load a predefined CSV.
- [x] Prove `head`, `shape`, column selection, filtering, sorting, calculated columns, `groupby`, `merge`, date parsing/extraction, and dataframe output.
- [x] Test error capture, reset, output truncation, and interruption/worker recovery.
- [ ] Test memory and responsiveness on the agreed desktop and representative low-memory/mobile devices.
- [x] Test the smallest plotting requirement only if the curriculum truly needs it; do not make plotting a reason to add a heavier runtime.
- [x] Verify that arbitrary learner Python never reaches Laravel and that datasets remain predefined.

Acceptance criteria:

- The Module 04 representative tasks run correctly, with usable errors and bounded output, within the agreed performance budget.
- The runtime can be lazy-loaded and reset without affecting ordinary lesson pages.
- The learner experience is honest about desktop support and does not pretend to be a notebook/Colab clone.
- If the gate fails, the fallback still teaches the intended analytical reasoning through bounded code/output tasks and downloadable notebooks.
- The decision and its effect on later Cleaning, EDA, Statistics, and Visualization content are recorded before M5.

Decision output:

- [x] Record fallback approval and the status of Pyodide as an optional desktop-only experiment.
- [x] Record supported Python/Pandas subset and resource limits.
- [x] Record device/performance guidance and asset-loading strategy.
- [x] Record which later exercises are interactive, bounded, or downloadable.

Gate C status: PASSED for the bounded fallback and representative browser feasibility spike only. Pyodide 314.0.7 successfully ran the Module 04 workflow in a Web Worker, but its initialization, memory, and asset cost are not approved as a V1 curriculum dependency. Evidence and curriculum implications are recorded in `docs/decisions/gate-c-python-pandas.md`.

### Gate D — Markdown plus structured interactive blocks

Objective: prove the repository-first content pipeline from Markdown to safe server-rendered HTML with registered interactive mounts.

Candidate approaches to evaluate:

- a maintained PHP Markdown parser such as League CommonMark, with an explicit safe Markdown configuration;
- the selected parser plus a sanitizer/allowlist layer appropriate for Laravel;
- a small custom directive resolver for the documented `:::callout` and `:::practice` forms, not a general page-builder language.

Prototype tasks:

- [x] Parse headings, prose, lists, tables, code blocks, links, images, callouts, and practice directives.
- [x] Resolve a stable practice key to a registered interactive component/configuration.
- [x] Reject or sanitize raw HTML, scripts, unregistered blocks, missing practice keys, unsafe links, and invalid attributes.
- [x] Render a representative lesson on the server and mount one JavaScript practice component progressively.
- [x] Cache parsed/rendered content with a source-hash key so source/config changes invalidate the previous entry.
- [x] Extract heading structure for the lesson outline and future accessible navigation.
- [x] Run the representative content through the spike-level `content:validate` command so broken references fail before deployment.

Acceptance criteria:

- A representative lesson with a callout, table, code block, and practice mount renders correctly without arbitrary authored JavaScript.
- Missing or malformed blocks produce actionable validation errors, not runtime-only failures.
- Sanitization and escaping prevent authored content from becoming an XSS path.
- The parser, directive grammar, registered component contract, cache behavior, and authoring conventions are recorded before M1/M2 content integration.

Decision output:

- [x] Record parser/sanitizer choice and allowed Markdown subset.
- [x] Record directive grammar and registered component lookup contract.
- [x] Record cache/invalidation strategy.
- [x] Record authoring examples and validation failure format.
- [x] Add `docs/decisions/gate-d-markdown-blocks.md` with the spike evidence and decision.

Gate D status: PASSED for the representative spike.

Selected approach: `league/commonmark` 2.10.1 using the GitHub-Flavored Markdown converter with `html_input=strip` and `allow_unsafe_links=false`, plus a small allowlisted PHP directive parser/resolver and trusted server-rendered block templates. No arbitrary authored HTML or JavaScript is allowed.

The selected approach is approved only for the demonstrated Gate D prototype. The final content system still requires later hardening and broader content fixtures.

### Gate E — Visualization library and bounded charting

Objective: select one browser charting library only if it can support the curriculum's analytical visuals without turning into a dashboard builder.

Candidate approaches to evaluate:

- Chart.js;
- Apache ECharts;
- Observable Plot or another maintained candidate if it offers the required chart types and accessibility with lower complexity.

Prototype tasks:

- [x] Render config-driven bar, line, scatter, histogram, and a bounded IQR/median distribution representation.
- [x] Prove responsive sizing, resize cleanup, reset, deterministic data, and accessible labels/adjacent tabular explanation.
- [x] Prove finite controls for metric, dimension, chart, sort, and highlight rather than arbitrary field/calculation authoring.
- [x] Measure lazy-load cost and behavior on normal lessons versus visualization lessons.
- [x] Check license, maintenance, SSR-safe integration, and shared-hosting asset delivery.

Acceptance criteria:

- The candidate covers the required Module 08/09 visual tasks with a small, config-driven API.
- Charts are explanatory learning objects, not decorative dashboards, and have a non-visual explanation/table where practical.
- No arbitrary uploads, relationship modeling, calculated-field engine, or full dashboard builder is required.
- The library and the bounded configuration contract are recorded before M6.

Decision output:

- [x] Record selected chart library and version/asset strategy.
- [x] Record supported chart types and accessibility fallback.
- [x] Record the config schema and lazy-load boundary.

Gate E status: PASSED for the representative bounded visualization spike only. Chart.js 4.5.1 is selected for the demonstrated V1 adapter; histogram and distribution use bounded derived representations, while the final Visualization Playground remains future M6 work. Evidence and limitations are recorded in `docs/decisions/gate-e-visualization.md`.

### Spike completion rule

- [x] Store a short decision record for Gates A–E, including evidence, chosen approach, rejected candidates, performance observations, licensing notes, and curriculum impact.
- [ ] For each gate, choose the simplest candidate that satisfies the documented acceptance criteria, architecture constraints, curriculum requirements, browser performance requirements, licensing requirements, and shared-hosting deployment model.
- [ ] Record the selected candidate and rationale in that gate's decision record after the spike.
- [ ] Treat every named library in the candidate lists as an evaluation candidate only; do not pre-approve it by name.
- [ ] If a spike fails, update the relevant open decision and use the simplest documented fallback.

## 5. Content architecture and authoring plan

### Repository structure

Use the architecture's simple hierarchy and keep authored content separate from application state:

```text
content/
└── data-analyst/
    ├── path.json
    ├── 01-thinking-with-data/
    │   ├── module.json
    │   ├── 01-analyst-role/
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

Planned conventions:

- [ ] Use stable lowercase machine keys and readable slugs independent of display titles.
- [ ] Keep the hierarchy `phase → module → topic → content/practice`; do not introduce lesson/unit layers without demonstrated content pressure.
- [ ] Use `path.json` for phase/module ordering and canonical path metadata.
- [ ] Use `module.json` for module metadata, topics, estimated time, recommended knowledge, publication state, and skill tags.
- [ ] Use `lesson.md` for narrative, examples, tables, code, callouts, and references to registered practice components.
- [ ] Use `exercises.json` for structured exercise keys, types, prompts, datasets, validators, hints, feedback, and interactive configuration.
- [ ] Keep large datasets out of exercise configuration.
- [ ] Keep Further Reading version-controlled with the relevant topic rather than in MySQL.
- [ ] Keep projects in repository content/config while storing only learner progress and responses in MySQL.

### Exercise contract

Start with a deliberately small reusable type set:

```text
multiple_choice
multi_select
numeric
text_self_assessment
result_based
```

Every exercise should define, as applicable:

- stable key and type;
- prompt/instructions;
- estimated time;
- dataset or interactive reference;
- input/output shape;
- validator and tolerance/ordering rules;
- hints and progressive feedback;
- reference answer/checklist for open-ended work;
- completion behavior and persistence policy.

Validation rules:

- [ ] Keep answer keys browser-visible where necessary; V1 is not high-stakes certification.
- [ ] Validate normalized outputs/results rather than exact SQL strings or formula strings.
- [ ] Make numeric tolerance, null handling, ordering, and column requirements explicit.
- [ ] Treat text self-assessment as learner submission plus reference/checklist, never AI grading.
- [ ] Make every deterministic exercise fixture-testable.

### Dataset manifest and versioning

Each dataset manifest should define:

- stable dataset key and version;
- file/table list and asset paths;
- schema and data types;
- grain for every table;
- primary/unique and foreign-key relationships;
- metric definitions and inclusion/exclusion rules;
- date coverage and known data-quality issues;
- curriculum/module/topic/project usage;
- expected row/unique-entity counts where useful;
- safe/public-fictional status;
- version or content hash sufficient to reproduce exercise results.

NusaMart rules:

- [ ] Create one canonical NusaMart data dictionary.
- [ ] Reuse canonical tables or documented subsets across Spreadsheet, SQL, Pandas, Visualization, Tableau, and Projects.
- [ ] Do not create contradictory module-specific NusaMart definitions.
- [ ] Version datasets whenever schema, grain, values, or expected results change.

### Content validation and rendering pipeline

Target pipeline:

```text
Markdown/config/dataset manifests
        ↓
content:validate
        ↓
parse + sanitize + whitelist
        ↓
resolve registered learning blocks
        ↓
render server-side HTML
        ↓
lazy-mount browser enhancements
        ↓
cache and serve
```

The validator must catch at least:

- [ ] duplicate keys and slugs;
- [ ] missing required metadata or content files;
- [ ] invalid phase/module/topic ordering;
- [ ] unpublished content referenced by published content;
- [ ] missing exercises, challenges, datasets, or interactive registrations;
- [ ] broken practice/dataset/project references;
- [ ] invalid exercise types or validator configuration;
- [ ] missing dataset tables, columns, relationships, or expected fixtures;
- [ ] malformed Markdown blocks and unsafe/unsupported constructs;
- [ ] broken Further Reading metadata where required.

### AI scope rule

- [ ] Do not create a standalone AI module, AI learning path, AI product feature, or mandatory “AI at Work” section in V1.
- [ ] Include AI only as optional/contextual learning material when it genuinely improves the specific analytical workflow being taught.
- [ ] Keep every core concept, exercise, and project understandable and complete without AI.
- [ ] Do not add an AI tutor, chatbot, AI grading, or runtime AI lesson generation.

### Representative content slice before bulk authoring

Before writing the full lesson library:

- [ ] Author one standard lesson from Module 01 with Indonesian prose, technical terms, a table, code, callout, Further Reading, one deterministic practice, and one self-assessment.
- [ ] Author one SQL-oriented lesson/challenge slice that exercises the approved Markdown practice mount and the SQL result validator.
- [ ] Attach a small versioned NusaMart dataset manifest and data dictionary fixture.
- [ ] Render the slice through the real Laravel pipeline on desktop and mobile widths.
- [ ] Validate the slice with `content:validate` and fixture tests.
- [ ] Record authoring conventions and use the slice as the template for later content batches.

Do not generate the approximately 80 lessons as part of this planning step.

## 6. Milestones

Milestones are sequenced around the architecture and the decision gates. The checkboxes are executable work items for future implementation; none of them have been started by this task.

### M0 — Foundation

#### Objective

Establish the greenfield Laravel shell, shared-hosting-compatible development/deployment baseline, content contract, safe Markdown pipeline, core design system, authentication boundary, and the evidence-backed decisions from Gates A–E.

#### Dependencies

- Product conflicts in the Open Decisions section are resolved or explicitly accepted as temporary launch assumptions.
- Target PHP/Laravel/MySQL hosting constraints are known.
- Gates A–E are run before any feature depends on their candidates.

#### Concrete tasks

- [x] Confirm the canonical source-document location and preserve existing worktree changes without restoring old root documents implicitly.
- [x] Confirm the supported PHP, Laravel, MySQL, database driver, PHP extensions, Node build version, and shared-hosting deployment constraints for local development.
- [x] Create the Laravel application shell with server-rendered routes and compiled static assets; do not require a production Node process.
- [x] Establish logical modular boundaries for Learning, Learner, Projects, and Datasets inside the monolith.
- [x] Establish environment/configuration conventions and ensure secrets remain in `.env` and are never committed or overwritten by deployment.
- [ ] Run and record Gates A–E, including rejected candidates and fallbacks.
- [x] Define stable key, slug, and route conventions for the representative repository content slice.
- [x] Implement the repository content loader and metadata/config reader without copying the full curriculum into MySQL.
- [x] Implement the Markdown parser, sanitizer/whitelist, structured-block resolver, and cache boundary selected by Gate D.
- [x] Define the dataset manifest/data dictionary loader and versioned asset contract.
- [x] Establish the initial design tokens: neutral surfaces, text, border, single accent, semantic colors, spacing, restrained radius, type scale, reading/content widths, and code typography.
- [ ] Build the shared server-rendered components needed by P0 screens: global header, phase/module rows, progress bar, lesson header/sidebar, content sections, callouts, code blocks, tables, practice shell, feedback, hint, Further Reading item, bookmark control, project stage navigation, and reference approach.
- [ ] Keep the UI copy Bahasa Indonesia-first while preserving established technical terms such as JOIN, DataFrame, dashboard, and confidence interval.
- [x] Add baseline logging for missing content, malformed config, and dataset/component failures without logging secrets or unnecessary learner answers.
- [ ] Define the small analytics event vocabulary and the no-op/local development behavior.

#### Acceptance criteria

- A minimal server-rendered Laravel page can be deployed with PHP/MySQL/static assets and does not depend on Redis, queues, Docker, or a Node dev server.
- The representative content slice renders through the safe Markdown/block pipeline and is cacheable.
- `content:validate` has a defined contract and can fail the build for broken content references.
- P0 design screens share one coherent editorial system and pass the anti-AI-slop review.
- The application boundary makes it impossible for learner SQL/Python to execute on Laravel.
- Authentication can be added without making public learning require an account.

#### Relevant tests

- [x] Application boot/configuration, database, and route smoke tests.
- [x] Content loader, stable-key, and cache-boundary tests.
- [x] Markdown allowlist/sanitization and structured-block resolution tests.
- [x] Dataset manifest schema tests.
- [ ] Design-system/component rendering smoke tests.
- [x] Local deployment/build smoke test on the recorded PHP/MySQL/static-asset baseline.

#### Risks/notes

- M0 is a foundation milestone, not permission to expand infrastructure. Any failed runtime spike must simplify the feature.
- Avoid implementing full curriculum pages before the representative content slice proves the contracts.

### M0A — Technical Foundation — COMPLETED

#### Objective

Create only the greenfield Laravel/application foundation needed before the technical spikes. No runtime candidate was selected and no curriculum/product feature was implemented.

#### Completed work

- [x] Created a Laravel 12 application scaffold using PHP 8.2 compatibility.
- [x] Selected Laravel application skeleton 12.0.0; installed Laravel framework 12.69.2.
- [x] Configured local development for MySQL database `belajar_data_v2` on `127.0.0.1:3306` using the supplied local credentials in `.env` only.
- [x] Set local session/cache to file-backed storage and queue connection to `sync`; no Redis or queue worker is required.
- [x] Removed the default Laravel Sail dependency and queue worker from the local development workflow; removed the unused jobs-table migration.
- [x] Added `config/belajardata.php` for repository content and dataset path conventions.
- [x] Established `app/Domain/Learning`, `Learner`, `Projects`, and `Datasets` boundaries inside the monolith.
- [x] Established `content/`, `datasets/`, `resources/views/`, `resources/js/`, `resources/css/`, and `tests/` conventions with focused README notes.
- [x] Added a minimal server-rendered foundation route/view at `/__foundation`; `/` redirects to it until the product homepage milestone.
- [x] Kept Vite as the frontend build pipeline for progressive enhancement; installed the scaffold's frontend dependencies and generated `public/build` successfully.
- [x] Added two foundation smoke tests: server-rendered view response and read-only MySQL connectivity/database selection.
- [x] Verified `composer validate --strict`, `php artisan about`, and `php artisan test` locally.

#### M0A verification results

- `composer validate --strict` — passed.
- `npm run build` — passed; Vite 6.4.3 generated `public/build`.
- `php artisan test` — passed; 2 tests and 5 assertions.
- Local database connectivity — passed; `belajar_data_v2` is reachable and the smoke test performs only `SELECT 1`.
- At M0A completion, no Gates A–E had been run; Gates B, C, D, and E were subsequently completed as separately scoped spikes.

#### Explicitly not started

- [x] M0B technical spikes A, B, C, D, and E completed; Gate A decision is now recorded.
- [x] M0C representative content loader, safe renderer, dataset manifest validation, and progressive component registry completed; this is not the final content system.
- [ ] Authentication, learner progress, bookmarks, or attempts.
- [ ] Final SQL, Spreadsheet Playground, Python/Pandas, visualization, or interactive components; only bounded future adapters are defined.
- [ ] Homepage, Learn/module/lesson UI, full design system, or curriculum lesson authoring.

#### M0A risks/notes

- The scaffold's generated SQLite file is ignored by Git, but the application configuration and smoke test use MySQL exclusively.
- Laravel's framework configuration files remain available as framework defaults; no queue worker, Redis service, Docker/Sail workflow, or other operational infrastructure is enabled.

### M0C — Product Foundation — COMPLETED

#### Objective

Promote the reusable M0A/Gate D foundation into a clean repository-first product boundary, validate the representative content and dataset slice, and remove disposable spike product surfaces. M0C does not start M1.

#### Completed work

- [x] Established canonical `content/{path-key}/{module-key}/{topic-key}/` metadata and lesson structure with stable machine keys.
- [x] Added production `ContentRepository` loading and validation for path/module/topic metadata, exercise config, lesson references, and source hashes.
- [x] Refactored Gate D's safe CommonMark renderer into the production lesson route `/learn/{pathKey}/{moduleKey}/{topicKey}` with server-rendered heading metadata, callouts, tables, code, links, images, and registered practice placeholders.
- [x] Established the registered learning-component contract: server-side `ComponentRegistry` and browser-side lazy `components/registry.js`; only `practice` is active in M0C.
- [x] Added production `content:validate` coverage for metadata, rendered lessons, structured references, versioned dataset manifests, and data dictionaries.
- [x] Added the versioned NusaMart manifest/data-dictionary contract and kept local fixture files repository-owned.
- [x] Added minimal editorial design tokens/primitives for reading content, tables, code, callouts, focus, and progressive blocks.
- [x] Added boundary logging for content, dataset, and component failures without logging secrets or unnecessary learner answers.
- [x] Removed disposable Gate B/C/E spike routes, controllers, views, fixtures, spike JavaScript, and spike-only tests; retained decision records and the documented static notebook fallback.
- [x] Kept future SQL, bounded spreadsheet, bounded Python/Pandas, and visualization adapters lazy and unregistered; no runtime library was selected or loaded by M0C.
- [x] Added focused feature and frontend tests for boot, MySQL connectivity, stable keys, content safety, directive resolution, malformed references, dataset contracts, component registration, rendering, and lazy boundaries.

#### M0C verification results

- `composer validate --strict` — passed.
- `php artisan content:validate` — passed for the representative lesson and `nusamart/v1` manifest.
- `php artisan test` — passed after the final test-file cleanup.
- `npm run build` — passed; Vite generated the production CSS, application entry, and lazy practice chunk.
- `node --test tests/Frontend/*.test.js` — passed.
- `git diff --check` — passed.

#### M0C remaining issues

- Gate A is now resolved by the representative spike decision record; the final SQL Playground remains future M3 work.
- The content validator is a foundation, not the complete curriculum graph/publication validator required before launch.
- M1 and all learner-facing product features remain unstarted.

### M1 — Learning Core

#### Objective

Deliver the core reading/navigation experience: homepage, five-phase Learn map, module pages, topic pages, canonical content rendering, Explore Skills index, soft prerequisites, bookmarks, recent learning, and guest/account progress.

#### Dependencies

- M0 content contract, renderer, design tokens, route conventions, and persistence boundaries.
- Representative content slice and its validation path.

#### Concrete tasks

- [ ] Implement public homepage with the positioning statement, real curriculum preview, Learn → Practice → Challenge → Project sequence, and a credible practice preview without fake metrics or social proof.
- [ ] Implement `/learn` with five phases, ordered modules, goals, estimates, progress, recommended sequence, and freedom to jump ahead.
- [ ] Implement module detail pages with topics, challenge, outcome, estimated effort, recommended knowledge, and current/continue state.
- [ ] Implement topic/lesson routes using stable slugs and server-rendered content.
- [ ] Implement lesson navigation with module sidebar/drawer, current position, completion state, optional on-page outline, previous/next, and one dominant action.
- [ ] Implement Explore Skills as an index into canonical topics/tags, not a duplicated curriculum.
- [ ] Implement the `ProgressStore` abstraction with guest localStorage and authenticated server-backed implementations.
- [ ] Implement idempotent topic start/completion and recent/resume state.
- [ ] Implement guest-to-account merge: stable-key matching, completion wins, no downgrade, and guest deletion only after successful merge.
- [ ] Implement authenticated bookmarks for topics/lessons and optionally Further Reading resources with constrained content types.
- [ ] Implement empty/error states and recovery-oriented messages.
- [ ] Implement responsive reading layout: desktop lesson workspace, tablet collapsible navigation, mobile selector/drawer, and desktop recommendation for complex tools.
- [ ] Apply accessibility foundations: labels, focus states, keyboard navigation, semantic headings, non-color completion states, readable tables/code, and reduced-motion handling.

#### Acceptance criteria

- A guest can browse and start any published module without login or prerequisite lock.
- A learner can see the recommended path, jump to another module, resume recent work, and understand progress as orientation rather than game state.
- An authenticated learner can persist topic progress and bookmarks across sessions.
- Guest progress merges without downgrading server completion.
- Learn/module/lesson screens look like a curriculum and reading product rather than a card-heavy SaaS dashboard.
- The representative content slice works on mobile; complex practice pages can clearly recommend desktop without hiding the lesson.

#### Relevant tests

- [ ] Public route/publication and stable-slug tests.
- [ ] Phase/module/topic ordering and soft-prerequisite tests.
- [ ] Guest localStorage persistence and recent-state tests.
- [ ] Authenticated progress idempotency and authorization tests.
- [ ] Guest-to-account merge tests for new, completed, in-progress, and conflicting states.
- [ ] Bookmark uniqueness, authorization, and removal tests.
- [ ] Responsive/accessibility smoke tests for P0 pages.

#### Risks/notes

- Derived module/phase progress should not be duplicated in MySQL unless evidence requires it.
- Do not build search as a launch blocker; a simple module/topic/skill index is sufficient initially.

### M2 — Assessment Core

#### Objective

Provide a reusable, configuration-driven practice and assessment contract with deterministic checks, progressive feedback, hints, reference approaches, guided self-assessment, and attempt persistence.

#### Dependencies

- M0 exercise schema and rendering/block contract.
- M1 lesson and progress persistence.

#### Concrete tasks

- [ ] Implement the shared Practice Shell with task, working area, primary check action, hint/reset controls, inline feedback, and completion state.
- [ ] Implement `multiple_choice`, `multi_select`, `numeric`, `text_self_assessment`, and `result_based` question types.
- [ ] Implement validator configuration for exact categorical answers, numeric tolerance, normalized tables/rows, required columns, ordering rules, and null behavior.
- [ ] Implement attempt loading/saving with constrained answer payloads, score where applicable, status, timestamps, and idempotent completion operations.
- [ ] Implement progressive hints and actionable incorrect feedback without revealing a complete solution too early.
- [ ] Implement reference answer/checklist flow for open-ended findings, recommendations, analysis plans, and executive summaries.
- [ ] Implement challenge-level aggregation and completion without hard-locking later curriculum.
- [ ] Register interactive component contracts around `initialize(config)`, `getAnswer()`, `validate()`, `reset()`, and `emitProgress()`.
- [ ] Add malformed-config and missing-reference errors to content validation.

#### Acceptance criteria

- A deterministic exercise can be authored in JSON, rendered in a lesson, checked, retried, reset, and completed.
- Valid alternate result order/formula/query implementations can pass when they produce the configured expected result.
- Open-ended work shows reference/checklist guidance and does not claim to perform AI/manual grading.
- Attempts and completion state persist only where useful and do not log unnecessary learner content.
- The shared shell can host SQL, spreadsheet, visualization, and special interactives without inventing unrelated UX patterns.

#### Relevant tests

- [ ] Unit tests for every validator type, numeric tolerance, ordering, missing columns, nulls, and malformed input.
- [ ] Exercise schema/config validation tests.
- [ ] Attempt authorization, idempotency, retry/reset, and completion tests.
- [ ] Browser tests for correct, incorrect, hint, reset, self-assessment, and reference flows.
- [ ] Accessibility tests for keyboard and screen-reader labels on core question types.

#### Risks/notes

- Browser-visible answer keys are acceptable for V1 because this is not high-stakes certification.
- Keep the question type set small; do not create a generic assessment authoring platform.

### M3 — SQL Playground

#### Objective

Deliver the isolated browser-side SQL learning environment required by Module 03 and SQL-enabled challenges/projects.

#### Dependencies

- Gate A decision.
- M1 lesson/content pipeline and M2 result-based assessment.
- Canonical versioned dataset manifest.

#### Concrete tasks

- [ ] Implement lazy-loading of the selected SQL runtime only on SQL practice pages.
- [ ] Load predefined dataset tables from versioned static assets into the isolated browser runtime.
- [ ] Implement schema browser and optional small sample previews using the design's restrained split-pane layout.
- [ ] Implement the SQL editor, Run, Reset, result table, row cap, runtime reset, and desktop guidance.
- [ ] Implement Web Worker execution/termination if the selected runtime supports it safely.
- [ ] Implement useful runtime/syntax errors while preserving the learner query.
- [ ] Implement result validation for columns, ordered/unordered rows, numeric tolerance, and expected analytical outputs.
- [ ] Prevent remote connections and any server-side query execution.
- [ ] Author and validate the representative Module 03 lesson/challenge slice, including grain, aggregation, JOIN multiplication, and a diagnostic wrong-query exercise.
- [ ] Persist meaningful practice/challenge completion and latest supported attempt, not every keystroke.

#### Acceptance criteria

- A learner can load the approved NusaMart fixture, write/run a supported query, inspect bounded results, receive actionable feedback, reset, and complete a challenge.
- SQL validation checks results rather than query text and supports legitimate alternate queries.
- A query never executes against application MySQL or an arbitrary remote service.
- Large output and runaway execution are bounded/recoverable.
- Ordinary lessons do not download the SQL runtime.

#### Relevant tests

- [ ] SQL runtime adapter tests for loading, querying, reset, errors, and worker termination.
- [ ] Result-normalization/tolerance tests using deterministic fixtures.
- [ ] Representative query tests for filtering, aggregation, joins, CASE, CTE, dates, and required window behavior.
- [ ] Browser tests for run/check/reset/error/retry/persistence.
- [ ] Security test proving no request path sends learner SQL to MySQL or a remote database.

#### Risks/notes

- Runtime-specific SQL differences must be documented in lessons and kept within the approved subset.
- Do not turn the schema browser into a database administration IDE.

### M4 — Spreadsheet Playground

#### Objective

Deliver a bounded spreadsheet-analysis environment sufficient for Module 02, with deterministic output validation and no ambition to reproduce Excel.

#### Dependencies

- Gate B decision.
- M1 content/progress and M2 result validation.
- Versioned spreadsheet-compatible fixtures and canonical NusaMart definitions.

#### Concrete tasks

- [ ] Implement the selected bounded grid/formula approach and lazy-load it only for spreadsheet exercises.
- [ ] Support predefined data and lookup/master ranges, cell/range references, selected formulas, and deterministic recalculation.
- [ ] Support only the approved sorting/filtering interactions.
- [ ] Implement bounded summary/pivot-style tasks as decided in Gate B.
- [ ] Validate target cells/tables/results rather than formula text.
- [ ] Add formula/error handling for blanks, missing keys, duplicate keys, invalid formulas, and numeric formatting.
- [ ] Author and validate Module 02 representative tasks: dataset inspection, filtering/sorting, business metrics, conditional logic, lookup, summary, comparison, and finding.
- [ ] Provide a readable non-ribbon UI and a clear desktop recommendation for complex grid tasks.

#### Acceptance criteria

- A learner can complete the intended Module 02 analysis using the supported feature allowlist.
- Multiple valid formulas that produce the expected result are accepted.
- Reset/retry is deterministic and state does not leak between exercises.
- No arbitrary workbook upload, macro, external link, full ribbon, or general spreadsheet authoring is present.
- The engine is not loaded on ordinary lessons.

#### Relevant tests

- [ ] Formula tests for each supported function and range/reference behavior.
- [ ] Lookup, duplicate/missing-key, blank, sort/filter, and summary tests.
- [ ] Output validator tests independent of formula spelling.
- [ ] Browser tests for edit, calculate, check, error, reset, and completion.
- [ ] Keyboard/accessibility and narrow-layout tests.

#### Risks/notes

- Pivot Table behavior is the main scope-expansion risk; prefer a bounded configured summary if a full pivot engine is not needed.

### M5 — Python/Pandas Practice

#### Objective

Provide the approved Module 04 Python/Pandas experience, or the documented bounded fallback, without running arbitrary Python on Laravel.

#### Dependencies

- Gate C decision.
- M1/M2 content and assessment contracts.
- Canonical CSV fixtures and Module 04 exercise definitions.

#### Concrete tasks

- [ ] If the optional browser experiment is retained, lazy-load the Python runtime only for Python exercises and isolate execution in a Web Worker where practical.
- [ ] If the optional browser experiment is retained, implement predefined dataset loading, code editor, Run, output/dataframe rendering, reset, bounded output, and error feedback.
- [ ] Support only the tested Python/Pandas subset: import, load, inspect, select, filter, sort, calculated columns, groupby, merge, dates, and dataframe output.
- [ ] Implement the selected bounded code/output exercises, expected-output checks, explanations, and downloadable notebooks without pretending to provide an open notebook runtime.
- [ ] Author and validate Module 04 tasks from load/inspect through filter/transform/aggregate/compare/interpret.
- [ ] Connect later Cleaning/EDA/Statistics tasks to the approved Python or fallback capability.
- [ ] Provide reset/recovery behavior and preserve work on runtime errors.

#### Acceptance criteria

- Module 04 can be completed using the approved experience on the supported device baseline.
- No arbitrary learner Python is sent to or executed by Laravel.
- Resource limits, error behavior, output caps, and desktop guidance are visible and tested.
- The fallback is explicitly reflected in lesson expectations and does not silently reduce required learning outcomes.
- Ordinary lesson pages do not load Pyodide or equivalent runtime.

#### Relevant tests

- [ ] Runtime adapter tests for CSV, Pandas operations, output, errors, reset, and resource caps, or equivalent fallback tests.
- [ ] Browser tests for edit/run/error/fix/rerun and completion.
- [ ] Device/performance checks for initial load and memory.
- [ ] Security test proving no server-side execution path.

#### Risks/notes

- Do not add a Python execution service to preserve the ideal UX. A reliable bounded fallback is preferable to new infrastructure.

### M6 — Visualization & High-Value Interactives

#### Objective

Deliver bounded visualization and the five high-value interactive learning objects without building a dashboard builder or generic diagram editor.

#### Dependencies

- Gate E decision.
- M2 interactive/assessment contract.
- Canonical datasets and content for Modules 06, 07, 08, 09, and 12.

#### Concrete tasks

- [ ] Implement the config-driven Visualization Playground with finite metric, dimension, chart, sorting, and highlighting controls.
- [ ] Implement required chart types and adjacent text/table explanations.
- [ ] Implement JOIN Row Multiplication using predefined tables, join key inspection, row counts, and grain warnings.
- [ ] Implement Sampling & Uncertainty with deterministic seeds where assessment reproducibility matters, bounded sample controls, and repeated-estimate views.
- [ ] Implement Metric Tree Builder using predefined nodes and limited valid relationships; do not build arbitrary node editing.
- [ ] Implement Communication Builder fields for headline, evidence, known, unknown, and next step, followed by reference/checklist review.
- [ ] Integrate visualization and interactives into the relevant module lessons/challenges.
- [ ] Provide non-interactive explanatory content for concepts that should remain understandable without JavaScript.
- [ ] Ensure controls, feedback, keyboard states, and reduced-motion behavior meet accessibility requirements.

#### Acceptance criteria

- Each required interactive materially teaches its concept and can be reset, validated, and completed.
- Visual controls are bounded and config-driven; there is no arbitrary upload, calculated-field engine, or dashboard builder.
- Sampling outputs are reproducible where expected; visualizations are responsive and not decorative.
- Metric Tree and Communication Builder accept structured answers and use self-assessment/reference guidance rather than AI grading.
- Runtime assets are lazy-loaded and do not affect normal lesson performance.

#### Relevant tests

- [ ] Chart configuration, data mapping, resize, cleanup, fallback table, and accessibility tests.
- [ ] JOIN row/grain transformation fixture tests.
- [ ] Deterministic sampling/uncertainty tests.
- [ ] Metric tree valid/invalid relationship tests.
- [ ] Communication Builder persistence, review, and checklist tests.
- [ ] Browser tests for reset, feedback, and completion across each interactive.

#### Risks/notes

- The strongest scope-control rule is to teach a concept with a bounded interaction, not to rebuild Tableau, Excel, a notebook, or a generic diagram tool.

### M7 — Tableau Learning

#### Objective

Deliver the external-tool learning experience for Tableau through safe downloadable data, guided tasks, checkpoints, and self-assessment.

#### Dependencies

- Resolved V1 scope: Tableau-only; Power BI is deferred/optional.
- M1/M2 content and checkpoint contracts.
- Canonical safe NusaMart datasets and tested task instructions.

#### Concrete tasks

- [ ] Author the Tableau track according to the approved V1 scope: connect/understand data, relationships/grain, calculated fields, dashboard, interactions, and analytical validation.
- [ ] Provide safe fictional/public datasets and clear download/version instructions.
- [ ] Implement dataset download metadata and checksum/version display where useful.
- [ ] Write guided tasks that send the learner to the real Tableau tool and bring them back to BelajarData.
- [ ] Implement checkpoints for totals, orders, growth, category/region findings, and dashboard checklist items.
- [ ] Make Tableau Public publishing optional and display the warning never to publish confidential/private/proprietary/company data.
- [ ] Avoid workbook parsing, Tableau simulation, and automatic dashboard grading.
- [ ] Test the tasks against the supported Tableau flow and record known UI/version assumptions.

#### Acceptance criteria

- A learner can download safe data, perform the guided Tableau workflow externally, return to BelajarData, validate analytical checkpoints, and complete guided self-assessment.
- The product does not claim to simulate or automatically inspect Tableau workbooks.
- At least two launch-ready project/tool workflows can use Tableau without hidden unsupported steps.
- Privacy guidance is visible at the point of publishing guidance.

#### Relevant tests

- [ ] Dataset download/version/link/metadata tests.
- [ ] Checkpoint validator tests.
- [ ] Content/link smoke tests and manual walkthrough against the supported Tableau version.
- [ ] Privacy-warning content review.

#### Risks/notes

- D-02 is resolved: V1 is Tableau-only. Power BI remains deferred/optional and must not be implemented as a V1 core track.

### M8 — Projects

#### Objective

Deliver the project workspace and the approved launch set of substantial, less-prescriptive projects with independent progress, checkpoints, reference approaches, and tool choice.

#### Dependencies

- M1 progress/authentication.
- M2 assessment/reference flow.
- M3–M7 approved practice capabilities.
- Decision on project count and stage model.

#### Concrete tasks

- [ ] Define project repository/config schema with stable project and stage keys, difficulty, focus, estimated effort, datasets, publication state, and tool guidance.
- [ ] Implement the canonical six-stage workspace: Brief, Understand, Plan, Investigate, Validate, Communicate, with Reference Approach separate and accessible after an attempt or confirmation.
- [ ] If the curriculum's “Build Your Evidence” step is retained, represent it as a substep within Investigate/Validate unless the stage decision explicitly changes the canonical model.
- [ ] Implement project landing page as editorial rows with real briefs, not a generic course-card marketplace.
- [ ] Implement project workspace with stage navigation, current state, notes/answers where needed, datasets, checkpoints, and reference approach.
- [ ] Implement independent project start/current-stage/stage-completion/completion persistence.
- [ ] Keep project access independent from core path unlocks and do not require two completed projects to access curriculum.
- [ ] Author and QA at least two substantial launch projects end-to-end: NusaMart Revenue Slowdown and Customer Retention Analysis, unless the launch decision selects a different pair.
- [ ] Prepare Delivery Performance Investigation as the third project according to the approved launch/post-launch decision.
- [ ] Ensure projects assess analytical outcomes rather than a mandatory software stack.
- [ ] Add objective checkpoints plus guided self-assessment for plans, findings, limitations, recommendations, and summaries.

#### Acceptance criteria

- A learner can start a project, resume it at a stage, complete supported checkpoints, review a reference approach, and communicate an evidence-limited conclusion.
- At least two projects are playable end-to-end and meet the PRD's launch bar.
- The workspace does not prescribe a hidden sequence of commands or auto-grade arbitrary dashboards/workbooks.
- Project progress is independent from topic/module progress and survives account sessions.
- Reference approaches present one defensible approach, alternatives/common mistakes, and limitations without claiming a single universal solution.

#### Relevant tests

- [ ] Project/stage publication and stable-key tests.
- [ ] Project progress idempotency, resume, authorization, and stage-transition tests.
- [ ] Checkpoint/result/self-assessment tests.
- [ ] Browser tests for starting, resuming, opening reference, completing, and returning to project list.
- [ ] End-to-end manual QA of at least two projects using the approved tool combinations.

#### Risks/notes

- Project content quality is a launch dependency, not a later polish item.
- Do not make project completion a hard prerequisite for the learning path.

### M9 — Launch Hardening

#### Objective

Make the complete V1 learning journey reliable, safe, accessible, measurable, performant, and deployable on conventional PHP shared hosting.

#### Dependencies

- M0–M8 feature contracts and decisions.
- Final product decisions in the Open Decisions section.
- Full content authoring and review for the approved launch scope.

#### Concrete tasks

- [ ] Complete and publish Modules 01–12 according to the approved curriculum/source-document alignment.
- [ ] Ensure every core topic has narrative content, relevant practice, Further Reading where useful, and its intended module challenge or approved equivalent.
- [ ] Ensure required datasets, data dictionaries, manifests, validators, and expected fixtures are versioned and internally coherent.
- [ ] Complete at least two substantial projects and the approved third-project status.
- [ ] Run `content:validate` in CI/deployment and fail on broken references, duplicate keys, malformed configs, unsafe blocks, or dataset mismatch.
- [ ] Run full backend tests for routing/publication, authentication, progress, guest merge, attempts, bookmarks, projects, and idempotent state updates.
- [ ] Run critical browser flows: guest learning, account persistence, guest merge, SQL run/check/persist, spreadsheet run/check, Python approved path/fallback, interactives, Tableau checkpoints, and project resume.
- [ ] Perform responsive/accessibility QA for reading, simple assessments, and representative complex tools.
- [ ] Audit language/tone, terminology, metric definitions, grain explanations, unsupported causality, and future-prerequisite references.
- [ ] Audit design for excessive cards, gradients, shadows, badges, fake metrics, decorative charts, unnecessary icons, and generic SaaS patterns.
- [ ] Add privacy/security review: CSRF, escaping, session security, authorization, rate limits where appropriate, safe Markdown, no server-side learner code execution, no unsafe dataset upload.
- [ ] Measure lazy-load/runtime performance, parsed-content caching, result row caps, asset sizes, and slow/low-memory behavior.
- [ ] Build release assets with `composer install --no-dev`, frontend asset build, tests, content validation, cache rebuild, migrations, and smoke test steps suitable for cPanel.
- [ ] Confirm production `.env` handling, storage permissions, database migration safety, backups, and rollback notes.
- [ ] Add simple product analytics for the approved event vocabulary without instrumenting every click/scroll.
- [ ] Perform final SEO/accessibility metadata and useful empty/error-state review.

#### Acceptance criteria

- A new learner can open the product without an account, understand five phases, start Module 01 or jump ahead, complete lessons/practice, and receive deterministic or guided feedback.
- The approved browser practice environments work within their documented limits and do not execute learner SQL/Python on Laravel.
- The full approved Modules 01–12 journey is available, coherent, and validated; pages existing without content do not count as complete.
- Tableau learning uses the real external tool and safe datasets.
- At least two substantial projects are playable end-to-end and resumable.
- An account preserves progress, bookmarks, recent state, and project progress across sessions.
- The deployment requires only the agreed PHP/Laravel/MySQL/static asset stack and no mandatory operational infrastructure outside it.
- The experience passes the source-document quality bar and demonstrates business question → data → analysis → evidence → decision → communication.

#### Relevant tests

- [ ] Full automated test suite and content validation.
- [ ] Browser regression suite for critical flows.
- [ ] Accessibility audit and keyboard-only pass.
- [ ] Responsive manual QA at mobile/tablet/desktop widths.
- [ ] Performance and asset-budget review for ordinary versus runtime-heavy pages.
- [ ] Shared-hosting deployment rehearsal and post-deploy smoke test.
- [ ] Final content/editorial review by a data-analysis subject-matter reviewer.

#### Risks/notes

- Launch readiness is content completeness plus product reliability, not merely route coverage.
- Search, Power BI expansion, third project breadth, richer authoring workflow, and advanced interactives remain candidates only after the approved V1 boundary is stable.

## 7. Open Decisions / Clarifications

These items are intentionally not silently resolved because the current source documents disagree or the target environment is unknown. The recommendation is the simplest option consistent with the product and architecture principles.

### D-01 — Canonical location of source documents — RESOLVED

Evidence: the request names `docs/curriculum.md`, `docs/PRD.md`, `docs/design.md`, and `docs/architecture.md`, while Git currently tracks differently versioned root-level documents as deleted and the `docs/` copies are untracked.

Decision: keep `docs/` as the canonical source location and preserve the existing root deletions. Do not restore or merge the old root files automatically.

The user confirmed this decision.

### D-02 — Tableau-only V1 versus the curriculum's Power BI + Tableau tracks — RESOLVED

Evidence: `curriculum.md` describes both Power BI and Tableau and says one track is enough; `PRD.md` explicitly makes Tableau the V1 core track and lists Power BI as out of V1 core scope; `architecture.md` and the recommended sequence only define Tableau for the external BI milestone.

Decision: launch Tableau only in V1. Power BI is deferred/optional and must not be implemented as a V1 core track. Before content implementation, align the authoritative curriculum/PRD wording so learners are not shown contradictory requirements.

The user confirmed Tableau-only V1.

### D-03 — Project launch count — PARTIALLY RESOLVED

Evidence: `curriculum.md` defines three projects and says learners complete any two; `PRD.md` requires at least two polished projects at launch and allows the third shortly after; `architecture.md` says two launch-ready projects.

Decision: target all three projects for launch if they meet the quality bar; two polished projects remain the minimum launch gate. Keep the project library and completion model capable of three projects; do not require two completions to unlock the path.

The user indicated that three projects are acceptable when feasible. M8/M9 must make the final go/no-go decision for Project 03 based on content quality and end-to-end QA rather than schedule optimism.

### D-04 — Project stage model — RESOLVED

Evidence: the curriculum project experience lists eight steps including “Build Your Evidence” and “Reference Approach”; the PRD defines six stages and a separate Reference Approach; the architecture fixes six canonical stages.

Clarification: this concerns the learner-facing project navigation, not the analytical work itself. One document describes eight visible steps (`Brief → Understand → Plan → Investigate → Validate → Build Your Evidence → Communicate → Reference Approach`), while the PRD/architecture define six canonical progress stages plus a separate reference section. The difference is whether “Build Your Evidence” gets its own progress stage and whether “Reference Approach” is treated as a stage.

Decision: use the architecture/PRD six-stage runtime (`brief`, `understand`, `plan`, `investigate`, `validate`, `communicate`), represent “Build Your Evidence” as a substep inside Investigate/Validate, and keep Reference Approach separate and never hard-locked. This keeps progress simple while preserving the full analytical workflow.

The user confirmed the six-stage progress model.

### D-05 — Hosting compatibility matrix — LOCAL BASELINE RECORDED

Evidence: the target is conventional PHP shared hosting/cPanel. The local development baseline is now known: PHP 8.2.12, Composer 2.9.2, Node 24.11.1, npm 11.6.2, MySQL 8.0.45, required initial PHP extensions enabled, and `belajar_data_v2` available on `127.0.0.1:3306`.

Recommendation: use the recorded local database for development and obtain the production host matrix before selecting final Laravel/runtime versions or deployment scripts. Keep the architecture free of services the host cannot run.

The local development database is authorized for use. Production hosting constraints remain an M0 input; do not assume that the local Node/MySQL tooling is available in production.

### D-06 — Runtime decision ownership — RESOLVED

Decision: technical libraries are selected from evidence produced by the technical spikes. No specific SQL, spreadsheet, Python, charting, or Markdown library is pre-approved.

For each gate, choose the simplest candidate that satisfies the documented acceptance criteria, architecture constraints, curriculum requirements, browser performance requirements, licensing requirements, and shared-hosting deployment model. Record the selected candidate and rationale in the relevant decision record after the spike. Preserve a documented fallback for every failed candidate.

The user confirmed this evidence-based selection process.

### D-07 — “AI-Augmented Workflow” in the dependency diagram — RESOLVED

Evidence: the curriculum dependency diagram includes AI-Augmented Workflow, while the module list does not define a standalone AI module; the PRD excludes AI tutor, runtime AI lesson generation, AI grading, and chatbot features.

Decision: there is no standalone AI module, AI track, or mandatory AI section in BelajarData V1. AI may appear only as optional/contextual learning material when it genuinely improves the specific analytical workflow being taught.

Do not create an AI module, AI learning path, AI product feature, mandatory “AI at Work” section, AI tutor/chatbot, or AI grading. Core fundamentals must remain understandable and complete without AI.

The user confirmed this V1 boundary.

## 8. Explicit non-goals for implementation

- [ ] Do not add an AI tutor, chatbot, AI grading, runtime lesson generation, or manual grading system.
- [ ] Do not add XP, streaks, leaderboards, certificates, community/forum, subscription/payments, or fake social proof.
- [ ] Do not add Data Scientist, Data Engineer, Machine Learning, Power BI core, or unrelated career paths to the V1 implementation unless the source documents are explicitly changed.
- [ ] Do not build a full Excel clone, notebook/Colab clone, Tableau simulator, dashboard builder, arbitrary dataset upload, or generic diagram editor.
- [ ] Do not run learner SQL or arbitrary Python on the Laravel server.
- [ ] Do not add Redis, queues, websockets, microservices, Elasticsearch, vector databases, LLM infrastructure, GraphQL, event sourcing, a complex CMS, separate frontend repository, mobile app, or real-time collaboration without a new architectural decision.

## 9. Definition of done for this planning task

- [x] Repository inspected.
- [x] All four source documents read completely.
- [x] Technical assumptions, dependencies, and risks recorded.
- [x] Repository conflicts and source-document conflicts recorded.
- [x] Technical spikes defined as early decision gates.
- [x] Content structure, metadata/config, exercises, datasets, validation, and rendering pipeline planned.
- [x] Milestones M0–M9 include objectives, dependencies, concrete checkbox tasks, acceptance criteria, tests, and risks/notes.
- [x] No application code, migrations, package installation, UI scaffold, or bulk lesson authoring performed.
