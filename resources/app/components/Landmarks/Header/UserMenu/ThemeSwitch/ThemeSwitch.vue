<script setup lang="ts">
import { computed, onMounted } from "vue";
import ThemeSwitchItem from "./ThemeSwitchItem.vue";
const colorScheme = document.querySelector("meta[name='color-scheme']");
if (!colorScheme) {
    throw new Error("Meta tag with name='color-scheme' not found");
}
const updateMeta = (val: string) => {
    colorScheme.setAttribute("content", val);
};
const theme = computed({
    get() {
        return localStorage.getItem("theme") || colorScheme.getAttribute("content") || "light dark";
    },
    set(val) {
        updateMeta(val);
        localStorage.setItem("theme", val);
    }
});
const options = [
    { value: "dark", label: "app.header.theme.dark", icon: "dark" },
    { value: "light", label: "app.header.theme.light", icon: "light" },
    { value: "light dark", label: "app.header.theme.system", icon: "system" }
];
onMounted(() => {
    if (colorScheme.getAttribute("content") !== theme.value) updateMeta(theme.value);
});
</script>

<template>
    <div class="theme-switch__list" :aria-label="$t('app.header.theme.label')">
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
