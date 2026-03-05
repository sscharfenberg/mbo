<script setup lang="ts">
import { useStickyNav } from "Composables/useStickyNav";

export type StickyNavItem = {
    id: string;
    label: string;
};

const props = defineProps<{
    items: StickyNavItem[];
}>();

const { sentinel, isStuck, activeSection } = useStickyNav(props.items.map(i => i.id));
</script>

<template>
    <div ref="sentinel" aria-hidden="true" class="sticky-nav__sentinel" />
    <nav class="sticky-nav" :class="{ 'sticky-nav--sticky': isStuck }">
        <a
            v-for="item in items"
            :key="item.id"
            :href="`#${item.id}`"
            :class="{ active: activeSection === item.id }"
            >{{ item.label }}</a
        >
    </nav>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@use "Abstracts/timings" as ti;

.sticky-nav__sentinel {
    height: 0;

    pointer-events: none;
}

.sticky-nav {
    display: flex;
    position: sticky;
    top: 0;
    z-index: 10;
    flex-wrap: wrap;

    padding: map.get(s.$main, "sticky-nav", "padding");
    margin-bottom: 1lh;
    gap: 1ch;

    background-color: map.get(c.$main, "sticky-nav", "background");
    backdrop-filter: blur(12px);
    color: map.get(c.$main, "sticky-nav", "surface");
    border-radius: map.get(s.$main, "sticky-nav", "radius");

    &::before {
        position: absolute;
        inset: 0;
        z-index: -1;

        border: map.get(s.$main, "sticky-nav", "border") solid transparent;

        background: linear-gradient(
                to bottom right,
                map.get(c.$main, "sticky-nav", "border-from"),
                map.get(c.$main, "sticky-nav", "border-to")
            )
            border-box;

        border-radius: inherit;
        mask:
            linear-gradient(black, black) border-box,
            linear-gradient(black, black) padding-box;
        mask-composite: subtract;

        content: "";
    }

    &--sticky {
        background-color: map.get(c.$main, "sticky-nav", "background-sticky");

        border-top-left-radius: 0;
        border-top-right-radius: 0;

        &::before {
            border-top-width: 0;

            background: map.get(c.$main, "sticky-nav", "border-sticky");
        }
    }

    > a {
        padding: map.get(s.$main, "sticky-nav", "link-padding");

        background-color: map.get(c.$main, "sticky-nav", "link-background");
        color: map.get(c.$main, "sticky-nav", "link-surface");
        border-radius: map.get(s.$main, "sticky-nav", "link-radius");

        text-decoration: none;

        transition:
            background-color map.get(ti.$timings, "fast") linear,
            color map.get(ti.$timings, "fast") linear;

        &:hover {
            background-color: map.get(c.$main, "sticky-nav", "link-background-hover");
            color: map.get(c.$main, "sticky-nav", "link-surface-hover");
        }

        &.active {
            background-color: map.get(c.$main, "sticky-nav", "link-background-active");
            color: map.get(c.$main, "sticky-nav", "link-surface-active");
        }
    }
}
</style>