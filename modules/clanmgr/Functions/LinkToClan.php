<?php

/**
 * @param string $clan_url
 * @return string
 */
function LinkToClan($clan_url)
{
    if ($clan_url== '') {
        return '';
    }
    if (substr($clan_url, 0, 7) != 'http://' and substr($clan_url, 0, 8) != 'https://') {
        $clan_url = "http://".$clan_url;
    }
    return '<a href="'. $clan_url .'" target="_blank">'. $clan_url .'</a>';
}
