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

    padding: map.get(s.$components, "modal", "padding");
    border-top: map.get(s.$components, "modal", "border") solid map.get(c.$components, "modal", "footer-border");
    gap: 2ch;

    background-color: map.get(c.$components, "modal", "footer-background");
    color: map.get(c.$components, "modal", "footer-surface");
    border-end-end-radius: calc(
        #{map.get(s.$components, "modal", "radius")} - #{map.get(s.$components, "modal", "border")}
    );
    border-end-start-radius: calc(
        #{map.get(s.$components, "modal", "radius")} - #{map.get(s.$components, "modal", "border")}
    );
}
</style>
