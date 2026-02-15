<script setup lang="ts"></script>
<template>
    <main>
        <section class="inner"><slot /></section>
    </main>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/mixins" as m;
@use "Abstracts/sizes" as s;

main {
    @include m.mqset(
        "padding",
        #{0 map.get(s.$app, "padding", "base")},
        #{0 map.get(s.$app, "padding", "portrait")},
        #{0 map.get(s.$app, "padding", "landscape")},
        #{0 map.get(s.$app, "padding", "desktop")}
    );

    .inner {
        max-width: map.get(s.$app, "cage");
        margin: 0 auto 2lh;
    }
}
</style>
