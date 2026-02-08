<script setup lang="ts">
import Icon from "Components/Visual/Icon.vue";
import { t } from "Composables/useTranslations";
const props = defineProps({
    name: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    icon: {
        type: String,
        required: true
    },
    selected: {
        type: Boolean,
        default: false
    }
});
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
        :aria-label="t(label)"
    />
    <label :for="'theme' + name.replace(' ', '')" :aria-label="t(label)" class="theme-switch__item">
        <icon :name="icon" />
    </label>
</template>
