import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import type { Ref } from "vue";

export type UseDeleteAccountReturn = {
    processing: Ref<boolean>;
    passwordError: Ref<string>;
    deleteAccount: (password: string) => void;
};

/**
 * Composable that manages account deletion.
 *
 * Sends a DELETE request via Inertia. On success the server logs the user out,
 * deletes the account, and redirects to the home page with a flash message.
 * On 422 the password error is surfaced in the form.
 */
export const useDeleteAccount = (): UseDeleteAccountReturn => {
    const processing = ref(false);
    const passwordError = ref("");

    const deleteAccount = (password: string): void => {
        processing.value = true;
        passwordError.value = "";
        router.delete("/user/delete", {
            data: { password },
            onError: (errors) => {
                passwordError.value = errors.password ?? "";
            },
            onFinish: () => {
                processing.value = false;
            }
        });
    };

    return { processing, passwordError, deleteAccount };
};