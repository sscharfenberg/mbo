<script setup lang="ts">
import { useId } from "vue";
import Icon from "Components/UI/Icon.vue";
import PopOver from "Components/UI/PopOver.vue";
import { useDeckCardActions } from "Composables/useDeckCardActions.ts";
const props = defineProps<{
    /** UUID of the deck this card belongs to. */
    deckId: string;
    /** UUID of the deck card entry. */
    cardId: string;
    /** Current number of copies (from server). */
    quantity: number;
    /** Whether this card is a basic land (exempt from copy limits). */
    isBasicLand: boolean;
    /** Maximum copies allowed by the format (e.g. 4 for constructed, 1 for singleton). */
    maxCopies: number;
    /** Whether the format is singleton (max 1 copy of non-basic cards). */
    isSingleton: boolean;
}>();
const popoverId = useId();
/** Close the action popover programmatically. */
function closePopover(): void {
    const el = document.getElementById(popoverId);
    if (el !== null) el.hidePopover();
}
const { canIncrement, increment, decrement, destroy } = useDeckCardActions(
    {
        deckId: props.deckId,
        cardId: props.cardId,
        quantity: () => props.quantity,
        isBasicLand: props.isBasicLand,
        maxCopies: props.maxCopies,
        isSingleton: props.isSingleton
    },
    closePopover
);
</script>

<template>
    <pop-over
        icon="more"
        aria-label="Actions"
        class-string="popover-button--rounded popover-button--tiny"
        :reference="popoverId"
        width="14rem"
    >
        <ul class="popover-list">
            <li class="popover-list-multi">
                <button
                    type="button"
                    class="popover-list-item"
                    @click="decrement"
                    :aria-label="$t('pages.deck.card_quantity.increment')"
                >
                    <icon name="subtract" />
                </button>
                <button
                    type="button"
                    class="popover-list-item"
                    :disabled="!canIncrement"
                    @click="increment"
                    :aria-label="$t('pages.deck.card_quantity.decrement')"
                >
                    <icon name="add" />
                </button>
            </li>
            <li>
                <button type="button" class="popover-list-item popover-list-item--caution" @click="destroy">
                    <icon name="delete" :size="1" />
                    (Remove all)
                </button>
            </li>
        </ul>
    </pop-over>
</template>

<style lang="scss" scoped>
.popover-list-multi {
    display: flex;

    gap: 0.5rem;

    > button {
        display: flex;
        align-items: center;
        justify-content: center;

        &:disabled {
            opacity: 0.35;

            cursor: not-allowed;
        }
    }
}
</style>
