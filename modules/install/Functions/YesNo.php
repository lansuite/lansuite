<?php

/**
 * @param boolean $TargetLang
 * @return string
 */
function YesNo($TargetLang)
{
    global $dsp;

    if ($TargetLang) {
        return $dsp->FetchIcon('yes', '');
    } else {
        return $dsp->FetchIcon('no', '');
    }
}
