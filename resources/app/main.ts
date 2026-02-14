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
import { setupI18n, loadLocaleMessages } from "@/i18n.ts";
import FullLayout from "./components/Layout/FullLayout.vue";
const progressBarSettings = { ariaLabel: "Ladefortschritt", parent: "#app" };
let timeout: ReturnType<typeof setTimeout>;

/******************************************************************************
 * mount Inertia App
 *****************************************************************************/
createInertiaApp({
    resolve: async name => {
        const pages = import.meta.glob<{ default: DefineComponent }>("./pages/**/*.vue");
        const pageLoader = pages[`./pages/${name}.vue`];
        if (!pageLoader) {
            throw new Error(`Page not found: ${name}`);
        }

        const page = await pageLoader();
        page.default.layout = page.default.layout || FullLayout;
        return page.default;
    },
    setup({ el, App, props, plugin }) {
        const { locale, supportedLocales } = props.initialPage.props as {
            locale?: string;
            supportedLocales?: string[];
        };
        const initialLocale = locale || "de";
        const availableLocales = supportedLocales || ["en"];

        const i18n = setupI18n({
            legacy: false,
            locale: initialLocale,
            fallbackLocale: availableLocales.filter(locale => locale !== initialLocale)[0],
            messages: {}
        });

        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);
        app.use(i18n);

        loadLocaleMessages(i18n, initialLocale).then(() => app.mount(el));
    },
    title: title => (title ? `MBO: ${title}` : `MBO`),
    progress: false // disable inertia NProgress implementation for more control
}).then(() => {
    console.log("app created");
});

/******************************************************************************
 * Inertia router
 *****************************************************************************/
router.on("start", () => {
    timeout = setTimeout(() => startProgress(progressBarSettings), 250);
});
router.on("progress", event => {
    if (doesProgressBarExist() && event.detail.progress?.percentage) {
        setProgress((event.detail.progress.percentage / 100) * 0.9);
    }
});
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
