<script setup lang="ts">
import { ref } from "vue";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import { useTwoFactorAuth } from "Composables/useTwoFactorAuth.ts";
import TwoFactorRecoveryCodes from "./TwoFactorRecoveryCodes.vue";
const { processing, validationErrors, requiresConfirmation, disableTwoFactor } = useTwoFactorAuth();
const password = ref("");
const showPassword = ref(false);
</script>

<template>
    <TwoFactorRecoveryCodes />
    <headline :size="4">{{ $t("pages.dashboard.two_factor.disable_section.headline") }}</headline>
    <form class="form" @submit.prevent="disableTwoFactor(password)">
        <form-legend :required="requiresConfirmation">{{
            $t("pages.dashboard.two_factor.disable_section.explanation")
        }}</form-legend>
        <form-group
            v-if="requiresConfirmation"
            for-id="disable-password"
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
                id="disable-password"
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
                    {{ showPassword ? $t("form.elements.password_hide") : $t("form.elements.password_show") }}
                </button>
            </template>
        </form-group>
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="security" />
                {{ $t("pages.dashboard.two_factor.disable") }}
                <loading-spinner v-if="processing" :size="2" />
            </button>
        </form-group>
    </form>
</template>
