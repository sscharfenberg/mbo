<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import PopOver from "Components/Popover/PopOver.vue";
import Icon from "Components/Visual/Icon.vue";
import AppHeaderLanguageSwitch from "./LanguageSwitch/AppHeaderLanguageSwitch.vue";
import AppHeaderThemeSwitch from "./ThemeSwitch/AppHeaderThemeSwitch.vue";
</script>

<template>
    Auth: {{ $page.props.auth }}
    <nav aria-label="User Navigation">
        <app-header-language-switch />
        <pop-over
            icon="account"
            aria-label="Navigation related to your user account"
            class-string="popover-button--rounded"
            reference="userMenu"
        >
            <ul class="popover-list popover-list--short">
                <li>
                    <Link class="popover-list-item" href="/register"
                        ><icon name="register" :size="1" /> Registration</Link
                    >
                </li>
                <li>
                    <a class="popover-list-item" href="/login">
                        <icon name="login" :size="1" />
                        Login
                    </a>
                </li>
                <li>
                    <a class="popover-list-item" href="https://www.google.com">
                        <icon name="user-settings" :size="1" />
                        Settings
                    </a>
                </li>
                <li>
                    <a class="popover-list-item" href="https://www.google.com">
                        <icon name="logout" :size="1" />
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
</style>
