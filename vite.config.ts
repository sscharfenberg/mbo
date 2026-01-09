import { default as vitePluginVue } from "@vitejs/plugin-vue";
import laravelPlugin from "laravel-vite-plugin";
import path from "node:path";
import { fileURLToPath } from "node:url";
import { defineConfig } from "vite";
import vitePluginVueDevtools from "vite-plugin-vue-devtools";
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/*
 * https://vite.dev/config/
 */
export default defineConfig({
    /*
     * https://vite.dev/config/shared-options.html#root
     */
    // root: "resources/app",

    /*
     * https://vite.dev/config/shared-options.html#publicdir
     */
    // publicDir: path.resolve(__dirname, "resources/app/static/"),

    /*
     * https://vite.dev/config/shared-options.html#plugins
     */
    plugins: [
        // https://github.com/laravel/vite-plugin
        // https://laravel.com/docs/12.x/vite
        laravelPlugin({
            input: ['resources/app/main.ts'],
            refresh: true,
        }),

        // https://github.com/vitejs/vite/tree/main/packages/plugin-vue
        vitePluginVue({
            template: {
                transformAssetUrls: {
                    // The Vue plugin will re-write asset URLs, when referenced
                    // in Single File Components, to point to the Laravel web
                    // server. Setting this to `null` allows the Laravel plugin
                    // to instead re-write asset URLs to point to the Vite
                    // server instead.
                    base: null,

                    // The Vue plugin will parse absolute URLs and treat them
                    // as absolute paths to files on disk. Setting this to
                    // `false` will leave absolute URLs untouched so they can
                    // reference assets in the public directly as expected.
                    includeAbsolute: false
                },
                compilerOptions: {
                    isCustomElement: tag => tag.startsWith("media-")
                }
            }
        }),

        // https://devtools.vuejs.org/
        // https://www.npmjs.com/package/vite-plugin-vue-devtools
        vitePluginVueDevtools(),

    ],

    /*
     * https://vite.dev/config/shared-options.html#resolve-alias
     */
    resolve: {
        alias: {
            "~": path.resolve(__dirname, "node_modules"),
            "@": path.resolve(__dirname, "resources/app"),
            Assets: path.resolve(__dirname, "resources/app/assets"),
            Components: path.resolve(__dirname, "resources/app/components"),
            Abstracts: path.resolve(__dirname, "resources/app/styles/abstracts")
        }
    },

    optimizeDeps: {
        exclude: ["js-big-decimal"]
    },

    /*
     * https://vite.dev/config/build-options.html
     */
    build: {
        // outDir: path.resolve(__dirname, "public"),
        emptyOutDir: true
    },

    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
