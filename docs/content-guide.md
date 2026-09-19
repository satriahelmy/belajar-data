# BelajarData Content Guide

Status: **REFERENCE STANDARD**

This guide captures the editorial lessons from Module 01. It is a quality reference for future curriculum production, not a rigid lesson template. Authors should read this guide and inspect production-quality Module 01 examples before writing new content.

## Editorial voice

Write like an experienced Indonesian Data Analyst sitting beside a junior analyst and working through a real question.

- Use Indonesian as the main language.
- Keep common working terms such as `row`, `column`, `field`, `grain`, `metric`, `dimension`, `dataset`, and `revenue` when that is how analysts naturally speak.
- Be direct, concrete, concise, practical, and professionally conversational.
- Prefer a question, a small example, an observation, and then the explanation.
- Let the data do some of the teaching before naming the concept.
- Use short questions when they help the learner inspect an example.

Avoid academic exposition, motivational filler, corporate-training language, artificial symmetry, and sentences that merely announce the next section.

## Lesson writing

Start from the learner's likely misconception or the business situation that exposes it. Build the mental model through the smallest useful sequence of examples and counterexamples.

A lesson may include several of these elements, in an order that suits the concept:

- a concrete business question or small table;
- an observation the learner can make;
- the concept name and its analytical consequence;
- a second example or counterexample;
- a reasoning checkpoint;
- a practice activity;
- a takeaway or a natural handoff to the next topic.

Do not force every lesson to include every element, or to use the same number of sections. A normal conceptual lesson should provide roughly 8–15 minutes of meaningful learning including practice, without padding simple concepts.

Endings should create a genuine need for the next topic. For example, after framing a data question, the next problem may be whether the table actually contains the right unit of observation. Do not use generic “next lesson” announcements when a direct connection is clearer.

## Examples and continuity

Use NusaMart when it helps learners carry a mental model across topics. Keep numbers, grain, field names, and definitions consistent with the referenced dataset.

Use a small alternative context when it explains a concept more naturally. Do not force NusaMart into every paragraph.

Show the example before the abstraction when possible. Small tables are often more useful than an extra paragraph of definition.

## Practice

Practice should test a decision or a piece of reasoning, not terminology recall. It should be answerable from the preceding content.

When two activities appear in one lesson, make them progress where useful:

1. recognise the issue or choose the defensible interpretation;
2. make a metric, comparison, or method decision;
3. formulate a question, explanation, or finding in the learner's own words.

Use concise prompts such as “Coba jawab”, “Mana yang paling aman?”, or “Tulis pertanyaanmu.” Keep the existing learner-facing actions such as “Cek jawaban”, “Lihat reference answer”, and “Tandai latihan selesai”.

For open-ended answers, use the existing guided self-assessment flow: learner response → reference answer → checklist. Do not add AI grading or manual/admin grading.

## Terminology

Use terms consistently:

- **business question:** the concern or decision expressed by a stakeholder;
- **data question / analytical question:** a version that states what can be measured and compared;
- **row / observation:** one recorded unit in a table;
- **grain / granularity:** what one row represents;
- **metric:** the quantity or rate being measured;
- **dimension:** a field used to split or give context to a metric;
- **aggregation:** the operation used to summarise values;
- **comparison:** the baseline or reference used to interpret a result;
- **finding:** what the evidence shows changed or differs;
- **evidence:** the data and context supporting a statement;
- **insight:** an interpretation useful for a decision but still bounded by evidence;
- **causal explanation:** a claim about why something happened, requiring stronger evidence than a pattern or time sequence.

Do not use “sales”, “revenue”, “orders”, and “average order value” interchangeably. State the metric and unit when the distinction matters.

## Callouts and section navigation

Use a callout only when it adds a warning or distinction that is not already clear in the paragraph immediately above it. Common mistakes should name a real misconception, not repeat a summary.

Section headings are real learning beats. The right-side navigation is generated from them, so do not add headings only to make the rail look fuller. Prefer useful, natural titles over textbook labels such as “Checkpoint” or “Key Takeaway” when a direct question is clearer.

## Further Reading

Add Further Reading only when it genuinely helps the topic. Prefer 2–4 curated resources where useful, especially official documentation, durable references, or strong explanatory material.

Every item needs:

- a real, reachable URL;
- a title that matches the source;
- a short reason explaining why it helps with this topic;
- no unnecessary duplication with another item in the same module.

Verify external links during content QA. If a source cannot be confidently verified, flag it for human review rather than inventing details.

## Anti-patterns

Remove or rewrite:

- repeated statements of the learning objective;
- “Pada bagian ini kita akan…”, “Selanjutnya kita akan…”, and similar announcements;
- a summary immediately after the same idea was already explained;
- perfectly symmetrical lesson structures;
- long theory before the learner sees the data;
- verbose instructions about how the exercise component works;
- implementation notes about browser enhancement, server persistence, component registration, or development state;
- claims that turn correlation or sequence into causation;
- examples whose numbers, grain, or denominator do not agree.

## Final quality checklist

Before publishing, read the module consecutively rather than lesson by lesson only.

- Does each lesson start from a real question, misconception, or useful example?
- Does the learner leave with a mental model and a decision they can make?
- Are examples, counterexamples, numbers, grain, and exercise answers consistent?
- Does practice test reasoning and lead naturally from recognition to formulation where appropriate?
- Does each lesson hand off naturally to the next without a template announcement?
- Are finding, insight, recommendation, evidence, and causal claims kept distinct?
- Are section headings meaningful and generated into the actual page navigation?
- Are callouts genuinely useful and varied?
- Are Further Reading links relevant, reachable, and non-duplicative?
- Does the content sound like one experienced analyst, not a reusable content template?
- Does the rendered page contain no unnecessary implementation or development language?
