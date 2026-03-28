<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import DataTable from "Components/DataTable/DataTable.vue";
import Icon from "Components/UI/Icon.vue";
import Paragraph from "Components/UI/Paragraph.vue";
import { useFormatting } from "Composables/useFormatting";
import type { CollectionCardStackRow } from "Types/collectionCardStackRow";
import type { ColumnDef, TableResponse } from "Types/dataTable";
import DeleteCardStackModal from "./shared/DeleteCardStackModal.vue";
defineProps<{
    table: TableResponse<CollectionCardStackRow>;
}>();
const { t } = useI18n();
const { formatPrice } = useFormatting();
const columns = computed<ColumnDef<CollectionCardStackRow>[]>(() => [
    {
        key: "name",
        label: t("pages.collection.columns.name"),
        sortable: true,
        visibleInCard: true,
        cardPrimary: true,
    },
    {
        key: "set_name",
        label: t("pages.collection.columns.set_name"),
        sortable: true,
        visibleInCard: true,
    },
    {
        key: "container_name",
        label: t("pages.collection.columns.container_name"),
        sortable: true,
        visibleInCard: true,
    },
    {
        key: "language",
        label: t("pages.collection.columns.language"),
        visibleInCard: true,
    },
    {
        key: "amount",
        label: t("pages.collection.columns.amount"),
        sortable: true,
        width: "5rem",
        align: "right",
        visibleInCard: true,
    },
    {
        key: "condition",
        label: t("pages.collection.columns.condition"),
        sortable: true,
        visibleInCard: true,
    },
    {
        key: "finish",
        label: t("pages.collection.columns.finish"),
        sortable: true,
    },
    {
        key: "price",
        label: t("pages.collection.columns.price"),
        sortable: true,
        align: "right",
    },
    {
        key: "total_price",
        label: t("pages.collection.columns.total_price"),
        sortable: true,
        align: "right",
        visibleInCard: true,
    },
]);
/** Resolve the flag image URL for a given language code. */
const flagSrc = (lang: string): string => new URL(`../../assets/flags/${lang}.svg`, import.meta.url).href;
/** The card stack row currently targeted for deletion, or null when the modal is hidden. */
const deleteTarget = ref<CollectionCardStackRow | null>(null);
/** Open the delete confirmation modal for the given row. */
const openDeleteModal = (row: CollectionCardStackRow) => {
    deleteTarget.value = row;
};
</script>

<template>
    <data-table :columns="columns" :response="table" :selectable="false" base-url="/collection">
        <template #cell-set_name="{ row }">
            <img
                v-if="row.set_icon"
                :src="row.set_icon"
                :alt="row.set_code"
                class="set"
                v-tooltip="`[${row.set_code.toUpperCase()}] ${row.set_name}`"
            />
        </template>
        <template #cell-container_name="{ row }">
            <template v-if="row.container_name">
                {{ row.container_name }}
            </template>
            <em v-else>{{ $t("pages.collection.unsorted") }}</em>
        </template>
        <template #cell-condition="{ row }">
            <template v-if="row.condition">{{ $t("enums.conditions." + row.condition) }}</template>
        </template>
        <template #cell-finish="{ row }">
            <template v-if="row.finish">{{ $t("enums.finishes." + row.finish) }}</template>
        </template>
        <template #cell-language="{ row }">
            <img
                v-if="row.language"
                :src="flagSrc(row.language)"
                :alt="t('enums.card_languages.' + row.language)"
                class="language"
                v-tooltip="t('enums.card_languages.' + row.language)"
            />
        </template>
        <template #cell-price="{ row }">{{ row.price ? formatPrice(row.price) : "" }}</template>
        <template #cell-total_price="{ row }">{{ row.total_price ? formatPrice(row.total_price) : "" }}</template>
        <template #actions="{ row }">
            <li>
                <button
                    type="button"
                    class="popover-list-item"
                    @click="router.visit(`/collection/cardstack/${row.id}/edit`)"
                >
                    <icon name="edit" :size="1" /> {{ $t("pages.edit_card.link") }}
                </button>
            </li>
            <li>
                <button
                    type="button"
                    class="popover-list-item popover-list-item--caution"
                    @click="openDeleteModal(row)"
                >
                    <icon name="delete" :size="1" />
                    {{ $t("pages.container_page.delete.link") }}
                </button>
            </li>
        </template>
        <template #empty>
            <paragraph>{{ $t("components.datatable.no_results") }}</paragraph>
        </template>
    </data-table>
    <delete-card-stack-modal
        v-if="deleteTarget"
        :card-stack="deleteTarget"
        :container-name="deleteTarget.container_name ?? $t('pages.collection.unsorted')"
        @close="deleteTarget = null"
    />
</template>

<style lang="scss" scoped>
.set {
    width: 1.5rem;
    height: 1.5rem;

    vertical-align: middle;
}

.language {
    width: 27px;
    height: 18px;

    vertical-align: middle;
}
</style>

<style lang="scss">
// doesn't work scoped.
@use "Abstracts/mixins" as m;

@include m.theme-dark(".set") {
    filter: invert(1);
}
</style>