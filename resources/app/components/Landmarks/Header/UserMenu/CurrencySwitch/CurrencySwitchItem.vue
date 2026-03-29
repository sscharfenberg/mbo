<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

const emit = defineEmits(["close"]);

defineProps<{
    /** Currency code (e.g. `"eur"`, `"usd"`). */
    currency: string;
    /** Display label for the currency option. */
    label: string;
    /** Whether this option is currently active. */
    selected: boolean;
}>();
</script>

<template>
    <Link
        class="currency-switch__item"
        :class="{ 'currency-switch__item--selected': selected }"
        :href="'/currency/' + currency"
        method="post"
        @click="emit('close')"
        as="button"
    >
        {{ label }}
    </Link>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@use "Abstracts/timings" as ti;

.currency-switch__item {
    flex-grow: 1;

    padding: 0.4rem 0.6rem;
    border: 0;

    background-color: map.get(c.$components, "currency", "item-background");
    color: map.get(c.$components, "currency", "item-surface");
    border-radius: map.get(s.$theme, "radius");

    font-size: 1rem;
    font-weight: normal;
    line-height: 1;
    text-align: center;
    text-decoration: none;
    text-shadow: none;

    cursor: pointer;

    transition:
        background-color map.get(ti.$timings, "fast") linear,
        color map.get(ti.$timings, "fast") linear;

    &:hover {
        background-color: map.get(c.$components, "currency", "item-background-hover");
        color: map.get(c.$components, "currency", "item-surface-hover");
    }

    &--selected {
        background-color: map.get(c.$components, "currency", "item-background-selected");
        color: map.get(c.$components, "currency", "item-surface-selected");
    }
}
</style>
