<?php

namespace App\Services;

use App\Models\Deck;

/**
 * Manages deck card operations that affect the deck itself.
 *
 * Extracted so that both add and remove flows share the same logic
 * for updating derived deck state (e.g. colors).
 */
final class DeckCardService
{
    /** Canonical WUBRG sort order for color identity strings. */
    private const WUBRG = ['W', 'U', 'B', 'R', 'G'];

    /**
     * Recalculate and persist the deck's color identity from its cards.
     *
     * Only applies to formats that do not enforce color identity (i.e.
     * non-commander formats). Commander-like formats derive their identity
     * from the command zone, which is immutable during card adds/removes.
     */
    public static function recalculateColors(Deck $deck): void
    {
        if ($deck->format->rules()->enforcesColorIdentity()) {
            return;
        }

        $merged = $deck->deckCards()
            ->join('oracle_cards', 'deck_cards.oracle_card_id', '=', 'oracle_cards.id')
            ->pluck('oracle_cards.color_identity')
            ->filter()
            ->flatMap(fn (string $ci): array => str_split($ci))
            ->unique()
            ->sort(fn (string $a, string $b): int => array_search($a, self::WUBRG) - array_search($b, self::WUBRG))
            ->implode('');

        $deck->update(['colors' => $merged ?: null]);
    }
}
