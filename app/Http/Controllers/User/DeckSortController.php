<?php

namespace App\Http\Controllers\User;

use App\Enums\DeckSort;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeckSortController extends Controller
{
    /**
     * Update the authenticated user's default deck sort preference.
     *
     * Used by the dashboard settings form; the per-deck localStorage
     * override is handled entirely on the frontend.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'deck_sort_default' => ['required', 'string', 'in:'.implode(',', array_column(DeckSort::cases(), 'value'))],
        ]);

        $user = $request->user();
        $user->deck_sort_default = $validated['deck_sort_default'];
        $user->save();

        $request->session()->flash('message', __('decks.sort.flash.success'));
        $request->session()->flash('type', 'success');

        return back();
    }
}
