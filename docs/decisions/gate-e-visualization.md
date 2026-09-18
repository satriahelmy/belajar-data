# Gate E — Visualization Library & Bounded Charting

**Status:** PASSED for the representative bounded visualization spike only  
**Date:** 2026-09-18  
**Scope:** M0B Gate E; this record does not approve the final M6 Visualization Playground, Module 08 authoring, dashboard building, or Tableau implementation.

## Decision

Select **Chart.js 4.5.1** as the browser charting library for the demonstrated bounded adapter. It is MIT-licensed, maintained, compatible with the existing Vite/Laravel progressive-enhancement pipeline, and small enough to lazy-load for visualization lessons.

Chart.js is not exposed to authored Markdown or learner configuration. Lessons will reference a registered visualization configuration. The adapter owns the library-specific options and accepts only predefined dataset references, metrics, dimensions, chart types, sorting, highlighting, and axis framing.

The spike intentionally uses two bounded derived representations rather than adding a plugin: histogram is a predefined bar-bin transformation, and distribution is an IQR band plus median marker. This covers the curriculum requirement without turning the application into a general chart authoring system. A full boxplot plugin remains unapproved.

## Candidates evaluated

| Candidate | Evidence | Decision |
| --- | --- | --- |
| Chart.js 4.5.1 | MIT; npm package tarball 1.58 MB, unpacked 6.18 MB; browser smoke-tested with actual bar, line, scatter, histogram, composition, and IQR/median rendering; Vite lazy chunk measured at 208.27 kB raw / 71.53 kB gzip. | Selected. The API, lifecycle (`new Chart`, `resize`, `destroy`), responsive behavior, and tree-shakable controllers fit the bounded adapter and shared-hosting deployment model. |
| Apache ECharts 6.1.0 | Apache-2.0 and broad built-in support including bar, line, scatter, pie, and boxplot; npm package tarball 12.18 MB, unpacked 60.30 MB, with a 1.12 MB minified ESM distribution. | Rejected for this spike because its feature surface and asset/dependency footprint are disproportionate to bounded learning visuals. Its accessibility capabilities are useful, but the same outcome can be achieved here with a table and text explanation. |
| Observable Plot 0.6.17 | ISC; npm package tarball 403 kB, unpacked 1.53 MB; concise declarative marks and built-in bin/box-style marks. | Rejected after API/fit review because the interaction, responsive lifecycle, and destroy/re-render contract would require more application-owned adapter behavior for this Laravel enhancement path. Its small asset size is attractive, but it did not provide a decisive benefit over the tested Chart.js path. |

The versions and package sizes above are evaluation evidence from the current npm registry, not a blanket promise that future releases will have identical sizes. No ECharts or Observable Plot package was installed in the application.

## Representative dataset and chart contract

The spike uses the existing versioned `datasets/nusamart/v1/` fixture. The server provides raw transactions/products plus this config shape:

```json
{
  "dataset": "nusamart-sales-v1",
  "allowed_metrics": ["revenue", "quantity", "orders", "average_order_value"],
  "allowed_dimensions": ["month", "category", "region", "channel"],
  "allowed_charts": ["bar", "line", "scatter", "histogram", "composition", "distribution"],
  "allowed_sorts": ["descending", "ascending", "chronological"],
  "allowed_highlights": ["none", "top", "West", "Electronics"],
  "allowed_scale_modes": ["honest", "truncated"]
}
```

The adapter maps the configuration to Chart.js. It never accepts raw Chart.js options, arbitrary fields, calculated fields, dataset URLs, or executable configuration.

Demonstrated representations:

- bar: revenue/quantity/orders by a selected predefined dimension;
- line: ordered month trend;
- scatter: fixed quantity x revenue relationship by order;
- histogram: three predefined order-revenue bins;
- composition: stacked monthly revenue by product category;
- distribution: minimum, Q1, median, Q3, maximum table plus IQR/median chart.

The prototype demonstrated sorting, highlighting, switching chart type, changing metric/dimension, and the intentionally misleading truncated-axis example. The table and text description update with every state.

## Accessibility and responsive behavior

