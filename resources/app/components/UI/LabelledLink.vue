<script setup lang="ts">
import type { RequestPayload } from "@inertiajs/core";
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";
import Icon from "Components/UI/Icon.vue";
const props = withDefaults(
    defineProps<{
        href: string;
        method?: "get" | "post" | "put" | "patch" | "delete";
        data?: RequestPayload;
        /** Icon name. Defaults to "external-link" for https links, "mail" for mailto. Pass "" to suppress. */
        icon?: string;
    }>(),
    {
        method: "get"
    }
);
const isExternal = computed(() => props.href.startsWith("https://") || props.href.startsWith("http://"));
const isMailto = computed(() => props.href.startsWith("mailto:"));
const resolvedIcon = computed(() => {
    if (props.icon === "") return undefined;
    if (props.icon) return props.icon;
    if (isExternal.value) return "external-link";
    if (isMailto.value) return "mail";
    return undefined;
});
</script>

<template>
    <Link v-if="!isExternal && !isMailto" class="text-link" :href="href" :method="method" :data="data">
        <icon v-if="resolvedIcon" :name="resolvedIcon" :size="1" />
        <slot />
    </Link>
    <a v-else-if="isExternal" :href="href" target="_blank" rel="noopener nofollow" class="text-link">
        <icon v-if="resolvedIcon" :name="resolvedIcon" :size="1" />
        <slot />
    </a>
    <a v-else :href="href" class="text-link">
        <icon v-if="resolvedIcon" :name="resolvedIcon" :size="1" />
        <slot />
    </a>
</template>

<style scoped lang="scss">
@use "sass:map"; // https://sass-lang.com/documentation/modules/map
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@use "Abstracts/timings" as ti;

.text-link {
    color: map.get(c.$components, "textlink", "surface");

    text-decoration-color: map.get(c.$components, "textlink", "decoration");
    text-decoration-style: solid;
    text-decoration-thickness: map.get(s.$components, "textlink", "underline-thickness");
    text-underline-offset: map.get(s.$components, "textlink", "underline-offset");

    transition:
        color map.get(ti.$timings, "fast") linear,
        text-decoration-color map.get(ti.$timings, "fast") linear;

    &:hover {
        color: map.get(c.$components, "textlink", "surface-hover");

        text-decoration-color: map.get(c.$components, "textlink", "decoration-hover");
    }

    > .icon {
        margin-right: 0.5ch;
    }
}
</style>
