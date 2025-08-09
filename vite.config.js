import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

const host = process.env.VITE_DEV_SERVER_HOST || '0.0.0.0';
const hmrHost = process.env.VITE_DEV_SERVER_HMR_HOST || process.env.VITE_DEV_SERVER_HOST || 'localhost';
const port = Number(process.env.VITE_DEV_SERVER_PORT || 5173);
const https = String(process.env.VITE_DEV_SERVER_HTTPS || 'false') === 'true';

export default defineConfig({
    server: {
        host, // bind em todas as interfaces para acesso via LAN
        port,
        https,
        hmr: {
            host: hmrHost, // defina para o IP da sua máquina na LAN (ex.: 192.168.0.110)
            port,
            protocol: https ? 'wss' : 'ws',
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            buildDirectory: 'build',
        }),
    ],
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
        }
    },
});
