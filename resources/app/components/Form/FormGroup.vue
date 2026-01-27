<script setup lang="ts">
defineProps({
    forId: String,
    label: String
});
</script>

<template>
    <div class="form-group">
        <label v-if="label?.length" :for="forId">{{ label }}</label>
        <span v-else class="label" />
        <div class="input"><slot /></div>
    </div>
</template>

<style lang="scss" scoped>
@use "Abstracts/mixins" as m;

.form-group {
    display: flex;
    flex-flow: column wrap;

    gap: 1ex;

    @include m.mq("landscape") {
        flex-flow: row wrap;

        gap: 2ch;
    }

    label,
    span.label {
        flex-grow: 1;
    }

    .input {
        flex: 0 0 100%;

        @include m.mq("landscape") {
            flex: 0 0 65%;
            gap: 2ch;
        }
    }
}
</style>
