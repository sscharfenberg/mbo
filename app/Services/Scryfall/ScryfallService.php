<?php

namespace App\Services\Scryfall;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class ScryfallService
{
    /**
     * Create a pre-configured HTTP client with Scryfall's required headers.
     *
     * @return PendingRequest
     */
    protected function http(): PendingRequest
    {
        return Http::withHeaders(config('mbo.scryfall.header'));
    }
}