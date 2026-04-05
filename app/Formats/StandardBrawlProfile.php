<?php

namespace App\Formats;

/**
 * Standard Brawl: 60-card singleton with commander + color identity, drawing
 * from the Standard card pool.
 */
class StandardBrawlProfile extends CommanderProfile
{
    public function minDeckSize(): int
    {
        return 60;
    }

    public function maxDeckSize(): ?int
    {
        return 60;
    }
}
