<script setup lang="ts">
import { Form } from "@inertiajs/vue3";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import NarrowLayout from "Components/Layout/NarrowLayout.vue";
import Icon from "Components/Visual/Icon.vue";
import LoadingSpinner from "Components/Visual/LoadingSpinner.vue";
defineOptions({ layout: NarrowLayout });
</script>

<template>
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
            <input type="password" name="password" id="password" @change="validate('password')" class="form-input" />
        </form-group>
        <form-group
            for-id="password_confirmation"
            label="Passwort bestätigen"
            :error="errors.password_confirmation"
            :invalid="invalid('password_confirmation')"
            :validated="valid('password_confirmation')"
            :validating="validating"
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
