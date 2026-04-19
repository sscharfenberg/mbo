<script setup lang="ts">
import { ref, useId } from "vue";
import DeckAddGroupModal from "@/pages/Decks/Deck/Add/DeckAddGroupModal.vue";
import Icon from "Components/UI/Icon.vue";
import PopOver from "Components/UI/PopOver.vue";
import type { DeckCardRow, DeckCategoryRow, DeckMeta } from "Types/deckPage.ts";
import DeckCustomGroupsModal from "./DeckCustomGroupsModal.vue";
const props = defineProps<{
    /** Deck metadata (name, format, state, colors, etc.). */
    deck: DeckMeta;
    /** All cards in the deck. */
    cards: DeckCardRow[];
    /** User-defined categories for this deck. */
    categories: DeckCategoryRow[];
    /** Maximum length for a category name. */
    categoryNameMax: number;
}>();
const popoverId = useId();
const showCustomGroupsModal = ref(false);
const showCreateGroupModal = ref(false);
/** Close the action popover programmatically. */
function closePopover(): void {
    const dialog = document.getElementById(popoverId);
    if (dialog !== null) dialog.hidePopover();
}
/** Open the custom groups modal and close the popover. */
function openCustomGroups(): void {
    closePopover();
    showCustomGroupsModal.value = true;
}
/** Open the create group modal and close the popover. */
function openCreateGroup(): void {
    closePopover();
    showCreateGroupModal.value = true;
}
</script>

<template>
    <pop-over icon="more" aria-label="test" class-string="popover-button--rounded" :reference="popoverId" width="14rem">
        <ul class="popover-list">
            <li>
                <button class="popover-list-item" @click="openCreateGroup">
                    <icon name="add" :size="1" />
                    {{ $t("pages.deck.create_group.link") }}
                </button>
            </li>
            <li>
                <button class="popover-list-item" @click="openCustomGroups">
                    <icon name="edit" :size="1" />
                    {{ $t("pages.deck.custom_groups.link") }}
                </button>
            </li>
        </ul>
    </pop-over>
    <deck-custom-groups-modal
        v-if="showCustomGroupsModal"
        :deck-id="props.deck.id"
        :cards="props.cards"
        :categories="props.categories"
        :category-name-max="props.categoryNameMax"
        @close="showCustomGroupsModal = false"
    />
    <deck-add-group-modal
        v-if="showCreateGroupModal"
        :deck-id="props.deck.id"
        :category-name-max="props.categoryNameMax"
        @close="showCreateGroupModal = false"
    />
</template>
