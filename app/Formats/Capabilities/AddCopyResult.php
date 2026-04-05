<?php

namespace App\Formats\Capabilities;

/**
 * Immutable result of asking a FormatProfile whether a card copy may be added.
 */
final readonly class AddCopyResult
{
    private function __construct(
        public bool $allowed,
        public ?AddCopyFailure $reason = null,
    ) {}

    public static function allowed(): self
    {
        return new self(true);
    }

    public static function denied(AddCopyFailure $reason): self
    {
        return new self(false, $reason);
    }
}
