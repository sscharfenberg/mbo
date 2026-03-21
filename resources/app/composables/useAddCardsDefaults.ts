import { computed, ref } from "vue";

const STORAGE_KEY = "mbo:add-cards-defaults";

/** Shape of the persisted defaults. All fields are optional — absent fields fall back to app defaults. */
export type AddCardsDefaults = {
    amount?: number;
    language?: string;
    condition?: string;
    foilType?: string;
};

/** App-wide fallback values used when no user defaults are saved. */
const APP_DEFAULTS: Required<AddCardsDefaults> = {
    amount: 1,
    language: "en",
    condition: "",
    foilType: ""
};

/** Reactive state holding the currently saved defaults. Shared across all consumers. */
const savedDefaults = ref<AddCardsDefaults>(load());

/** Read defaults from localStorage, returning an empty object on failure. */
function load(): AddCardsDefaults {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return {};
        return JSON.parse(raw) as AddCardsDefaults;
    } catch {
        return {};
    }
}

/** Write the given defaults to localStorage and update reactive state. */
function persist(defaults: AddCardsDefaults): void {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(defaults));
    savedDefaults.value = { ...defaults };
}

/** Return type of the {@link useAddCardsDefaults} composable. */
export type UseAddCardsDefaultsReturn = ReturnType<typeof useAddCardsDefaults>;

/**
 * Composable for managing "add cards" page defaults via localStorage.
 *
 * Provides the resolved defaults (saved values merged over app defaults),
 * the raw saved defaults for display, and save/clear/hasSavedDefaults helpers.
 *
 * @example
 * ```ts
 * const { defaults, saveDefaults, clearDefaults } = useAddCardsDefaults();
 * const amount = ref(defaults.value.amount);
 * ```
 */
export function useAddCardsDefaults() {
    /** Merged defaults: saved values take precedence over app defaults. */
    const defaults = computed<Required<AddCardsDefaults>>(() => ({
        ...APP_DEFAULTS,
        ...savedDefaults.value
    }));

    /** True when the user has saved at least one custom default. */
    const hasSavedDefaults = computed(() => Object.keys(savedDefaults.value).length > 0);

    /**
     * Persist the given form values as the user's defaults.
     *
     * @param current - The current form field values to save.
     */
    function saveDefaults(current: AddCardsDefaults): void {
        persist(current);
    }

    /** Remove all saved defaults, reverting to app-wide fallbacks. */
    function clearDefaults(): void {
        localStorage.removeItem(STORAGE_KEY);
        savedDefaults.value = {};
    }

    return {
        defaults,
        savedDefaults,
        hasSavedDefaults,
        saveDefaults,
        clearDefaults
    };
}