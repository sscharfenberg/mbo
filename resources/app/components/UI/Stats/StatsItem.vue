<script setup lang="ts">
import Headline from "Components/UI/Headline.vue";
</script>

<template>
    <li class="stats-item">
        <headline :size="4">
            <slot name="title" />
            <template v-if="$slots.icon" #right>
                <slot name="icon" />
            </template>
        </headline>
        <span class="stats-item__value">
            <slot name="value" />
        </span>
        <span v-if="$slots.detail" class="stats-item__detail">
            <slot name="detail" />
        </span>
        <span v-if="$slots.explanation" class="stats-item__explanation">
            <slot name="explanation" />
        </span>
    </li>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.stats-item {
    display: flex;
    flex-direction: column;

    padding: map.get(s.$components, "stats", "padding");
    border: map.get(s.$components, "stats", "border") solid map.get(c.$components, "stats", "border");

    background-color: map.get(c.$components, "stats", "background");
    color: map.get(c.$components, "stats", "surface");
    border-radius: map.get(s.$components, "stats", "radius");

    &__value {
        $os: map.get(s.$components, "stats", "num", "outline");
        $oc: map.get(c.$components, "stats", "num", "outline");

        margin-bottom: map.get(s.$components, "stats", "num", "gap");

        color: map.get(c.$components, "stats", "num", "surface");

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

    &__detail {
        display: flex;
        align-items: center;

        margin-bottom: map.get(s.$components, "stats", "num", "gap");
        gap: 0.5ch;
    }

    &__explanation {
        padding: map.get(s.$components, "stats", "explanation-padding");
        margin-top: auto;

        background-color: map.get(c.$components, "stats", "explanation-background");

        font-size: 0.85em;
    }
}
</style>
