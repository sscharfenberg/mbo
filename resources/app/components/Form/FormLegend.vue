<script setup lang="ts">
import Icon from "Components/Visual/Icon.vue";
defineProps({
    required: Boolean
});
</script>

<template>
    <ul class="form-legend">
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
    </ul>
</template>
