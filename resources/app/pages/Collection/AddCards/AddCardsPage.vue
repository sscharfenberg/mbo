<script setup lang="ts">
import { Form, Head } from "@inertiajs/vue3";
import { type Ref, computed, nextTick, ref } from "vue";
import { useI18n } from "vue-i18n";
import AddCardsDefaults from "@/pages/Collection/AddCards/AddCardsDefaults.vue";
import AddCardsLanguage from "@/pages/Collection/AddCards/AddCardsLanguage.vue";
import AddCardsSearch from "@/pages/Collection/AddCards/AddCardsSearch.vue";
import type { Container } from "@/types/container";
import type { ContainerListItem } from "@/types/containerListItem";
import ButtonGroup from "Components/Form/ButtonGroup.vue";
import FormGroup from "Components/Form/FormGroup.vue";
import MonoSelect from "Components/Form/Select/MonoSelect.vue";
import Badge from "Components/UI/Badge.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import { useAddCardsDefaults } from "Composables/useAddCardsDefaults.ts";
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
const { setBreadcrumbs } = useBreadcrumbs();
setBreadcrumbs([
    { labelKey: "pages.collection.link", href: "/collection", icon: "deck" },
    { labelKey: "pages.add_cards.link", href: "/collection/containers" }
]);
const {
    savedDefaults,
    hasSavedDefaults,
    amount,
    language: selectedLanguage,
    condition: selectedCondition,
    foilType: selectedFoilType,
    resetKey: searchKey,
    saveDefaults,
    clearDefaults,
    resetToDefaults
} = useAddCardsDefaults();
/** Container options formatted for MonoSelect: `{ value, label }` pairs. */
const containerOptions = computed(() =>
    props.containers.map(container => ({
        value: container.id,
        label: container.name
    }))
);
/** Currently selected container id. Initialized from the container prop when present. */
const selectedContainer = ref(props.container?.id as string);
/** CardCondition options formatted for MonoSelect with translated labels. */
const conditionOptions = computed(() =>
    props.conditions.map(condition => ({
        value: condition,
        label: t("enums.conditions." + condition)
    }))
);
/** FoilType options formatted for MonoSelect with translated labels. */
const foilOptions = computed(() =>
    props.foilTypes.map(type => ({
        value: type,
        label: t("enums.foil_types." + type)
    }))
);
/** Maps form field names to their corresponding refs for generic select handling. */
const selectRefs: Record<string, Ref<string>> = {
    container_id: selectedContainer,
    condition: selectedCondition,
    foil_type: selectedFoilType
};
/**
 * Generic change handler for MonoSelect fields. Updates the ref and
 * re-triggers precognitive validation for the given field name.
 */
const onSelectChange = (field: string, value: string, validate: (field: string) => void) => {
    selectRefs[field].value = value;
    nextTick(() => validate(field));
};

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
    <Form
        action="/collection/add"
        method="post"
        class="form"
        @success="resetToDefaults"
        #default="{ validate, processing, validating, errors, valid }"
    >
        <add-cards-defaults
            :amount="amount"
            :language="selectedLanguage"
            :condition="selectedCondition"
            :foil-type="selectedFoilType"
            :saved-defaults="savedDefaults"
            :has-saved-defaults="hasSavedDefaults"
            @save="saveDefaults"
            @clear="clearDefaults"
        />
        <add-cards-search :key="searchKey" :error="errors.default_card_id ?? ''" :invalid="!!errors?.default_card_id" />
        <form-group
            for-id="amount"
            :label="$t('form.fields.amount')"
            :error="errors.amount ?? ''"
            :invalid="!!errors?.amount"
            :validated="valid('amount')"
            :validating="validating"
            :required="true"
            addon-icon="deck"
        >
            <input
                type="text"
                name="amount"
                class="form-input"
                inputmode="numeric"
                v-model="amount"
                @change="validate('amount')"
            />
            <template #button>
                <button type="button" @mousedown.prevent @click="amount++" tabindex="-1">
                    <icon name="add" />
                </button>
                <button type="button" @mousedown.prevent @click="amount--" :disabled="amount <= 1" tabindex="-1">
                    <icon name="subtract" />
                </button>
            </template>
        </form-group>
        <add-cards-language
            v-model="selectedLanguage"
            :languages="languages"
            :error="errors.language ?? ''"
            :invalid="!!errors?.language"
        />
        <form-group
            :label="$t('form.fields.container.id')"
            :error="errors.container_id ?? ''"
            :invalid="!!errors?.container_id"
            :validated="valid('container_id')"
            :validating="validating"
        >
            <mono-select
                :options="containerOptions"
                :selected="selectedContainer"
                @change="onSelectChange('container_id', $event, validate)"
                addon-icon="storage"
            />
            <input type="hidden" name="container_id" :value="selectedContainer" />
        </form-group>
        <form-group
            :label="$t('form.fields.condition')"
            :error="errors.condition ?? ''"
            :invalid="!!errors?.condition"
            :validated="valid('condition')"
            :validating="validating"
        >
            <mono-select
                :options="conditionOptions"
                :selected="selectedCondition"
                @change="onSelectChange('condition', $event, validate)"
                :sort="false"
                addon-icon="cards"
            />
            <input type="hidden" name="condition" :value="selectedCondition" />
        </form-group>
        <form-group
            :label="$t('form.fields.foil_type')"
            :error="errors.foil_type ?? ''"
            :invalid="!!errors?.foil_type"
            :validated="valid('foil_type')"
            :validating="validating"
        >
            <mono-select
                :options="foilOptions"
                :selected="selectedFoilType"
                @change="onSelectChange('foil_type', $event, validate)"
                :sort="false"
                addon-icon="star"
            />
            <input type="hidden" name="foil_type" :value="selectedFoilType" />
        </form-group>
        <form-group class="button-group">
            <button-group>
                <button type="submit" name="redirect" value="back" class="btn-default" :disabled="processing">
                    <icon name="save" />
                    {{ $t("pages.add_cards.submit") }}
                    <loading-spinner v-if="processing" :size="2" />
                </button>
                <button type="submit" name="redirect" value="add_more" class="btn-primary" :disabled="processing">
                    <icon name="add" />
                    {{ $t("pages.add_cards.submit_and_add_more") }}
                    <loading-spinner v-if="processing" :size="2" />
                </button>
            </button-group>
        </form-group>
    </Form>
</template>

<style lang="scss" scoped>
.badge {
    margin-left: auto;
}
</style>
