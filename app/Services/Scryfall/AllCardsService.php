<?php

namespace App\Services\Scryfall;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\Scryfall\BulkdataService;

class AllCardsService
{

    /**
     * @function download and analyze bulk data from json and update database for "oracle_cards"
     * @return void
     */
    public function updateAllCards(): void
    {
        $type = "all_cards";
        $bds = new BulkDataService();
        if (Storage::disk('scryfall-bulk')->missing($type.".json")) {
            if (!$bds->downloadJson($type)) {
                Log::channel('scryfall')->error("error downloading '$type.json', aborting.");
                return; // error downloading file, abort
            }
        }
        dd("download successful");
    }

}
