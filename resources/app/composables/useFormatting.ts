import { usePage } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";

export type UseFormattingReturn = {
    formatDecimals: (num: number) => string;
    formatBytes: (bytes: number, si?: boolean, dp?: number) => string;
    formatPrice: (amount: number) => string;
};

/**
 * Composable that exposes number and byte formatting helpers.
 *
 * Reads the active vue-i18n locale so all output matches the user's selected
 * language without any hardcoded locale strings.
 */
export const useFormatting = (): UseFormattingReturn => {
    const { locale } = useI18n();

    /**
     * Format a decimal number as a locale-aware string.
     *
     * @param num - The number to format.
     * @return Formatted string using the active app locale.
     */
    const formatDecimals = (num: number): string => {
        return num.toLocaleString(locale.value, { style: "decimal" });
    };

    /**
     * Format a byte count as a human-readable string.
     *
     * @param bytes - Number of bytes.
     * @param si    - True to use metric (SI) units (powers of 1000).
     *               False to use binary (IEC) units (powers of 1024).
     * @param dp    - Number of decimal places to display.
     * @return Formatted string with the appropriate unit suffix.
     */
    const formatBytes = (bytes: number, si = false, dp = 1): string => {
        const thresh = si ? 1000 : 1024;

        if (Math.abs(bytes) < thresh) {
            return bytes + " B";
        }

        const units = si
            ? ["kB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"]
            : ["KiB", "MiB", "GiB", "TiB", "PiB", "EiB", "ZiB", "YiB"];
        let u = -1;
        const r = 10 ** dp;

        do {
            bytes /= thresh;
            ++u;
        } while (Math.round(Math.abs(bytes) * r) / r >= thresh && u < units.length - 1);

        return bytes.toFixed(dp) + " " + units[u];
    };

    /**
     * Format a monetary amount using the user's selected currency and locale.
     *
     * @param amount - The numeric amount to format.
     * @return Formatted currency string (e.g. "125,56 €" or "$125.56").
     */
    const formatPrice = (amount: number): string => {
        const currency = (usePage().props.currency as string) ?? "eur";

        return amount.toLocaleString(locale.value, {
            style: "currency",
            currency: currency.toUpperCase(),
        });
    };

    return { formatDecimals, formatBytes, formatPrice };
};