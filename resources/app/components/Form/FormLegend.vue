<script setup lang="ts">
import Icon from "Components/UI/Icon.vue";
defineProps<{
    items: { slot: string; icon: string; modifier?: string }[];
}>();
</script>

<template>
    <ul class="form-legend">
        <template v-for="item in items" :key="item.slot">
            <li v-if="$slots[item.slot]" :class="item.modifier ? item.modifier : undefined">
                <icon :name="item.icon" />
                <span><slot :name="item.slot" /></span>
            </li>
        </template>
    </ul>
</template>
