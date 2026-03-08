<script setup lang="ts">
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import LanguageMenu from "./LanguageMenu/LanguageMenu.vue";
import SiteMenu from "./SiteMenu/SiteMenu.vue";
import UserMenu from "./UserMenu/UserMenu.vue";
const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <site-menu v-if="user" />
    <nav class="user-menus" :aria-label="$t('header.user_nav')">
        <language-menu />
        <user-menu />
    </nav>
</template>

<style scoped lang="scss">
@use "Abstracts/mixins" as m;

.user-menus {
    display: flex;

    margin-left: auto;
    gap: 0.5ch;

    @include m.mq("portrait") {
        gap: 1ch;
    }
}
</style>
