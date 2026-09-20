import { SQL_SPIKE_LIMITS, SQL_SPIKE_QUERY_CASES } from './query-cases.js';
import { validateSqlResult } from './result-validator.js';

class SqlWorkerRunner {
    constructor(tables) {
        this.tables = tables;
        this.worker = null;
        this.nextId = 1;
        this.pending = new Map();
    }

    async start() {
        this.worker = new Worker(new URL('./sql-worker.js', import.meta.url), { type: 'module' });
        this.worker.addEventListener('message', (event) => this.handleMessage(event));
        this.worker.addEventListener('error', (event) => this.rejectAll(event.message || 'SQL worker failed.'));
        return this.send('init', { tables: this.tables }, SQL_SPIKE_LIMITS.timeoutMs + 10000);
    }

    async query(sql) {
        if (sql.length > SQL_SPIKE_LIMITS.maxQueryCharacters) {
            throw new Error(`Query is limited to ${SQL_SPIKE_LIMITS.maxQueryCharacters} characters.`);
        }

        return this.send('query', { sql, maxRows: SQL_SPIKE_LIMITS.maxRows }, SQL_SPIKE_LIMITS.timeoutMs);
    }

    async reset() {
        await this.terminate();
        return this.start();
    }

    async terminate() {
        this.rejectAll('SQL worker terminated.');
        this.worker?.terminate();
        this.worker = null;
    }

    send(action, payload, timeoutMs) {
        const id = this.nextId++;

        return new Promise((resolve, reject) => {
            const timeout = window.setTimeout(() => {
                this.pending.delete(id);
                this.worker?.terminate();
                this.worker = null;
                reject(new Error(`SQL worker timed out after ${timeoutMs} ms and was terminated.`));
            }, timeoutMs);

            this.pending.set(id, {
                resolve: (result) => {
                    window.clearTimeout(timeout);
                    resolve(result);
                },
                reject: (error) => {
                    window.clearTimeout(timeout);
                    reject(error);
                },
            });

            this.worker.postMessage({ id, action, payload });
        });
    }

    handleMessage(event) {
        const { id, result, error } = event.data;
        const pending = this.pending.get(id);

        if (! pending) return;
        this.pending.delete(id);

        if (error) {
            pending.reject(new Error(error));
            return;
        }

        pending.resolve(result);
    }

