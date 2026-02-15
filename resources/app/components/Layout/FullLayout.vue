<script setup lang="ts">
import AppFooter from "Components/Landmarks/Footer/AppFooter.vue";
import AppHeader from "Components/Landmarks/Header/AppHeader.vue";
import AppMain from "Components/Landmarks/Main/AppMain.vue";
import FlashMessage from "Components/Visual/FlashMessage.vue";
</script>

<template>
    <flash-message />
    <app-header />
    <app-main><slot /></app-main>
    <app-footer />
</template>
