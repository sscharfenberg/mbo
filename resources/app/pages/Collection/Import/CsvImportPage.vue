<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import FileUpload from "Components/Form/FileUpload.vue";
import FormGroup from "Components/Form/FormGroup.vue";
import MonoSelect from "Components/Form/Select/MonoSelect.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import type { BreadcrumbItem } from "Composables/useBreadcrumbs.ts";
import { useBreadcrumbs } from "Composables/useBreadcrumbs.ts";
import type { Container } from "Types/container";
import type { ContainerListItem } from "Types/containerListItem";
const props = defineProps<{
    container: Container | null;
    containers: ContainerListItem[];
    maxUploadBytes: number;
    allowedTypes: string[];
}>();
const { setBreadcrumbs } = useBreadcrumbs();
const crumbs: BreadcrumbItem[] = [{ labelKey: "pages.collection.link", href: "/collection", icon: "collection" }];
if (props.container) {
    crumbs.push({
        label: props.container.name,
        href: `/collection/containers/${props.container.id}`,
        icon: "storage"
    });
}
crumbs.push({ labelKey: "pages.import.link" });
setBreadcrumbs(crumbs);
const containerOptions = computed(() =>
    props.containers.map(c => ({
        value: c.id,
        label: c.name
    }))
);
const selectedContainer = ref(props.container?.id ?? "");
const uploadedFilename = ref("");
const uploadError = ref("");
const onUploadSuccess = (filename: string) => {
    uploadedFilename.value = filename;
    uploadError.value = "";
};
const onUploadError = (message: string) => {
    uploadError.value = message;
    uploadedFilename.value = "";
};
const onUploadClear = () => {
    uploadedFilename.value = "";
    uploadError.value = "";
};
</script>

<template>
    <Head
        ><title>{{ $t("pages.import.title") }}</title></Head
    >
    <headline>
        <icon name="upload" :size="3" />
        {{ $t("pages.import.title") }}
    </headline>
    <div class="form">
        <form-group :label="$t('pages.import.target')" for-id="container">
            <mono-select
                :options="containerOptions"
                :selected="selectedContainer"
                addon-icon="storage"
                @change="selectedContainer = $event"
            />
            <input type="hidden" name="container" v-model="selectedContainer" />
        </form-group>
        <form-group :label="$t('pages.import.file')" :error="uploadError" :invalid="uploadError.length > 0">
            <file-upload
                action="/collection/import/upload"
                :accept="allowedTypes.join(',')"
                :allowed-types="allowedTypes"
                :max-bytes="maxUploadBytes"
                @success="onUploadSuccess"
                @error="onUploadError"
                @clear="onUploadClear"
            />
        </form-group>
    </div>
</template>
