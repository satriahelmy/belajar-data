import { matchesResultBasedAnswer } from './practice-validator.js';

export function mount(element) {
    const mountPoint = element.querySelector('[data-role="practice-mount"]');

    if (!mountPoint) {
        element.dataset.enhanced = 'false';
        return;
    }

    try {
        const config = JSON.parse(element.dataset.config ?? '{}');
        const component = createPracticeComponent(element, mountPoint, config);

        component.initialize(config);
        element.learningComponent = component;
        element.dataset.enhanced = 'true';

        if (document.body?.dataset.authenticated === 'true' && config.exercise_key) {
            void loadAttempt(element, config, component);
        }
    } catch {
        const feedback = document.createElement('p');
        feedback.className = 'practice-feedback';
        feedback.textContent = 'Latihan ini belum dapat dibuka. Coba muat ulang halaman.';
        mountPoint.replaceChildren(feedback);
        element.dataset.enhanced = 'false';
    }
}

function createPracticeComponent(element, mountPoint, config) {
    if (config.type === 'multiple_choice') {
        return createMultipleChoice(element, mountPoint, config);
    }
    if (config.type === 'multi_select') {
        return createMultiSelect(element, mountPoint, config);
    }
    if (config.type === 'numeric') {
        return createNumeric(element, mountPoint, config);
    }
    if (config.type === 'text_self_assessment') {
        return createSelfAssessment(element, mountPoint, config);
    }
    if (config.type === 'result_based') {
        return createResultBased(element, mountPoint, config);
    }

    throw new Error('Unsupported practice type');
}

function createMultipleChoice(element, mountPoint, config) {
    const fieldset = document.createElement('fieldset');
    fieldset.className = 'practice-options';

    const legend = document.createElement('legend');
    legend.textContent = 'Pilih satu jawaban';
    fieldset.append(legend);

    (config.options ?? []).forEach((option, index) => {
        const label = document.createElement('label');
        label.className = 'practice-option';

        const input = document.createElement('input');
        input.type = 'radio';
        input.name = `practice-${config.id}`;
        input.value = String(index);

        const text = document.createElement('span');
        text.textContent = option;
        label.append(input, text);
        fieldset.append(label);
    });

    const controller = createDeterministicController(element, mountPoint, config, {
        fieldset,
        getAnswer: () => {
            const selected = fieldset.querySelector('input:checked');

            return { option: selected ? Number(selected.value) : null };
        },
        validate: (answer) => {
            if (! Number.isInteger(answer.option)) {
                return incompleteResult(answer, 'Pilih satu jawaban terlebih dahulu.');
            }

            return deterministicResult(answer.option === Number(config.correct_option), answer, config);
        },
        reset: () => fieldset.querySelectorAll('input').forEach((input) => { input.checked = false; }),
        hydrate: (answer) => {
            const input = fieldset.querySelector(`input[value="${answer.option}"]`);
            if (input) input.checked = true;
        },
    });

    mountPoint.replaceChildren(fieldset);
    mountControls(element, mountPoint, controller, config);

    return controller;
}

function createMultiSelect(element, mountPoint, config) {
    const fieldset = document.createElement('fieldset');
    fieldset.className = 'practice-options';

    const legend = document.createElement('legend');
    legend.textContent = 'Pilih semua jawaban yang tepat';
    fieldset.append(legend);

    (config.options ?? []).forEach((option, index) => {
        const label = document.createElement('label');
        label.className = 'practice-option';

        const input = document.createElement('input');
        input.type = 'checkbox';
        input.name = `practice-${config.id}`;
        input.value = String(index);

        const text = document.createElement('span');
        text.textContent = option;
        label.append(input, text);
        fieldset.append(label);
    });

    const controller = createDeterministicController(element, mountPoint, config, {
        fieldset,
        getAnswer: () => ({
            options: [...fieldset.querySelectorAll('input:checked')].map((input) => Number(input.value)),
        }),
        validate: (answer) => {
            if (! answer.options.length) {
                return incompleteResult(answer, 'Pilih jawaban yang menurutmu tepat.');
            }

            const selected = [...answer.options].sort((a, b) => a - b);
            const expected = [...(config.correct_options ?? [])].sort((a, b) => a - b);

            return deterministicResult(JSON.stringify(selected) === JSON.stringify(expected), answer, config);
        },
        reset: () => fieldset.querySelectorAll('input').forEach((input) => { input.checked = false; }),
        hydrate: (answer) => {
            (answer.options ?? []).forEach((option) => {
                const input = fieldset.querySelector(`input[value="${option}"]`);
                if (input) input.checked = true;
            });
        },
    });

    mountPoint.replaceChildren(fieldset);
    mountControls(element, mountPoint, controller, config);

    return controller;
}

