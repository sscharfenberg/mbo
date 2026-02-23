<script setup lang="ts">
import { Form, Head } from "@inertiajs/vue3";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import NarrowLayout from "Components/Layout/NarrowLayout.vue";
import Headline from "Components/Visual/Headline.vue";
import Icon from "Components/Visual/Icon.vue";
import LoadingSpinner from "Components/Visual/LoadingSpinner.vue";
defineOptions({ layout: NarrowLayout });
</script>

<template>
    <Head
        ><title>{{ $t("pages.resend-verification.title") }}</title></Head
    >
    <headline>
        <icon name="mail" :size="3" />
        {{ $t("pages.resend-verification.title") }}
    </headline>
    <Form
        action="/resend-verification"
        method="post"
        class="form"
        #default="{ errors, valid, invalid, validating, validate, processing }"
    >
        <form-legend>{{ $t("pages.resend-verification.intro") }}</form-legend>
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
            <input type="text" name="name" id="name" @change="validate('name')" class="form-input" />
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
            <input type="email" name="email" id="email" @change="validate('email')" class="form-input" />
        </form-group>
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="save" />
                {{ $t("pages.forgot.submit") }}
                <loading-spinner v-if="processing" :size="2" />
            </button>
        </form-group>
    </Form>
</template>
