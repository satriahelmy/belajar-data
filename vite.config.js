import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/sql-spike.css',
                'resources/js/spike/sql/sql.js',
            ],
            refresh: ['resources/views/**/*.blade.php', 'routes/**/*.php'],
        }),
        tailwindcss(),
    ],
});
