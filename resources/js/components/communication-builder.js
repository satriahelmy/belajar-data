export async function mount(element) {
    const mountPoint = element.querySelector('[data-role="communication-mount"]');
    const fallback = element.querySelector('[data-role="communication-fallback"]');

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
        feedback.textContent = 'Communication practice belum dapat dibuka. Struktur finding tetap dapat dibaca.';
        mountPoint.replaceChildren(feedback);
        element.dataset.enhanced = 'false';
    }
}

function createComponent(element, mountPoint, fallback, config) {
    const shell = buildShell(config);
    const controls = shell.querySelector('[data-role="controls"]');
    const review = shell.querySelector('[data-role="review"]');
    const status = shell.querySelector('[data-role="status"]');
    const feedback = shell.querySelector('[data-role="feedback"]');
    const buttons = [...shell.querySelectorAll('button')];

    populateFields(controls, config);
    populateReview(review, config);

    const setBusy = (busy) => buttons.forEach((button) => { button.disabled = busy; });
    const setFeedback = (message, state = '') => {
        feedback.textContent = message;
        feedback.dataset.state = state;
    };
    const setCompleted = (completed) => {
        element.dataset.completed = completed ? 'true' : 'false';
        element.dispatchEvent(new CustomEvent('practice:progress', { bubbles: true, detail: { completed } }));
    };
    const readFields = () => Object.fromEntries(
        config.field_order.map((field) => [field, controls.querySelector(`[data-field="${field}"]`).value]),
    );
    const readChecklist = () => [...review.querySelectorAll('input[type="checkbox"]')].map((input) => input.checked);

    const persist = async (localResult) => {
        if (document.body?.dataset.authenticated !== 'true' || !config.exercise_key || !window.axios) return localResult;
        try {
            const response = await window.axios.post('/attempts/check', { exercise_key: config.exercise_key, answer: localResult.answer });
            return response.data.result ?? localResult;
        } catch {
            return { ...localResult, feedback: 'Jawaban cocok, tetapi belum tersimpan ke akun. Coba lagi.' };
        }
    };

    const resetAttempt = async () => {
        if (document.body?.dataset.authenticated !== 'true' || !config.exercise_key || !window.axios) return;
        await window.axios.delete('/attempts', { data: { exercise_key: config.exercise_key } });
    };

    controls.addEventListener('input', () => setCompleted(false));
    review.addEventListener('change', () => setCompleted(false));

    shell.querySelector('[data-action="review"]').addEventListener('click', async () => {
        setBusy(true);
        review.hidden = false;
        const fields = readFields();
        const answer = buildCommunicationAnswer(fields, readChecklist());
        const completeFields = fieldsComplete(fields, config.field_order);
        const localResult = !completeFields
            ? {
                valid: false,
                completed: false,
                status: 'incomplete',
                score: null,
                feedback: 'Isi kelima bagian finding sebelum meninjau jawaban.',
                answer,
            }
            : {
                valid: true,
                completed: answer.complete,
                status: answer.complete ? 'completed' : 'reviewed',
                score: null,
                feedback: answer.complete
                    ? 'Latihan selesai. Reference answer dan checklist sudah ditinjau.'
                    : 'Cocokkan jawabanmu dengan reference answer dan checklist.',
                answer,
            };
        try {
            const persisted = await persist(localResult);
            setFeedback(persisted.feedback ?? '', persisted.status);
            setCompleted(persisted.completed === true);
        } finally {
            setBusy(false);
        }
    });

    shell.querySelector('[data-action="reset"]').addEventListener('click', async () => {
        setBusy(true);
        try {
            controls.querySelectorAll('textarea, input').forEach((input) => { input.value = ''; });
            review.querySelectorAll('input[type="checkbox"]').forEach((input) => { input.checked = false; });
            review.hidden = true;
            status.textContent = 'Isi lima bagian finding, lalu buka reference answer untuk review.';
            setFeedback('');
            setCompleted(false);
            await resetAttempt();
        } finally {
            setBusy(false);
        }
    });

    return {
        shell,
        async initialize() {
            fallback?.setAttribute('hidden', 'hidden');
            status.textContent = 'Isi lima bagian finding, lalu buka reference answer untuk review.';
            if (document.body?.dataset.authenticated === 'true' && config.exercise_key && window.axios) {
                try {
                    const response = await window.axios.get('/attempts', { params: { exercise_key: config.exercise_key } });
                    const answer = response.data.attempt?.answer;
                    if (answer?.fields && typeof answer.fields === 'object') {
                        Object.entries(answer.fields).forEach(([field, value]) => {
                            const input = controls.querySelector(`[data-field="${field}"]`);
                            if (input) input.value = String(value ?? '');
                        });
                        answer.checklist?.forEach((checked, index) => {
                            const input = review.querySelectorAll('input[type="checkbox"]')[index];
                            if (input) input.checked = checked === true;
                        });
                        review.hidden = false;
                    }
                    if (response.data.attempt?.status === 'completed') {
                        setFeedback('Latihan ini sudah selesai. Kamu tetap dapat meninjau finding lain.', 'correct');
                        setCompleted(true);
                    }
                } catch {
                    // A failed attempt load should not block writing.
                }
            }
        },
        getAnswer: () => buildCommunicationAnswer(readFields(), readChecklist()),
    };
}

