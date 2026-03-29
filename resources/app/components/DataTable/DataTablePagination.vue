<script setup lang="ts">
import { computed, ref } from "vue";
import MonoSelect from "Components/Form/Select/MonoSelect.vue";
import Icon from "Components/UI/Icon.vue";
const props = defineProps<{
    page: number;
    pageSize: number;
    total: number;
}>();
const emit = defineEmits<{
    navigate: [page: number];
    pageSizeChange: [size: number];
}>();

const totalPages = computed(() => Math.ceil(props.total / props.pageSize));
const from = computed(() => (props.page - 1) * props.pageSize + 1);
const to = computed(() => Math.min(props.page * props.pageSize, props.total));

/**
 * Visible page numbers with ellipsis truncation.
 * Shows a sliding window around the current page. First/last page
 * buttons already exist as dedicated icons, so they are not repeated here.
 * Ellipsis indicates more pages exist in that direction.
 * Example for page 5 of 20: ["...", 4, 5, 6, "..."]
 */
const NEIGHBORS = 2;
const visiblePages = computed(() => {
    const pages: (number | "...")[] = [];
    const total = totalPages.value;
    const current = props.page;
    if (total <= 1) return pages;
    const start = Math.max(1, current - NEIGHBORS);
    const end = Math.min(total, current + NEIGHBORS);
    if (start > 1) pages.push("...");
    for (let i = start; i <= end; i++) pages.push(i);
    if (end < total) pages.push("...");
    return pages;
});

const jumpToPage = ref(props.page);
/** Clamp user input to valid range before navigating. */
function onJumpToPage() {
    const clamped = Math.max(1, Math.min(totalPages.value, jumpToPage.value));
    jumpToPage.value = clamped;
    emit("navigate", clamped);
}

const pageSizeOptions = [25, 50, 100].map(s => ({ value: String(s), label: String(s) }));
</script>

<template>
    <nav class="dt-pagination" :aria-label="$t('components.datatable.pagination')">
        <div v-if="totalPages > 1" class="dt-pagination__col">
            <button
                :disabled="page <= 1"
                @click="emit('navigate', 1)"
                :aria-label="$t('components.datatable.first')"
                class="dt-pagination__page"
            >
                <icon name="first-page" :size="1" />
            </button>
            <button
                :disabled="page <= 1"
                @click="emit('navigate', page - 1)"
                :aria-label="$t('components.datatable.previous')"
                class="dt-pagination__page"
            >
                <icon name="chevron" :size="1" :additional-classes="['left']" />
            </button>
            <template v-for="p in visiblePages" :key="p">
                <span v-if="p === '...'" class="dt-pagination__ellipsis">…</span>
                <button
                    v-else
                    :class="{ 'dt-pagination__current': p === page }"
                    :aria-current="p === page ? 'page' : undefined"
                    @click="emit('navigate', p)"
                    class="dt-pagination__page"
                >
                    {{ p }}
                </button>
            </template>
            <button
                :disabled="page >= totalPages"
                @click="emit('navigate', page + 1)"
                :aria-label="$t('components.datatable.next')"
                class="dt-pagination__page"
            >
                <icon name="chevron" :size="1" :additional-classes="['right']" />
            </button>
            <button
                :disabled="page >= totalPages"
                @click="emit('navigate', totalPages)"
                :aria-label="$t('components.datatable.last')"
                class="dt-pagination__page"
            >
                <icon name="last-page" :size="1" />
            </button>
        </div>
        <div v-if="totalPages > 1" class="dt-pagination__col">
            <label for="jumpToPage">{{ $t("components.datatable.jump_to_page") }}</label>
            <input
                type="text"
                inputmode="numeric"
                :min="1"
                :max="totalPages"
                id="jumpToPage"
                v-model.number="jumpToPage"
                @keydown.enter="onJumpToPage"
                class="form-input dt-pagination__jump"
                :aria-label="$t('components.datatable.jump_to_page')"
            />
        </div>
        <div class="dt-pagination__col">
            <span class="dt-pagination__info"> {{ from }}–{{ to }} / {{ total }} </span>
            <mono-select
                :options="pageSizeOptions"
                :selected="String(pageSize)"
                :sort="false"
                :aria-label="$t('components.datatable.page_size')"
                @change="emit('pageSizeChange', Number($event))"
                :clearable="false"
            />
        </div>
    </nav>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@use "Abstracts/timings" as ti;

.dt-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;

    padding: map.get(s.$components, "datatable", "pagination", "padding");
    border: map.get(s.$components, "datatable", "pagination", "border") solid
        map.get(c.$components, "datatable", "pagination", "border");
    margin: map.get(s.$components, "datatable", "pagination", "margin");
    gap: map.get(s.$components, "datatable", "pagination", "gap");

    background-color: map.get(c.$components, "datatable", "pagination", "background");
    color: map.get(c.$components, "datatable", "pagination", "surface");
    border-radius: map.get(s.$components, "datatable", "pagination", "radius");

    &__col {
        display: flex;
        align-items: center;
        flex-wrap: wrap;

        gap: 0.5rem;
    }

    &__jump {
        width: 6rem;
        padding: 0.75ex 2ch;
    }

    &__page {
        display: flex;
        align-items: center;
        justify-content: center;

        min-width: map.get(s.$components, "datatable", "pagination", "page", "min-width");
        padding: map.get(s.$components, "datatable", "pagination", "page", "padding");
        border: map.get(s.$components, "datatable", "pagination", "page", "border") solid
            map.get(c.$components, "datatable", "pagination", "page", "border");

        background-color: map.get(c.$components, "datatable", "pagination", "page", "background");
        color: map.get(c.$components, "datatable", "pagination", "page", "surface");
        border-radius: map.get(s.$components, "datatable", "pagination", "page", "radius");

        transition:
            background-color map.get(ti.$timings, "fast") linear,
            color map.get(ti.$timings, "fast") linear;

        &:not([disabled], .dt-pagination__current):hover {
            background-color: map.get(c.$components, "datatable", "pagination", "page-hover", "background");
            color: map.get(c.$components, "datatable", "pagination", "page-hover", "surface");

            cursor: pointer;
        }

        &[disabled] {
            opacity: 0.5;

            cursor: not-allowed;
        }
    }

    &__current {
        background-color: map.get(c.$components, "datatable", "pagination", "page-current", "background");
        color: map.get(c.$components, "datatable", "pagination", "page-current", "surface");
        border-color: map.get(c.$components, "datatable", "pagination", "page-current", "surface");
    }
}
</style>
