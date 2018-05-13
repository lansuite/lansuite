<?php

/**
 * @param string $url
 * @return mixed
 */
function getModul($url)
{
    $ret=array();
    parse_str(substr(stristr($url, '.php'), 5), $ret);
    return $ret['mod'];
}
