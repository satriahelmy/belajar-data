import { validateSpreadsheetResult } from '../spreadsheet/result-validator.js';

export async function mount(element) {
    const mountPoint = element.querySelector('[data-role="sampling-mount"]');
    const fallback = element.querySelector('[data-role="sampling-fallback"]');

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
        feedback.textContent = 'Latihan sampling belum dapat dibuka. Ringkasan awal tetap dapat dibaca.';
        mountPoint.replaceChildren(feedback);
        element.dataset.enhanced = 'false';
    }
}

function createComponent(element, mountPoint, fallback, config) {
    const shell = buildShell(config);
    const metric = shell.querySelector('[data-control="metric"]');
    const sampleSize = shell.querySelector('[data-control="sampleSize"]');
    const repeats = shell.querySelector('[data-control="repeats"]');
    const seed = shell.querySelector('[data-control="seed"]');
    const output = shell.querySelector('[data-role="output"]');
    const description = shell.querySelector('[data-role="description"]');
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

    const readState = () => ({
        metric: metric.value,
        sampleSize: Number(sampleSize.value),
        repeats: Number(repeats.value),
        seed: Number(seed.value),
    });

    const render = () => {
        current = buildSamplingSummary(config.fixture, readState());
        renderOutput(output, current);
        renderMetrics(metrics, {
            'Metric': readState().metric,
            'Population mean': current.populationMean,
            'Estimasi terendah': current.estimateMin,
            'Estimasi tertinggi': current.estimateMax,
        });
        description.textContent = current.description;
        status.textContent = 'Estimasi diperbarui. Bandingkan sample mean dengan population mean.';
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

    [metric, sampleSize, repeats, seed].forEach((control) => {
        control.addEventListener('change', () => {
            render();
            setFeedback('');
            setCompleted(false);
        });
    });

    shell.querySelector('[data-action="check"]').addEventListener('click', async () => {
        setBusy(true);
        try {
            const state = readState();
            const answer = buildSamplingAnswer(state);
            const validator = config.validator ?? {};
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
                    ? (config.success_feedback ?? 'Tepat. Kamu melihat variasi estimasi sample tanpa menganggap satu sample sebagai population.')
                    : (config.incorrect_feedback ?? 'Pengaturan belum sesuai. Periksa metric, sample size, repeats, dan seed.'),
                answer,
            };
            const persisted = await persist(localResult);
            setFeedback(persisted.feedback ?? '', persisted.status === 'completed' ? 'correct' : persisted.status);
            setCompleted(persisted.completed === true);
        } finally {
            setBusy(false);
        }
    });

    shell.querySelector('[data-action="reset"]').addEventListener('click', async () => {
        setBusy(true);
        try {
            metric.value = config.starter_metric;
            sampleSize.value = String(config.starter_sample_size);
            repeats.value = String(config.starter_repeats);
            seed.value = String(config.starter_seed);
            render();
            setFeedback('');
            setCompleted(false);
            await resetAttempt();
        } finally {
            setBusy(false);
        }
    });

    shell.querySelector('[data-action="hint"]')?.addEventListener('click', () => {
        setFeedback('Bandingkan rentang sample mean. Sample yang berbeda dapat memberi estimasi berbeda walaupun berasal dari population yang sama.', 'incomplete');
    });

    return {
        shell,
        async initialize() {
            populateSelect(metric, config.allowed_metrics, config.starter_metric);
            populateSelect(sampleSize, config.allowed_sample_sizes, config.starter_sample_size);
            populateSelect(repeats, config.allowed_repeats, config.starter_repeats);
            populateSelect(seed, config.allowed_seeds, config.starter_seed);
            fallback?.setAttribute('hidden', 'hidden');
            render();
            if (document.body?.dataset.authenticated === 'true' && config.exercise_key && window.axios) {
                try {
                    const response = await window.axios.get('/attempts', { params: { exercise_key: config.exercise_key } });
                    if (response.data.attempt?.status === 'completed') {
                        setFeedback('Latihan ini sudah selesai. Kamu tetap dapat membandingkan pengaturan lain.', 'correct');
                        setCompleted(true);
                    }
                } catch {
                    // A failed attempt load should not block the simulation.
                }
            }
        },
        getAnswer: () => buildSamplingAnswer(readState()),
    };
}

