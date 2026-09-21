# M6A — Visualization Playground Decision

**Status:** M6A representative Visualization Playground implemented. The remaining four high-value interactives are not included in this slice.

## Decision

The M6A visualization component uses Chart.js 4.5.1, selected by Gate E, behind a registered and lazy-loaded `visualization-playground` adapter. Lessons provide finite configuration only. Learners can change a predefined metric, dimension, chart type, sort, highlight, and scale mode against the versioned NusaMart fixture.

The component is a bounded learning object. It is not a dashboard builder, a calculated-field engine, an upload tool, or a Tableau simulation.

## Implemented contract

The renderer validates the component reference and exercise configuration before exposing it to the browser. The public configuration contains only the approved dataset, finite option lists, starter state, deterministic result validator, and bounded fixture data. Raw Chart.js options and executable configuration never cross the content boundary.

The representative adapter supports:

- bar and line comparison;
- scatter relationship view;
- fixed revenue histogram bins;
- composition by month and category;
- distribution summary with minimum, quartiles, median, and maximum;
- sorting, highlighting, honest or truncated scale framing;
- a native table and text explanation adjacent to every chart state.

The selection state is checked as a deterministic table answer through the existing M2 attempt contract. Laravel receives only the finite selection and never receives a chart runtime instruction or arbitrary data operation.

## Rendering, accessibility, and performance

The lesson includes a server-rendered fallback table before JavaScript loads. The browser lazily imports the visualization component and then Chart.js only on pages that contain the registered component. Chart animation is disabled, resize is observed, controls are native labelled selects, and the table and explanation remain the accessible source of exact values.

The lesson cache key includes the visualization fixture source hash. Changes to the canonical dataset invalidate the rendered lesson cache without requiring distributed infrastructure.

## Alternatives and limits

Gate E rejected Apache ECharts and Observable Plot for the bounded V1 adapter because their feature surface or adapter complexity did not justify the additional footprint. No alternative charting package was installed.

This slice does not implement JOIN Row Multiplication, Sampling & Uncertainty, Metric Tree Builder, or Communication Builder. It also does not provide arbitrary chart authoring, uploads, calculated fields, dashboard pages, or automatic causal interpretation. Those remain separate M6 work.
