<?php

namespace App\Http\Controllers\Decks;

use App\Enums\DeckZone;
use App\Http\Controllers\Controller;
use App\Models\Deck;
use App\Models\DeckCard;
use App\Models\DefaultCard;
use App\Services\DeckCardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeckCardController extends Controller
{
    /**
     * Add a card to the deck.
     *
     * Expects `default_card_id` (specific printing) and `zone` (main/side).
     * The oracle card is resolved from the default card automatically.
     */
    public function store(Request $request, Deck $deck): JsonResponse
    {
        abort_unless($deck->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'default_card_id' => ['required', 'uuid', 'exists:default_cards,id'],
            'zone' => ['required', Rule::enum(DeckZone::class)],
        ]);

        $defaultCard = DefaultCard::findOrFail($validated['default_card_id']);

        $deckCard = DeckCard::create([
            'deck_id' => $deck->id,
            'oracle_card_id' => $defaultCard->oracle_id,
            'default_card_id' => $defaultCard->id,
            'zone' => $validated['zone'],
        ]);

        DeckCardService::recalculateColors($deck);

        return response()->json(['id' => $deckCard->id], 201);
    }
}
