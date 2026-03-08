<script setup lang="ts">
import { Form, Head } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import MonoSelect from "Components/Form/Select/MonoSelect.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
const props = defineProps<{ containerTypes: string[] }>();
const { t } = useI18n();
const types = computed(() => props.containerTypes.map(value => ({ value, label: t(`enums.binder_type.${value}`) })));
const selectedType = ref("");
</script>

<template>
    <Head
        ><title>{{ $t("pages.new_container.title") }}</title></Head
    >
    <headline>
        <icon name="add" :size="3" />
        {{ $t("pages.new_container.title") }}
    </headline>
    <Form method="POST" action="./" class="form" #default="{ errors, processing, validating, valid }">
        <form-legend :required="true" />
        <form-group
            for-id="name"
            :label="$t('form.fields.container.name')"
            :error="errors.name ?? ''"
            :invalid="!!errors?.name"
            :validated="valid('name')"
            :validating="validating"
            :required="true"
            addon-icon="container-name"
        >
            <input type="text" name="name" id="name" class="form-input" />
        </form-group>
        <form-group
            for-id="description"
            :label="$t('form.fields.container.description')"
            :error="errors.description ?? ''"
            :invalid="!!errors?.description"
            :validated="valid('description')"
            :validating="validating"
            :required="false"
        >
            <div class="form-input form-input__textarea">
                <textarea name="description" id="description" />
            </div>
        </form-group>
        <form-group
            :label="$t('form.fields.container.type')"
            :error="errors.type ?? ''"
            :invalid="!!errors?.type"
            :validated="valid('type')"
            :validating="validating"
            :required="false"
        >
            <mono-select :options="types" :selected="selectedType" @change="selectedType = $event" />
            <input type="hidden" name="type" :value="selectedType" />
        </form-group>
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="save" />
                {{ $t("pages.new_container.submit") }}
                <loading-spinner v-if="processing" :size="2" />
            </button>
        </form-group>
    </Form>
</template>

<style scoped lang="scss">
.form-input__textarea {
    --textarea-height: 4lh;
}
</style>
