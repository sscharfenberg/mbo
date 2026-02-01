<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
</script>
<template>
    <Link href="/"><img src="./logo.svg" alt="MtG Binder Organizer Logo" /></Link>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/mixins" as m;
@use "Abstracts/sizes" as s;

img {
    width: 4ch;
    height: auto;

    @include m.mq("portrait") {
        width: 6ch;
    }

    @include m.mq("landscape") {
        width: 8ch;
    }
}
</style>
