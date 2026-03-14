<script setup lang="ts">
import AppFooter from "Components/Landmarks/Footer/AppFooter.vue";
import AppHeader from "Components/Landmarks/Header/AppHeader.vue";
import AppMain from "Components/Landmarks/Main/AppMain.vue";
import LayoutOverlay from "Components/Layout/LayoutOverlay.vue";
import Breadcrumb from "Components/UI/Breadcrumb.vue";
import ToastContainer from "Components/UI/ToastContainer.vue";
import { useNavigation } from "Composables/useNavigation";
const { navigating } = useNavigation();
</script>

<template>
    <toast-container />
    <app-header />
    <app-main>
        <breadcrumb v-if="!navigating" />
        <slot />
        <layout-overlay v-if="navigating" />
    </app-main>
    <app-footer />
</template>
