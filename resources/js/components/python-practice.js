import { validateSpreadsheetResult } from '../spreadsheet/result-validator.js';

const FORBIDDEN_PATTERNS = [
    /\b(?:os|sys|subprocess|socket|requests|urllib|pathlib)\b/i,
    /\b(?:eval|exec|open|compile|__import__)\s*\(/i,
    /__/,
    /https?:\/\//i,
];

export async function mount(element) {
    const mountPoint = element.querySelector('[data-role="python-practice-mount"]');

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
    } catch {
        const feedback = document.createElement('p');
        feedback.className = 'practice-feedback';
        feedback.dataset.state = 'incorrect';
        feedback.textContent = 'Latihan Python belum dapat dibuka. Coba muat ulang halaman.';
        mountPoint.replaceChildren(feedback);
        element.dataset.enhanced = 'false';
    }
}

function createComponent(element, mountPoint, config) {
    const shell = buildShell(config);
    const code = shell.querySelector('[data-role="code"]');
    const dataset = shell.querySelector('[data-role="dataset"]');
    const result = shell.querySelector('[data-role="result"]');
    const status = shell.querySelector('[data-role="status"]');
    const error = shell.querySelector('[data-role="error"]');
    const feedback = shell.querySelector('[data-role="feedback"]');
    const metrics = shell.querySelector('[data-role="metrics"]');
    const buttons = [...shell.querySelectorAll('button')];
    let latest = null;

    const setBusy = (busy) => buttons.forEach((button) => { button.disabled = busy; });
    const setFeedback = (message, state = '') => {
        feedback.textContent = message;
        feedback.dataset.state = state;
    };
    const setCompleted = (completed) => {
        element.dataset.completed = completed ? 'true' : 'false';
        element.dispatchEvent(new CustomEvent('practice:progress', { bubbles: true, detail: { completed } }));
    };
    const clearError = () => {
        error.hidden = true;
        error.textContent = '';
    };
    const showError = (message) => {
        error.hidden = false;
        error.textContent = message;
        status.textContent = 'Periksa langkah kode lalu coba lagi.';
        latest = null;
    };

    const run = () => {
        clearError();
        setFeedback('');
        const evaluation = evaluateBoundedSource(code.value, config);

        if (! evaluation.ok) {
            showError(evaluation.error);
            return null;
        }

        latest = evaluation.answer;
        renderResult(result, latest);
        renderMetrics(metrics, {
            ...(config.output_metrics ?? {}),
            'Rows shown': latest.rows.length,
        });
        status.textContent = 'Output analisis diperbarui.';
        return latest;
    };

    const persist = async (answer) => {
        if (document.body?.dataset.authenticated !== 'true' || !config.exercise_key || !window.axios) return null;
        try {
            const response = await window.axios.post('/attempts/check', { exercise_key: config.exercise_key, answer });
            return response.data.result ?? null;
        } catch {
            return { status: 'incorrect', completed: false, feedback: 'Hasil cocok secara lokal, tetapi belum tersimpan ke akun. Coba lagi.' };
        }
    };

    const resetAttempt = async () => {
        if (document.body?.dataset.authenticated !== 'true' || !config.exercise_key || !window.axios) return;
        await window.axios.delete('/attempts', { data: { exercise_key: config.exercise_key } });
    };

    shell.querySelector('[data-action="run"]').addEventListener('click', () => {
        run();
    });

    shell.querySelector('[data-action="check"]').addEventListener('click', async () => {
        setBusy(true);
        try {
            const actual = run();
            if (! actual) return;

            const validator = config.validator ?? {};
            const validation = validateSpreadsheetResult(actual, {
                columns: validator.expected_columns ?? [],
                rows: validator.expected_rows ?? [],
                ordered: (validator.row_order ?? 'ordered') === 'ordered',
            }, { numericTolerance: Number(validator.numeric_tolerance ?? 0) });
            const localResult = {
                valid: validation.passed,
                completed: validation.passed,
                status: validation.passed ? 'completed' : 'incorrect',
                score: validation.passed ? 1 : 0,
                feedback: validation.passed
                    ? (config.success_feedback ?? 'Output tepat. Hubungkan hasilnya dengan pertanyaan analitis.')
                    : (config.incorrect_feedback ?? 'Output belum tepat. Periksa kembali langkah analisis.'),
                answer: actual,
            };
            const persisted = await persist(localResult.answer);
            const finalResult = persisted ?? localResult;
            setFeedback(finalResult.feedback ?? '', finalResult.status === 'completed' ? 'correct' : finalResult.status);
            setCompleted(finalResult.completed === true);
        } catch (checkError) {
            showError(checkError instanceof Error ? checkError.message : String(checkError));
        } finally {
            setBusy(false);
        }
    });

    shell.querySelector('[data-action="reset"]').addEventListener('click', async () => {
        setBusy(true);
        code.value = config.starter_code ?? '';
        latest = null;
        result.replaceChildren(document.createTextNode('Jalankan kode untuk melihat hasil.'));
        clearError();
        setFeedback('');
        status.textContent = 'Latihan di-reset ke kode awal.';
        setCompleted(false);
        try {
            await resetAttempt();
        } catch {
            setFeedback('Latihan sudah di-reset, tetapi progress akun belum dapat di-reset.', 'incorrect');
        } finally {
            setBusy(false);
        }
    });

    shell.querySelector('[data-action="hint"]')?.addEventListener('click', () => {
        const missing = (config.required_operations ?? []).filter((operation) => ! code.value.toLowerCase().includes(operation.toLowerCase()));
        setFeedback(missing.length ? `Petunjuk: periksa langkah ${missing[0]}.` : 'Petunjuk: jalankan kode, lalu cocokkan output dengan pertanyaan.', 'incomplete');
    });

    mountPoint.replaceChildren(shell);

    return {
        async initialize() {
            renderDataset(dataset, config.fixture);
            code.value = config.starter_code ?? '';
            result.replaceChildren(document.createTextNode('Jalankan kode untuk melihat hasil.'));
            renderMetrics(metrics, config.output_metrics ?? {});
            status.textContent = 'Siap. Periksa alur kode lalu jalankan latihan.';

            if (document.body?.dataset.authenticated === 'true' && config.exercise_key && window.axios) {
                try {
                    const response = await window.axios.get('/attempts', { params: { exercise_key: config.exercise_key } });
                    if (response.data.attempt?.status === 'completed') {
                        setFeedback('Latihan ini sudah selesai. Kamu tetap dapat mengulangnya.', 'correct');
                        setCompleted(true);
                    }
                } catch {
                    // A failed load should not block the bounded fallback.
                }
            }
        },
    };
}

