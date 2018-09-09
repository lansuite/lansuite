<?php

/**
 * @param boolean $paid
 * @return string
 */
function PaidIcon($paid)
{
    global $dsp;

    if ($paid) {
        return $dsp->FetchIcon('paid', '', t('Bezahlt'));
    } else {
        return $dsp->FetchIcon('not_paid', '', t('Nicht bezahlt'));
    }
}
