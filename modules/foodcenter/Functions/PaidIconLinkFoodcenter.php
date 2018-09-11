<?php

/**
 * Used as callback function in mastersearch
 *
 * @param boolean $paid
 * @return string
 */
function PaidIconLinkFoodcenter($paid)
{
    global $dsp, $line, $party;

    // Only link, if selected party is the current party
    if ($_POST["search_dd_input"][1] == $party->party_id) {
        $link = 'index.php?mod=usrmgr&action=changepaid&step=2&userid='. $line['userid'];
    }

    if ($paid) {
        return $dsp->FetchIcon('paid', $link, t('Bezahlt'));
    } else {
        return $dsp->FetchIcon('not_paid', $link, t('Nicht bezahlt'));
    }

    return $dsp->FetchIcon($link, 'not_paid', t('Nicht bezahlt'));
}
