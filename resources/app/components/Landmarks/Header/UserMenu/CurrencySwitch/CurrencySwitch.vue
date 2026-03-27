<script setup lang="ts">
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import CurrencySwitchItem from "./CurrencySwitchItem.vue";
const page = usePage();
const currency = computed(() => page.props.currency as string);
const supportedCurrencies = computed(() => page.props.supportedCurrencies as string[]);
const emit = defineEmits(["close"]);
</script>

<template>
    <ul class="popover-list">
        <currency-switch-item
            v-for="cur in supportedCurrencies"
            :key="cur"
            :currency="cur"
            :label="$t('header.currency.' + cur)"
            :selected="currency === cur"
            @close="emit('close')"
        />
    </ul>
</template>
