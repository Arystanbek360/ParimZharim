import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: {
                app: ['resources/css/app.css', 'resources/js/app.js'], // Основные файлы
                nova: ['resources/css/nova-collapsible-sidebar.css', 'resources/js/nova-collapsible-sidebar.js'], // Файлы Nova
            },
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            input: {
                'app': 'resources/js/app.js',
                'app-nova': 'resources/js/nova-collapsible-sidebar.js',
                'app-nova-css': 'resources/css/nova-collapsible-sidebar.css',
            },
            output: {
                entryFileNames: (chunk) => {
                    if (chunk.name === 'app-nova') {
                        return 'js/app-nova.js';
                    }
                    if (chunk.name === 'app-nova-css') {
                        return 'css/app-nova.css';
                    }
                    return 'js/[name].js'; // Для остальных JS файлов
                },
                chunkFileNames: 'js/[name].js',
                assetFileNames: 'css/[name][extname]',
            },
        },
    },
});