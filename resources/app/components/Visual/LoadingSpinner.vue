<script setup lang="ts">
const props = defineProps({
    size: {
        type: Number,
        default: 4
    }
});
const pixelSize = `${props.size * 12}px`;
</script>

<template>
    <span class="loading-spinner"></span>
</template>

<style scoped lang="scss">
.loading-spinner {
    --pixel-size: v-bind(pixelSize);
}
</style>
