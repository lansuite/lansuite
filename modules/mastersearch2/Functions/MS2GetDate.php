<?php

/**
 * Used as a callback function for MasterSearch2 class.
 *
 * @param int $time
 * @return string
 */
function MS2GetDate($time)
{
    global $dsp;

    if ($time > 0) {
        return '<span class="small">'. date('d.m.y', $time) .'<br />'. date('H:i', $time) .'</span>';
    } else {
        return $dsp->FetchIcon('no', '', '-');
    }
}
