import type { Ref } from "vue";
import { ref } from "vue";

/** Return type of the {@link useNavigation} composable. */
export type UseNavigationReturn = {
    navigating: Ref<boolean>;
};

// Module-level state — all consumers share the same singleton instance.
const navigating = ref(false);

/**
 * Composable for tracking Inertia page navigation state.
 *
 * `navigating` is set to `true` on the Inertia `start` event and back to
 * `false` on `finish` (wired in main.ts). This is used to render a full-page
 * overlay that prevents the user from seeing a partially updated UI — e.g.
 * the breadcrumb clears immediately on navigation start, but the new page
 * content only arrives once the request completes.
 *
 * The overlay itself delays becoming visible by 250ms via CSS animation-delay,
 * so fast navigations never show it at all.
 */
export function useNavigation(): UseNavigationReturn {
    return { navigating };
}