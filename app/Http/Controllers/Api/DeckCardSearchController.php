<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deck;
use App\Services\DeckCardSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeckCardSearchController extends Controller
{
    /**
     * Search cards eligible to be added to a deck.
     *
     * Filters by the deck's format legality and, for formats that enforce it,
     * the deck's color identity. Supports `set:xxx` / `cn:xxx` tokens via
     * `CardSearchParser` — presence of either switches from the oracle
     * autocomplete path to the printing picker path.
     *
     * Delegates all query building to {@see DeckCardSearchService}.
     */
    public function search(Request $request, Deck $deck): JsonResponse
    {
        abort_unless($deck->user_id === $request->user()->id, 403);

        $results = DeckCardSearchService::searchCardForDeck(
            $deck,
            trim((string) $request->query('q', ''))
        );

        return response()->json($results);
    }
}
