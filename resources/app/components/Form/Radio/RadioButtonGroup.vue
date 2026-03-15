<script setup lang="ts">
import { computed } from "vue";
import RadioButton from "Components/Form/Radio/RadioButton.vue";
/** Describes a single radio option within the group. */
interface RadioButton {
    /** Form value submitted when this option is selected. */
    value: string;
    /** i18n key resolved via `$t()` for the visible label. */
    label: string;
    /** Whether this option is currently selected. */
    checked: boolean;
    /** Optional icon name rendered alongside the label. */
    icon?: string;
}
const props = withDefaults(
    defineProps<{
        /** Shared `name` attribute for all radio inputs in this group. */
        name: string;
        /** The list of radio options to render. */
        radioButtons: RadioButton[];
        /** Visual layout direction — `"column"` stacks vertically, `"row"` lays out horizontally. */
        layout?: "column" | "row";
    }>(),
    { layout: "column" }
);
/** @emits change — Bubbles the native change event when the user selects a different option. */
const emit = defineEmits(["change"]);
/** Forwards the native change event from any child RadioButton to the parent. */
const onChange = (ev: Event) => emit("change", ev);
/** BEM class list combining the base block with the layout modifier. */
const classList = computed(() => {
    const classes = ["radio-group"];
    classes.push(`radio-group--${props.layout}`);
    return classes.join(" ");
});
</script>

<template>
    <ul role="list" :class="classList" :aria-label="$t('form.elements.radio_group')">
        <li v-for="button in radioButtons" :key="button.value" class="radio-group__item">
            <radio-button
                :value="button.value"
                :name="name"
                :label="$t(button.label)"
                :checked="button.checked"
                :icon="button.icon"
                @change="onChange"
            />
        </li>
    </ul>
</template>
