<?php

namespace App\Services;

use Transliterator;

/**
 * Normalizes card names and search queries for comparison against
 * `oracle_cards.searchable_name` / `default_cards.searchable_name`.
 *
 * Pipeline:
 *   1. Any-Latin; Latin-ASCII; Lower() via ICU Transliterator
 *      (folds Dûl → dul, Æ → ae, Ø → o, Greek/Cyrillic → Latin, lowercases,
 *      and normalizes smart quotes to straight quotes)
 *   2. Drop apostrophes (so "Lim-Dul's" becomes "lim-duls", not "lim-dul s")
 *   3. Replace remaining punctuation (hyphens, colons, commas, etc.) with a
 *      space, so hyphenated names split into separate tokens
 *   4. Collapse runs of whitespace and trim
 *
 * The same pipeline is run on both write side (Scryfall import) and read
 * side (CardSearchParser) so the stored column and the query tokens are
 * guaranteed to be comparable byte-for-byte.
 */
final class CardNameNormalizer
{
    private static ?Transliterator $transliterator = null;

    /**
     * Normalize a card name or raw query segment to the canonical form.
     *
     * Returns the empty string for empty / whitespace-only input.
     */
    public static function normalize(string $input): string
    {
        if ($input === '') {
            return '';
        }

        $transliterated = self::transliterator()->transliterate($input);
        if ($transliterated === false) {
            // Fallback: lowercase only. Transliterate should never fail for
            // valid UTF-8, but we don't want to blow up imports on bad data.
            $transliterated = mb_strtolower($input);
        }

        // Drop apostrophes (straight — smart quotes were normalized above)
        $withoutApostrophes = str_replace("'", '', $transliterated);
        // Replace remaining non-alphanumerics with a space so hyphenated
        // names ("Lim-Dul") split into separate searchable tokens
        $spaced = preg_replace('/[^a-z0-9\s]/', ' ', $withoutApostrophes) ?? '';
        $collapsed = preg_replace('/\s+/', ' ', $spaced) ?? '';

        return trim($collapsed);
    }

    private static function transliterator(): Transliterator
    {
        if (self::$transliterator === null) {
            self::$transliterator = Transliterator::create('Any-Latin; Latin-ASCII; Lower()');
        }

        return self::$transliterator;
    }
}
