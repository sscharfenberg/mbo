<script setup lang="ts">
/**
 * Renders the color identity of a card as a row of mana symbol icons.
 *
 * Splits the `colorIdentity` string (e.g. "RW") into individual letters,
 * orders them in WUBRG order, and renders the corresponding symbol SVG.
 * Colorless cards (null / empty) show the {C} symbol.
 */
import { computed } from "vue";
const props = defineProps<{
    /** Color identity string from the database, e.g. "RW", "WUBRG", or null for colorless. */
    colorIdentity: string | null;
}>();
/** WUBRG order + colorless fallback, mapped to their static symbol paths. */
const SYMBOLS: Record<string, string> = {
    W: "/symbol/W.svg",
    U: "/symbol/U.svg",
    B: "/symbol/B.svg",
    R: "/symbol/R.svg",
    G: "/symbol/G.svg",
    C: "/symbol/C.svg"
};
/** fixed order of color symbols **/
const WUBRG = ["W", "U", "B", "R", "G"];
/** Sorted list of { letter, path } pairs to render. Falls back to colorless. */
const colors = computed(() => {
    const ci = props.colorIdentity;
    if (!ci) {
        return [{ letter: "C", path: SYMBOLS["C"] }];
    }
    return WUBRG.filter(c => ci.includes(c)).map(c => ({ letter: c, path: SYMBOLS[c] }));
});
</script>

<template>
    <span class="color-identity">
        <img
            v-for="color in colors"
            :key="color.letter"
            :src="color.path"
            :alt="color.letter"
            class="color-identity__symbol"
        />
    </span>
</template>

<style scoped lang="scss">
.color-identity {
    display: inline-flex;

    vertical-align: middle;

    &__symbol {
        width: 1em;
        height: 1em;
    }
}
</style>
