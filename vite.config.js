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
        host: true, // доступ с Windows при запуске Vite в WSL
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
        // Не задаём hmr.port — WebSocket будет на том же порту, что и dev-сервер (5173 или 5174, если 5173 занят)
        hmr: {
            host: 'localhost',
            protocol: 'ws',
        },
    },
});
