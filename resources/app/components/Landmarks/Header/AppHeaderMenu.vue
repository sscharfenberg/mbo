<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import AppIcon from "Components/AppIcon/AppIcon.vue";
import PopOver from "Components/Popover/PopOver.vue";
import AppHeaderThemeSwitch from "./ThemeSwitch/AppHeaderThemeSwitch.vue";
</script>

<template>
    <nav aria-label="User Navigation">
        <pop-over
            icon="language"
            label="de"
            aria-label="Switch language"
            class-string="popover-button--rounded"
            reference="languageMenu"
            width="10ch"
        >
            <ul class="popover-list">
                <li>
                    <a class="popover-list-item" href="https://www.google.com"
                        ><img class="flag" src="./de.svg" alt="Deutsche Sprache" /> DE</a
                    >
                </li>
                <li>
                    <a class="popover-list-item" href="https://www.google.com"
                        ><img class="flag" src="./en.svg" alt="English Language" /> EN</a
                    >
                </li>
            </ul>
        </pop-over>
        <pop-over
            icon="account"
            aria-label="Navigation related to your user account"
            class-string="popover-button--rounded"
            reference="userMenu"
        >
            <ul class="popover-list popover-list--short">
                <li>
                    <Link class="popover-list-item" href="/auth/register"
                        ><app-icon name="register" :size="1" /> Registration</Link
                    >
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
    display: flex;

    margin-left: auto;
    gap: 1ch;
}

.flag {
    width: 24px;
    height: 14.4px;
}
</style>
