<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import { useI18n } from "vue-i18n";
import CardFaceImage from "Components/Card/CardFaceImage.vue";
import Modal from "Components/Modal/Modal.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import { useFormatting } from "Composables/useFormatting";
import type { CardPreview } from "Types/cardPreview";
import type { DefaultCardImage } from "Types/defaultCardImage";

/** @emits close — Fired when the modal should be dismissed. */
const emit = defineEmits<{ close: [] }>();
const props = defineProps<{
    /** UUID of the card stack to preview. Used to fetch card details from the API. */
    cardStackId: string;
}>();
const { t } = useI18n();
const { formatPrice } = useFormatting();

/** True while the preview data is being fetched from the server. */
const loading = ref(true);
/** True when the fetch request failed. */
const error = ref(false);
/** Card preview data returned by the API, or null while loading / on error. */
const card = ref<CardPreview | null>(null);

/** Map the API response to the shape expected by CardFaceImage. */
const faceImage = computed<DefaultCardImage | null>(() => {
    if (!card.value) return null;
    return {
        id: props.cardStackId,
        name: card.value.name,
        card_image_0: card.value.card_image_0,
        card_image_1: card.value.card_image_1,
        artist: card.value.artist,
        cn: card.value.collector_number,
        finishes: card.value.finish ? [card.value.finish] : [],
        set: {
            name: card.value.set_name ?? "",
            code: card.value.set_code ?? ""
        }
    };
});

/**
 * Fetch card preview data from the authenticated endpoint on mount.
 * Shows a loading spinner until the response arrives; sets the error
 * state if the request fails.
 */
onMounted(async () => {
    try {
        const response = await fetch(`/collection/cardstack/${props.cardStackId}/preview`, {
            headers: { Accept: "application/json" }
        });
        if (response.ok) {
            card.value = await response.json();
        } else {
            error.value = true;
        }
    } catch {
        error.value = true;
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <modal @close="emit('close')">
        <template #header>{{ card?.name ?? t("components.card_preview.title") }}</template>
        <div v-if="loading" class="preview-loading">
            <loading-spinner :size="4" :branded="true" />
            <p>{{ t("components.card_preview.loading") }}</p>
        </div>
        <div v-else-if="error" class="preview-error">
            <icon name="error" :size="2" />
            <p>{{ t("components.card_preview.error") }}</p>
        </div>
        <div v-else-if="card" class="preview">
            <card-face-image v-if="faceImage?.card_image_0" :card="faceImage" />
            <div class="col">
                <dl class="preview__details">
                    <template v-if="card.condition">
                        <dt>{{ t("form.fields.condition") }}</dt>
                        <dd>{{ t("enums.conditions." + card.condition) }}</dd>
                    </template>
                    <template v-if="card.finish">
                        <dt>{{ t("form.fields.finish") }}</dt>
                        <dd>{{ t("enums.finishes." + card.finish) }}</dd>
                    </template>
                    <template v-if="card.set_code && card.set_name && card.set_icon">
                        <dt>{{ t("form.fields.set_name") }}</dt>
                        <dd class="preview__set">
                            [{{ card.set_code.toUpperCase() }}] {{ card.set_name }}
                            <img :src="card.set_icon" :alt="`[${card.set_code.toUpperCase()}] ${card.set_name}`" />
                        </dd>
                    </template>
                    <template v-if="card.price">
                        <dt>{{ t("form.fields.price") }}</dt>
                        <dd>{{ formatPrice(card.price) }}</dd>
                    </template>
                    <template v-if="card.amount > 1 && card.total_price">
                        <dt>{{ t("components.card_preview.total_price", { amount: card.amount }) }}</dt>
                        <dd>{{ formatPrice(card.total_price) }}</dd>
                    </template>
                </dl>
                <a
                    v-if="card.scryfall_uri"
                    :href="card.scryfall_uri"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn-default"
                >
                    <icon name="external-link" :size="0" />
                    {{ t("components.card_preview.scryfall") }}
                </a>
            </div>
        </div>
    </modal>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@use "Abstracts/mixins" as m;

.preview-loading,
.preview-error {
    display: flex;
    align-items: center;

    padding: 2rem;
    gap: 1rem;
}

.preview {
    display: flex;
    flex-direction: column;

    overflow: hidden;

    gap: 1rem;

    @include m.mq("landscape") {
        align-items: flex-start;
        flex-direction: row;
    }

    &__details {
        display: grid;
        grid-template-columns: auto 1fr;

        margin: 0 0 1rem;
        gap: 0.5rem 1rem;

        dt {
            font-weight: 600;
        }

        dd {
            margin: 0;
        }
    }

    &__set {
        display: flex;
        justify-content: space-between;

        gap: 0.5rem;

        img {
            width: map.get(s.$components, "face-image", "set");
            height: map.get(s.$components, "face-image", "set");
            margin-left: auto;

            filter: none;
        }
    }
}
</style>

<style lang="scss">
@use "Abstracts/mixins" as m;

@include m.theme-dark(".preview__set img") {
    filter: invert(1);
}
</style>
