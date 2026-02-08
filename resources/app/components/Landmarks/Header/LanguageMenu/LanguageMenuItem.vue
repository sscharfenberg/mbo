<script setup lang="ts">
defineProps({
    locale: {
        type: String,
        required: true
    }
});
</script>

<template>
    <li>
        <a
            class="popover-list-item"
            :href="`/lang/${locale}`"
            :class="{ 'popover-list-item--selected': $page.props.locale === locale }"
        >
            <img v-if="locale === 'de'" class="flag" src="./de.svg" alt="Deutsche Sprache" />
            <img v-if="locale === 'en'" class="flag" src="./en.svg" alt="English Language" />
            {{ locale.toUpperCase() }}
        </a>
    </li>
</template>

<style scoped lang="scss">
.flag {
    width: 24px;
    height: 14.4px;
}
</style>
