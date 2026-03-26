<?php

namespace App\Services;

/**
 * Parses a card search query string into structured tokens.
 *
 * Supported syntax:
 *   - Space-separated name segments: "sol ring" → name LIKE %sol% AND name LIKE %ring%
 *   - set:xxx (aliases: s:, e:)     → filter by set code
 *   - number:xxx (alias: cn:)       → filter by collector number
 *
 * @phpstan-type ParsedSearch array{name_segments: string[], set_code: string|null, collector_number: string|null}
 */
class CardSearchParser
{
    /**
     * Parse a raw search string into structured filter tokens.
     *
     * Returns null when the input is too short to run a meaningful search
     * (no set/number filter and name query < 2 characters).
     *
     * @return ParsedSearch|null
     */
    public static function parse(string $query): ?array
    {
        // Extract "set:xxx" (aliases: s:, e:) tokens.
        $setCode = null;
        $remaining = preg_replace_callback(
            '/\b(?:set|s|e):(\S+)/i',
            function (array $m) use (&$setCode): string {
                $setCode = strtolower($m[1]);

                return '';
            },
            $query
        );

        // Extract "number:xxx" (alias: cn:) tokens.
        $collectorNumber = null;
        $remaining = preg_replace_callback(
            '/\b(?:number|cn):(\S+)/i',
            function (array $m) use (&$collectorNumber): string {
                $collectorNumber = $m[1];

                return '';
            },
            (string) $remaining
        );

        $remaining = trim((string) preg_replace('/\s+/', ' ', $remaining));

        // Require at least a filter or a name query of ≥ 2 characters.
        if (! $setCode && ! $collectorNumber && mb_strlen($remaining) < 2) {
            return null;
        }

        return [
            'name_segments' => array_values(array_filter(explode(' ', $remaining), fn (string $s) => $s !== '')),
            'set_code' => $setCode,
            'collector_number' => $collectorNumber,
        ];
    }
}
