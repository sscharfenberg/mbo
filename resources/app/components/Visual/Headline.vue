<script setup lang="ts">
withDefaults(
    defineProps<{
        size?: 2 | 3;
    }>(),
    {
        size: 2
    }
);
</script>

<template>
    <h2 v-if="size === 2"><slot /></h2>
    <h3 v-if="size === 3"><slot /></h3>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/sizes" as s;
@use "Abstracts/typography" as t;
@use "Abstracts/colors" as c;

h2 {
    border-bottom: map.get(s.$main, "h2", "border") solid map.get(c.$main, "h2", "border");
    margin: 0 0 0.5lh;

    color: map.get(c.$main, "h2", "surface");

    font-family: map.get(t.$main, "h2");
    font-size: map.get(s.$main, "h2", "font");
    font-weight: 200;
}

h3 {
    border-bottom: map.get(s.$main, "h3", "border") solid map.get(c.$main, "h3", "border");
    margin: 0 0 0.5lh;

    color: map.get(c.$main, "h3", "surface");

    font-family: map.get(t.$main, "h3");
    font-size: map.get(s.$main, "h3", "font");
    font-weight: 200;
}
</style>
