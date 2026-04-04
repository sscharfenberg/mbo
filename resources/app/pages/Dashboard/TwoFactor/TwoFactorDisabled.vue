<script setup lang="ts">
import { ref } from "vue";
import FormGroup from "Components/Form/FormGroup.vue";
import Icon from "Components/UI/Icon.vue";
import LabelledLink from "Components/UI/LabelledLink.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import Paragraph from "Components/UI/Paragraph.vue";
import { useTwoFactorAuth } from "Composables/useTwoFactorAuth.ts";
import TwoFactorModal from "./TwoFactorModal.vue";
const { processing, validationErrors, requiresConfirmation, showSetupModal, enableTwoFactor, clearSetupData } =
    useTwoFactorAuth();
/** Password input bound to the confirmation field (only shown when the session requires re-confirmation). */
const password = ref("");
/** Toggles the password field between `text` and `password` type for visibility. */
const showPassword = ref(false);
/**
 * Resets all local state and shared composable data when the setup modal is dismissed.
 * Called on both successful activation and user cancellation.
 */
const handleModalClose = () => {
    showSetupModal.value = false;
    password.value = "";
    showPassword.value = false;
    clearSetupData();
};
</script>

<template>
    <form class="form" @submit.prevent="enableTwoFactor(password)">
        <Paragraph style="margin: 0">
            <i18n-t keypath="pages.dashboard.two_factor.intro" scope="global">
                <template #totp
                    ><strong>{{ $t("pages.dashboard.two_factor.totp") }}</strong></template
                >
                <template #tool1
                    ><labelled-link href="https://bitwarden.com/">{{
                        $t("pages.dashboard.two_factor.tool1")
                    }}</labelled-link></template
                >
                <template #tool2
                    ><labelled-link href="https://www.enpass.io/">{{
                        $t("pages.dashboard.two_factor.tool2")
                    }}</labelled-link></template
                >
            </i18n-t>
            <span v-if="requiresConfirmation"><br />{{ $t("pages.dashboard.two_factor.requires_confirmation") }}</span>
        </Paragraph>
        <form-group
            v-if="requiresConfirmation"
            for-id="password_enable_2fa"
            :label="$t('form.fields.password')"
            :error="validationErrors.password"
            :invalid="!!validationErrors.password"
            :required="true"
            addon-icon="key"
        >
            <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                name="password"
                id="password_enable_2fa"
                class="form-input"
            />
            <template #button>
                <button
                    type="button"
                    @click.prevent="showPassword = !showPassword"
                    :aria-label="showPassword ? $t('components.password.hide') : $t('components.password.show')"
                    tabindex="-1"
                >
                    <icon :name="showPassword ? 'visibility-off' : 'visibility-on'" />
                    <span>{{ showPassword ? $t("components.password.hide") : $t("components.password.show") }}</span>
                </button>
            </template>
        </form-group>
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="security" />
                {{ $t("pages.dashboard.two_factor.enable") }}
                <loading-spinner v-if="processing" :size="2" />
            </button>
        </form-group>
    </form>
    <two-factor-modal v-if="showSetupModal" :requiresConfirmation="requiresConfirmation" @close="handleModalClose" />
</template>
