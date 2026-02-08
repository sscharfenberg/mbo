<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import PopOver from "Components/Popover/PopOver.vue";
import Icon from "Components/Visual/Icon.vue";
import AppHeaderThemeSwitch from "./ThemeSwitch/AppHeaderThemeSwitch.vue";
</script>

<template>
    <pop-over
        icon="account"
        :aria-label="t('header.user.label')"
        class-string="popover-button--rounded"
        reference="userMenu"
    >
        <ul class="popover-list popover-list--short">
            <li>
                <Link class="popover-list-item" href="/register">
                    <icon name="register" :size="1" />
                    {{ $t("header.user.register") }}
                </Link>
            </li>
            <li>
                <a class="popover-list-item" href="/login">
                    <icon name="login" :size="1" />
                    {{ $t("header.user.login") }}
                </a>
            </li>
            <li>
                <a class="popover-list-item" href="https://www.google.com">
                    <icon name="logout" :size="1" />
                    {{ $t("header.user.logout") }}
                </a>
            </li>
            <li>
                <a class="popover-list-item" href="https://www.google.com">
                    <icon name="user-settings" :size="1" />
                    {{ $t("header.user.settings") }}
                </a>
            </li>
            <li><app-header-theme-switch /></li>
        </ul>
    </pop-over>
</template>
