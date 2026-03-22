<script setup lang="ts" generic="T extends { id: string }">
import { computed, inject } from "vue";
import Icon from "Components/UI/Icon.vue";
import type { ColumnDef, SortEntry } from "Types/dataTable";
import { DATA_TABLE_KEY } from "Types/dataTable";
const props = defineProps<{
    columns: ColumnDef<T>[];
    sort: SortEntry[];
    selectable: boolean;
    rowIds: string[];
}>();
const emit = defineEmits<{
    sort: [key: string];
}>();

const provided = inject(DATA_TABLE_KEY)!;

/** Determine aria-sort attribute for a column. */
function ariaSort(col: ColumnDef<T>): "ascending" | "descending" | "none" | undefined {
    if (!col.sortable) return undefined;
    const entry = props.sort.find(s => s.key === col.key);
    if (!entry) return "none";
    return entry.direction === "asc" ? "ascending" : "descending";
}

/** Get current sort direction for a column, or null if not sorted. */
function sortDir(col: ColumnDef<T>): "asc" | "desc" | null {
    const entry = props.sort.find(s => s.key === col.key);
    return entry?.direction ?? null;
}

/** Header checkbox state: true = all selected, false = none, 'indeterminate' = some. */
const headerCheckState = computed(() => {
    if (props.rowIds.length === 0) return false;
    const selectedOnPage = props.rowIds.filter(id => provided.selectedIds.value.includes(id));
    if (selectedOnPage.length === 0) return false;
    if (selectedOnPage.length === props.rowIds.length) return true;
    return "indeterminate";
});

function onHeaderCheckbox() {
    provided.togglePageSelection(props.rowIds);
}
</script>

<template>
    <thead class="dt-head">
        <tr>
            <th v-if="selectable" class="dt-head__check">
                <input
                    type="checkbox"
                    :checked="headerCheckState === true"
                    :indeterminate="headerCheckState === 'indeterminate'"
                    @click.stop="onHeaderCheckbox"
                    :aria-label="$t('components.datatable.select_all')"
                />
            </th>
            <th
                v-for="col in columns"
                :key="col.key"
                :style="{ width: col.width ?? 'auto', textAlign: col.align ?? 'left' }"
                :aria-sort="ariaSort(col)"
            >
                <button v-if="col.sortable" type="button" @click="emit('sort', col.key)">
                    {{ col.label }}
                    <icon
                        v-if="sortDir(col) === 'asc'"
                        name="chevron"
                        :size="1"
                        :additional-classes="['dt-head__sort-asc']"
                    />
                    <icon
                        v-else-if="sortDir(col) === 'desc'"
                        name="chevron"
                        :size="1"
                        :additional-classes="['dt-head__sort-desc']"
                    />
                </button>
                <span v-else>{{ col.label }}</span>
            </th>
            <th class="dt-head__actions"><span class="sr-only">{{ $t('components.datatable.actions') }}</span></th>
        </tr>
    </thead>
</template>

<style lang="scss" scoped>
.dt-head {
    position: sticky;
    top: var(--datatable-sticky-offset, 0);

    th {
        text-align: left;

        button {
            display: inline-flex;
            align-items: center;

            gap: 0.25rem;

            cursor: pointer;
        }
    }

    &__check {
        width: 2rem;
    }

    &__sort-asc {
        transform: rotate(180deg);
    }

    &__sort-desc {
        transform: rotate(0deg);
    }

    &__actions {
        width: 3rem;
    }
}
</style>