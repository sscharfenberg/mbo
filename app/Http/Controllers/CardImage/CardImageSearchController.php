<?php

namespace App\Http\Controllers\CardImage;

use App\Http\Controllers\Controller;
use App\Models\DefaultCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CardImageSearchController extends Controller
{

    /**
     * Search default_cards by name (and optionally set code) and return id + first art crop.
     *
     * Supports "set:xxx" tokens in the query string to filter by set code, e.g.:
     *   "sol ring set:lea"  →  name LIKE %sol% AND name LIKE %ring% AND set.code = 'lea'
     *   "set:lea"           →  all cards from set 'lea'
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->query('q', ''));

        // Extract "set:xxx" tokens from the query string.
        $setCode = null;
        $nameQ = preg_replace_callback(
            '/\bset:(\S+)/i',
            function (array $m) use (&$setCode): string {
                $setCode = strtolower($m[1]);
                return '';
            },
            $q
        );
        $nameQ = trim((string) preg_replace('/\s+/', ' ', $nameQ));

        // Require at least a set filter or a name query of ≥ 2 characters.
        if (!$setCode && mb_strlen($nameQ) < 2) {
            return response()->json([]);
        }

        $query = DefaultCard::whereNotNull('art_crop');

        if ($setCode) {
            $query->whereHas('set', fn ($q) => $q->where('code', $setCode));
        }

        $segments = array_filter(explode(' ', $nameQ), fn (string $s) => $s !== '');
        foreach ($segments as $segment) {
            $query->where('name', 'like', "%$segment%");
        }

        $cards = $query->select('id', 'name', 'art_crop', 'set_id', 'artist_id')
            ->with('set:id,name,code', 'artist:id,name')
            ->orderBy('name')
            ->get()
            ->map(fn (DefaultCard $card) => [
                'id'       => $card->id,
                'name'     => $card->name,
                'art_crop' => $card->art_crop,
                'artist'   => $card->artist?->name,
                'set'      => $card->set ? [
                    'name' => $card->set->name,
                    'code' => $card->set->code,
                ] : null,
            ]);

        return response()->json($cards);
    }

}