<script setup lang="ts">
import { Form, Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import Checkbox from "Components/Form/Checkbox.vue";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import NarrowLayout from "Components/Layout/NarrowLayout.vue";
import Headline from "Components/Visual/Headline.vue";
import Icon from "Components/Visual/Icon.vue";
import LinkGroup from "Components/Visual/LinkGroup.vue";
import LoadingSpinner from "Components/Visual/LoadingSpinner.vue";
defineOptions({ layout: NarrowLayout });
defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
const showPassword = ref(false);
</script>

<template>
    <Head
        ><title>{{ $t("pages.login.title") }}</title></Head
    >
    <headline>{{ $t("pages.login.title") }}</headline>
    Status: {{ status }}<br />
    canregister {{ canRegister }}<br />
    <Form action="/login" method="post" class="form" #default="{ errors, processing }">
        <form-legend :required="true" />
        <form-group
            for-id="name"
            :label="$t('form.fields.username')"
            :error="errors.name"
            addon-icon="register"
            :required="true"
            :invalid="!!errors?.name"
        >
            <input type="text" name="name" id="name" class="form-input" />
        </form-group>
        <form-group for-id="password" :label="$t('form.fields.password')" :error="errors.password" :required="true">
            <template #addon>
                <button
                    class="form-group__addon"
                    @click.prevent="showPassword = !showPassword"
                    :aria-label="showPassword ? 'Hide Password' : 'Show Password'"
                >
                    <icon :name="showPassword ? 'visibility_off' : 'visibility_on'" />
                </button>
            </template>
            <input :type="showPassword ? 'text' : 'password'" name="password" id="password" class="form-input" />
        </form-group>
        <form-group for-id="remember" :label="$t('form.fields.remember')">
            <template #addon>
                <checkbox :label="$t('form.fields.remember')" ref-id="remember" :value="true" />
            </template>
        </form-group>
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="key" />
                {{ $t("pages.login.submit") }}
                <loading-spinner v-if="processing" :size="2" />
            </button>
        </form-group>
        <form-group>
            <link-group :label="$t('pages.login.nav-label')">
                <Link v-if="canRegister" class="text-link" href="/register">{{ $t("pages.register.link") }}</Link>
                <Link v-if="canResetPassword" class="text-link" href="/forgot-password">{{
                    $t("pages.forgot-access.link")
                }}</Link>
            </link-group>
        </form-group>
    </Form>
</template>
