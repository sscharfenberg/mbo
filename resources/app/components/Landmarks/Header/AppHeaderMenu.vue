<script setup lang="ts">
import LanguageMenu from "./LanguageMenu/LanguageMenu.vue";
import UserMenu from "./UserMenu/UserMenu.vue";
</script>

<template>
    <nav :aria-label="$t('header.nav')">
        <language-menu />
        <user-menu />
    </nav>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;

nav {
    display: flex;

    margin-left: auto;
    gap: 1ch;
}
</style>
