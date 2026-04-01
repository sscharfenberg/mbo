import { usePage } from "@inertiajs/vue3";
import { computed, ref, type Ref, useTemplateRef } from "vue";

export type UseFileUploadReturn = {
    /** Template ref for the native file input element. */
    fileInput: Ref<HTMLInputElement | null>;
    /** Original name of the selected file. */
    fileName: Ref<string>;
    /** Size of the selected file in bytes. */
    fileSize: Ref<number>;
    /** True while the XHR upload is in flight. */
    uploading: Ref<boolean>;
    /** Upload progress percentage (0–100). */
    progress: Ref<number>;
    /** True after the server responds with 2xx. */
    uploaded: Ref<boolean>;
    /** Handles the native file input change event and triggers the upload. */
    onFileChange: (event: Event) => void;
    /** Resets all state and notifies via the onClear callback. */
    clear: () => void;
};

export type FileUploadCallbacks = {
    onSuccess: (filename: string) => void;
    onError: (message: string) => void;
    onClear: () => void;
    onUploadingChange: (active: boolean) => void;
};

/**
 * Composable that encapsulates file upload via XMLHttpRequest with progress tracking.
 *
 * Uses XHR instead of fetch() because only XHR supports upload progress tracking
 * via `xhr.upload.onprogress`. Handles CSRF token injection, JSON error parsing,
 * and state management.
 *
 * @param action   - The URL to POST the file to.
 * @param callbacks - Event callbacks forwarded to the parent component.
 */
export const useFileUpload = (action: () => string, callbacks: FileUploadCallbacks): UseFileUploadReturn => {
    const page = usePage();
    const csrfToken = computed(() => page.props.csrfToken as string);

    const fileInput = useTemplateRef<HTMLInputElement>("fileInput");
    const fileName = ref("");
    const fileSize = ref(0);
    const uploading = ref(false);
    const progress = ref(0);
    const uploaded = ref(false);

    /** Resets internal state and the native file input without triggering callbacks. */
    const resetState = () => {
        fileName.value = "";
        fileSize.value = 0;
        progress.value = 0;
        uploaded.value = false;
        if (fileInput.value) fileInput.value.value = "";
    };

    /**
     * Uploads the file via XMLHttpRequest to the configured action URL.
     * On success, calls onSuccess with the server-returned filename.
     * On failure, calls onError with the extracted error message and resets state.
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
            callbacks.onUploadingChange(false);
            if (xhr.status >= 200 && xhr.status < 300) {
                uploaded.value = true;
                const response = JSON.parse(xhr.responseText);
                callbacks.onSuccess(response.filename);
            } else {
                try {
                    const response = JSON.parse(xhr.responseText);
                    const message = response.errors?.file?.[0] ?? response.message ?? xhr.statusText;
                    callbacks.onError(message);
                } catch {
                    callbacks.onError(xhr.statusText);
                }
                resetState();
            }
        });

        xhr.addEventListener("error", () => {
            uploading.value = false;
            callbacks.onUploadingChange(false);
            callbacks.onError(xhr.statusText);
            resetState();
        });

        uploading.value = true;
        callbacks.onUploadingChange(true);
        progress.value = 0;
        xhr.open("POST", action());
        xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken.value);
        xhr.setRequestHeader("Accept", "application/json");
        xhr.send(formData);
    };

    const onFileChange = (event: Event) => {
        const input = event.target as HTMLInputElement;
        const file = input.files?.[0];
        if (!file) return;
        fileName.value = file.name;
        fileSize.value = file.size;
        uploaded.value = false;
        upload(file);
    };

    const clear = () => {
        resetState();
        callbacks.onClear();
    };

    return { fileInput, fileName, fileSize, uploading, progress, uploaded, onFileChange, clear };
};