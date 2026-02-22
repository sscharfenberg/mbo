<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import DashboardPassword from "@/pages/User/Dashboard/DashboardPassword.vue";
import DashboardProfile from "@/pages/User/Dashboard/DashboardProfile.vue";
import DashboardTwoFactor from "@/pages/User/Dashboard/DashboardTwoFactor.vue";
import Headline from "Components/Visual/Headline.vue";
</script>

<template>
    <Head
        ><title>{{ $t("pages.dashboard.title") }}</title></Head
    >
    <headline>{{ $t("pages.dashboard.title") }}</headline>
    <dashboard-password />
    <dashboard-profile />
    <dashboard-two-factor />
</template>
