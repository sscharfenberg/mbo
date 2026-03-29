<script setup lang="ts" generic="T extends { id: string; href?: string }">
import { router } from "@inertiajs/vue3";
import { inject } from "vue";
import Checkbox from "Components/Form/Checkbox.vue";
import Icon from "Components/UI/Icon.vue";
import type { ColumnDef } from "Types/dataTable";
import { DATA_TABLE_KEY } from "Types/dataTable";
defineProps<{
    columns: ColumnDef<T>[];
    rows: T[];
    selectable: boolean;
}>();
const emit = defineEmits<{
    action: [row: T, el: HTMLElement];
}>();

const provided = inject(DATA_TABLE_KEY)!;

function onRowClick(row: T) {
    if (row.href) router.visit(row.href);
}

function onActionClick(row: T, event: MouseEvent) {
    emit("action", row, event.currentTarget as HTMLElement);
}
</script>

<template>
    <tbody class="dt-body">
        <tr
            v-for="row in rows"
            :key="row.id"
            :class="{ 'dt-body__row--clickable': !!row.href }"
            @click="row.href && onRowClick(row)"
        >
            <td v-if="selectable" class="dt-body__check" @click.stop>
                <checkbox
                    :ref-id="`dt-select-${row.id}`"
                    :checked-initially="provided.selectedIds.value.includes(row.id)"
                    :label="$t('components.datatable.select_row')"
                    @change="provided.toggleSelection(row.id)"
                />
            </td>
            <td v-for="col in columns" :key="col.key" :class="col.cellClass" :style="{ textAlign: col.align ?? 'left' }">
                <slot :name="`cell-${col.key}`" :row="row">
                    {{ row[col.key] }}
                </slot>
            </td>
            <td class="dt-body__actions">
                <button
                    type="button"
                    class="popover-button popover-button--rounded"
                    :style="{ 'anchor-name': `--dt-action-${row.id}` }"
                    @click.stop="onActionClick(row, $event)"
                    :aria-label="$t('components.datatable.row_actions')"
                >
                    <icon name="more" />
                </button>
            </td>
        </tr>
    </tbody>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.dt-body {
    &__row--clickable {
        cursor: pointer;
    }

    &__check {
        width: 2rem;

        vertical-align: middle;
    }

    &__actions {
        width: 3rem;

        button {
            cursor: pointer;
        }
    }

    td {
        padding: map.get(s.$components, "datatable", "padding", "td");

        color: map.get(c.$components, "datatable", "td", "surface");

        &.no-padding {
            padding: 0;
        }

        &:not(:last-child) {
            border-right: map.get(s.$components, "datatable", "border") solid
                map.get(c.$components, "datatable", "border");
        }
    }

    tr:nth-child(odd) td {
        background-color: map.get(c.$components, "datatable", "td", "background", "odd");
    }

    tr:nth-child(even) td {
        background-color: map.get(c.$components, "datatable", "td", "background", "even");
    }

    tr:not(:last-child) td {
        border-bottom: map.get(s.$components, "datatable", "border") solid map.get(c.$components, "datatable", "border");
    }

    tr:last-child {
        td:first-child {
            border-bottom-left-radius: calc(
                map.get(s.$components, "datatable", "radius") - map.get(s.$components, "datatable", "border")
            );
        }

        td:last-child {
            border-bottom-right-radius: calc(
                map.get(s.$components, "datatable", "radius") - map.get(s.$components, "datatable", "border")
            );
        }
    }
}
</style>
