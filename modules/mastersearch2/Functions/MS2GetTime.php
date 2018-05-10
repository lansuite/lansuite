<?php

/**
 * Used as a callback function for MasterSearch2 class.
 *
 * @param int $time
 * @return false|string
 */
function MS2GetTime($time)
{
    global $dsp;

    if ($time > 0) {
        return date('H:i', $time);
    } else {
        return $dsp->FetchIcon('no', '', '-');
    }
}
