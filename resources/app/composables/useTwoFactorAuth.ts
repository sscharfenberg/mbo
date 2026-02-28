import { router, usePage } from "@inertiajs/vue3";
import type { ComputedRef, Ref } from "vue";
import { computed, ref } from "vue";

/** Return type of the {@link useTwoFactorAuth} composable. */
export type UseTwoFactorAuthReturn = {
    qrCodeSvg: Ref<string | null>;
    manualSetupKey: Ref<string | null>;
    recoveryCodesList: Ref<string[]>;
    errors: Ref<string[]>;
    validationErrors: Ref<Record<string, string>>;
    password: Ref<string>;
    processing: Ref<boolean>;
    isRecoveryCodesVisible: Ref<boolean>;
    showSetupModal: Ref<boolean>;
    requiresConfirmation: ComputedRef<boolean>;
    twoFactorEnabled: ComputedRef<boolean>;
    hasSetupData: ComputedRef<boolean>;
    clearSetupData: () => void;
    clearErrors: () => void;
    clearTwoFactorAuthData: () => void;
    confirmPassword: () => Promise<boolean>;
    enableTwoFactor: () => Promise<void>;
    handleShowRecoveryCodes: () => Promise<void>;
    handleRegenerateRecoveryCodes: () => Promise<void>;
    fetchQrCode: () => Promise<void>;
    fetchSetupKey: () => Promise<void>;
    fetchSetupData: () => Promise<void>;
    fetchRecoveryCodes: () => Promise<void>;
};

/**
 * Perform a JSON GET request against the given URL.
 *
 * Thin wrapper around `fetch` that sets the `Accept` header,
 * checks for a successful response status, and parses the body as JSON.
 *
 * @template T - The expected shape of the JSON response body.
 * @param url - The endpoint to request.
 * @returns The parsed JSON response.
 * @throws {Error} When the response status is not OK.
 */
const fetchJson = async <T>(url: string): Promise<T> => {
    const response = await fetch(url, {
        headers: { Accept: "application/json" }
    });

    if (!response.ok) {
        throw new Error(`Failed to fetch: ${response.status}`);
    }

    return response.json();
};

// Shared reactive state — declared outside the composable so that every
// component calling `useTwoFactorAuth()` operates on the same data.
const errors = ref<string[]>([]);
const validationErrors = ref<Record<string, string>>({});
const manualSetupKey = ref<string | null>(null);
const password = ref("");
const processing = ref(false);
const qrCodeSvg = ref<string | null>(null);
const recoveryCodesList = ref<string[]>([]);
const isRecoveryCodesVisible = ref(false);
const showSetupModal = ref(false);

/** Whether both the QR code and manual setup key have been loaded. */
const hasSetupData = computed<boolean>(() => qrCodeSvg.value !== null && manualSetupKey.value !== null);

/**
 * Composable that manages two-factor authentication setup and recovery codes.
 *
 * All reactive state (QR code, setup key, recovery codes, errors) is shared
 * across every consumer so that multiple components can read/write the same
 * 2FA state without prop-drilling.
 *
 * Interacts with Fortify's built-in 2FA endpoints:
 * - `GET /user/two-factor-qr-code` — SVG of the TOTP QR code.
 * - `GET /user/two-factor-secret-key` — manual entry key for authenticator apps.
 * - `GET /user/two-factor-recovery-codes` — one-time-use backup codes.
 */
