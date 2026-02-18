<script setup lang="ts">
import RadioButton from "Components/Form/Radio/RadioButton.vue";

interface RadioButton {
    value: string;
    label: string;
    checked: boolean;
}
defineProps<{
    name: string;
    radioButtons: RadioButton[];
}>();
// const emit = defineEmits(["change"]);
// const onChange = (ev: Event) => emit("change", ev);
</script>

<template>
    <ul role="list" class="radio-group" aria-label="Verfügbare Optionen">
        <li v-for="button in radioButtons" :key="button.value" class="radio-group__item">
            <radio-button :value="button.value" :name="name" :label="button.label" :checked="button.checked" />
        </li>
    </ul>
</template>
