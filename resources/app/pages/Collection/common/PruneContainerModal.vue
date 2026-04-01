<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import FormLegend from "Components/Form/FormLegend.vue";
import Modal from "Components/Modal/Modal.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import { useFormatting } from "Composables/useFormatting.ts";
import type { Container } from "Types/container.ts";

const { formatDecimals, formatPrice } = useFormatting();
/** @emits close — Fired after successful pruning or when the user cancels. */
const emit = defineEmits<{ close: [] }>();
/** @props container — The container to be pruned; its name and stats are shown in the confirmation prompt. */
const props = defineProps<{ container: Container }>();
/** True while the DELETE request is in flight — disables both buttons to prevent double submission. */
const processing = ref<boolean>(false);
/**
 * Sends a DELETE request via Inertia router to remove all cards from the container.
 * On success the modal is closed (emitting `close`), which lets the parent
 * react to the updated Inertia page props.
 */
const onDelete = () => {
    processing.value = true;
    router.delete(`/collection/containers/${props.container.id}/prune`, {
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
        <template #header>{{ $t("pages.containers.prune.title", { name: container.name }) }}</template>
        <form-legend
            :items="[
                { slot: 'warning', icon: 'warning', modifier: 'warning' },
                { slot: 'question', icon: 'question', modifier: 'error' }
            ]"
        >
            <template #warning>
                <i18n-t keypath="pages.containers.prune.warning" scope="global">
                    <template #amount
                        ><strong>{{ formatDecimals(container.totalCards) }}</strong></template
                    >
                    <template #worth
                        ><strong>{{ formatPrice(container.totalPrice) }}</strong></template
                    >
                </i18n-t>
            </template>
            <template #question>{{ $t("pages.containers.prune.question") }} </template>
        </form-legend>
        <template #footer>
            <button type="submit" class="btn-default" :disabled="processing" @click="$emit('close')">
                <icon name="close" />
                {{ $t("pages.containers.prune.neg") }}
            </button>
            <button type="submit" class="btn-primary" :disabled="processing" @click="onDelete">
                <icon name="delete" />
                {{ $t("pages.containers.prune.aff") }}
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
