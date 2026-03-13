<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import type { Container } from "Types/container";
import ArtCropImage from "Components/Card/ArtCrop/ArtCropImage.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
defineProps<{ container: Container }>();
</script>

<template>
    <Head
        ><title>{{ container.name }}</title></Head
    >
    <headline>
        <icon name="container-type" :size="3" />
        {{ container.name }}
    </headline>
    <ul class="container-meta">
        <li>{{ container.name }}</li>
        <li v-if="container.description">{{ container.description }}</li>
        <li v-if="container.defaultCard"><art-crop-image :card="container.defaultCard" /></li>
        <li>
            <icon name="container-type" />
            {{ container.type === "other" ? container.custom_type : $t("enums.binder_type." + container.type) }}
        </li>
        <li>255 Cards</li>
        <li>145,56 €</li>
    </ul>
    <pre>{{ container }}</pre>
</template>
