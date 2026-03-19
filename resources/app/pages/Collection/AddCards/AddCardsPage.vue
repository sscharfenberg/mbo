<script setup lang="ts">
import { Form, Head } from "@inertiajs/vue3";
import { computed, nextTick, ref } from "vue";
import { useI18n } from "vue-i18n";
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
const { t } = useI18n();
const containerOptions = computed(() =>
    props.containers.map(container => {
        return {
            value: container.id,
            label: container.name
        };
    })
);
const selectedContainer = ref(props.container?.id as string);
const conditionOptions = computed(() =>
    props.conditions.map(condition => {
        return {
            value: condition,
            label: t("enums.conditions." + condition)
        };
    })
);
const selectedCondition = ref("");
const foilOptions = computed(() =>
    props.foilTypes.map(type => {
        return {
            value: type,
            label: t("enums.foil_types." + type)
        };
    })
);
const selectedFoilType = ref("");
/**
 * Called when the type select changes. Updates selectedType and re-triggers
 * precognitive validation for the container_type field.
 *
 * @param value - The newly selected type value.
 * @param validate - The precognitive validate callback from the Form slot.
 */
const onContainerChange = (value: string, validate: (field: string) => void) => {
    selectedContainer.value = value;
    nextTick(() => validate("container_id"));
};
const onConditionChange = (value: string, validate: (field: string) => void) => {
    selectedCondition.value = value;
    nextTick(() => validate("condition"));
};
const onFoilTypeChange = (value: string, validate: (field: string) => void) => {
    selectedFoilType.value = value;
    nextTick(() => validate("foil_type"));
};
const { setBreadcrumbs } = useBreadcrumbs();
const amount = ref(1);
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
    {{ languages }}
    <Form action="/collection/add" method="post" class="form" #default="{ validate, processing, errors, invalid }">
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
                @change="onContainerChange($event, validate)"
                addon-icon="storage"
            />
            <input type="hidden" name="container_id" :value="selectedContainer" />
        </form-group>
        <add-cards-search />
        <form-group :label="$t('form.fields.condition')">
            <mono-select
                :options="conditionOptions"
                :selected="selectedCondition"
                @change="onConditionChange($event, validate)"
                :sort="false"
                addon-icon="cards"
            />
            <input type="hidden" name="condition" :value="selectedCondition" />
        </form-group>
        <form-group
            for-id="amount"
            :label="$t('form.fields.amount')"
            :error="errors.amount ?? ''"
            :invalid="invalid('amount')"
            :required="true"
            addon-icon="deck"
        >
            <input type="text" class="form-input" inputmode="numeric" v-model="amount" />
            <template #button>
                <button type="button" @mousedown.prevent @click="amount++" tabindex="-1">
                    <icon name="add" />
                </button>
                <button type="button" @mousedown.prevent @click="amount--" :disabled="amount <= 1" tabindex="-1">
                    <icon name="subtract" />
                </button>
            </template>
        </form-group>
        <form-group :label="$t('form.fields.foil_type')">
            <mono-select
                :options="foilOptions"
                :selected="selectedFoilType"
                @change="onFoilTypeChange($event, validate)"
                :sort="false"
                addon-icon="star"
            />
            <input type="hidden" name="condition" :value="selectedFoilType" />
        </form-group>
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="save" />
                {{ $t("pages.add_cards.submit") }}
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
