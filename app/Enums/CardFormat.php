<?php

namespace App\Enums;

use App\Formats\CommanderProfile;
use App\Formats\ConstructedProfile;
use App\Formats\FormatProfile;
use App\Formats\GladiatorProfile;
use App\Formats\OathbreakerProfile;
use App\Formats\StandardBrawlProfile;

/**
 * Playable formats for Magic: The Gathering cards.
 *
 * Cases match the format strings stored in the legalities pivot table
 * (sourced from Scryfall). Only formats with a matching case here are
 * displayed in the UI — new Scryfall formats appear automatically in
 * the DB but require a case + i18n string to be shown.
 */
enum CardFormat: string
{
    case Standard = 'standard';
    case Pioneer = 'pioneer';
    case Modern = 'modern';
    case Legacy = 'legacy';
    case Vintage = 'vintage';
    case Commander = 'commander';
    case Oathbreaker = 'oathbreaker';
    case Brawl = 'brawl';
    case StandardBrawl = 'standardbrawl';
    case Alchemy = 'alchemy';
    case Historic = 'historic';
    case Timeless = 'timeless';
    case Gladiator = 'gladiator';
    case Pauper = 'pauper';
    case PauperCommander = 'paupercommander';
    case Penny = 'penny';
    case Duel = 'duel';
    case Premodern = 'premodern';
    case Predh = 'predh';
    case Oldschool = 'oldschool';
    case Future = 'future';

    /**
     * The construction-rules profile for this format.
     *
     * Card-pool differences (legality/banned lists) live in the `legalities`
     * pivot table — this method only decides which deckbuilding rules apply.
     */
    public function rules(): FormatProfile
    {
        return match ($this) {
            self::Commander,
            self::Duel,
            self::Brawl,
            self::PauperCommander,
            self::Predh => new CommanderProfile($this),
            self::StandardBrawl => new StandardBrawlProfile($this),
            self::Oathbreaker => new OathbreakerProfile($this),
            self::Gladiator => new GladiatorProfile($this),
            default => new ConstructedProfile($this),
        };
    }
}
