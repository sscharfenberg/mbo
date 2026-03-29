<template>
    <p><slot /></p>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

p {
    padding: map.get(s.$components, "paragraph", "padding");
    border: map.get(s.$components, "paragraph", "border") solid map.get(c.$components, "paragraph", "border");

    background-color: map.get(c.$components, "paragraph", "background");
    color: map.get(c.$components, "paragraph", "surface");
    border-radius: map.get(s.$components, "paragraph", "radius");
}
</style>
