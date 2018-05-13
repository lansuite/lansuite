<?php

/**
 * @param boolean $val
 * @return string
 */
function ShowActiveState($val)
{
    global $dsp;

    if ($val) {
        return $dsp->FetchIcon('yes', '', t('Ja'));
    } else {
        return $dsp->FetchIcon('no', '', t('Nein'));
    }
}
