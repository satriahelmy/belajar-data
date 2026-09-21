# M6D: Metric Tree Builder Decision

**Status:** M6D representative interactive implemented

## Decision

Use a bounded native HTML builder for a predefined Revenue metric tree. Learners choose the parent of each registered node from a finite allowlist, then inspect the server-rendered tree preview. The component does not support arbitrary node creation, free-form edges, drag-and-drop graph editing, or a diagram library.

The representative tree is:

```text
Revenue
├── Orders
└── Average order value
    ├── Units per order
    └── Average price per unit
```

The answer is validated as a bounded table of `node_id` and `parent_id` relationships through the existing M2 result contract.

## Evidence demonstrated

- Module 09 lesson and challenge are loaded from repository content.
- A readable fallback table is rendered before JavaScript enhancement.
- The browser preview reflects the selected parent relationships and indents the predefined hierarchy.
- Invalid relationships are limited to the configured options and receive deterministic feedback.
- Correct relationships persist through the existing authenticated attempt endpoint.
- Reset restores the configured starter tree.
- The ordinary Module 01 lesson does not load the metric tree component.
- The fixture and rendered cache boundary have a stable source hash.

## Alternatives considered

### Generic graph or diagram editor

Rejected because it would add interaction and dependency complexity without improving the bounded learning objective. The curriculum requires learners to reason about a small set of metric relationships, not author arbitrary graphs.

### Drag-and-drop node editing

Rejected for the representative slice. It adds pointer and keyboard-state complexity while making answer validation less transparent. Native selects keep the relationship contract explicit and accessible.

### Free-form metric formulas

Rejected because formula parsing and arbitrary node relationships would turn the component into a metric modeling tool. The lesson uses predefined, reviewable relationships instead.

## Contract and boundaries

- Markdown references only `:::metric-tree id="..."`.
- The server resolves the id to a registered `metric_tree_builder` result-based exercise.
- The exercise references `nusamart/v1` and a registered `revenue-tree` fixture.
- Every non-root node has a finite parent allowlist and a configured starter parent.
- The answer contains only the ordered `node_id` and `parent_id` table.
- No arbitrary JavaScript, uploads, node creation, or unbounded graph state is accepted.

## Cache and validation

Metric tree lesson cache keys include the source hash of the registered tree fixture. `content:validate` checks the directive, exercise config, module metadata, and dataset reference before deployment. Feature and frontend tests cover rendering, registry boundaries, relationship persistence, cycle-safe preview generation, cache invalidation, and ordinary-lesson isolation.

## Known limitations

This is one representative Revenue tree. It does not model arbitrary business metrics, formulas, targets, or dashboard dependencies. Additional trees should be added as reviewed configuration and content, not by expanding this component into a general editor.
