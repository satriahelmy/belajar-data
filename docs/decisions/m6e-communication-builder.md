# M6E: Communication Builder Decision

**Status:** M6E representative interactive implemented

## Decision

Use a bounded native HTML Communication Builder with five structured fields:

1. headline
2. evidence
3. known
4. unknown
5. next_step

Learners write an evidence-led finding, review the configured reference answer,
and check a short self-assessment checklist. The component uses the existing
`text_self_assessment` attempt contract. It does not perform semantic grading,
AI grading, or server-side text execution.

## Evidence demonstrated

- Module 12 lesson and challenge are loaded from repository content.
- A server-rendered fallback explains the five-part communication structure.
- The browser component progressively enhances the fallback with bounded textareas,
  reference-answer review, checklist state, feedback, and reset.
- All five fields are required before the learner can complete the exercise.
- Structured fields and checklist state persist through the existing authenticated
  attempt endpoint.
- An incomplete field remains incomplete on the server; a complete set of fields
  still uses reference/checklist self-assessment rather than semantic scoring.
- The ordinary Module 01 lesson does not load the Communication Builder.
- The lesson cache key includes the repository source hash.

## Alternatives considered

### Free-form prose only

Rejected because the learning objective requires learners to distinguish a
headline, evidence, known facts, unknowns, and a next step. Structured fields
make that reasoning visible without requiring automated language evaluation.

### AI or semantic text grading

Rejected because V1 must remain understandable and complete without AI, and
semantic grading would add privacy, reliability, cost, and explainability
problems. Reference answers and a checklist are sufficient for this bounded
self-assessment.

### Rich-text editor or communication template builder

Rejected because formatting and arbitrary authoring are outside the analytical
communication objective. Native textareas keep the interaction accessible,
testable, and shared-hosting friendly.

## Contract and boundaries

- Markdown references only `:::communication-practice id="..."`.
- The server resolves the id to a registered `text_self_assessment` exercise
  with `interactive.type` set to `communication_builder`.
- The exercise configuration defines the exact field order, labels, reference
  answer, checklist, and optional desktop guidance.
- The browser sends bounded strings and checklist booleans only.
- No arbitrary JavaScript, HTML, uploads, or learner-generated component config
  is accepted.

## Cache and validation

Communication lesson cache keys include the repository source hash. The content
validator checks the directive, exercise type, interactive field contract,
module metadata, and dataset reference before deployment. Feature and frontend
tests cover rendering, field validation, persistence, incomplete state, reset
helpers, registry boundaries, cache invalidation, and ordinary-lesson isolation.

## Known limitations

This slice cannot judge whether a learner's wording is analytically correct.
The learner uses the reference answer and checklist to self-assess. Future
communication exercises should remain bounded and reviewable rather than turning
the component into an AI tutor, generic writing editor, or grading service.
