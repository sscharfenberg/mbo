<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import { VueDraggable } from "vue-draggable-plus";
import { useI18n } from "vue-i18n";
import DeckAddGroupModal from "@/pages/Decks/Deck/Add/DeckAddGroupModal.vue";
import DeckCardActionsMenu from "@/pages/Decks/Deck/Cards/DeckCardActionsMenu.vue";
import DeckCardPreviewModal from "@/pages/Decks/Deck/Cards/DeckCardPreviewModal.vue";
import DeckGroupHeadline from "@/pages/Decks/Deck/Cards/DeckGroupHeadline.vue";
import CardImagePreview from "Components/Card/CardImagePreview.vue";
import ManaCost from "Components/Card/ManaCost.vue";
import Icon from "Components/UI/Icon.vue";
import { useDeckGrouping } from "Composables/useDeckGrouping.ts";
import type { DeckSort } from "Composables/useDeckSort.ts";
import type { DeckCardRow, DeckCategoryRow, DeckCommander } from "Types/deckPage";
/** Shape of the data needed by the preview modal. */
interface PreviewTarget {
    name: string;
    cardImage0: string | null;
    cardImage1: string | null;
}
/** A unified card group — either a default type group or a custom category. */
interface CardSection {
    key: string;
    label: string;
    cards: DeckCardRow[];
    count: number;
}
/** A visual section: commander zone, card group, or the "create group" drop target. */
type Section =
    | { kind: "commanders"; commanders: DeckCommander[] }
    | { kind: "group"; group: CardSection }
    | { kind: "create-group" };
/** Minimum column width in pixels. Columns are added when the container can fit another one. */
const MIN_COL_WIDTH = 256;
/** Hard cap on the number of columns regardless of available width. */
const MAX_COLUMNS = 4;
/** Gap between columns in pixels — must match the CSS gap value. */
const COL_GAP = 16;
const props = defineProps<{
    /** UUID of the deck. */
    deckId: string;
    /** Commanders / command zone cards with full oracle + printing data. */
    commanders: DeckCommander[];
    /** All cards in the deck with full oracle + printing data. */
    cards: DeckCardRow[];
    /** User-defined categories for this deck. */
    categories: DeckCategoryRow[];
    /** Active sort mode — by mana value or alphabetically by name. */
    sortMode: DeckSort;
    /** Maximum length for a category name. */
    categoryNameMax: number;
}>();
const { t } = useI18n();
/** Cards without a custom category — grouped by card type. */
const { groups: typeGroups } = useDeckGrouping(
    () => props.cards.filter(c => c.category_id === null),
    () => props.sortMode
);
/**
 * Merge default type groups and custom categories into a single alphabetically
 * sorted list. Default group labels are resolved via i18n so sorting is
 * locale-aware.
 */
const allGroups = computed<CardSection[]>(() => {
    const sections: CardSection[] = [];
    for (const g of typeGroups.value) {
        sections.push({
            key: g.group,
            label: t(`pages.deck.groups.${g.group}`),
            cards: g.cards,
            count: g.count,
        });
    }
    const catBuckets = new Map<string, CardSection>();
    for (const cat of props.categories) {
        catBuckets.set(cat.id, { key: `cat-${cat.id}`, label: cat.name, cards: [], count: 0 });
    }
    for (const card of props.cards) {
        if (card.category_id === null) continue;
        const bucket = catBuckets.get(card.category_id);
        if (bucket) {
            bucket.cards.push(card);
            bucket.count += card.quantity;
        }
    }
    for (const bucket of catBuckets.values()) {
        if (bucket.cards.length > 0) sections.push(bucket);
    }
    sections.sort((a, b) => a.label.localeCompare(b.label));
    return sections;
});
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
 * Distribute sections column-first: fill each column top-to-bottom before
 * moving to the next. Column count is driven by container width via ResizeObserver.
 */
