<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import Stats from "Components/UI/Stats/Stats.vue";
import StatsItem from "Components/UI/Stats/StatsItem.vue";
import { useBreadcrumbs } from "Composables/useBreadcrumbs.ts";
import { useFormatting } from "Composables/useFormatting.ts";

const { formatDecimals, formatPrice } = useFormatting();

defineProps<{
    stats: {
        totalCards: number;
        totalStacks: number;
        totalPrice: number;
        containers: number;
        commons: number;
        uncommons: number;
        rares: number;
        mythics: number;
    };
}>();

const { setBreadcrumbs } = useBreadcrumbs();
setBreadcrumbs([{ labelKey: "pages.collection.link" }]);
</script>

<template>
    <Head
        ><title>{{ $t("pages.collection.title") }}</title></Head
    >
    <headline>
        <icon name="collection" :size="3" />
        {{ $t("pages.collection.title") }}
    </headline>
    <stats>
        <stats-item>
            <template #title>{{ $t("pages.collection.stats.totalCards.title") }}</template>
            <template #value>{{ formatDecimals(stats.totalCards) }}</template>
            <template #explanation>{{ $t("pages.collection.stats.totalCards.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.collection.stats.totalStacks.title") }}</template>
            <template #value>{{ formatDecimals(stats.totalStacks) }}</template>
            <template #explanation>{{ $t("pages.collection.stats.totalStacks.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.collection.stats.totalPrice.title") }}</template>
            <template #value>{{ formatPrice(stats.totalPrice) }}</template>
            <template #explanation>{{ $t("pages.collection.stats.totalPrice.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.collection.stats.containers.title") }}</template>
            <template #value>{{ formatDecimals(stats.containers) }}</template>
            <template #explanation>{{ $t("pages.collection.stats.containers.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.collection.stats.commons.title") }}</template>
            <template #value>{{ formatDecimals(stats.commons) }}</template>
            <template #explanation>{{ $t("pages.collection.stats.commons.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.collection.stats.uncommons.title") }}</template>
            <template #value>{{ formatDecimals(stats.uncommons) }}</template>
            <template #explanation>{{ $t("pages.collection.stats.uncommons.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.collection.stats.rares.title") }}</template>
            <template #value>{{ formatDecimals(stats.rares) }}</template>
            <template #explanation>{{ $t("pages.collection.stats.rares.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.collection.stats.mythics.title") }}</template>
            <template #value>{{ formatDecimals(stats.mythics) }}</template>
            <template #explanation>{{ $t("pages.collection.stats.mythics.explanation") }}</template>
        </stats-item>
    </stats>
    <nav class="links">
        <Link href="/collection/containers" class="btn-primary">
            <icon name="storage" />
            {{ $t("pages.containers.link") }}
        </Link>
        <Link href="/collection/add" class="btn-default">
            <icon name="add" />
            {{ $t("pages.add_cards.link") }}
        </Link>
    </nav>
</template>

<style lang="scss" scoped>
.links {
    display: flex;

    margin: 1rem 0;
    gap: 1rem;
}
</style>
