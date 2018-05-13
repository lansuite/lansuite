<?php

/**
 * @param string $lasturl
 * @return string
 */
function getLastHitUrl($lasturl)
{
    return '<a href="'.$lasturl.'">'.substr(stristr($lasturl, '.php'), 5).'</a>';
}
