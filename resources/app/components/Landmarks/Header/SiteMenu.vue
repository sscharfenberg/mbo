<script setup lang="ts">
import LabelledLink from "Components/UI/LabelledLink.vue";
</script>

<template>
    <ul class="site-menus" :aria-label="$t('header.site_nav')">
        <li>
            <labelled-link href="/collection">
                {{ $t("pages.collection.link") }}
            </labelled-link>
        </li>
        <li>
            <labelled-link href="/decks">
                {{ $t("pages.decks.link") }}
            </labelled-link>
        </li>
    </ul>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/mixins" as m;
@use "Abstracts/sizes" as s;

.site-menus {
    display: flex;
    flex-direction: column;

    padding: 0;
    margin: 0;

    list-style: none;

    @include m.mq("landscape") {
        flex-direction: row;

        gap: 1ch;
    }

    @include m.mq("landscape") {
        > li {
            display: flex;
            align-items: center;

            &:nth-child(2n)::before {
                display: inline-block;

                width: map.get(s.$header, "sitelink-divider");
                height: 60%;
                margin-right: 1ch;

                background-color: currentcolor;
                border-radius: map.get(s.$header, "sitelink-divider-radius");

                content: "";
            }
        }
    }
}
</style>
