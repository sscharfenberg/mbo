<script setup lang="ts">
import { useDeckGrouping } from "Composables/useDeckGrouping.ts";
import type { DeckSort } from "Composables/useDeckSort.ts";
import type { DeckCardRow, DeckCommander } from "Types/deckPage";
const props = defineProps<{
    /** Commanders / command zone cards with full oracle + printing data. */
    commanders: DeckCommander[];
    /** All cards in the deck with full oracle + printing data. */
    cards: DeckCardRow[];
    /** Active sort mode — by mana value or alphabetically by name. */
    sortMode: DeckSort;
}>();
const { groups } = useDeckGrouping(
    () => props.cards,
    () => props.sortMode
);
</script>

<template>
    <h2>CardView: Image</h2>
    <div class="card-groups">
        <section v-if="commanders.length" class="card-groups__item">
            <h3>{{ $t("pages.deck.commanders") }} ({{ commanders.length }})</h3>
            <ul class="card-group__list">
                <li v-for="commander in commanders" :key="commander.oracle_card_id">
                    <span class="card__name">{{ commander.name }}</span>
                    {{ JSON.stringify(commander) }}
                </li>
            </ul>
        </section>
        <section v-for="group in groups" :key="group.group" class="card-group">
            <h3>{{ $t(`pages.deck.groups.${group.group}`) }} ({{ group.count }})</h3>
            <ul class="card-group__list">
                <li v-for="card in group.cards" :key="card.id">
                    <span class="card__name">{{ card.name }}</span>
                    {{ JSON.stringify(card) }}
                </li>
            </ul>
        </section>
    </div>
    <p v-if="!groups.length">{{ $t("pages.deck.no_cards") }}</p>
</template>
