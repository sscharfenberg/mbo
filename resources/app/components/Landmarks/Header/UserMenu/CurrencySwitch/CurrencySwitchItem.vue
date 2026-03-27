<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

const emit = defineEmits(["close"]);

defineProps<{
    /** Currency code (e.g. `"eur"`, `"usd"`). */
    currency: string;
    /** Display label for the currency option. */
    label: string;
    /** Whether this option is currently active. */
    selected: boolean;
}>();
</script>

<template>
    <li>
        <Link
            class="popover-list-item"
            :class="{ 'popover-list-item--selected': selected }"
            :href="'/currency/' + currency"
            method="post"
            as="button"
            @click="emit('close')"
        >
            {{ label }}
        </Link>
    </li>
</template>