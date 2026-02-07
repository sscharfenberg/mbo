<script setup lang="ts">
import { usePage } from "@inertiajs/vue3";
import AppHeaderLanguageSwitchItem from "Components/Landmarks/Header/LanguageSwitch/AppHeaderLanguageSwitchItem.vue";
import PopOver from "Components/Popover/PopOver.vue";
import { computed } from "vue";
const page = usePage();
const supportedLocales = computed(() => page.props.supportedLocales);
</script>

<template>
    <pop-over
        icon="language"
        :label="$page.props.locale"
        aria-label="Switch language"
        class-string="popover-button--rounded"
        reference="languageMenu"
        width="10ch"
    >
        <ul class="popover-list">
            <app-header-language-switch-item v-for="locale in supportedLocales" :key="locale" :locale="locale" />
        </ul>
    </pop-over>
</template>
