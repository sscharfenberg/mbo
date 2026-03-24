<script setup lang="ts" generic="T extends { id: string; href?: string }">
import { router } from "@inertiajs/vue3";
import { type Ref, computed, onBeforeUnmount, onMounted, provide, ref, useSlots, watch } from "vue";
import { useI18n } from "vue-i18n";
import DataTableActions from "Components/DataTable/DataTableActions.vue";
import DataTableBody from "Components/DataTable/DataTableBody.vue";
import DataTableCards from "Components/DataTable/DataTableCards.vue";
import DataTableHead from "Components/DataTable/DataTableHead.vue";
import DataTablePagination from "Components/DataTable/DataTablePagination.vue";
import DataTableToolbar from "Components/DataTable/DataTableToolbar.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import type { ColumnDef, TableResponse, SortEntry } from "Types/dataTable";
import { DATA_TABLE_KEY } from "Types/dataTable";

const props = withDefaults(
    defineProps<{
        columns: ColumnDef<T>[];
        response: TableResponse<T>;
        selectable?: boolean;
        baseUrl?: string;
    }>(),
    {
        selectable: false,
        baseUrl: ""
    }
);
const { t } = useI18n();
const slots = useSlots();

/** Filter slots to only cell-* and actions slots for forwarding. */
const cellSlotNames = computed(() => Object.keys(slots).filter(name => name.startsWith("cell-")));

// ---------------------------------------------------------------------------
// Sort normalization: server sends object | null, internal is always array
// ---------------------------------------------------------------------------
const sort = computed<SortEntry[]>(() => {
    if (!props.response.sort) return [];
    return [props.response.sort];
});

// ---------------------------------------------------------------------------
// Selection state
// ---------------------------------------------------------------------------
const selectedIds = ref<string[]>([]);

function toggleSelection(id: string) {
    const idx = selectedIds.value.indexOf(id);
    if (idx === -1) {
        selectedIds.value.push(id);
    } else {
        selectedIds.value.splice(idx, 1);
    }
}

function togglePageSelection(ids: string[]) {
    const allSelected = ids.every(id => selectedIds.value.includes(id));
    if (allSelected) {
        selectedIds.value = selectedIds.value.filter(id => !ids.includes(id));
    } else {
        const missing = ids.filter(id => !selectedIds.value.includes(id));
        selectedIds.value.push(...missing);
    }
}

/** Clear selection on sort/filter/search change, preserve on page change. */
watch(
    () => [props.response.sort, props.response.search, props.response.filters],
    () => {
        selectedIds.value = [];
    },
    { deep: true }
);

provide(DATA_TABLE_KEY, {
    selectedIds,
    toggleSelection,
    togglePageSelection
});

// ---------------------------------------------------------------------------
// Row IDs for header checkbox
// ---------------------------------------------------------------------------
const rowIds = computed(() => props.response.rows.map(row => row.id));

// ---------------------------------------------------------------------------
// Loading state — tracks Inertia navigation
// ---------------------------------------------------------------------------
const isLoading = ref(false);
router.on("start", () => {
    isLoading.value = true;
});
router.on("finish", () => {
    isLoading.value = false;
});

// ---------------------------------------------------------------------------
// aria-live announcements
// ---------------------------------------------------------------------------
const announcement = ref("");

watch(sort, newSort => {
    if (newSort.length === 0) return;
    const entry = newSort[0];
    const col = props.columns.find(c => c.key === entry.key);
    const label = col?.label ?? entry.key;
    announcement.value =
        entry.direction === "asc"
            ? t("components.datatable.sorted_asc", { column: label })
            : t("components.datatable.sorted_desc", { column: label });
});

watch(
    () => props.response.page,
    page => {
        if (!props.response.pageSize) return;
        const totalPages = Math.ceil(props.response.total / props.response.pageSize);
        announcement.value = t("components.datatable.page_status", {
            page,
            total: totalPages,
            size: props.response.rows.length
        });
    }
);

// ---------------------------------------------------------------------------
// Row action popover
// ---------------------------------------------------------------------------
const activeRow = ref<T | null>(null) as Ref<T | null>;
const actionButtonRef = ref<HTMLElement | null>(null);

function onAction(row: T, el: HTMLElement) {
    activeRow.value = row;
    actionButtonRef.value = el;
}

/** Reset popover state and return focus to the three-dot button that opened it. */
function onCloseActions() {
    const triggerEl = actionButtonRef.value;
    activeRow.value = null;
    actionButtonRef.value = null;
    triggerEl?.focus();
}

// ---------------------------------------------------------------------------
// Navigation helpers — emit Inertia requests
// ---------------------------------------------------------------------------
/**
 * Merge new params into the current URL, preserving existing query state
 * (e.g. pageSize survives a page navigation). Params set to null are removed.
 */
function buildUrl(params: Record<string, string | number | null>) {
    const base = props.baseUrl || window.location.pathname;
    const url = new URL(base, window.location.origin);
    // Carry over current query params so existing state (pageSize, sort, etc.) is preserved
    const currentParams = new URLSearchParams(window.location.search);
    for (const [key, value] of currentParams) {
        url.searchParams.set(key, value);
    }
    // Apply the new params on top
    for (const [key, value] of Object.entries(params)) {
        if (value === null || value === "") {
            url.searchParams.delete(key);
        } else {
            url.searchParams.set(key, String(value));
        }
    }
    return url.pathname + url.search;
}

