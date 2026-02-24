<template>
    <p><slot /></p>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

p {
    padding: map.get(s.$main, "paragraph", "padding");
    border: map.get(s.$main, "paragraph", "border") solid map.get(c.$main, "paragraph", "border");

    background-color: map.get(c.$main, "paragraph", "background");
    color: map.get(c.$main, "paragraph", "surface");
    border-radius: map.get(s.$main, "paragraph", "radius");
}
</style>
