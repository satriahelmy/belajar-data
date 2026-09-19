function updateBookmarkButton(button, bookmarked) {
    if (!button) {
        return;
    }

    button.setAttribute('aria-pressed', String(bookmarked));
    button.textContent = bookmarked ? 'Tersimpan' : 'Simpan lesson';
}

function showEmptyBookmarkState(list) {
    if (!list || list.querySelector('[data-bookmark-item]')) {
        return;
    }

    const empty = document.createElement('div');
    empty.className = 'empty-state';
    empty.innerHTML = '<strong>Belum ada lesson yang disimpan.</strong><p>Gunakan tombol Simpan lesson pada halaman lesson untuk membuat daftar bacaanmu.</p>';
    list.replaceWith(empty);
}

export function mountBookmarks(root = document) {
    const forms = [...root.querySelectorAll('[data-bookmark-form]')];

    if (forms.length === 0 || document.body?.dataset.authenticated !== 'true') {
        return;
    }

    forms.forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const button = form.querySelector('[data-bookmark-action]');
            const contentType = form.elements.content_type?.value;
            const contentKey = form.elements.content_key?.value;

            if (!button || !contentType || !contentKey) {
                return;
            }

            button.disabled = true;

            try {
                const response = await window.axios.post(form.action, {
                    content_type: contentType,
                    content_key: contentKey,
                });
                const bookmarked = response.data.bookmarked === true;
                const item = form.closest('[data-bookmark-item]');

                if (!bookmarked && item) {
                    const list = item.closest('[data-bookmark-list]');
                    item.remove();
                    showEmptyBookmarkState(list);
                } else {
                    updateBookmarkButton(button, bookmarked);
                }
            } catch {
                button.textContent = 'Coba lagi';
            } finally {
                if (document.body.contains(button)) {
                    button.disabled = false;
                }
            }
        });
    });
}
