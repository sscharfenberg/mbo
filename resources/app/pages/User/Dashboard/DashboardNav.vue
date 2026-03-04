<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";

const sentinel = ref<HTMLElement | null>(null);
const isStuck = ref(false);

onMounted(() => {
    const observer = new IntersectionObserver(
        entries => {
            isStuck.value = !entries[0]?.isIntersecting;
        },
        { threshold: [1] }
    );
    if (sentinel.value) observer.observe(sentinel.value);
    onUnmounted(() => observer.disconnect());
});
</script>

<template>
    <div ref="sentinel" aria-hidden="true" class="dashboard-nav__sentinel" />
    <nav class="dashboard-nav" :class="{ 'dashboard-nav--sticky': isStuck }">Dashboard Nav</nav>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.dashboard-nav__sentinel {
    height: 0;

    pointer-events: none;
}

.dashboard-nav {
    position: sticky;
    top: 0;
    z-index: 10;

    padding: map.get(s.$main, "dashboard-nav", "padding");

    background-color: map.get(c.$main, "dashboard-nav", "background");
    backdrop-filter: blur(12px);
    color: map.get(c.$main, "dashboard-nav", "surface");
    border-radius: map.get(s.$main, "dashboard-nav", "radius");

    &::before {
        position: absolute;
        inset: 0;
        z-index: -1;

        border: map.get(s.$main, "dashboard-nav", "border") solid transparent;

        background: linear-gradient(
                to bottom right,
                map.get(c.$main, "dashboard-nav", "border-from"),
                map.get(c.$main, "dashboard-nav", "border-to")
            )
            border-box;

        border-radius: inherit;
        mask:
            linear-gradient(black, black) border-box,
            linear-gradient(black, black) padding-box;
        mask-composite: subtract;

        content: "";
    }

    &--sticky {
        border-top-width: 0;

        background-color: map.get(c.$main, "dashboard-nav", "background-sticky");

        border-top-left-radius: 0;
        border-top-right-radius: 0;

        &::before {
            background: map.get(c.$main, "dashboard-nav", "border-sticky");
        }
    }
}
</style>
