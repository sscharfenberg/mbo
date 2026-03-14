<script setup lang="ts">
import type { DefaultCardArtCrop } from "Types/defaultCardArtCrop";
defineProps<{
    card: DefaultCardArtCrop;
    /** When true, shows a zoom effect on hover. Use in clickable contexts (e.g. results grid). */
    interactive?: boolean;
}>();
</script>

<template>
    <div class="art-crop" :class="{ 'card-image--interactive': interactive }">
        <img :src="card.art_crop" alt="" loading="lazy" class="art-crop__image" />
        <span class="art-crop__panel">
            <span class="art-crop__name">{{ card.name }}</span>
            <img
                :src="`/set/${card.set.code}.svg`"
                class="art-crop__set"
                :alt="`${card.set.code.toUpperCase()} - ${card.set.name}`"
                :title="`${card.set.code.toUpperCase()} - ${card.set.name}`"
            />
        </span>
    </div>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/z-indexes" as z;
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.art-crop {
    position: relative;

    overflow: hidden;
    width: 100%;
    max-width: map.get(s.$main, "art-crop", "card-image-max");
    aspect-ratio: 626 / 457;

    border-radius: map.get(s.$main, "art-crop", "radius");

    &__image {
        position: absolute;
        inset: 0;
        z-index: map.get(z.$index, "background");

        width: 100%;
        height: 100%;

        object-fit: cover;

        transition: transform 0.2s ease;
    }

    &--interactive:hover &__art-crop {
        transform: scale(1.15);
    }

    &__panel {
        display: flex;
        position: absolute;
        right: 0;
        bottom: 0;
        left: 0;
        align-items: center;

        padding: map.get(s.$main, "art-crop", "panel-padding");
        gap: 0.5ch;

        background: map.get(c.$main, "art-crop", "panel-background");
        color: map.get(c.$main, "art-crop", "panel-surface");
        border-bottom-right-radius: map.get(s.$main, "art-crop", "radius");
        border-bottom-left-radius: map.get(s.$main, "art-crop", "radius");
    }

    &__name {
        font-size: 0.9em;
    }

    &__set {
        width: map.get(s.$main, "art-crop", "set");
        height: map.get(s.$main, "art-crop", "set");
        margin-left: auto;

        filter: invert(1);
    }
}
</style>
<style lang="scss">
// doesn't work scoped.
@use "Abstracts/mixins" as m;

@include m.theme-dark(".art-crop__set") {
    filter: none;
}
</style>
