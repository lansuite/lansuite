<?php

/**
 * @param string $date
 * @return bool|string
 */
function CheckDateInFuture($date)
{
    global $func, $mf;

    if (!$mf->isChange and $func->str2time($date) < time()) {
        return t('Dieses Datum liegt in der Vergangenheit');
    } else {
        return false;
    }
}
