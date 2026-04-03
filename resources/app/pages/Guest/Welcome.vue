<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import Paragraph from "Components/UI/Paragraph.vue";
import Stats from "Components/UI/Stats/Stats.vue";
import StatsItem from "Components/UI/Stats/StatsItem.vue";
import { useFormatting } from "Composables/useFormatting.ts";
const { formatDecimals, formatBytes, formatPrice } = useFormatting();
const scryfallLogo = new URL("../../assets/images/scryfall.svg", import.meta.url).href;
defineProps<{
    scryfallStats: {
        oracleCards: { num: number; size: number };
        defaultCards: { num: number; size: number };
        sets: number;
        artists: number;
        artCrops: { num: number; size: number };
        cardImages: { num: number; size: number };
    };
    collectionStats: { totalCards: number; containers: number; totalPrice: number };
}>();
</script>

<template>
    <Head
        ><title>{{ $t("pages.welcome.title") }}</title></Head
    >
    <headline>
        <icon name="home" :size="3" />
        {{ $t("pages.welcome.claim") }}
    </headline>
    <paragraph>{{ $t("pages.welcome.intro") }}</paragraph>
    <headline :size="3">{{ $t("pages.welcome.scryfall_stats.title") }}</headline>
    <stats v-if="scryfallStats">
        <stats-item>
            <template #title>{{ $t("pages.welcome.scryfall_stats.oracle.title") }}</template>
            <template #icon>
                <img src="/symbol/W.svg" alt="white mana" class="icon medium" />
            </template>
            <template #value>{{ formatDecimals(scryfallStats.oracleCards.num) }}</template>
            <template #detail><icon name="file" />{{ formatBytes(scryfallStats.oracleCards.size) }}</template>
            <template #explanation>{{ $t("pages.welcome.scryfall_stats.oracle.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.welcome.scryfall_stats.default.title") }}</template>
            <template #icon>
                <img src="/symbol/U.svg" alt="blue mana" class="icon medium" />
            </template>
            <template #value>{{ formatDecimals(scryfallStats.defaultCards.num) }}</template>
            <template #detail><icon name="file" />{{ formatBytes(scryfallStats.defaultCards.size) }}</template>
            <template #explanation>{{ $t("pages.welcome.scryfall_stats.default.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.welcome.scryfall_stats.sets.title") }}</template>
            <template #icon>
                <img src="/symbol/B.svg" alt="black mana" class="icon medium" />
            </template>
            <template #value>{{ formatDecimals(scryfallStats.sets) }}</template>
            <template #explanation>{{ $t("pages.welcome.scryfall_stats.sets.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.welcome.scryfall_stats.artists.title") }}</template>
            <template #icon>
                <img src="/symbol/R.svg" alt="red mana" class="icon medium" />
            </template>
            <template #value>{{ formatDecimals(scryfallStats.artists) }}</template>
            <template #explanation>{{ $t("pages.welcome.scryfall_stats.artists.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.welcome.scryfall_stats.artCrops.title") }}</template>
            <template #icon>
                <img src="/symbol/G.svg" alt="green mana" class="icon medium" />
            </template>
            <template #value>{{ formatDecimals(scryfallStats.artCrops.num) }}</template>
            <template #detail><icon name="file" />{{ formatBytes(scryfallStats.artCrops.size) }}</template>
            <template #explanation>{{ $t("pages.welcome.scryfall_stats.artCrops.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.welcome.scryfall_stats.cardImages.title") }}</template>
            <template #icon>
                <img src="/symbol/T.svg" alt="tap symbol" class="icon medium" />
            </template>
            <template #value>{{ formatDecimals(scryfallStats.cardImages.num) }}</template>
            <template #detail><icon name="file" />{{ formatBytes(scryfallStats.cardImages.size) }}</template>
            <template #explanation>{{ $t("pages.welcome.scryfall_stats.cardImages.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.welcome.scryfall_stats.scryfall.title") }}</template>
            <template #icon>
                <img :src="scryfallLogo" alt="Scryfall" class="icon medium" />
            </template>
            <template #detail>
                <a href="https://scryfall.com" target="_blank" rel="noopener noreferrer" class="btn-primary"
                    ><icon name="external-link" />scryfall</a
                >
            </template>
            <template #explanation>{{ $t("pages.welcome.scryfall_stats.scryfall.explanation") }}</template>
        </stats-item>
    </stats>
    <br />
    <headline :size="3">{{ $t("pages.welcome.collection_stats.title") }}</headline>
    <stats v-if="collectionStats">
        <stats-item>
            <template #title>{{ $t("pages.welcome.collection_stats.cards.title") }}</template>
            <template #icon>
                <img src="/symbol/2-W.svg" alt="tap symbol" class="icon medium" />
            </template>
            <template #value>{{ formatDecimals(collectionStats.totalCards) }}</template>
            <template #explanation>{{ $t("pages.welcome.collection_stats.cards.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.welcome.collection_stats.containers.title") }}</template>
            <template #icon>
                <img src="/symbol/B-G-P.svg" alt="tap symbol" class="icon medium" />
            </template>
            <template #value>{{ formatDecimals(collectionStats.containers) }}</template>
            <template #explanation>{{ $t("pages.welcome.collection_stats.containers.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.welcome.collection_stats.worth.title") }}</template>
            <template #icon>
                <img src="/symbol/S.svg" alt="tap symbol" class="icon medium" />
            </template>
            <template #value>{{ formatPrice(collectionStats.totalPrice) }}</template>
            <template #explanation>{{ $t("pages.welcome.collection_stats.worth.explanation") }}</template>
        </stats-item>
    </stats>
</template>
