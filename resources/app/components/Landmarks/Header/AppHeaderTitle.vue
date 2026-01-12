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
        "font-size",
        #{map.get(s.$header, "font", "base")},
        #{map.get(s.$header, "font", "portrait")},
        #{map.get(s.$header, "font", "landscape")},
        #{map.get(s.$header, "font", "desktop")}
    );
}
</style>
