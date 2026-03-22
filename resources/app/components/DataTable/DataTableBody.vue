<script setup lang="ts" generic="T extends { id: string; href?: string }">
import { router } from "@inertiajs/vue3";
import { inject } from "vue";
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
            <td v-if="selectable" class="dt-body__check">
                <input
                    type="checkbox"
                    :checked="provided.selectedIds.value.includes(row.id)"
                    @click.stop="provided.toggleSelection(row.id)"
                    :aria-label="$t('components.datatable.select_row')"
                />
            </td>
            <td
                v-for="col in columns"
                :key="col.key"
                :style="{ textAlign: col.align ?? 'left' }"
            >
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
.dt-body {
    &__row--clickable {
        cursor: pointer;
    }

    &__check {
        width: 2rem;
    }

    &__actions {
        width: 3rem;

        button {
            cursor: pointer;
        }
    }
}
</style>