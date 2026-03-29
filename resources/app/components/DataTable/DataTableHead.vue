<script setup lang="ts" generic="T extends { id: string }">
import { computed, inject } from "vue";
import Checkbox from "Components/Form/Checkbox.vue";
import type { ColumnDef, SortEntry } from "Types/dataTable";
import { DATA_TABLE_KEY } from "Types/dataTable";
const props = defineProps<{
    columns: ColumnDef<T>[];
    sort: SortEntry[];
    selectable: boolean;
    rowIds: string[];
    stuck: boolean;
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
    <thead class="dt-head" :class="{ 'dt-head--stuck': stuck }">
        <tr>
            <th v-if="selectable" class="dt-head__check" @click.stop>
                <checkbox
                    ref-id="dt-select-all"
                    :checked-initially="headerCheckState === true"
                    :indeterminate="headerCheckState === 'indeterminate'"
                    :label="$t('components.datatable.select_all')"
                    @change="onHeaderCheckbox"
                />
            </th>
            <th
                v-for="col in columns"
                :key="col.key"
                :style="{ width: col.width ?? 'auto' }"
                :aria-sort="ariaSort(col)"
            >
                <button
                    v-if="col.sortable"
                    type="button"
                    class="dt-head__sort-btn"
                    :class="{
                        'dt-head__sort-btn--asc': sortDir(col) === 'asc',
                        'dt-head__sort-btn--desc': sortDir(col) === 'desc'
                    }"
                    @click="emit('sort', col.key)"
                    :style="{ textAlign: col.align ?? 'left' }"
                >
                    {{ col.label }}
                </button>
                <span v-else :style="{ textAlign: col.align ?? 'left' }">{{ col.label }}</span>
            </th>
            <th class="dt-head__actions">
                <span class="sr-only">{{ $t("components.datatable.actions") }}</span>
            </th>
        </tr>
    </thead>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@use "Abstracts/z-indexes" as z;

.dt-head {
    position: sticky;
    top: var(--datatable-sticky-offset, 0);
    z-index: map.get(z.$index, "main");

    &__check {
        width: 2rem;
    }

    &__sort-btn {
        position: relative;

        padding-right: 1.25rem !important; /* space for the triangles */

        &::before,
        &::after {
            position: absolute;
            right: 0.4rem;

            opacity: 0.3;

            width: 0;
            height: 0;
            border-right: 0.3125rem solid transparent;
            border-left: 0.3125rem solid transparent;

            content: "";
        }

        /* Asc triangle (pointing up) — top */
        &::before {
            top: calc(50% - 0.465rem);

            border-bottom: 0.375rem solid currentcolor;
        }

        /* Desc triangle (pointing down) — bottom */
        &::after {
            bottom: calc(50% - 0.465rem);

            border-top: 0.375rem solid currentcolor;
        }

        &--asc::before {
            opacity: 1;
        }

        &--desc::after {
            opacity: 1;
        }
    }

    &__actions {
        width: 3rem;
    }

    th {
        border-bottom: map.get(s.$table, "border") solid map.get(c.$components, "datatable", "border");

        background-color: map.get(c.$components, "datatable", "th", "background");

        text-align: left;

        &:not(:last-child) {
            border-right: map.get(s.$table, "border") solid map.get(c.$components, "datatable", "border");
        }

        &:first-child {
            border-top-left-radius: calc(map.get(s.$table, "radius") - map.get(s.$table, "border"));
        }

        &:last-child {
            border-top-right-radius: calc(map.get(s.$table, "radius") - map.get(s.$table, "border"));
        }

        &:not(:has(button)) {
            padding: map.get(s.$table, "padding", "th");
        }

        button {
            width: 100%;
            height: 100%;
            padding: map.get(s.$table, "padding", "th");
            border: 0;
            gap: 0.25rem;

            background: transparent;

            cursor: pointer;
        }

        button,
        span {
            color: map.get(c.$components, "datatable", "th", "surface");

            font-weight: normal;
        }
    }

    &--stuck {
        th:first-child {
            border-top-left-radius: 0;
        }

        th:last-child {
            border-top-right-radius: 0;
        }

        th {
            z-index: 2;

            background-color: map.get(c.$components, "datatable", "th", "background-stuck");
            color: map.get(c.$components, "datatable", "th", "surface-stuck");
        }
    }
}
</style>
