<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import { useBreadcrumbs } from "Composables/useBreadcrumbs.ts";
const { setBreadcrumbs } = useBreadcrumbs();
setBreadcrumbs([{ labelKey: "pages.decks.link" }]);
</script>

<template>
    <Head
        ><title>{{ $t("pages.decks.title") }}</title></Head
    >
    <headline>
        <icon name="deck" :size="3" />
        {{ $t("pages.decks.title") }}
    </headline>
    <Link class="btn-default" href="/decks/add">
        <icon name="add" />
        {{ $t("pages.create_deck.link") }}
    </Link>
</template>
