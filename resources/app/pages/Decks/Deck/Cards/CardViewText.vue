<script setup lang="ts">
import { useDeckGrouping } from "Composables/useDeckGrouping.ts";
import type { DeckCardRow, DeckCommander } from "Types/deckPage";
const props = defineProps<{
    /** Commanders / command zone cards with full oracle + printing data. */
    commanders: DeckCommander[];
    /** All cards in the deck with full oracle + printing data. */
    cards: DeckCardRow[];
}>();
const { groups } = useDeckGrouping(() => props.cards);
</script>

<template>
    <h2>CardView: text</h2>
    <section>
        <h3>{{ $t("pages.deck.commanders") }}</h3>
        <pre>{{ JSON.stringify(commanders, null, 2) }}</pre>
    </section>
    <section v-for="group in groups" :key="group.group">
        <h3>{{ $t(`pages.deck.groups.${group.group}`) }} ({{ group.count }})</h3>
        <pre>{{ JSON.stringify(group.cards, null, 2) }}</pre>
    </section>
    <p v-if="!groups.length">{{ $t("pages.deck.no_cards") }}</p>
</template>