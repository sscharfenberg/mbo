<?php

namespace App\Services\Scryfall;

use App\Models\BulkData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class ScryfallBulkdataService
{

    /**
     * @function setup: prune db table
     * @return void
     */
    private function setup(): void
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
        $this->setup();
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