function onSort(key: string) {
    const current = sort.value.find(s => s.key === key);
    let direction: "asc" | "desc" = "asc";
    if (current) {
        direction = current.direction === "asc" ? "desc" : "asc";
    }
    router.get(buildUrl({ sort: key, dir: direction, page: 1 }), {}, { preserveState: true, preserveScroll: true });
}

function onSearch(query: string) {
    router.get(buildUrl({ search: query || null, page: 1 }), {}, { preserveState: true, preserveScroll: true });
}

function onNavigate(page: number) {
    router.get(buildUrl({ page }), {}, { preserveState: true, preserveScroll: true });
}

function onPageSizeChange(size: number) {
    router.get(buildUrl({ pageSize: size, page: 1 }), {}, { preserveState: true, preserveScroll: true });
}

// ---------------------------------------------------------------------------
// Sticky header detection via IntersectionObserver
// ---------------------------------------------------------------------------
const stickysentinel = ref<HTMLElement | null>(null);
const isStuck = ref(false);
let observer: IntersectionObserver | null = null;

onMounted(() => {
    if (!stickysentinel.value) return;
    const offset = getComputedStyle(stickysentinel.value.closest(".dt")!)
        .getPropertyValue("--datatable-sticky-offset")
        .trim();
    const margin = offset ? `-${offset} 0px 0px 0px` : "0px";
    observer = new IntersectionObserver(
        ([entry]) => {
            isStuck.value = !entry.isIntersecting;
        },
        { rootMargin: margin }
    );
    observer.observe(stickysentinel.value);
});

onBeforeUnmount(() => {
    observer?.disconnect();
});
</script>

<template>
    <div class="dt" :class="{ 'dt--loading': isLoading }">
        <data-table-toolbar :search="response.search" :selected-count="selectedIds.length" @search="onSearch">
            <template v-if="$slots['toolbar-actions']" #actions>
                <slot name="toolbar-actions" :selected-ids="selectedIds" />
            </template>
        </data-table-toolbar>

        <div class="dt__wrapper">
            <div v-if="isLoading" class="dt__overlay">
                <loading-spinner :size="3" />
            </div>

            <!-- Sentinel for sticky header detection -->
            <div ref="stickysentinel" class="dt__sticky-sentinel" />

            <!-- Desktop: table layout -->
            <table class="dt__table" :aria-busy="isLoading">
                <data-table-head
                    :columns="columns"
                    :sort="sort"
                    :selectable="selectable"
                    :row-ids="rowIds"
                    :stuck="isStuck"
                    @sort="onSort"
                />
                <data-table-body :columns="columns" :rows="response.rows" :selectable="selectable" @action="onAction">
                    <template v-for="name in cellSlotNames" :key="name" #[name]="slotProps">
                        <slot :name="name" v-bind="slotProps" />
                    </template>
                </data-table-body>
            </table>

            <!-- Mobile: card layout -->
            <data-table-cards :columns="columns" :rows="response.rows" :selectable="selectable" @action="onAction">
                <template v-for="name in cellSlotNames" :key="name" #[name]="slotProps">
                    <slot :name="name" v-bind="slotProps" />
                </template>
            </data-table-cards>
        </div>

        <!-- Empty state -->
        <div v-if="response.rows.length === 0 && !isLoading" class="dt__empty">
            <slot name="empty" />
        </div>

        <!-- Pagination -->
        <data-table-pagination
            v-if="response.pageSize && response.total > 0"
            :page="response.page"
            :page-size="response.pageSize"
            :total="response.total"
            @navigate="onNavigate"
            @page-size-change="onPageSizeChange"
        />

        <!-- Row action popover -->
        <data-table-actions :row="activeRow" :trigger-el="actionButtonRef" @close="onCloseActions">
            <template v-if="activeRow" #default>
                <slot name="actions" :row="activeRow" />
            </template>
        </data-table-actions>

        <!-- Screen reader announcements -->
        <div class="sr-only" aria-live="polite">{{ announcement }}</div>
    </div>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.dt {
    container-type: inline-size;

    &--loading {
        pointer-events: none;
    }

    &__wrapper {
        position: relative;
    }

    &__sticky-sentinel {
        height: 0;
    }

    &__overlay {
        display: flex;

        position: absolute;
        inset: 0;
        z-index: 1;
        align-items: center;
        justify-content: center;

        background: rgb(255 255 255 / 60%);
    }

    &__table {
        display: table;

        width: 100%;
        border: map.get(s.$table, "border") solid map.get(c.$table, "border");

        border-radius: map.get(s.$table, "radius");

        border-spacing: 0;
    }

    &__empty {
        padding: 2rem;

        text-align: center;
    }
}

/* Show table on wide containers, cards on narrow */
@container (min-width: 640px) {
    .dt__table {
        display: table;
    }
}

@container (max-width: 639px) {
    .dt__table {
        display: none;
    }
}
</style>
