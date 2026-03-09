<script setup lang="ts">
defineProps<{
    visibleCount: number;
    numTotalResults: number;
}>();
</script>

<template>
    <div class="results__count-wrapper">
        <span class="results__count">
            <i18n-t keypath="form.fields.card_image.showing" scope="global">
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
    border: map.get(s.$form, "input", "border") solid map.get(c.$form, "input", "border");
    margin: 0 0 2ex;

    background-color: map.get(c.$form, "input", "background");
    color: map.get(c.$form, "input", "surface");
    border-radius: map.get(s.$form, "input", "radius-px");

    &-wrapper {
        display: flex;
        justify-content: flex-end;
    }
}
</style>
