# M8: Projects Decision Record

**Status:** Representative M8 implementation complete; manual end-to-end QA remains open

## Scope implemented

M8 uses the six-stage runtime resolved in D-04:

`brief → understand → plan → investigate → validate → communicate`

“Build Your Evidence” is represented as a substep inside Investigate and
Validate. Reference Approach is a separate repository document and is opened
after an attempt or an explicit confirmation. It is not a hidden progress stage.

Two projects are published:

- NusaMart Revenue Slowdown;
- Customer Retention Analysis.

Delivery Performance Investigation is represented as a roadmap project so the
three-project library remains extensible without pretending the third project
is launch-ready.

## Repository contract

Project metadata lives in `content/projects/{project-key}/project.json`. Each
published project defines stable stage keys, stage Markdown, one checkpoint
configuration per stage, dataset references, tool guidance, outcomes, and a
separate `reference.md`. `ProjectRepository` validates metadata, safe relative
paths, exercise contracts, stage files, and published reference files before
deployment.

Markdown is rendered through the existing safe Laravel Markdown pipeline. The
project workspace does not embed arbitrary JavaScript or prescribe a hidden
sequence of SQL, Python, spreadsheet, or Tableau commands.

## Progress contract

The `user_project_progress` table stores only learner state: user, stable
project key, status, current stage key, and timestamps. Answers and checkpoint
attempts reuse the existing bounded `learning_attempts` contract under a
`project/{project}/{stage}/{exercise}` key. Topic progress and project progress
remain independent, and project completion is not an access prerequisite for
the learning path.

## Assessment boundary

The representative projects combine multiple-choice, numeric, and guided
self-assessment checkpoints. Open-ended findings use the existing reference and
checklist pattern. The system does not parse arbitrary dashboards/workbooks,
run learner SQL or Python on Laravel, or use AI/manual grading.

## Remaining QA

Feature tests cover repository publication, stable keys, public access,
reference confirmation, auth boundaries, idempotent start/resume, checkpoint
validation, and persisted answers. A manual browser walkthrough of both
published projects with the approved tool combinations remains a launch QA
task for M9 hardening.
