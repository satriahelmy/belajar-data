# M7: Tableau Learning Decision

**Status:** M7 representative Tableau learning track implemented; external-tool walkthrough pending

## Decision

Use Tableau as an external professional tool. BelajarData owns the safe,
versioned dataset, guided instructions, deterministic checkpoints, and
self-assessment. Tableau owns the actual connection, worksheet, relationship,
calculated field, dashboard, and interaction workflow.

The representative track contains three topics:

1. dataset download, version, and grain;
2. relationships and defined metrics;
3. dashboard construction and analytical validation.

The Module 10 challenge combines the workflow into a Revenue Slowdown case.
The same dataset page also exposes an orders/customers pair for a Customer
Retention workflow, so the two launch project directions have a documented
Tableau entry point without building project workspaces in M7.

## Dataset contract

NusaMart v1 remains fictional and repository-owned. Tableau CSV files live in
`datasets/nusamart/v1/tableau/` and are referenced by the versioned manifest.
The download page shows the dataset version, file size, and SHA-256 checksum.
Downloads are bound to an explicit allowlist of four manifest entries:
transactions, products, orders, and customers.

## Checkpoint contract

M7 uses the existing deterministic M2 contracts:

- multiple choice for grain, relationship, and calculated-field reasoning;
- numeric checkpoints for total revenue, September revenue, and growth;
- multi-select for dashboard validation;
- text self-assessment with reference answer and checklist for the final finding.

No Tableau workbook is uploaded, parsed, simulated, or automatically graded.

## Privacy boundary

Tableau Public publishing is optional. The warning is shown in the lesson,
challenge, and dataset download guidance: confidential, private, proprietary,
and company data must never be published to Tableau Public. NusaMart is safe
for the representative public exercise.

## Supported workflow assumptions

- The learner has access to Tableau Desktop or Tableau Public in a desktop
  browser/application environment.
- CSV files are opened through Tableau's normal text-file connector.
- The learner returns to BelajarData for numeric checkpoints and self-assessment.
- Advanced LOD expressions, advanced table calculations, workbook parsing, and
  dashboard simulation remain outside V1.

Feature tests cover download metadata, checksum shape, manifest-bound files,
published content, links, privacy copy, and the absence of a Power BI/workbook
parser path. Browser smoke verifies the lesson, practice feedback, download
metadata, and privacy warning. Tableau Desktop 2025.1 was opened as the candidate
local walkthrough version, but the native flow was stopped before the CSV
connector step. A final native Tableau Desktop/Public walkthrough against the
supported launch version remains open. The native walkthrough must
verify that the NusaMart CSV opens through Tableau's normal text-file connector
and that the guided field/relationship/calculated-field assumptions remain valid;
it must not publish data or upload a workbook.
