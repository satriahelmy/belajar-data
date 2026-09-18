# Gate C — Python/Pandas Browser Feasibility

**Status:** PASSED for the bounded fallback and representative browser feasibility spike only  
**Date:** 2026-09-18  
**Scope:** M0B Gate C; this record does not approve M5 implementation, open-ended Python, or a server-side Python runtime.

## Decision

BelajarData V1 selects bounded code/output exercises with predefined NusaMart data, deterministic expected-output validation, explanations, and downloadable notebooks as the required Module 04 experience.

Pyodide browser execution was not pre-approved by name. The current evaluated candidate was Pyodide **v314.0.7** with Pandas **3.0.2**. It is technically feasible in a module Web Worker, but it is not selected as a V1 curriculum dependency because initialization, memory, and asset costs are too high and mobile/low-memory evidence is not yet sufficient. It may remain an explicitly optional desktop experiment only after a later product decision.

## Candidates and rationale

| Candidate | Evidence demonstrated | Decision |
| --- | --- | --- |
| Pyodide v314.0.7 + Pandas 3.0.2 | Actual browser execution of the representative Module 04 workflow in a module Web Worker; MPL-2.0 Pyodide distribution; supports Pandas in the Pyodide package set. | Rejected as the required V1 path because the runtime is heavy for ordinary shared-hosting learners and no low-memory/mobile baseline has been demonstrated. Retained only as an optional, lazy desktop experiment. |
| `@pyodide/pyodide` 0.17.1 | Package metadata was checked; it is an older, large package path and does not represent the current v314.0.7 distribution used by the spike. | Rejected as a stale/less suitable dependency candidate. |
| Bounded browser code/output + deterministic validation + downloadable notebook | No Python service, no runtime download for ordinary lessons, static/shared-hosting compatible, predictable output checks, and a notebook fallback for fuller local execution. | Selected as the V1 learning contract. |

No server-side Python candidate was evaluated for adoption. The architecture explicitly prohibits sending learner Python to Laravel, and introducing a Python service would violate the approved shared-hosting boundary.

## Representative workflow

The temporary fixture is `datasets/nusamart/v1/python/` and contains eight transaction rows plus four product rows. The browser prototype demonstrated:

- `pandas` import and predefined CSV loading;
- `head`, `shape`, and column inspection;
- column selection, filtering, sorting, calculated revenue, and month extraction;
- left `merge` with product data;
- September revenue comparison and category `groupby`;
- bounded DataFrame rendering and a native HTML bar summary;
- expected result: September revenue `600`, with Electronics `320`, Home `180`, and Grocery `100`.

The page flow is intentionally limited to **Code → Run → Output → Error → Fix → Run again → Reset**. It is not a notebook, terminal, upload area, package manager, or Colab clone.

## Measured browser evidence

Measurements were taken on the local desktop in-app Chromium browser against the pinned jsDelivr URL. They are spike measurements, not a claim of support for every device.

| Measurement | Observed result |
| --- | ---: |
| First uncached runtime initialization | ~5.32 s |
| First uncached Pandas package load | ~6.64 s |
| Subsequent cached runtime initialization | ~2.36–3.15 s |
| Subsequent cached Pandas package load | ~1.04–1.06 s |
| CSV parsing for the fixture | 35.8 ms in the latest run |
| Merge for the fixture | 17.7 ms in the latest run |
| Groupby for the fixture | 11.8 ms in the latest run |
| Representative learner run, including output encoding | ~100.9–134.7 ms |
| Pyodide worker heap | ~74.9 MB |
| Worker reset/recovery | ~6.6 ms in the recovery probe |

The browser Resource Timing API exposed ten relevant entries but did not expose byte sizes in the Worker. Independent range measurements of the pinned CDN assets reported approximately 13.57 MB for the listed WASM, standard-library, Pandas, NumPy, and direct dependency assets before other loader/lock files and compression. The Pyodide deployment documentation describes the full distribution as 200+ MB, so production must not make the full distribution a normal lesson dependency.

The small application-side lazy chunks are versioned Vite assets; the measured Python entry was 7.43 kB raw / 2.90 kB gzip and the worker was 5.38 kB raw. Those numbers exclude the external Pyodide distribution and are not a substitute for runtime asset budgeting.

## Supported subset and limits

The browser spike allowlists `pandas`, `numpy`, `io`, and `datetime` imports and supports only the demonstrated fixture workflow: predefined `read_csv`, inspection, selection, filtering, sorting, calculated columns, dates, merge, groupby, comparisons, and bounded DataFrame/Series output.

Limits are:

- source code maximum: 8,000 characters;
- output maximum: 8,000 characters;
- DataFrame output maximum: 20 rows;
- execution timeout: 15 seconds;
- no uploads, package installation, filesystem, network, terminal, notebook cells, credentials, or arbitrary remote data;
- runtime reset by terminating the Worker and creating a new one.

The preflight token/import check and restricted Python builtins are defense-in-depth for this spike, not a complete security sandbox. Therefore arbitrary learner code must remain browser-only, must never receive secrets, and must not become a server execution path.

## Worker, lazy-loading, and fallback architecture

Ordinary lesson pages do not load the Python entry or Worker. The Python module is a Vite dynamic import activated only on `[data-python-spike]`; the Worker dynamically imports the pinned Pyodide module only after the learner selects **Load browser Python** or **Run Python**. The Worker keeps the UI responsive during runtime initialization and execution. Terminating it provides a recoverable reset path.

The selected V1 fallback has no Pyodide dependency: it uses predefined data, bounded editable code/context, expected-output checks, a reference solution, and a downloadable `.ipynb`. This keeps shared-hosting deployment static and leaves open-ended execution to a learner's own notebook environment.

## Quick visualization decision

The representative task only needs a small visual comparison. The spike renders a native HTML bar summary from the validated DataFrame result and does not load Matplotlib or a charting runtime. Rich charting remains Gate E work; Module 04 does not gain a plotting dependency from this gate.

## Curriculum implications

- **Module 04:** required V1 exercises use bounded code/output checks and downloadable notebooks; the optional Worker experiment may be shown only with explicit desktop guidance.
- **Module 05:** cleaning exercises use bounded predefined tasks and deterministic checks; larger/open-ended cleaning may use downloadable notebooks.
- **Module 06:** EDA exercises use bounded browser interactions or downloadable notebooks, not a mandatory Python runtime.
- **Module 07:** statistics concepts remain understandable without browser Python; any later Python activity uses the same bounded/fallback contract.
- **Module 08:** visualization exercises use the future Gate E decision and native/approved browser visualizations; Python is not a prerequisite.

## Validation and known limitations

Automated coverage includes the server-rendered route, predefined fallback notebook download, Python source import/operation boundary, output truncation, and deterministic fallback validation. Browser smoke testing covered successful initialization, the representative workflow, bounded DataFrame output, native bar output, syntax/runtime error recovery, reset, and fallback validation.

The remaining limitation is device coverage: the spike was measured on a desktop browser only. A future optional-runtime decision must test representative low-memory/mobile devices and confirm asset hosting, MIME types, CORS, offline/cache policy, and accessibility before retaining Pyodide. Until then, the bounded fallback is the approved V1 path.

## References

- Pyodide usage and browser compatibility: https://pyodide.org/en/stable/usage/index.html
- Pyodide deployment and distribution size: https://pyodide.org/en/stable/usage/downloading-and-deploying.html
- Pyodide Web Worker guidance: https://pyodide.org/en/latest/usage/webworker.html
