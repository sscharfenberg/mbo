<script setup lang="ts">
import { computed, ref } from "vue";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import { useTwoFactorAuth } from "Composables/useTwoFactorAuth.ts";
const {
    handleRegenerateRecoveryCodes,
    handleShowRecoveryCodes,
    isRecoveryCodesVisible,
    password,
    processing,
    recoveryCodesList,
    requiresConfirmation,
    validationErrors
} = useTwoFactorAuth();
const showPassword = ref(false);
const recoveryCodesString = computed(() => recoveryCodesList.value.join("\n"));
</script>

<template>
    <div>
        <form class="form" @submit.prevent="handleShowRecoveryCodes" v-if="!isRecoveryCodesVisible">
            <form-legend>{{ $t("pages.dashboard.two_factor.recovery_codes.explanation") }}</form-legend>
            <form-group
                v-if="requiresConfirmation && !isRecoveryCodesVisible"
                for-id="password"
                :label="$t('form.fields.password')"
                :error="validationErrors.password"
                :invalid="!!validationErrors.password"
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
                        <icon :name="showPassword ? 'visibility-off' : 'visibility-on'" />
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
            <form-group v-if="!isRecoveryCodesVisible">
                <button type="submit" class="btn-default" :disabled="processing">
                    <icon name="visibility-on" />
                    {{ $t("pages.dashboard.two_factor.recovery_codes.show") }}
                    <loading-spinner v-if="processing" :size="2" />
                </button>
            </form-group>
        </form>
        <form
            v-else-if="isRecoveryCodesVisible && recoveryCodesList.length"
            class="form"
            @submit.prevent="handleRegenerateRecoveryCodes"
        >
            <form-legend>{{ $t("pages.dashboard.two_factor.regenerate_codes.explanation") }}</form-legend>
            <form-group :label="$t('pages.dashboard.two_factor.recovery_codes.label')">
                <div class="form-input form-input__textarea">
                    <textarea :value="recoveryCodesString" readonly />
                </div>
            </form-group>
            <form-group>
                <button class="btn-default" type="submit" :disabled="processing">
                    {{ $t("pages.dashboard.two_factor.regenerate_codes.submit") }}
                </button>
            </form-group>
        </form>
    </div>
</template>
