<script setup lang="ts"></script>

<template>
    <div class="button-group">
        <slot />
    </div>
</template>

<style scoped lang="scss">
.button-group {
    display: flex;
    flex-wrap: wrap;

    gap: 1rem;
}
</style>
