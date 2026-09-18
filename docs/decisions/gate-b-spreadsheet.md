# Gate B — Bounded Spreadsheet Execution

**Status:** PASSED for the representative technical spike only  
**Date:** 2026-09-18  
**Scope:** M0B Gate B; this record does not approve M4 implementation or complete Module 02 authoring.

## Question

Can BelajarData teach the initial Module 02 spreadsheet workflow in the browser with a small, deterministic, shared-hosting-friendly experience rather than a general spreadsheet clone?

## Evidence and candidates

The spike used a small provisional NusaMart v1 fixture with eight order-item rows and four product master rows. It demonstrated editable cells, exact lookup, filter/sort, the required formula subset, a configured category summary, output validation, reset, and browser-only execution.

Candidates were evaluated from package metadata and the requirements in `task.md`, `curriculum.md`, `architecture.md`, and `PRD.md`:

| Candidate | Evidence | Decision |
| --- | --- | --- |
| HyperFormula 3.4.0 | Maintained browser formula engine; GPL-3.0-only; npm reports 12.88 MB unpacked and dependencies including Chevrotain. | Rejected for the spike because the project license has not approved GPL/commercial licensing, and the bundle/dependency cost is high for the bounded allowlist. |
| Handsontable 18.1.1 | Maintained general data grid; license is supplied separately by the package; npm reports 34.61 MB unpacked. | Rejected because it is a feature-rich grid with licensing/commercial review and bundle cost beyond the learning goal. |
| Univer presets 0.25.1 | Apache-2.0, but the sheets preset pulls a broad suite of core, filter, sort, drawing, collaboration, and other packages. | Rejected as a general productivity suite with unnecessary dependency and UX complexity. |
| Formula.js 1.0.8 | Function collection, not a complete cell-reference parser/grid; npm metadata shows the last modification in 2022 and does not provide a clear license field. | Rejected because it still requires a parser/grid layer and leaves maintenance/licensing uncertainty. |
| Native HTML tables plus a bounded custom evaluator | No new runtime dependency; exact feature surface is small and testable; browser-only and compatible with compiled assets/shared hosting. | Selected for this spike. |

The selected approach is intentionally not a generic spreadsheet engine. It uses an explicit parser and function allowlist, so formulas are interpreted as data by controlled code and never passed to `eval`, `Function`, or arbitrary JavaScript execution.

## Selected feature contract

The prototype supports:

- cell and range references on `Transactions` and `Products`;
- arithmetic with `+`, `-`, `*`, `/` and comparisons;
- `SUM`, `COUNT`, `AVERAGE`, `IF`, `COUNTIF`, `COUNTIFS`, `SUMIF`, and `SUMIFS`;
- exact `VLOOKUP(..., FALSE)` only;
- predefined worksheet data and lookup/master data;
- editing bounded cells, deterministic recalculation, reset, and retry;
- one configured filter/sort view: region, month, and a small approved sort set;
- numeric output tolerance, blank/null handling, ordered summary-table validation, and explicit formula errors;
- a native, keyboard-reachable table structure with labelled inputs and horizontal overflow on narrow screens.

Not supported by this spike: arbitrary workbook creation, unlimited sheets, uploads, macros, external links, named ranges, formula text grading, general pivot configuration, chart building, formatting toolbars, collaboration, or a ribbon.

## Lookup and summary decisions

Lookup uses an exact product-key lookup. Missing keys return `#N/A`; duplicate keys return `#DUPLICATE!`; an out-of-range lookup column returns `#REF!`. The demonstrated curriculum-compatible function is `VLOOKUP`; `XLOOKUP` is not added until a later implementation need justifies it.

The spike explicitly investigated general Pivot Table behavior versus a configured summary. It selected the configured summary: category is the fixed dimension and revenue, quantity, and order count are the fixed measures. This retains the Module 02 learning outcome of answering “metric by dimension” business questions without the scope and UX cost of a general pivot engine. Later M4 work may add another preconfigured summary shape only when a curriculum task requires it.

## Validation contract

The exercise validates target outputs, not a particular formula spelling:

- formula targets are keyed by stable target names and compared with a small numeric tolerance;
- summary rows compare category, revenue, quantity, and order count in a declared order;
- blanks are treated as empty values, numeric formatting is presentation-only, and invalid formulas produce explicit error codes;
- reset clones the initial fixture so recalculation and retry are deterministic;
- the representative expected outputs live with the versioned spike manifest, not in MySQL.

## Browser architecture, performance, and devices

The Laravel controller server-renders the page and embeds a small versioned fixture as inert JSON. The evaluator, table rendering, lookup, filter/sort, summary, and validator run in the browser. No spreadsheet request, learner formula, or dataset execution is sent to MySQL or a remote service.

The spreadsheet module is a Vite dynamic import activated only on `[data-spreadsheet-spike]`. The production build measured:

- spreadsheet lazy chunk: 16.06 kB raw / 5.60 kB gzip;
- application entry: 53.45 kB raw / 20.37 kB gzip;
- CSS bundle: 36.80 kB raw / 9.36 kB gzip.

On the local desktop in-app browser with the eight-row fixture, the page reported approximately 6.20 ms initialization, 0.40–1.40 ms recalculation, and 6.30 ms reset. These are smoke measurements, not a production device benchmark. Desktop is recommended for complex worksheet tasks. On small screens the table remains readable through horizontal scrolling and controls wrap; the full spreadsheet interaction is not forced into a narrow viewport. A later browser QA pass must measure low-end mobile devices before making mobile a curriculum dependency.

## Cache and invalidation

The fixture is versioned under `datasets/nusamart/v1/`; schema or expected-output changes require a dataset version/manifest change. Vite content hashes invalidate the lazy JavaScript chunk on deployment. Workbook state is in-memory only during the spike; reset restores the immutable initial clone. No server cache or long-running process is required.

## Content validation and fallback

The current spike validates the fixture through Laravel JSON decoding, feature coverage, and Node tests for formulas, lookups, blanks, errors, sorting, summaries, validation, and deterministic cloning. The existing Gate D `content:validate` remains unchanged and is not the spreadsheet validator.

If a future required Module 02 task exceeds this allowlist or the custom evaluator becomes difficult to maintain, the fallback is a configured result interaction or downloadable spreadsheet exercise. A general spreadsheet library is not automatically introduced.

## Limitations

This is not the final Spreadsheet Playground, does not author Module 02 lessons, does not persist learner attempts, and does not benchmark large datasets. The provisional fixture must later be reconciled with the canonical NusaMart manifest/data dictionary before M4. The custom evaluator currently supports only the documented formula grammar and exact VLOOKUP; this narrowness is a deliberate safety and scope boundary.
