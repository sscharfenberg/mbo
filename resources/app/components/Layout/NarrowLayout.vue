<script setup lang="ts">
import AppFooter from "Components/Landmarks/Footer/AppFooter.vue";
import AppHeader from "Components/Landmarks/Header/AppHeader.vue";
import AppMain from "Components/Landmarks/Main/AppMain.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
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
        <div v-show="navigating" class="nav-overlay" aria-hidden="true">
            <loading-spinner :size="8" :branded="true" />
        </div>
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
