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
                Fields marked with a <icon name="required" class="form-group__icon form-group__icon--required" /> are
                required.
            </span>
        </li>
    </ul>
</template>
