<script setup lang="ts">
import { Link, router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import PopOver from "Components/Popover/PopOver.vue";
import Icon from "Components/Visual/Icon.vue";
import ThemeSwitch from "./ThemeSwitch/ThemeSwitch.vue";
const page = usePage();
const user = computed(() => page.props.auth.user);
const handleLogout = () => {
    router.flushAll();
    closePopover();
};
const closePopover = () => {
    const dialog = document.getElementById("userMenu");
    if (dialog !== null) dialog.hidePopover();
};
</script>

<template>
    <pop-over
        icon="account"
        :aria-label="$t('header.user.label')"
        class-string="popover-button--rounded"
        reference="userMenu"
    >
        <ul class="popover-list popover-list--short">
            <li v-if="!user">
                <Link class="popover-list-item" href="/register" @click="closePopover">
                    <icon name="register" :size="1" />
                    {{ $t("pages.register.link") }}
                </Link>
            </li>
            <li v-if="!user">
                <Link class="popover-list-item" href="/login" @click="closePopover">
                    <icon name="login" :size="1" />
                    {{ $t("pages.login.link") }}
                </Link>
            </li>
            <li v-if="user">
                <Link class="popover-list-item" href="/logout" method="post" @click="handleLogout">
                    <icon name="logout" :size="1" />
                    {{ $t("header.user.logout") }}
                </Link>
            </li>
            <li v-if="user">
                <Link class="popover-list-item" href="/dashboard" @click="closePopover">
                    <icon name="user-settings" :size="1" />
                    {{ $t("pages.dashboard.link") }}
                </Link>
            </li>
            <li><theme-switch /></li>
        </ul>
    </pop-over>
</template>
