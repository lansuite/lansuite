<?php

/**
 * @param string $mac
 */
function CheckMAC($mac): bool|string
{
    if ($mac) {
        $explode = explode('-', $mac);
        $count = count($explode);
        if ($count != 6) {
            return t('Bitte gib eine gültige MAC Adresse ein');
        }
    }
    return false;
}
