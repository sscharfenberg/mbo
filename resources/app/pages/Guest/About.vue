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
    <headline :size="3">{{ $t("pages.about.security") }}</headline>
    <paragraph>
        <i18n-t keypath="pages.about.security_text" scope="global">
            <template #link>
                <labelled-link href="https://github.com/sscharfenberg/mbo/blob/main/SECURITY.md">{{
                    $t("pages.about.security_link")
                }}</labelled-link>
            </template>
        </i18n-t>
    </paragraph>
    <headline :size="3">{{ $t("pages.about.attribution.title") }}</headline>
    <paragraph>
        <strong>Scryfall</strong><br />
        {{ $t("pages.about.attribution.scryfall") }}<br />
        <labelled-link href="https://scryfall.com">scryfall.com</labelled-link>
    </paragraph>
    <paragraph>
        <strong>Laravel</strong><br />
        <i18n-t keypath="pages.about.attribution.laravel" scope="global">
            <template #cite><cite>The framework for web artisans</cite></template> </i18n-t
        ><br />
        <labelled-link href="https://laravel.com">laravel.com</labelled-link>
    </paragraph>
    <paragraph>
        <strong>VueJS</strong><br />
        {{ $t("pages.about.attribution.vue") }}<br />
        <labelled-link href="https://vuejs.org">vuejs.org</labelled-link>
    </paragraph>
    <paragraph>
        <i18n-t keypath="pages.about.attribution.images" scope="global">
            <template #maintenance><cite>Error 503 (Maintenance)</cite></template>
            <template #notfound><cite>Error 404 (Page not found)</cite></template>
            <template #author>
                <labelled-link
                    href="https://pixabay.com/users/valiphotos-1720744/?utm_source=link-attribution&utm_medium=referral&utm_campaign=image&utm_content=3089995"
                    >Valentin</labelled-link
                >
            </template>
            <template #source>
                <labelled-link
                    href="https://pixabay.com//?utm_source=link-attribution&utm_medium=referral&utm_campaign=image&utm_content=3089995"
                    >Pixabay</labelled-link
                >
            </template>
            <template #license>
                <labelled-link href="https://pixabay.com/service/license-summary/"
                    >Pixabay Content License</labelled-link
                >
            </template>
        </i18n-t>
    </paragraph>
    <paragraph>
        <i18n-t keypath="pages.about.attribution.icons" scope="global">
            <template #link>
                <labelled-link href="https://fonts.google.com/icons">Google Material Design Icons</labelled-link>
            </template>
        </i18n-t>
    </paragraph>
    <paragraph>
        <i18n-t keypath="pages.about.attribution.symbols" scope="global">
            <template #scryfall>
                <labelled-link href="https://scryfall.com">scryfall.com</labelled-link>
            </template>
            <template #fcp>
                <labelled-link href="https://company.wizards.com/en/legal/fancontentpolicy"
                    >Fan Content Policy</labelled-link
                >
            </template>
        </i18n-t>
    </paragraph>
</template>
