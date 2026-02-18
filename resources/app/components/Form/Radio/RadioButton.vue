<script setup lang="ts">
defineProps<{
    value: string;
    name: string;
    label?: string;
    checked: boolean;
}>();
const emit = defineEmits(["change"]);
const onChange = (ev: Event) => emit("change", ev);
</script>

<template>
    <label :for="`${name}_${value}`" class="form-radio">
        <input
            :id="`${name}_${value}`"
            type="radio"
            :name="name"
            :value="value"
            :checked="checked ?? null"
            @change="onChange"
        />
        <span class="form-radio__button" />
        <span v-if="label?.length" class="form-radio__label">{{ label }}</span>
    </label>
</template>
