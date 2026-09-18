# Gate A Decision — Browser-side SQL Execution

Status: **PASSED for the representative technical spike**  
Date: 2026-09-18  
Scope: browser execution only; this is not the final SQL Playground.

## Decision

Select `sql.js` 1.14.2 for the future bounded SQL learning adapter. It is SQLite compiled to WebAssembly, runs in a dedicated Web Worker, and satisfies the demonstrated Module 03 query family without sending learner SQL to Laravel, MySQL, or any remote database.

The final SQL Playground remains M3 work. M0B only proves the engine boundary, fixture contract, result validator, resource limits, and lazy-loading shape.

## Candidates evaluated

### sql.js 1.14.2 — selected

- MIT licensed; SQLite itself is public domain.
- Stable npm release evaluated on 2026-09-18.
- The selected browser asset is `sql-wasm.wasm` (658,410 bytes) with the sql.js loader and a dedicated worker entry.
- Supports the complete representative query suite: SELECT, column selection, WHERE, ORDER BY, LIMIT, COUNT, COUNT DISTINCT, SUM, AVG, GROUP BY, multi-table JOIN, CASE, CTE, date extraction/comparison, window functions, and LAG.
- The worker boundary is straightforward and compatible with static shared-hosting assets.

### DuckDB-Wasm 1.32.0 — rejected for this gate

- MIT licensed and technically capable of the required analytical SQL patterns.
- The evaluated distribution is materially larger: approximately 39.36 MB for the MVP Wasm module or 34.24 MB for the EH module, plus worker assets and the Apache Arrow dependency. The npm package's unpacked size was approximately 144 MB.
- DuckDB-Wasm requires coordinated main-library, worker, Wasm, and optional cross-origin-isolated threaded assets. Its official deployment guidance supports static hosting, but the operational and bundle cost is not justified by the Module 03 subset demonstrated here.
- It was not selected because the smaller candidate passed every required query case without removing a curriculum objective. This is a cost/complexity decision, not a claim that DuckDB-Wasm lacks capability.

