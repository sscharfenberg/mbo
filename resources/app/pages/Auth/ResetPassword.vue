<script setup lang="ts">
import { Form, Head } from "@inertiajs/vue3";
import { debounce } from "lodash-es";
import { ref } from "vue";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import NarrowLayout from "Components/Layout/NarrowLayout.vue";
import Headline2 from "Components/Visual/Headline2.vue";
import Icon from "Components/Visual/Icon.vue";
import LoadingSpinner from "Components/Visual/LoadingSpinner.vue";
import PasswordStrength from "Components/Visual/PasswordStrength.vue";
defineOptions({ layout: NarrowLayout });
const props = defineProps<{
    token: string;
    email: string;
}>();
const inputEmail = ref(props.email);
const password = ref("");
const score = ref(null);
const showPassword = ref(false);
const onPasswordChange = debounce(
    () => {
        if (!password.value.length) return;
        fetch("/api/auth/entropy", {
            method: "POST",
            headers: { "Content-Type": "application/json", Accept: "application/json" },
            body: JSON.stringify({ p: password.value })
        })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            })
            .then(data => {
                score.value = data.score;
            })
            .catch(error => {
                console.error(error);
            })
            .finally(() => {
                console.log("entropy check finished.");
            });
    },
    750,
    { maxWait: 5000 }
);
</script>

<template>
    <Head
        ><title>{{ $t("pages.reset-password.title") }}</title></Head
    >
    <headline-2>{{ $t("pages.reset-password.title") }}</headline-2>
    <Form
        action="/reset-password"
        method="post"
        class="form"
        #default="{ errors, valid, invalid, validating, validate, processing }"
    >
        <form-legend>{{ $t("pages.reset-password.intro") }}</form-legend>
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
        >
            <template #addon>
                <button
                    class="form-group__addon"
                    @click.prevent="showPassword = !showPassword"
                    :aria-label="showPassword ? 'Hide Password' : 'Show Password'"
                    tabindex="-1"
                >
                    <icon :name="showPassword ? 'visibility_off' : 'visibility_on'" />
                </button>
            </template>
            <input
                :type="showPassword ? 'text' : 'password'"
                name="password"
                id="password"
                @change="validate('password')"
                @keyup="onPasswordChange"
                class="form-input"
                v-model="password"
            />
            <template #text><PasswordStrength v-if="score !== null" :score="score" /></template>
        </form-group>
        <form-group
            for-id="password_confirmation"
            :label="$t('form.fields.password_confirmation')"
            :error="errors.password_confirmation"
            :invalid="invalid('password_confirmation')"
            :validated="valid('password_confirmation')"
            :validating="validating"
            addon-icon="key"
            :required="true"
        >
            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                @change="validate('password_confirmation')"
                class="form-input"
            />
        </form-group>
        <input type="hidden" name="token" :value="token" />
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="save" />
                {{ $t("pages.reset-password.submit") }}
                <loading-spinner v-if="processing" :size="2" />
            </button>
        </form-group>
    </Form>
</template>
