<script setup lang="ts">
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import DataTable from "Components/DataTable/DataTable.vue";
import type { CardStackRow } from "Types/cardStackRow";
import type { ColumnDef, TableResponse } from "Types/dataTable";

defineProps<{
    table: TableResponse<CardStackRow>;
    baseUrl: string;
}>();

const { t } = useI18n();

const columns = computed<ColumnDef<CardStackRow>[]>(() => [
    {
        key: "name",
        label: t("pages.container_page.columns.name"),
        sortable: true,
        visibleInCard: true,
        cardPrimary: true,
    },
    {
        key: "set_name",
        label: t("pages.container_page.columns.set_name"),
        sortable: true,
        visibleInCard: true,
    },
    {
        key: "collector_number",
        label: t("pages.container_page.columns.collector_number"),
        sortable: true,
        width: "5rem",
        align: "right",
    },
    {
        key: "amount",
        label: t("pages.container_page.columns.amount"),
        sortable: true,
        width: "5rem",
        align: "right",
        visibleInCard: true,
    },
    {
        key: "condition",
        label: t("pages.container_page.columns.condition"),
        sortable: true,
        visibleInCard: true,
    },
    {
        key: "foil_type",
        label: t("pages.container_page.columns.foil_type"),
        sortable: true,
    },
    {
        key: "language",
        label: t("pages.container_page.columns.language"),
        sortable: true,
        visibleInCard: true,
    },
]);
</script>

<template>
    <data-table
        :columns="columns"
        :response="table"
        :selectable="true"
        :base-url="baseUrl"
    >
        <template #toolbar-actions="{ selectedIds }">
            <button
                v-if="selectedIds.length > 0"
                type="button"
                class="popover-button"
                @click="console.log('Move selected cards:', selectedIds)"
            >
                Move selected cards to…
            </button>
        </template>
        <template #cell-condition="{ row }">
            <template v-if="row.condition">{{ $t('enums.conditions.' + row.condition) }}</template>
        </template>
        <template #cell-foil_type="{ row }">
            <template v-if="row.foil_type">{{ $t('enums.foil_types.' + row.foil_type) }}</template>
        </template>
        <template #cell-language="{ row }">
            {{ $t('enums.card_languages.' + row.language) }}
        </template>
        <template #actions>
            <li><button type="button" class="popover-list-item">Edit</button></li>
            <li><button type="button" class="popover-list-item popover-list-item--caution">Delete</button></li>
        </template>
        <template #empty>
            <p>{{ $t('pages.container_page.empty') }}</p>
        </template>
    </data-table>
</template>