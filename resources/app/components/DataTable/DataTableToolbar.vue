<script setup lang="ts">
import { ref, watch } from "vue";
import Icon from "Components/UI/Icon.vue";

const props = defineProps<{
    search: string | null;
    selectedCount: number;
}>();
const emit = defineEmits<{
    search: [query: string];
}>();

const query = ref(props.search ?? "");
let debounceTimer: ReturnType<typeof setTimeout> | null = null;

watch(query, value => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        emit("search", value);
    }, 350);
});

/** Sync external search prop back to local input (e.g. on Inertia navigation). */
watch(
    () => props.search,
    value => {
        query.value = value ?? "";
    }
);
</script>

<template>
    <div class="dt-toolbar">
        <div class="form-group">
            <div class="form-group__addon"><icon name="search" /></div>
            <input
                v-model="query"
                type="search"
                :placeholder="$t('components.datatable.search_placeholder')"
                :aria-label="$t('components.datatable.search')"
                class="form-input"
            />
        </div>
        <span v-if="selectedCount > 0" class="dt-toolbar__selection">
            {{ $t("components.datatable.items_selected", { count: selectedCount }) }}
        </span>
        <div v-if="$slots.actions" class="dt-toolbar__actions">
            <slot name="actions" />
        </div>
    </div>
</template>

<style lang="scss" scoped>
@use "Abstracts/mixins" as m;

.dt-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;

    gap: 1rem;

    padding-block: 0.5rem;

    &__selection {
        margin-inline-start: auto;
    }

    .form-group {
        display: flex;
        flex-wrap: nowrap;

        min-width: 0;
        max-width: 12rem;
        gap: 0;

        @include m.mq("portrait") {
            max-width: 18rem;
        }

        @include m.mq("landscape") {
            max-width: 24rem;
        }
    }
}
</style>
