<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import DeckCardActionsMenu from "@/pages/Decks/Deck/Cards/DeckCardActionsMenu.vue";
import DeckCardPreviewModal from "@/pages/Decks/Deck/Cards/DeckCardPreviewModal.vue";
import DeckGroupHeadline from "@/pages/Decks/Deck/Cards/DeckGroupHeadline.vue";
import CardImagePreview from "Components/Card/CardImagePreview.vue";
import ManaCost from "Components/Card/ManaCost.vue";
import Icon from "Components/UI/Icon.vue";
import { useDeckGrouping } from "Composables/useDeckGrouping.ts";
import type { DeckCardGrouping } from "Composables/useDeckGrouping.ts";
import type { DeckSort } from "Composables/useDeckSort.ts";
import type { DeckCardRow, DeckCommander } from "Types/deckPage";

/** Shape of the data needed by the preview modal. */
interface PreviewTarget {
    name: string;
    cardImage0: string | null;
    cardImage1: string | null;
}

/** A visual section: commander zone, card-type group, or the "create group" drop target. */
type Section =
    | { kind: "commanders"; commanders: DeckCommander[] }
    | { kind: "group"; group: DeckCardGrouping }
    | { kind: "create-group" };

/** Minimum column width in pixels. Columns are added when the container can fit another one. */
const MIN_COL_WIDTH = 256;
/** Hard cap on the number of columns regardless of available width. */
const MAX_COLUMNS = 4;
/** Gap between columns in pixels — must match the CSS gap value. */
const COL_GAP = 16;

const props = defineProps<{
    /** Commanders / command zone cards with full oracle + printing data. */
    commanders: DeckCommander[];
    /** All cards in the deck with full oracle + printing data. */
    cards: DeckCardRow[];
    /** Active sort mode — by mana value or alphabetically by name. */
    sortMode: DeckSort;
}>();
const { groups } = useDeckGrouping(
    () => props.cards,
    () => props.sortMode
);

/** Template ref for the card-groups container element. */
const containerRef = ref<HTMLElement | null>(null);
/** Number of columns that fit in the current container width. */
const colCount = ref(1);
let observer: ResizeObserver | null = null;

onMounted(() => {
    if (!containerRef.value) return;
    observer = new ResizeObserver(([entry]) => {
        const width = entry.contentBoxSize[0].inlineSize;
        // How many columns fit: solve width >= n * MIN_COL_WIDTH + (n-1) * COL_GAP
        const n = Math.floor((width + COL_GAP) / (MIN_COL_WIDTH + COL_GAP));
        colCount.value = Math.max(1, Math.min(n, MAX_COLUMNS));
    });
    observer.observe(containerRef.value);
});

onBeforeUnmount(() => {
    observer?.disconnect();
});

/**
 * Distribute all sections round-robin into columns.
 * The column count is driven by the container width via ResizeObserver.
 */
const columns = computed<Section[][]>(() => {
    const sections: Section[] = [];
    if (props.commanders.length > 0) {
        sections.push({ kind: "commanders", commanders: props.commanders });
    }
    for (const group of groups.value) {
        sections.push({ kind: "group", group });
    }
    const count = Math.min(sections.length, colCount.value);
    if (count === 0) return [];
    const cols: Section[][] = Array.from({ length: count }, () => []);
    for (let i = 0; i < sections.length; i++) {
        cols[i % count].push(sections[i]);
    }
    cols[cols.length - 1].push({ kind: "create-group" });
    return cols;
});

/** The card currently shown in the preview modal, or null when hidden. */
const previewTarget = ref<PreviewTarget | null>(null);
</script>

<template>
    <h2>CardView: text</h2>
    <div ref="containerRef" class="card-groups">
        <div v-for="(col, ci) in columns" :key="ci" class="card-groups__column">
            <template
                v-for="section in col"
                :key="section.kind === 'commanders' ? 'cmd' : section.kind === 'group' ? section.group.group : 'new'"
            >
                <section v-if="section.kind === 'commanders'" class="card-group">
                    <deck-group-headline
                        >{{ $t("pages.deck.commanders") }} ({{ section.commanders.length }})</deck-group-headline
                    >
                    <ul class="card-group__list">
                        <li v-for="commander in section.commanders" :key="commander.oracle_card_id" class="card">
                            <span class="card__drag-handle"><icon name="drag" :size="1" /></span>
                            <card-image-preview
                                :src="commander.default_card.card_image_0"
                                :alt="commander.name"
                                @preview="
                                    previewTarget = {
                                        name: commander.name,
                                        cardImage0: commander.default_card.card_image_0,
                                        cardImage1: commander.default_card.card_image_1
                                    }
                                "
                            >
                                {{ commander.name }}
                            </card-image-preview>
                            <mana-cost v-for="(cost, i) in commander.mana_cost" :key="i" :mana-cost="cost" />
                            <deck-card-actions-menu />
                        </li>
                    </ul>
                </section>
                <section v-else-if="section.kind === 'group'" class="card-group">
                    <deck-group-headline
                        >{{ $t(`pages.deck.groups.${section.group.group}`) }} ({{
                            section.group.count
                        }})</deck-group-headline
                    >
                    <ul class="card-group__list">
                        <li v-for="card in section.group.cards" :key="card.id" class="card">
                            <span class="card__drag-handle"><icon name="drag" :size="1" /></span>
                            <card-image-preview
                                :src="card.default_card.card_image_0"
                                :alt="card.name"
                                @preview="
                                    previewTarget = {
                                        name: card.name,
                                        cardImage0: card.default_card.card_image_0,
                                        cardImage1: card.default_card.card_image_1
                                    }
                                "
                            >
                                {{ card.name }}
                            </card-image-preview>
                            <mana-cost v-for="(cost, i) in card.mana_cost" :key="i" :mana-cost="cost" />
                            <deck-card-actions-menu />
                        </li>
                    </ul>
                </section>
                <section v-else class="card-group card-group__drop-target">
                    <icon name="add" :size="2" />
                    {{ $t("pages.deck.create_group") }}
                </section>
            </template>
        </div>
    </div>
    <p v-if="!columns.length">{{ $t("pages.deck.no_cards") }}</p>
    <deck-card-preview-modal
        v-if="previewTarget"
        :name="previewTarget.name"
        :card-image0="previewTarget.cardImage0"
        :card-image1="previewTarget.cardImage1"
        @close="previewTarget = null"
    />
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.card {
    display: flex;
    align-items: center;

    padding: map.get(s.$pages, "deck", "card", "padding");
    border: map.get(s.$pages, "deck", "card", "border") solid map.get(c.$pages, "deck", "card", "border");
    gap: map.get(s.$pages, "deck", "card", "gap");

    background-color: map.get(c.$pages, "deck", "card", "background");
    color: map.get(c.$pages, "deck", "card", "surface");
    border-radius: map.get(s.$pages, "deck", "card", "radius");
}

:deep(.card-preview__trigger) {
    flex-grow: 1;

    overflow: hidden;

    padding: 0;

    white-space: nowrap;
    text-overflow: ellipsis;
}
</style>
