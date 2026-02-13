<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocaleController extends Controller
{
    /**
     * Update the application locale.
     */
    public function update(string $locale): JsonResponse
    {
        $availableLocales = config('mbo.app.supportedLocales');
        if (!in_array($locale, $availableLocales)) {
            return response()->json([], 422);
        }

        // update authenticated user locale, if available
        $userId = Auth::id();
        if ($userId) {
            $user = User::where('id', $userId)->first();
            $user->locale = $locale;
            $user->save();
        }
        else // set session locale
        {
            session(['locale' => $locale]);
        }

        app()->setLocale($locale);

        return response()->json(['locale' => $locale]);
    }
}
