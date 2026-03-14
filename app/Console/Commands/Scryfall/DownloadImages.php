<?php

namespace App\Console\Commands\Scryfall;

use App\Services\FormatService;
use App\Services\Scryfall\DefaultCardsService;
use App\Services\Scryfall\ImageDownloadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DownloadImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scryfall:images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download art crop images from Scryfall and cache them locally.';

    private FormatService $formatService;
    private ImageDownloadService $imageDownloadService;
    private DefaultCardsService $defaultCardsService;

    public function __construct()
    {
        parent::__construct();
        $this->formatService = new FormatService();
        $this->imageDownloadService = new ImageDownloadService();
        $this->defaultCardsService = new DefaultCardsService();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $start = now();
        $this->info("artisan command 'scryfall:images' started.");
        Log::channel('scryfall')->info("=======================================================");
        Log::channel('scryfall')->info("artisan command 'scryfall:images' started.");
        Log::channel('scryfall')->info("=======================================================");
        // download missing images to disk
        $this->imageDownloadService->downloadArtCrops();
        // resolve Scryfall URLs → local paths for newly downloaded images
        $resolved = $this->defaultCardsService->resolveArtCropPaths();
        $this->info("resolved $resolved art crop paths to local cache.");
        $ms = $start->diffInMilliseconds(now());
        Log::channel('scryfall')->info("=======================================================");
        Log::channel('scryfall')->info("artisan command 'scryfall:images' finished in ".$this->formatService->formatMs($ms).".");
        Log::channel('scryfall')->info("=======================================================");
        $this->info("artisan command 'scryfall:images' finished in ".$this->formatService->formatMs($ms).".");
    }
}