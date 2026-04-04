<script setup lang="ts">
import { Head, router, usePage } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import type { ContainerListItem } from "@/types/containerListItem";
import FormGroup from "Components/Form/FormGroup.vue";
import FormLegend from "Components/Form/FormLegend.vue";
import MonoSelect from "Components/Form/Select/MonoSelect.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import { useBreadcrumbs } from "Composables/useBreadcrumbs.ts";
import type { BreadcrumbItem } from "Composables/useBreadcrumbs.ts";
import type { Container } from "Types/container.ts";
const props = defineProps<{
    container: Container | null;
    containers: ContainerListItem[];
}>();
const { t } = useI18n();
const page = usePage();
const csrfToken = computed(() => page.props.csrfToken as string);
const { setBreadcrumbs } = useBreadcrumbs();
const crumbs: BreadcrumbItem[] = [
    { labelKey: "pages.collection.link", href: "/collection", icon: "collection" },
    { labelKey: "pages.containers.link", href: "/containers", icon: "storage" }
];
if (props.container) {
    crumbs.push({
        label: props.container.name,
        href: `/containers/${props.container.id}`,
        icon: "container-name"
    });
}
crumbs.push({ label: t("pages.container_qr.link") });
setBreadcrumbs(crumbs);
const containerOptions = computed(() =>
    props.containers.map(container => ({
        value: container.id,
        label: container.name
    }))
);
const selectedContainer = ref(props.container?.id ?? "");
const qrSvg = ref("");
const loading = ref(false);
/**
 * Navigate to the QR page for the selected container (or the generic page if cleared).
 * Inertia replaces props reactively, triggering the watcher below.
 */
function onContainerChange(id: string) {
    selectedContainer.value = id;
    if (!id) {
        qrSvg.value = "";
        return;
    }
    router.get(`/containers/${id}/qr`, {}, { preserveState: true });
}
/**
 * Fetch the QR code SVG from the server for the given container ID.
 */
async function fetchQr(containerId: string) {
    loading.value = true;
    qrSvg.value = "";
    try {
        const response = await fetch(`/containers/${containerId}/qr`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": csrfToken.value
            }
        });
        if (response.ok) {
            const data = await response.json();
            qrSvg.value = data.svg;
        }
    } finally {
        loading.value = false;
    }
}
/** Helper to derive the download filename from the selected container. */
function filename(ext: string): string {
    return `qr-${props.container?.name ?? "container"}.${ext}`;
}
/** Download the QR code as an SVG file with XML declaration. */
function downloadSvg() {
    const xmlDeclaration = '<?xml version="1.0" encoding="UTF-8"?>\n';
    const blob = new Blob([xmlDeclaration + qrSvg.value], { type: "image/svg+xml" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = filename("svg");
    a.click();
    URL.revokeObjectURL(url);
}
/** Rasterize the QR SVG to a 1024×1024 PNG via canvas and trigger a download. */
function downloadPng() {
    const size = 1024;
    const svgBlob = new Blob([qrSvg.value], { type: "image/svg+xml;charset=utf-8" });
    const svgUrl = URL.createObjectURL(svgBlob);
    const img = new Image();
    img.onload = () => {
        const canvas = document.createElement("canvas");
        canvas.width = size;
        canvas.height = size;
        const ctx = canvas.getContext("2d")!;
        ctx.drawImage(img, 0, 0, size, size);
        URL.revokeObjectURL(svgUrl);
        const a = document.createElement("a");
        a.href = canvas.toDataURL("image/png");
        a.download = filename("png");
        a.click();
    };
    img.src = svgUrl;
}
/** When Inertia delivers a new container prop after navigation, fetch the QR code. */
watch(
    () => props.container,
    newContainer => {
        if (newContainer) {
            selectedContainer.value = newContainer.id;
            fetchQr(newContainer.id);
        } else {
            qrSvg.value = "";
        }
    },
    { immediate: true }
);
</script>

<template>
    <Head
        ><title>{{ container?.name ?? t("pages.container_qr.any") }}</title></Head
    >
    <headline>
        <icon name="qr-code" :size="3" />
        {{ $t("pages.container_qr.title", { name: container?.name ?? t("pages.container_qr.any") }) }}
    </headline>
    <div class="qr-code">
        <form-legend :items="[{ slot: 'info', icon: 'info' }]">
            <template #info>
                {{ $t("pages.container_qr.explanation", { name: container?.name ?? t("pages.container_qr.any") }) }}
            </template>
        </form-legend>
        <form-group :label="$t('form.fields.container.id')" for-id="container" class="qr-code__select">
            <mono-select
                :options="containerOptions"
                :selected="selectedContainer"
                addon-icon="storage"
                :placeholder="$t('pages.container_qr.select_placeholder')"
                :clearable="false"
                @change="onContainerChange"
            />
        </form-group>
        <form-group v-if="loading">
            <div class="qr-code__loading">
                <loading-spinner :branded="true" :size="4" />
                {{ $t("pages.container_qr.loading") }}
            </div>
        </form-group>
        <form-group v-else-if="qrSvg">
            <div class="qr-code__preview" v-html="qrSvg" />
        </form-group>
        <form-group v-if="qrSvg">
            <div class="actions">
                <button type="button" class="btn-default" @click="downloadSvg">
                    <icon name="download" />
                    {{ $t("pages.container_qr.download_svg") }}
                </button>
                <button type="button" class="btn-default" @click="downloadPng">
                    <icon name="download" />
                    {{ $t("pages.container_qr.download_png") }}
                </button>
            </div>
        </form-group>
        <form-group v-else>
            {{ $t("pages.container_qr.no_selection") }}
        </form-group>
    </div>
</template>

<style scoped lang="scss">
.qr-code {
    display: flex;
    flex-direction: column;

    gap: 1rem;
}

.qr-code__preview {
    width: 100%;
    max-width: 20rem;

    :deep(svg) {
        width: 100%;
        height: auto;
    }
}

.actions {
    display: flex;
    flex-wrap: wrap;

    gap: 1ch;
}

.qr-code__loading {
    display: flex;
    align-items: center;

    gap: 1rem;
}
</style>

<style lang="scss">
@use "Abstracts/mixins" as m;

@include m.theme-dark(".qr-code__preview") {
    filter: invert(1);
}
</style>
