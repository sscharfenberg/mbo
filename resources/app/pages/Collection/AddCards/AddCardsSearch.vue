<script setup lang="ts">
import CardFaceImage from "Components/Card/CardFaceImage.vue";
import CardSearch from "Components/Card/CardSearch/CardSearch.vue";
import type { DefaultCardImage } from "Types/defaultCardImage";
</script>

<template>
    <card-search
        ref-id="search"
        endpoint="/api/card-image"
        label="form.fields.card"
        placeholder="card.search.placeholder.face"
        search-icon="image-search"
        :initial-card="(null as DefaultCardImage | null)"
    >
        <template #result="{ card }">
            <card-face-image :card="card" interactive />
        </template>
        <template #selected="{ card }">
            <card-face-image :card="card" />
        </template>
    </card-search>
</template>
