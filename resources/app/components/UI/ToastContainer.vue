<script setup lang="ts">
import { usePage } from "@inertiajs/vue3";
import { watch } from "vue";
import { useI18n } from "vue-i18n";
import Icon from "Components/UI/Icon.vue";
import { useToast } from "Composables/useToast";
import type { ToastType } from "Composables/useToast";
const { t } = useI18n();
const { activeToasts, addToast, removeToast } = useToast();
const page = usePage();
// Bridge Inertia session flash messages into the toast queue.
// { immediate: true } catches messages that arrive on the initial page load.
watch(
    () => page.props.flash,
    flash => {
        if (flash?.message) {
            addToast(flash.message, (flash.type as ToastType) ?? "info", 7000);
        }
    },
    { immediate: true }
);
// map toast types to icon names
function iconName(type: ToastType): string {
    switch (type) {
        case "success":
            return "check";
        case "warning":
            return "warning";
        case "error":
            return "error";
        case "info":
        default:
            return "info";
    }
}
</script>

<template>
    <Teleport to="body">
        <div
            class="toast-container"
            role="region"
            :aria-label="t('toast.region')"
            aria-live="polite"
            aria-atomic="false"
        >
            <TransitionGroup name="toast">
                <div
                    v-for="toast in activeToasts"
                    :key="toast.id"
                    class="toast-container__item"
                    :class="`toast-container__item--${toast.type}`"
                    :style="toast.duration > 0 ? { '--toast-duration': `${toast.duration}ms` } : {}"
                    role="alert"
                >
                    <icon :name="iconName(toast.type)" />
                    <span>{{ toast.message }}</span>
                    <button class="btn-close" :aria-label="t('toast.close')" @click="removeToast(toast.id)">
                        <icon name="close" />
                    </button>
                    <div v-if="toast.duration > 0" class="toast-container__progress" />
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<style lang="scss" scoped>
/**
 * styles for this component are located in
 * @/styles/components/_toast.scss
 */
</style>
