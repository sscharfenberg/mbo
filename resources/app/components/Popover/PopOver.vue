<script setup lang="ts">
import { ref } from "vue";
import Icon from "Components/UI/Icon.vue";
const props = withDefaults(
    defineProps<{
        icon: string;
        label?: string;
        ariaLabel?: string;
        classString?: string;
        reference?: string;
        width?: string;
    }>(),
    {
        reference: () => Math.random().toString(36).substring(2),
        width: "25ch"
    }
);
const reference = ref("--" + props.reference);
</script>

<template>
    <div class="popover">
        <button
            :popovertarget="props.reference"
            :aria-label="ariaLabel || `Menu öffnen`"
            class="popover-button"
            :class="classString"
        >
            <icon :name="icon" />
            {{ label }}
        </button>
        <dialog :id="props.reference" popover class="popover-content">
            <slot />
        </dialog>
    </div>
</template>

<style lang="scss" scoped>
/**
 * styles are in @/styles/components/popover
 * we are duplicating v-binds here so there are no build errors due to
 * "unused" vars.
 */
.popover-button {
    anchor-name: v-bind(reference);
}

.popover-content {
    width: v-bind(width);

    position-anchor: v-bind(reference);
}
</style>
