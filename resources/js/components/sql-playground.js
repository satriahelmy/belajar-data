import { SqlWorkerRunner } from '../spike/sql/sql.js';
import { validateSqlResult } from '../spike/sql/result-validator.js';

export async function mount(element) {
    const mountPoint = element.querySelector('[data-role="sql-playground-mount"]');

    if (! mountPoint) {
        element.dataset.enhanced = 'false';
        return;
    }

    try {
        const config = JSON.parse(element.dataset.config ?? '{}');
        const component = createComponent(element, mountPoint, config);
        await component.initialize();
        element.learningComponent = component;
        element.dataset.enhanced = 'true';
    } catch (error) {
        const feedback = document.createElement('p');
        feedback.className = 'practice-feedback';
        feedback.dataset.state = 'incorrect';
        feedback.textContent = error instanceof Error
            ? `SQL Playground belum dapat dibuka: ${error.message}`
            : 'SQL Playground belum dapat dibuka. Coba muat ulang halaman.';
        mountPoint.replaceChildren(feedback);
        element.dataset.enhanced = 'false';
    }
}

function createComponent(element, mountPoint, config) {
    const runner = new SqlWorkerRunner(config.fixture.tables);
    const shell = buildShell(config);
    const editor = shell.querySelector('[data-role="editor"]');
    const status = shell.querySelector('[data-role="status"]');
    const error = shell.querySelector('[data-role="error"]');
    const result = shell.querySelector('[data-role="result"]');
    const feedback = shell.querySelector('[data-role="feedback"]');
    const metrics = shell.querySelector('[data-role="metrics"]');
    const buttons = [...shell.querySelectorAll('button')];
    let latestQuery = null;

    const setBusy = (busy) => buttons.forEach((button) => { button.disabled = busy; });
    const clearError = () => {
        error.hidden = true;
        error.textContent = '';
    };
    const showError = (message) => {
        error.hidden = false;
        error.textContent = message;
        status.textContent = 'Query gagal. Perbaiki SQL atau reset runtime.';
    };
    const setFeedback = (message, state = '') => {
        feedback.textContent = message;
        feedback.dataset.state = state;
    };
    const setCompleted = (completed) => {
        element.dataset.completed = completed ? 'true' : 'false';
        element.dispatchEvent(new CustomEvent('practice:progress', {
            bubbles: true,
            detail: { completed },
        }));
    };

    const runQuery = async () => {
        const query = await runner.query(editor.value);
        latestQuery = query;
        renderResult(result, query);
        renderMetrics(metrics, {
            'Query latency': `${query.queryMs.toFixed(1)} ms`,
            'Rows returned': query.rowCount,
            'Rows shown': query.rows.length,
            'Output bounded': query.truncated ? 'yes' : 'no',
        });
        status.textContent = 'Query selesai di browser.';
        clearError();
        return query;
    };

    const persistResult = async (localResult) => {
        if (document.body?.dataset.authenticated !== 'true' || !config.exercise_key || !window.axios) {
            return localResult;
        }

        try {
            const response = await window.axios.post('/attempts/check', {
                exercise_key: config.exercise_key,
                answer: localResult.answer,
            });
            return response.data.result ?? localResult;
        } catch {
            return {
                ...localResult,
                feedback: 'Hasilnya cocok, tetapi belum tersimpan ke akun. Coba lagi.',
            };
        }
    };

    const resetAttempt = async () => {
        if (document.body?.dataset.authenticated !== 'true' || !config.exercise_key || !window.axios) {
            return;
        }

        await window.axios.delete('/attempts', { data: { exercise_key: config.exercise_key } });
    };

    shell.querySelector('[data-action="run"]').addEventListener('click', async () => {
        setBusy(true);
        try {
            await runQuery();
            setFeedback('');
        } catch (queryError) {
            showError(queryError instanceof Error ? queryError.message : String(queryError));
        } finally {
            setBusy(false);
        }
    });

    shell.querySelector('[data-action="check"]').addEventListener('click', async () => {
        setBusy(true);
        try {
            const query = await runQuery();

            if (query.truncated) {
                setFeedback(`Hasil melebihi batas ${config.limits.max_rows} baris. Tambahkan filter atau LIMIT sebelum mengecek.`, 'incomplete');
                setCompleted(false);
                return;
            }

            const validator = config.validator ?? {};
            const validation = validateSqlResult(query, {
                columns: validator.expected_columns ?? [],
                rows: validator.expected_rows ?? [],
                ordered: (validator.row_order ?? 'ordered') === 'ordered',
            }, {
                numericTolerance: Number(validator.numeric_tolerance ?? 0),
            });
            const localResult = {
                valid: validation.passed,
                completed: validation.passed,
                status: validation.passed ? 'completed' : 'incorrect',
                score: validation.passed ? 1 : 0,
                feedback: validation.passed
                    ? (config.success_feedback ?? 'Hasilnya tepat. Hubungkan output ini dengan pertanyaan analitisnya.')
                    : (config.incorrect_feedback ?? 'Hasil belum tepat. Periksa kolom, filter, grain, dan urutan barisnya.'),
                answer: { columns: query.columns, rows: query.rows },
            };
            const persisted = await persistResult(localResult);
            setFeedback(persisted.feedback ?? '', persisted.status === 'completed' ? 'correct' : persisted.status);
            setCompleted(persisted.completed === true);
        } catch (queryError) {
            showError(queryError instanceof Error ? queryError.message : String(queryError));
        } finally {
            setBusy(false);
        }
    });

    shell.querySelector('[data-action="reset"]').addEventListener('click', async () => {
        setBusy(true);
        try {
            await runner.reset();
            editor.value = config.starter_query;
            latestQuery = null;
            result.replaceChildren(document.createTextNode('Jalankan query untuk melihat hasil yang dibatasi.'));
            setFeedback('');
            clearError();
            renderMetrics(metrics, { 'Status runtime': 'siap', 'Batas output': `${config.limits.max_rows} baris` });
            status.textContent = 'Runtime di-reset dan dataset dimuat ulang.';
            setCompleted(false);
            try {
                await resetAttempt();
            } catch {
                setFeedback('Runtime sudah di-reset, tetapi progress akun belum dapat di-reset.', 'incorrect');
            }
        } catch (resetError) {
            showError(resetError instanceof Error ? resetError.message : String(resetError));
        } finally {
            setBusy(false);
        }
    });

    shell.querySelector('[data-action="hint"]')?.addEventListener('click', () => {
        setFeedback('Mulai dengan memilih tabel yang grain-nya sesuai pertanyaan, lalu batasi kolom dan baris yang benar-benar dibutuhkan.', 'incomplete');
    });

    mountPoint.replaceChildren(shell);

    return {
        async initialize() {
            renderSchema(shell.querySelector('[data-role="schema"]'), config.fixture);
            editor.value = config.starter_query;
            renderMetrics(metrics, { 'Status runtime': 'memuat', 'Batas output': `${config.limits.max_rows} baris` });
            status.textContent = 'Memuat dataset...';
            const init = await runner.start();
            renderMetrics(metrics, {
                'Inisialisasi': `${init.initializationMs.toFixed(1)} ms`,
                'Tabel': init.tableCount,
                'Baris fixture': init.rowCount,
                'Batas output': `${config.limits.max_rows} baris`,
            });
            status.textContent = 'Siap. Query dijalankan di browser.';

            if (document.body?.dataset.authenticated === 'true' && config.exercise_key && window.axios) {
                try {
                    const response = await window.axios.get('/attempts', { params: { exercise_key: config.exercise_key } });
                    const attempt = response.data.attempt;
                    if (attempt?.status === 'completed') {
                        setFeedback('Latihan ini sudah selesai. Kamu tetap dapat mengulang query.', 'correct');
                        setCompleted(true);
                    }
                } catch {
                    // A failed load should not block the playground.
                }
            }
        },
        latestQuery: () => latestQuery,
    };
}

