<script setup lang="ts">
import AppIcon from "Components/AppIcon/AppIcon.vue";
import PopOver from "Components/Popover/PopOver.vue";
import AppHeaderThemeSwitch from "./ThemeSwitch/AppHeaderThemeSwitch.vue";
</script>

<template>
    <nav aria-label="User Navigation">
        <pop-over
            icon="account"
            aria-label="Export als Datei zur Verwendung mit anderen Playern"
            reference="exportPlaylist"
        >
            <ul class="popover-list">
                <li>
                    <a class="popover-list-item" href="https://www.google.com">
                        <app-icon name="register" :size="1" />
                        Registration
                    </a>
                </li>
                <li>
                    <a class="popover-list-item" href="https://www.google.com">
                        <app-icon name="login" :size="1" />
                        Login
                    </a>
                </li>
                <li>
                    <a class="popover-list-item" href="https://www.google.com">
                        <app-icon name="user-settings" :size="1" />
                        Settings
                    </a>
                </li>
                <li>
                    <a class="popover-list-item" href="https://www.google.com">
                        <app-icon name="logout" :size="1" />
                        Logout
                    </a>
                </li>
                <li><app-header-theme-switch /></li>
            </ul>
        </pop-over>
    </nav>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;

nav {
    margin-left: auto;
}
</style>
