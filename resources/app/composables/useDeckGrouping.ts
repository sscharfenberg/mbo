import type { ComputedRef, MaybeRefOrGetter } from "vue";
import { computed, toValue } from "vue";
import type { DeckCardRow } from "Types/deckPage";

/** Supported deck card type groups, in display order. */
export type DeckCardGroup =
    | "creature"
    | "planeswalker"
    | "battle"
    | "artifact"
    | "enchantment"
    | "instant"
    | "sorcery"
    | "land"
    | "other";

/** A group of deck cards sharing a primary card type. */
export type DeckCardGrouping = {
    /** Primary card type for this group. */
    group: DeckCardGroup;
    /** Cards belonging to the group, in the order they were provided. */
    cards: DeckCardRow[];
    /** Sum of quantities of the cards in this group. */
    count: number;
};

/** Return type of {@link useDeckGrouping}. */
export type UseDeckGroupingReturn = {
    /** Non-empty groups in canonical display order. */
    groups: ComputedRef<DeckCardGrouping[]>;
};

/**
 * Canonical display order and precedence for primary card types. A card is
 * placed in the first group its type line matches when scanned in this order.
 * Lands take precedence over creature / artifact / enchantment so that
 * "Creature Land" / "Artifact Land" cards land (no pun intended) in the land
 * bucket, matching what deck builders like Moxfield and Archidekt do.
 */
const GROUP_ORDER: readonly DeckCardGroup[] = [
    "land",
    "creature",
    "planeswalker",
    "battle",
    "instant",
    "sorcery",
    "artifact",
    "enchantment",
    "other",
] as const;

/** Match tokens (case-insensitive) used to bucket a type line into a group. */
const GROUP_MATCHERS: Record<Exclude<DeckCardGroup, "other">, string> = {
    land: "Land",
    creature: "Creature",
    planeswalker: "Planeswalker",
    battle: "Battle",
    instant: "Instant",
    sorcery: "Sorcery",
    artifact: "Artifact",
    enchantment: "Enchantment",
};

/**
 * Determine the primary card-type group for a single card based on its
 * front-face type line. Falls back to `"other"` for cards with an empty
 * or unrecognised type line (tokens, schemes, etc.).
 */
function resolveGroup(typeLine: string): DeckCardGroup {
    for (const group of GROUP_ORDER) {
        if (group === "other") continue;
        if (typeLine.includes(GROUP_MATCHERS[group])) return group;
    }
    return "other";
}

/**
 * Group a reactive list of deck cards by their primary card type.
 *
 * The input can be a ref, a getter, or a plain array — `toValue` normalises
 * it so callers can pass `props.cards` directly. Groups are returned in the
 * canonical display order defined by {@link GROUP_ORDER}, with empty groups
 * omitted so consumers can `v-for` without guarding.
 *
 * @param cards - Deck cards to group. Accepts any `MaybeRefOrGetter<DeckCardRow[]>`.
 * @returns Reactive list of non-empty groups.
 */
export function useDeckGrouping(cards: MaybeRefOrGetter<DeckCardRow[]>): UseDeckGroupingReturn {
    const groups = computed<DeckCardGrouping[]>(() => {
        const buckets = new Map<DeckCardGroup, DeckCardGrouping>();
        for (const card of toValue(cards)) {
            const group = resolveGroup(card.type_line);
            let bucket = buckets.get(group);
            if (bucket === undefined) {
                bucket = { group, cards: [], count: 0 };
                buckets.set(group, bucket);
            }
            bucket.cards.push(card);
            bucket.count += card.quantity;
        }
        return GROUP_ORDER
            .map((group) => buckets.get(group))
            .filter((bucket): bucket is DeckCardGrouping => bucket !== undefined);
    });

    return { groups };
}