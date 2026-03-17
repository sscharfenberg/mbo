<script setup lang="ts">
import { Form, Head } from "@inertiajs/vue3";
import { computed, nextTick, ref } from "vue";
import AddCardsSearch from "@/pages/Collection/AddCards/AddCardsSearch.vue";
import type { Container } from "@/types/container";
import type { ContainerListItem } from "@/types/containerListItem";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import MonoSelect from "Components/Form/Select/MonoSelect.vue";
import Badge from "Components/UI/Badge.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import { useBreadcrumbs } from "Composables/useBreadcrumbs.ts";
const props = defineProps<{
    /** Present when adding cards to a specific container; null for unsorted / collection-level. */
    container: Container | null;
    /** Lightweight list of all user containers for the container dropdown. */
    containers: ContainerListItem[];
    /** CardCondition enum values. */
    conditions: string[];
    /** FoilType enum values. */
    foilTypes: string[];
    /** CardLanguage enum values. */
    languages: string[];
}>();
const containerOptions = computed(() =>
    props.containers.map(container => {
        return {
            value: container.id,
            label: container.name
        };
    })
);
const selectedContainer = ref(props.container?.id as string);

/**
 * Called when the type select changes. Updates selectedType and re-triggers
 * precognitive validation for the container_type field.
 *
 * @param value - The newly selected type value.
 * @param validate - The precognitive validate callback from the Form slot.
 */
const onTypeChange = (value: string, validate: (field: string) => void) => {
    selectedContainer.value = value;
    nextTick(() => validate("container_type"));
};
const { setBreadcrumbs } = useBreadcrumbs();
setBreadcrumbs([
    { labelKey: "pages.collection.link", href: "/collection", icon: "deck" },
    { labelKey: "pages.add_cards.link", href: "/collection/containers" }
]);
</script>

<template>
    <Head
        ><title>{{ $t("pages.add_cards.title") }}</title></Head
    >
    <headline>
        <icon name="add" :size="3" />
        {{ $t("pages.add_cards.title") }}
        <badge type="info" v-if="container">
            <icon name="storage" />
            {{ container?.type === "other" ? container?.custom_type : $t("enums.binder_type." + container?.type) }}:
            {{ container.name }}
        </badge>
        <badge v-else>
            <icon name="collection" />
            {{ $t("pages.add_cards.to_collection") }}
        </badge>
    </headline>
    <Form action="/collection/add" method="post" class="form" #default="{ validate, processing }">
        <form-legend
            :items="[
                { slot: 'required', icon: 'info' },
                { slot: 'add', icon: 'add' }
            ]"
        >
            <template #add>{{ $t("pages.add_cards.explanation") }}</template>
            <template #required>
                <i18n-t keypath="form.legend.required" scope="global">
                    <template #icon><icon name="required" /></template>
                </i18n-t>
            </template>
        </form-legend>
        <form-group :label="$t('form.fields.container.id')">
            <mono-select
                :options="containerOptions"
                :selected="selectedContainer"
                @change="onTypeChange($event, validate)"
                addon-icon="storage"
            />
            <input type="hidden" name="container_id" :value="selectedContainer" />
        </form-group>
        <add-cards-search />
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="save" />
                {{ $t("pages.register.submit") }}
                <loading-spinner v-if="processing" :size="2" />
            </button>
        </form-group>
    </Form>
</template>

<style lang="scss" scoped>
.badge {
    margin-left: auto;
}
</style>
