import type { ComputedRef, Ref } from "vue";
import { computed, onBeforeUnmount, onMounted, ref } from "vue";

/**
 * Configuration for the responsive column layout.
 *
 * `colGap` must match the CSS `gap` on the container — the ResizeObserver
 * calculation accounts for inter-column gaps when determining how many
 * columns fit.
 */
interface ColumnConfig {
    /** Minimum column width in pixels before a column is dropped. */
    minColWidth: number;
    /** Hard cap — prevents excessive columns on ultra-wide displays. */
    maxColumns: number;
    /** Gap between columns in pixels — must match the CSS `gap` value. */
    colGap: number;
}

/** Return type of {@link useResponsiveColumns}. */
export type UseResponsiveColumnsReturn<T> = {
    /** Template ref to bind to the container element. */
    containerRef: Ref<HTMLElement | null>;
    /** Sections distributed into columns, column-first. */
    columns: ComputedRef<T[][]>;
};

/**
 * Distribute a flat list of sections into a responsive multi-column layout.
 *
 * Column count is driven by the container width via ResizeObserver. Sections
 * are distributed column-first: each column is filled top-to-bottom before
 * moving to the next. Taller columns come first so trailing columns are
 * never empty.
 *
 * @param sections - Reactive list of sections to distribute.
 * @param config - Layout configuration (column widths, gap, max columns).
 * @returns Container ref and the distributed columns computed.
 */
export function useResponsiveColumns<T>(
    sections: ComputedRef<T[]>,
    config: ColumnConfig
): UseResponsiveColumnsReturn<T> {
    const containerRef = ref<HTMLElement | null>(null);
    const colCount = ref(1);
    let observer: ResizeObserver | null = null;

    onMounted(() => {
        if (!containerRef.value) return;
        observer = new ResizeObserver(([entry]) => {
            const width = entry.contentBoxSize[0].inlineSize;
            // Solve: width >= n * minColWidth + (n - 1) * colGap
            const n = Math.floor((width + config.colGap) / (config.minColWidth + config.colGap));
            colCount.value = Math.max(1, Math.min(n, config.maxColumns));
        });
        observer.observe(containerRef.value);
    });

    onBeforeUnmount(() => {
        observer?.disconnect();
    });

    const columns = computed<T[][]>(() => {
        const items = sections.value;
        const count = Math.min(items.length, colCount.value);
        if (count === 0) return [];

        // The first `extra` columns get one more item than the rest. This
        // keeps taller columns on the left and guarantees every column
        // receives at least one item — avoiding empty trailing columns
        // that would appear with a naive Math.ceil division.
        const cols: T[][] = Array.from({ length: count }, () => []);
        const base = Math.floor(items.length / count);
        const extra = items.length % count;
        let idx = 0;
        for (let c = 0; c < count; c++) {
            const size = c < extra ? base + 1 : base;
            for (let j = 0; j < size; j++) {
                cols[c].push(items[idx++]);
            }
        }
        return cols;
    });

    return { containerRef, columns };
}