import { router, usePage } from "@inertiajs/vue3";
import type { ComputedRef } from "vue";
import { computed, ref, watch } from "vue";
import type { DeckCardRow } from "Types/deckPage";

/** Parameters for {@link useDeckCardActions}. */
export interface DeckCardActionParams {
    /** UUID of the deck. */
    deckId: string;
    /** UUID of the deck card entry. */
    cardId: string;
    /** Getter for the server-authoritative quantity (reactive prop). */
    quantity: () => number;
    /** Whether this card is a basic land (exempt from copy limits). */
    isBasicLand: boolean;
    /** Maximum copies allowed by the format (e.g. 4, or 1 for singleton). */
    maxCopies: number;
    /** Whether the format is singleton. */
    isSingleton: boolean;
}

/** Return type of {@link useDeckCardActions}. */
export type UseDeckCardActionsReturn = {
    /** Whether one more copy can be added (accounts for pending clicks). */
    canIncrement: ComputedRef<boolean>;
    /** Add one copy. Debounced — rapid clicks are batched into a single request. */
    increment: () => void;
    /** Remove one copy. Deletes the card when the effective quantity reaches zero. */
    decrement: () => void;
    /** Remove the card entirely, regardless of quantity. */
    destroy: () => Promise<void>;
};

/** Milliseconds to wait after the last click before flushing the delta. */
const DEBOUNCE_MS = 500;

/**
 * Manages deck card quantity mutations with debounced batching.
 *
 * Tracks an optimistic local quantity so that `canIncrement` stays
 * accurate across rapid clicks (e.g. disables the button at the copy
 * limit). Clicks within {@link DEBOUNCE_MS} are collapsed into a single
 * PATCH request carrying the net delta.
 *
 * After a successful response the composable updates the Inertia page
 * props in place — mutating the same reactive `cards` array the
 * components reference — so the DOM patches without a full reload.
 * Only destructive actions (delete) trigger a lightweight
 * `router.reload({ only: ["deck"] })` to refresh deck metadata
 * (card count, colors).
 *
 * @param params — card identity, format rules, and a reactive quantity getter.
 * @param closePopover — callback to dismiss the host popover after destructive actions.
 */
export function useDeckCardActions(
    params: DeckCardActionParams,
    closePopover: () => void,
): UseDeckCardActionsReturn {
    const page = usePage();

    /**
     * Local quantity reflecting pending clicks. Drives the `canIncrement`
     * check so rapid clicking correctly disables the button at the limit.
     * Reset whenever Inertia delivers fresh server data.
     */
    const effectiveQty = ref(params.quantity());
    watch(params.quantity, (q) => {
        effectiveQty.value = q;
    });

    /** Whether one more copy can be added. */
    const canIncrement = computed((): boolean => {
        if (params.isBasicLand) return true;
        if (params.isSingleton) return false;
        return effectiveQty.value < params.maxCopies;
    });

    /** Debounce handle — cleared and reset on every click. */
    let flushTimer: ReturnType<typeof setTimeout> | null = null;

    /** Schedule a flush after the debounce window. */
    function scheduleFlush(): void {
        if (flushTimer !== null) clearTimeout(flushTimer);
        flushTimer = setTimeout(() => void flush(), DEBOUNCE_MS);
    }

    /**
     * Remove a card from the Inertia page props array by id.
     * Triggers Vue reactivity so the card disappears from the DOM
     * without a full page reload.
     */
    function spliceCard(): void {
        const cards = page.props.cards as DeckCardRow[];
        const idx = cards.findIndex((c) => c.id === params.cardId);
        if (idx !== -1) cards.splice(idx, 1);
    }

    /**
     * Send the accumulated delta to the server, then patch page props
     * in place. Only reloads deck metadata when the card is deleted
     * (card count / colors may have changed).
     */
    async function flush(): Promise<void> {
        flushTimer = null;
        const delta = effectiveQty.value - params.quantity();
        if (delta === 0) return;

        const response = await fetch(`/api/decks/${params.deckId}/cards/${params.cardId}/quantity`, {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": page.props.csrfToken as string,
                Accept: "application/json",
            },
            body: JSON.stringify({ delta }),
        });

        if (!response.ok) {
            effectiveQty.value = params.quantity();
            return;
        }

        const data = (await response.json()) as { quantity?: number; deleted?: boolean };

        if (data.deleted) {
            spliceCard();
            router.reload({ only: ["deck"] });
        } else if (data.quantity !== undefined) {
            const cards = page.props.cards as DeckCardRow[];
            const card = cards.find((c) => c.id === params.cardId);
            if (card) card.quantity = data.quantity;
        }
    }

    /** Add one copy (debounced). */
    function increment(): void {
        if (!canIncrement.value) return;
        effectiveQty.value++;
        scheduleFlush();
    }

    /**
     * Remove one copy (debounced). When the effective quantity reaches
     * zero the flush fires immediately and the popover is closed.
     */
    function decrement(): void {
        effectiveQty.value--;
        if (effectiveQty.value <= 0) {
            if (flushTimer !== null) clearTimeout(flushTimer);
            void flush();
            closePopover();
            return;
        }
        scheduleFlush();
    }

    /** Remove the card entirely, cancelling any pending quantity flush. */
    async function destroy(): Promise<void> {
        if (flushTimer !== null) clearTimeout(flushTimer);
        closePopover();

        const response = await fetch(`/api/decks/${params.deckId}/cards/${params.cardId}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": page.props.csrfToken as string,
                Accept: "application/json",
            },
        });

        if (response.ok) {
            spliceCard();
            router.reload({ only: ["deck"] });
        }
    }

    return { canIncrement, increment, decrement, destroy };
}