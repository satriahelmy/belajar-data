import { validateSpreadsheetResult } from '../spreadsheet/result-validator.js';

const PALETTE = ['#007f6d', '#0f6b78', '#c27a2c', '#7b4f8a', '#2c5f8a', '#b24c63'];

export async function mount(element) {
    const mountPoint = element.querySelector('[data-role="visualization-mount"]');
    const fallback = element.querySelector('[data-role="visualization-fallback"]');

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
    } catch (error) {
        const feedback = document.createElement('p');
        feedback.className = 'practice-feedback';
        feedback.dataset.state = 'incorrect';
        feedback.textContent = error instanceof Error
            ? `Visualisasi belum dapat dibuka: ${error.message}`
            : 'Visualisasi belum dapat dibuka. Tabel ringkasan tetap dapat dibaca.';
        mountPoint.replaceChildren(feedback);
        element.dataset.enhanced = 'false';
    }
}

function createComponent(element, mountPoint, fallback, config) {
    const shell = buildShell(config);
    const state = {
        metric: config.starter_metric,
        dimension: config.starter_dimension,
        chart: config.starter_chart,
        sort: config.starter_sort,
        highlight: config.starter_highlight,
        scaleMode: config.starter_scale_mode,
    };
    const canvas = shell.querySelector('[data-role="canvas"]');
    const table = shell.querySelector('[data-role="table"]');
    const description = shell.querySelector('[data-role="description"]');
    const status = shell.querySelector('[data-role="status"]');
    const error = shell.querySelector('[data-role="error"]');
    const feedback = shell.querySelector('[data-role="feedback"]');
    const metrics = shell.querySelector('[data-role="metrics"]');
    const buttons = [...shell.querySelectorAll('button')];
    let chartInstance = null;
    let chartConstructor = null;
    let resizeObserver = null;

    const setBusy = (busy) => buttons.forEach((button) => { button.disabled = busy; });
    const setFeedback = (message, stateName = '') => {
        feedback.textContent = message;
        feedback.dataset.state = stateName;
    };
    const setCompleted = (completed) => {
        element.dataset.completed = completed ? 'true' : 'false';
        element.dispatchEvent(new CustomEvent('practice:progress', {
            bubbles: true,
            detail: { completed },
        }));
    };
    const clearError = () => {
        error.hidden = true;
        error.textContent = '';
    };
    const showError = (message) => {
        error.hidden = false;
        error.textContent = message;
        status.textContent = 'Visual gagal diperbarui. Coba pilihan lain atau reset.';
    };

    const render = () => {
        const view = buildVisualizationView(config.fixture, state);
        renderTable(table, view);
        description.textContent = view.description;
        renderMetrics(metrics, {
            'Visual': state.chart,
            'Metric': state.metric,
            'Dimensi': state.dimension,
            'Baris': view.rows.length,
        });
        renderChart(canvas, view, state, chartConstructor, chartInstance, (next) => { chartInstance = next; });
        status.textContent = 'Visual diperbarui. Baca tabel dan keterangannya sebelum menyimpulkan.';
        clearError();
        return view;
    };

    const persist = async (localResult) => {
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
            return { ...localResult, feedback: 'Hasilnya cocok, tetapi belum tersimpan ke akun. Coba lagi.' };
        }
    };

    const resetAttempt = async () => {
        if (document.body?.dataset.authenticated !== 'true' || !config.exercise_key || !window.axios) {
            return;
        }

        await window.axios.delete('/attempts', { data: { exercise_key: config.exercise_key } });
    };

    shell.querySelectorAll('[data-control]').forEach((control) => {
        control.addEventListener('change', () => {
            state[control.dataset.control] = control.value;
            try {
                render();
                setFeedback('');
            } catch (renderError) {
                showError(renderError instanceof Error ? renderError.message : String(renderError));
            }
        });
    });

    shell.querySelector('[data-action="check"]').addEventListener('click', async () => {
        setBusy(true);
        try {
            const answer = buildVisualizationAnswer(state);
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
                    ? (config.success_feedback ?? 'Pilihan visualnya tepat. Hubungkan bentuk visual dengan pertanyaan yang ingin dijawab.')
                    : (config.incorrect_feedback ?? 'Pilihan visual belum sesuai. Periksa kembali pertanyaan, metric, dimensi, dan urutannya.'),
                answer,
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
        try {
            Object.assign(state, {
                metric: config.starter_metric,
                dimension: config.starter_dimension,
                chart: config.starter_chart,
                sort: config.starter_sort,
                highlight: config.starter_highlight,
                scaleMode: config.starter_scale_mode,
            });
            syncControls(shell, state);
            render();
            setFeedback('');
            setCompleted(false);
            await resetAttempt();
        } catch (resetError) {
            showError(resetError instanceof Error ? resetError.message : String(resetError));
        } finally {
            setBusy(false);
        }
    });

    shell.querySelector('[data-action="hint"]')?.addEventListener('click', () => {
        setFeedback('Mulai dari pertanyaan: apakah kamu sedang membandingkan kategori, melihat perubahan waktu, atau mencari hubungan dua angka?', 'incomplete');
    });

    return {
        shell,
        async initialize() {
            populateControls(shell, config, state);
            status.textContent = 'Menyiapkan visual...';
            chartConstructor = await loadChartConstructor();
            fallback?.setAttribute('hidden', 'hidden');
            render();
            resizeObserver = typeof ResizeObserver === 'function'
                ? new ResizeObserver(() => chartInstance?.resize())
                : null;
            resizeObserver?.observe(canvas.parentElement);
            if (document.body?.dataset.authenticated === 'true' && config.exercise_key && window.axios) {
                try {
                    const response = await window.axios.get('/attempts', { params: { exercise_key: config.exercise_key } });
                    if (response.data.attempt?.status === 'completed') {
                        setFeedback('Latihan ini sudah selesai. Kamu tetap dapat menguji pilihan visual lain.', 'correct');
                        setCompleted(true);
                    }
                } catch {
                    // A failed attempt load should not block the visual.
                }
            }
        },
        destroy() {
            resizeObserver?.disconnect();
            chartInstance?.destroy();
        },
        getAnswer: () => buildVisualizationAnswer(state),
    };
}

