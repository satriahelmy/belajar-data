# M3 SQL Playground Decision Record

Status: accepted for the M3 representative implementation

## Selected approach

Use `sql.js` 1.14.2, the Gate A selected SQLite-compatible WebAssembly runtime, inside a dedicated browser Web Worker. The Laravel page renders the lesson and a structured SQL component server-side. The component receives the versioned `nusamart/v1` fixture and result validator configuration from repository content, then lazy-loads the runtime through the frontend component registry only when a SQL playground is present.

The server never receives learner SQL for execution. Authenticated learners may send only the bounded result table to the existing M2 attempt endpoint for deterministic result validation and latest-attempt persistence.

## Evidence demonstrated

- The published Module 03 topic renders from repository Markdown and resolves a registered `sql-playground` directive.
- The challenge slice covers table grain, monthly aggregation, dimension JOINs, and a diagnostic order-level JOIN multiplication case.
- The browser worker creates only predefined fixture tables and accepts only `SELECT` or `WITH` queries.
- Query output is capped at 100 rows and execution is terminated after the 2-second worker timeout.
- The editor preserves the learner query after syntax or runtime errors.
- Result checks compare columns and normalized rows, with ordered/unordered modes and numeric tolerance supported by the shared assessment contract.
- The ordinary Module 01 lesson does not load the SQL runtime assets.

## Boundaries and supported subset

The runtime supports the documented SQLite-compatible analytical subset used by the representative query suite: projection, `WHERE`, `ORDER BY`, `LIMIT`, aggregates, `GROUP BY`, joins, `CASE`, CTEs, date extraction with the fixture's ISO date strings, `ROW_NUMBER`, and `LAG`. Mutation and administration statements such as `INSERT`, `UPDATE`, `DELETE`, `ATTACH`, `DETACH`, `PRAGMA`, schema changes, and extension loading are blocked.

The schema browser is intentionally a fixture browser, not a database administration interface. It exposes table names, columns, grain, row counts, and a small preview context without upload, remote connections, or arbitrary tables.

## Alternatives and why they were not selected

- DuckDB-Wasm was rejected for M3 because Gate A evidence showed that `sql.js` covered the required representative curriculum queries with a smaller operational surface and simpler shared-hosting asset delivery. DuckDB remains a future reconsideration only if the approved SQL curriculum outgrows the current subset.
- Server-side MySQL execution was rejected by the architecture and security boundary. The application database is for learner/application state and must never execute learner SQL.
- A generic browser database IDE was rejected because it would expand the scope beyond bounded learning tasks and weaken the fixture, output, and security constraints.

## Cache and invalidation

Lesson HTML remains cached by the existing repository source hash. A change to Markdown, exercise configuration, topic metadata, or the referenced fixture contract changes the content/source inputs and requires a new cache key. The browser runtime is loaded from the Vite build manifest and its worker is reset when the learner chooses Reset runtime or when the execution timeout fires.

## Known limitations

The current M3 content is a representative Module 03 slice, not the full SQL curriculum. The result validator intentionally checks the normalized output table rather than proving that a particular query plan was used. Browser-side expected outputs are not an integrity boundary; authenticated persistence revalidates the bounded result on Laravel. Large intermediate SQLite work remains bounded by the small versioned fixture and output cap, while a future larger fixture should add an explicit memory/plan budget before publication.