Evidence sources: [sql.js project](https://github.com/sql-js/sql.js), [DuckDB-Wasm project](https://github.com/duckdb/duckdb-wasm), and [DuckDB-Wasm deployment guidance](https://duckdb.org/docs/stable/clients/wasm/deploying_duckdb_wasm).

## Representative fixture

The spike uses `datasets/nusamart/v1/sql/` and keeps the M0C top-level NusaMart manifest unchanged. The SQL profile contains:

- `orders`: 6 rows, one row per order, primary key `order_id`;
- `order_items`: 9 rows, one row per product line in an order, composite key `order_id + product_id`;
- `products`: 4 rows, one row per product, primary key `product_id`;
- `customers`: 4 rows, one row per customer, primary key `customer_id`.

Relationships are `orders.customer_id → customers.customer_id`, `order_items.order_id → orders.order_id`, and `order_items.product_id → products.product_id`. The profile has a manifest and data dictionary following the M0C repository convention.

## Capability evidence

The browser suite passed 15/15 representative cases in the Web Worker:

1. SELECT and column selection;
2. WHERE;
3. ORDER BY and LIMIT;
4. COUNT;
5. COUNT DISTINCT;
6. SUM;
7. AVG with numeric tolerance;
8. GROUP BY with a product JOIN;
9. multiple-table JOIN;
10. CASE business logic;
11. CTE;
12. ISO date extraction and comparison;
13. `ROW_NUMBER()` window function;
14. `LAG()` time comparison;
15. a deliberately incorrect order-level aggregation after a one-to-many JOIN.

The suite also runs in the Node test harness against the same fixture and expected results. No expected result compares SQL strings.

## JOIN row-multiplication finding

`orders.shipping_cost` is an order-level value. Joining `orders` to `order_items` before summing produces 121 because each order's shipping cost is repeated for every item row. The correct order-level total is 79. The spike exposes the inflated result as a diagnostic learning case, proving that the engine can teach grain and JOIN consequences rather than hiding them.

## Result-validation contract

The prototype validator accepts:

- exact expected column names and order;
- ordered or unordered row comparison;
- numeric tolerance per case;
- exact NULL equality;
- bounded displayed output.

The validator compares normalized result data, not query text. The current UI caps displayed rows at 100. Because sql.js materializes a query result before JavaScript receives it, the display cap is not a complete memory cap; the worker timeout and later dataset/query limits remain necessary.

## Worker and security boundary

- The page embeds only the predefined fixture metadata/rows from the repository profile.
- The worker creates an in-memory SQLite database and never receives database credentials.
- No endpoint accepts learner SQL. The Laravel route only serves the static spike shell and fixture.
- The worker policy permits only SELECT/WITH statements and blocks mutation, attachment, pragma, extension-loading, and schema-changing operations.
- A 10,000-character query limit is applied before dispatch.
- A 2-second timeout terminates the worker. Reset creates a fresh worker and reloads the fixture.
- A deliberately unbounded recursive query timed out at 2,000 ms, the worker was terminated, and the representative suite passed after reset.

This prevents the demonstrated learner SQL from reaching Laravel/MySQL or an arbitrary remote datasource. It is not a security boundary for untrusted arbitrary code; the future adapter must preserve the same predefined-data and read-only constraints.

## Performance evidence

Measured on the local Codex in-app Chromium browser with the 23-row fixture:

| Measurement | Result | Evidence type |
| --- | ---: | --- |
| sql.js Wasm asset | 658.41 kB uncompressed / 322.99 kB gzip | Vite build output |
| SQL spike entry | 10.14 kB / 3.45 kB gzip | Vite build output |
| Worker bundle | 42.82 kB | Vite build output |
| Cold initialization | 31.0–51.2 ms | browser UI measurement |
| Warm representative suite | 13.4–18.2 ms for 15 cases | browser UI measurement |
| Simple SELECT | 1.0 ms | browser UI measurement |
| JOIN diagnostic query | 1.3 ms | browser UI measurement |
| Reset + fixture reload | 46.9–59.1 ms | browser UI measurement |
| Timeout termination | 2,000 ms | configured limit and browser observation |
| JS heap after initialization | 10.8 MiB | browser UI measurement via `performance.memory` |

These numbers are representative local measurements, not a mobile-device or production benchmark. The 10.8 MiB heap reading is browser-reported JS heap for this page, not a complete Wasm RSS measurement. The spike does not claim a low-memory/mobile budget has been proven. The practical recommendation is desktop/tablet for the future SQL interaction, with a server-rendered explanation and bounded result fallback available on constrained devices.

## SQLite dialect implications

The required learning objectives remain intact, but authoring must identify SQLite-compatible syntax:

- use ISO-8601 text dates with lexical comparison for the demonstrated fixture;
- use `substr(order_date, 1, 7)` or SQLite `strftime` for month extraction;
- do not assume `DATE '2025-09-01'`, `DATE_TRUNC`, warehouse-specific date functions, or engine-specific casts;
- use standard `JOIN`, aggregate, CTE, window, and `LAG` patterns demonstrated by the suite;
- teach type affinity and SQLite's practical NULL/numeric behavior where it matters.

The curriculum is not reduced to accommodate the engine. SQL topics already deferred by the curriculum—DDL/DML, administration, exhaustive date functions, and dialect-specific optimization—remain outside this spike.

## Lazy loading and shared hosting

The SQL code is isolated under `resources/js/spike/sql/` and is not imported by `resources/js/app.js` or registered in the M0C `ComponentRegistry`. Ordinary lesson response tests confirm that SQL runtime assets are not loaded on normal lessons. The spike uses Vite-generated static assets; the future adapter can self-host those assets on conventional PHP shared hosting without a Node process or server-side SQL runtime.

## Fallback and limitations

If later device testing rejects browser SQL for a target device class, retain the same repository fixture and result-validator contract but provide bounded precomputed result/code tasks and a downloadable SQL notebook/script. Do not move learner SQL execution to Laravel/MySQL.

Known limitations:

- the fixture is intentionally tiny and does not prove production-scale memory behavior;
- browser heap telemetry is available in the tested Chromium context but does not include a complete Wasm RSS measurement;
- the output cap is applied after engine materialization;
- SQLite syntax is not a substitute for teaching every warehouse dialect;
- the UI is only an evaluation harness, not the final editor, schema browser, assessment shell, or M3 experience;
- no low-memory mobile benchmark was run.
