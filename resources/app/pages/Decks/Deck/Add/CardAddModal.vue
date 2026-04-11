<script setup lang="ts">
import { onMounted, ref, useTemplateRef, watch } from "vue";
import { useDeckSearch } from "@/composables/useDeckSearch";
import SearchSyntax from "Components/Card/SearchSyntax.vue";
import FormGroup from "Components/Form/FormGroup.vue";
import Switch from "Components/Form/Switch.vue";
import Modal from "Components/Modal/Modal.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import Paragraph from "Components/UI/Paragraph.vue";
import type { DeckMeta } from "Types/deckPage.ts";
const props = defineProps<{
    /** Deck metadata — used to scope card search by format and color identity. */
    deck: DeckMeta;
}>();
/** @emits close — Fired when the modal finishes its close animation. */
const emit = defineEmits<{ close: [] }>();
/** Deck-scoped card search state and actions (printings endpoint). */
const { query, results, processing, searchPrintings, reset } = useDeckSearch(props.deck.id);
/** When true, the printings endpoint is called with `include_non_legal=1`. */
const includeNonLegal = ref(false);
/** Debounce timer for auto-search on input changes. */
let debounceTimer: ReturnType<typeof setTimeout> | null = null;
/** Template ref for focusing the search input on mount. */
const searchInput = useTemplateRef<HTMLInputElement>("searchInput");
/**
 * Fire the search immediately, cancelling any pending debounce.
 * Used by the form submit handler and the non-legal toggle.
 */
const runSearch = () => {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
        debounceTimer = null;
    }
    searchPrintings(query.value, { includeNonLegal: includeNonLegal.value });
};
/** Debounce input changes — wait 750ms after the last keystroke before searching. */
watch(query, () => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(runSearch, 750);
});
/** Re-run the search when the user toggles the non-legal switch. */
const onIncludeNonLegalChange = (checked: boolean) => {
    includeNonLegal.value = checked;
    runSearch();
};
/** Close the modal and clear search state so reopening starts fresh. */
const onClose = () => {
    reset();
    emit("close");
};
onMounted(() => searchInput.value?.focus());
</script>

<template>
    <modal @close="onClose">
        <template #header>
            {{ $t("pages.deck.add.title") }}
        </template>
        <form class="card-add-modal" @submit.prevent="runSearch">
            <form-group
                :label="$t('pages.deck.add.search_label')"
                :validating="processing"
                addon-icon="search"
                for-id="card_add_query"
            >
                <input
                    ref="searchInput"
                    v-model="query"
                    type="text"
                    name="card_add_query"
                    id="card_add_query"
                    class="form-input"
                    autocomplete="off"
                />
            </form-group>
            <form-group for-id="card_add_include_non_legal" :label="$t('pages.deck.add.include_non_legal')">
                <template #addon>
                    <Switch
                        ref-id="card_add_include_non_legal"
                        :label="$t('pages.deck.add.include_non_legal')"
                        :checked-initially="includeNonLegal"
                        @change="onIncludeNonLegalChange"
                    />
                </template>
            </form-group>
            <form-group>
                <button type="submit" class="btn-primary" :disabled="processing">
                    <icon name="search" />
                    {{ $t("pages.deck.add.submit") }}
                    <loading-spinner v-if="processing" :size="2" />
                </button>
            </form-group>
            <search-syntax v-if="!results.length && !processing" />
            <pre v-if="results.length" class="card-add-modal__dump">{{ JSON.stringify(results, null, 2) }}</pre>
            <paragraph v-else-if="query.length >= 2 && !processing">
                {{ $t("pages.deck.add.no_results") }}
            </paragraph>
        </form>
    </modal>
</template>

<style lang="scss" scoped>
.card-add-modal {
    display: flex;
    flex-direction: column;

    gap: 1rem;

    &__dump {
        overflow: auto;
        max-height: 50dvh;
        padding: 1rem;

        background: rgb(0 0 0 / 30%);
        border-radius: 0.5rem;

        font-family: monospace;
        font-size: 0.85rem;
    }
}
</style>
