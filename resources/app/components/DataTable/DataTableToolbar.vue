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
        <div class="dt-toolbar__search">
            <icon name="search" :size="1" />
            <input
                v-model="query"
                type="search"
                :placeholder="$t('components.datatable.search_placeholder')"
                :aria-label="$t('components.datatable.search')"
                class="dt-toolbar__input"
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
.dt-toolbar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    gap: 1rem;

    padding-block: 0.5rem;

    &__search {
        display: flex;
        align-items: center;

        gap: 0.5rem;
    }

    &__input {
        min-width: 12rem;

        padding: 0.375rem 0.5rem;

        border: 1px solid var(--color-border, #ccc);

        border-radius: 0.25rem;
    }

    &__selection {
        font-size: 0.875rem;
        font-weight: 600;
    }

    &__actions {
        margin-inline-start: auto;
    }
}
</style>
