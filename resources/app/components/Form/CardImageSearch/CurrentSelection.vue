<script setup lang="ts">
import type { DefaultCardArtCrop } from "Types/defaultCardArtCrop";
import ArtCropImage from "Components/Card/ArtCrop/ArtCropImage.vue";
defineProps<{
    card: DefaultCardArtCrop;
}>();
defineEmits<{
    clear: [];
}>();
</script>

<template>
    <div class="current-selection">
        <art-crop-image :card="card" />
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
    gap: 1lh;

    background-color: map.get(c.$form, "input", "background");
    color: map.get(c.$form, "input", "surface");
    border-top-right-radius: map.get(s.$form, "input", "radius-px");
    border-bottom-right-radius: map.get(s.$form, "input", "radius-px");

    .card-image {
        z-index: map.get(z.$index, "default");
    }

    .btn-default {
        align-self: flex-start;
    }
}
</style>
