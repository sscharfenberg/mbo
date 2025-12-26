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

}