export function buildCommunicationAnswer(fields, checklist) {
    const normalizedFields = Object.fromEntries(Object.entries(fields).map(([key, value]) => [key, String(value ?? '').trim()]));
    const normalizedChecklist = checklist.map((value) => value === true);

    return {
        text: JSON.stringify(normalizedFields),
        fields: normalizedFields,
        checklist: normalizedChecklist,
        complete: Object.values(normalizedFields).every((value) => value !== '')
            && normalizedChecklist.length > 0
            && normalizedChecklist.every(Boolean),
    };
}

export function fieldsComplete(fields, order) {
    return order.every((field) => String(fields[field] ?? '').trim() !== '');
}

function buildShell(config) {
    const shell = document.createElement('div');
    shell.className = 'communication-builder';
    shell.innerHTML = `
        <p class="communication-builder__guidance">${escapeText(config.desktop_note ?? '')}</p>
        <div class="communication-builder__controls" data-role="controls"></div>
        <div class="communication-builder__actions">
            <button class="button button--primary" type="button" data-action="review">Lihat reference answer</button>
            <button class="button button--quiet" type="button" data-action="reset">Reset</button>
        </div>
        <p class="communication-builder__status" data-role="status" role="status" aria-live="polite"></p>
        <div class="communication-builder__review" data-role="review" hidden></div>
        <p class="practice-feedback" data-role="feedback" aria-live="polite"></p>`;
    return shell;
}

function populateFields(container, config) {
    config.field_order.forEach((field) => {
        const label = document.createElement('label');
        label.className = 'communication-builder__field';
        label.textContent = config.field_labels[field];
        const input = document.createElement('textarea');
        input.dataset.field = field;
        input.rows = field === 'headline' ? 2 : 3;
        input.placeholder = `Tulis ${config.field_labels[field].toLowerCase()}...`;
        input.setAttribute('aria-label', config.field_labels[field]);
        label.append(input);
        container.append(label);
    });
}

function populateReview(container, config) {
    const referenceTitle = document.createElement('strong');
    referenceTitle.textContent = 'Reference answer';
    const reference = document.createElement('p');
    reference.textContent = config.reference_answer;
    const checklistTitle = document.createElement('strong');
    checklistTitle.textContent = 'Self-assessment';
    const checklist = document.createElement('ul');
    config.checklist.forEach((item) => {
        const listItem = document.createElement('li');
        const label = document.createElement('label');
        const input = document.createElement('input');
        input.type = 'checkbox';
        label.append(input, document.createTextNode(` ${item}`));
        listItem.append(label);
        checklist.append(listItem);
    });
    container.append(referenceTitle, reference, checklistTitle, checklist);
}

function escapeText(value) {
    return String(value).replace(/[&<>"']/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[character]));
}
