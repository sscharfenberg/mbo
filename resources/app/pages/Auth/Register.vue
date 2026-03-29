<script setup lang="ts">
import { Form, Head, usePage } from "@inertiajs/vue3";
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
const page = usePage();
const { password, score, onPasswordChange } = usePasswordEntropy();
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);
</script>

<template>
    <Head
        ><title>{{ $t("pages.register.title") }}</title></Head
    >
    <headline>
        <icon name="account" :size="3" />
        {{ $t("pages.register.title") }}
    </headline>
    <Form
        action="/register"
        method="post"
        class="form"
        #default="{ errors, processing, validate, validating, invalid, valid }"
    >
        <form-legend
            :items="[
                { slot: 'intro', icon: 'info' },
                { slot: 'required', icon: 'info' },
                { slot: 'password', icon: 'key' }
            ]"
        >
            <template #intro>{{ $t("pages.register.intro") }}</template>
            <template #required>
                <i18n-t keypath="form.legend.required" scope="global">
                    <template #icon><icon name="required" /></template>
                </i18n-t>
            </template>
            <template #password>{{ $t("form.legend.password") }}</template>
        </form-legend>
        <form-group
            for-id="name"
            :label="$t('form.fields.username')"
            :error="errors.name"
            :invalid="invalid('name')"
            :validated="valid('name')"
            :validating="validating"
            addon-icon="register"
            :required="true"
        >
            <input type="text" name="name" id="name" maxlength="80" @change="validate('name')" class="form-input" />
        </form-group>
        <form-group
            for-id="email"
            :label="$t('form.fields.email')"
            :error="errors.email"
            :invalid="invalid('email')"
            :validated="valid('email')"
            :validating="validating"
            addon-icon="mail"
            :required="true"
        >
            <input
                type="email"
                name="email"
                id="email"
                maxlength="255"
                @change="validate('email')"
                class="form-input"
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
                    :aria-label="showPassword ? $t('components.password.hide') : $t('components.password.show')"
                    tabindex="-1"
                >
                    <icon :name="showPassword ? 'visibility-off' : 'visibility-on'" />
                    <span>{{ showPassword ? $t("components.password.hide") : $t("components.password.show") }}</span>
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
                    :aria-label="
                        showPasswordConfirmation ? $t('components.password.hide') : $t('components.password.show')
                    "
                    tabindex="-1"
                >
                    <icon :name="showPasswordConfirmation ? 'visibility-off' : 'visibility-on'" />
                    <span>{{
                        showPasswordConfirmation ? $t("components.password.hide") : $t("components.password.show")
                    }}</span>
                </button>
            </template>
        </form-group>
        <input type="hidden" name="locale" :value="page.props.locale" />
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="save" />
                {{ $t("pages.register.submit") }}
                <loading-spinner v-if="processing" :size="2" />
            </button>
        </form-group>
    </Form>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/timings" as ti;

button.form-group__addon {
    cursor: pointer;

    transition:
        background-color map.get(ti.$timings, "fast") linear,
        color map.get(ti.$timings, "fast") linear;

    &:hover,
    &:active {
        background-color: map.get(c.$components, "input", "background-focus");
        color: map.get(c.$components, "input", "surface-focus");
    }
}
</style>
