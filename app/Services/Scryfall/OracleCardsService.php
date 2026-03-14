<?php

namespace App\Services\Scryfall;

use App\Models\OracleCard;
use App\Services\FormatService;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OracleCardsService
{

    private ScryfallImageService $imageService;
    private FormatService $formatService;
    private BulkdataService $bulkdataService;

    public function __construct()
    {
        $this->imageService = new ScryfallImageService();
        $this->formatService = new FormatService();
        $this->bulkdataService = new BulkdataService();
    }

    /**
     * Truncate the oracle_cards table before a fresh import.
     *
     * Temporarily disables foreign key checks to allow truncation.
     *
     * @return void
     */
    private function preRunCleanup(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        OracleCard::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Log::channel('scryfall')->notice("truncated oracle_cards table.");
    }

    /**
     * Persist a single oracle card to the database.
     *
     * Maps required Scryfall fields and conditionally includes optional
     * ones (mana_cost, layout, colors, color_identity). Image URIs are
     * resolved via ScryfallImageService::getImageUris().
     *
     * @param  array  $card  A single card object from the oracle_cards bulk JSON.
     * @return void
     */
    private function insertCard (array $card): void
    {
        // non nullable values
        $arr = [
            'id' => $card['oracle_id'],
            'name' => $card['name'],
            'collector_number' => $card['collector_number'],
            'type_line' => $card['type_line'],
            'lang' => $card['lang'],
            'cmc' => $card['cmc'],
            'legalities' => $card['legalities'],
            'image_uris' => $this->imageService->getImageUris($card),
            'reserved' => $card['reserved'],
            'game_changer' => $card['game_changer'],
            'scryfall_uri' => $card['scryfall_uri'],
        ];
        // nullable values
        if (array_key_exists('mana_cost', $card)) { $arr['mana_cost'] = $card['mana_cost']; }
        if (array_key_exists('layout', $card)) { $arr['layout'] = $card['layout']; }
        if (array_key_exists('colors', $card) && count($card['colors']) > 0) {
            $arr['colors'] = implode("", $card['colors']);
        }
        if (array_key_exists('color_identity', $card) && count($card['color_identity']) > 0) {
            $arr['color_identity'] = implode("", $card['color_identity']);
        }
        // insert into db
        try {
            $newCard = OracleCard::create($arr);
            if ($newCard->wasRecentlyCreated) {
                Log::channel('scryfall')->debug("Inserted OracleCard \"".$newCard->name."\".");
            }
        } catch (\Exception $e) {
            Log::channel('scryfall')->error("error inserting card ".$card['name'].": ".$e->getMessage());
            Log::channel('scryfall')->error($e->getTraceAsString());
        }
    }

    /**
     * Stream-parse the bulk JSON file and insert each card.
     *
     * Uses JsonParser to avoid loading the entire file into memory,
     * which is critical for the large Scryfall bulk exports.
     *
     * @param  string  $fileName  The filename on the "scryfall-bulk" disk.
     * @return void
     */
    private function traverseJson($fileName): void
    {
        $start = now();
        $count = 0;
        Log::channel('scryfall')->notice("begin traversing oracle cards json.");
        JsonParser::parse(Storage::disk('scryfall-bulk')->get($fileName))->traverse(function (mixed $value, string|int $key, JsonParser $parser) use (&$count) {
            $this->insertCard($value);
            $count++;
        });
        $ms = $start->diffInMilliseconds(now());
        $numCards = number_format($count, 0, ",", ".");
        Log::channel('scryfall')->notice("finished inserting $numCards oracle cards into database in ".$this->formatService->formatMs($ms).".");
    }

    /**
     * Run a full oracle-cards import from Scryfall.
     *
     * Downloads the "oracle_cards" bulk JSON (if not already cached),
     * truncates the existing data, streams through every card to insert
     * it, and cleans up the downloaded file afterwards.
     *
     * @return void
     */
    public function updateOracleCards(): void
    {
        $type = "oracle_cards";
        if (!$this->bulkdataService->prepareJson($type)) {
            Log::channel('scryfall')->error("error preparing '$type.json', aborting.");
            return; // error downloading file, abort
        }
        $this->preRunCleanup();
        $this->traverseJson($type.".json");
        $this->bulkdataService->postRunCleanup($type.".json");
    }

}
