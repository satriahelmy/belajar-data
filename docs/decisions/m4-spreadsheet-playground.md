# M4, Spreadsheet Playground Decision

**Status:** COMPLETE for the representative Module 02 slice  
**Date:** 2026-09-20  
**Related decision:** `docs/decisions/gate-b-spreadsheet.md`

## Decision

Implement the first Spreadsheet Playground as a browser-only, bounded component built from native HTML tables and a small custom formula evaluator. Do not add a spreadsheet runtime dependency. The component is lazy-loaded only when a repository lesson or challenge contains a registered `spreadsheet-playground` directive.

The selected approach is deliberately an analysis worksheet, not an Excel clone. It keeps the learning surface small enough to test, cache, secure, and deploy on ordinary PHP shared hosting.

## Evidence demonstrated

- Repository content renders a Module 02 lesson and challenge through Laravel.
- The canonical `nusamart/v1` fixture supplies `Transactions` and `Products` data in the browser.
- The evaluator supports cell and range references, arithmetic, comparisons, `SUM`, `COUNT`, `AVERAGE`, `IF`, `COUNTIF`, `COUNTIFS`, `SUMIF`, `SUMIFS`, and exact `VLOOKUP(..., FALSE)`.
- The prototype demonstrates table inspection, region/month filter, bounded sort, metric formulas, conditional logic, lookup, configured category summary, comparison, missing-key error handling, reset, result checking, and authenticated attempt persistence.
- Formula output is validated by target cells or table results, not formula spelling.
- The parser never calls `eval`, `Function`, external URLs, macros, uploads, or a server-side spreadsheet engine.

## Alternatives considered

Gate B evaluated HyperFormula, Handsontable, Univer, Formula.js, and the native bounded approach. HyperFormula raised GPL/commercial licensing and dependency-size concerns. Handsontable and Univer were broader than the learning requirement and introduced licensing, bundle, or UX complexity. Formula.js did not provide the complete parser/grid contract and had maintenance or licensing uncertainty. The native bounded approach was selected because it has no new dependency and its allowlist is directly testable.

## Feature and directive contract

The public content directive is:

```text
:::spreadsheet-playground id="sheet-metrics-01"
:::
```

The `id` resolves to one registered `result_based` exercise. The exercise config selects `formula`, `table`, or `summary` mode, references the versioned dataset, and declares the bounded starter state, allowed cells, columns, filters, sorts, summary dimension, or measures. The server renders inert JSON configuration and a non-JavaScript fallback label; the browser component progressively enhances the mount point.

The approved formula grammar is intentionally narrow. It allows worksheet references to the two predefined tables and local analysis cells, the documented functions, exact lookup only, and explicit error values. It does not grade formula text or support arbitrary workbook authoring.

## Security and deployment boundary

Markdown cannot supply formulas, JavaScript, dataset paths, or arbitrary component types. Exercise configuration is validated before rendering. The client receives only the selected fixture/config, and all displayed values are inserted as text. There is no upload path, macro path, external link path, or spreadsheet mutation route. Attempts use the existing M2 result contract and persist only bounded results for authenticated learners.

The implementation has no Redis, queue, Docker, Python service, or SPA requirement. It ships as Vite assets and can run on the documented PHP/MySQL shared-hosting model.

## Cache and invalidation

The Markdown lesson cache key includes the source hash and the spreadsheet fixture source hash. Dataset schema or row changes require a dataset version or manifest change. Vite content hashes invalidate the lazy JavaScript chunk on deployment. Worksheet state is in memory and reset restores the configured initial state. No server cache or long-running process is required.

## Validation and limitations

`php artisan content:validate` validates the repository lesson, exercise references, structured directives, and canonical dataset. PHP feature tests cover rendering, result checking, persistence, fixture boundaries, and route constraints. Node tests cover formula behavior, range references, errors, filter/sort, configured summary, and output validation. The full browser visual smoke remains a manual follow-up for low-end mobile performance.

Known limitations are intentional: no upload, general pivot builder, charts, formatting toolbar, collaboration, formula text grading, named ranges, external links, or arbitrary workbook creation. If future curriculum work exceeds this allowlist, use a configured result interaction or downloadable exercise before introducing a general spreadsheet library.
