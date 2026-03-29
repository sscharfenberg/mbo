<script setup lang="ts">
import { ref } from "vue";
import Icon from "Components/UI/Icon.vue";
import type { DefaultCardImage } from "Types/defaultCardImage.ts";
defineProps<{
    card: DefaultCardImage;
    /** When true, shows a zoom effect on hover. Use in clickable contexts (e.g. results grid). */
    interactive?: boolean;
}>();
/** True when the back face is showing. */
const flipped = ref(false);
/** True while the flip animation is running (prevents rapid double-clicks). */
const animating = ref(false);
function onFlip() {
    if (animating.value) return;
    animating.value = true;
    flipped.value = !flipped.value;
}
</script>

<template>
    <div
        class="face-image"
        :class="{ 'face-image--interactive': interactive, 'face-image--flipped': flipped }"
        @transitionend="animating = false"
    >
        <img :src="card.card_image_0 ?? undefined" :alt="card.name" loading="lazy" class="face-image__front" />
        <img
            v-if="card.card_image_1"
            :src="card.card_image_1"
            :alt="card.name"
            loading="lazy"
            class="face-image__back"
        />
        <button type="button" class="face-image__flip" v-if="card.card_image_1" @click.stop="onFlip">
            <icon name="flip" />
        </button>
        <div class="face-image__panel">
            <span class="face-image__panel-line">
                <icon name="star" :size="0" />
                {{ card.cn }}
                <img
                    :src="`/set/${card.set.code}.svg`"
                    class="face-image__set"
                    :alt="`${card.set.code.toUpperCase()} - ${card.set.name}`"
                    :title="`${card.set.code.toUpperCase()} - ${card.set.name}`"
                    v-tooltip="`${card.set.code.toUpperCase()} - ${card.set.name}`"
                />
            </span>
            <span v-if="card.artist" class="face-image__panel-artist">
                <icon name="brush" :size="0" />
                {{ card.artist }}
            </span>
        </div>
    </div>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@use "Abstracts/z-indexes" as z;
@use "Abstracts/timings" as ti;

.face-image {
    position: relative;

    width: 100%;
    max-width: map.get(s.$components, "face-image", "card-image-max");

    border-radius: map.get(s.$components, "face-image", "radius");
    perspective: 800px; // gives the 3D depth effect

    &__front,
    &__back {
        display: block;
        backface-visibility: hidden;

        width: 100%;

        border-radius: inherit;

        transition: transform map.get(ti.$timings, "medium") ease;
    }

    &__back {
        position: absolute;
        inset: 0;

        height: 100%;
        transform: rotateY(180deg);

        object-fit: cover;
    }

    &--flipped &__front {
        transform: rotateY(-180deg);
    }

    &--flipped &__back {
        transform: rotateY(0deg);
    }

    &__flip {
        position: absolute;
        right: 1rem;
        bottom: 50%;
        z-index: map.get(z.$index, "main");

        padding: 0.5rem;
        border: 0;

        // transform: translateY(-50%);

        background-color: map.get(c.$components, "face-image", "flip", "background");
        color: map.get(c.$components, "face-image", "flip", "surface");
        border-radius: 100dvh;

        cursor: pointer;

        transition:
            background-color map.get(ti.$timings, "fast") linear,
            color map.get(ti.$timings, "fast") linear;

        &:hover {
            background-color: map.get(c.$components, "face-image", "flip", "background-hover");
            color: map.get(c.$components, "face-image", "flip", "surface-hover");
        }

        .icon {
            width: map.get(s.$components, "face-image", "flip-size");
            height: map.get(s.$components, "face-image", "flip-size");
        }
    }

    &__panel {
        position: absolute;
        inset: auto 0 0;
        z-index: map.get(z.$index, "main");

        padding: map.get(s.$components, "face-image", "panel-padding");

        background-color: map.get(c.$components, "face-image", "meta", "background");
        color: map.get(c.$components, "face-image", "meta", "surface");
        border-bottom-right-radius: inherit;
        border-bottom-left-radius: inherit;

        &-line,
        &-artist {
            display: flex;
            align-items: center;

            gap: 1ch;

            font-size: 0.9em;
        }

        &-artist {
            opacity: 0.8;

            font-size: 0.8em;
        }
    }

    &__set {
        width: map.get(s.$components, "face-image", "set");
        height: map.get(s.$components, "face-image", "set");
        margin-left: auto;

        filter: invert(1);
    }
}
</style>
<style lang="scss">
// doesn't work scoped.
@use "Abstracts/mixins" as m;

@include m.theme-dark(".face-image__set") {
    filter: none;
}
</style>
