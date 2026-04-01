<?php

namespace App\Enums;

/**
 * Supported CSV import source formats.
 *
 * Each case maps to a CsvRowMapper implementation that handles
 * column mapping and value normalization for that source.
 */
enum ImportSource: string
{
    case Mbo = 'mbo';
    case Moxfield = 'moxfield';
    case Archidekt = 'archidekt';
}
