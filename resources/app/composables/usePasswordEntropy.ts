import { debounce, type DebouncedFunc } from "lodash-es";
import { type Ref, ref } from "vue";

/**
 * Composable for real-time password entropy checking.
 *
 * Provides a reactive password ref, a debounced handler that posts the
 * current value to the server-side zxcvbn endpoint, and a reactive score
 * ref (0-4) updated with each response.
 *
 * @returns {{ password: Ref<string>, score: Ref<number | null>, onPasswordChange: DebouncedFunc<() => void>, reset: () => void }}
 */
export function usePasswordEntropy(): {
    password: Ref<string>;
    score: Ref<number | null>;
    onPasswordChange: DebouncedFunc<() => void>;
    reset: () => void;
} {
    const password = ref("");
    const score = ref<number | null>(null);

    const onPasswordChange = debounce(
        () => {
            if (!password.value.length) return;
            fetch("/api/auth/entropy", {
                method: "POST",
                headers: { "Content-Type": "application/json", Accept: "application/json" },
                body: JSON.stringify({ p: password.value })
            })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    score.value = data.score;
                })
                .catch(error => {
                    console.error(error);
                });
        },
        750,
        { maxWait: 5000 }
    );

    const reset = () => {
        password.value = "";
        score.value = null;
    };

    return { password, score, onPasswordChange, reset };
}
