<script setup lang="ts">
import AppFooter from "Components/Landmarks/Footer/AppFooter.vue";
import AppHeader from "Components/Landmarks/Header/AppHeader.vue";
</script>

<template>
    <app-header />
    <slot />
    <app-footer />
</template>
