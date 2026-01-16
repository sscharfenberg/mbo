<script setup lang="ts">
import AppIcon from "Components/AppIcon/AppIcon.vue";

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
        :id="'theme' + label"
        name="theme"
        type="radio"
        :value="name"
        @change="emit('radio', props.name)"
        :checked="selected"
    />
    <label :for="'theme' + label" :aria-label="label" class="theme-switch__item">
        <app-icon :name="icon" />
    </label>
</template>
