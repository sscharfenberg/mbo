<script setup lang="ts">
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import { useStickyNav } from "Composables/useStickyNav";
export type StickyNavItem = {
    id: string;
    label: string;
};
const { t } = useI18n();
const props = defineProps<{
    items: StickyNavItem[];
    label?: string;
}>();
const navLabel = computed(() => props.label ?? t("sticky_nav.region"));
const { sentinel, isStuck, activeSection } = useStickyNav(props.items.map(i => i.id));
</script>

<template>
    <div ref="sentinel" aria-hidden="true" class="sticky-nav__sentinel" />
    <nav class="sticky-nav" :class="{ 'sticky-nav--sticky': isStuck }" :aria-label="navLabel">
        <a v-for="item in items" :key="item.id" :href="`#${item.id}`" :class="{ active: activeSection === item.id }">{{
            item.label
        }}</a>
    </nav>
</template>

<style lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@use "Abstracts/timings" as ti;
@use "Abstracts/z-indexes" as z;

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

    padding: map.get(s.$components, "sticky-nav", "padding");
    margin-bottom: 1lh;
    gap: 1ch;

    background-color: map.get(c.$components, "sticky-nav", "background");
    backdrop-filter: blur(12px);
    color: map.get(c.$components, "sticky-nav", "surface");
    border-radius: map.get(s.$components, "sticky-nav", "radius");

    &::before {
        position: absolute;
        inset: 0;
        z-index: map.get(z.$index, "background");

        border: map.get(s.$components, "sticky-nav", "border") solid transparent;

        background: linear-gradient(
                to bottom right,
                map.get(c.$components, "sticky-nav", "border-from"),
                map.get(c.$components, "sticky-nav", "border-to")
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
        background-color: map.get(c.$components, "sticky-nav", "background-sticky");

        border-top-left-radius: 0;
        border-top-right-radius: 0;

        &::before {
            border-top-width: 0;

            background: map.get(c.$components, "sticky-nav", "border-sticky");
        }
    }

    > a {
        padding: map.get(s.$components, "sticky-nav", "link-padding");

        background-color: map.get(c.$components, "sticky-nav", "link-background");
        color: map.get(c.$components, "sticky-nav", "link-surface");
        border-radius: map.get(s.$components, "sticky-nav", "link-radius");

        text-decoration: none;

        transition:
            background-color map.get(ti.$timings, "fast") linear,
            color map.get(ti.$timings, "fast") linear;

        &:hover {
            background-color: map.get(c.$components, "sticky-nav", "link-background-hover");
            color: map.get(c.$components, "sticky-nav", "link-surface-hover");
        }

        &.active {
            background-color: map.get(c.$components, "sticky-nav", "link-background-active");
            color: map.get(c.$components, "sticky-nav", "link-surface-active");
        }
    }
}
</style>
