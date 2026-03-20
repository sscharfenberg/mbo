<script setup lang="ts" generic="T extends { id: string }">
import { nextTick, useTemplateRef } from "vue";
import CurrentSelection from "Components/Card/CardSearch/CurrentSelection.vue";
import Results from "Components/Card/CardSearch/Results.vue";
import SearchSyntax from "Components/Card/SearchSyntax.vue";
import FormGroup from "Components/Form/FormGroup.vue";
import { useCardSearch } from "Composables/useCardSearch.ts";
const props = withDefaults(
    defineProps<{
        refId: string;
        /** API endpoint to search against (e.g. "/api/art-crop"). */
        endpoint: string;
        /** i18n key for the form group label. */
        label: string;
        /** i18n key for the input placeholder. */
        placeholder: string;
        /** Icon shown on the form group addon when nothing is selected. */
        searchIcon?: string;
        /** Icon shown on the form group addon when a card is selected. */
        selectedIcon?: string;
        /** Pre-selected card for edit mode. */
        initialCard?: T | null;
        /** form-group required? **/
        required?: boolean;
    }>(),
    {
        required: false
    }
);
const { searchQuery, results, processing, selectedCard, refValue, onCardSelected, onClearSelection } = useCardSearch<T>(
    props.endpoint
);
if (props.initialCard) {
    selectedCard.value = props.initialCard;
    refValue.value = props.initialCard.id;
}
const searchInput = useTemplateRef<HTMLInputElement>("searchInput");
function onClearAndFocus() {
    onClearSelection();
    nextTick(() => searchInput.value?.focus());
}
</script>

<template>
    <form-group
        :label="$t(label)"
        :addon-icon="selectedCard ? (selectedIcon ?? searchIcon ?? 'image-search') : (searchIcon ?? 'image-search')"
        :validating="processing"
        :required="required"
    >
        <current-selection v-if="selectedCard" @clear="onClearAndFocus">
            <slot name="selected" :card="selectedCard as T" />
        </current-selection>
        <template v-else>
            <input ref="searchInput" type="text" class="form-input" :id="refId" :placeholder="$t(placeholder)" v-model="searchQuery" />
        </template>
        <input type="hidden" :name="refId" :value="refValue" />
        <template v-if="!selectedCard && results.length === 0" #text>
            <search-syntax />
        </template>
    </form-group>
    <Results v-if="results.length > 0" :results="results as T[]" @change="onCardSelected">
        <template #result="{ card }">
            <slot name="result" :card="card as T" />
        </template>
    </Results>
</template>
