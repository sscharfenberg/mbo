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
     * Oracle-level card search for the deck (quick-add input).
     *
     * Returns distinct oracle cards with their newest printing auto-resolved.
     * Any `set:` / `cn:` tokens in the query are ignored — this endpoint is
     * for picking a card by name, not by printing.
     */
    public function oracle(Request $request, Deck $deck): JsonResponse
    {
        abort_unless($deck->user_id === $request->user()->id, 403);

        $results = DeckCardSearchService::searchOracleForDeck(
            $deck,
            trim((string) $request->query('q', ''))
        );

        return response()->json($results);
    }

    /**
     * Printing-level card search for the deck (full card-add modal).
     *
     * Honors `set:` / `cn:` tokens so the user can pin results to a specific
     * printing, and can return multiple printings of the same oracle card.
     * When `include_non_legal=1`, the format-legality filter is dropped but
     * color identity is still enforced — the Rule 0 escape hatch.
     */
    public function printings(Request $request, Deck $deck): JsonResponse
    {
        abort_unless($deck->user_id === $request->user()->id, 403);

        $results = DeckCardSearchService::searchPrintingsForDeck(
            $deck,
            trim((string) $request->query('q', '')),
            $request->boolean('include_non_legal'),
        );

        return response()->json($results);
    }
}
