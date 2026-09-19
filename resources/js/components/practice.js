export function mount(element) {
    const mountPoint = element.querySelector('[data-role="practice-mount"]');

    if (!mountPoint) {
        element.dataset.enhanced = 'false';
        return;
    }

    try {
        const config = JSON.parse(element.dataset.config ?? '{}');
        if (config.type === 'multiple_choice') {
            mountMultipleChoice(element, mountPoint, config);
        } else if (config.type === 'text_self_assessment') {
            mountSelfAssessment(element, mountPoint, config);
        } else {
            throw new Error('Unsupported practice type');
        }

        element.dataset.enhanced = 'true';
    } catch {
        const feedback = document.createElement('p');
        feedback.className = 'practice-feedback';
        feedback.textContent = 'Latihan ini belum dapat dibuka. Coba muat ulang halaman.';
        mountPoint.replaceChildren(feedback);
        element.dataset.enhanced = 'false';
    }
}

function mountMultipleChoice(element, mountPoint, config) {
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

    const check = document.createElement('button');
    check.type = 'button';
    check.className = 'button button--quiet practice-action';
    check.textContent = 'Cek jawaban';

    const feedback = document.createElement('p');
    feedback.className = 'practice-feedback';
    feedback.setAttribute('aria-live', 'polite');

    check.addEventListener('click', () => {
        const selected = fieldset.querySelector('input:checked');

        if (!selected) {
            feedback.textContent = 'Pilih satu jawaban terlebih dahulu.';
            feedback.dataset.state = 'incomplete';
            return;
        }

        const correct = Number(selected.value) === Number(config.correct_option);
        feedback.textContent = correct
            ? 'Benar. Hubungkan jawabanmu dengan alasan di lesson.'
            : 'Belum tepat. Baca kembali grain, metric, dan comparison yang disebutkan di atas.';
        feedback.dataset.state = correct ? 'correct' : 'incorrect';
        element.dataset.completed = correct ? 'true' : 'false';
    });

    mountPoint.replaceChildren(fieldset, check, feedback);
}

function mountSelfAssessment(element, mountPoint, config) {
    const label = document.createElement('label');
    label.className = 'practice-writing-label';
    label.textContent = 'Tulis jawabanmu';

    const textarea = document.createElement('textarea');
    textarea.rows = 5;
    textarea.className = 'practice-writing';
    textarea.placeholder = 'Tulis 1–2 kalimat...';
    label.append(textarea);

    const submit = document.createElement('button');
    submit.type = 'button';
    submit.className = 'button button--quiet practice-action';
    submit.textContent = 'Lihat reference answer';

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
        const check = document.createElement('input');
        check.type = 'checkbox';
        const checkLabel = document.createElement('label');
        checkLabel.append(check, document.createTextNode(` ${item}`));
        listItem.append(checkLabel);
        checklist.append(listItem);
    });
    review.append(checklist);

    const complete = document.createElement('button');
    complete.type = 'button';
    complete.className = 'button button--quiet practice-action';
    complete.textContent = 'Tandai latihan selesai';
    complete.addEventListener('click', () => {
        element.dataset.completed = 'true';
        complete.textContent = 'Latihan selesai di sesi ini';
    });

    submit.addEventListener('click', () => {
        if (textarea.value.trim() === '') {
            review.hidden = true;
            textarea.focus();
            return;
        }

        review.hidden = false;
        element.dataset.reviewed = 'true';
    });

    mountPoint.replaceChildren(label, submit, review, complete);
}
