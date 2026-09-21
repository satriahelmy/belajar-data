# M6B — JOIN Row Multiplication Decision

**Status:** M6B representative interactive implemented.

## Decision

BelajarData uses a bounded `join-grain-playground` component for the JOIN Row Multiplication learning object. The component presents only predefined NusaMart table pairs and join scenarios. It does not expose a general query builder, arbitrary table editor, or free-form relationship graph.

The representative scenarios are:

- `orders` to `order_items` on `order_id`, demonstrating one-to-many row multiplication;
- `order_items` to `products` on `product_id`, demonstrating a many-to-one context join;
- an intentionally wrong `product_id` key between `orders` and `order_items`, demonstrating an empty result as a relationship warning.

## Learning and validation contract

The learner sees each table's grain, columns, join key, left row count, right row count, joined row count, preview rows, and a grain warning. The deterministic answer contains only the selected scenario, join key, and row counts. The existing M2 result validator and attempt persistence handle completion; no arbitrary SQL or code is sent to Laravel.

The lesson and challenge use the canonical SQL fixture subset under `datasets/nusamart/v1/sql/`. The component performs only the three registered scenario joins in the browser and caps the displayed preview. Its server-rendered fallback remains readable if JavaScript does not load.

## Security and cache boundary

Markdown references only a registered component and an exercise key. The renderer validates the component type, dataset key/version, allowed scenario list, and starter scenario before emitting safe JSON. The rendered lesson cache key includes the joined fixture source hash. No user-authored table, relationship, query, or executable configuration is accepted.

## Limitation

This slice teaches grain and row multiplication. It does not replace the SQL Playground and does not implement arbitrary JOIN authoring, relationship modeling, or automatic diagnosis of learner SQL.
