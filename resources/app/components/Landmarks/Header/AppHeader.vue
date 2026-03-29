<script setup lang="ts">
import AppHeaderLogo from "./AppHeaderLogo.vue";
import AppHeaderMenu from "./AppHeaderMenu.vue";
import AppHeaderTitle from "./AppHeaderTitle.vue";
</script>

<template>
    <header class="app-header">
        <section class="inner">
            <app-header-logo />
            <app-header-title />
            <app-header-menu />
        </section>
    </header>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/mixins" as m;
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.app-header {
    @include m.mqset(
        "padding",
        map.get(s.$components, "header", "padding", "base"),
        map.get(s.$components, "header", "padding", "portrait"),
        map.get(s.$components, "header", "padding", "landscape"),
        map.get(s.$components, "header", "padding", "desktop")
    );

    .inner {
        display: flex;
        position: relative;
        align-items: center;
        justify-content: flex-start;

        max-width: map.get(s.$app, "max");
        padding: 0.5ex 2ch;
        margin: 0 auto;
        gap: 1ch;

        background-color: map.get(c.$components, "header", "background");
        backdrop-filter: blur(12px);

        @include m.mq("portrait") {
            gap: 1.5ch;
        }

        @include m.mq("landscape") {
            gap: 2ch;
        }

        @include m.mqset(
            "border-radius",
            #{0 0 map.get(s.$components, "header", "radius", "base") map.get(s.$components, "header", "radius", "base")},
            #{0 0 map.get(s.$components, "header", "radius", "portrait")
                map.get(s.$components, "header", "radius", "portrait")},
            #{0 0 map.get(s.$components, "header", "radius", "landscape")
                map.get(s.$components, "header", "radius", "landscape")},
            #{0 0 map.get(s.$components, "header", "radius", "desktop")
                map.get(s.$components, "header", "radius", "desktop")}
        );

        &::before {
            position: absolute;
            inset: 0;
            z-index: -1;

            border: map.get(s.$components, "header", "border") solid transparent;
            border-top-width: 0;

            background: linear-gradient(
                    to bottom right,
                    map.get(c.$components, "header", "border-from"),
                    map.get(c.$components, "header", "border-to")
                )
                border-box;

            border-radius: inherit;
            mask:
                linear-gradient(black, black) border-box,
                linear-gradient(black, black) padding-box;
            mask-composite: subtract;

            content: "";
        }
    }
}
</style>
