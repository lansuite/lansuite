<?php

/**
 * @param string $url
 * @return bool|string
 */
function GetSite($url)
{
    return @file_get_contents($url,false,stream_context_create(array('http' => array('timeout' => 10))));
}
