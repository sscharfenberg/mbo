<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import List, { type Container } from "@/pages/Collection/Container/ListContainers/List.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import Paragraph from "Components/UI/Paragraph.vue";

const { t } = useI18n();

const props = defineProps<{
    containers: Container[];
    containerTypes: string[];
    containersMax: number;
    containersAmount: number;
    canCreateNewContainer: boolean;
}>();

/** Currently selected type filter keys. Empty set means no filter active. */
const activeTypes = ref<Set<string>>(new Set());

/** Current name search string (raw, lowercasing happens in filteredContainers). */
const search = ref("");

/**
 * Returns the display/filter key for a container.
 * For "other" containers, returns the custom_type value instead of "other"
 * so custom types can be filtered and labeled individually.
 */
function effectiveType(c: Container): string {
    return c.type === "other" && c.custom_type ? c.custom_type : c.type;
}

/**
 * Returns the translated label for a standard binder type,
 * or the raw string for user-defined custom types.
 */
function typeLabel(type: string): string {
    return props.containerTypes.includes(type) ? t(`enums.binder_type.${type}`) : type;
}

/** Unique effective types present in the current container list, used to build the type filter. */
const usedTypes = computed(() => [...new Set(props.containers.map(effectiveType))]);

/**
 * Containers after applying both the active type filter and the name search.
 * Type filter: passes all when empty, otherwise requires effectiveType match.
 * Name filter: case-insensitive substring match on container name.
 */
const filteredContainers = computed(() => {
    const needle = search.value.toLowerCase();
    return props.containers.filter(
        c =>
            (activeTypes.value.size === 0 || activeTypes.value.has(effectiveType(c))) &&
            (!needle || c.name.toLowerCase().includes(needle))
    );
});

/**
 * Toggles a type in the active filter set.
 * Selecting an already-active type deselects it.
 */
function toggleType(type: string) {
    const next = new Set(activeTypes.value);
    if (next.has(type)) {
        next.delete(type);
    } else {
        next.add(type);
    }
    activeTypes.value = next;
}
</script>

<template>
    <Head
        ><title>{{ $t("pages.containers.title") }}</title></Head
    >
    <headline>
        <icon name="container-type" :size="3" />
        {{ $t("pages.containers.title") }}
    </headline>
    <paragraph> {{ $t("pages.containers.explanation") }}<br /> </paragraph>
    <ul class="meta">
        <li class="meta__showing">
            {{ t("pages.containers.showing", { amount: containersAmount, max: containersMax }) }}
        </li>
        <li class="meta__search">
            <input
                type="text"
                :placeholder="$t('pages.containers.search')"
                class="form-input"
                @keyup="search = ($event.target as HTMLInputElement).value"
            />
        </li>
        <li class="meta__types">
            <ul class="type-filter">
                <li
                    v-for="type in usedTypes"
                    :key="type"
                    class="type-filter__item"
                    :class="{ 'type-filter__item--active': activeTypes.has(type) }"
                    @click="toggleType(type)"
                >
                    {{ typeLabel(type) }}
                </li>
            </ul>
        </li>
        <li>
            <Link v-if="canCreateNewContainer" href="/collection/containers/new" class="btn-default">
                <icon name="add" />
                {{ $t("pages.new_container.link") }}
            </Link>
        </li>
    </ul>
    <List :containers="filteredContainers" />
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;

    padding: 0;
    margin: 0;
    gap: 1ch;

    list-style: none;
}

.type-filter {
    display: flex;
    flex-wrap: wrap;

    padding: 0.75ex 1ch;
    margin: 0;
    gap: 1ch;

    background-color: map.get(c.$main, "containers", "types-background");
    list-style: none;
    border-radius: map.get(s.$main, "containers", "types-radius");

    &__item {
        padding: 0.5ex 1ch;

        background-color: map.get(c.$main, "containers", "type-background");
        border-radius: map.get(s.$main, "containers", "types-radius");

        cursor: pointer;

        &--active {
            background-color: map.get(c.$main, "containers", "type-background-active");
            color: map.get(c.$main, "containers", "type-surface-active");
        }
    }
}
</style>
