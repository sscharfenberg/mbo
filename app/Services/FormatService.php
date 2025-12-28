<?php
namespace App\Services;
use Carbon\CarbonInterval;

class FormatService
{

    /**
     * @function format duration from milliseconds to human readable string
     * @param float $input
     * @return string
     */
    public function formatMs(float $input): string
    {
        $uSec = $input % 1000;
        $dateString = $uSec."ms";
        $input = floor($input / 1000);
        $seconds = $input % 60;
        $input = floor($input / 60);
        $minutes = $input % 60;
        if ($seconds > 0) {
            $dateString = $seconds."s ".$dateString;
        }
        if ($minutes > 0) {
            $dateString = $minutes."m ".$dateString;
        }
        return $dateString;
    }

    /**
     * @function bytes into human readable format
     * @param $bytes
     * @param int $precision
     * @return string
     */
    public function formatBytes($bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);
        // this will also work in place of the above line:
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . $units[$pow];
    }

}