export function buildSamplingSummary(fixture, state) {
    const values = (fixture.rows ?? []).map((row) => Number(row[state.metric] ?? 0));
    const populationMean = mean(values);
    const estimates = [];

    for (let repeat = 1; repeat <= state.repeats; repeat++) {
        const sample = sampleValues(values, state.sampleSize, state.seed, repeat);
        const sampleMean = mean(sample);
        estimates.push({
            repeat,
            sampleMean: round(sampleMean),
            delta: round(sampleMean - populationMean),
        });
    }

    const sampleMeans = estimates.map((estimate) => estimate.sampleMean);

    return {
        populationMean: round(populationMean),
        estimateMin: Math.min(...sampleMeans),
        estimateMax: Math.max(...sampleMeans),
        estimates,
        description: 'Sample mean dapat berubah dari satu sample ke sample lain. Population mean menjadi pembanding, bukan target yang otomatis diketahui learner dari satu sample.',
    };
}

export function buildSamplingAnswer(state) {
    return {
        columns: ['metric', 'sample_size', 'repeats', 'seed'],
        rows: [[state.metric, state.sampleSize, state.repeats, state.seed]],
    };
}

function sampleValues(values, size, seed, repeat) {
    const indices = values.map((_, index) => index);
    let state = (seed + (repeat * 7919)) % 233280;

    for (let index = indices.length - 1; index > 0; index--) {
        state = ((state * 9301) + 49297) % 233280;
        const swap = Math.floor((state / 233280) * (index + 1));
        [indices[index], indices[swap]] = [indices[swap], indices[index]];
    }

    return indices.slice(0, size).map((index) => values[index]);
}

function mean(values) {
    return values.length === 0 ? 0 : values.reduce((sum, value) => sum + value, 0) / values.length;
}

function round(value) {
    const rounded = Number(value.toFixed(2));
    return Number.isInteger(rounded) ? rounded : rounded;
}

function buildShell(config) {
    const shell = document.createElement('div');
    shell.className = 'sampling-uncertainty-playground';
    shell.innerHTML = `
        <p class="sampling-uncertainty-playground__guidance">${escapeText(config.desktop_note ?? '')}</p>
        <div class="sampling-uncertainty-playground__controls" aria-label="Pengaturan sampling">
            <label>Metric<select data-control="metric"></select></label>
            <label>Ukuran sample<select data-control="sampleSize"></select></label>
            <label>Jumlah pengulangan<select data-control="repeats"></select></label>
            <label>Seed<select data-control="seed"></select></label>
        </div>
        <div class="sampling-uncertainty-playground__actions">
            <button class="button button--primary" type="button" data-action="check">Cek pengaturan</button>
            <button class="button button--quiet" type="button" data-action="reset">Reset</button>
            <button class="button button--quiet" type="button" data-action="hint">Petunjuk</button>
        </div>
        <p class="sampling-uncertainty-playground__status" data-role="status" role="status" aria-live="polite"></p>
        <div class="sampling-uncertainty-playground__output" data-role="output"></div>
        <p class="sampling-uncertainty-playground__description" data-role="description"></p>
        <dl class="sampling-uncertainty-playground__metrics" data-role="metrics"></dl>
        <p class="practice-feedback" data-role="feedback" aria-live="polite"></p>`;
    return shell;
}

function populateSelect(select, values, selected) {
    select.replaceChildren();
    (values ?? []).forEach((value) => {
        const option = document.createElement('option');
        option.value = String(value);
        option.textContent = String(value);
        select.append(option);
    });
    select.value = String(selected);
}

function renderOutput(container, summary) {
    const table = document.createElement('table');
    const caption = document.createElement('caption');
    caption.textContent = 'Estimasi sample mean';
    table.append(caption);
    const head = document.createElement('thead');
    const headRow = document.createElement('tr');
    ['Repeat', 'Sample mean', 'Delta dari population mean'].forEach((label) => {
        const cell = document.createElement('th');
        cell.scope = 'col';
        cell.textContent = label;
        headRow.append(cell);
    });
    head.append(headRow);
    table.append(head);
    const body = document.createElement('tbody');
    summary.estimates.forEach((estimate) => {
        const row = document.createElement('tr');
        [estimate.repeat, estimate.sampleMean, estimate.delta].forEach((value, index) => {
            const cell = document.createElement(index === 0 ? 'th' : 'td');
            if (index === 0) cell.scope = 'row';
            cell.textContent = String(value);
            row.append(cell);
        });
        body.append(row);
    });
    table.append(body);
    container.replaceChildren(table);
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
