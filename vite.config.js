import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
            'resources/js/admin/dashboard.js',
            'resources/js/admin/layanandigital.js',
            'resources/js/admin/slamonitoring.js',
            'resources/js/admin/ticketing.js',
            'resources/js/pelapor/dashboard.js',
        ]),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
