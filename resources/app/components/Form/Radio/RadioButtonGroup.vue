<script setup lang="ts">
import { computed } from "vue";
import RadioButton from "Components/Form/Radio/RadioButton.vue";
interface RadioButton {
    value: string;
    label: string;
    checked: boolean;
    icon?: string;
}
const props = withDefaults(
    defineProps<{
        name: string;
        radioButtons: RadioButton[];
        layout?: "column" | "row";
    }>(),
    { layout: "column" }
);
const emit = defineEmits(["change"]);
const onChange = (ev: Event) => emit("change", ev);
const classList = computed(() => {
    const classes = ["radio-group"];
    classes.push(`radio-group--${props.layout}`);
    return classes.join(" ");
});
</script>

<template>
    <ul role="list" :class="classList" :aria-label="$t('form.elements.radio-group')">
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
