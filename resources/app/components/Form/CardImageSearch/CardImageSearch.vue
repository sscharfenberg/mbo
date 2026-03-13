<script setup lang="ts">
import type { DefaultCardArtCrop } from "Types/defaultCardArtCrop";
import { ref, watch } from "vue";
import CurrentSelection from "Components/Form/CardImageSearch/CurrentSelection.vue";
import Results from "Components/Form/CardImageSearch/Results.vue";
import FormGroup from "Components/Form/FormGroup.vue";
import Paragraph from "Components/UI/Paragraph.vue";
const props = defineProps<{
    refId: string;
    /** Pre-selected card for edit mode. When provided, the selection UI shows immediately. */
    initialCard?: DefaultCardArtCrop | null;
}>();
/** The selected card id submitted with the form. */
const refValue = ref(props.initialCard?.id ?? "");
/** The currently selected card result. */
const selectedCard = ref<DefaultCardArtCrop | null>(props.initialCard ?? null);
/** The current text in the search input, bound via v-model. */
const searchQuery = ref("");
/** Card results returned by the search endpoint. */
const results = ref<DefaultCardArtCrop[]>([]);
/** True while a search XHR is in flight. */
const processing = ref(false);
/** Timer handle for debouncing search input. */
let debounceTimer: ReturnType<typeof setTimeout> | null = null;
/**
 * Called when the user clicks a result. Stores the selection and clears
 * the results list so the search UI is replaced by CurrentSelection.
 */
function onCardSelected(card: DefaultCardArtCrop) {
    selectedCard.value = card;
    refValue.value = card.id;
    results.value = [];
}
/**
 * Called when the user clicks "Change selection". Resets the selected
 * card, the hidden form value, and the search query.
 */
function onClearSelection() {
    selectedCard.value = null;
    refValue.value = "";
    searchQuery.value = "";
}
/**
 * Fetch matching cards from the API and populate results.
 * Clears results if the query is empty.
 *
 * @param query - The search string entered by the user.
 */
async function searchCards(query: string) {
    if (!query.trim()) {
        results.value = [];
        return;
    }
    processing.value = true;
    const response = await fetch(`/api/card-image/search?q=${encodeURIComponent(query)}`);
    if (response.ok) {
        const data = await response.json();
        if (data) results.value = data;
    }
    processing.value = false;
}
/** Debounce search input changes by 300 ms before calling the API. */
watch(searchQuery, query => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => searchCards(query), 500);
});
</script>

<template>
    <form-group :label="$t('form.fields.container.image')" addon-icon="container-image" :validating="processing">
        <current-selection v-if="selectedCard" :card="selectedCard" @clear="onClearSelection" />
        <template v-else>
            <input
                type="text"
                class="form-input"
                :id="refId"
                :placeholder="$t('form.fields.card_image.search_input')"
                v-model="searchQuery"
            />
        </template>
        <input type="hidden" :name="refId" :value="refValue" />
        <template v-if="results.length > 0" #text>
            <Results :results="results" @change="onCardSelected" />
        </template>
        <template v-else-if="!selectedCard" #text>
            <paragraph>
                <i18n-t keypath="form.fields.card_image.search_tips" scope="global">
                    <template #query
                        ><strong>{{ $t("form.fields.card_image.query") }}</strong></template
                    >
                    <template #result
                        ><strong>{{ $t("form.fields.card_image.result") }}</strong></template
                    >
                    <template #set
                        ><strong>{{ $t("form.fields.card_image.set") }}</strong></template
                    >
                    <template #set_example
                        ><strong>{{ $t("form.fields.card_image.set_example") }}</strong></template
                    >
                    <template #ecl
                        ><strong>{{ $t("form.fields.card_image.ecl") }}</strong></template
                    >
                    <template #both
                        ><strong>{{ $t("form.fields.card_image.both") }}</strong></template
                    >
                    <template #both_result
                        ><strong>{{ $t("form.fields.card_image.both_result") }}</strong></template
                    >
                </i18n-t>
            </paragraph>
        </template>
    </form-group>
</template>
