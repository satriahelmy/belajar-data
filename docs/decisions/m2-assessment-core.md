# M2 Assessment Core Decision Record

Status: COMPLETE

## Scope

M2 establishes the reusable assessment contract before SQL, spreadsheet, Python, visualization, and project-specific interactives are built. The implementation stays inside the Laravel modular monolith and keeps the existing server-rendered lesson pipeline.

## Selected approach

- Exercise definitions remain repository-owned JSON beside each lesson or challenge.
- Laravel validates exercise configuration before rendering or accepting an attempt.
- A small PHP validator handles `multiple_choice`, `multi_select`, `numeric`, `text_self_assessment`, and `result_based`.
- The browser uses the same normalized answer contract for progressive feedback. The server remains the persistence and authorization boundary for authenticated learners.
- A single `learning_attempts` table stores the latest useful answer, score where applicable, status, attempt count, and timestamps. A unique `(user_id, exercise_key)` constraint makes completion idempotent.
- `practice` remains the only registered browser component. Its public contract is `initialize(config)`, `getAnswer()`, `validate()`, `reset()`, and `emitProgress()`.

## Why this is the simplest fit

The approach uses Laravel validation, Eloquent, MySQL, and the existing Vite entry without adding an assessment framework, queue, Redis, SPA runtime, or server-side execution environment. It supports deterministic checks and guided self-assessment while keeping answer keys browser-visible as allowed for V1.

## Supported validation contract

- Categorical answers use exact option indexes.
- Numeric answers use an explicit expected value and tolerance.
- Result tables define columns, rows, row ordering, required columns, numeric tolerance, and null behavior.
- Open-ended answers are stored for the learner and use a reference answer plus checklist. They are never AI-graded or presented as objectively scored.

## Security and persistence boundary

Markdown cannot create exercises or JavaScript. Exercise IDs and stable keys are resolved against repository content before an attempt is accepted. Authenticated attempt routes are protected by Laravel auth and query only the current learner's attempts. Answer payloads are bounded before persistence, and only normalized answer fields are stored.

The browser may validate for immediate feedback, but the authenticated response from Laravel is authoritative for persisted state. Guest interaction remains available without account persistence.

## Validation and cache strategy

`php artisan content:validate` loads every registered lesson and published challenge, validates exercise configuration, resolves every practice directive, renders the Markdown, and validates datasets. Rendered lesson HTML remains cached by source hash, so content changes naturally produce a new cache key. Attempts are not content-render caches and remain keyed by the stable exercise key.

## Alternatives considered

- A third-party assessment engine was rejected because the V1 question set is small and configuration-driven; it would add dependency and authoring complexity without solving the specialized result contract.
- A separate API or SPA assessment layer was rejected because the product architecture is server-rendered Laravel with progressive enhancement.
- AI or manual grading was rejected because open-ended V1 work is explicitly guided self-assessment.
- Separate practice and challenge attempt tables were deferred. The stable exercise key already distinguishes both scopes and the single table keeps the first persistence slice simple.

## Completion evidence

Dependency-free frontend runtime tests cover the Practice Shell interactions and semantic accessibility contract. Manual local-browser smoke coverage confirms the built asset handles mount, hint, incorrect/correct feedback, reset, keyboard interaction, self-assessment, reference answer, checklist completion, and progress state. Current production content exercises use multiple choice and self-assessment; the remaining validator types are covered by contract tests and ready for later content fixtures.
