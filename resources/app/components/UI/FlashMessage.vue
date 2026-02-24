<script setup lang="ts">
import { usePage } from "@inertiajs/vue3";
import { computed, onUnmounted, ref, watch } from "vue";
import Icon from "Components/UI/Icon.vue";
export type Type = "info" | "warning" | "error" | "success";
const classList = computed(() => {
    const list = ["flash-message__dialog"];
    list.push("flash-message__dialog--" + page.props.flash.type);
    return list.join(" ");
});
const page = usePage();
const showFlash = ref(false);
let dismissTimer: ReturnType<typeof setTimeout> | null = null;
const onKeyPress = (ev: KeyboardEvent) => {
    if (ev.key === "Escape") showFlash.value = false;
};
watch(
    () => page.props.flash.message,
    message => {
        if (dismissTimer) clearTimeout(dismissTimer);
        if (message?.length) {
            showFlash.value = true;
            document.addEventListener("keydown", onKeyPress);
            dismissTimer = setTimeout(() => {
                showFlash.value = false;
                document.removeEventListener("keydown", onKeyPress);
            }, 7000);
        }
    },
    { immediate: true }
);
onUnmounted(() => {
    if (dismissTimer) clearTimeout(dismissTimer);
    document.removeEventListener("keydown", onKeyPress);
});
const iconName = computed(() => {
    switch (page.props.flash.type) {
        case "info":
        default:
            return "info";
        case "success":
            return "check";
        case "warning":
            return "warning";
        case "error":
            return "error";
    }
});
</script>

<template>
    <Teleport to="body">
        <Transition name="toast">
            <dialog v-if="showFlash" id="flashMessage" class="flash-message__wrapper" open>
                <section :class="classList">
                    <icon :name="iconName" />
                    <span>{{ page.props.flash.message }}</span>
                    <button class="btn-close" @click="showFlash = false" :aria-label="$t('flash.close')">
                        <icon name="close" />
                    </button>
                </section>
            </dialog>
        </Transition>
    </Teleport>
</template>

<style lang="scss" scoped>
// main styles are found in
// resources/app/styles/components/_flash.scss
@use "sass:map";
@use "Abstracts/timings" as ti;

.toast-enter-from,
.toast-leave-to {
    opacity: 0;

    transform: translateY(-100%);
}

.toast-enter-active,
.toast-leave-active {
    transition:
        opacity map.get(ti.$timings, "fast") ease,
        transform map.get(ti.$timings, "fast") ease;
}

.toast-enter-to {
    opacity: 1;

    transform: translateY(0);
}
</style>
