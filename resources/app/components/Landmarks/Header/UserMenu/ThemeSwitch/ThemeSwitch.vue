<script setup lang="ts">
import { computed, onMounted } from "vue";
import ThemeSwitchItem from "./ThemeSwitchItem.vue";
/** Reference to the `<meta name="color-scheme">` tag that controls the browser's color scheme rendering. */
const colorScheme = document.querySelector("meta[name='color-scheme']");
if (!colorScheme) {
    throw new Error("Meta tag with name='color-scheme' not found");
}
/** Updates the meta tag's `content` attribute to apply the given color scheme immediately. */
const updateMeta = (val: string) => {
    colorScheme.setAttribute("content", val);
};
/**
 * Writable computed for the active theme.
 * - **get**: reads from `localStorage`, falling back to the meta tag value, then `"light dark"` (system default).
 * - **set**: updates both the meta tag (instant visual switch) and `localStorage` (persistence across reloads).
 */
const theme = computed({
    get() {
        return localStorage.getItem("theme") || colorScheme.getAttribute("content") || "light dark";
    },
    set(val) {
        updateMeta(val);
        localStorage.setItem("theme", val);
    }
});
/** Available theme options — `"light dark"` delegates to the OS preference. */
const options = [
    { value: "dark", label: "header.theme.dark", icon: "dark" },
    { value: "light", label: "header.theme.light", icon: "light" },
    { value: "light dark", label: "header.theme.system", icon: "system" }
];
/** Syncs the meta tag with the persisted theme on mount, in case the server-rendered default differs. */
onMounted(() => {
    if (colorScheme.getAttribute("content") !== theme.value) updateMeta(theme.value);
});
</script>

<template>
    <div class="theme-switch__list" :aria-label="$t('header.theme.label')">
        <theme-switch-item
            v-for="option in options"
            :key="option.value"
            :name="option.value"
            :label="option.label"
            :icon="option.icon"
            :selected="theme === option.value"
            @radio="theme = option.value"
        />
    </div>
</template>

<style lang="scss" scoped>
/**
 * styles for this component are located in
 * resources/app/styles/components/_theme-switch.scss
 */
</style>
