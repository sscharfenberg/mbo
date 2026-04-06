<?php

namespace App\Http\Controllers\Api;

use App\Enums\CardFormat;
use App\Http\Controllers\Controller;
use App\Services\CardSearchParser;
use App\Services\CommanderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommanderController extends Controller
{
    /**
     * Search for cards that can be a commander.
     *
     * A card qualifies when at least one of its faces is either:
     *  - a legendary creature (type_line contains "Legendary" and has power + toughness), or
     *  - has "can be your commander" in its oracle text.
     *
     * Supports "set:xxx" and "number:xxx" tokens via CardSearchParser.
     *
     * Required query parameters:
     *  - `format=<key>` — CardFormat enum value; filters to cards legal/restricted in this format.
     *
     * Optional query parameters:
     *  - `rule0=1`              — skip commander-legality filters (any oracle card matches).
     *  - `partner=1`            — restrict to cards with the Partner keyword.
     *  - `friends_forever=1`    — restrict to cards with "Friends forever".
     *  - `doctors_companion=1`  — restrict to cards with "Doctor's companion".
     *  - `background=1`         — restrict to cards with the Background subtype.
     *  - `exclude=<uuid>`       — exclude a specific oracle card (e.g. the already-selected commander).
     */
    public function search(Request $request): JsonResponse
    {
        $format = CardFormat::tryFrom($request->query('format', ''));

        if (! $format) {
            return response()->json([]);
        }

        $parsed = CardSearchParser::parse(trim($request->query('q', '')));

        if (! $parsed) {
            return response()->json([]);
        }

        $filters = [
            'rule0' => $request->boolean('rule0'),
            'partner' => $request->boolean('partner'),
            'friends_forever' => $request->boolean('friends_forever'),
            'doctors_companion' => $request->boolean('doctors_companion'),
            'background' => $request->boolean('background'),
            'exclude' => $request->query('exclude'),
        ];

        return response()->json(CommanderService::searchCommanders($format, $parsed, $filters));
    }
}