Chart.js renders to canvas, so the chart is not treated as the only accessible carrier. The prototype provides:

- native labelled `select` controls and buttons;
- keyboard-reachable control flow and visible focus styles;
- a canvas role/name and live chart description;
- an adjacent semantic table with current values;
- a text warning for the truncated-axis example;
- labels, values, and sorting that do not depend on color alone;
- responsive Chart.js container sizing and an explicit resize probe;
- no looping animation; reduced-motion risk is minimized by disabling chart animation in the spike.

Chart.js documents responsive resizing through a dedicated container and `responsive` configuration. The spike follows that model and calls `resize()` from a `ResizeObserver`. [Chart.js responsive documentation](https://www.chartjs.org/docs/latest/configuration/responsive.html)

## Lazy loading, performance, and lifecycle evidence

The normal application entry only detects `[data-visualization-spike]` and dynamically imports the visualization entry. The entry dynamically imports Chart.js only after **Load visualization**. Normal lesson pages therefore do not request the chart chunk.

Observed local desktop in-app Chromium measurements:

| Measurement | Observed result |
| --- | ---: |
| First lazy library load after page open | 135.1 ms |
| Subsequent cached lazy load | 29.4 ms |
| Initial chart render | 19.2–80.7 ms |
| Control rerender | 0.3–12.1 ms |
| Resize probe | 4.8 ms |
| Cleanup/destroy | below 0.1 ms, displayed as 0.0 ms at one-decimal precision |
| Sequential renders exercised | 12 renders in the final browser run |
| Visualization entry chunk | 9.94 kB raw / 3.96 kB gzip |
| Chart.js lazy chunk | 208.27 kB raw / 71.53 kB gzip |

These are representative smoke measurements on one desktop browser and a tiny eight-row dataset. They are not a low-end mobile performance certification. The Vite build produces static hashed assets suitable for shared hosting; no Node process is required at runtime.

## Fallback and boundary

If Chart.js is unavailable, the bounded table remains the essential explanation. The final product may use the same config-driven adapter or a simpler table-first exercise. No fallback may introduce arbitrary uploads, a calculated-field engine, relationship modeling, dashboard pages, or Tableau/Power BI simulation.

The Visualization Playground is a bounded learning object for choosing and interpreting a visual. Tableau learning remains a separate professional-tool workflow. Chart.js does not become a substitute for Tableau, Power BI, or a dashboard builder.

## Curriculum implications

- **Module 06:** bounded charts support question-driven EDA: category comparison, month trend, distribution, and relationship. Open-ended Pandas remains covered by the Gate C fallback/notebook decision.
- **Module 07:** scatter and distribution representations support intuition about variation and relationships; statistical inference is not hidden inside the chart library.
- **Module 08:** Chart.js is an implementation detail behind tool-agnostic lessons on comparison, trend, composition, distribution, relationship, encoding, sorting, and misleading axes.
- **Module 09:** this spike supports metric/context exercises only; it does not build a dashboard builder or metric-tree workspace.
- **Tableau:** professional BI visualization remains the separate Tableau-only V1 path already resolved by D-02.

## Known limitations

- The spike uses a provisional eight-row NusaMart fixture; it does not benchmark large datasets or production visual stories.
- Canvas charts need the adjacent table/text fallback used here; this is a deliberate accessibility boundary.
- A full boxplot, histogram bin editor, arbitrary reference lines, and custom color/encoding authoring are not supported.
- Low-end mobile/browser-device testing is not completed.
- The final M6 API, lesson integration, persistence, progress, and assessment contracts remain future work.

## References

- Chart.js documentation and MIT licensing: https://www.chartjs.org/docs/
- Chart.js scatter configuration: https://www.chartjs.org/docs/latest/charts/scatter
- Chart.js responsive configuration: https://www.chartjs.org/docs/latest/configuration/responsive.html
- Apache ECharts feature and accessibility comparison: https://echarts.apache.org/en/feature.html
- Apache ECharts ARIA guidance: https://echarts.apache.org/handbook/en/best-practices/aria/
- Observable Plot overview: https://observablehq.com/framework/lib/plot