function createNumeric(element, mountPoint, config) {
    const input = document.createElement('input');
    input.className = 'practice-number';
    input.type = 'number';
    input.step = 'any';
    input.inputMode = 'decimal';
    input.setAttribute('aria-label', 'Jawaban angka');

    const controller = createDeterministicController(element, mountPoint, config, {
        fieldset: input,
        getAnswer: () => ({ value: input.value === '' ? null : Number(input.value) }),
        validate: (answer) => {
            if (! Number.isFinite(answer.value)) {
                return incompleteResult(answer, 'Masukkan angka sebelum mengecek jawaban.');
            }

            const validator = config.validator ?? {};
            const valid = Math.abs(answer.value - Number(validator.expected)) <= Number(validator.tolerance ?? 0);

            return deterministicResult(valid, answer, config);
        },
        reset: () => { input.value = ''; },
        hydrate: (answer) => { input.value = answer.value ?? ''; },
    });

    mountPoint.replaceChildren(input);
    mountControls(element, mountPoint, controller, config);

    return controller;
}

function createSelfAssessment(element, mountPoint, config) {
    const label = document.createElement('label');
    label.className = 'practice-writing-label';
    label.textContent = 'Tulis jawabanmu';

    const textarea = document.createElement('textarea');
    textarea.rows = 5;
    textarea.className = 'practice-writing';
    textarea.placeholder = 'Tulis 1–2 kalimat...';
    label.append(textarea);

    const review = document.createElement('div');
    review.className = 'practice-review';
    review.hidden = true;

    const referenceTitle = document.createElement('strong');
    referenceTitle.textContent = 'Reference answer';
    const reference = document.createElement('p');
    reference.textContent = config.reference_answer ?? '';
    review.append(referenceTitle, reference);

    const checklistTitle = document.createElement('strong');
    checklistTitle.textContent = 'Self-assessment';
    review.append(checklistTitle);

    const checklist = document.createElement('ul');
    (config.checklist ?? []).forEach((item) => {
        const listItem = document.createElement('li');
        const checkLabel = document.createElement('label');
        const check = document.createElement('input');
        check.type = 'checkbox';
        checkLabel.append(check, document.createTextNode(` ${item}`));
        listItem.append(checkLabel);
        checklist.append(listItem);
    });
    review.append(checklist);

    const controller = {
        initialize() {},
        getAnswer() {
            return {
                text: textarea.value,
                checklist: [...checklist.querySelectorAll('input')].map((input) => input.checked),
                complete: [...checklist.querySelectorAll('input')].length > 0
                    && [...checklist.querySelectorAll('input')].every((input) => input.checked),
            };
        },
        validate(answer) {
            if (! answer.text.trim()) {
                return incompleteResult(answer, 'Tulis jawabanmu terlebih dahulu.');
            }

            return {
                valid: true,
                completed: answer.complete === true,
                status: answer.complete === true ? 'completed' : 'reviewed',
                score: null,
                feedback: answer.complete === true ? 'Latihan selesai.' : 'Cocokkan jawabanmu dengan reference answer dan checklist.',
                answer,
            };
        },
        reset() {
            textarea.value = '';
            checklist.querySelectorAll('input').forEach((input) => { input.checked = false; });
            review.hidden = true;
            emitProgress(element, false);
        },
        emitProgress(completed) {
            emitProgress(element, completed);
        },
        hydrate(answer) {
            textarea.value = answer.text ?? '';
            (answer.checklist ?? []).forEach((checked, index) => {
                const input = checklist.querySelectorAll('input')[index];
                if (input) input.checked = checked === true;
            });
            review.hidden = false;
        },
    };

    mountPoint.replaceChildren(label, review);
    mountControls(element, mountPoint, controller, config, { selfAssessment: true });

    return controller;
}

function createResultBased(element, mountPoint, config) {
    const input = document.createElement('textarea');
    input.className = 'practice-result-json';
    input.rows = 6;
    input.placeholder = '{"columns":[...],"rows":[[...]]}';
    input.setAttribute('aria-label', 'Hasil tabel dalam JSON');

    const controller = createDeterministicController(element, mountPoint, config, {
        fieldset: input,
        getAnswer: () => {
            try {
                return JSON.parse(input.value || '{}');
            } catch {
                return {};
            }
        },
        validate: (answer) => {
            if (! Array.isArray(answer.columns) || ! Array.isArray(answer.rows)) {
                return incompleteResult(answer, 'Masukkan columns dan rows dalam format JSON.');
            }

            const valid = matchesResultBasedAnswer(answer, config.validator ?? {});

            return deterministicResult(valid, answer, config);
        },
        reset: () => { input.value = ''; },
        hydrate: (answer) => { input.value = JSON.stringify(answer, null, 2); },
    });

    mountPoint.replaceChildren(input);
    mountControls(element, mountPoint, controller, config);

    return controller;
}

