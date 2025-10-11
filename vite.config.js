/**
 * Vite Configuration for Tailwind CSS v4
 * 
 * This configuration integrates Tailwind CSS v4 with Laravel using:
 * - @tailwindcss/vite plugin for native Tailwind v4 support
 * - laravel-vite-plugin for Laravel asset bundling
 * - Hot module replacement for development
 * - Multiple theme CSS files for easy theme switching
 * 
 * Build: npm run build
 * Dev: npm run dev
 */
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/themes/default.css",
                "resources/css/themes/nord.css",
                "resources/css/themes/nord-dark.css",
                "resources/css/themes/mandarin.css",
                "resources/js/app.js"
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