function buildShell(config) {
    const shell = document.createElement('div');
    shell.className = 'sql-playground';
    shell.innerHTML = `
        <p class="sql-playground__guidance">${escapeText(config.desktop_note)}</p>
        <div class="sql-playground__workspace">
            <aside class="sql-playground__schema" aria-label="Schema dataset" data-role="schema"></aside>
            <section class="sql-playground__editor-panel" aria-label="SQL editor">
                <label class="sql-playground__label" for="sql-editor-${escapeAttribute(config.id)}">Query</label>
                <textarea id="sql-editor-${escapeAttribute(config.id)}" class="sql-playground__editor" data-role="editor" spellcheck="false" autocapitalize="off" autocomplete="off"></textarea>
                <div class="sql-playground__actions">
                    <button class="button button--primary" type="button" data-action="run">Run query</button>
                    <button class="button button--quiet" type="button" data-action="check">Cek hasil</button>
                    <button class="button button--quiet" type="button" data-action="reset">Reset runtime</button>
                    <button class="button button--quiet" type="button" data-action="hint">Petunjuk</button>
                </div>
                <p class="sql-playground__status" data-role="status" role="status" aria-live="polite"></p>
                <pre class="sql-playground__error" data-role="error" role="alert" hidden></pre>
                <div class="sql-playground__result" data-role="result">Jalankan query untuk melihat hasil yang dibatasi.</div>
                <dl class="sql-playground__metrics" data-role="metrics"></dl>
                <p class="practice-feedback" data-role="feedback" aria-live="polite"></p>
            </section>
        </div>`;
    return shell;
}

