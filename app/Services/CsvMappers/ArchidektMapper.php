<?php

namespace App\Services\CsvMappers;

use App\Contracts\CsvRowMapper;

/**
 * CSV row mapper for Archidekt exports.
 *
 * Normalizes abbreviated conditions (NM, LP, etc.) to enum values,
 * title-case finishes to lowercase labels, and uppercase languages to ISO codes.
 * Uses Scryfall ID when available, with set code + collector number as fallback.
 */
class ArchidektMapper implements CsvRowMapper
{
    /** @var array<string, string> */
    private const CONDITION_MAP = [
        'NM' => 'near_mint',
        'LP' => 'light_played',
        'MP' => 'played',
        'HP' => 'poor',
        'D' => 'poor',
    ];

    /**
     * Maps Archidekt's uppercase language codes to CardLanguage enum values.
     * Archidekt uses ISO 3166 country codes for some languages (JP, KR, CT, CS)
     * while CardLanguage uses Scryfall's ISO 639-based codes.
     *
     * @var array<string, string>
     */
    private const LANGUAGE_MAP = [
        'EN' => 'en',
        'DE' => 'de',
        'FR' => 'fr',
        'IT' => 'it',
        'ES' => 'es',
        'SP' => 'es',
        'PT' => 'pt',
        'JP' => 'ja',
        'KR' => 'ko',
        'CS' => 'zhs',
        'CT' => 'zht',
        'RU' => 'ru',
        'PH' => 'ph',
    ];

    /** @var array<string, string> */
    private const FINISH_MAP = [
        'Normal' => 'nonfoil',
        'Foil' => 'foil',
        'Etched' => 'etched',
    ];

    public function requiredHeaders(): array
    {
        return ['quantity', 'name', 'edition code', 'condition', 'language', 'finish', 'collector number'];
    }

    public function mapRow(array $row): ?array
    {
        $amount = (int) ($row['quantity'] ?? 0);
        if ($amount < 1) {
            return null;
        }

        $condition = trim($row['condition'] ?? '');
        $finish = trim($row['finish'] ?? '');

        return [
            'scryfall_id' => trim($row['scryfall id'] ?? '') ?: null,
            'set_code' => strtolower(trim($row['edition code'] ?? '')),
            'collector_number' => trim($row['collector number'] ?? ''),
            'amount' => $amount,
            'name' => trim($row['name'] ?? ''),
            'language' => self::LANGUAGE_MAP[strtoupper(trim($row['language'] ?? 'EN'))] ?? null,
            'condition' => self::CONDITION_MAP[$condition] ?? null,
            'finish' => self::FINISH_MAP[$finish] ?? 'nonfoil',
        ];
    }
}
