const factories = {
    practice: () => import('./practice.js'),
    'sql-playground': () => import('./sql-playground.js'),
    'spreadsheet-playground': () => import('./spreadsheet-playground.js'),
    'python-practice': () => import('./python-practice.js'),
    'visualization-playground': () => import('./visualization-playground.js'),
    'join-grain-playground': () => import('./join-grain-playground.js'),
    'sampling-uncertainty-playground': () => import('./sampling-uncertainty-playground.js'),
    'metric-tree-builder': () => import('./metric-tree-builder.js'),
    'communication-builder': () => import('./communication-builder.js'),
};

export const registeredComponentTypes = Object.freeze(Object.keys(factories));

export function isRegisteredComponent(type) {
    return Object.hasOwn(factories, type);
}

export async function mountRegisteredComponents(root = document) {
    const elements = root.querySelectorAll('[data-learning-component]');

    await Promise.all([...elements].map(async (element) => {
        const type = element.dataset.learningComponent;

        if (!isRegisteredComponent(type)) {
            element.dataset.enhanced = 'false';
            console.error(`Learning component is not registered: ${type}`);
            return;
        }

        try {
            const module = await factories[type]();
            module.mount(element);
        } catch (error) {
            element.dataset.enhanced = 'false';
            console.error(`Learning component failed to load: ${type}`, error);
        }
    }));
}
