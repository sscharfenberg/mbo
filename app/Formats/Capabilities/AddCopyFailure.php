<?php

namespace App\Formats\Capabilities;

/**
 * Reasons a card copy cannot be added to a deck under a given format's rules.
 */
enum AddCopyFailure: string
{
    case ExceedsMaxCopies = 'exceeds_max_copies';
    case ViolatesSingleton = 'violates_singleton';
    case NotInPool = 'not_in_pool';
    case ExceedsDeckSize = 'exceeds_deck_size';
    case ViolatesColorIdentity = 'violates_color_identity';
}
