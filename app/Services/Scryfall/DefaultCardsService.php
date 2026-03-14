<?php

namespace App\Services\Scryfall;

use App\Models\DefaultCard;
use App\Services\FormatService;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Handles all database operations for the default_cards table.
 *
 * This service owns the DB writes for default cards. During import, it resolves
 * art_crop URLs to local paths when a cached image already exists on disk,
 * otherwise stores the Scryfall URL for later download by ImageDownloadService.
 *
 * Separation of concerns:
 *   - DefaultCardsService → database (import, path resolution)
 *   - ImageDownloadService → filesystem (downloading images to disk)
 */
class DefaultCardsService
{

    private ScryfallImageService $imageService;
    private ArtistsService $artistsService;
    private FormatService $formatService;
    private BulkdataService $bulkdataService;

    public function __construct()
    {
        $this->imageService = new ScryfallImageService();
        $this->artistsService = new ArtistsService();
        $this->formatService = new FormatService();
        $this->bulkdataService = new BulkdataService();
    }

    /**
     * Truncate the default_cards and artists tables before a fresh import.
     *
     * Temporarily disables foreign key checks to allow truncation.
     *
     * @return void
     */
    private function preRunCleanup(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DefaultCard::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Log::channel('scryfall')->notice("truncated default_cards table.");
        $this->artistsService->truncate();
    }

    /**
     * Resolve the art crop value for a card.
     *
     * If a locally cached image exists on disk with a matching timestamp,
     * returns the local public path. Otherwise returns the Scryfall URL
     * so that ImageDownloadService can download it later.
     *
     * @param  array  $card  A single card object from the default_cards bulk JSON.
     * @return string|null   Local path or Scryfall URL, null if no art crop available.
     */
    private function resolveArtCrop(array $card): ?string
    {
        $scryfallUrl = $this->imageService->getArtCrop($card);
        if ($scryfallUrl === null) {
            return null;
        }

        $setCode = $card['set'] ?? null;
        if ($setCode === null) {
            return $scryfallUrl;
        }

        $timestamp = $this->imageService->parseTimestamp($scryfallUrl);
        $filename = $this->imageService->buildArtCropFilename($card['id'], $timestamp);
        $diskPath = "$setCode/$filename";

        if (Storage::disk('art-crops')->exists($diskPath)) {
            return "/art-crops/$diskPath";
        }

        return $scryfallUrl;
    }

    /**
     * Persist a single default card to the database.
     *
     * Maps required Scryfall fields (prices, finishes, rarity, etc.) and
     * conditionally includes optional ones (oracle_id, layout, artist_id).
     * Art crop is resolved to a local path if cached, otherwise the Scryfall URL is stored.
     *
     * @param  array  $card  A single card object from the default_cards bulk JSON.
     * @return void
     */
    private function insertCard(array $card): void
    {
        // non nullable values
        $arr = [
            'id' => $card['id'],
            'name' => $card['name'],
            'collector_number' => $card['collector_number'],
            'lang' => $card['lang'],
            'image_uris' => $this->imageService->getImageUris($card),
            'art_crop' => $this->resolveArtCrop($card),
            'finishes' => $card['finishes'],
            'games' => $card['games'],
            'prices' => $card['prices'],
            'digital' => $card['digital'],
            'rarity' => $card['rarity'],
            'set_id' => $card['set_id'],
            'artist_id' => $this->artistsService->resolveArtistId($card['artist'] ?? null),
        ];
        // nullable values
        if (array_key_exists('oracle_id', $card)) { $arr['oracle_id'] = $card['oracle_id']; }
        if (array_key_exists('layout', $card)) { $arr['layout'] = $card['layout']; }
        // insert into db
        try {
            DefaultCard::create($arr);
        } catch (\Exception $e) {
            Log::channel('scryfall')->error("error inserting DefaultCard [".strtoupper($card['set'])."] ".$card['name'].": ".$e->getMessage());
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
        Log::channel('scryfall')->notice("begin traversing $fileName.");
        JsonParser::parse(Storage::disk('scryfall-bulk')->get($fileName))->traverse(function (mixed $value, string|int $key, JsonParser $parser) use (&$count) {
            $this->insertCard($value);
            $count++;
        });
        $ms = $start->diffInMilliseconds(now());
        $numCards = number_format($count, 0, ",", ".");
        Log::channel('scryfall')->notice("finished inserting $numCards cards into database in ".$this->formatService->formatMs($ms).".");
    }

    /**
     * Update art_crop paths for cards that still point to Scryfall URLs
     * but already have a cached image on disk.
     *
     * This is a lightweight post-download pass — no JSON parsing or API calls,
     * just a DB query with disk checks. Intended to be called after
     * ImageDownloadService has finished downloading images.
     *
     * @return int  Number of paths resolved.
     */
    public function resolveArtCropPaths(): int
    {
        $resolved = 0;

        DefaultCard::whereNotNull('art_crop')
            ->where('art_crop', 'like', 'https://%')
            ->with('set:id,code')
            ->chunkById(500, function ($cards) use (&$resolved) {
                foreach ($cards as $card) {
                    $setCode = $card->set?->code;
                    if (!$setCode) {
                        continue;
                    }

                    $timestamp = $this->imageService->parseTimestamp($card->art_crop);
                    $filename = $this->imageService->buildArtCropFilename($card->id, $timestamp);
                    $diskPath = "$setCode/$filename";

                    if (Storage::disk('art-crops')->exists($diskPath)) {
                        $card->update(['art_crop' => "/art-crops/$diskPath"]);
                        $resolved++;
                    }
                }
            });

        if ($resolved > 0) {
            Log::channel('scryfall')->notice("resolved $resolved art crop paths to local cache.");
        }

        return $resolved;
    }

    /**
     * Run a full default-cards import from Scryfall.
     *
     * Downloads the "default_cards" bulk JSON (if not already cached),
     * truncates the existing data, streams through every card to insert
     * it, and cleans up the downloaded file afterwards.
     *
     * @return void
     */
    public function updateAllCards(): void
    {
        $type = "default_cards";
        if (!$this->bulkdataService->prepareJson($type)) {
            Log::channel('scryfall')->error("error preparing '$type.json', aborting.");
            return; // error downloading file, abort
        }
        $this->preRunCleanup();
        $this->traverseJson($type.".json");
        $this->bulkdataService->postRunCleanup($type.".json");
    }

}
