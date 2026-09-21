# M5 — Python/Pandas Practice Decision

**Status:** Selected bounded V1 fallback, implemented for the representative Module 04 slice.

## Decision

BelajarData V1 uses a bounded browser code/output exercise for Python/Pandas practice. The learner edits a starter snippet against predefined NusaMart CSV fixtures, and the browser checks the required analysis steps against deterministic expected output configured in repository content.

This is a learning contract, not an open Python runtime. The browser does not execute arbitrary Python, the source is never sent to Laravel, and Laravel has no Python execution route or service. Learners who need a fuller notebook environment can download the representative `.ipynb` file.

## Evidence and alternatives

Gate C demonstrated that Pyodide 314.0.7 with Pandas 3.0.2 can run the representative workflow in a Web Worker, but initialization, memory, asset cost, and incomplete mobile or low-memory evidence make it unsuitable as a required V1 dependency. The Gate C record remains the evidence for that conclusion.

The following alternatives were considered:

- **Pyodide plus Pandas:** rejected as the required V1 path because the runtime cost is too high for the shared-hosting and ordinary learner baseline. It remains an optional future experiment only after a separate product and device decision.
- **Server-side Python or a Python service:** rejected because it violates the approved deployment boundary and would send learner code to server infrastructure.
- **A full browser notebook/editor runtime:** rejected for the representative slice because it adds runtime and authoring complexity without improving the bounded learning outcomes demonstrated by the exercises.

No new PHP, Python, or JavaScript package was installed for M5.

## Learning and component contract

The registered `python-practice` component receives a validated `python_practice` exercise config containing:

- the canonical dataset key and version;
- a starter code snippet;
- a bounded list of required analysis operations;
- deterministic expected output and metrics;
- a fixed notebook download path;
- learner-facing prompt, feedback, and desktop guidance.

The supported representative operation contract covers loading the provided CSVs, inspecting shape and rows, selecting/filtering, sorting, calculated columns, date periods, merging, grouping, and tabular output. The component accepts only a bounded source length of 8,000 characters, at most eight required operation tokens, at most 20 fixture rows in the rendered result, and at most 8,000 characters of output. It rejects unsafe imports/calls, filesystem or network access patterns, dynamic execution, and missing required operations.

The result submitted to Laravel contains only the bounded result table and configured metrics. It does not contain learner source code. Completion uses the existing M2 deterministic result validator and attempt contract.

## Content and data boundary

The representative Module 04 lesson and challenge use `nusamart/v1` with repository-owned `transactions.csv` and `products.csv` fixtures. The lesson progresses from inspection to filter/sort, derived context, merge, and aggregate comparison. The challenge uses September category revenue and a guided finding response.

The renderer validates the exercise reference, component type, dataset key/version, starter code, operation tokens, and notebook URL before rendering. The Python fixture source hash is part of the rendered lesson cache key, so changing the fixture invalidates the cached lesson output.

## Known limitations

This fallback does not provide open-ended Python execution or arbitrary DataFrame exploration in the browser. The notebook download is the escape hatch for fuller local execution. Device and low-memory performance coverage for a future Pyodide experiment remains outstanding and is intentionally not treated as M5 completion evidence.
