import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/firebase.js',
                'resources/js/google-login.js',
                'resources/js/applications.js',
                'resources/js/psychotest.js', // 👈 Agregado
            ],
            refresh: true,
        }),
    ],
});

