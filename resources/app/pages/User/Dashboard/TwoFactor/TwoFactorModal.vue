<script setup lang="ts">
import { Form } from "@inertiajs/vue3";
import { onMounted, ref, nextTick, useTemplateRef } from "vue";
import { useTwoFactorAuth } from "@/composables/useTwoFactorAuth";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import Modal from "Components/Modal/Modal.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
type Props = {
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    requiresConfirmation: false,
    twoFactorEnabled: false
});

const { qrCodeSvg, manualSetupKey, clearSetupData, fetchSetupData, errors } = useTwoFactorAuth();
const showVerificationStep = ref(false);
const codeInputRef = useTemplateRef("codeInputRef");
const code = ref<string>("");

onMounted(async () => {
    if (!qrCodeSvg.value) {
        await fetchSetupData();
    }
});
const emit = defineEmits(["close"]);

const handleModalNextStep = () => {
    if (props.requiresConfirmation) {
        showVerificationStep.value = true;

        nextTick(() => {
            codeInputRef.value?.focus();
        });

        return;
    }
    clearSetupData();
    emit("close");
};
const resetModalState = () => {
    if (props.twoFactorEnabled) {
        clearSetupData();
    }

    showVerificationStep.value = false;
    code.value = "";
};
</script>

<template>
    <modal>
        <template #header>{{ $t("pages.dashboard.two_factor.setup.title") }}</template>
        <div v-if="!showVerificationStep">
            <div v-if="errors?.length">ERRORS {{ errors }}</div>
            <form v-else class="form">
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
                >
                    <input id="manualSetupKey" class="form-input" type="text" readonly :value="manualSetupKey" />
                </form-group>
                <form-group>
                    <button class="btn-primary" @click="handleModalNextStep">
                        {{ $t("pages.dashboard.two_factor.setup.next") }}
                    </button>
                </form-group>
            </form>
        </div>
        <div v-else>
            <Form
                action="/user/confirmed-two-factor-authentication"
                error-bag="confirmTwoFactorAuthentication"
                reset-on-error
                @success="
                    clearSetupData();
                    resetModalState();
                    $emit('close');
                "
                method="post"
                v-slot="{ errors, processing }"
            >
                <input ref="codeInputRef" v-model="code" type="text" name="code" id="code" class="form-input" />
                {{ errors?.code }}
                <button
                    type="button"
                    class="w-auto flex-1"
                    @click="showVerificationStep = false"
                    :disabled="processing"
                >
                    Back
                </button>
                {{ processing }} {{ code }}
                <button type="submit" class="w-auto flex-1" :disabled="processing || code.length < 6">Confirm</button>
            </Form>
        </div>
    </modal>
</template>
