<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import DashboardPassword from "@/pages/User/Dashboard/DashboardPassword.vue";
import DashboardProfile from "@/pages/User/Dashboard/DashboardProfile.vue";
import TwoFactor from "@/pages/User/Dashboard/TwoFactor/TwoFactor.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import StickyNav from "Components/UI/StickyNav.vue";

const { t } = useI18n();

const navItems = computed(() => [
    { id: "passwordSection", label: t("pages.dashboard.password.link") },
    { id: "profileSection", label: t("pages.dashboard.profile.link") },
    { id: "2faSection", label: t("pages.dashboard.two_factor.link") }
]);
</script>

<template>
    <Head
        ><title>{{ $t("pages.dashboard.title") }}</title></Head
    >
    <headline>
        <icon name="user-settings" :size="3" />
        {{ $t("pages.dashboard.title") }}
    </headline>
    <sticky-nav :items="navItems" />
    <dashboard-password />
    <dashboard-profile />
    <two-factor />
</template>
