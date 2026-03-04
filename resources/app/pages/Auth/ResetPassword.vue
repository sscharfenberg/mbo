<script setup lang="ts">
import { Form, Head } from "@inertiajs/vue3";
import { ref } from "vue";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import NarrowLayout from "Components/Layout/NarrowLayout.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import PasswordStrength from "Components/UI/PasswordStrength.vue";
import { usePasswordEntropy } from "Composables/usePasswordEntropy";
defineOptions({ layout: NarrowLayout });
const props = defineProps<{
    token: string;
    email: string;
}>();
const inputEmail = ref(props.email);
const { password, score, onPasswordChange } = usePasswordEntropy();
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);
</script>

<template>
    <Head
        ><title>{{ $t("pages.reset_password.title") }}</title></Head
    >
    <headline>
        <icon name="keyboard" :size="3" />
        {{ $t("pages.reset_password.title") }}
    </headline>
    <Form
        action="/reset-password"
        method="post"
        class="form"
        #default="{ errors, valid, invalid, validating, validate, processing }"
    >
        <form-legend>{{ $t("pages.reset_password.intro") }}</form-legend>
        <form-group
            for-id="email"
            :label="$t('form.fields.email')"
            :error="errors.email"
            :invalid="false"
            :validated="true"
            :validating="validating"
            addon-icon="mail"
            :required="true"
        >
            <input
                type="email"
                name="email"
                id="email"
                @change="validate('email')"
                class="form-input"
                v-model="inputEmail"
                readonly
            />
        </form-group>
        <form-group
            for-id="password"
            :label="$t('form.fields.password')"
            :error="errors.password"
            :invalid="invalid('password')"
            :validated="valid('password')"
            :validating="validating"
            :required="true"
            addon-icon="key"
        >
            <input
                :type="showPassword ? 'text' : 'password'"
                name="password"
                id="password"
                @change="validate('password')"
                @keyup="onPasswordChange"
                class="form-input"
                v-model="password"
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
            <template #text><PasswordStrength v-if="score !== null" :score="score" /></template>
        </form-group>
        <form-group
            for-id="password_confirmation"
            :label="$t('form.fields.password_confirmation')"
            :error="errors.password_confirmation"
            :invalid="invalid('password_confirmation')"
            :validated="valid('password_confirmation')"
            :validating="validating"
            :required="true"
            addon-icon="key"
        >
            <input
                :type="showPasswordConfirmation ? 'text' : 'password'"
                name="password_confirmation"
                id="password_confirmation"
                @change="validate('password_confirmation')"
                class="form-input"
            />
            <template #button>
                <button
                    type="button"
                    @mousedown.prevent
                    @click="showPasswordConfirmation = !showPasswordConfirmation"
                    :aria-label="showPasswordConfirmation ? $t('form.elements.password_hide') : $t('form.elements.password_show')"
                    tabindex="-1"
                >
                    <icon :name="showPasswordConfirmation ? 'visibility-off' : 'visibility-on'" />
                    {{ showPasswordConfirmation ? $t("form.elements.password_hide") : $t("form.elements.password_show") }}
                </button>
            </template>
        </form-group>
        <input type="hidden" name="token" :value="token" />
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="save" />
                {{ $t("pages.reset_password.submit") }}
                <loading-spinner v-if="processing" :size="2" />
            </button>
        </form-group>
    </Form>
</template>
