<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import Headline from "Components/Visual/Headline.vue";
import Paragraph from "Components/Visual/Paragraph.vue";
</script>

<template>
    <Head
        ><title>{{ $t("pages.dashboard.title") }}</title></Head
    >
    <headline>{{ $t("pages.dashboard.title") }}</headline>
    <Headline :size="3">{{ $t("pages.dashboard.theme.headline") }}</Headline>
    <Paragraph>{{ $t("pages.dashboard.theme.intro") }}</Paragraph>
    <Headline :size="3">{{ $t("pages.dashboard.password.headline") }}</Headline>
    <Paragraph>{{ $t("pages.dashboard.password.intro") }}</Paragraph>
    <Headline :size="3">{{ $t("pages.dashboard.theme.headline") }}</Headline>
    <Paragraph>{{ $t("pages.dashboard.theme.intro") }}</Paragraph>
    <Headline :size="3">{{ $t("pages.dashboard.two-factor.headline") }}</Headline>
    <Paragraph>{{ $t("pages.dashboard.two-factor.intro") }}</Paragraph>
</template>
