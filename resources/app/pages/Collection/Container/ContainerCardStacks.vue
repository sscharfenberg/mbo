<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import DataTable from "Components/DataTable/DataTable.vue";
import Icon from "Components/UI/Icon.vue";
import PopOver from "Components/UI/PopOver.vue";
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
        key: "foil_type",
        label: t("pages.container_page.columns.foil_type"),
        sortable: true
    }
]);
/** Resolve the flag image URL for a given language code. */
const flagSrc = (lang: string): string => new URL(`../../../assets/flags/${lang}.svg`, import.meta.url).href;
/** Programmatically hides the user menu popover by its DOM id. */
const closePopover = () => {
    const dialog = document.getElementById("userMenu");
    if (dialog !== null) dialog.hidePopover();
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
                        <button
                            type="button"
                            class="popover-list-item"
                            @click="
                                console.log('move', selectedIds);
                                closePopover;
                            "
                        >
                            <icon name="move" :size="1" />
                            {{ $t("components.datatable.mass_actions.move") }}
                        </button>
                    </li>
                    <li>
                        <button
                            type="button"
                            class="popover-list-item"
                            @click="
                                console.log('delete', selectedIds);
                                closePopover;
                            "
                        >
                            <icon name="delete" :size="1" />
                            {{ $t("components.datatable.mass_actions.delete") }}
                        </button>
                    </li>
                </ul>
            </pop-over>
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
        <template #cell-foil_type="{ row }">
            <template v-if="row.foil_type">{{ $t("enums.foil_types." + row.foil_type) }}</template>
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
                    @click="console.log('del', row)"
                >
                    <icon name="delete" :size="1" />
                    Delete
                </button>
            </li>
        </template>
        <template #empty>
            <p>{{ $t("pages.container_page.empty") }}</p>
        </template>
    </data-table>
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
