<script setup lang="ts">
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import PopOver from "Components/Popover/PopOver.vue";
import LanguageMenuItem from "./LanguageMenuItem.vue";
const page = usePage();
const { locale } = useI18n();
const supportedLocales = computed(() => page.props.supportedLocales);
const onClose = () => {
    console.log("close");
    const dialog = document.getElementById("languageMenu");
    if (dialog !== null) dialog.hidePopover();
};
</script>

<template>
    <pop-over
        icon="language"
        :label="locale"
        aria-label="Switch language"
        class-string="popover-button--rounded"
        reference="languageMenu"
        width="10ch"
    >
        <ul class="popover-list">
            <language-menu-item v-for="loc in supportedLocales" :key="loc" :locale="loc" @close="onClose" />
        </ul>
    </pop-over>
</template>
