<script setup lang="ts">
import { useId } from "vue";
import ColorIdentity from "Components/Card/ColorIdentity.vue";
import DeckState from "Components/Deck/DeckState.vue";
import Icon from "Components/UI/Icon.vue";
import PopOver from "Components/UI/PopOver.vue";
import VisibilityBadge from "Components/UI/VisibilityBadge.vue";
import type { DeckMeta } from "Types/deckPage.ts";
const popoverId = useId();
defineProps<{
    /** Deck metadata (name, format, state, colors, etc.). */
    deck: DeckMeta;
}>();
/** Close the action popover programmatically. */
function closePopover(): void {
    const dialog = document.getElementById(popoverId);
    if (dialog !== null) dialog.hidePopover();
}
</script>

<template>
    <section class="deck-meta">
        <header class="deck-meta__name">
            {{ deck.name.toUpperCase() }}
            <pop-over
                icon="more"
                aria-label="test"
                class-string="popover-button--rounded"
                :reference="popoverId"
                width="14rem"
            >
                <ul class="popover-list">
                    <li v-if="deck.visibility === 'private'">
                        <button class="popover-list-item" @click="closePopover">
                            <icon name="visibility-on" :size="1" />
                            {{ $t("pages.decks.actions.set_public") }}
                        </button>
                    </li>
                </ul>
            </pop-over>
        </header>
        <div class="deck-meta__badges">
            <color-identity class="deck-meta__colors" :color-identity="deck.colors" />
            <visibility-badge :visibility="deck.visibility" />
            <deck-state :state="deck.state" />
        </div>
        <pre v-if="deck.description">{{ deck.description }}</pre>

        {{ deck }}
    </section>
</template>

<style lang="scss" scoped>
:deep(.deck-state) {
    padding: 0.11lh 1ch;

    font-size: 0.9em;
}
</style>