function createDeterministicController(element, mountPoint, config, fields) {
    const controller = {
        initialize() {},
        getAnswer: fields.getAnswer,
        validate: fields.validate,
        reset() {
            fields.reset();
            emitProgress(element, false);
        },
        emitProgress(completed) {
            emitProgress(element, completed);
        },
        hydrate: fields.hydrate,
    };

    return controller;
}

function mountControls(element, mountPoint, controller, config, options = {}) {
    const actions = document.createElement('div');
    actions.className = 'practice-actions';

    const check = document.createElement('button');
    check.type = 'button';
    check.className = 'button button--quiet practice-action';
    check.textContent = options.selfAssessment ? 'Lihat reference answer' : 'Cek jawaban';

    const reset = document.createElement('button');
    reset.type = 'button';
    reset.className = 'button button--quiet practice-action';
    reset.textContent = 'Reset';

    actions.append(check, reset);

    let hintIndex = 0;
    let hint = null;
    if ((config.hints ?? []).length > 0) {
        hint = document.createElement('button');
        hint.type = 'button';
        hint.className = 'button button--quiet practice-action';
        hint.textContent = 'Petunjuk';
        actions.append(hint);
    }

    const feedback = document.createElement('p');
    feedback.className = 'practice-feedback';
    feedback.setAttribute('aria-live', 'polite');

    const hintOutput = document.createElement('p');
    hintOutput.className = 'practice-hint';
    hintOutput.hidden = true;
    hintOutput.setAttribute('aria-live', 'polite');

    check.addEventListener('click', async () => {
        if (options.selfAssessment) {
            const review = mountPoint.querySelector('.practice-review');
            if (review) review.hidden = false;
        }

        const result = controller.validate(controller.getAnswer());
        const persisted = await persistResult(config, controller, result);
        renderResult(feedback, element, controller, persisted);
    });

    reset.addEventListener('click', async () => {
        controller.reset();
        feedback.textContent = '';
        feedback.dataset.state = '';
        hintOutput.hidden = true;
        hintIndex = 0;

        if (document.body?.dataset.authenticated === 'true' && config.exercise_key && window.axios) {
            try {
                await window.axios.delete('/attempts', { data: { exercise_key: config.exercise_key } });
            } catch {
                feedback.textContent = 'Progress belum dapat di-reset. Coba lagi.';
                feedback.dataset.state = 'incorrect';
            }
        }
    });

    hint?.addEventListener('click', () => {
        const hints = config.hints ?? [];
        hintOutput.hidden = false;
        hintOutput.textContent = hints[Math.min(hintIndex, hints.length - 1)];
        hintIndex += 1;
    });

    mountPoint.append(actions, hintOutput, feedback);
}

async function persistResult(config, controller, result) {
    if (document.body?.dataset.authenticated !== 'true' || !config.exercise_key || !window.axios) {
        return result;
    }

    try {
        const response = await window.axios.post('/attempts/check', {
            exercise_key: config.exercise_key,
            answer: result.answer,
        });
        return response.data.result ?? result;
    } catch {
        return {
            ...result,
            feedback: 'Jawaban dicatat di sesi ini, tetapi belum tersimpan ke akun.',
        };
    }
}

async function loadAttempt(element, config, controller) {
    try {
        const response = await window.axios.get('/attempts', { params: { exercise_key: config.exercise_key } });
        const attempt = response.data.attempt;

        if (! attempt?.answer) {
            return;
        }

        controller.hydrate(attempt.answer);
        if (attempt.status === 'completed') {
            renderResult(null, element, controller, {
                completed: true,
                status: 'completed',
                feedback: 'Latihan selesai.',
            });
        }
    } catch {
        // A failed load should not block the practice shell.
    }
}

function renderResult(feedback, element, controller, result) {
    if (feedback) {
        feedback.textContent = result.feedback ?? '';
        feedback.dataset.state = result.status === 'completed' ? 'correct' : result.status;
    }

    element.dataset.completed = result.completed ? 'true' : 'false';
    controller.emitProgress(result.completed === true);
}

function emitProgress(element, completed) {
    element.dataset.completed = completed ? 'true' : 'false';
    element.dispatchEvent(new CustomEvent('practice:progress', {
        bubbles: true,
        detail: { completed },
    }));
}

function deterministicResult(valid, answer, config) {
    return {
        valid,
        completed: valid,
        status: valid ? 'completed' : 'incorrect',
        score: valid ? 1 : 0,
        feedback: valid
            ? (config.success_feedback ?? 'Benar. Hubungkan jawabanmu dengan alasan di lesson.')
            : (config.incorrect_feedback ?? 'Belum tepat. Periksa kembali konsep yang sedang diuji.'),
        answer,
    };
}

function incompleteResult(answer, feedback) {
    return {
        valid: false,
        completed: false,
        status: 'incomplete',
        score: null,
        feedback,
        answer,
    };
}
