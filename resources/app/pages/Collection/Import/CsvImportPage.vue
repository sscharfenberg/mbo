<script setup lang="ts">
import { Form, Head, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import FileUpload from "Components/Form/FileUpload/FileUpload.vue";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import MonoSelect from "Components/Form/Select/MonoSelect.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import Paragraph from "Components/UI/Paragraph.vue";
import type { BreadcrumbItem } from "Composables/useBreadcrumbs.ts";
import { useBreadcrumbs } from "Composables/useBreadcrumbs.ts";
import { useFormatting } from "Composables/useFormatting";
import type { Container } from "Types/container";
import type { ContainerListItem } from "Types/containerListItem";
const props = defineProps<{
    /** Pre-selected container from route model binding, or null when accessed without an ID. */
    container: Container | null;
    /** All containers belonging to the user, for the target dropdown. */
    containers: ContainerListItem[];
    /** Maximum upload size in bytes, from config('mbo.csv_upload.max_bytes'). */
    maxUploadBytes: number;
    /** Allowed file extensions for the upload input (e.g. [".csv"]). */
    allowedTypes: string[];
    /** Available import source format identifiers (e.g. ["mbo", "moxfield", "archidekt"]). */
    sources: string[];
    /** Import results returned by ImportController::store(), null on initial GET. */
    results?: {
        imported: number;
        merged: number;
        skipped: number;
        skipped_rows: Array<{ row: number; name: string; reason: string }>;
    } | null;
}>();
const { t } = useI18n();
const { formatDecimals } = useFormatting();
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
/** Container options formatted for MonoSelect. */
const containerOptions = computed(() =>
    props.containers.map(c => ({
        value: c.id,
        label: c.name
    }))
);
/** Source format options with translated labels for MonoSelect. */
const sourceOptions = computed(() =>
    props.sources.map(s => ({
        value: s,
        label: t(`pages.import.sources.${s}`)
    }))
);
const selectedContainer = ref(props.container?.id ?? "");
const selectedSource = ref("mbo");
/** Server-generated filename on the tmp disk, set after successful XHR upload. */
const uploadedFilename = ref("");
/** Error message from the XHR upload (file too large, not parseable, etc.). */
const uploadError = ref("");
/** Stores the tmp filename and clears any previous upload error. */
const onUploadSuccess = (filename: string) => {
    uploadedFilename.value = filename;
    uploadError.value = "";
};
/** Stores the error message and clears any previous filename. */
const onUploadError = (message: string) => {
    uploadError.value = message;
    uploadedFilename.value = "";
};
/** Resets both filename and error when the user clears the file input. */
const onUploadClear = () => {
    uploadedFilename.value = "";
    uploadError.value = "";
};
const isUploading = ref(false);
/** Submit is enabled only when a file has been uploaded and no upload is in progress. */
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
            <form-group>
                <form-legend
                    v-if="selectedSource === 'mbo' || selectedSource === 'archidekt'"
                    :items="[{ slot: 'explanation', icon: 'info' }]"
                >
                    <template #explanation>{{ $t(`pages.import.explanations.${selectedSource}`) }}</template>
                </form-legend>
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
        <headline :size="3">{{ $t("pages.import.results.title") }}</headline>
        <paragraph>
            {{ $t("pages.import.results.imported", { count: formatDecimals(results.imported) }) }}<br />
            {{ $t("pages.import.results.merged", { count: formatDecimals(results.merged) }) }}<br />
            <span v-if="results.skipped > 0">
                {{ $t("pages.import.results.skipped", { count: formatDecimals(results.skipped) }) }}
            </span>
        </paragraph>
        <table v-if="results.skipped_rows.length > 0" class="dt__table">
            <thead class="dt-head">
                <tr>
                    <th>{{ $t("pages.import.results.skipped_table.row") }}</th>
                    <th>{{ $t("pages.import.results.skipped_table.name") }}</th>
                    <th>{{ $t("pages.import.results.skipped_table.reason") }}</th>
                </tr>
            </thead>
            <tbody class="dt-body">
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
    </template>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/sizes" as s;
@use "Abstracts/colors" as c;

.dt__table {
    margin: 1lh 0;
}
</style>
