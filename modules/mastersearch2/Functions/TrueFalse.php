<?php

/**
 * Used as a callback function for MasterSearch2 class.
 *
 * @param boolean $val
 * @return string
 */
function TrueFalse($val)
{
    global $dsp;

    if ($val) {
        return $dsp->FetchIcon('yes', '', t('Ja'));
    } else {
        return $dsp->FetchIcon('no', '', t('Nein'));
    }
}
