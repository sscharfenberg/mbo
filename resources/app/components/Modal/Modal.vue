<script setup lang="ts">
import { onMounted } from "vue";
import ModalBody from "./ModalBody.vue";
import ModalFooter from "./ModalFooter.vue";
import ModalHeader from "./ModalHeader.vue";
const openModal = () => document.getElementById("modal")?.showModal();
const closeModal = () => document.getElementById("modal")?.close();
onMounted(() => {
    openModal();
});
</script>

<template>
    <dialog id="modal" class="modal-dialog" closedby="closerequest">
        <div class="modal-dialog__content">
            <modal-header @close="closeModal"><slot name="header" /></modal-header>
            <modal-body><slot /></modal-body>
            <modal-footer v-if="$slots.footer"><slot name="footer" /></modal-footer>
        </div>
    </dialog>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.modal-dialog {
    position: fixed;

    overflow-y: hidden;
    height: 100dvh;
    max-height: 100dvh;
    padding: 0;
    border: 0;
    margin: 0 auto;

    background: transparent;
    outline: 0;

    &::backdrop {
        background: map.get(c.$main, "modal", "backdrop");
        backdrop-filter: blur(10px);
    }

    &__content {
        display: flex;
        flex-direction: column;

        width: 100%;
        max-width: map.get(s.$main, "modal", "max-width");

        // padding: map.get(s.$main, "modal", "padding");
        border: map.get(s.$main, "modal", "border") solid map.get(c.$main, "modal", "border");
        margin: 1lh 0;

        background-color: map.get(c.$main, "modal", "background");
        color: map.get(c.$main, "modal", "surface");
        border-radius: map.get(s.$main, "modal", "radius");
    }
}
</style>
