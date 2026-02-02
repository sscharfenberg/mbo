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
        $weak = $zxcvbn->passwordStrength($request->get('p'));
        dd($weak);
        return response()->json(['p' => $request->get('p')]);
    }
}
