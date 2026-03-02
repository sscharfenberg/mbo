<script setup lang="ts">
import StatsItem from "@/pages/Guest/Welcome/StatsItem.vue";
import Headline from "Components/UI/Headline.vue";

// Spread into a plain array (the `as const` literal would block .sort()),
// then shuffle in place. The tuple assertion tells TS all 5 indices are defined.
const manaIcons = [...["W", "U", "B", "R", "G"]].sort(() => Math.random() - 0.5) as [
    string,
    string,
    string,
    string,
    string
];

defineProps<{
    oracleCards: { num: number; size: number };
    defaultCards: { num: number; size: number };
    sets: number;
}>();
</script>

<template>
    <headline :size="3">{{ $t("pages.welcome.stats.title") }}</headline>
    <ul class="stats">
        <stats-item type="oracle" :num="oracleCards.num" :size="oracleCards.size" :mana-icon="manaIcons[0]" />
        <stats-item type="default" :num="defaultCards.num" :size="defaultCards.size" :mana-icon="manaIcons[1]" />
        <stats-item type="sets" :num="sets" :mana-icon="manaIcons[2]" />
    </ul>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(200px, 100%), 1fr));

    padding: 0;
    margin: 0;
    gap: 1ch;

    list-style: none;
}
</style>
