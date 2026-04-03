<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import FormLegend from "Components/Form/FormLegend.vue";
import Modal from "Components/Modal/Modal.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import { useFormatting } from "Composables/useFormatting.ts";

const { formatDecimals, formatPrice } = useFormatting();
/** @emits close — Fired after successful deletion or when the user cancels. */
const emit = defineEmits<{ close: [] }>();
defineProps<{
    totalCards: number;
    totalPrice: number;
    containers: number;
}>();
/** True while the DELETE request is in flight. */
const processing = ref(false);
/**
 * Sends a DELETE request via Inertia router to nuke the entire collection.
 * On success the modal is closed — the controller redirects back with a flash message.
 */
const onDelete = () => {
    processing.value = true;
    router.delete("/collection", {
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
            {{ $t("pages.collection.nuke.title") }}
        </template>
        <form-legend
            :items="[
                { slot: 'warning', icon: 'warning', modifier: 'warning' },
                { slot: 'question', icon: 'question', modifier: 'error' }
            ]"
        >
            <template #warning>
                <i18n-t keypath="pages.collection.nuke.summary" scope="global">
                    <template #amount
                        ><strong>{{ formatDecimals(totalCards) }}</strong></template
                    >
                    <template #containers
                        ><strong>{{ formatDecimals(containers) }}</strong></template
                    >
                    <template #worth
                        ><strong>{{ formatPrice(totalPrice) }}</strong></template
                    >
                </i18n-t>
            </template>
            <template #question>{{ $t("pages.collection.nuke.question") }} </template>
        </form-legend>
        <template #footer>
            <button type="button" class="btn-default" :disabled="processing" @click="$emit('close')">
                <icon name="close" />
                {{ $t("pages.collection.nuke.neg") }}
            </button>
            <button type="button" class="btn-primary" :disabled="processing" @click="onDelete">
                <icon name="delete" />
                {{ $t("pages.collection.nuke.aff") }}
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
