<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import DashboardPassword from "@/pages/User/Dashboard/DashboardPassword.vue";
import Headline from "Components/Visual/Headline.vue";
import Paragraph from "Components/Visual/Paragraph.vue";
</script>

<template>
    <Head
        ><title>{{ $t("pages.dashboard.title") }}</title></Head
    >
    <headline>{{ $t("pages.dashboard.title") }}</headline>
    <Headline :size="3">{{ $t("pages.dashboard.password.headline") }}</Headline>
    <dashboard-password />
    <Headline :size="3">{{ $t("pages.dashboard.profile.headline") }}</Headline>
    <Paragraph>{{ $t("pages.dashboard.profile.intro") }}</Paragraph>
    <Headline :size="3">{{ $t("pages.dashboard.two-factor.headline") }}</Headline>
    <Paragraph>{{ $t("pages.dashboard.two-factor.intro") }}</Paragraph>
</template>
