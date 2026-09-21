import { validateSpreadsheetResult } from '../spreadsheet/result-validator.js';

export async function mount(element) {
    const mountPoint = element.querySelector('[data-role="join-grain-mount"]');
    const fallback = element.querySelector('[data-role="join-grain-fallback"]');

    if (! mountPoint) {
        element.dataset.enhanced = 'false';
        return;
    }

    try {
        const config = JSON.parse(element.dataset.config ?? '{}');
        const component = createComponent(element, mountPoint, fallback, config);
        mountPoint.replaceChildren(component.shell);
        await component.initialize();
        element.learningComponent = component;
        element.dataset.enhanced = 'true';
    } catch {
        const feedback = document.createElement('p');
        feedback.className = 'practice-feedback';
        feedback.dataset.state = 'incorrect';
        feedback.textContent = 'Latihan JOIN belum dapat dibuka. Tabel pemeriksaan tetap dapat dibaca.';
        mountPoint.replaceChildren(feedback);
        element.dataset.enhanced = 'false';
    }
}

function createComponent(element, mountPoint, fallback, config) {
    const shell = buildShell(config);
    const scenarioSelect = shell.querySelector('[data-role="scenario"]');
    const schema = shell.querySelector('[data-role="schema"]');
    const output = shell.querySelector('[data-role="output"]');
    const warning = shell.querySelector('[data-role="warning"]');
    const metrics = shell.querySelector('[data-role="metrics"]');
    const status = shell.querySelector('[data-role="status"]');
    const feedback = shell.querySelector('[data-role="feedback"]');
    const buttons = [...shell.querySelectorAll('button')];
    let current = null;

    const setBusy = (busy) => buttons.forEach((button) => { button.disabled = busy; });
    const setFeedback = (message, state = '') => {
        feedback.textContent = message;
        feedback.dataset.state = state;
    };
    const setCompleted = (completed) => {
        element.dataset.completed = completed ? 'true' : 'false';
        element.dispatchEvent(new CustomEvent('practice:progress', { bubbles: true, detail: { completed } }));
    };

    const render = () => {
        const key = scenarioSelect.value;
        const scenario = config.scenarios[key];
        if (! scenario) throw new Error('Skenario JOIN tidak tersedia.');
        current = joinScenario(config.fixture, scenario);
        renderSchema(schema, config.fixture, scenario);
        renderOutput(output, current);
        warning.textContent = scenario.warning;
        renderMetrics(metrics, {
            'Skenario': scenario.label,
            'Join key': scenario.join_key,
            'Left rows': current.left_rows,
            'Joined rows': current.result_rows,
        });
        status.textContent = 'Hasil JOIN diperbarui. Bandingkan grain sebelum membaca metric.';
        return current;
    };

    const persist = async (localResult) => {
        if (document.body?.dataset.authenticated !== 'true' || !config.exercise_key || !window.axios) return localResult;
        try {
            const response = await window.axios.post('/attempts/check', { exercise_key: config.exercise_key, answer: localResult.answer });
            return response.data.result ?? localResult;
        } catch {
            return { ...localResult, feedback: 'Hasilnya cocok, tetapi belum tersimpan ke akun. Coba lagi.' };
        }
    };

    const resetAttempt = async () => {
        if (document.body?.dataset.authenticated !== 'true' || !config.exercise_key || !window.axios) return;
        await window.axios.delete('/attempts', { data: { exercise_key: config.exercise_key } });
    };

    scenarioSelect.addEventListener('change', () => {
        try {
            render();
            setFeedback('');
            setCompleted(false);
        } catch (error) {
            status.textContent = error instanceof Error ? error.message : String(error);
        }
    });

    shell.querySelector('[data-action="check"]').addEventListener('click', async () => {
        setBusy(true);
        try {
            const result = current ?? render();
            const validator = config.validator ?? {};
            const answer = buildJoinAnswer(scenarioSelect.value, config.scenarios[scenarioSelect.value], result);
            const validation = validateSpreadsheetResult(answer, {
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
                    ? (config.success_feedback ?? 'Tepat. Kamu membaca grain dan row count sebelum memakai hasil JOIN.')
                    : (config.incorrect_feedback ?? 'Belum tepat. Bandingkan join key, grain tabel, dan jumlah row hasil.'),
                answer,
            };
            const persisted = await persist(localResult);
            setFeedback(persisted.feedback ?? '', persisted.status === 'completed' ? 'correct' : persisted.status);
            setCompleted(persisted.completed === true);
        } catch (error) {
            status.textContent = error instanceof Error ? error.message : String(error);
        } finally {
            setBusy(false);
        }
    });

    shell.querySelector('[data-action="reset"]').addEventListener('click', async () => {
        setBusy(true);
        try {
            scenarioSelect.value = config.starter_scenario;
            render();
            setFeedback('');
            setCompleted(false);
            await resetAttempt();
        } finally {
            setBusy(false);
        }
    });

    shell.querySelector('[data-action="hint"]')?.addEventListener('click', () => {
        setFeedback('Mulai dengan menulis grain left table dan right table. Setelah itu, hitung berapa kali satu key dapat muncul di sisi kanan.', 'incomplete');
    });

    return {
        shell,
        async initialize() {
            scenarioSelect.value = config.starter_scenario;
            fallback?.setAttribute('hidden', 'hidden');
            render();
            if (document.body?.dataset.authenticated === 'true' && config.exercise_key && window.axios) {
                try {
                    const response = await window.axios.get('/attempts', { params: { exercise_key: config.exercise_key } });
                    if (response.data.attempt?.status === 'completed') {
                        setFeedback('Latihan ini sudah selesai. Kamu tetap dapat membandingkan skenario lain.', 'correct');
                        setCompleted(true);
                    }
                } catch {
                    // A failed attempt load should not block the interactive.
                }
            }
        },
        getAnswer: () => buildJoinAnswer(scenarioSelect.value, config.scenarios[scenarioSelect.value], current ?? render()),
    };
}

export function joinScenario(fixture, scenario) {
    const leftRows = fixture.tables?.[scenario.left_table]?.rows ?? [];
    const rightRows = fixture.tables?.[scenario.right_table]?.rows ?? [];
    const rows = [];

    leftRows.forEach((left) => {
        rightRows.forEach((right) => {
            if (left[scenario.join_key] !== undefined
                && left[scenario.join_key] !== null
                && left[scenario.join_key] === right[scenario.join_key]) {
                rows.push([left[scenario.join_key], right[scenario.join_key], right.quantity ?? null]);
            }
        });
    });

    return {
        left_rows: leftRows.length,
        right_rows: rightRows.length,
        result_rows: rows.length,
        rows,
    };
}

export function buildJoinAnswer(scenarioKey, scenario, result) {
    return {
        columns: ['scenario', 'join_key', 'left_rows', 'right_rows', 'result_rows'],
        rows: [[scenarioKey, scenario.join_key, result.left_rows, result.right_rows, result.result_rows]],
    };
}

function buildShell(config) {
    const shell = document.createElement('div');
    shell.className = 'join-grain-playground';
    shell.innerHTML = `
        <p class="join-grain-playground__guidance">${escapeText(config.desktop_note ?? '')}</p>
        <label class="join-grain-playground__scenario-label" for="join-scenario-${escapeAttribute(config.id)}">Skenario JOIN
            <select id="join-scenario-${escapeAttribute(config.id)}" data-role="scenario"></select>
        </label>
        <div class="join-grain-playground__workspace">
            <section class="join-grain-playground__schema" data-role="schema" aria-label="Tabel sumber"></section>
            <section class="join-grain-playground__result" aria-label="Hasil JOIN">
                <div data-role="output"></div>
                <p class="join-grain-playground__warning" data-role="warning"></p>
            </section>
        </div>
        <div class="join-grain-playground__actions">
            <button class="button button--primary" type="button" data-action="check">Cek hasil</button>
            <button class="button button--quiet" type="button" data-action="reset">Reset</button>
            <button class="button button--quiet" type="button" data-action="hint">Petunjuk</button>
        </div>
        <p class="join-grain-playground__status" data-role="status" role="status" aria-live="polite"></p>
        <dl class="join-grain-playground__metrics" data-role="metrics"></dl>
        <p class="practice-feedback" data-role="feedback" aria-live="polite"></p>`;

    for (const scenarioKey of config.allowed_scenarios ?? []) {
        const option = document.createElement('option');
        option.value = scenarioKey;
        option.textContent = config.scenarios[scenarioKey]?.label ?? scenarioKey;
        shell.querySelector('[data-role="scenario"]').append(option);
    }
    return shell;
}

function renderSchema(element, fixture, scenario) {
    element.replaceChildren();
    [scenario.left_table, scenario.right_table].forEach((tableKey, index) => {
        const table = fixture.tables[tableKey];
        const heading = document.createElement('p');
        heading.className = 'join-grain-playground__table-label';
        heading.textContent = `${index === 0 ? 'LEFT' : 'RIGHT'} · ${tableKey} · ${index === 0 ? scenario.left_grain : scenario.right_grain}`;
        element.append(heading);
        const columns = document.createElement('p');
        columns.className = 'join-grain-playground__columns';
        columns.textContent = Object.keys(table.columns ?? {}).join(' · ');
        element.append(columns);
    });
    const key = document.createElement('p');
    key.className = 'join-grain-playground__key';
    key.textContent = `JOIN KEY · ${scenario.join_key}`;
    element.append(key);
}

function renderOutput(element, result) {
    const table = document.createElement('table');
    const caption = document.createElement('caption');
    caption.textContent = 'Preview hasil JOIN';
    table.append(caption);
    const head = document.createElement('thead');
    const headRow = document.createElement('tr');
    ['Left key', 'Right key', 'Right quantity'].forEach((label) => {
        const cell = document.createElement('th');
        cell.scope = 'col';
        cell.textContent = label;
        headRow.append(cell);
    });
    head.append(headRow);
    table.append(head);
    const body = document.createElement('tbody');
    result.rows.slice(0, 12).forEach((row) => {
        const rowElement = document.createElement('tr');
        row.forEach((value, index) => {
            const cell = document.createElement(index === 0 ? 'th' : 'td');
            if (index === 0) cell.scope = 'row';
            cell.textContent = String(value ?? '');
            rowElement.append(cell);
        });
        body.append(rowElement);
    });
    table.append(body);
    element.replaceChildren(table);
}

function renderMetrics(element, values) {
    element.replaceChildren();
    Object.entries(values).forEach(([label, value]) => {
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
    return String(value).replace(/[&<>"']/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[character]));
}

function escapeAttribute(value) {
    return escapeText(value).replace(/`/g, '&#096;');
}
