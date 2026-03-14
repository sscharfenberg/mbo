<script setup lang="ts">
import AppFooter from "Components/Landmarks/Footer/AppFooter.vue";
import AppHeader from "Components/Landmarks/Header/AppHeader.vue";
import AppMain from "Components/Landmarks/Main/AppMain.vue";
import Breadcrumb from "Components/UI/Breadcrumb.vue";
import ToastContainer from "Components/UI/ToastContainer.vue";
</script>

<template>
    <toast-container />
    <app-header />
    <app-main>
        <breadcrumb />
        <slot />
    </app-main>
    <app-footer />
</template>
