<script setup lang="ts">
import FormGroup from "Components/Form/FormGroup.vue";
defineProps<{
    /** Finish label values. */
    finishes: string[];
    /** Currently selected finish, controlled by the parent via v-model. */
    modelValue: string;
    /** Validation error message for the finish field. */
    error?: string;
    /** Whether the field is in an invalid state. */
    invalid?: boolean;
}>();
const emit = defineEmits<{
    "update:modelValue": [value: string];
}>();
</script>

<template>
    <form-group :label="$t('form.fields.finish')" :required="true" :error="error" :invalid="invalid">
        <div class="finish__wrapper">
            <span class="finish" v-for="finish in finishes" :key="finish">
                <input
                    type="radio"
                    :id="`finish-${finish}`"
                    name="finish"
                    :checked="finish === modelValue"
                    :value="finish"
                    class="sr-only"
                    @change="emit('update:modelValue', finish)"
                />
                <label :for="`finish-${finish}`">
                    {{ $t("enums.finishes." + finish) }}
                </label>
            </span>
        </div>
    </form-group>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@use "Abstracts/timings" as ti;

.finish {
    &__wrapper {
        display: flex;
        flex-wrap: wrap;

        gap: 1ch;
    }

    label {
        display: flex;
        align-items: center;

        padding: map.get(s.$form, "finish", "padding");
        border: map.get(s.$form, "finish", "border") solid map.get(c.$form, "finish", "border");

        background-color: map.get(c.$form, "finish", "background");
        border-radius: map.get(s.$form, "finish", "radius");

        cursor: pointer;

        transition:
            background-color map.get(ti.$timings, "fast") linear,
            color map.get(ti.$timings, "fast") linear,
            border-color map.get(ti.$timings, "fast") linear;

        &:hover {
            background-color: map.get(c.$form, "finish", "background-hover");
        }
    }
}

input:checked + label {
    background-color: map.get(c.$form, "finish", "background-checked");
    border-color: map.get(c.$form, "finish", "border-checked");
}
</style>
