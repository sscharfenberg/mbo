<script setup lang="ts" generic="T extends { id: string; href?: string }">
import { router } from "@inertiajs/vue3";
import { computed, inject } from "vue";
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
const primaryCol = computed(() =>
    props.columns.find((c) => c.cardPrimary) ?? null,
);

/** Columns visible in card mode, excluding the primary. */
const cardColumns = computed(() =>
    props.columns.filter(
        (c) => c.visibleInCard && c.key !== primaryCol.value?.key,
    ),
);

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
                <div v-if="selectable" class="dt-cards__check">
                    <input
                        type="checkbox"
                        :checked="provided.selectedIds.value.includes(row.id)"
                        @click.stop="provided.toggleSelection(row.id)"
                        :aria-label="$t('components.datatable.select_row')"
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
                <template v-for="col in cardColumns" :key="col.key">
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
/* Only visible in narrow containers — hidden by DataTable.vue's container query at >= 640px */
.dt-cards {
    display: none;
}

@container (max-width: 639px) {
    .dt-cards {
        display: flex;
        flex-direction: column;

        gap: 0.75rem;
    }

    .dt-cards__card {
        padding: 0.75rem;

        border: 1px solid var(--color-border, #ccc);

        border-radius: 0.5rem;

        &--clickable {
            cursor: pointer;
        }
    }

    .dt-cards__header {
        display: flex;
        align-items: center;

        margin-block-end: 0.5rem;

        gap: 0.5rem;
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
        grid-template-columns: auto 1fr;

        gap: 0.25rem 0.75rem;
    }

    .dt-cards__label {
        color: var(--color-muted, #666);

        font-size: 0.75rem;
        font-weight: 600;
    }

    .dt-cards__value {
        margin: 0;
    }
}
</style>