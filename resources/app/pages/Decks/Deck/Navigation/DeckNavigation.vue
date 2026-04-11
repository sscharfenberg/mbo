<script setup lang="ts">
import type { DeckMeta } from "Types/deckPage.ts";
import DeckNavigationAdd from "./DeckNavigationAdd.vue";
import DeckNavigationSort from "./DeckNavigationSort.vue";
import DeckNavigationView from "./DeckNavigationView.vue";
defineProps<{
    /** Deck metadata (name, format, state, colors, etc.). */
    deck: DeckMeta;
}>();
</script>

<template>
    <div :aria-label="$t('pages.deck.navigation.label')" class="deck-navigation">
        <deck-navigation-add :deck="deck" />
        <deck-navigation-view :deck="deck" />
        <deck-navigation-sort :deck="deck" />
    </div>
</template>

<style lang="scss" scoped>
.deck-navigation {
    display: flex;

    gap: 1rem;
}
</style>
