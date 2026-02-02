<script setup lang="ts"></script>

<template>
    <h2><slot /></h2>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/typography" as t;
@use "Abstracts/colors" as c;

h2 {
    margin: 0 0 0.5lh;

    color: map.get(c.$main, "headline");

    font-family: map.get(t.$main, "headline");
    font-size: 2rem;
    font-weight: 200;
}
</style>
