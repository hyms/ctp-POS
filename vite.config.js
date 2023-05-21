import {defineConfig} from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
// import vue from '@vitejs/plugin-vue2'

export default defineConfig({
    plugins: [
        laravel({input: ["resources/js/app.js", 'resources/css/pdf_style.css', 'resources/css/pos_print.css']}),
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
            "@": "/resources/js",
        },
    },
});
