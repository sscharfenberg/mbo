<script setup lang="ts">
import { ref } from "vue";
defineProps({
    refId: {
        type: String,
        default: Math.random().toString(36).substring(2)
    },
    checkedInitially: {
        type: Boolean,
        required: true
    },
    disabled: {
        type: Boolean,
        default: false
    }
});
const checkboxStatus = ref(false);
</script>

<template>
    <input :id="refId" type="checkbox" :name="refId" @change="$emit('change', checkboxStatus)" />
    <label :for="refId"></label>
</template>
