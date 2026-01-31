<script setup lang="ts">
import { Form, useForm } from "@inertiajs/vue3";
import FormGroup from "Components/Form/FormGroup.vue";
import LoadingSpinner from "Components/Loading/LoadingSpinner.vue";
import Icon from "Components/Visual/Icon/Icon.vue";
const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: ""
});
</script>

<template>
    <Form action="/register" method="post" class="app-form" #default="{ errors, processing }">
        processing: {{ processing }}<br />
        errors: {{ errors }}
        <form-group for-id="name" label="Benutzername" :error="errors.name">
            <input type="text" name="name" id="name" v-model="form.name" />
        </form-group>
        <form-group for-id="email" label="E-Mail-Adresse" :error="errors.email">
            <input type="email" name="email" id="email" v-model="form.email" />
        </form-group>
        <form-group for-id="password" label="Passwort" :error="errors.password">
            <input type="password" name="password" id="password" v-model="form.password" />
        </form-group>
        <form-group for-id="password_confirmation" label="Passwort Bestätigen" :error="errors.password_confirmation">
            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                v-model="form.password_confirmation"
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
