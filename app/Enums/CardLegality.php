<?php

namespace App\Enums;

/**
 * Legality status for an oracle card in a specific format.
 *
 * The `not_legal` status is not represented — absence from the pivot table
 * implies the card is not legal in that format.
 */
enum CardLegality: string
{
    case Legal = 'legal';
    case Banned = 'banned';
    case Restricted = 'restricted';
}
