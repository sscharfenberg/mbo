<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import CardViewImage from "@/pages/Decks/Deck/Cards/CardViewImage.vue";
import CardViewText from "@/pages/Decks/Deck/Cards/CardViewText.vue";
import DeckHeader from "@/pages/Decks/Deck/DeckHeader.vue";
import DeckNavigation from "@/pages/Decks/Deck/Navigation/DeckNavigation.vue";
import { useBreadcrumbs } from "Composables/useBreadcrumbs.ts";
import { useDeckSort } from "Composables/useDeckSort.ts";
import { useDeckView } from "Composables/useDeckView.ts";
import type { DeckCardRow, DeckCategoryRow, DeckCommander, DeckMeta } from "Types/deckPage";
const props = defineProps<{
    /** Deck metadata (name, format, state, colors, etc.). */
    deck: DeckMeta;
    /** Commanders / command zone cards with full oracle + printing data. */
    commanders: DeckCommander[];
    /** All cards in the deck with full oracle + printing data. */
    cards: DeckCardRow[];
    /** User-defined categories for this deck. */
    categories: DeckCategoryRow[];
}>();
const { setBreadcrumbs } = useBreadcrumbs();
setBreadcrumbs([{ labelKey: "pages.decks.link", href: "/decks", icon: "deck" }, { label: props.deck.name }]);
/** Effective deck view mode — localStorage override for this deck, or the user's default. */
const { viewMode } = useDeckView(props.deck.id);
/** Effective deck sort mode — localStorage override for this deck, or the user's default. */
const { sortMode } = useDeckSort(props.deck.id);
</script>

<template>
    <Head
        ><title>{{ $t("pages.deck.title", { name: deck.name }) }}</title></Head
    >
    <deck-header :deck="deck" :has-commanders="commanders.length > 0" />
    <deck-navigation :deck="deck" :cards="cards" />
    <card-view-text v-if="viewMode === 'text'" :commanders="commanders" :cards="cards" :sort-mode="sortMode" />
    <card-view-image v-if="viewMode === 'cards'" :commanders="commanders" :cards="cards" :sort-mode="sortMode" />

    <section>
        <template v-if="deck.default_card_image">
            <h3>Default Card Image</h3>
            <img
                v-if="deck.default_card_image.card_image_0"
                :src="deck.default_card_image.card_image_0"
                :alt="deck.name"
            />
            <img
                v-if="deck.default_card_image.card_image_1"
                :src="deck.default_card_image.card_image_1"
                :alt="deck.name"
            />
        </template>
    </section>

    <section>
        <h2>{{ $t("pages.deck.categories") }}</h2>
        <template v-if="categories.length">
            <pre>{{ JSON.stringify(categories, null, 2) }}</pre>
        </template>
        <p v-else>{{ $t("pages.deck.no_categories") }}</p>
    </section>
</template>
