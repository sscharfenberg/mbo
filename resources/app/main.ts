/******************************************************************************
 * Main app entrypoint
 *****************************************************************************/
import "@/styles/app.scss";
import { createInertiaApp, router } from "@inertiajs/vue3";
import { doesProgressBarExist, finishProgress, setProgress, startProgress } from "@sscharfenberg/progressbar/progressbar.js";
import type { DefineComponent } from "vue";
import { createApp, h } from "vue";
const progressBarSettings = { ariaLabel: "Ladefortschritt" };
let timeout: ReturnType<typeof setTimeout>;

/**
 * mount Inertia App
 */
createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob<DefineComponent>("./pages/**/*.vue");
        const page = pages[`./pages/${name}.vue`];

        if (!page) {
            throw new Error(`Page not found: ${name}`);
        }

        return page(); // This returns the Promise that resolves to the component
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: false // disable inertia NProgress implementation for more control
}).then(() => {
    console.log("app created");
});

/**
 * @function on router start
 */
router.on("start", () => {
    timeout = setTimeout(() => startProgress(progressBarSettings), 250);
});

/**
 * @function on router progress
 */
router.on("progress", (event) => {
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
