<script setup lang="ts">
import { ref } from "vue";
import FormGroup from "Components/Form/FormGroup.vue";
import Badge from "Components/Visual/Badge.vue";
import Headline from "Components/Visual/Headline.vue";
import Icon from "Components/Visual/Icon.vue";
import LoadingSpinner from "Components/Visual/LoadingSpinner.vue";
import Paragraph from "Components/Visual/Paragraph.vue";
import { useTwoFactorAuth } from "Composables/useTwoFactorAuth.ts";
import TwoFactorModal from "./TwoFactorModal.vue";
const {
    password,
    processing,
    validationErrors,
    requiresConfirmation,
    twoFactorEnabled,
    enableTwoFactor,
    showSetupModal
} = useTwoFactorAuth();
const showPassword = ref(false);
</script>

<template>
    <headline :size="3">
        {{ $t("pages.dashboard.two-factor.headline") }}
        <template #right>
            <badge v-if="!twoFactorEnabled" type="warning"><icon name="security" />{{ $t("state.disabled") }}</badge>
            <badge v-else type="success"><icon name="key" />{{ $t("state.enabled") }}</badge>
        </template>
    </headline>
    <Paragraph>
        <i18n-t keypath="pages.dashboard.two-factor.intro" scope="global">
            <template #totp
                ><strong>{{ $t("pages.dashboard.two-factor.totp") }}</strong></template
            >
            <template #tool1
                ><cite>{{ $t("pages.dashboard.two-factor.tool1") }}</cite></template
            >
            <template #tool2
                ><cite>{{ $t("pages.dashboard.two-factor.tool2") }}</cite></template
            >
        </i18n-t>
    </Paragraph>
    <section v-if="!twoFactorEnabled">
        <form class="form" @submit.prevent="enableTwoFactor">
            <form-group
                v-if="requiresConfirmation"
                for-id="password"
                :label="$t('form.fields.password')"
                :error="validationErrors.password"
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
    <!--    <two-factor-modal :show="showSetupModal" />-->
    <two-factor-modal />
</template>
