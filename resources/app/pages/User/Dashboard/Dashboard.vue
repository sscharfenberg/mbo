<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import DashboardNav from "@/pages/User/Dashboard/DashboardNav.vue";
import DashboardPassword from "@/pages/User/Dashboard/DashboardPassword.vue";
import DashboardProfile from "@/pages/User/Dashboard/DashboardProfile.vue";
import TwoFactor from "@/pages/User/Dashboard/TwoFactor/TwoFactor.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
</script>

<template>
    <Head
        ><title>{{ $t("pages.dashboard.title") }}</title></Head
    >
    <headline>
        <icon name="user-settings" :size="3" />
        {{ $t("pages.dashboard.title") }}
    </headline>
    <dashboard-nav />
    <dashboard-password />
    <dashboard-profile />
    <two-factor />
</template>
