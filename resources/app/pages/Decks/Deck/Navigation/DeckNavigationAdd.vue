<script setup lang="ts">
import { ref } from "vue";
import CardAddModal from "@/pages/Decks/Deck/Add/CardAddModal.vue";
import Icon from "Components/UI/Icon.vue";
import type { DeckMeta } from "Types/deckPage.ts";
defineProps<{
    /** Deck metadata (name, format, state, colors, etc.). */
    deck: DeckMeta;
}>();
/** Controls visibility of the full card-add modal. */
const showAddModal = ref(false);
</script>

<template>
    <div class="deck-navigation-add">
        <button type="button" class="btn-default" @click="showAddModal = true">
            <icon name="add" />
            {{ $t("pages.deck.add.label") }}
        </button>
        <div class="deck-navigation-add__quick">
            <input type="text" class="form-input" :placeholder="$t('pages.deck.quick_add.label')" />
        </div>
        <card-add-modal v-if="showAddModal" :deck="deck" @close="showAddModal = false" />
    </div>
</template>

<style lang="scss" scoped>
.deck-navigation-add {
    display: flex;

    gap: 1rem;
}
</style>
