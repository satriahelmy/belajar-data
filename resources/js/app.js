import './bootstrap';

import { mountRegisteredComponents } from './components/registry';

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

const mount = () => mountRegisteredComponents(document);

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        setupMobileNavigation();
        mount();
    }, { once: true });
} else {
    setupMobileNavigation();
    mount();
}
