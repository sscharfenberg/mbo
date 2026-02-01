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
