<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use Illuminate\Http\RedirectResponse;

class CurrencyController extends Controller
{
    /**
     * Switch the authenticated user's currency preference.
     *
     * @param  string  $currency  The currency code to switch to (e.g. "eur", "usd").
     */
    public function update(string $currency): RedirectResponse
    {
        if (Currency::tryFrom($currency) === null) {
            abort(422);
        }

        $user = request()->user();
        $user->currency = $currency;
        $user->save();

        return back();
    }
}
