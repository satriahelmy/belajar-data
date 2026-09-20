import { applyTableView, buildConfiguredSummary, calculateFormulaCells } from '../spreadsheet/evaluator.js';
import { validateSpreadsheetResult } from '../spreadsheet/result-validator.js';

export async function mount(element) {
    const mountPoint = element.querySelector('[data-role="spreadsheet-playground-mount"]');

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
        feedback.textContent = 'Latihan spreadsheet belum dapat dibuka. Coba muat ulang halaman.';
        mountPoint.replaceChildren(feedback);
        element.dataset.enhanced = 'false';
    }
}

function createComponent(element, mountPoint, config) {
    const shell = buildShell(config);
    const dataset = shell.querySelector('[data-role="dataset"]');
    const work = shell.querySelector('[data-role="work"]');
    const status = shell.querySelector('[data-role="status"]');
    const error = shell.querySelector('[data-role="error"]');
    const result = shell.querySelector('[data-role="result"]');
    const feedback = shell.querySelector('[data-role="feedback"]');
    const metrics = shell.querySelector('[data-role="metrics"]');
    const buttons = [...shell.querySelectorAll('button')];
    let latest = null;
    let currentState = {
        cells: { ...(config.starter_cells ?? {}) },
        region: config.starter_filter ?? 'all',
        month: 'all',
        sort: config.starter_sort ?? 'order_id_asc',
    };

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
        status.textContent = 'Tampilan atau perhitungan gagal. Periksa input lalu coba lagi.';
    };

    const collectCells = () => {
        const cells = {};
        work.querySelectorAll('[data-cell]').forEach((input) => { cells[input.dataset.cell] = input.value; });
        return cells;
    };

    const calculate = () => {
        currentState.cells = collectCells();
        const calculated = calculateFormulaCells(currentState.cells, config.fixture);
        latest = { columns: Object.keys(calculated.values), rows: [Object.values(calculated.values)] };
        renderFormulaValues(work, calculated.values);
        renderResult(result, latest);
        renderMetrics(metrics, {
            'Mode': 'formula',
            'Target': latest.columns.length,
            'Error': Object.values(calculated.values).filter((value) => typeof value === 'string' && value.startsWith('#')).length,
        });
        status.textContent = 'Hasil rumus diperbarui.';
        clearError();
        return latest;
    };

    const renderTableView = () => {
        const rows = applyTableView(config.fixture.tables.transactions.rows, currentState);
        latest = { columns: config.view_columns, rows: rows.map((row) => config.view_columns.map((column) => row[column] ?? null)) };
        renderResult(result, latest);
        renderMetrics(metrics, { 'Mode': 'filter/sort', 'Rows shown': rows.length, 'Region': currentState.region, 'Month': currentState.month });
        status.textContent = 'Tampilan data diperbarui.';
        clearError();
        return latest;
    };

    const renderSummary = () => {
        const summary = buildConfiguredSummary(config.fixture, config.summary_measures);
        if (summary.error) throw new Error(summary.error);
        latest = summary;
        renderResult(result, latest);
        renderMetrics(metrics, { 'Mode': 'summary', 'Rows shown': latest.rows.length, 'Dimension': config.summary_dimension, 'Measures': config.summary_measures.length });
        status.textContent = 'Ringkasan diperbarui.';
        clearError();
        return latest;
    };

    const run = () => {
        if (config.mode === 'formula') return calculate();
        if (config.mode === 'table') return renderTableView();
        return renderSummary();
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

    shell.querySelector('[data-action="run"]').addEventListener('click', () => {
        try {
            run();
            setFeedback('');
        } catch (runError) {
            showError(runError instanceof Error ? runError.message : String(runError));
        }
    });

    shell.querySelector('[data-action="check"]').addEventListener('click', async () => {
        setBusy(true);
        try {
            const actual = run();
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
                    ? (config.success_feedback ?? 'Hasilnya tepat. Hubungkan output ini dengan pertanyaan analitisnya.')
                    : (config.incorrect_feedback ?? 'Hasil belum tepat. Periksa kembali filter, rumus, dan output yang diminta.'),
                answer: actual,
            };
            const persisted = await persist(localResult);
            setFeedback(persisted.feedback ?? '', persisted.status === 'completed' ? 'correct' : persisted.status);
            setCompleted(persisted.completed === true);
        } catch (checkError) {
            showError(checkError instanceof Error ? checkError.message : String(checkError));
        } finally {
            setBusy(false);
        }
    });

    shell.querySelector('[data-action="reset"]').addEventListener('click', async () => {
        setBusy(true);
        currentState = {
            cells: { ...(config.starter_cells ?? {}) },
            region: config.starter_filter ?? 'all',
            month: 'all',
            sort: config.starter_sort ?? 'order_id_asc',
        };
        hydrateControls(work, currentState, config);
        latest = null;
        result.replaceChildren(document.createTextNode('Jalankan perhitungan untuk melihat hasil.'));
        setFeedback('');
        clearError();
        status.textContent = 'Workbook di-reset ke data awal.';
        setCompleted(false);
        try {
            await resetAttempt();
        } catch {
            setFeedback('Workbook sudah di-reset, tetapi progress akun belum dapat di-reset.', 'incorrect');
        } finally {
            setBusy(false);
        }
    });

    shell.querySelector('[data-action="hint"]')?.addEventListener('click', () => {
        setFeedback((config.hints ?? [])[0] ?? 'Periksa dulu satu row mewakili apa, lalu pilih kolom dan fungsi yang sesuai.', 'incomplete');
    });

    mountPoint.replaceChildren(shell);

    return {
        async initialize() {
            renderDataset(dataset, config.fixture, config.mode === 'table' ? config.view_columns : Object.keys(config.fixture.tables.transactions.columns));
            buildWorkArea(work, config, currentState);
            hydrateControls(work, currentState, config);
            if (config.mode === 'table') renderTableView();
            else {
                result.replaceChildren(document.createTextNode('Jalankan perhitungan untuk melihat hasil.'));
                renderMetrics(metrics, { 'Mode': config.mode, 'Rows': config.fixture.tables.transactions.rows.length });
                status.textContent = 'Siap. Masukkan atau periksa rumus yang tersedia.';
            }
            if (document.body?.dataset.authenticated === 'true' && config.exercise_key && window.axios) {
                try {
                    const response = await window.axios.get('/attempts', { params: { exercise_key: config.exercise_key } });
                    if (response.data.attempt?.status === 'completed') {
                        setFeedback('Latihan ini sudah selesai. Kamu tetap dapat mengulangnya.', 'correct');
                        setCompleted(true);
                    }
                } catch {
                    // A failed load should not block the spreadsheet shell.
                }
            }
        },
    };
}

