<script setup lang="ts">
import Icon from "Components/Visual/Icon/Icon.vue";

defineProps({
    forId: String,
    label: String,
    error: {
        type: String,
        default: ""
    },
    invalid: {
        type: Boolean,
        default: false
    },
    validating: {
        type: Boolean,
        default: true
    }
});
</script>

<template>
    <div class="form-group">
        <label v-if="label?.length" :for="forId">{{ label }}</label>
        <span v-else class="label" />
        <div class="input-col">
            <div class="input">
                <slot />
                <div class="validating">Validating.</div>
            </div>
            <div v-if="invalid" class="error">
                <icon name="error" />
                {{ error }}
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/mixins" as m;
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.form-group {
    display: flex;
    flex-flow: column wrap;

    gap: 1ex;

    @include m.mq("landscape") {
        flex-flow: row wrap;

        gap: 2ch;
    }

    label,
    span.label {
        flex-grow: 1;
    }

    .input-col {
        flex: 0 0 100%;

        @include m.mq("landscape") {
            flex: 0 0 65%;
            gap: 2ch;
        }
    }

    .input {
        display: flex;

        > :not(.validating, .btn-primary) {
            flex-grow: 1;
        }
    }

    .error {
        display: flex;
        align-items: flex-start;

        padding: 1ex 1.5ch;
        border: 2px solid map.get(c.$form, "error-border");
        margin-top: 1ex;
        gap: 1ch;

        background-color: map.get(c.$form, "error-background");
        color: map.get(c.$form, "error-surface");
    }
}
</style>
