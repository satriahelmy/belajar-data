import './bootstrap';

import { mountRegisteredComponents } from './components/registry';

const mount = () => mountRegisteredComponents(document);

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mount, { once: true });
} else {
    mount();
}
