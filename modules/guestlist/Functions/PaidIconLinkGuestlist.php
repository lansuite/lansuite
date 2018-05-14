<?php

/**
 * @param boolean $paid
 * @return string
 */
function PaidIconLinkGuestlist($paid)
{
    global $dsp, $line, $auth;

    if ($auth['type'] > 1) {
        if ($paid) {
            return $dsp->FetchIcon('paid', 'index.php?mod=guestlist&step=11&userid=' . $line['userid'], t('Bezahlt'));
        } else {
            return $dsp->FetchIcon('not_paid', 'index.php?mod=guestlist&step=10&userid=' . $line['userid'], t('Nicht bezahlt'));
        }
    } else {
        if ($paid) {
            return $dsp->FetchIcon('paid', '', t('Bezahlt'));
        } else {
            return $dsp->FetchIcon('not_paid', '', t('Nicht bezahlt'));
        }
    }
}
