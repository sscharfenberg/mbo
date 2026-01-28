<script setup lang="ts"></script>

<template>
    <main><slot /></main>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/mixins" as m;
@use "Abstracts/sizes" as s;
@use "Abstracts/colors" as c;

main {
    position: relative;

    background-color: map.get(c.$main, "background");
    backdrop-filter: blur(12px);

    @include m.mqset(
        "padding",
        map.get(s.$app, "padding", "base"),
        map.get(s.$app, "padding", "portrait"),
        map.get(s.$app, "padding", "landscape"),
        map.get(s.$app, "padding", "desktop")
    );
    @include m.mqset(
        "margin",
        #{0 map.get(s.$app, "padding", "base")},
        #{0 map.get(s.$app, "padding", "portrait")},
        #{0 map.get(s.$app, "padding", "landscape")},
        #{0 map.get(s.$app, "padding", "desktop")}
    );
    @include m.mqset(
        "border-radius",
        map.get(s.$main, "radius", "base"),
        map.get(s.$main, "radius", "portrait"),
        map.get(s.$main, "radius", "landscape"),
        map.get(s.$main, "radius", "desktop")
    );

    &::before {
        position: absolute;
        inset: 0;
        z-index: -1;

        border: map.get(s.$main, "border") solid transparent;

        background: linear-gradient(to bottom right, map.get(c.$main, "border-from"), map.get(c.$main, "border-to"))
            border-box;

        border-radius: inherit;
        mask:
            linear-gradient(black, black) border-box,
            linear-gradient(black, black) padding-box;
        mask-composite: subtract;

        content: "";
    }
}
</style>
