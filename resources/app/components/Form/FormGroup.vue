<script setup lang="ts">
import Icon from "Components/Visual/Icon.vue";
import LoadingSpinner from "Components/Visual/LoadingSpinner.vue";

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
        default: false
    },
    validated: {
        type: Boolean,
        default: false
    },
    addonIcon: String
});
</script>

<template>
    <div class="form-group">
        <label v-if="label?.length" :for="forId">{{ label }}</label>
        <span v-else class="label" />
        <div class="form-group__input">
            <div class="form-group__slot">
                <div v-if="addonIcon?.length" class="form-group__addon" aria-hidden="true">
                    <icon :name="addonIcon" />
                </div>
                <slot />
                <loading-spinner v-if="validating" class="form-group--validating colored" :size="1.5" />
                <div v-if="!validating && validated" class="form-group--validated" aria-label="This field is valid.">
                    <icon name="check" :size="1" />
                </div>
            </div>
            <div v-if="invalid && error.length" class="form-group__error">
                <icon name="error" />
                {{ error }}
            </div>
        </div>
    </div>
</template>
