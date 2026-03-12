<script setup lang="ts">
import Icon from "Components/UI/Icon.vue";

export interface Container {
    id: string;
    name: string;
    description: string | null;
    type: string;
    custom_type: string | null;
    artUrl: string | null;
    sort: number;
}
defineProps<{ containers: Container[] }>();
</script>

<template>
    <ul class="clist">
        <li v-for="container in containers" :key="container.id" class="clist__item">
            <span class="clist__drag-handle"><icon name="drag" /></span>
            <span class="clist__data">
                <img
                    v-if="container.artUrl"
                    :src="container.artUrl ?? undefined"
                    class="clist__image"
                    :alt="container.name"
                />
                <span v-else class="clist__image" />
                <span class="clist__name">{{ container.name }}</span>
                <span class="clist__type"
                    ><icon name="container-type" />
                    {{
                        container.type === "other" ? container.custom_type : $t("enums.binder_type." + container.type)
                    }}</span
                >
                <span class="clist__count">0</span>
                <span class="clist__price">125,56€</span>
            </span>
        </li>
    </ul>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/mixins" as m;
@use "Abstracts/sizes" as s;

.clist {
    display: grid;
    grid-template-columns: auto 4rem 1fr auto auto auto;

    padding: 0;
    margin: 1lh 0 0;
    gap: 0.5lh 1ch;

    list-style: none;

    &__item {
        display: grid;
        align-items: center;
        grid-template-columns: subgrid;
        grid-column: 1 / -1;

        border: map.get(s.$main, "containers", "border") solid map.get(c.$main, "containers", "border");
        gap: 0.5rem;

        background: map.get(c.$main, "containers", "background", "odd");
        color: map.get(c.$main, "containers", "surface");
        border-radius: map.get(s.$main, "containers", "radius");

        &:nth-of-type(even) {
            background: map.get(c.$main, "containers", "background", "even");
        }

        @include m.mq("portrait") {
            gap: 1rem;
        }
    }

    &__drag-handle {
        display: inline-flex;
        align-items: center;
        align-self: stretch;

        padding: 1ex 1ch;

        border-top-left-radius: calc(
            map.get(s.$main, "containers", "radius") - map.get(s.$main, "containers", "border")
        );
        border-bottom-left-radius: calc(
            map.get(s.$main, "containers", "radius") - map.get(s.$main, "containers", "border")
        );
    }

    &__data {
        display: grid;
        align-items: center;
        grid-template-columns: subgrid;
        grid-column: 2 / -1;

        padding-right: 1ch;
    }

    &__name {
        overflow: hidden;
        min-width: 0;

        white-space: nowrap;

        text-overflow: ellipsis;
    }

    &__type {
        display: flex;
        align-items: center;

        gap: 0.5ch;

        .icon {
            display: none;

            @include m.mq("portrait") {
                display: block;
            }
        }
    }

    &__image {
        align-self: stretch;

        width: 100%;

        object-fit: cover;
    }

    &__price {
        display: none;

        @include m.mq("portrait") {
            display: block;
        }
    }
}
</style>
