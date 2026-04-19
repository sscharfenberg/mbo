<?php

namespace App\Http\Controllers\Decks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Decks\StoreDeckCategoryRequest;
use App\Models\Deck;
use App\Models\DeckCard;
use App\Models\DeckCategory;
use Illuminate\Http\RedirectResponse;

class DeckCategoryController extends Controller
{
    /**
     * Create a new custom category for the given deck.
     *
     * Optionally assigns the deck card that was dragged onto the
     * "create group" drop target to the newly created category.
     */
    public function store(StoreDeckCategoryRequest $request, Deck $deck): RedirectResponse
    {
        $validated = $request->validated();

        $category = DeckCategory::create([
            'deck_id' => $deck->id,
            'name' => $validated['group_name'],
        ]);

        if (! empty($validated['card_id'])) {
            DeckCard::where('id', $validated['card_id'])
                ->where('deck_id', $deck->id)
                ->update(['category_id' => $category->id]);
        }

        $request->session()->flash('message', __('decks.category_created', [
            'group' => $category->name,
            'deck' => $deck->name,
        ]));
        $request->session()->flash('type', 'success');

        return redirect()->back();
    }
}