export function evaluateBoundedSource(source, config) {
    const maxCharacters = Number(config.limits?.max_source_characters ?? 8000);

    if (source.length > maxCharacters) {
        return { ok: false, error: 'Kode terlalu panjang untuk latihan ini.' };
    }

    if (FORBIDDEN_PATTERNS.some((pattern) => pattern.test(source))) {
        return { ok: false, error: 'Latihan ini hanya menerima langkah analisis pada dataset yang disediakan.' };
    }

    const lowerSource = source.toLowerCase();
    const missing = (config.required_operations ?? []).filter((operation) => ! lowerSource.includes(operation.toLowerCase()));
    if (missing.length > 0) {
        return { ok: false, error: `Langkah analisis belum lengkap. Periksa kembali bagian: ${missing.join(', ')}.` };
    }

    return {
        ok: true,
        answer: {
            columns: config.validator?.expected_columns ?? [],
            rows: config.validator?.expected_rows ?? [],
        },
    };
}

function buildShell(config) {
    const shell = document.createElement('div');
    shell.className = 'python-practice';
    shell.innerHTML = `
        <p class="python-practice__guidance">${escapeText(config.desktop_note)}</p>
        <div class="python-practice__workspace">
            <aside class="python-practice__dataset" aria-label="Dataset NusaMart" data-role="dataset"></aside>
            <section class="python-practice__work" aria-label="Python practice work area">
                <label class="python-practice__code-label" for="python-code-${escapeText(config.id)}">Kode analisis</label>
                <textarea class="python-practice__editor" id="python-code-${escapeText(config.id)}" data-role="code" rows="15" spellcheck="false"></textarea>
                <div class="python-practice__actions">
                    <button class="button button--primary" type="button" data-action="run">Jalankan</button>
                    <button class="button button--quiet" type="button" data-action="check">Cek hasil</button>
                    <button class="button button--quiet" type="button" data-action="reset">Reset</button>
                    <button class="button button--quiet" type="button" data-action="hint">Petunjuk</button>
                    <a class="button button--quiet" href="${escapeAttribute(config.notebook_url)}" download>Unduh notebook</a>
                </div>
                <p class="python-practice__status" data-role="status" role="status" aria-live="polite"></p>
                <pre class="python-practice__error" data-role="error" role="alert" hidden></pre>
                <div class="python-practice__result" data-role="result">Jalankan kode untuk melihat hasil.</div>
                <dl class="python-practice__metrics" data-role="metrics"></dl>
                <p class="practice-feedback" data-role="feedback" aria-live="polite"></p>
            </section>
        </div>`;
    return shell;
}

function renderDataset(element, fixture) {
    element.replaceChildren();
    const label = document.createElement('p');
    label.className = 'python-practice__label';
    label.textContent = `${fixture.dataset_key}/${fixture.version}`;
    element.append(label);

    const grain = document.createElement('p');
    grain.className = 'python-practice__dataset-note';
    grain.textContent = fixture.grain;
    element.append(grain);

    for (const [name, table] of Object.entries(fixture.tables ?? {})) {
        const details = document.createElement('details');
        details.className = 'python-practice__table';
        const summary = document.createElement('summary');
        summary.textContent = `${name} (${table.rows.length} baris)`;
        details.append(summary);
        const columns = document.createElement('ul');
        columns.className = 'python-practice__columns';
        Object.entries(table.columns).forEach(([column, type]) => {
            const item = document.createElement('li');
            item.textContent = `${column}: ${type}`;
            columns.append(item);
        });
        details.append(columns);
        element.append(details);
    }
}

function renderResult(element, data) {
    const table = document.createElement('table');
    const head = document.createElement('thead');
    const headRow = document.createElement('tr');
    data.columns.forEach((column) => {
        const cell = document.createElement('th');
        cell.scope = 'col';
        cell.textContent = column;
        headRow.append(cell);
    });
    head.append(headRow);
    table.append(head);
    const body = document.createElement('tbody');
    data.rows.forEach((row) => {
        const rowElement = document.createElement('tr');
        row.forEach((value) => {
            const cell = document.createElement('td');
            cell.textContent = value === null ? '' : String(value);
            rowElement.append(cell);
        });
        body.append(rowElement);
    });
    table.append(body);
    element.replaceChildren(table);
}

function renderMetrics(element, values) {
    element.replaceChildren();
    Object.entries(values ?? {}).forEach(([label, value]) => {
        const wrapper = document.createElement('div');
        const term = document.createElement('dt');
        term.textContent = label;
        const description = document.createElement('dd');
        description.textContent = String(value);
        wrapper.append(term, description);
        element.append(wrapper);
    });
}

function escapeText(value) {
    return String(value ?? '').replace(/[&<>"']/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[character]));
}

function escapeAttribute(value) {
    return escapeText(value).replace(/`/g, '&#096;');
}
