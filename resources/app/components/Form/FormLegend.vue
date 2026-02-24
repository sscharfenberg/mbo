<script setup lang="ts">
import Icon from "Components/UI/Icon.vue";
withDefaults(
    defineProps<{
        required?: boolean;
        password?: boolean;
    }>(),
    { required: false, password: false }
);
</script>

<template>
    <ul class="form-legend">
        <li v-if="$slots.default">
            <icon name="info" />
            <span><slot /></span>
        </li>
        <li v-if="required">
            <icon name="info" />
            <span>
                <i18n-t keypath="form.legend.required" scope="global">
                    <template #icon>
                        <icon name="required" class="form-group__icon form-group__icon--required" />
                    </template>
                </i18n-t>
            </span>
        </li>
        <li v-if="password">
            <icon name="key" />
            <span>{{ $t("form.legend.password") }} </span>
        </li>
    </ul>
</template>
