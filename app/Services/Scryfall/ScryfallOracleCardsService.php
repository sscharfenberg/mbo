<?php

namespace App\Services\Scryfall;

use App\Models\BulkData;
use App\Services\FormatService;
use Illuminate\Http\Client\ConnectionException;
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
        if (env('APP_ENV') == 'production') {
            Storage::disk('scryfall-bulk')->delete($fileName);
            Log::channel('scryfall')->info("deleted '$fileName' from disk 'scryfall-bulk'.");
        }
    }


    private function traverseJson($fileName): void
    {
        JsonParser::parse(Storage::disk('scryfall-bulk')->get($fileName))->traverse(function (mixed $value, string|int $key, JsonParser $parser) {
            dd($value);
        });
    }


    /**
     * @function download json file, and place it into "scryfall-bulk" disk
     * @param string $fileName
     * @return bool
     * @throws ConnectionException
     */
    private function downloadJson(string $fileName): bool
    {
        $f = new FormatService();
        $start = now();
        $bd = BulkData::where('type', '=', 'oracle_cards')->first();
        $uri = $bd->download_uri;
        Log::channel('scryfall')->notice('Starting download for oracle cards from url '.$uri);
        $response = Http::withHeaders(config('mbo.scryfall.header'))->get($uri);
        if ($response->failed()) {
            Log::channel('scryfall')->critical("error calling oracle uri '$uri' from scryfall: ".$response->body());
            return false;
        }
        Storage::disk('scryfall-bulk')->put($fileName, $response->body());
        $realSize = Storage::disk('scryfall-bulk')->size($fileName);
        if ($realSize != $bd->size) {
            Log::channel('scryfall')->error("downloaded size for '$fileName' ($realSize) differs from expected size ($bd->size).");
            return false;
        }
        Log::channel('scryfall')->debug("downloaded '$fileName' from scryfall to disk 'scryfall-bulk'.");
        Log::channel('scryfall')->debug("filesize for '$fileName' ($realSize = ".$f->formatBytes($realSize).") as expected.");
        $ms = $start->diffInMilliseconds(now());
        Log::channel('scryfall')->notice("downloaded oracle_cards.json in ".$f->formatMs($ms).".");
        return true;
    }

    /**
     * @function
     * @return void
     * @throws ConnectionException
     */
    public function updateOracleCards(): void
    {
        $fileName = "oracle_cards.json";
        if (Storage::disk('scryfall-bulk')->missing($fileName)) {
            if (!$this->downloadJson($fileName)) {
                Log::channel('scryfall')->error("error downloading oracle cards, aborting.");
                return; // error downloading file, abort
            }
        }
        $this->traverseJson($fileName);
        $this->cleanup($fileName);
    }

}
