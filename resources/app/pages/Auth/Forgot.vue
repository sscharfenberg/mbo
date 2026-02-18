<script setup lang="ts">
import { Form, Head } from "@inertiajs/vue3";
import FormGroup from "Components/Form/FormGroup.vue";
import RadioButtonGroup from "Components/Form/Radio/RadioButtonGroup.vue";
import NarrowLayout from "Components/Layout/NarrowLayout.vue";
import Headline from "Components/Visual/Headline.vue";
defineOptions({ layout: NarrowLayout });
const types = [
    { value: "password", label: "Passwort", checked: true },
    { value: "name", label: "Username", checked: false }
];
</script>

<template>
    <Head
        ><title>{{ $t("pages.forgot.title") }}</title></Head
    >
    <headline>{{ $t("pages.forgot.title") }}</headline>
    <Form action="/login" method="post" class="form" #default="{ errors, valid, invalid, validating, validate }">
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
            <input type="email" name="email" id="email" @change="validate('email')" class="form-input" />
        </form-group>
        <form-group for-id="type_password" label="Was" :required="true">
            <radio-button-group name="type" :radio-buttons="types" />
        </form-group>
    </Form>
</template>
