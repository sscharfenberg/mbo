<?php

namespace App\Enums;

enum Locale: string
{
    case De = 'de';
    case En = 'en';

    /**
     * The default currency for this locale.
     */
    public function defaultCurrency(): Currency
    {
        return match ($this) {
            self::De => Currency::Eur,
            self::En => Currency::Usd,
        };
    }
}
