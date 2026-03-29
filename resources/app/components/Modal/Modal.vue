<script setup lang="ts">
import { onMounted, ref } from "vue";
import ModalBody from "./ModalBody.vue";
import ModalFooter from "./ModalFooter.vue";
import ModalHeader from "./ModalHeader.vue";

const emit = defineEmits<{ close: [] }>();

const modalRef = ref<HTMLDialogElement | null>(null);
const contentRef = ref<HTMLDivElement | null>(null);
const isClosing = ref(false);

/**
 * Open the native dialog via `showModal()`.
 *
 * Resets the local closing flag first so that any previous close animation
 * state does not leak into the next open cycle.
 */
const openModal = () => {
    const modal = modalRef.value;
    if (!modal) return;
    isClosing.value = false;
    modal.showModal();
};

/**
 * Close the dialog with an optional exit animation.
 *
 * If reduced motion is enabled (or the content node is unavailable), the
 * dialog closes immediately. Otherwise, it applies the `is-closing` state
 * and waits for the content animation to finish before calling `close()`.
 */
const closeModal = () => {
    const modal = modalRef.value;
    const content = contentRef.value;
    if (!modal?.open || isClosing.value) return;
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches || !content) {
        modal.close();
        emit("close");
        return;
    }
    isClosing.value = true;
    const handleAnimationEnd = (event: AnimationEvent) => {
        if (event.target !== content) return;
        isClosing.value = false;
        modal.close();
        emit("close");
    };
    content.addEventListener("animationend", handleAnimationEnd, { once: true });
};

/**
 * Intercept native cancel requests (for example Escape key presses) so the
 * modal can run its exit animation before the dialog is actually closed.
 *
 * @param event - Native dialog cancel event.
 */
const onDialogCancel = (event: Event) => {
    event.preventDefault();
    closeModal();
};

/**
 * Close the modal when users click outside the content area on the dialog
 * backdrop itself.
 *
 * @param event - Mouse click event emitted by the dialog element.
 */
const onDialogClick = (event: MouseEvent) => {
    if (event.target === event.currentTarget) {
        closeModal();
    }
};

/**
 * Open the dialog immediately after mount so rendering this component
 * behaves like "show this modal now" for consumers.
 */
onMounted(() => {
    openModal();
});
</script>

<template>
    <dialog
        id="modal"
        ref="modalRef"
        class="modal-dialog"
        :class="{ 'is-closing': isClosing }"
        closedby="closerequest"
        @cancel="onDialogCancel"
        @click="onDialogClick"
    >
        <div ref="contentRef" class="modal-dialog__content">
            <modal-header @close="closeModal"><slot name="header" /></modal-header>
            <modal-body :has-footer="!!$slots.footer"><slot /></modal-body>
            <modal-footer v-if="$slots.footer"><slot name="footer" /></modal-footer>
        </div>
    </dialog>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@forward "modal-animations";

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
        opacity: 0;

        background: map.get(c.$components, "modal", "backdrop");
        backdrop-filter: blur(10px);
    }

    &__content {
        display: flex;
        flex-direction: column;

        width: 100%;
        max-width: map.get(s.$main, "modal", "max-width");

        // padding: map.get(s.$main, "modal", "padding");
        border: map.get(s.$main, "modal", "border") solid map.get(c.$components, "modal", "border");
        margin: 1rem 0;

        background-color: map.get(c.$components, "modal", "background");
        color: map.get(c.$components, "modal", "surface");
        border-radius: map.get(s.$main, "modal", "radius");

        // reset modal styles, since we might open the modal from within an
        // element with different values, and we don't want to inherit them.
        font-size: 1rem;
        font-weight: normal;
        text-shadow: none;
    }
}
</style>
