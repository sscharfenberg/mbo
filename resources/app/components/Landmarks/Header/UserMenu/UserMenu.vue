<script setup lang="ts">
import { Link, router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import Icon from "Components/UI/Icon.vue";
import PopOver from "Components/UI/PopOver.vue";
import CurrencySwitch from "./CurrencySwitch/CurrencySwitch.vue";
import ThemeSwitch from "./ThemeSwitch/ThemeSwitch.vue";
const page = usePage();
/** The authenticated user object, or `null`/`undefined` when logged out — controls which menu items are visible. */
const user = computed(() => page.props.auth.user);
/** Feature flags from the backend (e.g. `registration`, `resetPasswords`) gating guest-only links. */
const features = computed(() => page.props.features);
/**
 * Handles logout by flushing all pending Inertia requests and closing the popover.
 * The actual POST to `/logout` is handled by the Inertia `<Link>` element.
 */
const handleLogout = () => {
    router.flushAll();
    closePopover();
};
/** Programmatically hides the user menu popover by its DOM id. */
const closePopover = () => {
    const dialog = document.getElementById("userMenu");
    if (dialog !== null) dialog.hidePopover();
};
/** Trigger button classes — adds `--active` highlight when a user is logged in. */
const buttonClassList = computed(() => {
    const classes = ["popover-button--rounded"];
    if (user.value) {
        classes.push("popover-button--active");
    }
    return classes.join(" ");
});
</script>

<template>
    <pop-over icon="account" :aria-label="$t('header.user.label')" :class-string="buttonClassList" reference="userMenu">
        <ul class="popover-list popover-list--short">
            <li v-if="!user">
                <Link class="popover-list-item" href="/login" @click="closePopover">
                    <icon name="login" :size="1" />
                    {{ $t("pages.login.link") }}
                </Link>
            </li>
            <li v-if="!user && features.resetPasswords">
                <Link class="popover-list-item" href="/forgot" @click="closePopover">
                    <icon name="key" :size="1" />
                    {{ $t("pages.forgot.link") }}
                </Link>
            </li>
            <li v-if="!user && features.registration">
                <Link class="popover-list-item" href="/register" @click="closePopover">
                    <icon name="register" :size="1" />
                    {{ $t("pages.register.link") }}
                </Link>
            </li>
            <li v-if="user">
                <Link class="popover-list-item" href="/dashboard" @click="closePopover">
                    <icon name="user-settings" :size="1" />
                    {{ $t("pages.dashboard.link") }}
                </Link>
            </li>
            <li v-if="user">
                <Link class="popover-list-item" href="/logout" method="post" @click="handleLogout">
                    <icon name="logout" :size="1" />
                    {{ $t("header.user.logout") }}
                </Link>
            </li>
            <li><theme-switch /></li>
            <li v-if="user"><currency-switch @close="closePopover" /></li>
        </ul>
    </pop-over>
</template>
