<script setup lang="ts">
import AppFooter from "Components/Landmarks/Footer/AppFooter.vue";
import AppHeader from "Components/Landmarks/Header/AppHeader.vue";
import AppMain from "Components/Landmarks/Main/AppMain.vue";
import ToastContainer from "Components/UI/ToastContainer.vue";
</script>

<template>
    <toast-container />
    <app-header />
    <app-main><slot /></app-main>
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
