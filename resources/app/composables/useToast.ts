import type { Ref } from "vue";
import { ref } from "vue";

/** Severity level of a toast notification. */
export type ToastType = "info" | "success" | "warning" | "error";

/** A single toast notification entry. */
export type Toast = {
    id: string;
    message: string;
    type: ToastType;
    /** Auto-dismiss delay in milliseconds. `0` disables auto-dismiss. */
    duration: number;
};

/** Return type of the {@link useToast} composable. */
export type UseToastReturn = {
    activeToasts: Ref<Toast[]>;
    addToast: (message: string, type?: ToastType, duration?: number) => void;
    removeToast: (id: string) => void;
};

/** Maximum number of toasts shown simultaneously. Additional toasts are queued. */
const MAX_VISIBLE = 5;

/** Default auto-dismiss delay in milliseconds. */
const DEFAULT_DURATION = 5000;

// Module-level state — all consumers share the same singleton instance,
// so toasts triggered from composables, plain .ts files, and Vue components
// all appear in the same rendered list.
const activeToasts = ref<Toast[]>([]);
const queue = ref<Toast[]>([]);
const timers = new Map<string, ReturnType<typeof setTimeout>>();

/**
 * Schedule automatic removal of a toast after `duration` milliseconds.
 * Does nothing when `duration` is 0 (manual-dismiss only).
 *
 * @param id       - UUID of the toast to remove.
 * @param duration - Delay in milliseconds before removal.
 */
function scheduleRemoval(id: string, duration: number) {
    if (duration > 0) {
        timers.set(
            id,
            setTimeout(() => removeToast(id), duration)
        );
    }
}

/**
 * Remove a toast by ID, cancel its timer, and promote the next queued toast
 * into the active list (if any).
 *
 * Defined at module level so it can be referenced by {@link scheduleRemoval}
 * before the composable function is called.
 *
 * @param id - UUID of the toast to remove.
 */
function removeToast(id: string) {
    const timer = timers.get(id);
    if (timer) {
        clearTimeout(timer);
        timers.delete(id);
    }
    activeToasts.value = activeToasts.value.filter(t => t.id !== id);
    // Promote the next queued toast now that a slot has freed up.
    if (queue.value.length > 0) {
        const next = queue.value.shift()!;
        activeToasts.value.push(next);
        scheduleRemoval(next.id, next.duration);
    }
}

/**
 * Composable for displaying toast notifications from anywhere in the app.
 *
 * State lives at module level, so calling `useToast()` from a plain `.ts`
 * file, a composable, or a Vue component all share the same active list and
 * queue. The `ToastContainer` component (mounted once in each layout) reads
 * `activeToasts` and renders the visible toasts.
 *
 * At most {@link MAX_VISIBLE} toasts are shown at the same time. Any excess
 * toasts are queued and promoted automatically as slots free up.
 * Auto-dismiss timers only start when a toast becomes active, never while
 * it sits in the queue.
 *
 * @example
 * ```ts
 * const { addToast } = useToast()
 * addToast('Saved!', 'success')
 * addToast('Something went wrong', 'error', 8000)
 * addToast('Stays until closed', 'warning', 0)
 * ```
 */
export function useToast(): UseToastReturn {
    /**
     * Add a new toast notification.
     *
     * If fewer than {@link MAX_VISIBLE} toasts are currently active the toast
     * is shown immediately; otherwise it is added to the queue and will appear
     * once an active slot becomes available.
     *
     * @param message  - Text to display inside the toast.
     * @param type     - Severity level; defaults to `"info"`.
     * @param duration - Auto-dismiss delay in ms; `0` disables auto-dismiss. Defaults to {@link DEFAULT_DURATION}.
     */
    function addToast(
        message: string,
        type: ToastType = "info",
        duration: number = DEFAULT_DURATION
    ) {
        const toast: Toast = { id: crypto.randomUUID(), message, type, duration };
        if (activeToasts.value.length < MAX_VISIBLE) {
            activeToasts.value.push(toast);
            scheduleRemoval(toast.id, duration);
        } else {
            queue.value.push(toast);
        }
    }

    return { activeToasts, addToast, removeToast };
}