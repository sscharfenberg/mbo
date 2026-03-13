<script setup lang="ts">
import { Form, Head } from "@inertiajs/vue3";
import type { Container } from "Types/container";
import { computed, nextTick, ref } from "vue";
import { useI18n } from "vue-i18n";
import CardImageSearch from "Components/Form/CardImageSearch/CardImageSearch.vue";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import MonoSelect from "Components/Form/Select/MonoSelect.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
const props = defineProps<{
    containerTypes: string[];
    nameMax: number;
    descriptionMax: number;
    customTypeMax: number;
    /** Present in edit mode; absent when creating a new container. */
    container?: Container;
}>();
const { t } = useI18n();
/**
 * Local refs for text inputs — initialized from the container prop in edit mode,
 * empty strings when creating. Using local refs (rather than `:value="prop"`)
 * prevents Inertia's precognitive re-renders from resetting in-progress edits.
 */
const nameValue = ref(props.container?.name ?? "");
const descriptionValue = ref(props.container?.description ?? "");
const customTypeValue = ref(props.container?.custom_type ?? "");
/** True when a container prop is present, i.e. the form is in edit mode. */
const isEdit = computed(() => !!props.container);
/** Form action URL — points to the edit endpoint in edit mode, create endpoint otherwise. */
const formAction = computed(() =>
    isEdit.value ? `/collection/containers/${props.container!.id}` : "/collection/containers/new"
);
/** HTTP method — PATCH for edit, POST for create. */
const formMethod = computed(() => (isEdit.value ? "patch" : "post"));
/** BinderType options formatted for MonoSelect: `{ value, label }` pairs with translated labels. */
const types = computed(() => props.containerTypes.map(value => ({ value, label: t(`enums.binder_type.${value}`) })));
/** Currently selected binder type. Initialized from the container prop in edit mode. */
const selectedType = ref(props.container?.type ?? "");
/**
 * Called when the type select changes. Updates selectedType and re-triggers
 * precognitive validation for the container_type field.
 *
 * @param value - The newly selected type value.
 * @param validate - The precognitive validate callback from the Form slot.
 */
const onTypeChange = (value: string, validate: (field: string) => void) => {
    selectedType.value = value;
    nextTick(() => validate("container_type"));
};
</script>

<template>
    <Head
        ><title>{{ isEdit ? $t("pages.edit_container.title") : $t("pages.new_container.title") }}</title></Head
    >
    <headline>
        <icon :name="isEdit ? 'container-type' : 'add'" :size="3" />
        {{ isEdit ? $t("pages.edit_container.title") : $t("pages.new_container.title") }}
    </headline>
    <Form
        :method="formMethod"
        :action="formAction"
        class="form"
        #default="{ errors, processing, validating, valid, validate }"
    >
        <form-legend :required="true" />
        <form-group
            for-id="container_name"
            :label="$t('form.fields.container.name')"
            :error="errors.container_name ?? ''"
            :invalid="!!errors?.container_name"
            :validated="valid('container_name')"
            :validating="validating"
            :required="true"
            addon-icon="container-name"
        >
            <input
                type="text"
                name="container_name"
                id="container_name"
                class="form-input"
                :maxlength="props.nameMax"
                v-model="nameValue"
                @change="validate('container_name')"
            />
        </form-group>
        <form-group
            :label="$t('form.fields.container.type')"
            :error="errors.container_type ?? ''"
            :invalid="!!errors?.container_type"
            :validated="valid('container_type')"
            :validating="validating"
            :required="true"
        >
            <mono-select
                :options="types"
                :selected="selectedType"
                @change="onTypeChange($event, validate)"
                addon-icon="container-type"
            />
            <input type="hidden" name="container_type" :value="selectedType" />
        </form-group>
        <form-group
            v-if="selectedType === 'other'"
            for-id="container_type_other"
            :label="$t('form.fields.container.other_type')"
            :error="errors.container_type_other ?? ''"
            :invalid="!!errors?.container_type_other"
            :validated="valid('container_type_other')"
            :validating="validating"
            :required="true"
            addon-icon="storage"
        >
            <input
                type="text"
                name="container_type_other"
                id="container_type_other"
                class="form-input"
                :maxlength="props.customTypeMax"
                v-model="customTypeValue"
                @change="validate('container_type_other')"
            />
        </form-group>
        <form-group
            for-id="container_description"
            :label="$t('form.fields.container.description')"
            :error="errors.container_description ?? ''"
            :invalid="!!errors?.container_description"
            :validated="valid('container_description')"
            :validating="validating"
            :required="false"
        >
            <div class="form-input__textarea-addon"><icon name="text" /></div>
            <div class="form-input form-input__textarea">
                <textarea
                    name="container_description"
                    id="container_description"
                    :maxlength="props.descriptionMax"
                    v-model="descriptionValue"
                    @change="validate('container_description')"
                />
            </div>
        </form-group>
        <card-image-search ref-id="container_image" :initial-card="container?.defaultCard ?? null" />
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="save" />
                {{ isEdit ? $t("pages.edit_container.submit") : $t("pages.new_container.submit") }}
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
