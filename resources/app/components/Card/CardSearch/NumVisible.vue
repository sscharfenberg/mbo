<script setup lang="ts">
defineProps<{
    visibleCount: number;
    numTotalResults: number;
}>();
</script>

<template>
    <div class="results__count-wrapper">
        <span class="results__count">
            <i18n-t keypath="card.search.showing" scope="global">
                <template #visible
                    ><strong>{{ visibleCount }}</strong></template
                >
                <template #total
                    ><strong>{{ numTotalResults }}</strong></template
                >
            </i18n-t>
        </span>
    </div>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.results__count {
    padding: 0.75ex 1.5ch;
    border: map.get(s.$components, "input", "border") solid map.get(c.$components, "input", "border");
    margin: 0 0 2ex;

    background-color: map.get(c.$components, "input", "background");
    color: map.get(c.$components, "input", "surface");
    border-radius: map.get(s.$components, "input", "radius-px");

    &-wrapper {
        display: flex;
        justify-content: flex-end;
    }
}
</style>
