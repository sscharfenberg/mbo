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

.stats-item {
    display: flex;
    flex-direction: column;

    padding: map.get(s.$main, "stats", "padding");
    border: map.get(s.$main, "stats", "border") solid map.get(c.$main, "stats", "border");

    background-color: map.get(c.$main, "stats", "background");
    color: map.get(c.$main, "stats", "surface");
    border-radius: map.get(s.$main, "stats", "radius");

    &__num {
        $os: map.get(s.$main, "stats", "num", "outline");
        $oc: map.get(c.$main, "stats", "num", "outline");

        margin-bottom: map.get(s.$main, "stats", "num", "gap");

        color: map.get(c.$main, "stats", "num", "surface");

        font-size: 2rem;
        font-weight: 900;

        text-shadow:
            -#{$os} -#{$os} $oc,
            #{$os} -#{$os} $oc,
            -#{$os} #{$os} $oc,
            #{$os} #{$os} $oc,
            0 -#{$os} $oc,
            0 #{$os} $oc,
            -#{$os} 0 $oc,
            #{$os} 0 $oc;
    }

    &__size {
        display: flex;
        align-items: center;

        margin-bottom: map.get(s.$main, "stats", "num", "gap");
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
