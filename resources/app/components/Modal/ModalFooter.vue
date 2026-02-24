<script setup lang="ts"></script>

<template>
    <div class="modal-dialog__footer"><slot /></div>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.modal-dialog__footer {
    display: flex;
    align-items: center;

    padding: map.get(s.$main, "modal", "padding");
    border-top: map.get(s.$main, "modal", "border") solid map.get(c.$main, "modal", "footer-border");
    gap: 2ch;

    background-color: map.get(c.$main, "modal", "footer-background");
    color: map.get(c.$main, "modal", "footer-surface");
    border-end-end-radius: calc(#{map.get(s.$main, "modal", "radius")} - #{map.get(s.$main, "modal", "border")});
    border-end-start-radius: calc(#{map.get(s.$main, "modal", "radius")} - #{map.get(s.$main, "modal", "border")});
}
</style>