async function loadChartConstructor() {
    const {
        BarController,
        BarElement,
        CategoryScale,
        Chart,
        Legend,
        LinearScale,
        LineController,
        LineElement,
        PointElement,
        ScatterController,
        Tooltip,
    } = await import('chart.js');

    Chart.register(
        BarController,
        BarElement,
        CategoryScale,
        Legend,
        LinearScale,
        LineController,
        LineElement,
        PointElement,
        ScatterController,
        Tooltip,
    );

    return Chart;
}

export function buildVisualizationAnswer(state) {
    return {
        columns: ['chart', 'metric', 'dimension', 'sort', 'highlight', 'scale_mode'],
        rows: [[state.chart, state.metric, state.dimension, state.sort, state.highlight, state.scaleMode]],
    };
}

export function buildVisualizationView(fixture, state) {
    if (state.chart === 'scatter') {
        const rows = [...(fixture.rows ?? [])]
            .sort((left, right) => state.sort === 'descending'
                ? Number(right.revenue) - Number(left.revenue)
                : Number(left.revenue) - Number(right.revenue));

        return {
            chartType: 'scatter',
            labels: rows.map((row) => row.order_id),
            datasets: [{
                label: state.metric,
                data: rows.map((row) => ({ x: Number(row.quantity), y: Number(row.revenue) })),
            }],
            columns: ['order_id', 'quantity', 'revenue'],
            rows: rows.map((row) => [row.order_id, Number(row.quantity), Number(row.revenue)]),
            description: 'Setiap titik mewakili satu baris transaksi. Posisi titik membantu membandingkan quantity dan revenue, bukan membuktikan sebab-akibat.',
        };
    }

    if (state.chart === 'histogram') {
        const bins = [
            { label: '0–99', min: 0, max: 100 },
            { label: '100–199', min: 100, max: 200 },
            { label: '200+', min: 200, max: Number.POSITIVE_INFINITY },
        ];
        const rows = bins.map((bin) => [
            bin.label,
            (fixture.rows ?? []).filter((row) => Number(row.revenue) >= bin.min && Number(row.revenue) < bin.max).length,
        ]);

        return {
            chartType: 'bar',
            labels: rows.map(([label]) => label),
            datasets: [{ label: 'Jumlah transaksi', data: rows.map(([, value]) => value) }],
            columns: ['revenue_bin', 'transactions'],
            rows,
            description: 'Histogram ini mengelompokkan revenue transaksi ke rentang tetap. Gunakan bentuk sebaran untuk melihat konsentrasi, bukan untuk menyimpulkan penyebab.',
        };
    }

    if (state.chart === 'distribution') {
        const distributionRows = fixture.views?.[state.dimension]?.rows ?? fixture.rows ?? [];
        const values = [...distributionRows].map((row) => Number(row[state.metric] ?? 0)).sort((left, right) => left - right);
        const rows = [
            ['Minimum', quantile(values, 0)],
            ['Q1', quantile(values, .25)],
            ['Median', quantile(values, .5)],
            ['Q3', quantile(values, .75)],
            ['Maximum', quantile(values, 1)],
        ];

        return {
            chartType: 'bar',
            labels: rows.map(([label]) => label),
            datasets: [{ label: state.metric, data: rows.map(([, value]) => value) }],
            columns: ['statistic', state.metric],
            rows,
            description: `Distribusi ${state.metric} diringkas dengan minimum, kuartil, median, dan maksimum. Median dan rentang antar-kuartil membantu membaca nilai tipikal dan variasi.`,
        };
    }

    if (state.chart === 'composition') {
        const monthly = fixture.views?.month?.rows ?? [];
        const months = sortLabels(monthly.map((row) => row.label), 'chronological');
        const categories = unique((fixture.rows ?? []).map((row) => row.category)).sort();
        const datasets = categories.map((category) => ({
            label: category,
            data: months.map((month) => aggregateMetric(
                (fixture.rows ?? []).filter((row) => row.month === month && row.category === category),
                state.metric,
            )),
        }));
        const rows = months.map((month) => [month, ...datasets.map((dataset) => dataset.data[months.indexOf(month)])]);

        return {
            chartType: 'bar',
            labels: months,
            datasets,
            columns: ['month', ...categories],
            rows,
            description: 'Composition memperlihatkan total tiap periode sekaligus bagian kategori di dalamnya. Periksa total dan bagian sebelum menyebut perubahan sebagai penyebab.',
        };
    }

    const view = fixture.views?.[state.dimension];
    if (! view) {
        throw new Error('Dimensi visual tidak tersedia.');
    }

    const rows = sortRows(view.rows ?? [], state.metric, state.sort);
    const chartType = state.chart === 'line' ? 'line' : 'bar';

    return {
        chartType,
        labels: rows.map((row) => row.label),
        datasets: [{
            label: state.metric,
            data: rows.map((row) => Number(row[state.metric] ?? 0)),
        }],
        columns: [state.dimension, state.metric, 'orders'],
        rows: rows.map((row) => [row.label, Number(row[state.metric] ?? 0), Number(row.orders ?? 0)]),
        description: `${capitalize(state.metric)} menurut ${state.dimension}. Urutan dan highlight membantu menemukan pola, tetapi angka tetap perlu dibaca bersama konteks dan grain data.`,
    };
}

