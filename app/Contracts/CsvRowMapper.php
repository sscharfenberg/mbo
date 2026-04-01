<?php

namespace App\Contracts;

/**
 * Contract for source-specific CSV row mappers.
 *
 * Each implementation handles one import source (MBO, Moxfield, Archidekt),
 * declaring its required headers and normalizing raw CSV values to the
 * canonical format used by CsvImportService.
 */
interface CsvRowMapper
{
    /**
     * Column headers required by this mapper (lowercase).
     *
     * @return array<string>
     */
    public function requiredHeaders(): array;

    /**
     * Map a raw CSV row to a normalized import array, or null to skip.
     *
     * @param  array<string, string>  $row  Keyed by lowercase header name.
     * @return array{scryfall_id: ?string, set_code: string, collector_number: string, amount: int, name: string, language: string, condition: ?string, finish: string}|null
     */
    public function mapRow(array $row): ?array;
}
