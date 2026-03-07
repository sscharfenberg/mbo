<script setup lang="ts">
import { computed, ref } from "vue";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import { useTwoFactorAuth } from "Composables/useTwoFactorAuth.ts";

const {
    handleRegenerateRecoveryCodes,
    handleShowRecoveryCodes,
    isRecoveryCodesVisible,
    processing,
    recoveryCodesList,
    requiresConfirmation,
    validationErrors
} = useTwoFactorAuth();
const recoveryCodesString = computed(() => recoveryCodesList.value.join("\n"));
const password = ref("");
const showPassword = ref(false);

const onSubmit = (e: SubmitEvent) => {
    const action = (e.submitter as HTMLButtonElement | null)?.value;
    if (action === "show") handleShowRecoveryCodes(password.value);
    else if (action === "regenerate") handleRegenerateRecoveryCodes(password.value);
};
</script>

<template>
    <headline :size="4">{{ $t("pages.dashboard.two_factor.recovery_codes.headline") }}</headline>
    <form class="form" @submit.prevent="onSubmit">
        <form-legend :required="requiresConfirmation">{{
            $t("pages.dashboard.two_factor.recovery_codes.explanation")
        }}</form-legend>
        <form-group
            v-if="requiresConfirmation && !isRecoveryCodesVisible"
            for-id="recovery-codes-password"
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
                id="recovery-codes-password"
                class="form-input"
            />
            <template #button>
                <button
                    type="button"
                    @mousedown.prevent
                    @click="showPassword = !showPassword"
                    :aria-label="showPassword ? $t('form.elements.password_hide') : $t('form.elements.password_show')"
                    tabindex="-1"
                >
                    <icon :name="showPassword ? 'visibility-off' : 'visibility-on'" />
                    <span>{{
                        showPassword ? $t("form.elements.password_hide") : $t("form.elements.password_show")
                    }}</span>
                </button>
            </template>
        </form-group>
        <form-group v-if="!isRecoveryCodesVisible">
            <button type="submit" name="action" value="show" class="btn-default" :disabled="processing">
                <icon name="visibility-on" />
                {{ $t("pages.dashboard.two_factor.recovery_codes.show") }}
                <loading-spinner v-if="processing" :size="2" />
            </button>
        </form-group>
        <template v-if="isRecoveryCodesVisible">
            <form-group :label="$t('pages.dashboard.two_factor.recovery_codes.label')">
                <div class="form-input form-input__textarea">
                    <textarea :value="recoveryCodesString" readonly />
                </div>
            </form-group>
            <form-legend>{{ $t("pages.dashboard.two_factor.regenerate_codes.explanation") }}</form-legend>
            <form-group>
                <button type="submit" name="action" value="regenerate" class="btn-default" :disabled="processing">
                    {{ $t("pages.dashboard.two_factor.regenerate_codes.submit") }}
                </button>
            </form-group>
        </template>
    </form>
</template>

<style lang="scss" scoped>
.form {
    margin-bottom: 1lh;
}
</style>
