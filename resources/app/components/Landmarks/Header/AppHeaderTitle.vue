<template>
    <h1>MBO</h1>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/mixins" as m;
@use "Abstracts/sizes" as s;
@use "Abstracts/colors" as c;
@use "Abstracts/shadows" as sh;

h1 {
    position: relative;

    padding: 0;
    margin: 0;

    transform: skew(-15deg);

    font-weight: 900;
    line-height: 1;
    letter-spacing: 0.03em;

    @include m.mqset(
        "font",
        #{map.get(s.$header, "font-size", "base")},
        #{map.get(s.$header, "font-size", "portrait")},
        #{map.get(s.$header, "font-size", "landscape")},
        #{map.get(s.$header, "font-size", "desktop")}
    );
}
</style>
