<?php

namespace App\Services\CsvMappers;

use App\Contracts\CsvRowMapper;

/**
 * CSV row mapper for Moxfield exports.
 *
 * Normalizes full language names to ISO codes, human-readable conditions
 * to enum values, and empty foil strings to 'nonfoil'.
 * Moxfield has no Scryfall ID column — card lookup always uses set code + collector number.
 */
class MoxfieldMapper implements CsvRowMapper
{
    /** @var array<string, string> */
    private const LANGUAGE_MAP = [
        'English' => 'en',
        'German' => 'de',
        'French' => 'fr',
        'Italian' => 'it',
        'Spanish' => 'es',
        'Portuguese' => 'pt',
        'Japanese' => 'ja',
        'Korean' => 'ko',
        'Simplified Chinese' => 'zhs',
        'Traditional Chinese' => 'zht',
        'Russian' => 'ru',
        'Phyrexian' => 'ph',
    ];

    /** @var array<string, string> */
    private const CONDITION_MAP = [
        'Mint' => 'mint',
        'Near Mint' => 'near_mint',
        'Lightly Played' => 'light_played',
        'Moderately Played' => 'played',
        'Heavily Played' => 'poor',
        'Damaged' => 'poor',
    ];

    public function requiredHeaders(): array
    {
        return ['count', 'name', 'edition', 'condition', 'language', 'foil', 'collector number'];
    }

    public function mapRow(array $row): ?array
    {
        $amount = (int) ($row['count'] ?? 0);
        if ($amount < 1) {
            return null;
        }

        $condition = trim($row['condition'] ?? '');
        $finish = strtolower(trim($row['foil'] ?? ''));

        return [
            'scryfall_id' => null,
            'set_code' => strtolower(trim($row['edition'] ?? '')),
            'collector_number' => trim($row['collector number'] ?? ''),
            'amount' => $amount,
            'name' => trim($row['name'] ?? ''),
            'language' => self::LANGUAGE_MAP[trim($row['language'] ?? '')] ?? 'en',
            'condition' => self::CONDITION_MAP[$condition] ?? null,
            'finish' => $finish ?: 'nonfoil',
        ];
    }
}
