<script setup lang="ts">
import { ref } from "vue";
const props = withDefaults(
    defineProps<{
        /** HTML `id` and `name` for the hidden input — auto-generated if omitted. */
        refId: string;
        /** Initial checked state on mount. */
        checkedInitially?: boolean;
        /** Whether the checkbox is disabled (not currently wired to the template). */
        disabled?: boolean;
        /** Visible label text (hidden off-screen, used for accessibility). */
        label?: string;
        /** Form value submitted when checked. */
        value?: string;
    }>(),
    {
        refId: () => Math.random().toString(36).substring(2),
        checkedInitially: false,
        value: "true"
    }
);
/** Local checked state — initialised from `checkedInitially` and updated on user interaction. */
const checkboxStatus = ref(props.checkedInitially);
/** @emits change — Fired with the new boolean checked state whenever the user toggles the checkbox. */
const emit = defineEmits<{
    change: [checked: boolean];
}>();
/** Syncs local state with the native input and emits the new checked value to the parent. */
const onCheckboxChange = (event: Event) => {
    checkboxStatus.value = (event.target as HTMLInputElement).checked;
    emit("change", checkboxStatus.value);
};
</script>

<template>
    <div class="wrapper">
        <input
            :id="refId"
            type="checkbox"
            :name="refId"
            @change="onCheckboxChange"
            :value="value"
            :checked="checkboxStatus"
        />
        <label :for="refId">{{ label }}</label>
    </div>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@use "Abstracts/timings" as ti;

input {
    visibility: hidden;

    width: 0;
    height: 0;

    &:checked + label {
        background: map.get(c.$form, "checkbox", "background-checked");

        &::after {
            background: map.get(c.$form, "checkbox", "surface-checked");
        }
    }

    &:checked + label::after {
        left: calc(100% - #{map.get(s.$form, "checkbox", "border")});

        transform: translateX(-100%);
    }
}

label {
    display: block;
    position: relative;

    width: map.get(s.$form, "checkbox", "size") * 2;
    height: map.get(s.$form, "checkbox", "size");

    background-color: map.get(c.$form, "checkbox", "background");
    border-radius: 90dvh;

    text-indent: -9999px;

    cursor: pointer;

    transition: map.get(ti.$timings, "quick");

    &::after {
        position: absolute;
        top: #{map.get(s.$form, "checkbox", "border")};
        left: #{map.get(s.$form, "checkbox", "border")};

        width: map.get(s.$form, "checkbox", "size") - 2 * map.get(s.$form, "checkbox", "border");
        height: map.get(s.$form, "checkbox", "size") - 2 * map.get(s.$form, "checkbox", "border");

        background: map.get(c.$form, "checkbox", "surface");
        border-radius: 90dvh;

        content: "";

        transition: map.get(ti.$timings, "quick");
    }
}

label:active::after {
    width: map.get(s.$form, "checkbox", "size") * 1.25;
}

.wrapper {
    display: flex;

    padding: 0.95ex 0;
}
</style>
