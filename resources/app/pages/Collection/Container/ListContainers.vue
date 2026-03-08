<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

interface Container {
    id: string;
    name: string;
    description: string | null;
    type: string;
    custom_type: string | null;
    created_at: string;
    updated_at: string;
}
defineProps<{
    containers: Container[];
    containerTypes: string[];
}>();
</script>

<template>
    <h2>List Containers</h2>
    <pre>{{ containers }}</pre>
    <Link href="/collection/containers/new">{{ $t("pages.new_container.link") }}</Link>
</template>
