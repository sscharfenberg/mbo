<?php

namespace App\Services\CsvMappers;

use App\Contracts\CsvRowMapper;

class MboMapper implements CsvRowMapper
{
    public function requiredHeaders(): array
    {
        return ['scryfall id', 'count', 'name', 'edition', 'condition', 'language', 'foil', 'collector number'];
    }

    public function mapRow(array $row): ?array
    {
        $amount = (int) ($row['count'] ?? 0);
        if ($amount < 1) {
            return null;
        }

        return [
            'scryfall_id' => trim($row['scryfall id'] ?? '') ?: null,
            'set_code' => strtolower(trim($row['edition'] ?? '')),
            'collector_number' => trim($row['collector number'] ?? ''),
            'amount' => $amount,
            'name' => trim($row['name'] ?? ''),
            'language' => trim($row['language'] ?? 'en'),
            'condition' => trim($row['condition'] ?? '') ?: null,
            'finish' => trim($row['foil'] ?? 'nonfoil') ?: 'nonfoil',
        ];
    }
}
