<script setup lang="ts">
import { onMounted } from "vue";

const openModal = () => document.getElementById("modal")?.showModal();
const closeModal = () => document.getElementById("modal")?.close();
onMounted(() => {
    openModal();
});
</script>

<template>
    <Teleport to="body">
        <dialog id="modal" class="modal-dialog" closedby="closerequest">
            <div class="modal-dialog__content">
                Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor
                sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet.
                <button type="button" @click="closeModal">Close</button>
            </div>
        </dialog>
    </Teleport>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.modal-dialog {
    position: fixed;

    padding: 0;
    border: 0;
    margin: 2lh auto auto;

    background: transparent;

    &::backdrop {
        background: map.get(c.$main, "modal", "backdrop");
        backdrop-filter: blur(10px);
    }

    &__content {
        width: 100%;
        max-width: 480px;
        padding: map.get(s.$main, "modal", "padding");
        border: map.get(s.$main, "modal", "border") solid map.get(c.$main, "modal", "border");

        background-color: map.get(c.$main, "modal", "background");
        color: map.get(c.$main, "modal", "surface");
        border-radius: map.get(s.$main, "modal", "radius");
    }
}
</style>
