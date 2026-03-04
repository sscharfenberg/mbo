<script setup lang="ts">
import { Form } from "@inertiajs/vue3";
import { nextTick, onMounted, ref } from "vue";
import { useClipboard } from "@/composables/useClipboard";
import { useTwoFactorAuth } from "@/composables/useTwoFactorAuth";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import OTPInput from "Components/Form/OTPInput/OTPInput.vue";
import Modal from "Components/Modal/Modal.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
type Props = {
    requiresConfirmation?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    requiresConfirmation: false
});

const { qrCodeSvg, manualSetupKey, fetchSetupData } = useTwoFactorAuth();
const { copy, copied } = useClipboard();
const showVerificationStep = ref(false);
const code = ref<string>("");

onMounted(async () => {
    if (!qrCodeSvg.value) {
        await fetchSetupData();
    }
});
const emit = defineEmits(["close"]);

const handleModalNextStep = async () => {
    if (props.requiresConfirmation) {
        showVerificationStep.value = true;
        await nextTick();
        document.querySelector<HTMLInputElement>("[data-input-otp]")?.focus();
        return;
    }
    emit("close");
};
</script>

<template>
    <modal>
        <template #header>
            <span v-if="!showVerificationStep">{{ $t("pages.dashboard.two_factor.setup.title") }}</span>
            <span v-else>{{ $t("pages.dashboard.two_factor.verification.title") }}</span>
        </template>
        <form v-if="!showVerificationStep" class="form">
            <form-legend>{{ $t("pages.dashboard.two_factor.setup.explanation") }}</form-legend>
            <loading-spinner v-if="!qrCodeSvg" :size="2" />
            <form-group v-else :label="$t('pages.dashboard.two_factor.setup.qrcode_label')">
                <div v-html="qrCodeSvg" />
            </form-group>
            <loading-spinner v-if="!manualSetupKey" :size="2" />
            <form-group
                v-else
                for-id="manualSetupKey"
                :label="$t('pages.dashboard.two_factor.setup.setupkey_label')"
                addon-icon="key"
            >
                <input id="manualSetupKey" class="form-input" type="text" readonly :value="manualSetupKey" />
                <template #button>
                    <button type="button" @click="copy(manualSetupKey ?? '')">
                        <icon :name="copied ? 'check' : 'copy'" />
                        {{ copied ? $t("pages.dashboard.two_factor.setup.copied") : $t("pages.dashboard.two_factor.setup.copy") }}
                    </button>
                </template>
            </form-group>
            <form-group>
                <button class="btn-primary" @click="handleModalNextStep">
                    {{ $t("pages.dashboard.two_factor.setup.next") }}
                </button>
            </form-group>
        </form>
        <Form
            v-else
            action="/user/confirmed-two-factor-authentication"
            error-bag="confirmTwoFactorAuthentication"
            class="form"
            reset-on-error
            @success="$emit('close')"
            method="post"
            v-slot="{ errors, processing, submit }"
        >
            <form-legend>{{ $t("pages.dashboard.two_factor.verification.explanation") }}</form-legend>
            <form-group
                :error="errors?.code"
                :invalid="!!errors?.code"
                :label="$t('pages.login.2fa.2fa_code')"
                :required="true"
                for-id="code"
            >
                <OTPInput
                    id="code"
                    v-model="code"
                    name="code"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    :maxlength="6"
                    autofocus
                    @complete="() => submit()"
                />
            </form-group>
            <form-group>
                <button type="submit" class="btn-primary" :disabled="processing || code.length < 6">
                    <icon name="check" />
                    {{ $t("pages.dashboard.two_factor.verification.submit") }}
                    <loading-spinner v-if="processing" :size="2" />
                </button>
            </form-group>
            <form-group>
                <button type="button" class="btn-default" @click="showVerificationStep = false" :disabled="processing">
                    {{ $t("pages.dashboard.two_factor.verification.back") }}
                </button>
            </form-group>
        </Form>
    </modal>
</template>
