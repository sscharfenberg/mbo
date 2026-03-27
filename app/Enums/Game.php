<?php

namespace App\Enums;

enum Game: int
{
    case Paper = 1;
    case Arena = 2;
    case Mtgo = 4;
    case Astral = 8;
    case Sega = 16;

    /**
     * Convert a Scryfall games array (e.g. ["paper", "arena"]) to a bitmask.
     *
     * @param  array<string>  $games
     */
    public static function fromScryfallArray(array $games): int
    {
        $mask = 0;

        foreach ($games as $game) {
            $case = self::fromLabel($game);

            if ($case !== null) {
                $mask |= $case->value;
            }
        }

        return $mask;
    }

    /**
     * Check whether a bitmask includes this game.
     */
    public function isPresentIn(int $mask): bool
    {
        return ($mask & $this->value) !== 0;
    }

    /**
     * Lowercase label for display and i18n keys.
     */
    public function label(): string
    {
        return strtolower($this->name);
    }

    /**
     * Resolve a Game case from its lowercase label string.
     */
    public static function fromLabel(string $label): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->label() === $label) {
                return $case;
            }
        }

        return null;
    }

    /**
     * All lowercase label strings.
     *
     * @return array<string>
     */
    public static function labels(): array
    {
        return array_map(fn (self $case) => $case->label(), self::cases());
    }

    /**
     * Decode a bitmask into an array of lowercase label strings.
     *
     * @return array<string>
     */
    public static function labelsFromMask(int $mask): array
    {
        $labels = [];

        foreach (self::cases() as $case) {
            if ($case->isPresentIn($mask)) {
                $labels[] = $case->label();
            }
        }

        return $labels;
    }
}
