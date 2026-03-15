<script setup lang="ts">
import Icon from "Components/UI/Icon.vue";
const props = withDefaults(
    defineProps<{
        /** Theme value used as the radio `value` and emitted on selection (e.g. `"dark"`, `"light"`, `"light dark"`). */
        name: string;
        /** i18n key for the accessible label on the radio input and its visual label. */
        label: string;
        /** Icon name rendered inside the label (e.g. `"dark"`, `"light"`, `"system"`). */
        icon: string;
        /** Whether this option is currently active. */
        selected?: boolean;
    }>(),
    { selected: false }
);
/** @emits radio — Fired with the theme `name` when the user selects this option. */
const emit = defineEmits(["radio"]);
</script>

<template>
    <input
        :id="'theme' + name.replace(' ', '')"
        name="theme"
        type="radio"
        :value="name"
        @change="emit('radio', props.name)"
        :checked="selected"
        :aria-label="$t(label)"
    />
    <label :for="'theme' + name.replace(' ', '')" :aria-label="$t(label)" class="theme-switch__item">
        <icon :name="icon" />
    </label>
</template>

<style lang="scss" scoped>
/**
 * styles for this component are located in
 * resources/app/styles/components/_theme-switch.scss
 */
</style>
