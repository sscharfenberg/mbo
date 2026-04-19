<?php

namespace App\Http\Controllers\Decks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Decks\ModifyDeckCardRequest;
use App\Http\Requests\Decks\StoreDeckCardRequest;
use App\Http\Requests\Decks\UpdateDeckCardCategoryRequest;
use App\Http\Requests\Decks\UpdateDeckCardQuantityRequest;
use App\Models\Deck;
use App\Models\DeckCard;
use App\Models\DefaultCard;
use App\Services\DeckCardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class DeckCardController extends Controller
{
    /**
     * Add a card to the deck.
     *
     * Expects `default_card_id` (specific printing) and `zone` (main/side).
     * The oracle card is resolved from the default card automatically.
     */
    public function store(StoreDeckCardRequest $request, Deck $deck): JsonResponse
    {
        $validated = $request->validated();

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

    /**
     * Change a deck card's quantity by a signed delta.
     *
     * Positive deltas are validated against format rules (singleton, max copies,
     * deck size). When the resulting quantity reaches zero or below the card row
     * is deleted entirely.
     */
    public function updateQuantity(UpdateDeckCardQuantityRequest $request, Deck $deck, DeckCard $deckCard): JsonResponse
    {
        $delta = $request->validated()['delta'];
        $newQuantity = $deckCard->quantity + $delta;

        if ($newQuantity <= 0) {
            $deckCard->delete();
            DeckCardService::recalculateColors($deck);

            return response()->json(['deleted' => true]);
        }

        if ($delta > 0) {
            $oracleCard = $deckCard->oracleCard;
            $currentDeckSize = $deck->deckCards()->sum('quantity');

            $result = $deck->format->rules()->canAddCopy(
                $oracleCard,
                $newQuantity - 1,
                $currentDeckSize + $delta - 1,
            );

            abort_unless($result->allowed, 422);
        }

        $deckCard->update(['quantity' => $newQuantity]);

        return response()->json(['quantity' => $newQuantity]);
    }

    /**
     * Remove a deck card entirely, regardless of quantity.
     */
    public function destroy(ModifyDeckCardRequest $request, Deck $deck, DeckCard $deckCard): JsonResponse
    {
        $deckCard->delete();

        DeckCardService::recalculateColors($deck);

        return response()->json(status: 204);
    }

    /**
     * Update a deck card's category assignment.
     *
     * Accepts a nullable `category_id` — null removes the card from any
     * custom category, returning it to the default type-based grouping.
     */
    public function updateCategory(UpdateDeckCardCategoryRequest $request, Deck $deck, DeckCard $deckCard): RedirectResponse
    {
        $deckCard->update($request->validated());

        return redirect()->back();
    }
}
