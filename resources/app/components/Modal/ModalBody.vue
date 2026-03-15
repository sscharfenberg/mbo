<script setup lang="ts">
withDefaults(
    defineProps<{
        hasFooter?: boolean;
    }>(),
    {
        hasFooter: false
    }
);
</script>

<template>
    <div :class="['modal-dialog__body', { 'modal-dialog__body--no-footer': !hasFooter }]"><slot /></div>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/sizes" as s;

.modal-dialog__body {
    overflow-y: auto;
    max-height: 75dvh;

    padding: map.get(s.$main, "modal", "padding");

    &--no-footer {
        margin-bottom: calc(#{map.get(s.$main, "modal", "radius")} - #{map.get(s.$main, "modal", "border")});
    }
}
</style>
