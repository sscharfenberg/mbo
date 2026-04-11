<script setup lang="ts">
import Badge from "Components/UI/Badge.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import { useTwoFactorAuth } from "Composables/useTwoFactorAuth.ts";
import TwoFactorDisabled from "./TwoFactorDisabled.vue";
import TwoFactorEnabled from "./TwoFactorEnabled.vue";
import TwoFactorModal from "./TwoFactorModal.vue";
const { twoFactorEnabled, requiresConfirmation, showSetupModal, clearSetupData } = useTwoFactorAuth();
/**
 * Closes the setup modal and resets transient setup data.
 * Lives on the parent so the modal survives the
 * {@link TwoFactorDisabled} → {@link TwoFactorEnabled} swap that
 * happens as soon as Fortify flips `twoFactorEnabled` to true.
 */
const handleModalClose = () => {
    showSetupModal.value = false;
    clearSetupData();
};
</script>

<template>
    <headline :size="3" anchor-id="2faSection">
        <icon name="security" />
        {{ $t("pages.dashboard.two_factor.headline") }}
        <template #right>
            <badge v-if="!twoFactorEnabled" type="warning"><icon name="key" />{{ $t("state.disabled") }}</badge>
            <badge v-else type="success"><icon name="security" />{{ $t("state.enabled") }}</badge>
        </template>
    </headline>
    <TwoFactorDisabled v-if="!twoFactorEnabled" />
    <TwoFactorEnabled v-else />
    <two-factor-modal v-if="showSetupModal" :requiresConfirmation="requiresConfirmation" @close="handleModalClose" />
</template>
