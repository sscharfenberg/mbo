import { usePage } from "@inertiajs/vue3";
import type { App } from "vue";

/**
 * @function get the translation with the key using the dot notation.
 * @param key
 */
export const t = (key: string): string => {
    const page = usePage();
    const translations = page.props.translations as Record<string, unknown>;
    const result = key
        .split(".")
        .reduce<unknown>(
            (a, b) =>
                a !== null && a !== undefined && typeof a === "object" && b in a
                    ? (a as Record<string, unknown>)[b]
                    : a,
            translations
        );
    return typeof result === "string" ? result : "";
};

/**
 * default export for installing as VueJS Plugin
 */
export default {
    install: (app: App) => {
        app.config.globalProperties.$t = t;
    }
};
