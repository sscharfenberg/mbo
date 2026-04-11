<?php

namespace App\Http\Controllers\User;

use App\Enums\DeckView;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeckViewController extends Controller
{
    /**
     * Update the authenticated user's default deck view preference.
     *
     * Used by the dashboard settings form; the per-deck localStorage
     * override is handled entirely on the frontend.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'deck_view_default' => ['required', 'string', 'in:'.implode(',', array_column(DeckView::cases(), 'value'))],
        ]);

        $user = $request->user();
        $user->deck_view_default = $validated['deck_view_default'];
        $user->save();

        $request->session()->flash('message', __('decks.view.flash.success'));
        $request->session()->flash('type', 'success');

        return back();
    }
}
