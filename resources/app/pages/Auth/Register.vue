<script setup lang="ts">
import { Form } from "@inertiajs/vue3";
import FormGroup from "Components/Form/FormGroup.vue";
import LoadingSpinner from "Components/Loading/LoadingSpinner.vue";
import Icon from "Components/Visual/Icon/Icon.vue";
</script>

<template>
    <Form
        action="/register"
        method="post"
        class="app-form"
        #default="{ errors, processing, validate, validating, invalid }"
    >
        processing: {{ processing }}<br />
        errors: {{ errors }}<br />
        <form-group
            for-id="name"
            label="Benutzername"
            :error="errors.name"
            :invalid="invalid('name')"
            :validating="validating"
        >
            <input type="text" name="name" id="name" @change="validate('name')" />
        </form-group>
        <form-group
            for-id="email"
            label="E-Mail-Adresse"
            :error="errors.email"
            :invalid="invalid('email')"
            :validating="validating"
        >
            <input type="email" name="email" id="email" @change="validate('email')" />
        </form-group>
        <form-group
            for-id="password"
            label="Passwort"
            :error="errors.password"
            :invalid="invalid('password')"
            :validating="validating"
        >
            <input type="password" name="password" id="password" @change="validate('password')" />
        </form-group>
        <form-group
            for-id="password_confirmation"
            label="Passwort Bestätigen"
            :error="errors.password_confirmation"
            :invalid="invalid('password_confirmation')"
        >
            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                @change="validate('password_confirmation')"
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
