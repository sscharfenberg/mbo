<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ZxcvbnPhp\Zxcvbn;

class EntropyController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function calculate(Request $request): JsonResponse {
        $zxcvbn = new Zxcvbn();
        $entropy = $zxcvbn->passwordStrength($request->get('p'));
        return response()->json([
            'guesses' => $entropy['guesses'],
            'score' => $entropy['score'],
            'time' => $entropy['crack_times_seconds']['offline_slow_hashing_1e4_per_second'],
        ]);
    }
}
