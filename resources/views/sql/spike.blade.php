<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gate A: Browser SQL spike</title>
    @vite(['resources/css/app.css', 'resources/css/sql-spike.css', 'resources/js/spike/sql/sql.js'])
</head>
<body>
    <main class="sql-spike" data-sql-spike>
        <p class="sql-spike__eyebrow">M0B / Gate A / technical spike</p>
        <h1>Browser-side SQL execution</h1>
        <p class="sql-spike__intro">
            SQL runs in a Web Worker against a predefined NusaMart fixture. No query is sent to Laravel or MySQL.
            This page is an evaluation harness, not the final SQL Playground.
        </p>

        <section class="sql-spike__boundary" aria-labelledby="boundary-title">
            <h2 id="boundary-title">Execution boundary</h2>
            <ul>
                <li>Engine: sql.js / SQLite compiled to WebAssembly.</li>
                <li>Input: predefined versioned fixture only.</li>
                <li>Transport: browser Web Worker; server receives no learner SQL.</li>
                <li>Output cap: 100 rows; query timeout: 2 seconds.</li>
            </ul>
        </section>

        <section class="sql-spike__workspace" aria-labelledby="workspace-title">
            <h2 id="workspace-title">Query experiment</h2>
            <label for="sql-case">Representative query</label>
            <select id="sql-case" data-role="case-select"></select>
            <label for="sql-editor">SQL</label>
            <textarea id="sql-editor" data-role="editor" rows="8" spellcheck="false"></textarea>
            <div class="sql-spike__actions">
                <button type="button" data-action="run">Run</button>
                <button type="button" data-action="reset">Reset runtime</button>
                <button type="button" data-action="suite">Run representative suite</button>
            </div>
            <p class="sql-spike__status" data-role="status" role="status" aria-live="polite">Initializing browser SQL runtime…</p>
            <pre class="sql-spike__error" data-role="error" hidden></pre>
        </section>

        <section aria-labelledby="metrics-title">
            <h2 id="metrics-title">Measurements</h2>
            <dl class="sql-spike__metrics" data-role="metrics"></dl>
        </section>

        <section aria-labelledby="result-title">
            <h2 id="result-title">Result</h2>
            <div class="sql-spike__result" data-role="result">
                <p>Run a query to inspect bounded rows.</p>
            </div>
        </section>

        <section aria-labelledby="fixture-title">
            <h2 id="fixture-title">Fixture contract</h2>
            <pre class="sql-spike__fixture" data-role="fixture-summary"></pre>
        </section>
    </main>

    <script type="application/json" id="sql-spike-fixture">@json($fixture)</script>
</body>
</html>
