<script setup lang="ts">
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import { useFormatting } from "Composables/useFormatting.ts";
const { formatDecimals, formatBytes } = useFormatting();
defineProps<{
    type: string;
    num: number;
    size?: number;
    manaIcon: string;
}>();
</script>

<template>
    <li class="stats-item">
        <headline :size="4">
            {{ $t("pages.welcome.stats." + type + ".title") }}
            <template #right>
                <icon :name="manaIcon" />
            </template>
        </headline>
        <span class="stats-item__num">{{ formatDecimals(num) }}</span>
        <span v-if="size" class="stats-item__size"> <icon name="file" />{{ formatBytes(size) }} </span>
        <span class="stats-item__explanation">{{ $t("pages.welcome.stats." + type + ".explanation") }}</span>
    </li>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@use "Abstracts/shadows" as sh;

.stats-item {
    display: flex;
    flex-direction: column;

    padding: map.get(s.$main, "stats", "padding");
    border: map.get(s.$main, "stats", "border") solid map.get(c.$main, "stats", "border");

    background-color: map.get(c.$main, "stats", "background");
    color: map.get(c.$main, "stats", "surface");
    border-radius: map.get(s.$main, "stats", "radius");

    &__num {
        $outline: map.get(c.$main, "stats", "num", "outline");

        margin-bottom: 0.5rem;

        color: map.get(c.$main, "stats", "num", "surface");

        font-size: 2rem;
        font-weight: 900;

        text-shadow:
            -2px -2px $outline,
            2px -2px $outline,
            -2px 2px $outline,
            2px 2px $outline,
            0 -2px $outline,
            0 2px $outline,
            -2px 0 $outline,
            2px 0 $outline;
    }

    &__size {
        display: flex;
        align-items: center;

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
