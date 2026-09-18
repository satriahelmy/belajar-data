export function mount(element) {
    const status = element.querySelector('[data-role="enhancement-status"]');

    if (!status) {
        element.dataset.enhanced = 'false';
        return;
    }

    try {
        const config = JSON.parse(element.dataset.config ?? '{}');
        status.textContent = `${config.type ?? 'Practice'} component registered for progressive enhancement.`;
        element.dataset.enhanced = 'true';
    } catch {
        status.textContent = 'Practice component configuration could not be enhanced.';
        element.dataset.enhanced = 'false';
    }
}
