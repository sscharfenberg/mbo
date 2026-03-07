<script setup lang="ts">
import { Form, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
const user = usePage().props.auth.user;
const name = ref(user?.name ?? "");
const email = ref(user?.email ?? "");
</script>

<template>
    <headline :size="3" anchor-id="profileSection">{{ $t("pages.dashboard.profile.headline") }}</headline>
    <Form
        action="/user/profile-information"
        method="put"
        class="form"
        #default="{ errors, valid, invalid, validating, validate, processing }"
    >
        <form-legend :required="true">{{ $t("pages.dashboard.profile.intro") }}</form-legend>
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
            <input type="text" name="name" id="name" v-model="name" @change="validate('name')" class="form-input" />
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
                v-model="email"
                @change="validate('email')"
                class="form-input"
            />
        </form-group>
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="save" />
                {{ $t("pages.dashboard.profile.submit") }}
                <loading-spinner v-if="processing" :size="2" />
            </button>
        </form-group>
    </Form>
</template>

<style scoped lang="scss">
.form {
    margin: 1em 0;
}
</style>
