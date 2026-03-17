<script setup lang="ts">
import { ref, watch } from "vue";
import FormGroup from "Components/Form/FormGroup.vue";
/** The current text in the search input, bound via v-model. */
const searchQuery = ref("");
/** True while a search XHR is in flight. */
const processing = ref(false);
/** Timer handle for debouncing search input. */
let debounceTimer: ReturnType<typeof setTimeout> | null = null;
/**
 * Fetch matching cards from the API.
 * Clears results if the query is empty.
 *
 * @param query - The search string entered by the user.
 */
async function searchCards(query: string) {
    if (!query.trim()) {
        return;
    }
    processing.value = true;
    const response = await fetch(`/api/card-search?q=${encodeURIComponent(query)}`);
    if (response.ok) {
        const data = await response.json();
        if (data) {
            // TODO: store results
        }
    }
    processing.value = false;
}
/** Debounce search input changes by 500 ms before calling the API. */
watch(searchQuery, query => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => searchCards(query), 500);
});
</script>

<template>
    <form-group
        for-id="search"
        :label="$t('form.fields.card_search')"
        addon-icon="image-search"
        :required="true"
        :validating="processing"
    >
        <input type="text" name="search" id="search" class="form-input" v-model="searchQuery" />
    </form-group>
</template>
