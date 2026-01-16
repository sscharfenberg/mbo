<script setup lang="ts">
import AppHeaderThemeSwitchItem from "Components/Landmarks/Header/ThemeSwitch/AppHeaderThemeSwitchItem.vue";
import { computed, onMounted } from "vue";
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
    { value: "dark", label: "Dunkel", icon: "dark" },
    { value: "light", label: "Hell", icon: "light" },
    { value: "light dark", label: "System", icon: "system" }
];
onMounted(() => {
    if (colorScheme.getAttribute("content") !== theme.value) updateMeta(theme.value);
});
</script>

<template>
    <div class="theme-switch__list">
        <app-header-theme-switch-item
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
