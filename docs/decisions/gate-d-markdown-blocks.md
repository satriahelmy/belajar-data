# Gate D Decision — Markdown + Structured Interactive Blocks

Status: **PASSED for the representative technical spike**  
Date: 2026-09-18  
Scope: one temporary Module 01 lesson and one registered multiple-choice practice placeholder.

## Selected approach

- Markdown parser: `league/commonmark` 2.10.1, directly declared in `composer.json`.
- Converter: `GithubFlavoredMarkdownConverter` for CommonMark plus GFM tables.
- Parser safety configuration:
  - `html_input = strip`;
  - `allow_unsafe_links = false`.
- Structured blocks: a small PHP line-oriented directive parser with an explicit directive and attribute allowlist.
- Rendering: server-rendered Blade view with trusted HTML templates for callouts and practice mounts.
- Enhancement: Vite-bundled JavaScript detects registered practice mounts and enhances their status without receiving executable code from Markdown.
- Validation: spike-level `php artisan content:validate` scans lesson files, loads adjacent exercise JSON, renders directives, and fails on malformed/missing references.

## Evidence demonstrated

- Repository lesson loaded from `content/data-analyst/01-thinking-with-data/01-analyst-role/lesson.md`.
- Exercise configuration loaded from adjacent `exercises.json`.
- Headings, paragraphs, lists, GFM tables, fenced SQL code, links, images, callouts, and practice directives render in the server response.
- A practice directive resolves by stable `id` and matching `type` to structured exercise data.
- Raw HTML/script tags are stripped and unsafe `javascript:` links are not emitted as active links.
- Unknown directives, unknown attributes, invalid practice attributes, and missing practice references fail with `ContentValidationException`.
- Heading metadata is extracted from rendered HTML for a future lesson outline.
- Cache keys include a SHA-256 hash of the lesson and exercise configuration, so content/config changes use a new cache entry. Deployment cache clearing remains available through Laravel's normal cache commands.
- Vite build and progressive enhancement are proven by the generated `public/build` assets and the practice mount script.

## Alternatives evaluated

### Parsedown

Composer metadata exposed stable 1.8.0 releases and a 2.0 beta/dev line. It was not selected for this spike because CommonMark provided the required GFM table support, explicit safe parser options, and a maintained extension-oriented API already compatible with the Laravel dependency set.

### HTML Purifier

`ezyang/htmlpurifier` 4.19.1 is a maintained HTML sanitizer and remains a possible future defense if authored raw HTML is ever deliberately enabled. It was not added for this spike because raw HTML is disabled at the Markdown parser boundary; adding a second HTML policy engine would increase dependency/configuration complexity without improving the demonstrated allowlisted content model.

## Allowed Markdown subset

The prototype permits:

- headings;
- paragraphs and line breaks;
- ordered/unordered lists;
- GFM tables;
- fenced code blocks;
- links and images subject to CommonMark's unsafe-link filtering;
- `callout` and `practice` registered blocks.

The prototype does not permit raw HTML, embedded scripts, arbitrary attributes, arbitrary directives, front matter, or authored JavaScript.

## Directive grammar

Supported form:

```text
:::callout type="common-mistake"
Plain callout text.
:::

:::practice type="multiple_choice" id="thinking-analyst-01"
:::
```

Rules:

- Only `callout` and `practice` are registered.
- `callout` accepts only `type` from `note`, `important`, `common-mistake`, or `warning` and requires a non-empty body.
- `practice` accepts only `type` and `id`, requires an empty body, and requires a matching exercise config.
- Attribute names/values are constrained to safe machine-key formats.
- Unknown, duplicate, malformed, or unclosed directives fail validation.

## Registered component lookup contract

`practice.id` resolves against the `exercises` map loaded from the lesson's adjacent `exercises.json`. The directive `type` must match the registered config type. The renderer emits only a public config subset (`id`, `type`, `prompt`, `options`) in `data-config`; it does not emit the answer key.

The server emits a trusted mount with `data-learning-component="practice"`. JavaScript may progressively enhance that mount, but Markdown cannot select a JavaScript module or inject executable code.

## Security boundary

Repository content is authored/trusted, but it still passes through a defensive boundary:

1. lesson paths are constrained to safe repository keys;
2. JSON structure and exercise IDs are validated;
3. directives and attributes are allowlisted;
4. raw HTML is stripped by CommonMark;
5. unsafe link/image protocols are rejected by CommonMark;
6. block HTML is produced only by trusted PHP templates;
7. authored Markdown never carries arbitrary JavaScript.

If future requirements intentionally enable authored HTML, the security decision must be revisited and a dedicated sanitizer such as HTML Purifier evaluated separately.

## Cache and invalidation

The prototype uses Laravel's configured file cache with a key containing the lesson key and a SHA-256 hash of the lesson Markdown plus exercise config. A source/config change therefore selects a new cache key without requiring manual per-file invalidation. Normal deployment cache clearing remains available through `php artisan optimize:clear`.

## Known limitations

- This is not the final content system or authoring schema.
- The validator covers the current lesson/config slice and renderer references; it does not yet validate the full curriculum graph, datasets, publication ordering, or all future metadata.
- Callout body text is safely escaped plain text in this spike; nested Markdown inside callouts is not yet supported.
- The practice mount is a progressive placeholder, not the final assessment engine.
- No Markdown library other than the selected CommonMark path was installed or benchmarked in a full production content corpus.
