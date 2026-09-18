# M0C Decision Record — Product Foundation

Status: **COMPLETED for the representative product foundation**  
Date: 2026-09-18

## Scope

M0C promotes only the minimum reusable parts of M0A and the technical spikes into a clean repository-first application foundation. It does not implement M1, the final assessment system, or any playground runtime.

## Selected foundation

- Laravel server-rendered routes and Blade views remain the application boundary.
- `League CommonMark` 2.10.1 with the Gate D safe configuration remains the Markdown implementation.
- Repository content uses stable lowercase kebab-case keys and canonical `path.json`, `module.json`, `lesson.md`, and `exercises.json` files.
- `ContentRepository` validates metadata, lesson files, exercise config, and stable lesson keys before rendering.
- `ComponentRegistry` is the server-side allowlist. Only `practice` is registered in M0C; SQL, spreadsheet, Python, and visualization are planned adapters, not active components.
- `DatasetManifestRepository` validates versioned dataset manifests, data dictionaries, table columns, and repository-local file references.
- Laravel's configured cache stores rendered lessons under a source/config hash key. A source or metadata change selects a new cache key; normal deployment cache clearing remains available.
- Vite builds a small application entry point. Registered learning components are lazy-loaded and enhance server-rendered placeholders; heavy runtimes are not imported globally.

## Reusable versus disposable spike work

Retained: Gate decision records, the safe Markdown implementation, representative content fixture, dataset manifest/data dictionary, and focused acceptance tests.

Removed from the product surface: Gate B/C/E spike routes, controllers, views, fixtures, spike JavaScript, and spike-only tests. Chart.js was removed from the application bundle because its Gate E selection belongs to a later visualization adapter milestone.

The downloadable Module 04 notebook remains a static optional asset because it is the documented fallback boundary; no server-side Python runtime was introduced.

## Gate A note

M0C deliberately left SQL as an unregistered future adapter. Gate A is now resolved separately by the representative spike record in `docs/decisions/gate-a-sql.md`; the final SQL Playground remains outside M0C.

## Explicit non-goals

No authentication, learner progress, homepage/Learn UI, full design system, final playgrounds, CMS, arbitrary uploads, Redis, queues, Docker, microservices, server-side learner SQL/Python, or bulk curriculum authoring is included.
