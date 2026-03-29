<script setup lang="ts" generic="T extends { id: string; href?: string }">
import { router } from "@inertiajs/vue3";
import { computed, inject } from "vue";
import Checkbox from "Components/Form/Checkbox.vue";
import Icon from "Components/UI/Icon.vue";
import type { ColumnDef } from "Types/dataTable";
import { DATA_TABLE_KEY } from "Types/dataTable";

const props = defineProps<{
    columns: ColumnDef<T>[];
    rows: T[];
    selectable: boolean;
}>();
const emit = defineEmits<{
    action: [row: T, el: HTMLElement];
}>();

const provided = inject(DATA_TABLE_KEY)!;

/** The primary column shown at the top of each card. First match wins. */
const primaryCol = computed(() => props.columns.find(c => c.cardPrimary) ?? null);

/** Columns visible in card mode, excluding the primary. */
const cardColumns = computed(() => props.columns.filter(c => c.visibleInCard && c.key !== primaryCol.value?.key));

/** Return card columns that have a non-empty value for the given row. */
function visibleCardColumns(row: T) {
    return cardColumns.value.filter(col => {
        const val = row[col.key as keyof T];
        return val !== null && val !== undefined && val !== "" && val !== 0;
    });
}

function onCardClick(row: T) {
    if (row.href) router.visit(row.href);
}

function onActionClick(row: T, event: MouseEvent) {
    emit("action", row, event.currentTarget as HTMLElement);
}
</script>

<template>
    <div class="dt-cards">
        <article
            v-for="row in rows"
            :key="row.id"
            class="dt-cards__card"
            :class="{ 'dt-cards__card--clickable': !!row.href }"
            @click="row.href && onCardClick(row)"
        >
            <div class="dt-cards__header">
                <div v-if="selectable" class="dt-cards__check" @click.stop>
                    <checkbox
                        :ref-id="`dt-card-select-${row.id}`"
                        :checked-initially="provided.selectedIds.value.includes(row.id)"
                        :label="$t('components.datatable.select_row')"
                        @change="provided.toggleSelection(row.id)"
                    />
                </div>
                <div v-if="primaryCol" class="dt-cards__primary">
                    <slot :name="`cell-${primaryCol.key}`" :row="row">
                        {{ row[primaryCol.key] }}
                    </slot>
                </div>
                <button
                    type="button"
                    class="dt-cards__action popover-button popover-button--rounded"
                    :style="{ 'anchor-name': `--dt-action-${row.id}` }"
                    @click.stop="onActionClick(row, $event)"
                    :aria-label="$t('components.datatable.row_actions')"
                >
                    <icon name="more" />
                </button>
            </div>
            <dl class="dt-cards__fields">
                <template v-for="col in visibleCardColumns(row)" :key="col.key">
                    <dt class="dt-cards__label">{{ col.label }}</dt>
                    <dd class="dt-cards__value">
                        <slot :name="`cell-${col.key}`" :row="row">
                            {{ row[col.key] }}
                        </slot>
                    </dd>
                </template>
            </dl>
        </article>
    </div>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

/* Only visible in narrow containers — hidden by DataTable.vue's container query at >= breakpoint */
.dt-cards {
    display: none;
}

@container (max-width: #{map.get(s.$table, "breakpoint") - 1px}) {
    .dt-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(map.get(s.$table, "cards", "min-width"), 1fr));

        gap: map.get(s.$table, "cards", "gap");
    }

    .dt-cards__card {
        padding: map.get(s.$table, "cards", "padding");
        border: map.get(s.$table, "cards", "border") solid map.get(c.$components, "datatable", "cards", "border");

        background-color: map.get(c.$components, "datatable", "cards", "background");
        color: map.get(c.$components, "datatable", "cards", "surface");
        border-radius: map.get(s.$table, "cards", "radius");

        &--clickable {
            cursor: pointer;
        }
    }

    .dt-cards__header {
        display: flex;
        align-items: center;

        margin-bottom: 0.5rem;

        gap: 1ch;
    }

    .dt-cards__primary {
        flex: 1;

        font-weight: 600;
    }

    .dt-cards__action {
        cursor: pointer;
    }

    .dt-cards__fields {
        display: grid;
        grid-template-columns: minmax(30%, auto) 1fr;

        margin: 0;
        gap: 0.25rem 0.5rem;
    }

    .dt-cards__label {
        display: flex;
        align-items: center;
    }

    .dt-cards__value {
        margin: 0;
    }
}
</style>
