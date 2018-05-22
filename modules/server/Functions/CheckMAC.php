<?php

/**
 * @param string $mac
 * @return bool|string
 */
function CheckMAC($mac)
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
