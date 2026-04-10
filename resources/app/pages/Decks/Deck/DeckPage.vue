<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import DeckHeader from "@/pages/Decks/Deck/DeckHeader.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import { useBreadcrumbs } from "Composables/useBreadcrumbs.ts";
import { useFormatting } from "Composables/useFormatting.ts";
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

const { formatDateTime } = useFormatting();
const { setBreadcrumbs } = useBreadcrumbs();

setBreadcrumbs([{ labelKey: "pages.decks.link", href: "/decks", icon: "deck" }, { label: props.deck.name }]);
</script>

<template>
    <Head
        ><title>{{ $t("pages.deck.title", { name: deck.name }) }}</title></Head
    >
    <headline>
        <icon name="deck" :size="3" />
        {{ deck.name }}
    </headline>

    <deck-header :deck="deck" />

    <section>
        <h2>Debug</h2>
        <h2>Metadata</h2>
        <dl>
            <dt>{{ $t("pages.deck.format") }}</dt>
            <dd>{{ $t(`enums.card_formats.${deck.format}`) }}</dd>

            <dt>{{ $t("pages.deck.state") }}</dt>
            <dd>{{ $t(`enums.deck_state.${deck.state}`) }}</dd>

            <dt>{{ $t("pages.deck.visibility") }}</dt>
            <dd>{{ deck.visibility }}</dd>

            <dt>{{ $t("pages.deck.colors") }}</dt>
            <dd>{{ deck.colors ?? "—" }}</dd>

            <dt>{{ $t("pages.deck.bracket") }}</dt>
            <dd>{{ deck.bracket ?? "—" }}</dd>

            <dt>{{ $t("pages.deck.card_count") }}</dt>
            <dd>{{ deck.card_count }}</dd>

            <dt>{{ $t("pages.deck.last_activity") }}</dt>
            <dd>{{ formatDateTime(deck.last_activity) }}</dd>

            <dt>{{ $t("pages.deck.description") }}</dt>
            <dd>{{ deck.description ?? $t("pages.deck.no_description") }}</dd>
        </dl>

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
        <h2>{{ $t("pages.deck.commanders") }}</h2>
        <span>{{ JSON.stringify(commanders, null, 2) }}</span>
    </section>

    <section>
        <h2>{{ $t("pages.deck.cards") }}</h2>
        <template v-if="cards.length">
            <pre>{{ JSON.stringify(cards, null, 2) }}</pre>
        </template>
        <p v-else>{{ $t("pages.deck.no_cards") }}</p>
    </section>

    <section>
        <h2>{{ $t("pages.deck.categories") }}</h2>
        <template v-if="categories.length">
            <pre>{{ JSON.stringify(categories, null, 2) }}</pre>
        </template>
        <p v-else>{{ $t("pages.deck.no_categories") }}</p>
    </section>
</template>
