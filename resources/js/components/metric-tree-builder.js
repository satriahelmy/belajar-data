import { validateSpreadsheetResult } from '../spreadsheet/result-validator.js';

export async function mount(element) {
    const mountPoint = element.querySelector('[data-role="metric-tree-mount"]');
    const fallback = element.querySelector('[data-role="metric-tree-fallback"]');

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
        feedback.textContent = 'Metric tree belum dapat dibuka. Susunan awal tetap dapat dibaca.';
        mountPoint.replaceChildren(feedback);
        element.dataset.enhanced = 'false';
    }
}

function createComponent(element, mountPoint, fallback, config) {
    const shell = buildShell(config);
    const state = { ...config.starter_parents };
    const controls = shell.querySelector('[data-role="controls"]');
    const tree = shell.querySelector('[data-role="tree"]');
    const metrics = shell.querySelector('[data-role="metrics"]');
    const status = shell.querySelector('[data-role="status"]');
    const feedback = shell.querySelector('[data-role="feedback"]');
    const buttons = [...shell.querySelectorAll('button')];

    const setBusy = (busy) => buttons.forEach((button) => { button.disabled = busy; });
    const setFeedback = (message, stateName = '') => {
        feedback.textContent = message;
        feedback.dataset.state = stateName;
    };
    const setCompleted = (completed) => {
        element.dataset.completed = completed ? 'true' : 'false';
        element.dispatchEvent(new CustomEvent('practice:progress', { bubbles: true, detail: { completed } }));
    };

    const render = () => {
        renderTree(tree, config.nodes, config.root_id, state);
        renderMetrics(metrics, {
            'Root': labelFor(config.nodes, config.root_id),
            'Child metric': Object.keys(state).length,
            'Relationships': Object.values(state).filter(Boolean).length,
        });
        status.textContent = 'Tree diperbarui. Periksa apakah setiap metric menjelaskan cabang parent yang tepat.';
    };

    const persist = async (localResult) => {
        if (document.body?.dataset.authenticated !== 'true' || !config.exercise_key || !window.axios) return localResult;
        try {
            const response = await window.axios.post('/attempts/check', { exercise_key: config.exercise_key, answer: localResult.answer });
            return response.data.result ?? localResult;
        } catch {
            return { ...localResult, feedback: 'Susunan cocok, tetapi belum tersimpan ke akun. Coba lagi.' };
        }
    };

    const resetAttempt = async () => {
        if (document.body?.dataset.authenticated !== 'true' || !config.exercise_key || !window.axios) return;
        await window.axios.delete('/attempts', { data: { exercise_key: config.exercise_key } });
    };

    controls.addEventListener('change', (event) => {
        const nodeId = event.target.dataset.nodeId;
        if (! nodeId) return;
        state[nodeId] = event.target.value;
        render();
        setFeedback('');
        setCompleted(false);
    });

    shell.querySelector('[data-action="check"]').addEventListener('click', async () => {
        setBusy(true);
        try {
            const answer = buildMetricTreeAnswer(state);
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
                    ? (config.success_feedback ?? 'Tepat. Setiap cabang memiliki hubungan metric yang dapat dijelaskan.')
                    : (config.incorrect_feedback ?? 'Belum tepat. Periksa kembali parent setiap metric dan hubungan formulanya.'),
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
            Object.assign(state, config.starter_parents);
            controls.querySelectorAll('select[data-node-id]').forEach((select) => {
                select.value = state[select.dataset.nodeId];
            });
            render();
            setFeedback('');
            setCompleted(false);
            await resetAttempt();
        } finally {
            setBusy(false);
        }
    });

    shell.querySelector('[data-action="hint"]')?.addEventListener('click', () => {
        setFeedback('Mulai dari pertanyaan: metric apa yang langsung membentuk Revenue, lalu metric apa yang menjelaskan Average order value?', 'incomplete');
    });

    return {
        shell,
        async initialize() {
            populateControls(controls, config.nodes, config.root_id, config.allowed_parent_options, state);
            fallback?.setAttribute('hidden', 'hidden');
            render();
            if (document.body?.dataset.authenticated === 'true' && config.exercise_key && window.axios) {
                try {
                    const response = await window.axios.get('/attempts', { params: { exercise_key: config.exercise_key } });
                    if (response.data.attempt?.status === 'completed') {
                        setFeedback('Latihan ini sudah selesai. Kamu tetap dapat membandingkan susunan lain.', 'correct');
                        setCompleted(true);
                    }
                } catch {
                    // A failed attempt load should not block the builder.
                }
            }
        },
        getAnswer: () => buildMetricTreeAnswer(state),
    };
}

