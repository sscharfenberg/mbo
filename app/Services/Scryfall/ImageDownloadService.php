<?php

namespace App\Services\Scryfall;

use App\Models\DefaultCard;
use App\Services\FormatService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageDownloadService
{

    private ScryfallImageService $imageService;
    private FormatService $formatService;

    public function __construct()
    {
        $this->imageService = new ScryfallImageService();
        $this->formatService = new FormatService();
    }

    /**
     * Download all missing or outdated art crop images from Scryfall.
     *
     * Walks every default_card that still has a Scryfall URL in art_crop,
     * checks the local disk for a cached version with the same timestamp,
     * downloads if missing or outdated, and updates the DB to the local path.
     *
     * @return void
     */
    public function downloadArtCrops(): void
    {
        $start = now();
        $downloaded = 0;
        $skipped = 0;
        $failed = 0;

        Log::channel('scryfall')->notice("begin downloading art crop images.");

        DefaultCard::whereNotNull('art_crop')
            ->where('art_crop', 'like', 'https://%')
            ->with('set:id,code')
            ->chunkById(200, function ($cards) use (&$downloaded, &$skipped, &$failed) {
                foreach ($cards as $card) {
                    $result = $this->processArtCrop($card);
                    match ($result) {
                        'downloaded' => $downloaded++,
                        'skipped' => $skipped++,
                        'failed' => $failed++,
                    };
                }
            });

        $ms = $start->diffInMilliseconds(now());
        $total = $downloaded + $skipped + $failed;
        Log::channel('scryfall')->notice(
            "finished art crop download: $total cards processed "
            . "($downloaded downloaded, $skipped skipped, $failed failed) "
            . "in " . $this->formatService->formatMs($ms) . "."
        );
    }

    /**
     * Process a single card's art crop image.
     *
     * Checks if the local disk already has the correct version (by timestamp),
     * downloads from Scryfall if not, cleans up old versions, and updates
     * the art_crop column to the local public URL.
     *
     * @param  DefaultCard  $card
     * @return string  'downloaded', 'skipped', or 'failed'
     */
    private function processArtCrop(DefaultCard $card): string
    {
        $scryfallUrl = $card->art_crop;
        $timestamp = $this->imageService->parseTimestamp($scryfallUrl);
        $setCode = $card->set?->code;

        if (!$setCode) {
            Log::channel('scryfall')->warning("card {$card->id} ({$card->name}) has no set, skipping art crop.");
            return 'failed';
        }

        $filename = $this->imageService->buildArtCropFilename($card->id, $timestamp);
        $diskPath = "$setCode/$filename";

        // Already cached with same timestamp — just update URL to local path
        if (Storage::disk('art-crops')->exists($diskPath)) {
            $card->update(['art_crop' => "/art-crops/$diskPath"]);
            return 'skipped';
        }

        // Clean up old versions of this card's art crop
        $this->cleanupOldVersions($setCode, $card->id);

        // Download from Scryfall
        try {
            $response = Http::get($scryfallUrl);
            if ($response->successful()) {
                Storage::disk('art-crops')->put($diskPath, $response->body());
                $card->update(['art_crop' => "/art-crops/$diskPath"]);
                Log::channel('scryfall')->debug("downloaded art crop for [{$setCode}] {$card->name}.");
                return 'downloaded';
            }
            Log::channel('scryfall')->error("failed to download art crop for {$card->name}: HTTP {$response->status()}");
            return 'failed';
        } catch (\Exception $e) {
            Log::channel('scryfall')->error("failed to download art crop for {$card->name}: {$e->getMessage()}");
            return 'failed';
        }
    }

    /**
     * Delete any previously cached art crop versions for a given card.
     *
     * Finds files matching the card UUID pattern in the set directory
     * and removes them before downloading the new version.
     *
     * @param  string  $setCode  The set code directory.
     * @param  string  $uuid     The card UUID.
     * @return void
     */
    private function cleanupOldVersions(string $setCode, string $uuid): void
    {
        $files = Storage::disk('art-crops')->files($setCode);
        foreach ($files as $file) {
            $basename = basename($file);
            if ($basename === "$uuid.jpg" || str_starts_with($basename, "$uuid--")) {
                Storage::disk('art-crops')->delete($file);
            }
        }
    }

}