function buildShell(config) {
    const shell = document.createElement('div');
    shell.className = 'spreadsheet-playground';
    shell.innerHTML = `
        <p class="spreadsheet-playground__guidance">${escapeText(config.desktop_note)}</p>
        <div class="spreadsheet-playground__workspace">
            <aside class="spreadsheet-playground__dataset" aria-label="Dataset NusaMart" data-role="dataset"></aside>
            <section class="spreadsheet-playground__work" aria-label="Spreadsheet work area" data-role="work">
                <div data-role="controls"></div>
                <div class="spreadsheet-playground__actions">
                    <button class="button button--primary" type="button" data-action="run">Hitung</button>
                    <button class="button button--quiet" type="button" data-action="check">Cek hasil</button>
                    <button class="button button--quiet" type="button" data-action="reset">Reset</button>
                    <button class="button button--quiet" type="button" data-action="hint">Petunjuk</button>
                </div>
                <p class="spreadsheet-playground__status" data-role="status" role="status" aria-live="polite"></p>
                <pre class="spreadsheet-playground__error" data-role="error" role="alert" hidden></pre>
                <div class="spreadsheet-playground__result" data-role="result">Jalankan perhitungan untuk melihat hasil.</div>
                <dl class="spreadsheet-playground__metrics" data-role="metrics"></dl>
                <p class="practice-feedback" data-role="feedback" aria-live="polite"></p>
            </section>
        </div>`;
    return shell;
}

function buildWorkArea(work, config, state) {
    const controls = work.querySelector('[data-role="controls"]');
    controls.replaceChildren();

    if (config.mode === 'formula') {
        const table = document.createElement('table');
        table.className = 'spreadsheet-playground__formula-table';
        table.innerHTML = '<thead><tr><th scope="col">Cell</th><th scope="col">Tujuan</th><th scope="col">Formula / nilai</th><th scope="col">Hasil</th></tr></thead>';
        const body = document.createElement('tbody');
        for (const [cell, starter] of Object.entries(config.starter_cells ?? {})) {
            const row = document.createElement('tr');
            const cellName = document.createElement('th');
            cellName.scope = 'row';
            cellName.textContent = cell;
            const label = document.createElement('td');
            label.textContent = config.cell_labels?.[cell] ?? 'Target analisis';
            const inputCell = document.createElement('td');
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'spreadsheet-playground__input';
            input.dataset.cell = cell;
            input.value = starter;
            input.setAttribute('aria-label', `${cell}: ${label.textContent}`);
            inputCell.append(input);
            const output = document.createElement('td');
            output.dataset.outputCell = cell;
            output.textContent = 'Belum dihitung';
            row.append(cellName, label, inputCell, output);
            body.append(row);
        }
        table.append(body);
        controls.append(table);
        return;
    }

    if (config.mode === 'table') {
        const filter = document.createElement('div');
        filter.className = 'spreadsheet-playground__filters';
        filter.append(
            createSelect('Region', 'region', ['all', ...new Set(config.fixture.tables.transactions.rows.map((row) => row.region))], state.region),
            createSelect('Bulan', 'month', ['all', ...new Set(config.fixture.tables.transactions.rows.map((row) => row.order_date.slice(0, 7)))], state.month),
            createSelect('Urutkan', 'sort', ['order_id_asc', 'order_date_asc', 'order_date_desc', 'revenue_desc', 'revenue_asc', 'quantity_desc'], state.sort),
        );
        controls.append(filter);
        filter.querySelectorAll('select').forEach((select) => select.addEventListener('change', () => {
            state[select.dataset.control] = select.value;
        }));
    }

    if (config.mode === 'summary') {
        const note = document.createElement('p');
        note.className = 'spreadsheet-playground__summary-note';
        note.textContent = `Ringkasan tetap memakai ${config.summary_dimension} sebagai dimension dan ${config.summary_measures.join(', ')} sebagai measure.`;
        controls.append(note);
    }
}