function buildShell(config) {
    const shell = document.createElement('div');
    shell.className = 'visualization-playground';
    shell.innerHTML = `
        <p class="visualization-playground__guidance">${escapeText(config.desktop_note ?? '')}</p>
        <div class="visualization-playground__controls" aria-label="Pilihan visual">
            <label>Metric<select data-control="metric"></select></label>
            <label>Dimensi<select data-control="dimension"></select></label>
            <label>Bentuk visual<select data-control="chart"></select></label>
            <label>Urutan<select data-control="sort"></select></label>
            <label>Highlight<select data-control="highlight"></select></label>
            <label>Skala<select data-control="scaleMode"></select></label>
        </div>
        <div class="visualization-playground__actions">
            <button class="button button--primary" type="button" data-action="check">Cek pilihan</button>
            <button class="button button--quiet" type="button" data-action="reset">Reset</button>
            <button class="button button--quiet" type="button" data-action="hint">Petunjuk</button>
        </div>
        <p class="visualization-playground__status" data-role="status" role="status" aria-live="polite"></p>
        <p class="visualization-playground__error" data-role="error" role="alert" hidden></p>
        <div class="visualization-playground__chart-wrap">
            <canvas data-role="canvas" role="img" aria-label="Visualisasi data NusaMart"></canvas>
        </div>
        <p class="visualization-playground__description" data-role="description"></p>
        <div class="visualization-playground__table" data-role="table"></div>
        <dl class="visualization-playground__metrics" data-role="metrics"></dl>
        <p class="practice-feedback" data-role="feedback" aria-live="polite"></p>`;
    return shell;
}

function populateControls(shell, config, state) {
    const controls = {
        metric: config.allowed_metrics,
        dimension: config.allowed_dimensions,
        chart: config.allowed_charts,
        sort: config.allowed_sorts,
        highlight: config.allowed_highlights,
        scaleMode: config.allowed_scale_modes,
    };

    for (const [name, values] of Object.entries(controls)) {
        const select = shell.querySelector(`[data-control="${name}"]`);
        for (const value of values ?? []) {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = humanize(value);
            select.append(option);
        }
    }
    syncControls(shell, state);
}

function syncControls(shell, state) {
    for (const [name, value] of Object.entries(state)) {
        const select = shell.querySelector(`[data-control="${name}"]`);
        if (select) select.value = value;
    }
}

