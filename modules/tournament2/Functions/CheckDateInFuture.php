<?php

/**
 * @param string $date
 */
function CheckDateInFuture($date): bool|string
{
    global $func, $mf;

    if (!$mf->isChange and $func->str2time($date) < time()) {
        return t('Dieses Datum liegt in der Vergangenheit');
    } else {
        return false;
    }
}
