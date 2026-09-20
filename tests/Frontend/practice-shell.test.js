import assert from 'node:assert/strict';
import test from 'node:test';

import { mount } from '../../resources/js/components/practice.js';

class FakeNode {
    constructor() {
        this.parentNode = null;
    }
}

class FakeText extends FakeNode {
    constructor(text) {
        super();
        this.text = text;
    }

    get textContent() {
        return this.text;
    }
}

class FakeElement extends FakeNode {
    constructor(tagName) {
        super();
        this.tagName = tagName.toUpperCase();
        this.children = [];
        this.attributes = {};
        this.listeners = {};
        this.dataset = {};
        this.className = '';
        this.hidden = false;
        this.checked = false;
        this.value = '';
        this.type = '';
    }

    append(...nodes) {
        nodes.flat().forEach((node) => {
            node.parentNode = this;
            this.children.push(node);
        });
    }

    replaceChildren(...nodes) {
        this.children = [];
        this.append(...nodes);
    }

    addEventListener(type, listener) {
        this.listeners[type] ??= [];
        this.listeners[type].push(listener);
    }

    async click() {
        await Promise.all((this.listeners.click ?? []).map((listener) => listener({
            type: 'click',
            target: this,
        })));
    }

    dispatchEvent(event) {
        (this.listeners[event.type] ?? []).forEach((listener) => listener(event));
        return true;
    }

    setAttribute(name, value) {
        this.attributes[name] = String(value);
    }

    getAttribute(name) {
        return this.attributes[name] ?? null;
    }

    get textContent() {
        return this._textContent ?? this.children.map((child) => child.textContent ?? '').join('');
    }

    set textContent(value) {
        this._textContent = String(value);
        this.children = [];
    }

    querySelector(selector) {
        return this.querySelectorAll(selector)[0] ?? null;
    }

    querySelectorAll(selector) {
        return this.descendants().filter((element) => matches(element, selector));
    }

    descendants() {
        return this.children.flatMap((child) => child instanceof FakeElement
            ? [child, ...child.descendants()]
            : []);
    }
}

class FakeDocument {
    constructor() {
        this.body = new FakeElement('body');
    }

    createElement(tagName) {
        return new FakeElement(tagName);
    }

    createTextNode(text) {
        return new FakeText(text);
    }
}

function matches(element, selector) {
    if (selector === 'input') return element.tagName === 'INPUT';
    if (selector === 'button') return element.tagName === 'BUTTON';
    if (selector === 'textarea') return element.tagName === 'TEXTAREA';
    if (selector === 'fieldset') return element.tagName === 'FIELDSET';
    if (selector === 'legend') return element.tagName === 'LEGEND';
    if (selector === 'label') return element.tagName === 'LABEL';
    if (selector === 'input:checked') return element.tagName === 'INPUT' && element.checked;
    if (selector === '.practice-feedback') return element.className === 'practice-feedback';
    if (selector === '.practice-hint') return element.className === 'practice-hint';
    if (selector === '.practice-review') return element.className === 'practice-review';
    if (selector === '[data-role="practice-mount"]') return element.dataset.role === 'practice-mount';

    const valueMatch = selector.match(/^input\[value="([^"]+)"\]$/);
    if (valueMatch) return element.tagName === 'INPUT' && element.value === valueMatch[1];

    return false;
}

function createPracticeBlock(config) {
    globalThis.document = new FakeDocument();
    globalThis.CustomEvent = class {
        constructor(type, init = {}) {
            this.type = type;
            this.detail = init.detail;
        }
    };

    const block = new FakeElement('section');
    block.dataset.config = JSON.stringify(config);
    const mountPoint = new FakeElement('div');
    mountPoint.dataset.role = 'practice-mount';
    block.append(mountPoint);

    const progressEvents = [];
    block.addEventListener('practice:progress', (event) => progressEvents.push(event.detail));
    mount(block);

    return { block, progressEvents };
}

function findButton(block, label) {
    return block.querySelectorAll('button').find((button) => button.textContent === label);
}

test('practice shell supports incorrect, correct, reset, hint, and progress events', async () => {
    const { block, progressEvents } = createPracticeBlock({
        id: 'demo-01',
        type: 'multiple_choice',
        prompt: 'Pilih.',
        options: ['Benar', 'Salah'],
        correct_option: 0,
        hints: ['Baca metric.', 'Cek comparison.'],
    });

    assert.equal(block.dataset.enhanced, 'true');
    assert.equal(typeof block.learningComponent.initialize, 'function');
    assert.equal(typeof block.learningComponent.getAnswer, 'function');
    assert.equal(typeof block.learningComponent.validate, 'function');
    assert.equal(typeof block.learningComponent.reset, 'function');
    assert.equal(typeof block.learningComponent.emitProgress, 'function');
    assert.equal(block.querySelector('fieldset').querySelector('legend').textContent, 'Pilih satu jawaban');
    assert.equal(block.querySelector('.practice-feedback').getAttribute('aria-live'), 'polite');
    assert.equal(block.querySelector('.practice-hint').getAttribute('aria-live'), 'polite');

    const inputs = block.querySelectorAll('input');
    assert.ok(inputs.every((input) => input.parentNode.tagName === 'LABEL'));
    inputs[1].checked = true;
    await findButton(block, 'Cek jawaban').click();
    assert.equal(block.querySelector('.practice-feedback').dataset.state, 'incorrect');
    assert.equal(block.dataset.completed, 'false');

    inputs[0].checked = true;
    inputs[1].checked = false;
    await findButton(block, 'Cek jawaban').click();
    assert.equal(block.querySelector('.practice-feedback').dataset.state, 'correct');
    assert.equal(block.dataset.completed, 'true');

    const hint = block.querySelector('.practice-hint');
    await findButton(block, 'Petunjuk').click();
    assert.equal(hint.textContent, 'Baca metric.');
    await findButton(block, 'Petunjuk').click();
    assert.equal(hint.textContent, 'Cek comparison.');

    await findButton(block, 'Reset').click();
    assert.equal(block.dataset.completed, 'false');
    assert.equal(block.querySelector('.practice-feedback').textContent, '');
    assert.equal(inputs.filter((input) => input.checked).length, 0);
    assert.deepEqual(progressEvents.map((event) => event.completed), [false, true, false]);
});

test('practice shell supports reference answer and checklist self-assessment', async () => {
    const { block } = createPracticeBlock({
        id: 'demo-self',
        type: 'text_self_assessment',
        prompt: 'Tulis.',
        reference_answer: 'Jawaban rujukan.',
        checklist: ['Metric jelas', 'Batas evidence disebutkan'],
    });

    const textarea = block.querySelector('textarea');
    const review = block.querySelector('.practice-review');
    assert.equal(textarea.parentNode.tagName, 'LABEL');
    assert.equal(block.querySelector('.practice-feedback').getAttribute('aria-live'), 'polite');
    textarea.value = 'Finding dengan batas evidence.';

    await findButton(block, 'Lihat reference answer').click();
    assert.equal(review.hidden, false);
    assert.match(review.textContent, /Jawaban rujukan/);
    assert.equal(block.dataset.completed, 'false');

    block.querySelectorAll('input').forEach((input) => { input.checked = true; });
    await findButton(block, 'Lihat reference answer').click();
    assert.equal(block.dataset.completed, 'true');
    assert.equal(block.querySelector('.practice-feedback').dataset.state, 'correct');
});
