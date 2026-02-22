<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import DashboardPassword from "@/pages/User/Dashboard/DashboardPassword.vue";
import DashboardProfile from "@/pages/User/Dashboard/DashboardProfile.vue";
import Headline from "Components/Visual/Headline.vue";
import Paragraph from "Components/Visual/Paragraph.vue";
</script>

<template>
    <Head
        ><title>{{ $t("pages.dashboard.title") }}</title></Head
    >
    <headline>{{ $t("pages.dashboard.title") }}</headline>
    <headline :size="3">{{ $t("pages.dashboard.password.headline") }}</headline>
    <dashboard-password />
    <headline :size="3">{{ $t("pages.dashboard.profile.headline") }}</headline>
    <dashboard-profile />
    <headline :size="3">{{ $t("pages.dashboard.two-factor.headline") }}</headline>
    <Paragraph>{{ $t("pages.dashboard.two-factor.intro") }}</Paragraph>
</template>
