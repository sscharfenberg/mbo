<script setup lang="ts">
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
</script>

<template>
    <div class="nav-overlay" aria-hidden="true">
        <loading-spinner :size="8" :branded="true" />
    </div>
</template>

<style scoped lang="scss">
@use "Abstracts/colors" as c;
@use "Abstracts/z-indexes" as z;
@use "sass:map";

.nav-overlay {
    display: flex;
    position: absolute;
    inset: 0;
    z-index: map.get(z.$index, "overlay");
    align-items: center;
    justify-content: center;

    opacity: 0;

    background-color: map.get(c.$app, "nav-overlay", "background");
    border-radius: inherit;

    animation: nav-overlay-show 0s 150ms forwards; // 100ms delay, so very fast requests do not show the overlay
}

@keyframes nav-overlay-show {
    to {
        opacity: 1;
    }
}
</style>
