<script setup lang="ts">
import type { SlotProps } from "vue-input-otp";

defineProps<{
    slots: SlotProps[];
    inputmode: "numeric" | "text";
}>();
</script>

<template>
    <div v-for="(slot, index) in slots" :key="index" class="otp__char" :class="{ 'otp__char--active': slot?.isActive }">
        {{ slot?.char }}
    </div>
</template>
