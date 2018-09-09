<?php

/**
 * @param string $pw
 * @return string
 */
function PWIcon($pw)
{
    global $dsp;

    if ($pw) {
        return $dsp->FetchIcon('locked', '', t('Geschützt'));
    } else {
        return $dsp->FetchIcon('unlocked', '', t('Nicht geschützt'));
    }
}
