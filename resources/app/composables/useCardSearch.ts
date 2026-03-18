import { ref, watch } from "vue";

/** Reactive state and helpers for a debounced card search against a JSON API endpoint. */
export function useCardSearch<T>(endpoint: string) {
    /** The current text in the search input, bound via v-model. */
    const searchQuery = ref("");
    /** Card results returned by the search endpoint. */
    const results = ref<T[]>([]);
    /** True while a search XHR is in flight. */
    const processing = ref(false);
    /** The currently selected card result. */
    const selectedCard = ref<T | null>(null);
    /** The hidden form value (card id). */
    const refValue = ref("");

    /** Timer handle for debouncing search input. */
    let debounceTimer: ReturnType<typeof setTimeout> | null = null;
    /** AbortController for the current in-flight request, so a new search cancels stale ones. */
    let abortController: AbortController | null = null;

    /**
     * Fetch matching cards from the API and populate results.
     * Clears results if the query is empty.
     */
    async function searchCards(query: string) {
        if (!query.trim()) {
            results.value = [];
            return;
        }
        if (abortController) abortController.abort();
        abortController = new AbortController();
        processing.value = true;
        try {
            const response = await fetch(`${endpoint}?q=${encodeURIComponent(query)}`, {
                signal: abortController.signal
            });
            if (response.ok) {
                const data = await response.json();
                if (data) results.value = data;
            }
        } catch (e) {
            if (e instanceof DOMException && e.name === "AbortError") return;
            throw e;
        }
        processing.value = false;
    }

    /** Called when the user clicks a result. */
    function onCardSelected(card: T) {
        selectedCard.value = card;
        refValue.value = (card as Record<string, unknown>).id as string;
        results.value = [];
    }

    /** Called when the user clicks "Change selection". */
    function onClearSelection() {
        selectedCard.value = null;
        refValue.value = "";
        searchQuery.value = "";
    }

    /** Debounce search input changes by 500 ms before calling the API. */
    watch(searchQuery, query => {
        if (debounceTimer) clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => searchCards(query), 500);
    });

    return {
        searchQuery,
        results,
        processing,
        selectedCard,
        refValue,
        onCardSelected,
        onClearSelection
    };
}

export type UseCardSearchReturn<T> = ReturnType<typeof useCardSearch<T>>;