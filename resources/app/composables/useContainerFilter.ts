import { computed, ref } from "vue";
import type { Ref, ComputedRef } from "vue";
import { useI18n } from "vue-i18n";
import type { Container } from "@/pages/Collection/Containers/ContainersResultList.vue";

export type UseContainerFilterReturn = {
    activeTypes: Ref<Set<string>>;
    search: Ref<string>;
    usedTypes: ComputedRef<string[]>;
    filteredContainers: ComputedRef<Container[]>;
    toggleType: (type: string) => void;
    typeLabel: (type: string) => string;
};

/**
 * Manages type-filter and name-search state for the containers list.
 *
 * All types are selected by default so the full list is visible on first load.
 * Filtering is purely client-side and reactive — no server round-trips.
 *
 * @param containers - Reactive ref to the full (unfiltered) container list.
 * @param containerTypes - Static array of known BinderType enum values, used to distinguish standard types from custom ones.
 * @returns Reactive filter state and helper functions.
 */
export function useContainerFilter(containers: Ref<Container[]>, containerTypes: string[]): UseContainerFilterReturn {
    const { t } = useI18n();

    /**
     * Returns the display/filter key for a container.
     * For "other" containers, returns the custom_type value instead of "other"
     * so custom types can be filtered and labeled individually.
     *
     * @param c - The container to derive the type key from.
     * @returns The effective type string used for filtering and labeling.
     */
    function effectiveType(c: Container): string {
        return c.type === "other" && c.custom_type ? c.custom_type : c.type;
    }

    /**
     * Returns the translated label for a standard binder type,
     * or the raw string for user-defined custom types.
     *
     * @param type - Effective type key as returned by `effectiveType`.
     * @returns Translated enum label, or the raw custom type string.
     */
    function typeLabel(type: string): string {
        return containerTypes.includes(type) ? t(`enums.binder_type.${type}`) : type;
    }

    /** Currently selected type filter keys. All types selected by default = show all. */
    const activeTypes = ref<Set<string>>(new Set(containers.value.map(effectiveType)));

    /** Current name search string (raw, lowercasing happens in filteredContainers). */
    const search = ref("");

    /** Unique effective types present in the current container list, used to build the type filter. */
    const usedTypes = computed(() => [...new Set(containers.value.map(effectiveType))]);

    /**
     * Containers after applying both the active type filter and the name search.
     * Type filter: passes when effectiveType is in the active set.
     * Name filter: case-insensitive substring match on container name.
     */
    const filteredContainers = computed(() => {
        const needle = search.value.toLowerCase();
        return containers.value.filter(
            c => activeTypes.value.has(effectiveType(c)) && (!needle || c.name.toLowerCase().includes(needle))
        );
    });

    /**
     * Toggles a type in the active filter set.
     * Selecting an already-active type deselects it.
     *
     * @param type - Effective type key to toggle.
     */
    function toggleType(type: string) {
        const next = new Set(activeTypes.value);
        if (next.has(type)) {
            next.delete(type);
        } else {
            next.add(type);
        }
        activeTypes.value = next;
    }

    return { activeTypes, search, usedTypes, filteredContainers, toggleType, typeLabel };
}