function hydrateControls(work, state, config) {
    work.querySelectorAll('[data-cell]').forEach((input) => { input.value = state.cells[input.dataset.cell] ?? ''; });
    work.querySelectorAll('[data-control]').forEach((select) => { select.value = state[select.dataset.control] ?? select.value; });
}

function createSelect(labelText, control, options, selected) {
    const label = document.createElement('label');
    label.className = 'spreadsheet-playground__filter';
    const text = document.createElement('span');
    text.textContent = labelText;
    const select = document.createElement('select');
    select.dataset.control = control;
    options.forEach((value) => {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = value === 'all' ? 'Semua' : value;
        option.selected = value === selected;
        select.append(option);
    });
    label.append(text, select);
    return label;
}

function renderDataset(element, fixture, columns) {
    element.replaceChildren();
    const title = document.createElement('p');
    title.className = 'spreadsheet-playground__label';
    title.textContent = `${fixture.dataset_key}/${fixture.version}`;
    element.append(title);
    const grain = document.createElement('p');
    grain.className = 'spreadsheet-playground__dataset-note';
    grain.textContent = fixture.grain;
    element.append(grain);

    for (const [name, table] of Object.entries(fixture.tables)) {
        const details = document.createElement('details');
        details.className = 'spreadsheet-playground__sheet';
        const summary = document.createElement('summary');
        summary.textContent = `${name} (${table.rows.length} baris)`;
        details.append(summary);
        const list = document.createElement('ul');
        list.className = 'spreadsheet-playground__columns';
        for (const [column, type] of Object.entries(table.columns)) {
            const item = document.createElement('li');
            item.textContent = `${column} · ${type}`;
            list.append(item);
        }
        details.append(list);
        element.append(details);
    }

    const note = document.createElement('p');
    note.className = 'spreadsheet-playground__dataset-note';
    note.textContent = `Tabel transaksi menampilkan ${columns.length} kolom yang relevan untuk latihan ini.`;
    element.append(note);
}

function renderFormulaValues(work, values) {
    work.querySelectorAll('[data-output-cell]').forEach((output) => {
        const value = values[output.dataset.outputCell];
        output.textContent = formatValue(value);
        output.dataset.error = typeof value === 'string' && value.startsWith('#') ? 'true' : 'false';
    });
}

function renderResult(element, result) {
    if (! result || ! result.columns?.length) {
        element.textContent = 'Belum ada hasil.';
        return;
    }
    const table = document.createElement('table');
    const thead = document.createElement('thead');
    const header = document.createElement('tr');
    result.columns.forEach((column) => {
        const cell = document.createElement('th');
        cell.scope = 'col';
        cell.textContent = column;
        header.append(cell);
    });
    thead.append(header);
    const tbody = document.createElement('tbody');
    result.rows.forEach((row) => {
        const tr = document.createElement('tr');
        row.forEach((value) => {
            const cell = document.createElement('td');
            cell.textContent = formatValue(value);
            tr.append(cell);
        });
        tbody.append(tr);
    });
    table.append(thead, tbody);
    element.replaceChildren(table);
}

function renderMetrics(element, values) {
    element.replaceChildren();
    Object.entries(values).forEach(([label, value]) => {
        const wrapper = document.createElement('div');
        const term = document.createElement('dt');
        const detail = document.createElement('dd');
        term.textContent = label;
        detail.textContent = String(value);
        wrapper.append(term, detail);
        element.append(wrapper);
    });
}

function formatValue(value) {
    return value === null || value === undefined ? '' : String(value);
}

function escapeText(value) {
    const span = document.createElement('span');
    span.textContent = value ?? '';
    return span.innerHTML;
}
