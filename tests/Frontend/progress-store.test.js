import assert from 'node:assert/strict';
import test from 'node:test';

import { createGuestProgressStore } from '../../resources/js/learner/progress-store.js';

function createStorage() {
    const values = new Map();

    return {
        getItem(key) {
            return values.get(key) ?? null;
        },
        setItem(key, value) {
            values.set(key, value);
        },
        removeItem(key) {
            values.delete(key);
        },
    };
}

test('guest progress keeps completion and recent topic order', () => {
    const store = createGuestProgressStore(createStorage());

    store.markStarted('data-analyst/01-thinking-with-data/01-analyst-role');
    store.markCompleted('data-analyst/01-thinking-with-data/01-analyst-role');
    store.markStarted('data-analyst/01-thinking-with-data/02-business-to-data-question');
    store.markStarted('data-analyst/01-thinking-with-data/01-analyst-role');

    assert.equal(
        store.getTopicProgress('data-analyst/01-thinking-with-data/01-analyst-role').status,
        'completed',
    );
    assert.deepEqual(store.getRecent(), [
        'data-analyst/01-thinking-with-data/01-analyst-role',
        'data-analyst/01-thinking-with-data/02-business-to-data-question',
    ]);
});

test('guest progress can be cleared after a successful merge', () => {
    const storage = createStorage();
    const store = createGuestProgressStore(storage);
    store.markStarted('data-analyst/01-thinking-with-data/01-analyst-role');

    assert.equal(store.snapshot().topics.length, 1);
    store.clear();
    assert.equal(store.snapshot().topics.length, 0);
});
