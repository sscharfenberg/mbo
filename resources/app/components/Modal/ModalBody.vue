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
    <div id="modal-body" :class="['modal-dialog__body', { 'modal-dialog__body--no-footer': !hasFooter }]"><slot /></div>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/sizes" as s;

.modal-dialog__body {
    overflow-y: auto;

    // `min-height: 0` lets this flex item shrink below its intrinsic content
    // size so the parent's `max-height` cap wins and the scrollbar lives here.
    min-height: 0;
    flex: 1 1 auto;

    padding: map.get(s.$components, "modal", "padding");

    &--no-footer {
        margin-bottom: calc(
            #{map.get(s.$components, "modal", "radius")} - #{map.get(s.$components, "modal", "border")}
        );
    }
}
</style>
