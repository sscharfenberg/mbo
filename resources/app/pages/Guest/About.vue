<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import LabelledLink from "Components/UI/LabelledLink.vue";
import Paragraph from "Components/UI/Paragraph.vue";
import { useBreadcrumbs } from "Composables/useBreadcrumbs.ts";
const { setBreadcrumbs } = useBreadcrumbs();
setBreadcrumbs([{ labelKey: "pages.about.link" }]);
</script>

<template>
    <Head
        ><title>{{ $t("pages.about.title") }}</title></Head
    >
    <headline>
        <icon name="info" :size="3" />
        {{ $t("pages.about.title") }}
    </headline>
    <paragraph>
        {{ $t("pages.about.hello") }}<br />
        <br />
        {{ $t("pages.about.intro") }}
    </paragraph>
    <headline :size="3">{{ $t("pages.about.attribution.title") }}</headline>
    <paragraph>
        <strong>Scryfall</strong><br />
        The pillar of MtG community websites. Scryfall provides the data that drives MBO - all data about cards, sets,
        artists and all card images come from them. Sites like this would not be possible without the developers and the
        community over at scryfall.<br />
        <labelled-link href="https://scryfall.com" :external="true" icon="external-link" rel="noopener nofollow"
            >scryfall.com</labelled-link
        >
    </paragraph>
    <paragraph>
        <strong>Laravel</strong><br />
        <cite>The framework for web artisans</cite> provides the backend (server) framework for MBO. Laravel is an
        opinionated full-stack framework.<br />
        <labelled-link href="https://laravel.com" :external="true" icon="external-link" rel="noopener nofollow"
            >laravel.com</labelled-link
        >
    </paragraph>
    <paragraph>
        <strong>VueJS</strong><br />
        is the frontend framework that powers the client - what's running in your browser. VueJS is easy to get into,
        and complex enough to power enterprise solutions.<br />
        <labelled-link href="https://vuejs.org" :external="true" icon="external-link" rel="noopener nofollow"
            >vuejs.org</labelled-link
        >
    </paragraph>
    <paragraph>
        Images <cite>Error 503 (Maintenance)</cite> and <cite>Error 404 (Page not found)</cite> by
        <labelled-link
            href="https://pixabay.com/users/valiphotos-1720744/?utm_source=link-attribution&utm_medium=referral&utm_campaign=image&utm_content=3089995"
            :external="true"
            icon="external-link"
            rel="noopener nofollow"
            >Valentin</labelled-link
        >
        from
        <labelled-link
            href="https://pixabay.com//?utm_source=link-attribution&utm_medium=referral&utm_campaign=image&utm_content=3089995"
            :external="true"
            icon="external-link"
            rel="noopener nofollow"
            >Pixabay</labelled-link
        >.
    </paragraph>
    <paragraph>
        Icons used throughout the site are from
        <labelled-link
            href="https://fonts.google.com/icons"
            :external="true"
            icon="external-link"
            rel="noopener nofollow"
            >Google Material Design Icons</labelled-link
        >
    </paragraph>
    <paragraph>
        MtG symbols © Wizards of the Coast LLC. SVGs provided by
        <labelled-link href="https://scryfall.com" :external="true" icon="external-link" rel="noopener nofollow"
            >scryfall.com</labelled-link
        >
        and used under the
        <labelled-link
            href="https://company.wizards.com/en/legal/fancontentpolicy"
            :external="true"
            icon="external-link"
            rel="noopener nofollow"
            >Fan Content Policy</labelled-link
        >.</paragraph
    >
</template>
