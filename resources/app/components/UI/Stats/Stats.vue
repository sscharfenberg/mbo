<template>
    <ul class="stats">
        <slot />
    </ul>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(200px, 100%), 1fr));

    padding: 0;
    margin: 0;
    gap: 1ch;

    list-style: none;
}
</style>
