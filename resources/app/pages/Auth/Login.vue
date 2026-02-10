<script setup lang="ts">
import { Form, Head } from "@inertiajs/vue3";
import { ref } from "vue";
import Checkbox from "Components/Form/Checkbox.vue";
import FormGroup from "Components/Form/FormGroup.vue";
import NarrowLayout from "Components/Layout/NarrowLayout.vue";
import Headline from "Components/Visual/Headline.vue";
import Icon from "Components/Visual/Icon.vue";
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
    <Head><title>Login</title></Head>
    <headline>Login</headline>
    Status: {{ status }}<br />
    canReset {{ canResetPassword }}<br />
    canregister {{ canRegister }}<br />
    <Form action="/login" method="post" class="form" #default="{ errors, processing }">
        <form-group
            for-id="name"
            label="Benutzername"
            :error="errors.name"
            addon-icon="register"
            :required="true"
            :invalid="!!errors?.name"
        >
            <input type="text" name="name" id="name" class="form-input" />
        </form-group>
        <form-group for-id="password" label="Passwort" :error="errors.password" :required="true">
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
        <form-group for-id="remember_password" label="Remember me">
            <template #addon>
                <checkbox label="Remember me" ref-id="remember" :value="true" />
            </template>
        </form-group>
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="key" />
                Login
                <loading-spinner v-if="processing" :size="2" />
            </button>
        </form-group>
    </Form>
</template>
