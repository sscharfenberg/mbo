<script setup lang="ts">
import { computed, useId } from "vue";
import Icon from "Components/UI/Icon.vue";
const props = defineProps({
    score: {
        type: Number,
        required: true
    }
});
const barWidth = computed(() => {
    let pct = (4 - props.score) * 20 + 10;
    if (props.score === 4) pct = 0;
    return `${pct}%`;
});

// Unique CSS anchor name so the valid/invalid indicator anchors to this
// component's meter, not to any sibling's input anchor.
const anchorName = `--psm-${useId().replace(/[^a-z0-9_-]/gi, "")}`;
</script>

<template>
    <div class="password-strength">
        <div
            class="password-strength__meter"
            :style="`anchor-name: ${anchorName}`"
            role="meter"
            aria-valuemin="0"
            aria-valuemax="4"
            :aria-valuenow="score"
            aria-label="Password Strength"
        >
            <div class="password-strength__bar" />
        </div>
        <div v-if="score >= 3" class="form-group--valid" :style="`position-anchor: ${anchorName}`">
            <icon name="check" :size="1" />
        </div>
        <div v-if="score < 3" class="form-group--invalid" :style="`position-anchor: ${anchorName}`">
            <icon name="warning" :size="1" />
        </div>
    </div>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.password-strength {
    display: flex;
    position: relative;
    align-items: center;

    // Right padding reserves space for the valid/invalid icon (always shown).
    // The icon is 20px wide; 1ch gap between meter and icon.
    padding: map.get(s.$form, "password-strength", "padding");
    padding-right: calc(20px + 1.5ch);
    border: map.get(s.$form, "password-strength", "border") solid map.get(c.$form, "password-strength", "border");
    margin-top: 1ex;
    gap: 1ch;

    background-color: map.get(c.$form, "password-strength", "background");
    border-radius: map.get(s.$form, "password-strength", "radius");

    &__meter {
        --bar-width: v-bind(barWidth);

        position: relative;
        flex-grow: 1;

        height: 2ex;
        border: map.get(s.$form, "password-strength", "meter-border") solid
            map.get(c.$form, "password-strength", "border");

        background: map.get(c.$form, "password-strength", "meter-bar");
        border-radius: map.get(s.$form, "password-strength", "meter-radius");
    }

    &__bar {
        position: absolute;
        inset: 0 0 0 auto;

        width: var(--bar-width);
        height: 100%;

        background: #444;
        border-top-right-radius: map.get(s.$form, "password-strength", "meter-radius");
        border-bottom-right-radius: map.get(s.$form, "password-strength", "meter-radius");

        transition: width 500ms linear;
    }

    // Override the global right-overlap positioning: place the icon to the
    // right of the meter (in the reserved padding area) instead of inside it.
    .form-group--valid,
    .form-group--invalid {
        right: unset;
        left: calc(anchor(right) + 0.5ch);
    }

    .icon {
        padding: 0.2ex;

        border-radius: 90dvh;

        &.check {
            background-color: map.get(c.$form, "password-strength", "pass", "background");
            color: map.get(c.$form, "password-strength", "pass", "surface");
        }

        &.warning {
            background-color: map.get(c.$form, "password-strength", "fail", "background");
            color: map.get(c.$form, "password-strength", "fail", "surface");
        }
    }
}
</style>
