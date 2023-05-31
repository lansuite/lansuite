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
    if (!str_starts_with($clan_url, 'http://') and !str_starts_with($clan_url, 'https://')) {
        $clan_url = "http://".$clan_url;
    }
    return '<a href="'. $clan_url .'" target="_blank">'. $clan_url .'</a>';
}
