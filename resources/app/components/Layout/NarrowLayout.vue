<script setup lang="ts">
import AppFooter from "Components/Landmarks/Footer/AppFooter.vue";
import AppHeader from "Components/Landmarks/Header/AppHeader.vue";
import AppMain from "Components/Landmarks/Main/AppMain.vue";
import LayoutOverlay from "Components/Layout/LayoutOverlay.vue";
import ToastContainer from "Components/UI/ToastContainer.vue";
import { useNavigation } from "Composables/useNavigation";
const { navigating } = useNavigation();
/**
 * narrow layout does not have a breadcrumb
 * these pages are supposed to sit below the root,
 * so a breadcrumb would only offer a link back to home
 */
</script>

<template>
    <toast-container />
    <app-header />
    <app-main>
        <slot />
        <layout-overlay v-show="navigating" />
    </app-main>
    <app-footer />
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/sizes" as s;

main {
    max-width: map.get(s.$main, "max-narrow");
    margin: 0 auto;
}
</style>
