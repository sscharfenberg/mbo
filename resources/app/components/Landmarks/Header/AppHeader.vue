<script setup lang="ts">
import AppHeaderLogo from "./AppHeaderLogo.vue";
import AppHeaderMenu from "./AppHeaderMenu.vue";
import AppHeaderTitle from "./AppHeaderTitle.vue";
</script>

<template>
    <header class="app-header">
        <app-header-logo />
        <app-header-title />
        <app-header-menu />
    </header>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/mixins" as m;
@use "Abstracts/sizes" as s;

.app-header {
    display: flex;
    align-items: center;
    justify-content: flex-start;

    gap: 1ch;

    @include m.mq("portrait") {
        gap: 1.5ch;
    }

    @include m.mq("landscape") {
        gap: 2ch;
    }

    @include m.mqset(
        "padding",
        map.get(s.$header, "padding", "base"),
        map.get(s.$header, "padding", "portrait"),
        map.get(s.$header, "padding", "landscape"),
        map.get(s.$header, "padding", "desktop")
    );
    @include m.mqset(
        "gap",
        #{map.get(s.$header, "gap", "base")},
        #{map.get(s.$header, "gap", "portrait")},
        #{map.get(s.$header, "gap", "landscape")},
        #{map.get(s.$header, "gap", "desktop")}
    );
}
</style>
