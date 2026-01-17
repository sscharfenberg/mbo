/******************************************************************************
 * Main app entrypoint
 *****************************************************************************/
import "@/styles/app.scss";
import { createInertiaApp, router } from "@inertiajs/vue3";
import {
    doesProgressBarExist,
    finishProgress,
    setProgress,
    startProgress
} from "@sscharfenberg/progressbar/progressbar.js";
import type { DefineComponent } from "vue";
import { createApp, h } from "vue";
import { createI18n } from "vue-i18n";
import AppLayout from "./components/Layout/AppLayout.vue";
const progressBarSettings = { ariaLabel: "Ladefortschritt", parent: "#app" };
let timeout: ReturnType<typeof setTimeout>;

const i18n = createI18n({
    legacy: false
});

/**
 * mount Inertia App
 */
createInertiaApp({
    resolve: async name => {
        const pages = import.meta.glob<{ default: DefineComponent }>("./pages/**/*.vue");
        const pageLoader = pages[`./pages/${name}.vue`];
        if (!pageLoader) {
            throw new Error(`Page not found: ${name}`);
        }

        const page = await pageLoader();
        page.default.layout = page.default.layout || AppLayout;
        return page.default;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)
            .mount(el);
    },
    title: title => (title ? `MBO: ${title}` : `MBO`),
    progress: false // disable inertia NProgress implementation for more control
}).then(() => {
    console.log("app created");
});

/**
 * @function on router start
 */
router.on("start", () => {
    console.log("router start");
    timeout = setTimeout(() => startProgress(progressBarSettings), 250);
});

/**
 * @function on router progress
 */
router.on("progress", event => {
    console.log(event.detail);
    if (doesProgressBarExist() && event.detail.progress?.percentage) {
        setProgress((event.detail.progress.percentage / 100) * 0.9);
    }
});

/**
 * @function on router finish
 */
router.on("finish", event => {
    clearTimeout(timeout);
    if (doesProgressBarExist() && event.detail.visit.completed) {
        finishProgress();
    } else if (event.detail.visit.interrupted) {
        setProgress(0);
    } else if (event.detail.visit.cancelled) {
        finishProgress();
    }
});
