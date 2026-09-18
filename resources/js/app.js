import './bootstrap';

function enhanceRegisteredPracticeBlocks() {
    document.querySelectorAll('[data-learning-component="practice"]').forEach((element) => {
        const status = element.querySelector('[data-role="enhancement-status"]');

        if (!status) {
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
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', enhanceRegisteredPracticeBlocks);
} else {
    enhanceRegisteredPracticeBlocks();
}
