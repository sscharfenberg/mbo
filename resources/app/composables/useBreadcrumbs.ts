import type { Ref } from "vue";
import { ref } from "vue";

/** A single breadcrumb item. Provide either `label` (raw string) or `labelKey` (i18n key). */
export type BreadcrumbItem = {
    /** Raw string label — used as-is, takes precedence over labelKey. */
    label?: string;
    /** i18n key — resolved by the Breadcrumb component via $t(). Used when label is absent. */
    labelKey?: string;
    /** Optional named parameters passed to $t(). Only relevant when using labelKey. */
    params?: Record<string, string>;
    /** Optional Inertia href. Omit for the current (last) item. */
    href?: string;
    /** Optional icon name, rendered via <Icon>. */
    icon?: string;
};

/** Return type of the {@link useBreadcrumbs} composable. */
export type UseBreadcrumbsReturn = {
    crumbs: Ref<BreadcrumbItem[]>;
    setBreadcrumbs: (items: BreadcrumbItem[]) => void;
};

// Module-level state — all consumers share the same singleton instance.
const crumbs = ref<BreadcrumbItem[]>([]);

/**
 * Composable for managing breadcrumb navigation.
 *
 * State lives at module level so calling `useBreadcrumbs()` from any page
 * component or the Breadcrumb UI component always shares the same list.
 * The Inertia router resets crumbs on each navigation start (wired in main.ts).
 *
 * Pages call `set([...])` in `<script setup>` with plain i18n key strings —
 * the Breadcrumb component resolves them via `$t()`.
 *
 * @example
 * ```ts
 * const props = defineProps<{ container: Container }>()
 * const { setBreadcrumbs } = useBreadcrumbs()
 * setBreadcrumbs([
 *     { labelKey: 'pages.collection.link', href: '/collection', icon: 'deck' },
 *     { labelKey: 'pages.containers.link', href: '/containers' },
 *     // i18n key with interpolated param:
 *     { labelKey: 'pages.container.link', params: { name: props.container.name } },
 *     // or raw string directly:
 *     { label: props.container.name },
 * ])
 * ```
 */
export function useBreadcrumbs(): UseBreadcrumbsReturn {
    function setBreadcrumbs(items: BreadcrumbItem[]) {
        crumbs.value = items;
    }

    return { crumbs, setBreadcrumbs };
}
