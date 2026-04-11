<script setup lang="ts">
import { useId } from "vue";
import Icon from "Components/UI/Icon.vue";
import PopOver from "Components/UI/PopOver.vue";
import type { DeckMeta } from "Types/deckPage.ts";
defineProps<{
    /** Deck metadata (name, format, state, colors, etc.). */
    deck: DeckMeta;
}>();
const popoverId = useId();
/** Close the action popover programmatically. */
function closePopover(): void {
    const dialog = document.getElementById(popoverId);
    if (dialog !== null) dialog.hidePopover();
}
</script>

<template>
    <pop-over icon="more" aria-label="test" class-string="popover-button--rounded" :reference="popoverId" width="14rem">
        <ul class="popover-list">
            <li v-if="deck.visibility === 'private'">
                <button class="popover-list-item" @click="closePopover">
                    <icon name="visibility-on" :size="1" />
                    {{ $t("pages.decks.actions.set_public") }}
                </button>
            </li>
        </ul>
    </pop-over>
</template>
