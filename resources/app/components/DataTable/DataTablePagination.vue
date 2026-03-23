<script setup lang="ts">
import { computed, ref } from "vue";
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
 * Always shows first + last page. Current page sits in the middle
 * with one neighbor on each side. Ellipsis fills gaps.
 * Example for page 5 of 20: [1, "...", 4, 5, 6, "...", 20]
 */
const visiblePages = computed(() => {
    const pages: (number | "...")[] = [];
    const total = totalPages.value;
    const current = props.page;
    if (total <= 7) {
        for (let i = 1; i <= total; i++) pages.push(i);
        return pages;
    }
    pages.push(1);
    if (current > 3) pages.push("...");
    const start = Math.max(2, current - 1);
    const end = Math.min(total - 1, current + 1);
    for (let i = start; i <= end; i++) pages.push(i);
    if (current < total - 2) pages.push("...");
    pages.push(total);
    return pages;
});

const jumpToPage = ref(props.page);
/** Clamp user input to valid range before navigating. */
function onJumpToPage() {
    const clamped = Math.max(1, Math.min(totalPages.value, jumpToPage.value));
    jumpToPage.value = clamped;
    emit("navigate", clamped);
}

const pageSizes = [25, 50, 100];
</script>

<template>
    <nav class="dt-pagination" :aria-label="$t('components.datatable.pagination')">
        <span class="dt-pagination__info"> {{ from }}–{{ to }} / {{ total }} </span>
        <div v-if="totalPages > 1" class="dt-pagination__controls">
            <button :disabled="page <= 1" @click="emit('navigate', 1)" :aria-label="$t('components.datatable.first')">
                <icon name="chevron" :size="1" :additional-classes="['dt-pagination__chevron-left']" />
                <icon name="chevron" :size="1" :additional-classes="['dt-pagination__chevron-left']" />
            </button>
            <button
                :disabled="page <= 1"
                @click="emit('navigate', page - 1)"
                :aria-label="$t('components.datatable.previous')"
            >
                <icon name="chevron" :size="1" :additional-classes="['dt-pagination__chevron-left']" />
            </button>
            <template v-for="p in visiblePages" :key="p">
                <span v-if="p === '...'" class="dt-pagination__ellipsis">…</span>
                <button
                    v-else
                    :class="{ 'dt-pagination__current': p === page }"
                    :aria-current="p === page ? 'page' : undefined"
                    @click="emit('navigate', p)"
                >
                    {{ p }}
                </button>
            </template>
            <button
                :disabled="page >= totalPages"
                @click="emit('navigate', page + 1)"
                :aria-label="$t('components.datatable.next')"
            >
                <icon name="chevron" :size="1" :additional-classes="['dt-pagination__chevron-right']" />
            </button>
            <button
                :disabled="page >= totalPages"
                @click="emit('navigate', totalPages)"
                :aria-label="$t('components.datatable.last')"
            >
                <icon name="chevron" :size="1" :additional-classes="['dt-pagination__chevron-right']" />
                <icon name="chevron" :size="1" :additional-classes="['dt-pagination__chevron-right']" />
            </button>
        </div>
        <div class="dt-pagination__options">
            <input
                v-if="totalPages > 1"
                type="number"
                :min="1"
                :max="totalPages"
                v-model.number="jumpToPage"
                @keydown.enter="onJumpToPage"
                class="dt-pagination__jump"
                :aria-label="$t('components.datatable.jump_to_page')"
            />
            <select
                :value="pageSize"
                @change="emit('pageSizeChange', Number(($event.target as HTMLSelectElement).value))"
                class="dt-pagination__size"
                :aria-label="$t('components.datatable.page_size')"
            >
                <option v-for="size in pageSizes" :key="size" :value="size">{{ size }}</option>
            </select>
        </div>
    </nav>
</template>

<style lang="scss" scoped>
.dt-pagination {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    margin-top: 0.5rem;
    gap: 1rem;

    &__controls {
        display: flex;
        align-items: center;

        gap: 0.25rem;
    }

    &__current {
        font-weight: bold;
        text-decoration: underline;
    }

    &__options {
        display: flex;

        gap: 0.5rem;
    }

    &__chevron-left {
        transform: rotate(90deg);
    }

    &__chevron-right {
        transform: rotate(-90deg);
    }

    &__jump {
        width: 4rem;
    }
}
</style>
