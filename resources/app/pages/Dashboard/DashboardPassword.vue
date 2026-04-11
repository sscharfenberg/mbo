<script setup lang="ts">
import { Form } from "@inertiajs/vue3";
import { ref } from "vue";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import PasswordStrength from "Components/Form/PasswordStrength.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import { usePasswordEntropy } from "Composables/usePasswordEntropy.ts";
const { password, score, onPasswordChange, reset } = usePasswordEntropy();
const showCurrentPassword = ref(false);
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);
</script>

<template>
    <headline :size="3" anchor-id="passwordSection"
        ><icon name="key" />{{ $t("pages.dashboard.password.headline") }}</headline
    >
    <Form
        action="/user/password"
        method="put"
        class="form"
        resetOnSuccess
        @success="reset"
        #default="{ errors, valid, invalid, validating, processing }"
    >
        <form-legend
            :items="[
                { slot: 'intro', icon: 'required' },
                { slot: 'required', icon: 'info' }
            ]"
        >
            <template #intro>{{ $t("pages.dashboard.password.intro") }}</template>
            <template #required>
                <i18n-t keypath="form.legend.required" scope="global">
                    <template #icon><icon name="required" /></template>
                </i18n-t>
            </template>
        </form-legend>
        <form-group
            for-id="current_password"
            :label="$t('form.fields.current_password')"
            :error="errors.current_password"
            :invalid="invalid('current_password')"
            :validated="valid('current_password')"
            :validating="validating"
            :required="true"
            addon-icon="key"
        >
            <input
                :type="showCurrentPassword ? 'text' : 'password'"
                name="current_password"
                id="current_password"
                @keyup="onPasswordChange"
                class="form-input"
            />
            <template #button>
                <button
                    type="button"
                    @mousedown.prevent
                    @click="showCurrentPassword = !showCurrentPassword"
                    :aria-label="showCurrentPassword ? $t('components.password.hide') : $t('components.password.show')"
                    tabindex="-1"
                >
                    <icon :name="showCurrentPassword ? 'visibility-off' : 'visibility-on'" />
                    <span>{{
                        showCurrentPassword ? $t("components.password.hide") : $t("components.password.show")
                    }}</span>
                </button>
            </template>
        </form-group>
        <form-group
            for-id="password"
            :label="$t('form.fields.new_password')"
            :error="errors.password"
            :invalid="invalid('password')"
            :validated="valid('password')"
            :validating="validating"
            :required="true"
            addon-icon="key"
        >
            <input
                :type="showPassword ? 'text' : 'password'"
                name="password"
                id="password"
                @keyup="onPasswordChange"
                class="form-input"
                v-model="password"
            />
            <template #button>
                <button
                    type="button"
                    @mousedown.prevent
                    @click="showPassword = !showPassword"
                    :aria-label="showPassword ? $t('components.password.hide') : $t('components.password.show')"
                    tabindex="-1"
                >
                    <icon :name="showPassword ? 'visibility-off' : 'visibility-on'" />
                    <span>{{ showPassword ? $t("components.password.hide") : $t("components.password.show") }}</span>
                </button>
            </template>
            <template v-if="score !== null" #text><PasswordStrength :score="score" /></template>
        </form-group>
        <form-group
            for-id="password_confirmation"
            :label="$t('form.fields.password_confirmation')"
            :error="errors.password_confirmation"
            :invalid="invalid('password_confirmation')"
            :validated="valid('password_confirmation')"
            :validating="validating"
            :required="true"
            addon-icon="key"
        >
            <input
                :type="showPasswordConfirmation ? 'text' : 'password'"
                name="password_confirmation"
                id="password_confirmation"
                class="form-input"
            />
            <template #button>
                <button
                    type="button"
                    @mousedown.prevent
                    @click="showPasswordConfirmation = !showPasswordConfirmation"
                    :aria-label="
                        showPasswordConfirmation ? $t('components.password.hide') : $t('components.password.show')
                    "
                    tabindex="-1"
                >
                    <icon :name="showPasswordConfirmation ? 'visibility-off' : 'visibility-on'" />
                    <span>{{
                        showPasswordConfirmation ? $t("components.password.hide") : $t("components.password.show")
                    }}</span>
                </button>
            </template>
        </form-group>
        <form-group>
            <button type="submit" class="btn-default" :disabled="processing">
                <icon name="save" />
                {{ $t("pages.dashboard.password.submit") }}
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
