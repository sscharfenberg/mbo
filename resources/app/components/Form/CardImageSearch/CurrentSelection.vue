<script setup lang="ts">
import CardImage from "Components/Form/CardImageSearch/CardImage.vue";
import type { CardResult } from "Components/Form/CardImageSearch/types";
defineProps<{
    card: CardResult;
}>();
defineEmits<{
    clear: [];
}>();
</script>

<template>
    <div class="current-selection">
        <card-image :card="card" />
        <button type="button" class="btn-default" @click="$emit('clear')">
            {{ $t("form.fields.card_image.change_selection") }}
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
    border: map.get(s.$form, "input", "border") solid map.get(c.$form, "input", "border");
    border-left-width: 0;
    gap: 1ch;

    background-color: map.get(c.$form, "input", "background");
    color: map.get(c.$form, "input", "surface");
    border-top-right-radius: map.get(s.$form, "input", "radius-px");
    border-bottom-right-radius: map.get(s.$form, "input", "radius-px");

    .card-image {
        z-index: map.get(z.$index, "default");
    }

    .btn-default {
        margin-top: 2ex;
    }
}
</style>
