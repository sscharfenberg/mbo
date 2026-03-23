<script setup lang="ts">
import { computed, ref } from "vue";
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
const legendItems = computed(() => {
    const items = [{ slot: "intro", icon: "info" }];
    if (requiresConfirmation.value) items.push({ slot: "required", icon: "info" });
    return items;
});
</script>

<template>
    <TwoFactorRecoveryCodes />
    <headline :size="4">{{ $t("pages.dashboard.two_factor.disable_section.headline") }}</headline>
    <form class="form" @submit.prevent="disableTwoFactor(password)">
        <form-legend :items="legendItems">
            <template #intro>{{ $t("pages.dashboard.two_factor.disable_section.explanation") }}</template>
            <template #required>
                <i18n-t keypath="form.legend.required" scope="global">
                    <template #icon><icon name="required" /></template>
                </i18n-t>
            </template>
        </form-legend>
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
                {{ $t("pages.dashboard.two_factor.disable") }}
                <loading-spinner v-if="processing" :size="2" />
            </button>
        </form-group>
    </form>
</template>
