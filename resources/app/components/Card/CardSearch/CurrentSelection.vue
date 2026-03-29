<script setup lang="ts">
withDefaults(
    defineProps<{
        /** When true, the clear button is hidden (card cannot be changed). */
        locked?: boolean;
    }>(),
    { locked: false }
);
defineEmits<{
    clear: [];
}>();
</script>

<template>
    <div class="current-selection">
        <slot />
        <button v-if="!locked" type="button" class="btn-default" @click="$emit('clear')">
            {{ $t("card.search.change_selection") }}
        </button>
    </div>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@use "Abstracts/z-indexes" as z;

.current-selection {
    display: flex;
    flex-grow: 1;
    flex-direction: column;

    padding: 0.75ex 1.5ch 1.25ex;
    border: map.get(s.$components, "input", "border") solid map.get(c.$components, "input", "border");
    border-left-width: 0;
    gap: 1lh;

    background-color: map.get(c.$components, "input", "background");
    color: map.get(c.$components, "input", "surface");
    border-top-right-radius: map.get(s.$components, "input", "radius-px");
    border-bottom-right-radius: map.get(s.$components, "input", "radius-px");

    .card-image {
        z-index: map.get(z.$index, "default");
    }

    .btn-default {
        align-self: flex-start;
    }
}
</style>
