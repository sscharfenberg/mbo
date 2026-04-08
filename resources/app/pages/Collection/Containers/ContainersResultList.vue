<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { VueDraggable } from "vue-draggable-plus";
import { useI18n } from "vue-i18n";
import ContainerMenu from "@/pages/Collection/common/ContainerMenu.vue";
import Icon from "Components/UI/Icon.vue";
import VisibilityBadge from "Components/UI/VisibilityBadge.vue";
import { useFormatting } from "Composables/useFormatting";
import type { Container } from "Types/container";
const { t } = useI18n();
const { formatPrice, formatDecimals } = useFormatting();
const props = defineProps<{ containers: Container[] }>();
/** Emitted after a successful drag-drop; carries the visible rows in their new order. */
const emit = defineEmits<{ reorder: [containers: Container[]] }>();
/**
 * Local writable copy of the containers prop, used as VueDraggable's v-model.
 * A watch keeps it in sync whenever the parent passes a fresh array (e.g. after
 * an Inertia page reload), without breaking any in-progress drag.
 */
const list = ref([...props.containers]);
watch(
    () => props.containers,
    val => {
        list.value = [...val];
    }
);
</script>

<template>
    <VueDraggable
        v-model="list"
        tag="ul"
        class="clist"
        handle=".clist__drag-handle"
        ghost-class="clist__item--ghost"
        @end="emit('reorder', list)"
    >
        <li v-for="container in list" :key="container.id" class="clist__item">
            <span class="clist__drag-handle"><icon name="drag" /></span>
            <Link class="clist__data" :href="`/containers/${container.id}`">
                <img
                    v-if="container.defaultCard"
                    :src="container.defaultCard.art_crop"
                    class="clist__image"
                    :alt="container.name"
                />
                <span v-else class="clist__image" />
                <span class="clist__name">
                    {{ container.name }}
                    <span v-if="container.description" class="clist__description">{{ container.description }}</span>
                </span>
                <span class="clist__type"
                    ><icon name="storage" />
                    {{
                        container.type === "other"
                            ? container.custom_type
                            : $t("enums.container_type." + container.type)
                    }}</span
                >
                <span
                    class="clist__count"
                    v-tooltip="
                        t(
                            'pages.container_page.cards_count',
                            { count: formatDecimals(container.totalCards) },
                            container.totalCards
                        )
                    "
                    ><icon name="deck" />{{ formatDecimals(container.totalCards) }}</span
                >
                <span class="clist__price"><icon name="money" />{{ formatPrice(container.totalPrice) }}</span>
                <visibility-badge class="clist__visibility" :visibility="container.visibility" />
            </Link>
            <ContainerMenu :container="container" :containers="containers" />
        </li>
    </VueDraggable>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/mixins" as m;
@use "Abstracts/sizes" as s;
@use "Abstracts/timings" as ti;

.clist {
    display: grid;
    grid-template-columns: auto 1fr auto auto auto auto;

    padding: 0;
    margin: 1lh 0 0;
    row-gap: 0.5lh;

    list-style: none;

    @include m.mq("landscape") {
        grid-template-columns: auto 4rem 1fr auto auto auto auto auto;
    }

    &__item {
        display: grid;
        align-items: center;
        grid-template-columns: subgrid;
        grid-column: 1 / -1;

        padding-right: 1ch;
        border: map.get(s.$pages, "containers", "border") solid map.get(c.$pages, "containers", "border");
        row-gap: 0.5rem;

        background: map.get(c.$pages, "containers", "background", "odd");
        color: map.get(c.$pages, "containers", "surface");
        border-radius: map.get(s.$pages, "containers", "radius");

        transition: background-color map.get(ti.$timings, "fast") linear;

        &:hover {
            background: map.get(c.$pages, "containers", "background-hover", "odd");
        }

        &:nth-of-type(even) {
            background: map.get(c.$pages, "containers", "background", "even");

            &:hover {
                background: map.get(c.$pages, "containers", "background-hover", "even");
            }
        }

        &--ghost {
            opacity: 0.3;
        }

        @include m.mq("portrait") {
            row-gap: 1rem;
        }
    }

    &__drag-handle {
        display: inline-flex;
        align-items: center;
        align-self: stretch;

        padding: 1ex 1ch;

        border-top-left-radius: calc(
            map.get(s.$pages, "containers", "radius") - map.get(s.$pages, "containers", "border")
        );
        border-bottom-left-radius: calc(
            map.get(s.$pages, "containers", "radius") - map.get(s.$pages, "containers", "border")
        );

        cursor: grab;

        &:active {
            cursor: grabbing;
        }
    }

    &__data {
        display: grid;
        align-items: center;
        grid-template-columns: subgrid;
        grid-column: 2 / -2;

        padding-right: 1ch;

        column-gap: 1ch;

        color: inherit;

        text-decoration: none;
    }

    &__name {
        overflow: hidden;
        min-width: 0;
        padding: 0.5ex 0;

        white-space: nowrap;

        text-overflow: ellipsis;
    }

    &__description {
        display: block;

        overflow: hidden;
        min-width: 0;
        padding: 0 0 0.5ex;

        color: map.get(c.$pages, "containers", "surface-description");

        font-size: 0.8rem;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    &__type,
    &__count,
    &__price {
        display: flex;
        align-items: center;

        gap: 0.5ch;

        .icon {
            display: none;

            @include m.mq("landscape") {
                display: block;
            }
        }
    }

    &__visibility {
        display: none;

        @include m.mq("landscape") {
            display: inline-flex;
        }
    }

    &__image {
        display: none;
        align-self: stretch;

        width: 100%;

        object-fit: cover;

        @include m.mq("landscape") {
            display: block;
        }
    }

    &__price {
        display: none;

        @include m.mq("landscape") {
            display: flex;
        }
    }
}
</style>
