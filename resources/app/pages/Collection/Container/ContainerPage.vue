<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import ArtCropImage from "Components/Card/ArtCrop/ArtCropImage.vue";
import Badge from "Components/UI/Badge.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import { useBreadcrumbs } from "Composables/useBreadcrumbs";
import type { Container } from "Types/container";
const props = defineProps<{ container: Container }>();
const { setBreadcrumbs } = useBreadcrumbs();
setBreadcrumbs([
    { labelKey: "pages.collection.link", href: "/collection", icon: "collection" },
    { labelKey: "pages.containers.link", href: "/collection/containers", icon: "container-type" },
    { label: props.container.name }
]);
</script>

<template>
    <Head
        ><title>{{ container.name }}</title></Head
    >
    <headline>
        <icon name="container-name" :size="3" />
        {{ container.name }}
        <badge type="info">{{
            container.type === "other" ? container.custom_type : $t("enums.binder_type." + container.type)
        }}</badge>
    </headline>
    <ul class="container-meta">
        <li class="container-meta__name">{{ container.name }}</li>
        <li v-if="container.description">{{ container.description }}</li>
        <li v-if="container.defaultCard"><art-crop-image :card="container.defaultCard" /></li>
        <li>
            <icon name="container-type" />{{
                container.type === "other" ? container.custom_type : $t("enums.binder_type." + container.type)
            }}
        </li>
        <li><icon name="deck" />255 Cards</li>
        <li><icon name="wallet" />145,56 €</li>
        <li>
            <Link :href="`/collection/containers/${container.id}`" class="btn-default">
                <icon name="edit" />
                {{ $t("pages.edit_container.link") }}
            </Link>
        </li>
    </ul>
    (List Cards in this container here. tbd)
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/shadows" as sh;
@use "Abstracts/sizes" as s;
@use "Abstracts/mixins" as m;
@use "Abstracts/z-indexes" as z;

.container-meta {
    display: flex;
    position: relative;
    flex-direction: column;

    padding: map.get(s.$main, "container", "meta", "padding");
    margin: 0;
    gap: 1ch;

    background-color: map.get(c.$main, "container", "meta", "background");
    color: map.get(c.$main, "container", "meta", "surface");
    list-style: none;
    border-radius: map.get(s.$main, "container", "meta", "radius");

    &::before {
        position: absolute;
        inset: 0;
        z-index: map.get(z.$index, "background");

        border: map.get(s.$main, "container", "meta", "border") solid transparent;

        background: linear-gradient(
                to bottom right,
                map.get(c.$main, "container", "meta", "border-from"),
                map.get(c.$main, "container", "meta", "border-to")
            )
            border-box;

        border-radius: inherit;
        mask:
            linear-gradient(black, black) border-box,
            linear-gradient(black, black) padding-box;
        mask-composite: subtract;

        content: "";
    }

    @include m.mq("landscape") {
        display: grid;
        grid-template-columns: 1fr 1fr;

        li:has(.art-crop) {
            display: flex;
            justify-content: flex-end;
            grid-column: 2;
            grid-row: 1 / span 10;
        }

        li:not(:has(.art-crop)) {
            display: flex;
            align-items: center;
            grid-column: 1;

            gap: 1ch;
        }
    }

    .art-crop {
        z-index: map.get(z.$index, "default");
    }

    &__name {
        color: map.get(c.$main, "container", "meta", "surface-name");

        font-size: 2rem;
        font-weight: 300;
        text-shadow: map.get(sh.$main, "container-name");
    }
}

.badge {
    margin-left: auto;
}
</style>
