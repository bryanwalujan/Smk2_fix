import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        hmr: {
            overlay: false // Menonaktifkan overlay error
        },
        watch: {
            ignored: ['**/public/qrcodes/*'] // Mengabaikan perubahan di folder qrcodes
        }
    }
});