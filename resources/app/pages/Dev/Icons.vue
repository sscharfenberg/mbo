<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
const iconGlob = import.meta.glob("../../assets/icons/*.svg");
const iconNames = Object.keys(iconGlob)
    .map(p => p.replace(/^.*\/(.+)\.svg$/, "$1"))
    .sort();
</script>

<template>
    <Head><title>Icon Overview</title></Head>
    <headline>Icon Overview ({{ iconNames.length }})</headline>
    <div class="icon-overview">
        <div v-for="name in iconNames" :key="name" class="icon-overview__item">
            <icon :name="name" :size="4" />
            <span class="icon-overview__label">{{ name }}</span>
        </div>
    </div>
</template>

<style scoped lang="scss">
@use "sass:color";

.icon-overview {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(7rem, 1fr));

    gap: 1ch;

    &__item {
        display: flex;
        align-items: center;
        flex-direction: column;

        padding: 1ch;
        gap: 0.5rem;

        background-color: light-dark(color.adjust(#eee, $alpha: -0.5), color.adjust(#222, $alpha: -0.5));
        border-radius: 16px;
    }

    &__label {
        text-align: center;
        word-break: break-all;
    }
}
</style>
