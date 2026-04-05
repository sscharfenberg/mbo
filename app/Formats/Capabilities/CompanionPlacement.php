<?php

namespace App\Formats\Capabilities;

/**
 * Where a companion card sits relative to the main deck.
 *
 * - Sideboard: companion is part of the 15-card sideboard (constructed formats).
 * - Outside:   companion sits outside the 99+1 deck and is paid for with {3} (Commander).
 */
enum CompanionPlacement: string
{
    case Sideboard = 'sideboard';
    case Outside = 'outside';
}
