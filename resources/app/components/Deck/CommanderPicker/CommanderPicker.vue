<script setup lang="ts">
import { onMounted, ref, useTemplateRef, watch } from "vue";
import ColorIdentity from "Components/Card/ColorIdentity.vue";
import ManaCost from "Components/Card/ManaCost.vue";
import SearchSyntax from "Components/Card/SearchSyntax.vue";
import FormGroup from "Components/Form/FormGroup.vue";
import Modal from "Components/Modal/Modal.vue";
import Icon from "Components/UI/Icon.vue";
/** Shape of a single face in a commander search result. */
type CommanderFace = {
    type_line: string;
    mana_cost: string | null;
};
/** Shape of a single commander search result from `/api/commander`. */
type CommanderResult = {
    id: string;
    name: string;
    color_identity: string | null;
    can_have_partner: boolean;
    faces: CommanderFace[];
};
/** @emits close — Fired when the modal should be dismissed. */
defineEmits<{ close: [] }>();
const searchInput = useTemplateRef<HTMLInputElement>("searchInput");
/** Current search query bound to the input. */
const query = ref("");
/** Commander results returned by the API. */
const results = ref<CommanderResult[]>([]);
/** True while an API request is in flight. */
const loading = ref(false);
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
        const response = await fetch(`/api/commander?q=${encodeURIComponent(q)}`);
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
onMounted(() => searchInput.value?.focus());
</script>

<template>
    <modal @close="$emit('close')">
        <template #header>{{ $t("components.commander_picker.title") }}</template>
        <div class="form">
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
            <search-syntax v-if="!results.length" />
            <nav v-if="results.length" class="commander-picker__wrapper">
                <button class="commander-picker__commander" v-for="card in results" :key="card.id">
                    <span class="commander-picker__name">{{ card.name }}</span>
                    <span class="commander-picker__ci">
                        <color-identity :color-identity="card.color_identity" />
                    </span>
                    <span class="commander-picker__faces">
                        <span class="commander-picker__type" v-for="(face, i) in card.faces" :key="i">
                            <span v-if="i > 0"> // </span>
                            {{ face.type_line }} <mana-cost :mana-cost="face.mana_cost" />
                        </span>
                    </span>
                    <span class="commander-picker__partner" v-if="card.can_have_partner">
                        <icon name="register" />
                    </span>
                </button>
            </nav>
            <p v-else-if="query.length >= 2 && !loading">{{ $t("components.commander_picker.no_results") }}</p>
        </div>
    </modal>
</template>
