import { usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import type { Ref } from "vue";
import { useI18n } from "vue-i18n";
import { useToast } from "Composables/useToast";
import type { Container } from "Types/container";

export type UseContainerSortReturn = {
    localContainers: Ref<Container[]>;
    isSaving: Ref<boolean>;
    handleReorder: (reordered: Container[]) => void;
};

/**
 * Manages local container order and persists changes to the server.
 *
 * Owns `localContainers` — the mutable source of truth for order that the
 * filter composable and result list both read from. Reorders are applied
 * optimistically on the client before the server confirms them.
 *
 * The server PATCH is debounced (500 ms) so rapid consecutive drags collapse
 * into a single request. An AbortController cancels any superseded in-flight
 * request; `isSaving` stays `true` for the full duration from first drag to
 * final server confirmation.
 *
 * @param initialContainers - The server-rendered container array from Inertia props.
 * @returns Reactive sort state and the reorder handler.
 */
export function useContainerSort(initialContainers: Container[]): UseContainerSortReturn {
    const page = usePage();
    const { t } = useI18n();
    const { addToast } = useToast();

    /** Local mutable copy of containers — updated on drag reorder without a full page reload. */
    const localContainers = ref<Container[]>([...initialContainers]);

    /** True while a sort request is debouncing or in flight. */
    const isSaving = ref(false);

    /** Timer handle for debouncing sort PATCHes — reset on every new reorder. */
    let debounceTimer: ReturnType<typeof setTimeout> | null = null;

    /** AbortController for the most recent in-flight sort PATCH. */
    let abortController: AbortController | null = null;

    /**
     * Handles a drag-drop reorder emitted by ContainersResultList.
     *
     * `reordered` is only the currently visible (filtered) subset in its new order.
     * We first apply an optimistic update: the filtered items are slotted back into
     * the positions they occupied in `localContainers`, so hidden items keep their
     * relative positions and the UI reflects the change instantly.
     *
     * @param reordered - The visible (filtered) containers in their new order, as emitted by ContainersResultList.
     */
    function handleReorder(reordered: Container[]) {
        const reorderedIds = new Set(reordered.map(c => c.id));
        const positions = localContainers.value.map((c, i) => (reorderedIds.has(c.id) ? i : -1)).filter(i => i !== -1);
        const newList = [...localContainers.value];
        positions.forEach((pos, i) => {
            newList[pos] = reordered[i]!;
        });
        localContainers.value = newList;

        isSaving.value = true;
        if (debounceTimer !== null) clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            abortController?.abort();
            abortController = new AbortController();

            fetch("/collection/containers/sort", {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": page.props.csrfToken as string
                },
                body: JSON.stringify({ order: newList.map(c => c.id) }),
                signal: abortController.signal
            })
                .then(res => res.json())
                .then((data: { ok?: boolean }) => {
                    isSaving.value = false;
                    if (!data.ok) addToast(t("pages.containers.sort_error"), "error");
                })
                .catch((err: Error) => {
                    // AbortError means a newer request superseded this one — keep isSaving true.
                    if (err.name !== "AbortError") {
                        isSaving.value = false;
                        addToast(t("pages.containers.sort_error"), "error");
                    }
                });
        }, 500);
    }

    return { localContainers, isSaving, handleReorder };
}
