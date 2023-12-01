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

    // If it is a string, a date field in the format of "2005-03-15 11:12:31" comes in
    if (is_string($time)) {
        return '<span class="small">' . $time  .'</span>';
    }

    if ($time > 0) {
        return '<span class="small">'. date('d.m.y', $time) . ' ' . date('H:i', $time) .'</span>';
    }

    return $dsp->FetchIcon('no', '', '-');
}
