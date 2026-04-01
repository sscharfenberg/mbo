<script setup lang="ts">
import { Form, Head, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import FileUpload from "Components/Form/FileUpload/FileUpload.vue";
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
    sources: string[];
    results?: {
        imported: number;
        merged: number;
        skipped: number;
        skipped_rows: Array<{ row: number; name: string; reason: string }>;
    } | null;
}>();
const { t } = useI18n();
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
const sourceOptions = computed(() =>
    props.sources.map(s => ({
        value: s,
        label: t(`pages.import.sources.${s}`)
    }))
);
const selectedContainer = ref(props.container?.id ?? "");
const selectedSource = ref("mbo");
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
const isUploading = ref(false);
const canSubmit = computed(() => uploadedFilename.value && !isUploading.value);
</script>

<template>
    <Head
        ><title>{{ $t("pages.import.title") }}</title></Head
    >
    <headline>
        <icon name="upload" :size="3" />
        {{ $t("pages.import.title") }}
    </headline>
    <template v-if="!results">
        <Form class="form" action="/collection/import" method="post" #default="{ errors }">
            <form-group :label="$t('pages.import.source')" for-id="source">
                <mono-select
                    :options="sourceOptions"
                    :selected="selectedSource"
                    :clearable="false"
                    @change="selectedSource = $event"
                />
                <input type="hidden" name="source" v-model="selectedSource" />
            </form-group>
            <form-group :label="$t('pages.import.target')" for-id="container">
                <mono-select
                    :options="containerOptions"
                    :selected="selectedContainer"
                    addon-icon="storage"
                    @change="selectedContainer = $event"
                />
                <input type="hidden" name="container" v-model="selectedContainer" />
            </form-group>
            <form-group
                :label="$t('pages.import.file')"
                :error="uploadError || errors.filename"
                :invalid="!!uploadError || !!errors.filename"
            >
                <file-upload
                    action="/collection/import/upload"
                    :allowed-types="allowedTypes"
                    :max-bytes="maxUploadBytes"
                    @success="onUploadSuccess"
                    @error="onUploadError"
                    @clear="onUploadClear"
                    @uploading="isUploading = $event"
                />
            </form-group>
            <input type="hidden" name="filename" v-model="uploadedFilename" />
            <form-group>
                <button class="btn-primary" :disabled="!canSubmit">
                    <icon name="save" />
                    {{ $t("pages.import.submit") }}
                </button>
            </form-group>
        </Form>
    </template>
    <template v-else>
        <div class="import-results">
            <h2>{{ $t("pages.import.results.title") }}</h2>
            <ul class="import-results__summary">
                <li>{{ $t("pages.import.results.imported", { count: results.imported }) }}</li>
                <li>{{ $t("pages.import.results.merged", { count: results.merged }) }}</li>
                <li v-if="results.skipped > 0">
                    {{ $t("pages.import.results.skipped", { count: results.skipped }) }}
                </li>
            </ul>
            <table v-if="results.skipped_rows.length > 0" class="import-results__table">
                <thead>
                    <tr>
                        <th>{{ $t("pages.import.results.skipped_table.row") }}</th>
                        <th>{{ $t("pages.import.results.skipped_table.name") }}</th>
                        <th>{{ $t("pages.import.results.skipped_table.reason") }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in results.skipped_rows" :key="row.row">
                        <td>{{ row.row }}</td>
                        <td>{{ row.name }}</td>
                        <td>{{ $t(`pages.import.results.reasons.${row.reason}`) }}</td>
                    </tr>
                </tbody>
            </table>
            <button class="btn-default" @click="router.visit('/collection/import')">
                <icon name="upload" />
                {{ $t("pages.import.results.import_another") }}
            </button>
        </div>
    </template>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/sizes" as s;
@use "Abstracts/colors" as c;

.import-results {
    &__summary {
        padding: 0;
        margin: map.get(s.$components, "form-group", "margin");

        list-style: none;

        > li {
            padding: 0.25rem 0;
        }
    }

    &__table {
        width: 100%;
        margin: map.get(s.$components, "form-group", "margin");

        border-collapse: collapse;

        th,
        td {
            padding: 0.5rem;
            border-bottom: 1px solid map.get(c.$components, "datatable", "border");

            text-align: left;
        }

        th {
            font-weight: 600;
        }
    }
}
</style>