<script setup lang="ts">
import Icon from "Components/UI/Icon.vue";
import type { DeckMeta } from "Types/deckPage.ts";
defineProps<{
    /** Deck metadata (name, format, state, colors, etc.). */
    deck: DeckMeta;
}>();
</script>

<template>
    <div class="deck-navigation-add">
        <button type="button" class="btn-default">
            <icon name="add" />
            {{ $t("pages.deck.add.label") }}
        </button>
        <div class="deck-navigation-add__quick">
            <input type="text" class="form-input" :placeholder="$t('pages.deck.quick_add.label')" />
        </div>
    </div>
</template>

<style lang="scss" scoped>
.deck-navigation-add {
    display: flex;

    gap: 1rem;
}
</style>