function renderSchema(element, fixture) {
    element.replaceChildren();
    const title = document.createElement('p');
    title.className = 'sql-playground__label';
    title.textContent = `${fixture.dataset_key}/${fixture.version}`;
    element.append(title);

    const description = document.createElement('p');
    description.className = 'sql-playground__schema-note';
    description.textContent = fixture.grain;
    element.append(description);

    for (const [tableName, table] of Object.entries(fixture.tables)) {
        const details = document.createElement('details');
        details.className = 'sql-playground__table';
        const summary = document.createElement('summary');
        summary.textContent = `${tableName} (${table.rows.length} baris)`;
        details.append(summary);

        const columns = document.createElement('ul');
        columns.className = 'sql-playground__columns';
        for (const [column, type] of Object.entries(table.columns)) {
            const item = document.createElement('li');
            item.textContent = `${column} · ${type}`;
            columns.append(item);
        }
        details.append(columns);

        const sample = document.createElement('p');
        sample.className = 'sql-playground__sample';
        sample.textContent = 'Preview tersedia dari fixture kecil ini.';
        details.append(sample);
        element.append(details);
    }
}

function renderMetrics(element, metrics) {
    element.replaceChildren();
    for (const [label, value] of Object.entries(metrics)) {
        const wrapper = document.createElement('div');
        const term = document.createElement('dt');
        const detail = document.createElement('dd');
        term.textContent = label;
        detail.textContent = String(value);
        wrapper.append(term, detail);
        element.append(wrapper);
    }
}

function renderResult(element, result) {
    if (! result.columns.length) {
        element.textContent = 'Query tidak mengembalikan tabel.';
        return;
    }

    const table = document.createElement('table');
    const thead = document.createElement('thead');
    const header = document.createElement('tr');
    for (const column of result.columns) {
        const cell = document.createElement('th');
        cell.scope = 'col';
        cell.textContent = column;
        header.append(cell);
    }
    thead.append(header);
    const tbody = document.createElement('tbody');
    for (const row of result.rows) {
        const tr = document.createElement('tr');
        for (const value of row) {
            const cell = document.createElement('td');
            cell.textContent = value === null ? 'NULL' : String(value);
            tr.append(cell);
        }
        tbody.append(tr);
    }
    table.append(thead, tbody);
    element.replaceChildren(table);
}

function escapeText(value) {
    const span = document.createElement('span');
    span.textContent = value ?? '';
    return span.innerHTML;
}

function escapeAttribute(value) {
    return String(value ?? '').replace(/[^a-z0-9-]/gi, '-');
}
