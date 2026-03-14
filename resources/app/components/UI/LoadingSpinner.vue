<script setup lang="ts">
const props = withDefaults(defineProps<{ size?: number; branded?: boolean }>(), { size: 4, branded: false });
const pixelSize = `${props.size * 12}px`;
</script>

<template>
    <span :class="['loading-spinner', { branded: props.branded }]"></span>
</template>

<style scoped lang="scss">
/**
 * styles are located in
 * resources/app/styles/components/pogress/_loading-spinner.scss
 */
.loading-spinner {
    --pixel-size: v-bind(pixelSize);
}
</style>
