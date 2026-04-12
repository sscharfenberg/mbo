<script setup lang="ts">
import { computed, onUnmounted, ref, watch } from "vue";
import CardFaceImage from "Components/Card/CardFaceImage.vue";
import NumVisible from "Components/Card/CardSearch/NumVisible.vue";
import type { DeckSearchResult } from "Types/deckPage.ts";

const props = defineProps<{
    results: DeckSearchResult[];
}>();

const emit = defineEmits<{
    select: [result: DeckSearchResult];
}>();

const PAGE_SIZE = 20;
const visibleCount = ref(PAGE_SIZE);
const sentinel = ref<HTMLElement | null>(null);
const visibleResults = computed(() => props.results.slice(0, visibleCount.value));
/** True while the observer should ignore the next intersection (initial fire after observe). */
let skipNext = true;

watch(
    () => props.results,
    () => {
        visibleCount.value = PAGE_SIZE;
        skipNext = true;
    }
);

let observer: IntersectionObserver | null = null;

watch(sentinel, el => {
    observer?.disconnect();
    if (!el) return;
    observer = new IntersectionObserver(
        entries => {
            if (skipNext) {
                skipNext = false;
                return;
            }
            if (entries[0]?.isIntersecting && visibleCount.value < props.results.length) {
                visibleCount.value = Math.min(visibleCount.value + PAGE_SIZE, props.results.length);
            }
        },
        { root: document.getElementById("modal-body") }
    );
    observer.observe(el);
});

onUnmounted(() => observer?.disconnect());
</script>

<template>
    <num-visible v-if="results.length > PAGE_SIZE" :visible-count="visibleCount" :num-total-results="results.length" />
    <ul class="card-add-results">
        <li
            v-for="result in visibleResults"
            :key="result.printing!.id"
            class="card-add-results__item"
            @click="emit('select', result)"
        >
            <card-face-image v-if="result.printing" :card="result.printing" interactive tooltip-container="#modal" />
        </li>
    </ul>
    <div ref="sentinel" class="card-add-results__sentinel" />
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/sizes" as s;

.card-add-results {
    display: grid;
    grid-template-columns: repeat(
        auto-fill,
        minmax(min(map.get(s.$pages, "deck", "add-card", "results-min"), 100%), 1fr)
    );

    padding: 0;
    margin: 0;
    gap: map.get(s.$pages, "deck", "add-card", "gap");

    list-style: none;

    &__item:hover {
        position: relative;
        z-index: 1;
    }

    &__sentinel {
        height: 1px;
    }
}

:deep(.face-image) {
    border-radius: map.get(s.$pages, "deck", "add-card", "radius");
}
</style>
