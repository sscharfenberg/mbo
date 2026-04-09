<script setup lang="ts">
import ColorIdentity from "Components/Card/ColorIdentity.vue";
import type { DeckMeta } from "Types/deckPage.ts";

defineProps<{
    /** Deck metadata (name, format, state, colors, etc.). */
    deck: DeckMeta;
}>();
</script>

<template>
    <section class="deck-meta">
        <color-identity class="decklist__colors" :color-identity="deck.colors" />
        <header>{{ deck.name.toUpperCase() }}</header>
        {{ deck }}
    </section>
</template>
