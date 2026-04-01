<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupTempUploads implements ShouldQueue
{
    use Queueable;

    /**
     * Delete all files on the tmp disk that are older than 24 hours.
     */
    public function handle(): void
    {
        $log = Log::channel('schedule');
        $start = microtime(true);

        $log->info('CleanupTempUploads: starting');

        $disk = Storage::disk('tmp');
        $cutoff = Carbon::now()->subDay()->getTimestamp();

        $candidates = [];
        foreach ($disk->files() as $file) {
            if ($disk->lastModified($file) < $cutoff) {
                $candidates[] = $file;
            }
        }

        $log->info('CleanupTempUploads: found {count} file(s) older than 24h', [
            'count' => count($candidates),
        ]);

        $deleted = 0;
        foreach ($candidates as $file) {
            $disk->delete($file);
            $log->info('CleanupTempUploads: deleted {file}', ['file' => $file]);
            $deleted++;
        }

        $elapsed = round(microtime(true) - $start, 2);

        $log->info('CleanupTempUploads: done — {deleted} file(s) deleted in {elapsed}s', [
            'deleted' => $deleted,
            'elapsed' => $elapsed,
        ]);
    }
}
