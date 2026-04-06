<script setup lang="ts">
import { nextTick, onMounted, ref, useTemplateRef, watch } from "vue";
import SearchSyntax from "Components/Card/SearchSyntax.vue";
import type { CommanderResult } from "Components/Deck/ShowCommanderOverview.vue";
import ShowCommanderOverview from "Components/Deck/ShowCommanderOverview.vue";
import Checkbox from "Components/Form/Checkbox.vue";
import FormGroup from "Components/Form/FormGroup.vue";
import Modal from "Components/Modal/Modal.vue";
import Icon from "Components/UI/Icon.vue";

/** @emits close — Fired when the modal should be dismissed. */
defineEmits<{ close: [] }>();
const searchInput = useTemplateRef<HTMLInputElement>("searchInput");
/** Current search query bound to the input. */
const query = ref("");
/** Commander results returned by the API. */
const results = ref<CommanderResult[]>([]);
/** True while an API request is in flight. */
const loading = ref(false);
/** When true, skip commander-legality filters (Rule 0). */
const rule0 = ref(false);
let debounceTimer: ReturnType<typeof setTimeout> | null = null;
/**
 * Fetch commanders matching the given query string.
 * Skips the request when the query is shorter than 2 characters.
 *
 * @param q - The search query.
 */
const fetchCommanders = async (q: string) => {
    if (q.length < 2) {
        results.value = [];
        return;
    }
    loading.value = true;
    try {
        const params = new URLSearchParams({ q });
        if (rule0.value) params.set("rule0", "1");
        const response = await fetch(`/api/commander?${params}`);
        if (response.ok) {
            results.value = await response.json();
        }
    } finally {
        loading.value = false;
    }
};
/** Debounce search input — waits 500ms after the last keystroke before fetching. */
watch(query, value => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => fetchCommanders(value), 500);
});
/** Re-fetch immediately when Rule 0 is toggled (if there is a query). */
watch(rule0, () => fetchCommanders(query.value));
onMounted(() => searchInput.value?.focus());

/** The currently selected commander, or null when nothing is picked yet. */
const selected = ref<CommanderResult | null>(null);

/** Store the picked commander and clear the search state. */
const onCommanderSelected = (card: CommanderResult) => {
    selected.value = card;
    query.value = "";
    results.value = [];
};

/** Clear the selection so the user can search again. */
const clearSelection = async () => {
    selected.value = null;
    await nextTick();
    searchInput.value?.focus();
};
</script>

<template>
    <modal @close="$emit('close')">
        <template #header>{{ $t("components.commander_picker.title") }}</template>
        <div class="form">
            <template v-if="selected">
                <form-group :label="$t('components.commander_picker.selected')">
                    <div class="commander-picker__commander commander-picker__commander--selected">
                        <show-commander-overview :card="selected" />
                    </div>
                </form-group>
                <form-group>
                    <button type="button" class="btn-default" @click="clearSelection">
                        <icon name="register" />
                        {{ $t("components.commander_picker.change") }}
                    </button>
                </form-group>
            </template>
            <template v-else>
                <form-group
                    :label="$t('pages.add_deck.commander.search')"
                    :required="true"
                    addon-icon="register"
                    for-id="commander_id"
                >
                    <input
                        ref="searchInput"
                        v-model="query"
                        type="text"
                        name="commander_id"
                        id="commander_id"
                        class="form-input"
                        autocomplete="off"
                    />
                </form-group>
                <form-group>
                    <label class="rule0" for="include_rule0">
                        <checkbox ref-id="include_rule0" @change="rule0 = $event" />
                        {{ $t("components.commander_picker.include_rule0") }}
                    </label>
                </form-group>
                <search-syntax v-if="!results.length" />
                <nav v-if="results.length" class="commander-picker__wrapper">
                    <button
                        class="commander-picker__commander"
                        v-for="card in results"
                        :key="card.id"
                        @click="onCommanderSelected(card)"
                    >
                        <show-commander-overview :card="card" />
                    </button>
                </nav>
                <p v-else-if="query.length >= 2 && !loading">{{ $t("components.commander_picker.no_results") }}</p>
            </template>
        </div>
    </modal>
</template>

<style lang="scss" scoped>
.rule0 {
    display: flex;
    align-items: center;

    gap: 1rem;
}
</style>
