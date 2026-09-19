import { createAuthenticatedProgressStore, createGuestProgressStore } from './progress-store';

function renderProgress(element, status) {
    const statusElement = element.querySelector('[data-progress-status]');
    const action = element.querySelector('[data-progress-action]');
    const completed = status === 'completed';

    element.dataset.progressState = status;
    if (statusElement) {
        statusElement.textContent = completed ? 'Topic selesai' : status === 'started' ? 'Sedang dipelajari' : 'Belum dimulai';
    }
    if (action) {
        action.hidden = completed;
        action.textContent = completed ? 'Topic selesai' : 'Tandai selesai';
    }
}

export async function mountTopicProgress(root = document) {
    const elements = [...root.querySelectorAll('[data-topic-progress]')];
    const guestStore = createGuestProgressStore();
    const authenticated = document.body?.dataset.authenticated === 'true';
    const accountStore = authenticated ? createAuthenticatedProgressStore() : null;
    let accountTopics = {};

    if (accountStore) {
        try {
            const guestSnapshot = guestStore.snapshot();
            accountTopics = guestSnapshot.topics.length > 0
                ? await accountStore.mergeGuest(guestSnapshot)
                : await accountStore.getAll();

            if (guestSnapshot.topics.length > 0) {
                guestStore.clear();
            }
        } catch {
            accountTopics = {};
        }
    }

    if (elements.length === 0) {
        return;
    }

    for (const element of elements) {
        const contentKey = element.dataset.progressKey;
        const initialStatus = accountStore
            ? accountTopics[contentKey]?.status ?? element.dataset.progressState ?? 'not_started'
            : guestStore.getTopicProgress(contentKey)?.status ?? element.dataset.progressState ?? 'not_started';
        const store = accountStore ?? guestStore;

        renderProgress(element, initialStatus);

        if (initialStatus !== 'completed') {
            try {
                await store.markStarted(contentKey);
                renderProgress(element, 'started');
            } catch {
                renderProgress(element, initialStatus);
            }
        }

        element.querySelector('[data-progress-action]')?.addEventListener('click', async () => {
            const action = element.querySelector('[data-progress-action]');
            if (action) {
                action.disabled = true;
            }

            try {
                await store.markCompleted(contentKey);
                renderProgress(element, 'completed');
            } finally {
                if (action && element.dataset.progressState !== 'completed') {
                    action.disabled = false;
                }
            }
        }, { once: true });
    }
}
