<script setup lang="ts">
import AppFooter from "Components/Landmarks/Footer/AppFooter.vue";
import AppHeader from "Components/Landmarks/Header/AppHeader.vue";
import AppMain from "Components/Landmarks/Main/AppMain.vue";
</script>

<template>
    <app-header />
    <app-main>
        <div class="frosted-glass">
            <slot />
        </div>
    </app-main>
    <app-footer />
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/sizes" as s;

main {
    width: 100%;
    max-width: map.get(s.$main, "max-narrow");
    margin: 0 auto;
}
</style>
