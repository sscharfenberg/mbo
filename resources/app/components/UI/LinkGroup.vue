<script setup lang="ts">
defineProps<{
    label: string;
}>();
</script>

<template>
    <nav :aria-label="label"><slot /></nav>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/mixins" as m;

nav {
    display: flex;
    flex-flow: column wrap;

    gap: 1ch;

    @include m.mq("portrait") {
        align-items: center;
        flex-direction: row;

        gap: 1.5ch;
    }
}
</style>
