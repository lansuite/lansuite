<?php

/**
 * Used as a callback function for MasterSearch2 class.
 *
 * @param int $time
 */
function MS2GetTime($time): false|string
{
    global $dsp;

    if ($time > 0) {
        return date('H:i', $time);
    } else {
        return $dsp->FetchIcon('no', '', '-');
    }
}
