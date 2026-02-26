<script setup lang="ts">
import { Form } from "@inertiajs/vue3";
import { useClipboard } from "@vueuse/core";
import { onMounted, ref, nextTick, useTemplateRef } from "vue";
import { useTwoFactorAuth } from "@/composables/useTwoFactorAuth";
import Modal from "Components/Modal/Modal.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import Paragraph from "Components/UI/Paragraph.vue";
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
const { copy, copied } = useClipboard();
</script>

<template>
    <modal>
        <template #header>Enable Two-Factor Authentication</template>
        <div v-if="!showVerificationStep">
            <div v-if="errors?.length">ERRORS {{ errors }}</div>
            <div v-else>
                <loading-spinner v-if="!qrCodeSvg" :size="2" />
                <div v-else>
                    <paragraph>
                        finish enabling two-factor authentication, scan the QR code or enter the setup key in your
                        authenticator app</paragraph
                    >
                    <div v-html="qrCodeSvg" />
                    <button class="w-full" @click="handleModalNextStep">Next step</button>
                </div>
                or, enter the code manually
                <loading-spinner v-if="!manualSetupKey" :size="2" />
                <div v-else>
                    <input type="text" readonly :value="manualSetupKey" />
                    <button @click="copy(manualSetupKey || '')">
                        <span v-if="copied" class="w-4 text-green-500">Copied</span>
                        <span v-else>Copy</span>
                    </button>
                </div>
                <button class="btn-primary" @click="handleModalNextStep">Next Step</button>
            </div>
        </div>
        <div v-else>
            <Form
                action="/user/confirmed-two-factor-authentication"
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