    rejectAll(message) {
        for (const pending of this.pending.values()) {
            pending.reject(new Error(message));
        }
        this.pending.clear();
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

function memoryUsage() {
    const memory = performance.memory;

    if (! memory || typeof memory.usedJSHeapSize !== 'number') {
        return 'unavailable in this browser';
    }

    return `${(memory.usedJSHeapSize / 1024 / 1024).toFixed(1)} MiB JS heap`;
}

function renderResult(element, result) {
    if (! result.columns.length) {
        element.innerHTML = '<p>Query returned no tabular result.</p>';
        return;
    }

    const table = document.createElement('table');
    const header = document.createElement('tr');
    for (const column of result.columns) {
        const cell = document.createElement('th');
        cell.scope = 'col';
        cell.textContent = column;
        header.append(cell);
    }

    const thead = document.createElement('thead');
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

function formatFixtureSummary(fixture) {
    return JSON.stringify({
        dataset: `${fixture.dataset_key}/${fixture.version}`,
        profile: fixture.profile,
        grain: fixture.grain,
        tables: Object.fromEntries(Object.entries(fixture.tables).map(([name, table]) => [name, {
            rows: table.rows.length,
            columns: Object.keys(table.columns),
        }])),
        relationships: fixture.relationships,
    }, null, 2);
}

async function mountSqlSpike(root) {
    const fixtureElement = document.querySelector('#sql-spike-fixture');
    const fixture = JSON.parse(fixtureElement?.textContent ?? '{}');
    const runner = new SqlWorkerRunner(fixture.tables);
    const select = root.querySelector('[data-role="case-select"]');
    const editor = root.querySelector('[data-role="editor"]');
    const status = root.querySelector('[data-role="status"]');
    const error = root.querySelector('[data-role="error"]');
    const result = root.querySelector('[data-role="result"]');
    const metrics = root.querySelector('[data-role="metrics"]');
    const buttons = root.querySelectorAll('button');

    select.replaceChildren();
    for (const queryCase of SQL_SPIKE_QUERY_CASES) {
        const option = document.createElement('option');
        option.value = queryCase.id;
        option.textContent = queryCase.label;
        select.append(option);
    }

    const selectedCase = () => SQL_SPIKE_QUERY_CASES.find((queryCase) => queryCase.id === select.value) ?? SQL_SPIKE_QUERY_CASES[0];
    const setCase = () => {
        editor.value = selectedCase().sql;
        error.hidden = true;
        error.textContent = '';
    };

    select.addEventListener('change', setCase);
    setCase();
    root.querySelector('[data-role="fixture-summary"]').textContent = formatFixtureSummary(fixture);

    const setBusy = (busy) => buttons.forEach((button) => { button.disabled = busy; });
    const showError = (message) => {
        error.hidden = false;
        error.textContent = message;
        status.textContent = 'Query failed. Fix the SQL or reset the worker.';
    };

    try {
        const init = await runner.start();
        renderMetrics(metrics, {
            'Cold init': `${init.initializationMs.toFixed(1)} ms`,
            'Tables': init.tableCount,
            'Fixture rows': init.rowCount,
            'Heap after init': memoryUsage(),
        });
        status.textContent = 'Ready. SQL is isolated in the browser worker.';
    } catch (initError) {
        showError(initError.message);
        return;
    }

    root.querySelector('[data-action="run"]').addEventListener('click', async () => {
        setBusy(true);
        try {
            const query = await runner.query(editor.value);
            renderResult(result, query);
            renderMetrics(metrics, {
                'Query latency': `${query.queryMs.toFixed(1)} ms`,
                'Rows returned': query.rowCount,
                'Rows shown': query.rows.length,
                'Output bounded': query.truncated ? 'yes' : 'no',
            });
            status.textContent = 'Query completed in the browser worker.';
            error.hidden = true;
        } catch (queryError) {
            showError(queryError.message);
        } finally {
            setBusy(false);
        }
    });

    root.querySelector('[data-action="reset"]').addEventListener('click', async () => {
        setBusy(true);
        const startedAt = performance.now();
        try {
            const reset = await runner.reset();
            renderMetrics(metrics, {
                'Reset + init': `${(performance.now() - startedAt).toFixed(1)} ms`,
                'Tables': reset.tableCount,
                'Heap after reset': memoryUsage(),
            });
            status.textContent = 'Worker reset and fixture reloaded.';
            error.hidden = true;
            result.replaceChildren(document.createTextNode('Run a query to inspect bounded rows.'));
        } catch (resetError) {
            showError(resetError.message);
        } finally {
            setBusy(false);
        }
    });

    root.querySelector('[data-action="suite"]').addEventListener('click', async () => {
        setBusy(true);
        const startedAt = performance.now();
        let passed = 0;

        try {
            for (const queryCase of SQL_SPIKE_QUERY_CASES) {
                const actual = await runner.query(queryCase.sql);
                const validation = validateSqlResult(actual, queryCase.expected, queryCase.options);
                if (! validation.passed) {
                    throw new Error(`Validation failed for [${queryCase.id}].`);
                }
                passed++;
            }
            renderMetrics(metrics, { 'Suite result': `${passed}/${SQL_SPIKE_QUERY_CASES.length} passed`, 'Suite time': `${(performance.now() - startedAt).toFixed(1)} ms` });
            status.textContent = 'Representative Module 03 query suite passed in the browser worker.';
            error.hidden = true;
        } catch (suiteError) {
            showError(suiteError.message);
        } finally {
            setBusy(false);
        }
    });
}

if (document.querySelector('[data-sql-spike]')) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => mountSqlSpike(document.querySelector('[data-sql-spike]')), { once: true });
    } else {
        void mountSqlSpike(document.querySelector('[data-sql-spike]'));
    }
}

export { SqlWorkerRunner, mountSqlSpike };
