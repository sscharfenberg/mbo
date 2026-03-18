<?php

namespace App\Services\Scryfall;

use App\Models\DefaultCard;
use App\Models\OracleCard;
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

    /**
     * Cached directory listings indexed by "disk:setCode".
     * Each entry maps UUID to an array of relative disk paths for that card.
     *
     * @var array<string, array<string, string[]>>
     */
    private array $fileIndex = [];

    /**
     * Build or retrieve the file index for a given disk + set directory.
     *
     * Lists all files once per set per disk, then indexes them by UUID prefix
     * for O(1) lookups. The index maps each UUID to all its matching files.
     *
     * @param  string  $disk     The Storage disk name.
     * @param  string  $setCode  The set code directory.
     * @return array<string, string[]>  UUID → list of relative disk paths.
     */
    private function getFileIndex(string $disk, string $setCode): array
    {
        $cacheKey = "$disk:$setCode";
        if (isset($this->fileIndex[$cacheKey])) {
            return $this->fileIndex[$cacheKey];
        }

        $index = [];
        foreach (Storage::disk($disk)->files($setCode) as $file) {
            $basename = basename($file);
            // Extract UUID: everything before the first "--" or ".jpg"
            $uuid = preg_replace('/(--.*)$|\.jpg$/', '', $basename);
            if ($uuid) {
                $index[$uuid][] = $file;
            }
        }

        $this->fileIndex[$cacheKey] = $index;
        return $index;
    }

    /**
     * Find a file on disk matching a UUID prefix for an art crop.
     *
     * Art crop filenames: {uuid}--{timestamp}.jpg or {uuid}.jpg
     * A card has exactly one art crop, so any file starting with the UUID is a match.
     *
     * @param  string  $disk     The Storage disk name.
     * @param  string  $setCode  The set code directory.
     * @param  string  $uuid     The card UUID.
     * @return string|null  The relative disk path if found, null otherwise.
     */
    private function findArtCropOnDisk(string $disk, string $setCode, string $uuid): ?string
    {
        $index = $this->getFileIndex($disk, $setCode);
        return $index[$uuid][0] ?? null;
    }

    /**
     * Find a file on disk matching a UUID and face index for a card image.
     *
     * Card image filenames: {uuid}--{timestamp}--{faceIndex}.jpg or {uuid}--{faceIndex}.jpg
     * Must match the specific face index suffix to avoid returning face 0's
     * file when looking for face 1 (and vice versa).
     *
     * @param  string  $disk       The Storage disk name.
     * @param  string  $setCode    The set code directory.
     * @param  string  $uuid       The card UUID.
     * @param  int     $faceIndex  The face index (0 = front, 1 = back).
     * @return string|null  The relative disk path if found, null otherwise.
     */
    private function findCardImageOnDisk(string $disk, string $setCode, string $uuid, int $faceIndex): ?string
    {
        $index = $this->getFileIndex($disk, $setCode);
        $files = $index[$uuid] ?? [];
        $suffix = "--$faceIndex.jpg";
        foreach ($files as $file) {
            if (str_ends_with(basename($file), $suffix)) {
                return $file;
            }
        }
        return null;
    }

    /**
     * Update art_crop paths for default cards that still point to Scryfall URLs
     * but already have a cached image on disk.
     *
     * Instead of building the expected filename from the URL timestamp, scans
     * the disk for any file matching the card UUID. This makes resolution
     * resilient to timestamp mismatches between the bulk data and disk.
     *
     * @return int  Number of paths resolved.
     */
    public function resolveArtCropPaths(): int
    {
        $this->fileIndex = [];
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

                    $diskPath = $this->findArtCropOnDisk('art-crops', $setCode, $card->id);
                    if ($diskPath) {
                        $card->update(['art_crop' => "/art-crops/$diskPath"]);
                        $resolved++;
                    }
                }
            });

        return $resolved;
    }

    /**
     * Update card_image_0 / card_image_1 for default cards that still have
     * Scryfall URLs but already have cached images on disk.
     *
     * Scans the disk for a file matching the card UUID and the specific face
     * index suffix, so face 0 and face 1 files are never confused.
     *
     * @return int  Number of resolved image paths.
     */
    public function resolveCardImagePaths(): int
    {
        $this->fileIndex = [];
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

                        $diskPath = $this->findCardImageOnDisk('card-images', $setCode, $card->id, $index);
                        if ($diskPath) {
                            $card->update([$column => "/card-images/$diskPath"]);
                            $resolved++;
                        }
                    }
                });
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

        return $resolved;
    }

}