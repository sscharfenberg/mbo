<script setup lang="ts">
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import Icon from "Components/UI/Icon.vue";
import CurrencySwitchItem from "./CurrencySwitchItem.vue";
const page = usePage();
const currency = computed(() => page.props.currency as string);
const supportedCurrencies = computed(() => page.props.supportedCurrencies as string[]);
const emit = defineEmits(["close"]);
</script>

<template>
    <div class="currency-switch">
        <icon name="money" :size="1" />
        <nav class="currency-switch__nav" :aria-label="$t('header.currency.label')">
            <currency-switch-item
                v-for="cur in supportedCurrencies"
                :key="cur"
                :currency="cur"
                :label="$t('header.currency.' + cur)"
                :selected="currency === cur"
                @close="emit('close')"
            />
        </nav>
    </div>
</template>

<style lang="scss" scoped>
.currency-switch {
    display: flex;
    align-items: center;

    gap: 1ch;

    &__nav {
        display: flex;
        align-items: center;
        flex-grow: 1;

        gap: 1ch;
    }

    > .icon {
        margin: 0.4rem calc(0.6rem - 1ch) 0.4rem 0.6rem;
    }
}
</style>