export function buildMetricTreeAnswer(parents) {
    return {
        columns: ['node_id', 'parent_id'],
        rows: Object.entries(parents).map(([nodeId, parentId]) => [nodeId, parentId]),
    };
}

export function buildMetricTreeRows(nodes, rootId, parents) {
    const children = {};
    Object.entries(parents).forEach(([nodeId, parentId]) => {
        children[parentId] ??= [];
        children[parentId].push(nodeId);
    });
    const rows = [];
    const visit = (nodeId, depth, path = new Set()) => {
        if (path.has(nodeId)) return;
        const node = nodes.find((candidate) => candidate.id === nodeId);
        if (! node) return;
        rows.push({ id: node.id, label: node.label, description: node.description, depth });
        const nextPath = new Set(path);
        nextPath.add(nodeId);
        (children[nodeId] ?? []).forEach((childId) => visit(childId, depth + 1, nextPath));
    };
    visit(rootId, 0);
    return rows;
}

function buildShell(config) {
    const shell = document.createElement('div');
    shell.className = 'metric-tree-builder';
    shell.innerHTML = `
        <p class="metric-tree-builder__guidance">${escapeText(config.desktop_note ?? '')}</p>
        <div class="metric-tree-builder__controls" data-role="controls" aria-label="Hubungan metric"></div>
        <div class="metric-tree-builder__actions">
            <button class="button button--primary" type="button" data-action="check">Cek tree</button>
            <button class="button button--quiet" type="button" data-action="reset">Reset</button>
            <button class="button button--quiet" type="button" data-action="hint">Petunjuk</button>
        </div>
        <p class="metric-tree-builder__status" data-role="status" role="status" aria-live="polite"></p>
        <div class="metric-tree-builder__tree" data-role="tree" aria-label="Metric tree"></div>
        <dl class="metric-tree-builder__metrics" data-role="metrics"></dl>
        <p class="practice-feedback" data-role="feedback" aria-live="polite"></p>`;
    return shell;
}

function populateControls(container, nodes, rootId, options, state) {
    container.replaceChildren();
    nodes.filter((node) => node.id !== rootId).forEach((node) => {
        const label = document.createElement('label');
        label.textContent = node.label;
        const select = document.createElement('select');
        select.dataset.nodeId = node.id;
        select.setAttribute('aria-label', `Parent untuk ${node.label}`);
        (options[node.id] ?? []).forEach((parentId) => {
            const option = document.createElement('option');
            option.value = parentId;
            option.textContent = labelFor(nodes, parentId);
            select.append(option);
        });
        select.value = state[node.id];
        label.append(select);
        container.append(label);
    });
}

function renderTree(container, nodes, rootId, parents) {
    const rows = buildMetricTreeRows(nodes, rootId, parents);
    const list = document.createElement('ul');
    list.className = 'metric-tree-builder__list';
    rows.forEach((row) => {
        const item = document.createElement('li');
        item.style.setProperty('--metric-tree-depth', String(row.depth));
        const node = document.createElement('div');
        node.className = row.depth === 0 ? 'metric-tree-builder__node metric-tree-builder__node--root' : 'metric-tree-builder__node';
        const title = document.createElement('strong');
        title.textContent = row.label;
        const description = document.createElement('span');
        description.textContent = row.description;
        node.append(title, description);
        item.append(node);
        list.append(item);
    });
    container.replaceChildren(list);
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

function labelFor(nodes, id) {
    return nodes.find((node) => node.id === id)?.label ?? id;
}

function escapeText(value) {
    return String(value).replace(/[&<>"']/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[character]));
}
