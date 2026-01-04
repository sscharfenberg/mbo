<?php

namespace App\Services\Scryfall;

use App\Models\BulkData;
use App\Services\FormatService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class BulkdataService
{

    /**
     * @function download json file, and place it into "scryfall-bulk" disk
     * @param string $type
     * @return bool
     */
    public function prepareJson(string $type): bool
    {
        $fileName = $type.".json";
        $f = new FormatService();
        if (Storage::disk('scryfall-bulk')->exists($fileName)) {
            Log::channel('scryfall')->notice("JSON file '$fileName' already exists in disk 'scryfall-bulk'.");
            return true;
        }
        $start = now();
        $bd = BulkData::where('type', '=', $type)->first();
        $uri = $bd->download_uri;
        try {
            Log::channel('scryfall')->notice("starting download of '$fileName' from scryfall.");
            $response = Http::withHeaders(config('mbo.scryfall.header'))
                ->timeout(-1) // disable timeouts since we want to download large files.
                ->get($uri);
            if ($response->failed()) {
                Log::channel('scryfall')->critical("error calling oracle uri '$uri' from scryfall: ".$response->body());
                return false;
            } else {
                Storage::disk('scryfall-bulk')->put($fileName, $response->body());
                $realSize = Storage::disk('scryfall-bulk')->size($fileName);
                $realSizeFormatted = number_format($realSize, 0, ',', '.');
                if ($realSize != $bd->size) {
                    Log::channel('scryfall')->error("downloaded size for '$fileName' ($realSize) differs from expected size ($bd->size).");
                    return false;
                }
                Log::channel('scryfall')->debug("downloaded '$fileName' from scryfall to disk 'scryfall-bulk'.");
                Log::channel('scryfall')->debug("filesize for '$fileName' ($realSizeFormatted = ".$f->formatBytes($realSize).") as expected.");
                $ms = $start->diffInMilliseconds(now());
                Log::channel('scryfall')->notice("downloaded '$fileName' in ".$f->formatMs($ms).".");
                return true;
            }
        } catch (\Exception $e) {
            Log::channel('scryfall')->error("error downloading '$fileName': ".$e->getMessage());
            Log::channel('scryfall')->error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * @function cleanup after processing.
     * @param $fileName
     * @return void
     */
    public function postRunCleanup ($fileName): void
    {
        if (env('APP_ENV') == 'production') {
            Storage::disk('scryfall-bulk')->delete($fileName);
            Log::channel('scryfall')->notice("deleted '$fileName' from disk 'scryfall-bulk'.");
        }
    }

    /**
     * @function setup: prune db table
     * @return void
     */
    private function preRunCleanup(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        BulkData::truncate();
        Log::channel('scryfall')->debug("table 'bulk_data' truncated.");
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * @function
     * @param array $bulk
     * @return void
     */
    private function insertBulkData(array $bulk): void
    {
        $arr = [
            'id' => $bulk['id'],
            'type' => $bulk['type'],
            'updated_at' => $bulk['updated_at'],
            'uri' => $bulk['uri'],
            'name' => $bulk['name'],
            'description' => $bulk['description'],
            'size' => $bulk['size'],
            'download_uri' => $bulk['download_uri'],
            'content_type' => $bulk['content_type'],
            'content_encoding' => $bulk['content_encoding'],
        ];
        $newData = BulkData::create($arr);
        if ($newData->wasRecentlyCreated) {
            Log::channel('scryfall')->debug("created bulkdata entry '$newData->name' last updated @ $newData->updated_at.");
        }
    }

    /**
     * @function get bulk metadata from scryfall.
     * @return void
     */
    public function getBulkMetadata(): void
    {
        Log::channel('scryfall')->debug('Updating bulk metadata from scryfall.');
        $this->preRunCleanup();
        try {
            $response = Http::accept('application/json')
                ->get('https://api.scryfall.com/bulk-data');
            if ($response->successful()) {
                $json = $response->json();
                Log::channel('scryfall')->debug('API request to scryfall successful.: '.json_encode($json, JSON_PRETTY_PRINT));
                $scryfallData = collect($json['data']);
                $scryfallData->each(function ($item) {
                    $this->insertBulkData($item);
                });
            }
            if ($response->failed()) {
                Log::channel('scryfall')->error('Failed scryfall api request: '.$response->body());
            }
        } catch (\Exception $exception) {
            Log::channel('scryfall')->error($exception->getMessage());
        }
    }

}
