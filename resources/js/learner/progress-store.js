const STORAGE_KEY = 'belajardata.progress.v1';
const MAX_RECENT = 10;

function emptyState() {
    return { topics: {}, recent: [] };
}

function readState(storage) {
    try {
        const parsed = JSON.parse(storage.getItem(STORAGE_KEY) ?? '{}');
        return {
            topics: parsed.topics && typeof parsed.topics === 'object' ? parsed.topics : {},
            recent: Array.isArray(parsed.recent) ? parsed.recent : [],
        };
    } catch {
        return emptyState();
    }
}

function writeState(storage, state) {
    storage.setItem(STORAGE_KEY, JSON.stringify(state));
}

function touchRecent(state, contentKey) {
    state.recent = [contentKey, ...state.recent.filter((key) => key !== contentKey)].slice(0, MAX_RECENT);
}

function updateTopic(storage, contentKey, status) {
    const state = readState(storage);
    const now = new Date().toISOString();
    const current = state.topics[contentKey] ?? {};
    const completed = status === 'completed' || current.status === 'completed';

    state.topics[contentKey] = {
        content_key: contentKey,
        status: completed ? 'completed' : 'started',
        started_at: current.started_at ?? now,
        completed_at: completed ? (current.completed_at ?? now) : (current.completed_at ?? null),
        last_activity_at: now,
    };
    touchRecent(state, contentKey);
    writeState(storage, state);

    return state.topics[contentKey];
}

export function createGuestProgressStore(storage = globalThis.localStorage) {
    return {
        getTopicProgress(contentKey) {
            return readState(storage).topics[contentKey] ?? null;
        },
        getRecent() {
            return readState(storage).recent;
        },
        markStarted(contentKey) {
            return updateTopic(storage, contentKey, 'started');
        },
        markCompleted(contentKey) {
            return updateTopic(storage, contentKey, 'completed');
        },
        snapshot() {
            const state = readState(storage);

            return {
                topics: Object.values(state.topics),
                recent: state.recent,
            };
        },
        clear() {
            storage.removeItem(STORAGE_KEY);
        },
    };
}

export function createAuthenticatedProgressStore(client = globalThis.axios) {
    return {
        async getAll() {
            const response = await client.get('/progress/topics');
            return response.data.topics ?? {};
        },
        async markStarted(contentKey) {
            const response = await client.post('/progress/topics/start', { content_key: contentKey });
            return response.data.topic;
        },
        async markCompleted(contentKey) {
            const response = await client.post('/progress/topics/complete', { content_key: contentKey });
            return response.data.topic;
        },
        async mergeGuest(snapshot) {
            const response = await client.post('/progress/merge-guest', snapshot);
            return response.data.topics ?? {};
        },
    };
}
