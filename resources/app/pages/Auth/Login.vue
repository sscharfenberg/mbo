<script setup lang="ts">
import { Head, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import Checkbox from "Components/Form/Checkbox.vue";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import NarrowLayout from "Components/Layout/NarrowLayout.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import LabelledLink from "Components/UI/LabelledLink.vue";
import LinkGroup from "Components/UI/LinkGroup.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import { useLogin } from "Composables/useLogin.ts";
defineOptions({ layout: NarrowLayout });
defineProps<{
    status?: string;
}>();
const page = usePage();
const features = computed(() => page.props.features);
const showPassword = ref(false);
const { errors, name, password, remember, requiresTwoFactor, recoveryCode, showRecoveryCode, processing, submit } =
    useLogin();
</script>

<template>
    <Head
        ><title>{{ $t("pages.login.title") }}</title></Head
    >
    <headline>
        <icon name="key" :size="3" />
        {{ $t("pages.login.title") }}
    </headline>
    <form class="form" @submit.prevent="submit">
        <form-legend :required="true" :two-factor="requiresTwoFactor" />
        <form-group
            v-if="!requiresTwoFactor"
            for-id="name"
            :label="$t('form.fields.username')"
            :error="errors.name ?? ''"
            addon-icon="register"
            :required="true"
            :invalid="!!errors?.name"
        >
            <input v-model="name" type="text" name="name" id="name" class="form-input" />
        </form-group>
        <form-group
            v-if="!requiresTwoFactor"
            for-id="password"
            :label="$t('form.fields.password')"
            :error="errors.password ?? ''"
            :invalid="!!errors?.password"
            :required="true"
        >
            <template #addon>
                <button
                    type="button"
                    class="form-group__addon"
                    @click.prevent="showPassword = !showPassword"
                    :aria-label="showPassword ? $t('form.elements.password_hide') : $t('form.elements.password_show')"
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
        <form-group v-if="!requiresTwoFactor" for-id="remember" :label="$t('form.fields.remember')">
            <template #addon>
                <checkbox
                    :label="$t('form.fields.remember')"
                    ref-id="remember"
                    value="true"
                    :checked-initially="true"
                    @change="remember = $event"
                />
            </template>
        </form-group>
        <form-group
            v-if="requiresTwoFactor"
            for-id="code"
            :label="showRecoveryCode ? $t('pages.login.2fa.recovery_code') : $t('pages.login.2fa.2fa_code')"
            :error="showRecoveryCode ? (errors.recovery_code ?? '') : (errors.code ?? '')"
            :invalid="showRecoveryCode ? !!errors.recovery_code : !!errors.code"
            :required="true"
        >
            <input
                id="code"
                v-model="recoveryCode"
                type="text"
                name="code"
                class="form-input"
                autocomplete="one-time-code"
                autofocus
            />
        </form-group>
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="key" />
                {{ requiresTwoFactor ? $t("pages.login.2fa.verify") : $t("pages.login.submit") }}
                <loading-spinner v-if="processing" :size="2" />
            </button>
        </form-group>
        <form-group v-if="requiresTwoFactor">
            <button
                type="button"
                class="btn-default"
                :disabled="processing"
                @click="showRecoveryCode = !showRecoveryCode"
            >
                {{ showRecoveryCode ? $t("pages.login.2fa.toggle.2fa") : $t("pages.login.2fa.toggle.recovery") }}
            </button>
        </form-group>
        <form-group>
            <link-group :label="$t('pages.login.nav-label')">
                <labelled-link v-if="features.registration" href="/register">{{
                    $t("pages.register.link")
                }}</labelled-link>
                <labelled-link v-if="features.resetPasswords" href="/forgot">{{
                    $t("pages.forgot.link")
                }}</labelled-link>
                <labelled-link v-if="features.emailVerification" href="/resend-verification">{{
                    $t("pages.resend-verification.link")
                }}</labelled-link>
            </link-group>
        </form-group>
    </form>
</template>
