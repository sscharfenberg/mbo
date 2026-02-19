<script setup lang="ts">
import { Form, Head } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import RadioButtonGroup from "Components/Form/Radio/RadioButtonGroup.vue";
import NarrowLayout from "Components/Layout/NarrowLayout.vue";
import Headline2 from "Components/Visual/Headline2.vue";
import Icon from "Components/Visual/Icon.vue";
import LoadingSpinner from "Components/Visual/LoadingSpinner.vue";
const { t } = useI18n();
defineOptions({ layout: NarrowLayout });
const types = [
    { value: "password", label: t("pages.forgot.type.password"), checked: true, icon: "key" },
    { value: "name", label: t("pages.forgot.type.username"), checked: false, icon: "account" }
];
</script>

<template>
    <Head
        ><title>{{ $t("pages.forgot.title") }}</title></Head
    >
    <headline-2>{{ $t("pages.forgot.title") }}</headline-2>
    <Form
        action="/forgot"
        method="post"
        class="form"
        #default="{ errors, valid, invalid, validating, validate, processing }"
    >
        <form-legend>{{ $t("pages.forgot.intro") }}</form-legend>
        <form-group for-id="type_password">
            <radio-button-group name="type" :radio-buttons="types" />
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
