<?php

namespace App\Services\Scryfall;

use App\Models\BulkData;
use App\Services\FormatService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Support\Facades\Storage;

class ScryfallOracleCardsService
{

    /**
     * @function cleanup after processing.
     * @param $fileName
     * @return void
     */
    private function cleanup ($fileName): void
    {
        Storage::disk('scryfall-bulk')->delete($fileName);
        Log::channel('scryfall')->info("deleted '$fileName' from disk 'scryfall-bulk'.");
    }


    /**
     * @function
     * @return void
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function updateOracleCards(): void
    {
        $start = now();
        $f = new FormatService();
        $bd = BulkData::where('type', '=', 'oracle_cards')->first();
        $uri = $bd->download_uri;
        $fileName = $bd->type.".json";
        Log::channel('scryfall')->info('Starting download for oracle cards: '.$uri);
        $response = Http::withHeaders(config('binder.scryfall.header'))
            ->get($uri);
        if ($response->successful()) {
            Storage::disk('scryfall-bulk')->put($fileName, $response->body());
            $realSize = Storage::disk('scryfall-bulk')->size($fileName);
            if ($realSize == $bd->size) {
                Log::channel('scryfall')->debug("downloaded '$fileName' from scryfall to disk 'scryfall-bulk'.");
                Log::channel('scryfall')->debug("filesize for '$fileName' ($realSize = ".$f->formatBytes($realSize).") as expected.");
            } else {
                Log::channel('scryfall')->error("downloaded size for '$fileName' ($realSize) differs from expected size ($bd->size).");
            }
            // traverse local json
        } else {
            Log::channel('scryfall')->error("error calling icon uri '$uri' from scryfall: ".$response->body());
        }

//        JsonParser::parse($bd->download_uri)->traverse(function (mixed $value, string|int $key, JsonParser $parser) {
//            // lazily load one key and value at a time, we can also access the parser if needed
//            dd($value);
//        });

        $this->cleanup($fileName);
    }

}
