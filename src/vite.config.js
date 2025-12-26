import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/dashboard.css',
                'resources/css/feedback-widget.css',
                'resources/js/app.js',
                'resources/js/adminlte.js',
                'resources/js/bootstrap.js',
                'resources/js/feedback-widget.js',
                'resources/css/adminlte.css', // Add separate AdminLTE CSS file
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
        cors: true,
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    'feedback-widget': ['resources/js/feedback-widget.js', 'resources/css/feedback-widget.css'],
                }
            }
        }
    }
});
