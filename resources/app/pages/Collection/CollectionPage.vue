<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import { useBreadcrumbs } from "Composables/useBreadcrumbs.ts";
const { setBreadcrumbs } = useBreadcrumbs();
setBreadcrumbs([{ labelKey: "pages.collection.link" }]);
</script>

<template>
    <Head
        ><title>{{ $t("pages.collection.title") }}</title></Head
    >
    <headline>
        <icon name="collection" :size="3" />
        {{ $t("pages.collection.title") }}
    </headline>
    <Link href="/collection/containers" class="btn-default">
        <icon name="storage" />
        {{ $t("pages.containers.link") }}
    </Link>
    <Link href="/collection/add" class="btn-default">
        <icon name="add" />
        {{ $t("pages.add_cards.link") }}
    </Link>
    <p>list cards, search cards, stats.</p>
</template>
