<?php

/**
 * @param string $url
 */
function GetSite($url): bool|string
{
    return @file_get_contents($url, false, stream_context_create(array('http' => array('timeout' => 10))));
}
