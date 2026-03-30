import { router, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import type { Ref } from "vue";

type TwoFactorLoginResponse = { two_factor?: boolean; redirect?: string };

export type UseLoginWithTwoFactorReturn = {
    errors: Ref<Record<string, string>>;
    name: Ref<string>;
    password: Ref<string>;
    remember: Ref<boolean>;
    requiresTwoFactor: Ref<boolean>;
    recoveryCode: Ref<string>;
    showRecoveryCode: Ref<boolean>;
    processing: Ref<boolean>;
    submit: () => Promise<void>;
};

/**
 * Composable that handles the login + two-factor challenge flow on one page.
 *
 * Uses JSON requests for `/login` so Fortify returns `{ two_factor: true }`
 * instead of redirecting to `route('two-factor.login')`.
 */
export const useLogin = (): UseLoginWithTwoFactorReturn => {
    const page = usePage();
    const csrfToken = computed(() => page.props.csrfToken as string);

    const errors = ref<Record<string, string>>({});
    const name = ref("");
    const password = ref("");
    const remember = ref(true);
    const requiresTwoFactor = ref(false);
    const recoveryCode = ref("");
    const showRecoveryCode = ref(false);
    const processing = ref(false);

    /**
     * Normalize backend validation errors into a flat key/message object.
     *
     * Fortify returns validation errors as arrays per field. The login UI only
     * needs the first message per field for compact inline display.
     *
     * @param response - Failed HTTP response containing an `errors` payload.
     */
    const mapErrors = async (response: Response): Promise<void> => {
        const data = await response.json();
        errors.value = Object.fromEntries(
            Object.entries(data.errors ?? {}).map(([key, messages]) => [
                key,
                Array.isArray(messages) ? String(messages[0]) : String(messages)
            ])
        );
    };

    /**
     * Submit credentials to Fortify's login endpoint using a JSON request.
     *
     * JSON mode is important because Fortify then returns `{ two_factor: true }`
     * instead of redirecting to `route('two-factor.login')`, allowing this app
     * to keep the full challenge flow on the same Login page.
     */
    const submitLogin = async (): Promise<void> => {
        processing.value = true;
        errors.value = {};

        const response = await fetch("/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": csrfToken.value
            },
            body: JSON.stringify({
                name: name.value,
                password: password.value,
                remember: remember.value
            })
        });

        if (response.status === 422) {
            await mapErrors(response);
            processing.value = false;
            return;
        }

        if (!response.ok) {
            processing.value = false;
            return;
        }

        const data = (await response.json().catch(() => ({}))) as TwoFactorLoginResponse;

        if (data.two_factor) {
            requiresTwoFactor.value = true;
            password.value = "";
            processing.value = false;
            return;
        }

        router.visit(data.redirect ?? "/dashboard");
    };

    /**
     * Submit the second-factor challenge to Fortify.
     *
     * Sends either an authenticator code (`code`) or a backup recovery code
     * (`recovery_code`) depending on the current UI mode. On success, the user
     * is fully authenticated and redirected to the dashboard.
     */
    const submitTwoFactorChallenge = async (): Promise<void> => {
        processing.value = true;
        errors.value = {};

        const payload = showRecoveryCode.value ? { recovery_code: recoveryCode.value } : { code: recoveryCode.value };

        const response = await fetch("/two-factor-challenge", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": csrfToken.value
            },
            body: JSON.stringify(payload)
        });

        if (response.status === 422) {
            await mapErrors(response);
            processing.value = false;
            return;
        }

        if (response.ok) {
            const data = (await response.json().catch(() => ({}))) as { redirect?: string };
            router.visit(data.redirect ?? "/dashboard");
            return;
        }

        processing.value = false;
    };

    /**
     * Unified submit handler for the Login page.
     *
     * Delegates to password login first and, once Fortify indicates that
     * second-factor verification is required, delegates to the challenge
     * endpoint on subsequent submits.
     */
    const submit = async (): Promise<void> => {
        if (requiresTwoFactor.value) {
            await submitTwoFactorChallenge();
            return;
        }
        await submitLogin();
    };

    return {
        errors,
        name,
        password,
        remember,
        requiresTwoFactor,
        recoveryCode,
        showRecoveryCode,
        processing,
        submit
    };
};
