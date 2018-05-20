<?php

/**
 * @param string $ip
 * @return bool|string
 */
function CheckIP($ip)
{
    global $cfg;

    if ($cfg['sys_internet'] == 0) {
        $ip_address = gethostbyname($ip);
    } else {
        $ip_address = $ip;
    }

    $explode = explode('.', $ip_address);
    $count = count($explode);
    if ($count != 4) {
        return t('Bitte gib eine gültige IP Adresse ein');
    } elseif ($explode[0] > 255 or $explode[1] > 255 or $explode[2] > 255 or $explode[3] > 255) {
        return t('Bitte gib eine gültige IP Adresse ein');
    }

    return false;
}
