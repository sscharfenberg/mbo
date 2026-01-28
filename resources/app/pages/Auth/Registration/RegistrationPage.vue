<script setup lang="ts">
import { Form } from "@inertiajs/vue3";
import FormGroup from "Components/Form/FormGroup.vue";
import { reactive } from "vue";
const form = reactive({
    name: "",
    email: "",
    password: ""
});
</script>

<template>
    <Form action="/auth/register" method="post">
        <form-group for-id="name" label="Benutzername"
            ><input type="text" name="name" id="name" v-model="form.name"
        /></form-group>
        <form-group for-id="email" label="E-Mail-Adresse"
            ><input type="email" name="email" id="email" v-model="form.email"
        /></form-group>
        <form-group for-id="password" label="Passwort"
            ><input type="password" name="password" id="password" v-model="form.password"
        /></form-group>
        <form-group>
            <button type="submit" class="btn-primary">Submit!</button>
        </form-group>
    </Form>
</template>
