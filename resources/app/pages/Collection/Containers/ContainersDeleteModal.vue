<script setup lang="ts">
import { ref } from "vue";
import FormLegend from "Components/Form/FormLegend.vue";
import Modal from "Components/Modal/Modal.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import type { Container } from "Types/container";
const emit = defineEmits<{ close: [] }>();
defineProps<{ container: Container }>();
const processing = ref<boolean>(false);
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
            <button type="submit" class="btn-primary" :disabled="processing">
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
