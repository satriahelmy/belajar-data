# Module 01 Content Production Record

Status: **COMPLETED for the Module 01 content benchmark**  
Date: 2026-09-19

## Scope

Module 01 — *Thinking with Data* is the first production-quality content slice. It validates the authoring contract, lesson progression, practice references, canonical NusaMart continuity, and the server-rendered challenge flow. It does not start M1B/M1C, authentication, learner progress, or the M2 assessment persistence system.

## Canonical topic sequence

1. What Does a Data Analyst Actually Do?
2. From Business Question to Data Question
3. Understanding Data & Tables
4. Metrics & Dimensions
5. Granularity: What Does One Row Represent?
6. Aggregation & Comparison
7. From Data to Insight

The sequence follows `docs/curriculum.md`: question before tool, then data structure, metric/dimension, grain, aggregation/comparison, and evidence-limited insight. The content-depth pass preserves that sequence while making each lesson a progression from misconception to mental model, example/counterexample, reasoning checkpoint, takeaway, and next-topic transition.

## Case and dataset boundary

The module uses the existing `datasets/nusamart/v1/transactions.json` fixture and does not create a contradictory NusaMart world. Its current eight-row sample aggregates to revenue 450 for August 2025 and 600 for September 2025. The challenge deliberately tests the initial “sales turun” claim and teaches that the claim is not supported by this comparison. Region breakdown is shown as evidence, while causality remains an unknown.

The fixture is still representative rather than a production-scale dataset. Later modules may add reconciled dataset profiles only through an explicit dataset/version decision.

## Practice and assessment boundary

Each topic has two registered practice references, mixing deterministic multiple-choice checks with guided `text_self_assessment` where the learner must frame, explain, or write a finding. The challenge has six deterministic checkpoints plus one `text_self_assessment` config containing a reference answer and checklist. The current practice enhancement runs in the browser for the session only; answers and completion are not persisted. Persistence, attempts, broader validators, and challenge aggregation remain M2/M1B work.

## Content-depth pass

The seven lessons are estimated at 10, 13, 12, 10, 12, 14, and 13 minutes respectively (84 minutes total), with a 20-minute synthesis challenge (104 minutes total). The estimate reflects reading, examples, and practice rather than prose volume. The challenge now includes a secondary-metric checkpoint comparing revenue with average order value before the learner writes the final finding.

An editorial voice pass then removed repeated lesson-template language, duplicated recaps, generic “next section” framing, and implementation-facing practice copy. Lessons now vary their section rhythm and more often begin with a question or small table before naming the concept. The rewrite clarified the difference between revenue, orders, average order value, grain, finding, insight, and causal claim without reducing the underlying learning progression.

## Editorial rules demonstrated

- Indonesian is the primary language; standard terms such as metric, dimension, grain, comparison, finding, insight, and evidence remain in English where useful.
- Narrative is Markdown; exercises and reference answers are structured JSON; no lesson content is hardcoded in Blade.
- The module contains tables, a bounded text/code example, callouts, links, and registered practice directives.
- No AI module, mandatory AI section, AI feature, AI tutor, or AI grading is required for the learning outcomes.
- Further Reading uses verified durable Tableau/IBM resources and is kept with topic metadata.

## Reusable patterns for Modules 02–12

- Keep one stable module/topic key and one `lesson.md` plus `exercises.json` pair.
- Start with an explicit business question and learning outcome before introducing a tool.
- State grain, metric, dimension, comparison, and evidence limits near the first relevant example.
- Use bounded datasets with a data dictionary and show the unit of observation before aggregation.
- Use deterministic checks for categorical decisions and guided self-assessment for written findings.
- Give each normal lesson multiple meaningful concept beats and at least one reasoning opportunity; do not add headings or prose solely to populate the section rail.
- End each lesson with an explicit takeaway and a natural transition to the next topic or synthesis challenge.
- Treat challenge evidence as a documented dataset reference, not as an independently regenerated business world.
