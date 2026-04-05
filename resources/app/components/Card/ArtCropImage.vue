<script setup lang="ts">
import Icon from "Components/UI/Icon.vue";
import type { DefaultCardArtCrop } from "Types/defaultCardArtCrop.ts";
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
            <span class="art-crop__info">
                <span class="art-crop__name">{{ card.name }}</span>
                <span v-if="card.artist" class="art-crop__artist">
                    <icon name="brush" :size="0" />
                    {{ card.artist }}
                </span>
            </span>
            <img
                v-if="card.set.path"
                :src="card.set.path"
                class="art-crop__set"
                :alt="`${card.set.code.toUpperCase()} - ${card.set.name}`"
                :title="`${card.set.code.toUpperCase()} - ${card.set.name}`"
                v-tooltip="`${card.set.code.toUpperCase()} - ${card.set.name}`"
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
    max-width: map.get(s.$components, "art-crop", "card-image-max");
    aspect-ratio: 626 / 457;

    border-radius: map.get(s.$components, "art-crop", "radius");

    &__image {
        position: absolute;
        inset: 0;
        z-index: map.get(z.$index, "default");

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

        padding: map.get(s.$components, "art-crop", "panel-padding");
        gap: 0.5ch;

        background: map.get(c.$components, "art-crop", "panel-background");
        color: map.get(c.$components, "art-crop", "panel-surface");
        border-bottom-right-radius: map.get(s.$components, "art-crop", "radius");
        border-bottom-left-radius: map.get(s.$components, "art-crop", "radius");
    }

    &__info {
        display: flex;
        flex-direction: column;

        overflow: hidden;
        min-width: 0;
    }

    &__name {
        font-size: 0.9em;
    }

    &__artist {
        display: flex;
        opacity: 0.8;

        overflow: hidden;

        gap: 1ch;

        font-size: 0.75em;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    &__set {
        width: map.get(s.$components, "art-crop", "set");
        height: map.get(s.$components, "art-crop", "set");
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
