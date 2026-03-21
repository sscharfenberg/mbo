<script setup lang="ts">
import CardFaceImage from "Components/Card/CardFaceImage.vue";
import CardSearch from "Components/Card/CardSearch/CardSearch.vue";
import type { DefaultCardImage } from "Types/defaultCardImage";
defineProps<{
    /** Validation error message for the default_card_id field. */
    error?: string;
    /** Whether the field is in an invalid state. */
    invalid?: boolean;
}>();
const initialCard: DefaultCardImage | null = null;
</script>

<template>
    <card-search
        ref-id="default_card_id"
        endpoint="/api/card-image"
        label="form.fields.card"
        placeholder="card.search.placeholder.face"
        search-icon="image-search"
        selected-icon="container-image"
        :initial-card="initialCard"
        :required="true"
        :error="error"
        :invalid="invalid"
    >
        <template #result="{ card }">
            <card-face-image :card="card as DefaultCardImage" interactive />
        </template>
        <template #selected="{ card }">
            <card-face-image :card="card as DefaultCardImage" />
        </template>
    </card-search>
</template>
