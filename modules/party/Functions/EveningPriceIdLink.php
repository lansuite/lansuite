<?php

/**
 * @param int $evening_price_id
 * @return string
 */
function EveningPriceIdLink($evening_price_id)
{
    global $dsp, $line;

    if ($evening_price_id == $line['price_id']) {
        return $dsp->FetchIcon('yes', '', t('Ja'));
    } else {
        return $dsp->FetchIcon('no', 'index.php?mod=party&action=price&step=11&party_id=' . $_GET['party_id'] . '&evening_price_id=' . $line['price_id'], t('Nein'));
    }
}
