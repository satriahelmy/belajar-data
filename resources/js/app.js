import './bootstrap';

import { mountRegisteredComponents } from './components/registry';
import { mountBookmarks } from './learner/bookmarks';
import { mountTopicProgress } from './learner/progress';

const setupMobileNavigation = () => {
    const toggle = document.querySelector('[data-nav-toggle]');
    const navigation = document.querySelector('[data-site-nav]');

    if (! toggle || ! navigation) {
        return;
    }

    toggle.addEventListener('click', () => {
        const isOpen = navigation.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(isOpen));
    });
};

const mount = async () => {
    await mountRegisteredComponents(document);
    await mountTopicProgress(document);
    mountBookmarks(document);
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        setupMobileNavigation();
        void mount();
    }, { once: true });
} else {
    setupMobileNavigation();
    void mount();
}