function renderChart(canvas, view, state, ChartConstructor, currentChart, setChart) {
    currentChart?.destroy();
    const maxValue = Math.max(...view.datasets.flatMap((dataset) => dataset.data.map((value) => typeof value === 'object' ? Number(value.y) : Number(value))), 0);
    const colors = highlightColors(view.labels, state.highlight, view.datasets[0]?.data ?? []);
    const datasets = view.datasets.map((dataset, index) => ({
        ...dataset,
        backgroundColor: view.datasets.length > 1 ? PALETTE[index % PALETTE.length] : colors,
        borderColor: view.datasets.length > 1 ? PALETTE[index % PALETTE.length] : '#007f6d',
        borderWidth: 2,
        pointRadius: view.chartType === 'scatter' ? 4 : 3,
        tension: view.chartType === 'line' ? .25 : 0,
    }));
    const isScatter = view.chartType === 'scatter';
    const chart = new ChartConstructor(canvas, {
        type: view.chartType,
        data: { labels: isScatter ? undefined : view.labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            plugins: {
                legend: { display: view.datasets.length > 1 },
                tooltip: { enabled: true },
            },
            scales: {
                y: {
                    beginAtZero: state.scaleMode === 'honest',
                    min: state.scaleMode === 'truncated' && maxValue > 0 ? Math.max(0, maxValue * .75) : undefined,
                },
            },
        },
    });
    setChart(chart);
}

function renderTable(container, view) {
    const table = document.createElement('table');
    const caption = document.createElement('caption');
    caption.textContent = 'Tabel angka yang mendasari visual';
    table.append(caption);
    const head = document.createElement('thead');
    const headRow = document.createElement('tr');
    view.columns.forEach((column) => {
        const cell = document.createElement('th');
        cell.scope = 'col';
        cell.textContent = humanize(column);
        headRow.append(cell);
    });
    head.append(headRow);
    table.append(head);
    const body = document.createElement('tbody');
    view.rows.forEach((row) => {
        const rowElement = document.createElement('tr');
        row.forEach((value, index) => {
            const cell = document.createElement(index === 0 ? 'th' : 'td');
            if (index === 0) cell.scope = 'row';
            cell.textContent = String(value);
            rowElement.append(cell);
        });
        body.append(rowElement);
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

function sortRows(rows, metric, sort) {
    return [...rows].sort((left, right) => {
        if (sort === 'chronological') return String(left.label).localeCompare(String(right.label));
        const difference = Number(left[metric] ?? 0) - Number(right[metric] ?? 0);
        return sort === 'ascending' ? difference : -difference || String(left.label).localeCompare(String(right.label));
    });
}

function sortLabels(labels, sort) {
    return [...labels].sort((left, right) => sort === 'chronological'
        ? String(left).localeCompare(String(right))
        : String(left).localeCompare(String(right)));
}

function highlightColors(labels, highlight, values) {
    if (highlight === 'none') return labels.map(() => '#007f6d');
    const topIndex = highlight === 'top'
        ? values.reduce((best, value, index) => Number(value) > Number(values[best] ?? -Infinity) ? index : best, 0)
        : -1;

    return labels.map((label, index) => {
        const active = (highlight === 'top' && index === topIndex) || label === highlight;
        return active ? '#c27a2c' : '#c8d7d4';
    });
}

function quantile(values, position) {
    if (values.length === 0) return 0;
    const index = (values.length - 1) * position;
    const lower = Math.floor(index);
    const upper = Math.ceil(index);
    if (lower === upper) return values[lower];
    return Number((values[lower] + (values[upper] - values[lower]) * (index - lower)).toFixed(2));
}

function unique(values) {
    return [...new Set(values)];
}

function aggregateMetric(rows, metric) {
    if (metric === 'quantity') return rows.reduce((sum, row) => sum + Number(row.quantity ?? 0), 0);
    if (metric === 'orders') return unique(rows.map((row) => row.order_id)).length;

    const revenue = rows.reduce((sum, row) => sum + Number(row.revenue ?? 0), 0);
    if (metric === 'average_order_value') {
        const orders = unique(rows.map((row) => row.order_id)).length;
        return orders > 0 ? Number((revenue / orders).toFixed(2)) : 0;
    }

    return revenue;
}

function capitalize(value) {
    return humanize(value).replace(/^./, (letter) => letter.toUpperCase());
}

function humanize(value) {
    return String(value).replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());
}

function escapeText(value) {
    return String(value).replace(/[&<>"']/g, (character) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    }[character]));
}
