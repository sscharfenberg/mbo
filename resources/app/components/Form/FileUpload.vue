<script setup lang="ts">
import { usePage } from "@inertiajs/vue3";
import { computed, ref, useTemplateRef } from "vue";
import Icon from "Components/UI/Icon.vue";
const props = withDefaults(
    defineProps<{
        /** URL to POST the file to. */
        action: string;
        /** Accepted file types for the input (e.g. ".csv"). */
        accept?: string;
        /** Validation error message from the parent. */
        error?: string;
        /** Allowed file type labels shown in the requirements list (e.g. [".csv"]). */
        allowedTypes?: string[];
        /** Maximum file size in bytes, shown in the requirements list. */
        maxBytes?: number;
    }>(),
    { accept: ".csv", error: "", allowedTypes: () => [".csv"], maxBytes: 0 }
);
const emit = defineEmits<{
    /** Emitted with the server-returned filename on successful upload. */
    success: [filename: string];
    /** Emitted when the file is cleared. */
    clear: [];
    /** Emitted with the error message on upload failure. */
    error: [message: string];
}>();
const page = usePage();
/** CSRF token from the shared Inertia props, sent as X-CSRF-TOKEN header on the XHR. */
const csrfToken = computed(() => page.props.csrfToken as string);
const fileInput = useTemplateRef<HTMLInputElement>("fileInput");
/** Original name of the selected file, shown in the info row. */
const fileName = ref("");
/** Size of the selected file in bytes. */
const fileSize = ref(0);
/** True while the XHR upload is in flight. */
const uploading = ref(false);
/** Upload progress percentage (0–100), updated via xhr.upload.onprogress. */
const progress = ref(0);
/** True after the server responds with 2xx. */
const uploaded = ref(false);
/** Human-readable maximum file size for the requirements list. */
const formattedMaxSize = computed(() => {
    if (props.maxBytes === 0) return "";
    if (props.maxBytes < 1024) return `${props.maxBytes} B`;
    if (props.maxBytes < 1024 * 1024) return `${(props.maxBytes / 1024).toFixed(0)} KB`;
    return `${(props.maxBytes / (1024 * 1024)).toFixed(0)} MB`;
});
/** Human-readable file size (bytes or KB). */
const formattedSize = computed(() => {
    if (fileSize.value === 0) return "";
    if (fileSize.value < 1024) return `${fileSize.value} B`;
    return `${(fileSize.value / 1024).toFixed(1)} KB`;
});
/**
 * Handles the native file input change event.
 * Captures the selected file's metadata and immediately triggers the upload.
 */
const onFileChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;
    fileName.value = file.name;
    fileSize.value = file.size;
    uploaded.value = false;
    upload(file);
};
/**
 * Uploads the file via XMLHttpRequest to the configured action URL.
 * Uses XHR instead of fetch() because only XHR supports upload progress tracking.
 * On success, emits the server-returned filename. On failure, emits the error
 * message extracted from the JSON response and resets the component.
 */
const upload = (file: File) => {
    const formData = new FormData();
    formData.append("file", file);
    const xhr = new XMLHttpRequest();
    xhr.upload.addEventListener("progress", e => {
        if (e.lengthComputable) {
            progress.value = Math.round((e.loaded / e.total) * 100);
        }
    });
    xhr.addEventListener("load", () => {
        uploading.value = false;
        if (xhr.status >= 200 && xhr.status < 300) {
            uploaded.value = true;
            const response = JSON.parse(xhr.responseText);
            emit("success", response.filename);
        } else {
            try {
                const response = JSON.parse(xhr.responseText);
                const message = response.errors?.file?.[0] ?? response.message ?? xhr.statusText;
                emit("error", message);
            } catch {
                emit("error", xhr.statusText);
            }
            resetState();
        }
    });
    xhr.addEventListener("error", () => {
        uploading.value = false;
        emit("error", xhr.statusText);
        resetState();
    });
    uploading.value = true;
    progress.value = 0;
    xhr.open("POST", props.action);
    xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken.value);
    xhr.setRequestHeader("Accept", "application/json");
    xhr.send(formData);
};
/** Resets internal state and the native file input without emitting any event. */
const resetState = () => {
    fileName.value = "";
    fileSize.value = 0;
    progress.value = 0;
    uploaded.value = false;
    if (fileInput.value) fileInput.value.value = "";
};
/**
 * Resets all component state and notifies the parent via the "clear" event.
 * Called when the user explicitly removes the file.
 */
const clear = () => {
    resetState();
    emit("clear");
};
</script>

<template>
    <div class="file-upload">
        <label v-if="!fileName" class="file-upload__trigger" :class="{ 'file-upload__trigger--disabled': uploading }">
            <input
                ref="fileInput"
                type="file"
                :accept="accept"
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
            <li v-if="maxBytes" :aria-label="$t('components.file_upload.max_size', { size: formattedMaxSize })">
                {{ formattedMaxSize }}
            </li>
        </ul>
        <div v-if="fileName" class="file-upload__info">
            <div class="form-group__addon"><icon name="file" /></div>
            <input class="form-input" readonly :value="`${fileName} (${formattedSize})`" />
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
@use "sass:map";
@use "Abstracts/sizes" as s;
@use "Abstracts/colors" as c;

.file-upload {
    display: flex;
    flex-grow: 1;
    flex-wrap: wrap;

    &__trigger {
        display: inline-flex;

        width: fit-content;
        flex: 0 0 100%;

        &--disabled {
            opacity: 0.5;

            pointer-events: none;
        }
    }

    &__requirements {
        display: flex;

        padding: map.get(s.$components, "file-upload", "requirements", "padding");
        border: map.get(s.$components, "file-upload", "requirements", "border") solid
            map.get(c.$components, "file-upload", "requirements", "border");
        margin: map.get(s.$components, "file-upload", "requirements", "margin");
        gap: map.get(s.$components, "file-upload", "requirements", "gap");

        background-color: map.get(c.$components, "file-upload", "requirements", "background");
        color: map.get(c.$components, "file-upload", "requirements", "surface");

        list-style: none;
        border-radius: map.get(s.$components, "file-upload", "requirements", "radius");

        > li {
            display: flex;
            position: relative;
            align-items: center;

            gap: 0.5ch;

            &:not(:first-child)::before {
                display: block;
                position: absolute;

                width: map.get(s.$components, "file-upload", "requirements", "border");

                height: 1lh;
                transform: translateX(-0.75rem);

                background-color: map.get(c.$components, "file-upload", "requirements", "surface");

                content: "";
            }
        }
    }

    &__info {
        display: flex;

        flex: 0 0 100%;
    }

    .btn-clear {
        margin: map.get(s.$components, "file-upload", "requirements", "margin");
    }

    &__progress {
        display: flex;
        align-items: center;

        gap: 0.5rem;

        .progressbar {
            position: relative;
            inset: auto;

            flex: 1;
        }
    }
}
</style>
