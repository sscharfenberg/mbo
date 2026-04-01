<?php

namespace App\Services\CsvMappers;

use App\Contracts\CsvRowMapper;

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

    /** @var array<string, string> */
    private const FINISH_MAP = [
        'Normal' => 'nonfoil',
        'Foil' => 'foil',
        'Etched' => 'etched',
    ];

    public function requiredHeaders(): array
    {
        return ['quantity', 'name', 'edition', 'condition', 'language', 'finish', 'collector number'];
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
            'set_code' => strtolower(trim($row['edition'] ?? '')),
            'collector_number' => trim($row['collector number'] ?? ''),
            'amount' => $amount,
            'name' => trim($row['name'] ?? ''),
            'language' => strtolower(trim($row['language'] ?? 'en')),
            'condition' => self::CONDITION_MAP[$condition] ?? null,
            'finish' => self::FINISH_MAP[$finish] ?? 'nonfoil',
        ];
    }
}
