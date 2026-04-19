<script setup lang="ts">
import type { DeckCardRow, DeckMeta } from "Types/deckPage.ts";
import DeckNavigationAdd from "./DeckNavigationAdd.vue";
import DeckNavigationSort from "./DeckNavigationSort.vue";
import DeckNavigationView from "./DeckNavigationView.vue";
defineProps<{
    /** Deck metadata (name, format, state, colors, etc.). */
    deck: DeckMeta;
    /** All cards in the deck — forwarded for zone capacity checks. */
    cards: DeckCardRow[];
}>();
</script>

<template>
    <div :aria-label="$t('pages.deck.navigation.label')" class="deck-navigation">
        <deck-navigation-add :deck="deck" :cards="cards" />
        <deck-navigation-view :deck="deck" />
        <deck-navigation-sort :deck="deck" />
    </div>
</template>

<style lang="scss" scoped>
.deck-navigation {
    display: flex;
    align-items: flex-end;
    flex-wrap: wrap;

    margin: 0 0 1rem;
    gap: 1rem;
}
</style>
