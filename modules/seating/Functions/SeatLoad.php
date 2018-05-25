<?php

/**
 * Get number of loaded seats in block
 *
 * @param int $blockid
 * @return string
 */
function SeatLoad($blockid)
{
    $width = 100;
    $seats = SeatsAvailable($blockid);

    if ($seats != 0) {
        $SeatLoad = SeatsOccupied($blockid) / $seats * 100;
    } else {
        $SeatLoad = 0;
    }

    ($SeatLoad)? $score = ceil(($width / SeatsAvailable($blockid)) * SeatsOccupied($blockid)) : $score = 0;
    $score_rest = $width - $score;
    $votebar = '<ul class="BarOccupied infolink" style="width:'. (int)$score .'px;"></ul><ul id="infobox" class="BarFree" style="width:'. $score_rest .'px;"></ul><ul class="BarClear">&nbsp;</ul>';

    return round($SeatLoad, 1) .'% '.$votebar.' ';
}
