<script setup lang="ts">
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import { useFormatting } from "Composables/useFormatting.ts";
const { formatDecimals, formatBytes } = useFormatting();
defineProps<{
    type: string;
    num: number;
    size?: number;
}>();
</script>

<template>
    <li class="stats-item">
        <headline :size="4">{{ $t("pages.welcome.stats." + type + ".title") }}</headline>
        <span class="stats-item__num">{{ formatDecimals(num) }}</span>
        <span v-if="size" class="stats-item__size"> <icon name="file" />{{ formatBytes(size) }} </span>
        <span class="stats-item__explanation">{{ $t("pages.welcome.stats." + type + ".explanation") }}</span>
    </li>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.stats-item {
    display: flex;
    flex-direction: column;

    padding: map.get(s.$main, "stats", "padding");
    border: map.get(s.$main, "stats", "border") solid map.get(c.$main, "stats", "border");

    background-color: map.get(c.$main, "stats", "background");
    color: map.get(c.$main, "stats", "surface");
    border-radius: map.get(s.$main, "stats", "radius");

    &__num {
        place-self: center;

        margin-bottom: 0.5rem;

        background: map.get(c.$main, "stats", "num");
        background-clip: text;
        color: transparent;

        font-size: 2rem;
        font-weight: 900;
    }

    &__size {
        display: flex;
        align-items: center;
        place-self: center;

        margin-bottom: 0.5rem;
        gap: 0.5ch;
    }

    &__explanation {
        padding: map.get(s.$main, "stats", "explanation-padding");
        margin-top: auto;

        background-color: map.get(c.$main, "stats", "explanation-background");

        font-size: 0.85em;
    }
}
</style>
