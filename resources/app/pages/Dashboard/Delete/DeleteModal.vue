<script setup lang="ts">
import { onMounted, ref } from "vue";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import Modal from "Components/Modal/Modal.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import { useDeleteAccount } from "Composables/useDeleteAccount.ts";

const emit = defineEmits<{ close: [] }>();

const showPassword = ref(false);
const password = ref("");
const passwordRef = ref<HTMLInputElement | null>(null);

onMounted(() => passwordRef.value?.focus());

const { processing, passwordError, deleteAccount } = useDeleteAccount();

const onSubmit = () => deleteAccount(password.value);
</script>

<template>
    <modal @close="emit('close')">
        <template #header>
            {{ $t("pages.dashboard.deletion.modal.title") }}
        </template>
        <form class="form" @submit.prevent="onSubmit">
            <form-legend>{{ $t("pages.dashboard.deletion.modal.explanation") }}</form-legend>
            <form-group
                for-id="password"
                :label="$t('form.fields.password')"
                :required="true"
                addon-icon="key"
                :error="passwordError"
                :invalid="!!passwordError"
            >
                <input
                    ref="passwordRef"
                    v-model="password"
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    id="password"
                    class="form-input"
                    autocomplete="current-password"
                />
                <template #button>
                    <button
                        type="button"
                        @mousedown.prevent
                        @click="showPassword = !showPassword"
                        :aria-label="
                            showPassword ? $t('form.elements.password_hide') : $t('form.elements.password_show')
                        "
                        tabindex="-1"
                    >
                        <icon :name="showPassword ? 'visibility-off' : 'visibility-on'" />
                        <span>{{
                            showPassword ? $t("form.elements.password_hide") : $t("form.elements.password_show")
                        }}</span>
                    </button>
                </template>
            </form-group>
            <form-group>
                <button type="submit" class="btn-primary" :disabled="processing || !password">
                    <icon name="delete" />
                    {{ $t("pages.dashboard.deletion.modal.submit") }}
                    <loading-spinner v-if="processing" :size="2" />
                </button>
            </form-group>
        </form>
    </modal>
</template>
