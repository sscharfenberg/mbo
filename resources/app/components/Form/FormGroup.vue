<script setup lang="ts">
import { useId } from "vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
withDefaults(
    defineProps<{
        /** HTML `for` attribute linking the label to its input. */
        forId?: string;
        /** Visible label text rendered before the input. When empty, the label row is still rendered for layout. */
        label?: string;
        /** Validation error message shown below the input when `invalid` is true. */
        error?: string;
        /** When true, renders the error message and marks the field visually as invalid. */
        invalid?: boolean;
        /** Shows a loading spinner beside the input (e.g. during async server-side validation). */
        validating?: boolean;
        /** Shows a check-mark indicator anchored to the input when validation has passed. */
        validated?: boolean;
        /** Icon name rendered as a static addon to the left of the input. */
        addonIcon?: string;
        /** When true, displays the required icon next to the label. */
        required?: boolean;
    }>(),
    {
        error: "",
        invalid: false,
        validating: false,
        validated: false,
        required: false
    }
);
/**
 * Unique CSS anchor name for this instance so each form-group's valid
 * indicator is anchored to its own input, not to a sibling's.
 * `useId()` may contain colons (SSR mode), so non-CSS-ident characters are stripped.
 */
const anchorName = `--fgf-${useId().replace(/[^a-z0-9_-]/gi, "")}`;
</script>

<template>
    <div class="form-group">
        <label v-if="label?.length" :for="forId">
            <span>{{ label }}:</span>
            <span v-if="required" class="form-group__icon"><icon name="required" /></span>
        </label>
        <span v-else class="label"
            ><span v-if="required" class="form-group__icon"><icon name="required" /></span
        ></span>
        <div class="form-group__input">
            <div class="form-group__slot">
                <div v-if="addonIcon?.length" class="form-group__addon" aria-hidden="true">
                    <icon :name="addonIcon" />
                </div>
                <div v-if="!addonIcon && $slots.addon">
                    <slot name="addon" />
                </div>
                <div class="form-group__field" :style="`anchor-name: ${anchorName}`">
                    <slot />
                </div>
                <loading-spinner v-if="validating" class="form-group--validating colored" :size="1.5" />
                <div v-if="$slots.button" class="form-group__button">
                    <slot name="button" />
                </div>
                <div
                    v-if="!validating && validated"
                    class="form-group--valid"
                    :style="`position-anchor: ${anchorName}`"
                    aria-label="This field is valid."
                >
                    <icon name="check" :size="1" />
                </div>
            </div>
            <div v-if="$slots.text" class="form-group__text">
                <slot name="text" />
            </div>
            <div v-if="invalid && error.length" class="form-group__error">
                <icon name="error" />
                {{ error }}
            </div>
        </div>
    </div>
</template>
