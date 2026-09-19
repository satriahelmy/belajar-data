import assert from 'node:assert/strict';
import test from 'node:test';

import {
    createAuthenticatedProgressStore,
    createGuestProgressStore,
} from '../../resources/js/learner/progress-store.js';

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

test('authenticated progress store sends the guest snapshot to the merge endpoint', async () => {
    const calls = [];
    const client = {
        async post(path, payload) {
            calls.push({ path, payload });

            return { data: { topics: { 'data-analyst/01-thinking-with-data/01-analyst-role': { status: 'completed' } } } };
        },
    };
    const store = createAuthenticatedProgressStore(client);
    const snapshot = {
        topics: [{ content_key: 'data-analyst/01-thinking-with-data/01-analyst-role', status: 'completed' }],
        recent: ['data-analyst/01-thinking-with-data/01-analyst-role'],
    };

    const topics = await store.mergeGuest(snapshot);

    assert.deepEqual(calls, [{ path: '/progress/merge-guest', payload: snapshot }]);
    assert.equal(topics['data-analyst/01-thinking-with-data/01-analyst-role'].status, 'completed');
});
