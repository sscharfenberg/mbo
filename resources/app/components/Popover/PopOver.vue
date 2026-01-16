<script setup lang="ts">
import AppIcon from "Components/AppIcon/AppIcon.vue";
import { ref } from "vue";
const props = defineProps({
    icon: {
        type: String,
        required: true
    },
    label: String,
    ariaLabel: String,
    reference: {
        type: String,
        default: Math.random().toString(36).substring(2)
    }
});
const reference = ref("--" + props.reference);
</script>

<template>
    <div class="popover">
        <button :popovertarget="props.reference" :aria-label="ariaLabel || `Menu öffnen`" class="popover-button">
            <app-icon :name="icon" />
        </button>
        <dialog :id="props.reference" popover class="popover-content">
            <slot />
        </dialog>
    </div>
</template>

<style lang="scss" scoped>
// styles are in @/styles/components/popover
// we are duplicating v-binds here so there are no build errors due to
// "unused" vars.
.popover-button {
    anchor-name: v-bind(reference);
}

.popover-content {
    position-anchor: v-bind(reference);
}
</style>