export const useTwoFactorAuth = (): UseTwoFactorAuthReturn => {
    /**
     * Fetch the TOTP QR code SVG from Fortify.
     *
     * The SVG can be rendered directly in the template so the user can scan
     * it with their authenticator app. On failure the QR code ref is cleared
     * and an error message is recorded.
     */
    const fetchQrCode = async (): Promise<void> => {
        try {
            const { svg } = await fetchJson<{ svg: string; url: string }>("/user/two-factor-qr-code");

            qrCodeSvg.value = svg;
        } catch {
            errors.value.push("Failed to fetch QR code");
            qrCodeSvg.value = null;
        }
    };

    /**
     * Fetch the manual setup key from Fortify.
     *
     * This is the base-32 secret that users can type into their authenticator
     * app when scanning a QR code is not possible.
     */
    const fetchSetupKey = async (): Promise<void> => {
        try {
            const { secretKey: key } = await fetchJson<{ secretKey: string }>("/user/two-factor-secret-key");

            manualSetupKey.value = key;
        } catch {
            errors.value.push("Failed to fetch a setup key");
            manualSetupKey.value = null;
        }
    };

    /**
     * Reset the QR code and manual setup key refs and clear any errors.
     *
     * Useful when the user cancels the 2FA enrollment flow and the
     * setup UI needs to return to its initial state.
     */
    const clearSetupData = (): void => {
        manualSetupKey.value = null;
        qrCodeSvg.value = null;
        clearErrors();
    };

    /** Clear all recorded error messages. */
    const clearErrors = (): void => {
        errors.value = [];
    };

    /**
     * Reset every piece of 2FA state (setup data, recovery codes, errors).
     *
     * Intended to be called after 2FA has been fully disabled so the UI
     * no longer displays stale data from a previous enrollment.
     */
    const clearTwoFactorAuthData = (): void => {
        clearSetupData();
        clearErrors();
        recoveryCodesList.value = [];
        isRecoveryCodesVisible.value = false;
        showSetupModal.value = false;
    };

    /**
     * Fetch the current set of one-time-use recovery codes from Fortify.
     *
     * Recovery codes let the user regain access to their account if they
     * lose their authenticator device. The codes should be displayed once
     * and stored securely by the user.
     */
    const fetchRecoveryCodes = async (): Promise<void> => {
        try {
            clearErrors();
            recoveryCodesList.value = await fetchJson<string[]>("/user/two-factor-recovery-codes");
        } catch {
            errors.value.push("Failed to fetch recovery codes");
            recoveryCodesList.value = [];
        }
    };

    /**
     * Fetch both the QR code and the manual setup key in parallel.
     *
     * Convenience wrapper used during initial 2FA enrollment to load all
     * data the setup screen needs in a single call.
     */
    const fetchSetupData = async (): Promise<void> => {
        try {
            clearErrors();
            await Promise.all([fetchQrCode(), fetchSetupKey()]);
        } catch {
            qrCodeSvg.value = null;
            manualSetupKey.value = null;
        }
    };

    const page = usePage();
    const requiresConfirmation = computed(() => page.props.requiresConfirmation as boolean);
    const twoFactorEnabled = computed(() => page.props.twoFactorEnabled as boolean);

    /**
     * Validate the user's password against the backend and mark it as confirmed
     * in the session. Uses a plain `fetch` (not Inertia) because this is a
     * side-effect-only API call — we need to set `auth.password_confirmed_at`
     * in the session so that Fortify's `password.confirm` middleware passes on
     * the subsequent 2FA enable request, without triggering an Inertia page visit.
     *
     * @returns `true` when the password was accepted, `false` on validation failure.
     */
    const confirmPassword = async (): Promise<boolean> => {
        const response = await fetch("/confirm-password", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": page.props.csrfToken as string
            },
            body: JSON.stringify({ password: password.value })
        });
        if (!response.ok) {
            const data = await response.json();
            validationErrors.value = Object.fromEntries(
                Object.entries(data.errors ?? {}).map(([key, msgs]) => [
                    key,
                    Array.isArray(msgs) ? msgs[0] : msgs
                ])
            );
            return false;
        }
        return true;
    };

    /**
     * Orchestrates the two-factor authentication enable flow.
     *
     * When `confirmPassword` is enabled in the Fortify config, the user's
     * password is validated first via {@link confirmPassword} to satisfy
     * Fortify's `password.confirm` middleware. Once confirmed (or skipped
     * when not required), an Inertia POST to Fortify's 2FA endpoint enables
     * TOTP for the authenticated user and opens the setup modal on success.
     */
    const enableTwoFactor = async (): Promise<void> => {
        processing.value = true;
        validationErrors.value = {};

        if (requiresConfirmation.value) {
            const confirmed = await confirmPassword();
            if (!confirmed) {
                processing.value = false;
                return;
            }
        }

        router.post(
            "/user/two-factor-authentication",
            {},
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    showSetupModal.value = true;
                    password.value = "";
                },
                onFinish: () => {
                    processing.value = false;
                }
            }
        );
    };

    /**
     * Fetch and reveal the current recovery codes for the authenticated user.
     *
     * Mirrors the same password-confirmation flow used by enabling 2FA: when
     * `confirmPassword` is active, this first confirms the password to satisfy
     * Fortify's `password.confirm` middleware, then requests recovery codes.
     * On success, recovery codes are marked as visible in the UI.
     */
    const handleShowRecoveryCodes = async (): Promise<void> => {
        processing.value = true;
        validationErrors.value = {};

        if (requiresConfirmation.value) {
            const confirmed = await confirmPassword();
            if (!confirmed) {
                processing.value = false;
                return;
            }
        }

        await fetchRecoveryCodes();
        isRecoveryCodesVisible.value = recoveryCodesList.value.length > 0;
        password.value = "";
        processing.value = false;
    };

    /**
     * Generate a fresh set of recovery codes and refresh the displayed list.
     *
     * Uses the same password-confirmation guard as other 2FA-sensitive actions.
     * The regenerate endpoint is called as JSON to avoid Fortify's default
     * redirect response, then recovery codes are fetched again for display.
     */
    const handleRegenerateRecoveryCodes = async (): Promise<void> => {
        processing.value = true;
        validationErrors.value = {};
        const postRegenerate = async (): Promise<Response> =>
            fetch("/user/two-factor-recovery-codes", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": page.props.csrfToken as string
                },
                body: JSON.stringify({})
            });

        let response = await postRegenerate();

        // Only ask for password again when the session confirmation has expired.
        if (response.status === 423 && requiresConfirmation.value) {
            const confirmed = await confirmPassword();
            if (!confirmed) {
                processing.value = false;
                return;
            }
            response = await postRegenerate();
        }

        if (!response.ok) {
            errors.value.push("Failed to regenerate recovery codes");
            processing.value = false;
            return;
        }

        await fetchRecoveryCodes();
        isRecoveryCodesVisible.value = recoveryCodesList.value.length > 0;
        password.value = "";
        processing.value = false;
    };

    return {
        qrCodeSvg,
        manualSetupKey,
        recoveryCodesList,
        errors,
        validationErrors,
        password,
        processing,
        isRecoveryCodesVisible,
        showSetupModal,
        requiresConfirmation,
        twoFactorEnabled,
        hasSetupData,
        clearSetupData,
        clearErrors,
        clearTwoFactorAuthData,
        confirmPassword,
        enableTwoFactor,
        handleShowRecoveryCodes,
        handleRegenerateRecoveryCodes,
        fetchQrCode,
        fetchSetupKey,
        fetchSetupData,
        fetchRecoveryCodes
    };
};
