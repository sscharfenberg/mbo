<?php

namespace App\Services\Scryfall;

use App\Models\DefaultCard;
use App\Models\OracleCard;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Resolves Scryfall image URLs to local filesystem paths.
 *
 * After ImageDownloadService has downloaded images to disk, this service
 * updates database records to point at the local cache instead of Scryfall.
 *
 * Separation of concerns:
 *   - ImageDownloadService  → filesystem (downloading images to disk)
 *   - ResolveImagePathsService → database (URL → local path resolution)
 */
class ResolveImagePathsService
{

    private ScryfallImageService $imageService;

    public function __construct()
    {
        $this->imageService = new ScryfallImageService();
    }

    /**
     * Update art_crop paths for default cards that still point to Scryfall URLs
     * but already have a cached image on disk.
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
     * Update card_image_0 / card_image_1 for default cards that still have
     * Scryfall URLs but already have cached images on disk.
     *
     * @return int  Number of resolved image paths.
     */
    public function resolveCardImagePaths(): int
    {
        $resolved = 0;

        foreach ([0, 1] as $index) {
            $column = "card_image_$index";

            DefaultCard::whereNotNull($column)
                ->where($column, 'like', 'https://%')
                ->with('set:id,code')
                ->chunkById(500, function ($cards) use (&$resolved, $column, $index) {
                    foreach ($cards as $card) {
                        $setCode = $card->set?->code;
                        if (!$setCode) {
                            continue;
                        }

                        $timestamp = $this->imageService->parseTimestamp($card->$column);
                        $filename = $this->imageService->buildCardImageFilename($card->id, $timestamp, $index);
                        $diskPath = "$setCode/$filename";

                        if (Storage::disk('card-images')->exists($diskPath)) {
                            $card->update([$column => "/card-images/$diskPath"]);
                            $resolved++;
                        }
                    }
                });
        }

        if ($resolved > 0) {
            Log::channel('scryfall')->notice("resolved $resolved card image paths to local cache.");
        }

        return $resolved;
    }

    /**
     * Copy resolved card image paths from default_cards to their oracle_cards.
     *
     * For each oracle card that still has Scryfall URLs, finds a matching
     * default card (via oracle_id) that already has a resolved local path
     * and copies it over.
     *
     * @return int  Number of oracle cards updated.
     */
    public function resolveOracleCardImagePaths(): int
    {
        $resolved = 0;

        OracleCard::whereNotNull('card_image_0')
            ->where('card_image_0', 'like', 'https://%')
            ->chunkById(500, function ($oracleCards) use (&$resolved) {
                foreach ($oracleCards as $oracleCard) {
                    $defaultCard = DefaultCard::where('oracle_id', $oracleCard->id)
                        ->whereNotNull('card_image_0')
                        ->where('card_image_0', 'not like', 'https://%')
                        ->first();

                    if (!$defaultCard) {
                        continue;
                    }

                    $update = ['card_image_0' => $defaultCard->card_image_0];
                    if ($defaultCard->card_image_1 !== null) {
                        $update['card_image_1'] = $defaultCard->card_image_1;
                    }

                    $oracleCard->update($update);
                    $resolved++;
                }
            });

        if ($resolved > 0) {
            Log::channel('scryfall')->notice("resolved $resolved oracle card image paths from default cards.");
        }

        return $resolved;
    }

}