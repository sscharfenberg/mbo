<script setup lang="ts">
import type { RequestPayload } from "@inertiajs/core";
import { Link } from "@inertiajs/vue3";
import Icon from "Components/UI/Icon.vue";
withDefaults(
    defineProps<{
        href: string;
        method?: "get" | "post" | "put" | "patch" | "delete";
        data?: RequestPayload;
        external?: boolean;
        icon?: string;
    }>(),
    {
        method: "get",
        external: false
    }
);
</script>

<template>
    <Link v-if="!external" class="text-link" :href="href" :method="method" :data="data">
        <slot />
    </Link>
    <a v-else :href="href" target="_blank">
        <icon v-if="icon?.length" :name="icon" :size="1" />
        <slot />
    </a>
</template>

<style scoped lang="scss">
@use "sass:map"; // https://sass-lang.com/documentation/modules/map
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@use "Abstracts/timings" as ti;

.text-link {
    color: map.get(c.$app, "textlink-surface");

    text-decoration-color: map.get(c.$app, "textlink-decoration");
    text-decoration-style: solid;
    text-decoration-thickness: map.get(s.$app, "textlink-underline-thickness");
    text-underline-offset: map.get(s.$app, "textlink-underline-offset");

    transition:
        color map.get(ti.$timings, "fast") linear,
        text-decoration-color map.get(ti.$timings, "fast") linear;

    &:hover {
        color: map.get(c.$app, "textlink-surface-hover");

        text-decoration-color: map.get(c.$app, "textlink-decoration-hover");
    }

    > .icon {
        margin-right: 0.5ch;
    }
}
</style>
