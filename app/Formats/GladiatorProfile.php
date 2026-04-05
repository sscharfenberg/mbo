<?php

namespace App\Formats;

/**
 * Gladiator: 100-card singleton, no commander, no color identity restriction.
 */
class GladiatorProfile extends FormatProfile
{
    public function minDeckSize(): int
    {
        return 100;
    }

    public function maxDeckSize(): ?int
    {
        return 100;
    }

    public function maxSideboardSize(): int
    {
        return 0;
    }

    public function maxCopies(): int
    {
        return 1;
    }
}
