<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class CleanupTempUploads implements ShouldQueue
{
    use Queueable;

    /**
     * Delete all files on the tmp disk that are older than 24 hours.
     */
    public function handle(): void
    {
        $disk = Storage::disk('tmp');
        $cutoff = Carbon::now()->subDay()->getTimestamp();

        foreach ($disk->files() as $file) {
            if ($disk->lastModified($file) < $cutoff) {
                $disk->delete($file);
            }
        }
    }
}
