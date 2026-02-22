<script setup lang="ts">
import { router, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import FormGroup from "Components/Form/FormGroup.vue";
import Badge from "Components/Visual/Badge.vue";
import Headline from "Components/Visual/Headline.vue";
import Icon from "Components/Visual/Icon.vue";
import LoadingSpinner from "Components/Visual/LoadingSpinner.vue";
import Paragraph from "Components/Visual/Paragraph.vue";
const page = usePage();
const requiresConfirmation = computed(() => page.props.requiresConfirmation as boolean);
const twoFactorEnabled = computed(() => page.props.twoFactorEnabled as boolean);
const showSetupModal = ref(false);
const showPassword = ref(false);
const processing = ref(false);
const password = ref("");
const errors = ref<Record<string, string>>({});
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
        errors.value = Object.fromEntries(
            Object.entries(data.errors ?? {}).map(([key, msgs]) => [key, Array.isArray(msgs) ? msgs[0] : msgs])
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
const enableTwoFactor = async () => {
    processing.value = true;
    errors.value = {};

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
</script>

<template>
    <headline :size="3">
        {{ $t("pages.dashboard.two-factor.headline") }}
        <template #right>
            <badge v-if="!twoFactorEnabled" type="warning"><icon name="security" />{{ $t("state.disabled") }}</badge>
            <badge v-else type="success"><icon name="key" />{{ $t("state.enabled") }}</badge>
        </template>
    </headline>
    <Paragraph>{{ $t("pages.dashboard.two-factor.intro") }}</Paragraph>
    <section v-if="!twoFactorEnabled">
        <form class="form" @submit.prevent="enableTwoFactor">
            <form-group
                v-if="requiresConfirmation"
                for-id="password"
                :label="$t('form.fields.password')"
                :error="errors.password"
                :required="true"
            >
                <template #addon>
                    <button
                        type="button"
                        class="form-group__addon"
                        @click.prevent="showPassword = !showPassword"
                        :aria-label="
                            showPassword ? $t('form.elements.password_hide') : $t('form.elements.password_show')
                        "
                        tabindex="-1"
                    >
                        <icon :name="showPassword ? 'visibility_off' : 'visibility_on'" />
                    </button>
                </template>
                <input
                    v-model="password"
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    id="password"
                    class="form-input"
                />
            </form-group>
            <form-group>
                <button type="submit" class="btn-primary" :disabled="processing">
                    <icon name="security" />
                    {{ $t("pages.dashboard.two-factor.enable") }}
                    <loading-spinner v-if="processing" :size="2" />
                </button>
            </form-group>
        </form>
    </section>
    <section v-else>enabled</section>
</template>
