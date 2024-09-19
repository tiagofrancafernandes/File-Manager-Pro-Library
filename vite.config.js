import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/js/doc-reader/helpers.js',
                'resources/js/doc-reader/doc-reader.js',
                'resources/js/doc-reader/doc-reader-worker.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
            '@resources': fileURLToPath(new URL('./resources', import.meta.url)),
            '@public': fileURLToPath(new URL('./public', import.meta.url)),
            '@asset': fileURLToPath(new URL('./public', import.meta.url)),
            '@base': fileURLToPath(new URL('./', import.meta.url)),
        }
    }
});
