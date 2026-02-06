<script setup lang="ts">
import { Form } from "@inertiajs/vue3";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import NarrowLayout from "Components/Layout/NarrowLayout.vue";
import Headline from "Components/Visual/Headline.vue";
import Icon from "Components/Visual/Icon.vue";
import LoadingSpinner from "Components/Visual/LoadingSpinner.vue";
import PasswordStrength from "Components/Visual/PasswordStrength.vue";
import { debounce } from "lodash-es";
import { ref } from "vue";
defineOptions({ layout: NarrowLayout });
const password = ref("");
const score = ref(null);
const showPassword = ref(false);
const onPasswordChange = debounce(
    () => {
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
    <headline>Registration</headline>
    <Form
        action="/register"
        method="post"
        class="form"
        #default="{ errors, processing, validate, validating, invalid, valid }"
    >
        <form-legend :required="true" />
        <form-group
            for-id="name"
            label="Benutzername"
            :error="errors.name"
            :invalid="invalid('name')"
            :validated="valid('name')"
            :validating="validating"
            addon-icon="register"
            :required="true"
        >
            <input type="text" name="name" id="name" @change="validate('name')" class="form-input" />
        </form-group>
        <form-group
            for-id="email"
            label="E-Mail-Adresse"
            :error="errors.email"
            :invalid="invalid('email')"
            :validated="valid('email')"
            :validating="validating"
            addon-icon="mail"
            :required="true"
        >
            <input type="email" name="email" id="email" @change="validate('email')" class="form-input" />
        </form-group>
        <form-group
            for-id="password"
            label="Passwort"
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
            label="Passwort bestätigen"
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
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="save" />
                Register
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
        background-color: map.get(c.$form, "input", "background-focus");
        color: map.get(c.$form, "input", "surface-focus");
    }
}
</style>
