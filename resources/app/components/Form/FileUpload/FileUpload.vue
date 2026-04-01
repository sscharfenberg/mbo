<script setup lang="ts">
import Icon from "Components/UI/Icon.vue";
import { useFormatting } from "Composables/useFormatting.ts";
import { useFileUpload } from "./useFileUpload.ts";
const props = withDefaults(
    defineProps<{
        /** URL to POST the file to. */
        action: string;
        /** Validation error message from the parent. */
        error?: string;
        /** Allowed file types, used for both the input accept attribute and the requirements list (e.g. [".csv"]). */
        allowedTypes?: string[];
        /** Maximum file size in bytes, shown in the requirements list. */
        maxBytes?: number;
    }>(),
    { error: "", allowedTypes: () => [".csv"], maxBytes: 0 }
);
const emit = defineEmits<{
    /** Emitted with the server-returned filename on successful upload. */
    success: [filename: string];
    /** Emitted when the file is cleared. */
    clear: [];
    /** Emitted with the error message on upload failure. */
    error: [message: string];
    /** Emitted when the upload starts or finishes. */
    uploading: [active: boolean];
}>();
const { formatBytes } = useFormatting();
const { fileInput, fileName, fileSize, uploading, progress, uploaded, onFileChange, clear } = useFileUpload(
    () => props.action,
    {
        onSuccess: (filename: string) => emit("success", filename),
        onError: (message: string) => emit("error", message),
        onClear: () => emit("clear"),
        onUploadingChange: (active: boolean) => emit("uploading", active)
    }
);
</script>

<template>
    <div class="file-upload">
        <label v-if="!fileName" class="file-upload__trigger" :class="{ 'file-upload__trigger--disabled': uploading }">
            <input
                ref="fileInput"
                type="file"
                :accept="allowedTypes.join(',')"
                class="sr-only"
                @change="onFileChange"
                :disabled="uploading"
            />
            <span class="btn-default">
                <icon name="upload" />
                {{ $t("components.file_upload.choose") }}
            </span>
        </label>
        <ul
            v-if="!uploading && !uploaded"
            class="file-upload__requirements"
            :aria-label="$t('components.file_upload.requirements')"
        >
            <li
                v-for="type in allowedTypes"
                :key="type"
                :aria-label="$t('components.file_upload.allowed_type', { type })"
            >
                <icon name="file" />{{ type }}
            </li>
            <li v-if="maxBytes" :aria-label="$t('components.file_upload.max_size', { size: formatBytes(maxBytes) })">
                {{ formatBytes(maxBytes) }}
            </li>
        </ul>
        <div v-if="fileName" class="file-upload__info">
            <div class="form-group__addon"><icon name="file" /></div>
            <input class="form-input" readonly :value="`${fileName} (${formatBytes(fileSize)})`" />
        </div>
        <button
            v-if="fileName && !uploading"
            type="button"
            class="btn-default btn-clear"
            @click="clear"
            :aria-label="$t('components.file_upload.clear')"
        >
            <icon name="clear" />
            {{ $t("components.file_upload.clear") }}
        </button>
        <div v-if="uploading" class="file-upload__progress">
            <div class="progressbar">
                <div class="progressbar__bar" :style="{ '--bar-percentage': progress + '%' }" />
            </div>
            <span class="file-upload__percentage">{{ progress }}%</span>
        </div>
    </div>
</template>

<style lang="scss" scoped>
/*
 * styles can be found in
 * resources/app/styles/components/form/_file-upload.scss
 */
</style>
