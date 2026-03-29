<script setup lang="ts">
import { nextTick, ref, useTemplateRef } from "vue";
import Icon from "Components/UI/Icon.vue";
const props = withDefaults(
    defineProps<{
        /** Whether the collapsible starts in the open state. */
        initialOpen?: boolean;
        /** Slide animation duration in milliseconds. */
        duration?: number;
    }>(),
    {
        initialOpen: false,
        duration: 300
    }
);
/** Tracks whether the body is currently visible. Initialized from the `initialOpen` prop. */
const isOpen = ref(props.initialOpen);
/** Tracks the intended open/closed state — toggled immediately on click for instant visual feedback (e.g. chevron rotation). */
const active = ref(props.initialOpen);
/** Guards against double-clicks while a transition is in progress. */
const animating = ref(false);
/** Template ref to the body wrapper element used for height measurement and animation. */
const body = useTemplateRef<HTMLElement>("body");

/**
 * Toggle the collapsible open or closed.
 * No-ops if a transition is already running or the body element is missing.
 */
function toggle() {
    if (animating.value || !body.value) return;
    animating.value = true;
    active.value = !active.value;

    if (isOpen.value) {
        collapse();
    } else {
        expand();
    }
}

/**
 * Listen for the `height` transition ending on the given element, ignoring
 * bubbled events from children and transitions on other properties.
 * Automatically removes the listener after it fires once.
 *
 * @param el - The element whose transition to observe.
 * @param callback - Invoked once the height transition completes.
 */
function onTransitionEnd(el: HTMLElement, callback: () => void) {
    const handler = (e: TransitionEvent) => {
        if (e.target !== el || e.propertyName !== "height") return;
        el.removeEventListener("transitionend", handler);
        callback();
    };
    el.addEventListener("transitionend", handler);
}

/**
 * Animate the body open. Sets `isOpen` first so Vue renders the element,
 * then waits a tick to measure `scrollHeight` and transitions from `0` to
 * the measured value. Cleans up inline styles after the transition ends
 * so the element reflows naturally if its content changes while open.
 */
function expand() {
    const el = body.value!;
    isOpen.value = true;
    nextTick(() => {
        el.style.height = "0";
        el.style.overflow = "hidden";
        const targetHeight = el.scrollHeight;
        requestAnimationFrame(() => {
            el.style.height = `${targetHeight}px`;
            onTransitionEnd(el, () => {
                el.style.height = "";
                el.style.overflow = "";
                animating.value = false;
            });
        });
    });
}

/**
 * Animate the body closed by snapshotting the current `scrollHeight`,
 * then transitioning to `0`. Sets `isOpen` to false after the transition
 * so Vue hides the element via the reactive style binding.
 */
function collapse() {
    const el = body.value!;
    el.style.height = `${el.scrollHeight}px`;
    el.style.overflow = "hidden";
    requestAnimationFrame(() => {
        el.style.height = "0";
        onTransitionEnd(el, () => {
            el.style.height = "";
            el.style.overflow = "";
            isOpen.value = false;
            animating.value = false;
        });
    });
}
</script>

<template>
    <div class="collapsible">
        <button type="button" class="collapsible__head" :aria-expanded="active" @click="toggle">
            <slot name="head" :is-open="active" />
            <icon name="chevron" />
        </button>
        <div ref="body" class="collapsible__body" :style="{ display: isOpen ? 'block' : 'none' }">
            <div class="collapsible__body-inner">
                <slot name="body" />
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@use "Abstracts/timings" as ti;

.collapsible {
    border: map.get(s.$components, "accordion", "border") solid map.get(c.$components, "accordion", "border");

    background-color: map.get(c.$components, "accordion", "background");
    color: map.get(c.$components, "accordion", "surface");
    border-radius: map.get(s.$components, "accordion", "radius");

    &__head {
        display: flex;
        align-items: center;
        justify-content: space-between;

        width: 100%;
        padding: map.get(s.$components, "accordion", "padding");
        border: 0;

        background-color: transparent;
        border-radius: inherit;

        cursor: pointer;

        transition:
            border-bottom-right-radius map.get(ti.$timings, "quick") ease,
            border-bottom-left-radius map.get(ti.$timings, "quick") ease;

        .icon {
            transition: transform map.get(ti.$timings, "quick") ease;
        }

        &[aria-expanded="true"] {
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;

            .icon {
                transform: rotate(180deg);
            }
        }

        &:hover {
            background-color: map.get(c.$components, "accordion", "background-hover");
        }
    }

    &__body {
        // background-color: map.get(c.$components, "accordion", "background");
        color: map.get(c.$components, "accordion", "surface");
        border-radius: inherit;

        transition: height map.get(ti.$timings, "quick") ease-in-out;
    }

    &__body-inner {
        padding: map.get(s.$components, "accordion", "padding");
    }
}
</style>
