<script setup lang="ts">
import { computed } from "vue";
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
</script>

<template>
    <div class="password-strength">
        <div
            class="password-strength__meter"
            role="meter"
            aria-valuemin="0"
            aria-valuemax="4"
            :aria-valuenow="score"
            aria-label="Password Strength"
        >
            <div class="password-strength__bar" />
        </div>
        <div v-if="score >= 3" class="form-group--valid"><icon name="check" :size="1" /></div>
        <div v-if="score < 3" class="form-group--invalid"><icon name="warning" :size="1" /></div>
    </div>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.password-strength {
    display: flex;
    align-items: center;

    padding: map.get(s.$form, "password-strength", "padding");
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

    .form-group--valid,
    .form-group--invalid {
        position: relative;

        top: unset;
        right: unset;
    }
}
</style>
