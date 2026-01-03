<?php

namespace App\Console\Commands\Scryfall;

use App\Services\FormatService;
use App\Services\Scryfall\BulkdataService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateBulkData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scryfall:bulk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get bulk data from scryfall';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fd = new FormatService();
        $bds = new BulkdataService();
        $start = now();
        $this->info("artisan command 'scryfall:bulk' started.");
        Log::channel('scryfall')->info("=======================================================");
        Log::channel('scryfall')->info("artisan command 'scryfall:bulk' started.");
        Log::channel('scryfall')->info("=======================================================");
        $bds->getBulkMetadata();
        $ms = $start->diffInMilliseconds(now());
        Log::channel('scryfall')->info("=======================================================");
        Log::channel('scryfall')->info("artisan command 'scryfall:bulk' finished in ".$fd->formatMs($ms).".");
        Log::channel('scryfall')->info("=======================================================");
        $this->info("artisan command 'scryfall:bulk' finished in ".$fd->formatMs($ms).".");
    }
}
