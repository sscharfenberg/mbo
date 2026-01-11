<script setup lang="ts">
import AppHeaderLogo from "./AppHeaderLogo.vue";
import AppHeaderMenu from "./AppHeaderMenu.vue";
import AppHeaderTitle from "./AppHeaderTitle.vue";
</script>

<template>
    <header>
        <div class="app-header">
            <app-header-logo />
            <app-header-title />
            <app-header-menu />
        </div>
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

    @include m.mqset(
        "padding",
        #{map.get(s.$header, "padding", "base") 0},
        #{map.get(s.$header, "padding", "portrait") 0},
        #{map.get(s.$header, "padding", "landscape") 0},
        #{map.get(s.$header, "padding", "desktop") 0}
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
