<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import FormLegend from "Components/Form/FormLegend.vue";
import Modal from "Components/Modal/Modal.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import type { Container } from "Types/container";
/** @emits close — Fired after successful deletion or when the user cancels. */
const emit = defineEmits<{ close: [] }>();
/** @props container — The container to be deleted; its name is shown in the confirmation prompt. */
const props = defineProps<{ container: Container }>();
/** True while the DELETE request is in flight — disables both buttons to prevent double submission. */
const processing = ref<boolean>(false);
/**
 * Sends a DELETE request via Inertia router to remove the container.
 * On success the modal is closed (emitting `close`), which lets the parent
 * react to the updated Inertia page props. `processing` is reset on finish
 * regardless of outcome so the buttons become interactive again on error.
 */
const onDelete = () => {
    processing.value = true;
    router.delete(`/collection/containers/${props.container.id}`, {
        onSuccess: () => {
            emit("close");
        },
        onFinish: () => {
            processing.value = false;
        }
    });
};
</script>

<template>
    <modal @close="emit('close')">
        <template #header>
            <i18n-t keypath="pages.containers.delete.title" scope="global">
                <template #name
                    ><cite>{{ container.name }}</cite></template
                >
            </i18n-t>
        </template>
        <form-legend :items="[{ slot: 'question', icon: 'question' }]">
            <template #question>{{ $t("pages.containers.delete.question") }} </template>
        </form-legend>
        <template #footer>
            <button type="submit" class="btn-default" :disabled="processing" @click="$emit('close')">
                <icon name="close" />
                {{ $t("pages.containers.delete.neg") }}
            </button>
            <button type="submit" class="btn-primary" :disabled="processing" @click="onDelete">
                <icon name="delete" />
                {{ $t("pages.containers.delete.aff") }}
                <loading-spinner v-if="processing" :size="2" />
            </button>
        </template>
    </modal>
</template>

<style lang="scss" scoped>
.btn-primary {
    margin-left: auto;
}
</style>
