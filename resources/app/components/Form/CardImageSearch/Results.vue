<script setup lang="ts">
import { computed, onUnmounted, ref, watch } from "vue";
import ArtCropImage from "Components/Card/ArtCrop/ArtCropImage.vue";
import NumVisible from "Components/Form/CardImageSearch/NumVisible.vue";
import type { DefaultCardArtCrop } from "Types/defaultCardArtCrop";
const props = defineProps<{
    results: DefaultCardArtCrop[];
}>();
const emit = defineEmits<{
    change: [card: DefaultCardArtCrop];
}>();
/** Number of results rendered per page / scroll batch. */
const PAGE_SIZE = 20;
/** The id of the currently highlighted result, or null if none selected. */
const selectedId = ref<string | null>(null);
/** How many results are currently visible (grows as the user scrolls). */
const visibleCount = ref(PAGE_SIZE);
/** Ref to the invisible sentinel element that triggers infinite scroll. */
const sentinel = ref<HTMLElement | null>(null);
/** The slice of results currently rendered in the DOM. */
const visibleResults = computed(() => props.results.slice(0, visibleCount.value));
/** Marks the clicked card as selected and notifies the parent. */
function selectCard(card: DefaultCardArtCrop) {
    selectedId.value = card.id;
    emit("change", card);
}
/** Reset visible count whenever the results list is replaced (new search). */
watch(
    () => props.results,
    () => {
        visibleCount.value = PAGE_SIZE;
    }
);
let observer: IntersectionObserver | null = null;
/**
 * Observe the sentinel element. When it enters the viewport, load the next
 * batch of results. The observer is recreated whenever the sentinel mounts.
 */
watch(sentinel, el => {
    observer?.disconnect();
    if (!el) return;
    observer = new IntersectionObserver(entries => {
        if (entries[0]?.isIntersecting && visibleCount.value < props.results.length) {
            visibleCount.value = Math.min(visibleCount.value + PAGE_SIZE, props.results.length);
        }
    });
    observer.observe(el);
});
onUnmounted(() => observer?.disconnect());
</script>

<template>
    <div class="results-wrapper">
        <num-visible
            v-if="results.length > PAGE_SIZE"
            :visible-count="visibleCount"
            :num-total-results="results.length"
        />
        <ul class="results">
            <li
                v-for="card in visibleResults"
                :key="card.id"
                class="result"
                :class="{ 'result--active': card.id === selectedId }"
                @click="selectCard(card)"
            >
                <art-crop-image :card="card" interactive />
            </li>
        </ul>
        <div ref="sentinel" class="results__sentinel" />
    </div>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/shadows" as sh;
@use "Abstracts/sizes" as s;

.results-wrapper {
    display: contents;
}

.results {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(#{map.get(s.$main, "art-crop", "min-width")}, 100%), 1fr));

    padding: 0;
    margin: 0;
    gap: 1ch;

    list-style: none;

    &__sentinel {
        height: 1px;
    }
}

.result {
    cursor: pointer;

    &--active .card-image {
        box-shadow: map.get(sh.$main, "card-image-active");
    }
}
</style>
