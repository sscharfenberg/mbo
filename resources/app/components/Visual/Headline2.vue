<template>
    <h2><slot /></h2>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/sizes" as s;
@use "Abstracts/typography" as t;
@use "Abstracts/colors" as c;

h2 {
    border-bottom: map.get(s.$main, "headline2", "border") solid map.get(c.$main, "headline2", "border");
    margin: 0 0 0.5lh;

    color: map.get(c.$main, "headline2", "surface");

    font-family: map.get(t.$main, "headline2");
    font-size: map.get(s.$main, "headline2", "font");
    font-weight: 200;
}
</style>