const columns = computed<Section[][]>(() => {
    const sections: Section[] = [];
    if (props.commanders.length > 0) {
        sections.push({ kind: "commanders", commanders: props.commanders });
    }
    for (const group of allGroups.value) {
        sections.push({ kind: "group", group });
    }
    const count = Math.min(sections.length, colCount.value);
    if (count === 0) return [];
    const perCol = Math.ceil(sections.length / count);
    const cols: Section[][] = Array.from({ length: count }, () => []);
    for (let i = 0; i < sections.length; i++) {
        cols[Math.floor(i / perCol)].push(sections[i]);
    }
    cols[cols.length - 1].push({ kind: "create-group" });
    return cols;
});
/** True while a card is being dragged — shows the drop target and dims default groups. */
const dragging = ref(false);
/** SortableJS group config for default groups: cards can be dragged out but not dropped in. */
const defaultGroup = { name: "deck-cards", pull: "clone" as const, put: false };
/** Items dropped on the "create new group" target. Kept as a ref for VueDraggable v-model. */
const dropTargetList = ref<DeckCardRow[]>([]);
/** SortableJS group config for the "create new group" drop target: accepts drops only. */
const createGroupTarget = { name: "deck-cards", pull: false, put: true };
/** True when the "create group" modal should be shown. */
const showCreateGroupModal = ref(false);
/** The card that was dropped on the "create new group" target. */
const droppedCard = ref<DeckCardRow | null>(null);
/** Called when a card is dropped on the "create new group" target. */
function onDropToCreateGroup(): void {
    dragging.value = false;
    if (dropTargetList.value.length > 0) {
        droppedCard.value = dropTargetList.value[0];
        dropTargetList.value = [];
        showCreateGroupModal.value = true;
    }
}
/** The card currently shown in the preview modal, or null when hidden. */
const previewTarget = ref<PreviewTarget | null>(null);
</script>

<template>
    <div ref="containerRef" class="card-groups">
        <div v-for="(col, ci) in columns" :key="ci" class="card-groups__column">
            <template
                v-for="section in col"
                :key="section.kind === 'commanders' ? 'cmd' : section.kind === 'group' ? section.group.key : 'new'"
            >
                <section
                    v-if="section.kind === 'commanders'"
                    class="card-group"
                    :class="{ 'card-group--dragging': dragging }"
                >
                    <deck-group-headline
                        >{{ $t("pages.deck.commanders") }} ({{ section.commanders.length }})</deck-group-headline
                    >
                    <ul class="card-group__list">
                        <li v-for="commander in section.commanders" :key="commander.oracle_card_id" class="card">
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
                <section
                    v-else-if="section.kind === 'group'"
                    class="card-group"
                    :class="{ 'card-group--dragging': dragging }"
                >
                    <deck-group-headline
                        >{{ section.group.label }} ({{ section.group.count }})</deck-group-headline
                    >
                    <VueDraggable
                        :model-value="section.group.cards"
                        tag="ul"
                        class="card-group__list"
                        handle=".card__drag-handle"
                        :group="defaultGroup"
                        :sort="false"
                        ghost-class="card--ghost"
                        @start="dragging = true"
                        @end="dragging = false"
                    >
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
                    </VueDraggable>
                </section>
                <section v-else-if="dragging" class="card-group card-group__drop-target">
                    <icon name="add" :size="2" />
                    {{ $t("pages.deck.create_group.link") }}
                    <VueDraggable
                        v-model="dropTargetList"
                        tag="div"
                        class="card-group__drop-zone"
                        :group="createGroupTarget"
                        ghost-class="card--ghost"
                        @add="onDropToCreateGroup"
                    />
                </section>
            </template>
        </div>
    </div>
    <p v-if="!columns.length">{{ $t("pages.deck.no_cards") }}</p>
    <deck-add-group-modal
        v-if="showCreateGroupModal && droppedCard"
        :deck-id="props.deckId"
        :card="droppedCard"
        :category-name-max="props.categoryNameMax"
        @close="
            showCreateGroupModal = false;
            droppedCard = null;
        "
    />
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

    &__drag-handle {
        cursor: move;
    }
}

:deep(.card-preview__trigger) {
    flex-grow: 1;

    overflow: hidden;

    padding: 0;

    white-space: nowrap;
    text-overflow: ellipsis;
}
</style>
