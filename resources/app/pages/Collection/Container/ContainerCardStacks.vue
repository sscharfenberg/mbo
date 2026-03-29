<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import CardImagePreview from "Components/Card/CardImagePreview.vue";
import DataTable from "Components/DataTable/DataTable.vue";
import Icon from "Components/UI/Icon.vue";
import Paragraph from "Components/UI/Paragraph.vue";
import PopOver from "Components/UI/PopOver.vue";
import { useFormatting } from "Composables/useFormatting";
import type { CardStackRow } from "Types/cardStackRow";
import type { ContainerListItem } from "Types/containerListItem";
import type { ColumnDef, TableResponse } from "Types/dataTable";
import DeleteCardStackModal from "../shared/DeleteCardStackModal.vue";
import DeleteSelectedCardStacksModal from "../shared/DeleteSelectedCardStacksModal.vue";
import MoveSelectedCardStacksModal from "../shared/MoveSelectedCardStacksModal.vue";
defineProps<{
    table: TableResponse<CardStackRow>;
    baseUrl: string;
    containerName: string;
    containers: ContainerListItem[];
}>();
const { t } = useI18n();
const { formatPrice } = useFormatting();
const columns = computed<ColumnDef<CardStackRow>[]>(() => [
    {
        key: "name",
        label: t("pages.container_page.columns.name"),
        sortable: true,
        visibleInCard: true,
        cardPrimary: true
    },
    {
        key: "set_name",
        label: t("pages.container_page.columns.set_name"),
        sortable: true,
        visibleInCard: true
    },
    {
        key: "collector_number",
        label: t("pages.container_page.columns.collector_number"),
        sortable: true,
        width: "5rem",
        align: "right"
    },
    {
        key: "language",
        label: t("pages.container_page.columns.language"),
        visibleInCard: true
    },
    {
        key: "amount",
        label: t("pages.container_page.columns.amount"),
        sortable: true,
        width: "5rem",
        align: "right",
        visibleInCard: true
    },
    {
        key: "condition",
        label: t("pages.container_page.columns.condition"),
        sortable: true,
        visibleInCard: true
    },
    {
        key: "finish",
        label: t("pages.container_page.columns.finish"),
        sortable: true
    },
    {
        key: "price",
        label: t("pages.container_page.columns.price"),
        sortable: true,
        align: "right"
    },
    {
        key: "total_price",
        label: t("pages.container_page.columns.total_price"),
        sortable: true,
        align: "right",
        visibleInCard: true
    }
]);
/** Resolve the flag image URL for a given language code. */
const flagSrc = (lang: string): string => new URL(`../../../assets/flags/${lang}.svg`, import.meta.url).href;
/** The card stack row currently targeted for deletion, or null when the modal is hidden. */
const deleteTarget = ref<CardStackRow | null>(null);
/** Close the row action popover and open the delete confirmation modal for the given row. */
const openDeleteModal = (row: CardStackRow) => {
    deleteTarget.value = row;
};
/** Selected IDs for the move-selected modal, or null when the modal is hidden. */
const moveSelectedIds = ref<string[] | null>(null);
/** Close the mass-actions popover and open the move-selected modal. */
const openMoveModal = (selectedIds: string[]) => {
    document.getElementById("massActions")?.hidePopover();
    moveSelectedIds.value = [...selectedIds];
};
/** Selected IDs for the delete-selected modal, or null when the modal is hidden. */
const deleteSelectedIds = ref<string[] | null>(null);
/** Close the mass-actions popover and open the delete-selected modal. */
const openDeleteSelectedModal = (selectedIds: string[]) => {
    document.getElementById("massActions")?.hidePopover();
    deleteSelectedIds.value = [...selectedIds];
};
</script>

<template>
    <data-table :columns="columns" :response="table" :selectable="true" :base-url="baseUrl">
        <template #toolbar-actions="{ selectedIds }">
            <pop-over
                v-if="selectedIds.length > 0"
                icon="more"
                :aria-label="$t('header.user.label')"
                class-string="popover-button--rounded"
                reference="massActions"
                width="auto"
            >
                <ul class="popover-list popover-list--short">
                    <li>
                        <button type="button" class="popover-list-item" @click="openMoveModal(selectedIds)">
                            <icon name="move" :size="1" />
                            {{ $t("components.datatable.mass_actions.move") }}
                        </button>
                    </li>
                    <li>
                        <button type="button" class="popover-list-item" @click="openDeleteSelectedModal(selectedIds)">
                            <icon name="delete" :size="1" />
                            {{ $t("components.datatable.mass_actions.delete") }}
                        </button>
                    </li>
                </ul>
            </pop-over>
        </template>
        <template #cell-name="{ row }">
            <card-image-preview :src="row.card_image_0" :alt="row.name">
                {{ row.name }}
            </card-image-preview>
        </template>
        <template #cell-set_name="{ row }">
            <img
                v-if="row.set_icon"
                :src="row.set_icon"
                :alt="row.set_code"
                class="set"
                v-tooltip="`[${row.set_code.toUpperCase()}] ${row.set_name}`"
            />
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
        :container-name="containerName"
        @close="deleteTarget = null"
    />
    <move-selected-card-stacks-modal
        v-if="moveSelectedIds"
        :containers="containers"
        :selected-ids="moveSelectedIds"
        :container-name="containerName"
        @close="moveSelectedIds = null"
    />
    <delete-selected-card-stacks-modal
        v-if="deleteSelectedIds"
        :selected-ids="deleteSelectedIds"
        :container-name="containerName"
        @close="deleteSelectedIds = null"
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
