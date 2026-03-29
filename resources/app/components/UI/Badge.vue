<script setup lang="ts">
withDefaults(
    defineProps<{
        type?: "success" | "warning" | "error" | "info";
    }>(),
    {
        type: "info"
    }
);
</script>

<template>
    <span class="badge" :class="type"><slot /></span>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/sizes" as s;
@use "Abstracts/colors" as c;

.badge {
    display: inline-flex;
    align-items: center;

    padding: map.get(s.$components, "badge", "padding");
    border: map.get(s.$components, "badge", "border") solid transparent;
    gap: 0.5ch;

    backdrop-filter: blur(12px);
    border-radius: map.get(s.$components, "badge", "radius");

    font-size: 1rem;
    font-weight: 700;

    &.success {
        background: map.get(c.$state, "success", "background");
        color: map.get(c.$state, "success", "surface");
        border-color: map.get(c.$state, "success", "border");
    }

    &.warning {
        background-color: map.get(c.$state, "warning", "background");
        color: map.get(c.$state, "warning", "surface");
        border-color: map.get(c.$state, "warning", "border");
    }

    &.error {
        background-color: map.get(c.$state, "error", "background");
        color: map.get(c.$state, "error", "surface");
        border-color: map.get(c.$state, "error", "border");
    }

    &.info {
        background-color: map.get(c.$state, "info", "background");
        color: map.get(c.$state, "info", "surface");
        border-color: map.get(c.$state, "info", "border");
    }
}
</style>
