<script setup lang="ts"></script>
<template>
    <main>
        <section class="frosted-glass inner"><slot /></section>
    </main>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/mixins" as m;
@use "Abstracts/sizes" as s;
@use "Abstracts/z-indexes" as z;

/**
 * z-index 1 is needed here because without it, select options wrap behind the footer:
 *
 *   - footer's backdrop-filter silently creates a stacking context with z-index: auto
 *   - z-index: auto and z-index: 0 are painted in the same step, ordered by DOM position —
 *     footer comes last, so it won
 *   - z-index: 1 on <main> moves it into the next paint step (positive z-index), which always
 *     renders after the auto step regardless of DOM order
 *   - The dropdown's z-index: 6 then resolves normally within <main>'s stacking context
 *
 * Worth knowing for the future: any CSS property that creates a stacking context without an
 * explicit numeric z-index (backdrop-filter, opacity, transform, filter, isolation: isolate) is
 * the silent culprit in most "my z-index isn't working" bugs.
 */
main {
    position: relative;
    z-index: map.get(z.$index, "main");

    width: 100%;

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
